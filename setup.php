<?php

/**
 * Informe Técnico - Songa
 *
 * Plugin nativo para GLPI 11.
 * Internal plugin key and installation directory: informeglopi
 */

define('PLUGIN_INFORMEGLOPI_VERSION', '0.2.0');

require_once __DIR__ . '/src/Report.php';
require_once __DIR__ . '/src/Profile.php';
require_once __DIR__ . '/src/ReportTab.php';

/**
 * Initialize plugin hooks.
 *
 * @return void
 */
function plugin_init_informeglopi(): void
{
    global $PLUGIN_HOOKS;

    Plugin::registerClass(\GlpiPlugin\Informeglopi\Profile::class, [
        'addtabon' => ['Profile'],
    ]);

    Plugin::registerClass(\GlpiPlugin\Informeglopi\ReportTab::class, [
        'addtabon' => [
            'Computer', 'Phone', 'Printer', 'Monitor', 'NetworkEquipment',
        ],
    ]);
}

/**
 * Return plugin metadata.
 *
 * @return array<string, mixed>
 */
function plugin_version_informeglopi(): array
{
    return [
        'name'         => 'Informe Técnico',
        'version'      => PLUGIN_INFORMEGLOPI_VERSION,
        'author'       => 'Songa - Infraestructura IT',
        'license'      => 'Proprietary',
        'homepage'     => 'https://github.com/fmosquera01/InformeGlopi',
        'requirements' => [
            'glpi' => [
                'min' => '11.0.0',
                'max' => '11.99.99',
            ],
            'php' => [
                'min' => '8.2.0',
            ],
        ],
    ];
}

/**
 * Check plugin prerequisites.
 *
 * @return bool
 */
function plugin_informeglopi_check_prerequisites(): bool
{
    if (!defined('GLPI_VERSION')) {
        return false;
    }

    return version_compare(GLPI_VERSION, '11.0.0', '>=')
        && version_compare(GLPI_VERSION, '12.0.0', '<');
}

/**
 * Check plugin configuration.
 *
 * @param bool $verbose
 *
 * @return bool
 */
function plugin_informeglopi_check_config($verbose = false): bool
{
    return true;
}
