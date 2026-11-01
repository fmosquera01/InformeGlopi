<?php
declare(strict_types=1);

use GlpiPlugin\Informeglopi\Report;

include('../../../inc/includes.php');

Session::checkLoginUser();

if (!Session::haveRight(Report::$rightname, READ)) {
    Html::displayRightError();
}

$reportId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($reportId < 1) {
    Html::displayErrorAndDie(__('ID de informe no válido.', 'informeglopi'));
}

$report = new Report();
if (!$report->getFromDB($reportId)) {
    Html::displayNotFoundError();
}

if (!Session::haveAccessToEntity(
    (int) ($report->fields['entities_id'] ?? -1),
    (bool) ($report->fields['is_recursive'] ?? false)
)) {
    Html::displayRightError();
}

$snapshot = json_decode((string) ($report->fields['asset_snapshot'] ?? '{}'), true);
if (!is_array($snapshot)) {
    $snapshot = [];
}
$assetFields = is_array($snapshot['fields'] ?? null) ? $snapshot['fields'] : [];

Html::header(__('Informe técnico', 'informeglopi'), $_SERVER['PHP_SELF'], 'assets', 'computer');

$e = static fn($value): string => htmlspecialchars((string) ($value ?? '—'), ENT_QUOTES, 'UTF-8');

echo "<div class='card mb-3'><div class='card-header'><h3 class='card-title'>" . $e($report->fields['report_number'] ?? '') . "</h3></div><div class='card-body'>";
echo "<div class='row mb-3'>";
echo "<div class='col-md-3'><strong>" . $e(__('Estado', 'informeglopi')) . ":</strong> " . $e($report->fields['status'] ?? '') . "</div>";
echo "<div class='col-md-3'><strong>" . $e(__('Fecha de creación', 'informeglopi')) . ":</strong> " . $e($report->fields['date_creation'] ?? '') . "</div>";
echo "<div class='col-md-3'><strong>" . $e(__('Tipo de activo', 'informeglopi')) . ":</strong> " . $e($snapshot['itemtype'] ?? '') . "</div>";
echo "<div class='col-md-3'><strong>" . $e(__('ID del activo', 'informeglopi')) . ":</strong> " . $e($snapshot['items_id'] ?? '') . "</div>";
echo "</div>";
echo "<div class='alert alert-info'>" . $e(__('Este informe conserva los datos del activo capturados al momento de crear el borrador. La edición del contenido y las firmas se incorporarán en la siguiente etapa.', 'informeglopi')) . "</div>";
echo "<h4>" . $e(__('Datos del activo (snapshot)', 'informeglopi')) . "</h4>";
echo "<div class='table-responsive'><table class='table table-sm table-striped'><tbody>";
foreach ($assetFields as $key => $value) {
    if (is_array($value) || is_object($value)) {
        continue;
    }
    echo "<tr><th style='width:30%'>" . $e($key) . "</th><td>" . $e($value) . "</td></tr>";
}
echo "</tbody></table></div>";

$itemtype = (string) ($report->fields['itemtype'] ?? '');
$itemsId = (int) ($report->fields['items_id'] ?? 0);
if ($itemtype !== '' && $itemsId > 0) {
    $assetUrl = '/front/' . strtolower($itemtype) . '.form.php?id=' . $itemsId;
    echo "<a class='btn btn-secondary' href='" . $e($CFG_GLPI['root_doc'] . $assetUrl) . "'>" . $e(__('Volver al activo', 'informeglopi')) . "</a>";
}
echo "</div></div>";

Html::footer();
