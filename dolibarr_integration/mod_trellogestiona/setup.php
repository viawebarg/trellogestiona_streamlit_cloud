<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Página de configuración del módulo TrelloGestiona
 */

// Cargar Dolibarr
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Archivo de inclusión principal no encontrado");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Control de acceso
if (!$user->admin) accessforbidden();

// Cargar traducciones
$langs->loadLangs(array('admin', 'trellogestiona@trellogestiona'));

// Parámetros
$action = GETPOST('action', 'aZ09');

// Inicializar variables
$error = 0;
$setupnotice = '';

/*
 * Acciones
 */

if ($action == 'update') {
    // Actualizar configuración
    $streamlit_url = GETPOST('streamlit_url', 'alpha');
    $trello_api_key = GETPOST('trello_api_key', 'alpha');
    $trello_token = GETPOST('trello_token', 'alpha');
    
    if (!empty($streamlit_url)) {
        dolibarr_set_const($db, 'TRELLOGESTIONA_STREAMLIT_URL', $streamlit_url, 'chaine', 0, '', $conf->entity);
    }
    
    if (!empty($trello_api_key)) {
        dolibarr_set_const($db, 'TRELLOGESTIONA_API_KEY', $trello_api_key, 'chaine', 0, '', $conf->entity);
    }
    
    if (!empty($trello_token)) {
        dolibarr_set_const($db, 'TRELLOGESTIONA_TOKEN', $trello_token, 'chaine', 0, '', $conf->entity);
    }
    
    // Guardar en la base de datos
    $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."trellogestiona_config WHERE entity = ".$conf->entity;
    $result = $db->query($sql);
    if ($result && $db->num_rows($result) > 0) {
        // Actualizar configuración existente
        $obj = $db->fetch_object($result);
        $sql = "UPDATE ".MAIN_DB_PREFIX."trellogestiona_config SET";
        $sql .= " streamlit_url = '".$db->escape($streamlit_url)."',";
        $sql .= " trello_api_key = '".$db->escape($trello_api_key)."',";
        $sql .= " trello_token = '".$db->escape($trello_token)."',";
        $sql .= " tms = '".$db->idate(dol_now())."',";
        $sql .= " fk_user_modif = ".$user->id;
        $sql .= " WHERE rowid = ".$obj->rowid;
        
        $resql = $db->query($sql);
        if (!$resql) {
            $error++;
            $setupnotice .= $db->lasterror();
        }
    } else {
        // Insertar nueva configuración
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."trellogestiona_config";
        $sql .= " (entity, streamlit_url, trello_api_key, trello_token, date_creation, fk_user_creat)";
        $sql .= " VALUES (".$conf->entity.", ";
        $sql .= "'".$db->escape($streamlit_url)."', ";
        $sql .= "'".$db->escape($trello_api_key)."', ";
        $sql .= "'".$db->escape($trello_token)."', ";
        $sql .= "'".$db->idate(dol_now())."', ";
        $sql .= $user->id;
        $sql .= ")";
        
        $resql = $db->query($sql);
        if (!$resql) {
            $error++;
            $setupnotice .= $db->lasterror();
        }
    }
    
    if (!$error) {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    } else {
        setEventMessages($langs->trans("SetupError"), null, 'errors');
    }
}

/*
 * Vista
 */

$title = $langs->trans("TrelloGestiona") . ' - ' . $langs->trans("Setup");

llxHeader('', $title);

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($title, $linkback, 'trellogestiona.png@trellogestiona');

// Configuración principal
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print '</tr>';

// Streamlit URL
print '<tr class="oddeven">';
print '<td>'.$langs->trans("StreamlitURL").'</td>';
print '<td><input type="text" name="streamlit_url" size="60" value="'.$conf->global->TRELLOGESTIONA_STREAMLIT_URL.'"></td>';
print '</tr>';

// Trello API Key
print '<tr class="oddeven">';
print '<td>'.$langs->trans("TrelloAPIKey").'</td>';
print '<td><input type="text" name="trello_api_key" size="60" value="'.$conf->global->TRELLOGESTIONA_API_KEY.'"></td>';
print '</tr>';

// Trello Token
print '<tr class="oddeven">';
print '<td>'.$langs->trans("TrelloToken").'</td>';
print '<td><input type="text" name="trello_token" size="60" value="'.$conf->global->TRELLOGESTIONA_TOKEN.'"></td>';
print '</tr>';

print '</table>';

print '<div class="center">';
print '<input type="submit" class="button" value="'.$langs->trans("SaveSettings").'">';
print '</div>';

print '</form>';

// Información sobre uso
print '<br>';
print '<div class="info">';
print $langs->trans("ModuleHelp");
print '<br>';
print $langs->trans("ModuleHelpConfig");
print '</div>';

// Cerrar página
llxFooter();
$db->close();