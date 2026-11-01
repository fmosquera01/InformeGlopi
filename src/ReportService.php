<?php
declare(strict_types=1);

namespace GlpiPlugin\Informeglopi;

use CommonDBTM;
use RuntimeException;
use Session;
use Throwable;

final class ReportService
{
    private const ALLOWED_ITEMTYPES = ['Computer', 'Phone', 'Printer', 'Monitor', 'NetworkEquipment'];

    public static function createDraft(string $itemtype, int $itemsId): int
    {
        global $DB;

        if (!Session::haveRight(Report::$rightname, CREATE)) {
            throw new RuntimeException(__('No tiene permisos para crear informes técnicos.', 'informeglopi'));
        }
        if (!in_array($itemtype, self::ALLOWED_ITEMTYPES, true) || !class_exists($itemtype)) {
            throw new RuntimeException(__('Tipo de activo no permitido.', 'informeglopi'));
        }

        /** @var CommonDBTM $item */
        $item = new $itemtype();
        if (!$item->getFromDB($itemsId)) {
            throw new RuntimeException(__('No se encontró el activo seleccionado.', 'informeglopi'));
        }
        if (!$item->can($itemsId, READ)) {
            throw new RuntimeException(__('No tiene permisos para consultar este activo.', 'informeglopi'));
        }

        $userId = (int) Session::getLoginUserID();
        if ($userId < 1) {
            throw new RuntimeException(__('No se pudo identificar al usuario de GLPI.', 'informeglopi'));
        }

        $year = (int) date('Y');
        $entityId = (int) ($item->fields['entities_id'] ?? Session::getActiveEntity());
        $isRecursive = (int) ($item->fields['is_recursive'] ?? 0);

        $snapshotFields = [];
        foreach (($item->fields ?? []) as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $snapshotFields[(string) $key] = $value;
            }
        }
        $snapshotJson = json_encode([
            'schema_version' => 1,
            'captured_at' => date('Y-m-d H:i:s'),
            'itemtype' => $itemtype,
            'items_id' => $itemsId,
            'asset_name' => (string) ($item->fields['name'] ?? ''),
            'fields' => $snapshotFields,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $DB->beginTransaction();
        try {
            $DB->query("INSERT IGNORE INTO glpi_plugin_informeglopi_sequences (year, last_number) VALUES ({$year}, 0)");
            $DB->query("UPDATE glpi_plugin_informeglopi_sequences SET last_number = last_number + 1 WHERE year = {$year}");

            $sequence = 0;
            foreach ($DB->request([
                'SELECT' => ['last_number'],
                'FROM' => 'glpi_plugin_informeglopi_sequences',
                'WHERE' => ['year' => $year],
                'LIMIT' => 1,
            ]) as $row) {
                $sequence = (int) ($row['last_number'] ?? 0);
                break;
            }
            if ($sequence < 1) {
                throw new RuntimeException(__('No se pudo reservar el número correlativo del informe.', 'informeglopi'));
            }

            $reportNumber = 'ITSIS' . $year . '-' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $DB->insert('glpi_plugin_informeglopi_reports', [
                'report_number' => $reportNumber,
                'itemtype' => $itemtype,
                'items_id' => $itemsId,
                'entities_id' => $entityId,
                'is_recursive' => $isRecursive,
                'status' => 'draft',
                'asset_snapshot' => $snapshotJson,
                'users_id' => $userId,
            ]);

            $reportId = 0;
            foreach ($DB->request([
                'SELECT' => ['id'],
                'FROM' => 'glpi_plugin_informeglopi_reports',
                'WHERE' => ['report_number' => $reportNumber],
                'LIMIT' => 1,
            ]) as $row) {
                $reportId = (int) ($row['id'] ?? 0);
                break;
            }
            if ($reportId < 1) {
                throw new RuntimeException(__('No se pudo guardar el informe técnico.', 'informeglopi'));
            }

            $DB->insert('glpi_plugin_informeglopi_history', [
                'reports_id' => $reportId,
                'action' => 'created',
                'old_status' => null,
                'new_status' => 'draft',
                'changes_json' => json_encode([
                    'report_number' => $reportNumber,
                    'itemtype' => $itemtype,
                    'items_id' => $itemsId,
                ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'comment' => __('Creación del borrador.', 'informeglopi'),
                'users_id' => $userId,
            ]);

            $DB->commit();
            return $reportId;
        } catch (Throwable $e) {
            $DB->rollBack();
            throw $e;
        }
    }
}
