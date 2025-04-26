<?php
//
// Archivo generado por Dolibarr Installer - Versión final optimizada por Viaweb
//

// --------------------
// URLs y Paths
// --------------------
$dolibarr_main_url_root = 'https://web.viaweb.net.ar/erp';
$dolibarr_main_document_root = "/home/webviaw/public_html/erp";

$dolibarr_main_url_root_alt = '/custom';
$dolibarr_main_document_root_alt = "/home/webviaw/public_html/erp/custom";

// Carpeta de datos
$dolibarr_main_data_root = "/home/webviaw/dolibarrvwdata2";

// --------------------
// Base de datos
// --------------------
$dolibarr_main_db_host = 'localhost';
$dolibarr_main_db_port = '0';
$dolibarr_main_db_name = 'webviaw_viawebn2_viaweb_doli852';
$dolibarr_main_db_prefix = 'llxeh_';
$dolibarr_main_db_user = 'webviaw_viawebn2_viaweb_doli852';
$dolibarr_main_db_pass = 'viawebn2_viaweb_doli852'; // ⚠️ Protegé este archivo correctamente
$dolibarr_main_db_type = 'mysqli';
$dolibarr_main_db_character_set = 'utf8';
$dolibarr_main_db_collation = 'utf8_general_ci';

// --------------------
// Seguridad y entorno
// --------------------
$dolibarr_main_authentication = 'dolibarr';
$dolibarr_main_prod = '1';                         // Modo producción activado
$dolibarr_main_force_https = 1;                    // Forzar uso de HTTPS
$dolibarr_main_restrict_os_commands = 'mariadb-dump, mariadb, mysqldump, mysql, pg_dump, pg_restore, clamdscan, clamdscan.exe';
$dolibarr_nocsrfcheck = '0';                       // Protección CSRF activa
$dolibarr_main_instance_unique_id = 'dc9ef1ef500e22ff21cf4de0a7fcf642';

// --------------------
// Mailing
// --------------------
$dolibarr_mailing_limit_sendbyweb = '0';
$dolibarr_mailing_limit_sendbycli = '0';

// --------------------
// Distribución
// --------------------
$dolibarr_main_distrib = 'standard';

// --------------------
// Logs de SQL
// --------------------
$dolibarr_main_log_sql = 1;

// --------------------
// Zona horaria y lenguaje
// --------------------
date_default_timezone_set('America/Argentina/Salta');
$dolibarr_main_force_language = 'es_ES';

// --------------------
// Manejo de errores
// --------------------
ini_set('display_errors', 0);                      // No mostrar errores (producción)
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', '/home/webviaw/dolibarrvwdata2/logs/php_errors.log');
?>
