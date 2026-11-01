<?php
declare(strict_types=1);

namespace GlpiPlugin\\Informeglopi;

use CommonDBTM;

/**
 * Informe Técnico record.
 *
 * Reports are append-only documents. Workflow services will enforce
 * snapshot, signature and finalization rules before exposing mutations.
 */
final class Report extends CommonDBTM
{
    public static string $rightname = 'plugin_informeglopi';

    public static function getTypeName($nb = 0): string
    {
        return _n('Informe técnico', 'Informes técnicos', $nb, 'informeglopi');
    }

    public function canView(): bool
    {
        return parent::canView() && (bool) (self::canViewType());
    }

    private static function canViewType(): bool
    {
        return \\Session::haveRight(self::$rightname, READ);
    }
}
