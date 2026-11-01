<?php

/**
 * Install the Informe Técnico plugin.
 *
 * Database schema and profile rights will be installed in a later
 * implementation step. This bootstrap intentionally makes no changes
 * to the GLPI database.
 *
 * @return bool
 */
function plugin_informeglopi_install(): bool
{
    return true;
}

/**
 * Uninstall the Informe Técnico plugin.
 *
 * Persistent report records must not be removed until an explicit
 * retention and backup policy has been defined.
 *
 * @return bool
 */
function plugin_informeglopi_uninstall(): bool
{
    return true;
}
