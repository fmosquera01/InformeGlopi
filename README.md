# Informe Técnico - Songa

Plugin nativo para GLPI 11. Directorio interno: `informeglopi`.

## Objetivo

Gestionar el ciclo de vida del documento institucional **Informe Técnico de Ítems de Propiedad, Planta y Equipos**, vinculado al activo GLPI. Cada informe es un registro independiente y conserva una fotografía histórica de los datos del activo al momento de su elaboración.

## Reglas funcionales acordadas

- Un activo puede tener múltiples informes; el historial no sobrescribe documentos anteriores.
- El formato institucional aprobado debe reproducirse sin alterar encabezados, títulos, distribución, firmas ni estructura.
- Los datos del activo y sus componentes se guardan como snapshot histórico.
- El valor en libros se ingresa manualmente por el personal autorizado y conserva usuario y fecha de registro.
- Tipo de daño: **Daño a la propiedad** o **Desgaste**. Puede cambiarse durante el borrador y se bloquea al finalizar.
- Evidencias fotográficas múltiples, con categoría, autor y checksum.
- Firmas de técnico y supervisor/administrador, con usuario, fecha y checksum de la imagen.
- Flujo previsto: Borrador → Firma técnico → Firma supervisor/administrador → Finalizado.
- El PDF final se conserva con SHA-256 y no debe regenerarse ni modificarse después de finalizar.
- Los permisos se integran exclusivamente con perfiles y derechos nativos de GLPI; no existe ACL paralela.
- La desinstalación del plugin no elimina los informes ni sus tablas.

## Estructura actual

- `setup.php`: metadatos, inicialización y registro de pestañas.
- `hook.php`: creación idempotente de tablas y registro de derechos nativos.
- `src/Profile.php`: pestaña de derechos en Administración → Perfiles.
- `src/Report.php`: entidad CommonDBTM del informe.
- `src/ReportService.php`: creación transaccional, correlativo anual y snapshot inicial del activo.
- `front/report.form.php`: formulario para iniciar un borrador desde la ficha del activo.
- `src/ReportTab.php`: pestaña de historial en activos compatibles.
- `front/report.php`: vista inicial de un informe.
- `README.md`, `CHANGELOG.md`.

## Modelo de datos inicial (v0.2.0)

- `glpi_plugin_informeglopi_sequences`: correlativo anual.
- `glpi_plugin_informeglopi_reports`: informe, estado, campos manuales, snapshot JSON y metadatos del PDF.
- `glpi_plugin_informeglopi_signatures`: firmas técnico/supervisor.
- `glpi_plugin_informeglopi_evidences`: fotografías y archivos de evidencia.
- `glpi_plugin_informeglopi_history`: auditoría de acciones y cambios.

Las relaciones se gestionan desde el plugin y no se crean claves foráneas hacia tablas GLPI, para mantener compatibilidad con el ciclo de vida de sus activos.

## Estado de desarrollo

**Versión 0.3.0 — creación inicial de borradores.**

Implementado en repositorio:
- Registro de pestaña de informes para Computer, Phone, Printer, Monitor y NetworkEquipment.
- Pestaña de permisos en perfiles GLPI con derechos de consulta/edición y aprobación.
- Esquema inicial de informes, correlativos, firmas, evidencias y auditoría.
- Consulta de historial por activo.
- Creación de borradores desde activos compatibles.
- Numeración anual `ITSISAAAA-NNN` reservada dentro de una transacción.
- Snapshot JSON de los campos escalares disponibles del activo al momento de crear el borrador.
- Registro de auditoría para la creación del informe.
- Vista de lectura del borrador con los datos capturados.
- Conservación de tablas al desinstalar.

Pendiente:
- Edición de campos técnicos/manuales del borrador.
- Captura genérica de activos y componentes por itemtype.
- Gestión de evidencias y firmas.
- Máquina de estados y bloqueo de registros firmados.
- Generación del PDF respetando exactamente la plantilla documental oficial.
- Pruebas de instalación y funcionamiento en una instancia GLPI 11.

## Requisito documental pendiente

Para reproducir el formato sin desviaciones, el desarrollo del PDF debe basarse en la plantilla institucional oficial en blanco (.docx), además de los dos ejemplos ya descritos. No se debe inventar ni rediseñar la plantilla.
