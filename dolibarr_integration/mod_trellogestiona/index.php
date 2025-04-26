<?php
/**
 * Página principal del módulo TrelloGestiona
 */

// Carga del entorno Dolibarr
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once './lib/trellogestiona.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->read) {
    accessforbidden();
}

// Redirigir al dashboard
header('Location: dashboard.php');
exit;