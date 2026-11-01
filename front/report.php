<?php
declare(strict_types=1);

use GlpiPlugin\\Informeglopi\\Report;

Session::checkLoginUser();

if (!isset($_GET['id']) || (int) $_GET['id'] < 1) {
    Html::displayErrorAndDie(__('ID de informe no válido.'));
}

$report = new Report();
if (!$report->getFromDB((int) $_GET['id'])) {
    Html::displayNotFoundError();
}

Html::header(__('Informe técnico', 'informeglopi'), $_SERVER['PHP_SELF'], 'assets', 'computer');
if (!Session::haveRight(Report::$rightname, READ)) {
    Html::displayRightError();
}

echo "<div class='card'><div class='card-body'>";
echo '<h2>' . htmlspecialchars((string) $report->fields['report_number'], ENT_QUOTES, 'UTF-8') . '</h2>';
echo '<p>' . htmlspecialchars(__('Vista de informe en construcción. La ficha y el PDF institucional se incorporarán en la siguiente etapa.', 'informeglopi'), ENT_QUOTES, 'UTF-8') . '</p>';
echo '</div></div>';

Html::footer();
