<?php
/* Copyright (C) 2015	Jean-François Ferry		<jfefe@aternatik.fr>
 * Copyright (C) 2016	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2017	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2021	Alexis LAURIER			<contact@alexislaurier.fr>
 * Copyright (C) 2024	MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024   Frédéric France         <frederic.france@free.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   api     Module DolibarrApi
 *  \brief      API loader
 *				Search files htdocs/<module>/class/api_<module>.class.php
 *  \file       htdocs/api/index.php
 */

use Luracast\Restler\Format\UploadFormat;

if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', '1'); // Do not check anti CSRF attack test
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1'); // Do not check anti POST attack test
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1'); // If there is no need to load and show top and left menu
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1'); // If we don't need to load the html.form.class.php
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1'); // Do not load ajax.lib.php library
}
if (!defined("NOLOGIN")) {
	define("NOLOGIN", '1'); // If this page is public (can be called outside logged session)
}
if (!defined("NOSESSION")) {
	define("NOSESSION", '1');
}
if (!defined("NODEFAULTVALUES")) {
	define("NODEFAULTVALUES", '1');
}

// Force entity if a value is provided into HTTP header. Otherwise, will use the entity of user of token used.
if (!empty($_SERVER['HTTP_DOLAPIENTITY'])) {
	define("DOLENTITY", (int) $_SERVER['HTTP_DOLAPIENTITY']);
}

// Response for preflight requests (used by browser when into a CORS context)
if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS' && !empty($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Content-Type, Authorization, api_key, DOLAPIKEY');
	http_response_code(204);
	exit;
}

// When we request url to get the json file, we accept Cross site so we can include the descriptor into an external tool.
if (preg_match('/\/explorer\/swagger\.json/', $_SERVER["PHP_SELF"])) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Content-Type, Authorization, api_key, DOLAPIKEY');
}
// When we request url to get an API, we accept Cross site so we can make js API call inside another website
if (preg_match('/\/api\/index\.php/', $_SERVER["PHP_SELF"])) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Content-Type, Authorization, api_key, DOLAPIKEY');
}
header('X-Frame-Options: SAMEORIGIN');


$res = 0;
if (!$res && file_exists("../main.inc.php")) {
	$res = include '../main.inc.php';
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/includes/restler/framework/Luracast/Restler/AutoLoader.php';

call_user_func(
	/**
	 * @return Luracast\Restler\AutoLoader
	 */
	static function () {
		$loader = Luracast\Restler\AutoLoader::instance();
		spl_autoload_register($loader);
		return $loader;
	}
);

require_once DOL_DOCUMENT_ROOT.'/api/class/api.class.php';
require_once DOL_DOCUMENT_ROOT.'/api/class/api_access.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */


$url = $_SERVER['PHP_SELF'];
if (preg_match('/api\/index\.php$/', $url)) {	// sometimes $_SERVER['PHP_SELF'] is 'api\/index\.php' instead of 'api\/index\.php/explorer.php' or 'api\/index\.php/method'
	$url = $_SERVER['PHP_SELF'].(empty($_SERVER['PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO']);
}
// Fix for some NGINX setups (this should not be required even with NGINX, however setup of NGINX are often mysterious and this may help is such cases)
if (getDolGlobalString('MAIN_NGINX_FIX')) {
	$url = (isset($_SERVER['SCRIPT_URI']) && $_SERVER["SCRIPT_URI"] !== null) ? $_SERVER["SCRIPT_URI"] : $_SERVER['PHP_SELF'];
}

// Enable and test if module Api is enabled
if (!isModEnabled('api')) {
	$langs->load("admin");
	dol_syslog("Call of Dolibarr API interfaces with module API REST are disabled");
	print $langs->trans("WarningModuleNotActive", 'Api').'.<br><br>';
	print $langs->trans("ToActivateModule");
	//session_destroy();
	exit(0);
}

// Test if explorer is not disabled
if (preg_match('/api\/index\.php\/explorer/', $url) && getDolGlobalString('API_EXPLORER_DISABLED')) {
	$langs->load("admin");
	dol_syslog("Call Dolibarr API interfaces with module API REST disabled");
	print $langs->trans("WarningAPIExplorerDisabled").'.<br><br>';
	//session_destroy();
	exit(0);
}


// This 2 lines are useful only if we want to exclude some Urls from the explorer
//use Luracast\Restler\Explorer;
//Explorer::$excludedPaths = array('/categories');


// Analyze URLs
// index.php/explorer                           do a redirect to index.php/explorer/
// index.php/explorer/                          called by swagger to build explorer page index.php/explorer/index.html
// index.php/explorer/.../....png|.css|.js      called by swagger for resources to build explorer page
// index.php/explorer/resources.json            called by swagger to get list of all services
// index.php/explorer/resources.json/xxx        called by swagger to get detail of services xxx
// index.php/xxx                                called by any REST client to run API


$reg = array();
preg_match('/index\.php\/([^\/]+)(.*)$/', $url, $reg);
// .../index.php/categories?sortfield=t.rowid&sortorder=ASC


$hookmanager->initHooks(array('api'));

// When in production mode, a file api/temp/routes.php is created with the API available of current call.
// But, if we set $refreshcache to false, so it may have only one API in the routes.php file if we make a call for one API without
// using the explorer. And when we make another call for another API, the API is not into the api/temp/routes.php and a 404 is returned.
// So we force refresh to each call.
$refreshcache = (getDolGlobalString('API_PRODUCTION_DO_NOT_ALWAYS_REFRESH_CACHE') ? false : true);
if (!empty($reg[1]) && $reg[1] == 'explorer' && ($reg[2] == '/swagger.json' || $reg[2] == '/swagger.json/root' || $reg[2] == '/resources.json' || $reg[2] == '/resources.json/root')) {
	$refreshcache = true;
	if (!is_writable($conf->api->dir_temp)) {
		dol_syslog("ErrorFailedToWriteInApiTempDirectory ".$conf->api->dir_temp, LOG_ERR);
		print 'Erreur temp dir api/temp not writable';
		header('HTTP/1.1 500 temp dir api/temp not writable');
		exit(0);
	}
}

$api = new DolibarrApi($db, '', $refreshcache);
//var_dump($api->r->apiVersionMap);

// If MAIN_API_DEBUG is set to 1, we save logs into file "dolibarr_api.log"
if (getDolGlobalString('MAIN_API_DEBUG')) {
	$r = $api->r;
	$r->onCall(function () use ($r) {
		// Don't log Luracast Restler Explorer resources calls
		//if (!preg_match('/^explorer/', $r->url)) {
		//	'method'  => $api->r->requestMethod,
		//	'url'     => $api->r->url,
		//	'route'   => $api->r->apiMethodInfo->className.'::'.$api->r->apiMethodInfo->methodName,
		//	'version' => $api->r->getRequestedApiVersion(),
		//	'data'    => $api->r->getRequestData(),
		//dol_syslog("Debug API input ".var_export($r, true), LOG_DEBUG, 0, '_api');
		dol_syslog("Debug API url ".var_export($r->url, true), LOG_DEBUG, 0, '_api');
		dol_syslog("Debug API input ".var_export($r->getRequestData(), true), LOG_DEBUG, 0, '_api');
		//}
	});
}


// Enable the Restler API Explorer.
// See https://github.com/Luracast/Restler-API-Explorer for more info.
$api->r->addAPIClass('Luracast\\Restler\\Explorer');

$api->r->setSupportedFormats('JsonFormat', 'XmlFormat', 'UploadFormat'); // 'YamlFormat'
$api->r->addAuthenticationClass('DolibarrApiAccess', '');

// Define accepted mime types
UploadFormat::$allowedMimeTypes = array('image/jpeg', 'image/png', 'text/plain', 'application/octet-stream');


// Restrict API to some IPs
if (getDolGlobalString('API_RESTRICT_ON_IP')) {
	$allowedip = explode(' ', getDolGlobalString('API_RESTRICT_ON_IP'));
	$ipremote = getUserRemoteIP();
	if (!in_array($ipremote, $allowedip)) {
		dol_syslog('Remote ip is '.$ipremote.', not into list ' . getDolGlobalString('API_RESTRICT_ON_IP'));
		print 'APIs are not allowed from the IP '.$ipremote;
		header('HTTP/1.1 503 API not allowed from your IP '.$ipremote);
		//session_destroy();
		exit(0);
	}
}


// Call Explorer file for all APIs definitions (this part is slow)
if (!empty($reg[1]) && $reg[1] == 'explorer' && ($reg[2] == '/swagger.json' || $reg[2] == '/swagger.json/root' || $reg[2] == '/resources.json' || $reg[2] == '/resources.json/root')) {
	// Scan all API files to load them

	$listofapis = array();

	$modulesdir = dolGetModulesDirs();
	foreach ($modulesdir as $dir) {
		// Search available module
		dol_syslog("Scan directory ".$dir." for module descriptor files, then search for API files");

		$handle = @opendir(dol_osencode($dir));
		if (is_resource($handle)) {
			while (($file = readdir($handle)) !== false) {
				$regmod = array();
				if (is_readable($dir.$file) && preg_match("/^mod(.*)\.class\.php$/i", $file, $regmod)) {
					$module = strtolower($regmod[1]);
					$moduledirforclass = getModuleDirForApiClass($module);
					$modulenameforenabled = $module;
					if ($module == 'propale') {
						$modulenameforenabled = 'propal';
					} elseif ($module == 'supplierproposal') {
						$modulenameforenabled = 'supplier_proposal';
					} elseif ($module == 'ficheinter') {
						$modulenameforenabled = 'intervention';
					}

					dol_syslog("Found module file ".$file." - module=".$module." - modulenameforenabled=".$modulenameforenabled." - moduledirforclass=".$moduledirforclass);

					// Defined if module is enabled
					$enabled = true;
					if (!isModEnabled($modulenameforenabled)) {
						$enabled = false;
					}

					if ($enabled) {
						// If exists, load the API class for enable module
						// Search files named api_<object>.class.php into /htdocs/<module>/class directory
						// @todo : use getElementProperties() function ?
						$dir_part = dol_buildpath('/'.$moduledirforclass.'/class/');

						$handle_part = @opendir(dol_osencode($dir_part));
						if (is_resource($handle_part)) {
							while (($file_searched = readdir($handle_part)) !== false) {
								if ($file_searched == 'api_access.class.php') {
									continue;
								}

								//$conf->global->API_DISABLE_LOGIN_API = 1;
								if ($file_searched == 'api_login.class.php' && getDolGlobalString('API_DISABLE_LOGIN_API')) {
									continue;
								}

								//dol_syslog("We scan to search api file with into ".$dir_part.$file_searched);

								$regapi = array();
								if (is_readable($dir_part.$file_searched) && preg_match("/^api_(.*)\.class\.php$/i", $file_searched, $regapi)) {
									$classname = ucwords($regapi[1]);
									$classname = str_replace('_', '', $classname);
									require_once $dir_part.$file_searched;
									if (class_exists($classname.'Api')) {
										//dol_syslog("Found API by index.php: classname=".$classname."Api for module ".$dir." into ".$dir_part.$file_searched);
										$listofapis[strtolower($classname.'Api')] = $classname.'Api';
									} elseif (class_exists($classname)) {
										//dol_syslog("Found API by index.php: classname=".$classname." for module ".$dir." into ".$dir_part.$file_searched);
										$listofapis[strtolower($classname)] = $classname;
									} else {
										dol_syslog("We found an api_xxx file (".$file_searched.") but class ".$classname." does not exists after loading file", LOG_WARNING);
									}
								}
							}
						}
					}
				}
			}
		}
	}

	// Sort the classes before adding them to Restler.
	// The Restler API Explorer shows the classes in the order they are added and it's a mess if they are not sorted.
	asort($listofapis);
	foreach ($listofapis as $apiname => $classname) {
		$api->r->addAPIClass($classname, $apiname);
	}
	//var_dump($api->r);
}

// Call one APIs or one definition of an API
$regbis = array();
if (!empty($reg[1]) && ($reg[1] != 'explorer' || ($reg[2] != '/swagger.json' && $reg[2] != '/resources.json' && preg_match('/^\/(swagger|resources)\.json\/(.+)$/', $reg[2], $regbis) && $regbis[2] != 'root'))) {
	$moduleobject = $reg[1];
	if ($moduleobject == 'explorer') {  // If we call page to explore details of a service
		$moduleobject = $regbis[2];
	}

	$moduleobject = strtolower($moduleobject);
	$moduledirforclass = getModuleDirForApiClass($moduleobject);

	// Load a dedicated API file
	dol_syslog("Load a dedicated API file moduleobject=".$moduleobject." moduledirforclass=".$moduledirforclass);

	$tmpmodule = $moduleobject;
	if ($tmpmodule != 'api') {
		$tmpmodule = preg_replace('/api$/i', '', $tmpmodule);
	}
	$classfile = str_replace('_', '', $tmpmodule);

	// Special cases that does not match name rules conventions
	if ($moduleobject == 'supplierproposals') {
		$classfile = 'supplier_proposals';
	}
	if ($moduleobject == 'supplierorders') {
		$classfile = 'supplier_orders';
	}
	if ($moduleobject == 'supplierinvoices') {
		$classfile = 'supplier_invoices';
	}
	if ($moduleobject == 'ficheinter') {
		$classfile = 'interventions';
	}
	if ($moduleobject == 'interventions') {
		$classfile = 'interventions';
	}
	if ($moduleobject == 'eventattendees') {
		$moduledirforclass = 'eventorganization';
	}

	$dir_part_file = dol_buildpath('/'.$moduledirforclass.'/class/api_'.$classfile.'.class.php', 0, 2);

	$classname = ucwords($moduleobject);

	// Test rules on endpoints. For example:
	// $conf->global->API_ENDPOINT_RULES = 'endpoint1:1,endpoint2:1,...'
	if (getDolGlobalString('API_ENDPOINT_RULES')) {
		$listofendpoints = explode(',', getDolGlobalString('API_ENDPOINT_RULES'));
		$endpointisallowed = false;

		foreach ($listofendpoints as $endpointrule) {
			$tmparray = explode(':', $endpointrule);
			if (($classfile == $tmparray[0] || $classfile.'api' == $tmparray[0]) && $tmparray[1] == 1) {
				$endpointisallowed = true;
				break;
			}
		}

		if (! $endpointisallowed) {
			dol_syslog('The API with endpoint /'.$classfile.' is forbidden by config API_ENDPOINT_RULES', LOG_WARNING);
			print 'The API with endpoint /'.$classfile.' is forbidden by config API_ENDPOINT_RULES';
			header('HTTP/1.1 501 API is forbidden by API_ENDPOINT_RULES');
			//session_destroy();
			exit(0);
		}
	}

	$parameters = array('url' => $url, 'ip' => getUserRemoteIP(), 'moduleobject' => $moduleobject, 'classfile' => $classfile, 'classname' => $classname);
	$object = $api;
	$action = $api->r->requestMethod;
	// Note that $action and $object may be modified by some hooks
	$reshook = $hookmanager->executeHooks('beforeApiCall', $parameters, $object, $action);
	if ($reshook < 0) {
		dol_syslog('beforeapicall Failed to call hook '.$hookmanager->error, LOG_ERR);
	}

	dol_syslog('Search api file /'.$moduledirforclass.'/class/api_'.$classfile.'.class.php => dir_part_file='.$dir_part_file.', classname='.$classname);

	$res = false;
	if ($dir_part_file) {
		$res = include_once $dir_part_file;
	}
	if (!$res) {
		dol_syslog('Failed to make include_once '.$dir_part_file, LOG_WARNING);
		print 'API not found (failed to include API file)';
		header('HTTP/1.1 501 API not found (failed to include API file)');
		//session_destroy();
		exit(0);
	}

	if (class_exists($classname)) {
		$api->r->addAPIClass($classname);
	}
}


//var_dump($api->r->apiVersionMap);
//exit;

// We do not want that restler outputs data if we use native compression (default behaviour) but we want to have it returned into a string.
// If API_DISABLE_COMPRESSION is set, returnResponse is false => It use default handling so output result directly.
$usecompression = (!getDolGlobalString('API_DISABLE_COMPRESSION') && !empty($_SERVER['HTTP_ACCEPT_ENCODING']));
$foundonealgorithm = 0;
if ($usecompression) {
	if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'br') !== false && function_exists('brotli_compress')) {
		$foundonealgorithm++;
	}
	if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'bz') !== false && function_exists('bzcompress')) {
		$foundonealgorithm++;
	}
	if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('gzencode')) {
		$foundonealgorithm++;
	}
	if (!$foundonealgorithm) {
		$usecompression = false;
	}
}

//dol_syslog('We found some compression algorithm: '.$foundonealgorithm.' -> usecompression='.$usecompression, LOG_DEBUG);

Luracast\Restler\Defaults::$returnResponse = $usecompression;

// Call API (we suppose we found it).
// The handle will use the file api/temp/routes.php to get data to run the API. If the file exists and the entry for API is not found, it will return 404.
$responsedata = $api->r->handle();

if (Luracast\Restler\Defaults::$returnResponse) {
	// We try to compress the data received data
	if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'br') !== false && function_exists('brotli_compress') && defined('BROTLI_TEXT')) {
		header('Content-Encoding: br');
		$result = brotli_compress($responsedata, 11, constant('BROTLI_TEXT'));
	} elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'bz') !== false && function_exists('bzcompress')) {
		header('Content-Encoding: bz');
		$result = bzcompress($responsedata, 9);
	} elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('gzencode')) {
		header('Content-Encoding: gzip');
		$result = gzencode($responsedata, 9);
	} else {
		header('Content-Encoding: text/html');
		print "No compression method found. Try to disable compression by adding API_DISABLE_COMPRESSION=1";
		exit(0);
	}

	// Restler did not output data yet, we return it now
	echo $result;
}

if (getDolGlobalInt("API_ENABLE_COUNT_CALLS") && $api->r->responseCode == 200) {
	$error = 0;
	$db->begin();
	$userid = DolibarrApiAccess::$user->id;

	$sql = "SELECT up.value";
	$sql .= " FROM ".MAIN_DB_PREFIX."user_param as up";
	$sql .= " WHERE up.param = 'API_COUNT_CALL'";
	$sql .= " AND up.fk_user = ".((int) $userid);
	$sql .= " AND up.entity = ".((int) $conf->entity);

	$result = $db->query($sql);
	if ($result) {
		$updateapi = false;
		$nbrows = $db->num_rows($result);
		if ($nbrows == 0) {
			$sql2 = "INSERT INTO ".MAIN_DB_PREFIX."user_param";
			$sql2 .= " (fk_user, entity, param, value)";
			$sql2 .= " VALUES (".((int) $userid).", ".((int) $conf->entity).", 'API_COUNT_CALL', 1)";
		} else {
			$updateapi = true;
			$sql2 = "UPDATE ".MAIN_DB_PREFIX."user_param as up";
			$sql2 .= " SET up.value = up.value + 1";
			$sql2 .= " WHERE up.param = 'API_COUNT_CALL'";
			$sql2 .= " AND up.fk_user = ".((int) $userid);
			$sql2 .= " AND up.entity = ".((int) $conf->entity);
		}

		$result2 = $db->query($sql2);
		if (!$result2) {
			$modeapicall = $updateapi ? 'updating' : 'inserting';
			dol_syslog('Error while '.$modeapicall. ' API_COUNT_CALL for user '.$userid, LOG_ERR);
			$error++;
		}
	} else {
		dol_syslog('Error on select API_COUNT_CALL for user '.$userid, LOG_ERR);
		$error++;
	}

	if ($error) {
		$db->rollback();
	} else {
		$db->commit();
	}
}

// Call API termination method
$apiMethodInfo = &$api->r->apiMethodInfo;
$terminateCall = '_terminate_' . $apiMethodInfo->methodName . '_' . $api->r->responseFormat->getExtension();
if (method_exists($apiMethodInfo->className, $terminateCall)) {
	// Now flush output buffers so that response data is sent to the client even if we still have action to do in a termination method.
	ob_end_flush();

	// If you're using PHP-FPM, this function will allow you to send the response and then continue processing
	if (function_exists('fastcgi_finish_request')) {
		fastcgi_finish_request();
	}

	// Call a termination method. Warning: This method can do I/O, sync but must not make output.
	call_user_func(array(Luracast\Restler\Scope::get($apiMethodInfo->className), $terminateCall), $responsedata);
}

//session_destroy();
