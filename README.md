# Informe Técnico - Songa

Plugin nativo para **GLPI 11**, destinado a elaborar, firmar, consultar y conservar informes técnicos asociados a los activos de Songa.

- Repositorio: https://github.com/fmosquera01/InformeGlopi
- Clave interna del plugin: `informeglopi`
- Directorio de instalación en GLPI: `plugins/informeglopi`
- Versión inicial de desarrollo: `0.1.0`
- Plataforma objetivo: GLPI 11.x / PHP 8.2+

> Este repositorio está en etapa de construcción. La versión inicial contiene únicamente el arranque y los metadatos del plugin; todavía no crea tablas ni presenta funcionalidades operativas.

## Requisitos funcionales acordados

1. Pestaña **Informes técnicos** en los activos GLPI compatibles.
2. Múltiples informes por activo, con historial consultable y sin sobrescribir documentos anteriores.
3. Recuperación de datos del activo desde GLPI y conservación de una fotografía histórica de esos datos al crear el informe.
4. Descripción detallada del activo y de sus componentes, para facilitar verificaciones patrimoniales y procesos de baja.
5. Valor en libros ingresado manualmente por el técnico, porque lo proporciona otra área.
6. Clasificación del daño seleccionable: **Daño a la propiedad** o **Desgaste**. El PDF debe mostrar una X en la opción elegida.
7. Respeto estricto al formato institucional aprobado por Gestión Documental. No se rediseñará la plantilla sin autorización.
8. Evidencias fotográficas adjuntas al informe.
9. Flujo de borrador, firma del técnico, firma del supervisor/administrador y finalización.
10. Tras la firma final, el PDF y los datos firmados quedan bloqueados; se conserva una huella SHA-256.
11. Integración exclusiva con los perfiles y permisos nativos de GLPI 11. No se implementará un ACL paralelo.
12. Historial de acciones y firmas con usuario y fecha/hora.

## Estructura prevista

```text
informeglopi/
├── setup.php
├── hook.php
├── README.md
├── CHANGELOG.md
├── front/
├── src/
├── templates/
├── css/
├── js/
├── pics/
├── locales/
└── install/
```

La estructura se ampliará siguiendo las convenciones oficiales de desarrollo de plugins GLPI 11.

## Instalación de desarrollo

1. Copiar el directorio del plugin a `<GLPI_ROOT>/plugins/informeglopi`.
2. Verificar que GLPI detecte **Informe Técnico** en Configuración/Plugins.
3. Instalar y activar desde la interfaz de GLPI.

La base actual no crea tablas ni modifica perfiles. Esa parte se añadirá junto con las clases y migraciones correspondientes.

## Decisiones de integridad documental

- Los informes finalizados no se editan ni se reemplazan.
- Un informe nuevo crea un registro independiente, incluso si el activo ya tiene informes anteriores.
- La información de inventario usada en el informe se conserva como snapshot; no se reconstruye desde el estado actual del activo.
- La plantilla institucional será tratada como documento controlado.
