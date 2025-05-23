<?php
/* Copyright (C) 2009-2015 	Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2011-2022 	Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024       Charlene Benke          <charlene@patas-monkey.com>
 * Copyright (C) 2025       Marc de Lima Lucio      <marc-dll@user.noreply.github.com>
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

// Need global variable $urllogo, $title and $titletruedolibarrversion to be defined by caller (like dol_loginfunction in security2.lib.php)
// Caller can also set 	$morelogincontent = array(['options']=>array('js'=>..., 'table'=>...);
// $titletruedolibarrversion must be defined

if (!defined('NOBROWSERNOTIF')) {
	define('NOBROWSERNOTIF', 1);
}
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 *
 * @var string $dolibarr_main_force_https
 *
 * @var string $captcha
 *
 * @var int<0,1> $dol_hide_leftmenu
 * @var int<0,1> $dol_hide_topmenu
 * @var int<0,1> $dol_no_mouse_hover
 * @var int<0,1> $dol_optimize_smallscreen
 * @var int<0,1> $dol_use_jmobile
 * @var string $focus_element
 * @var string $login
 * @var string $main_authentication
 * @var string $main_home
 * @var string $password
 * @var string $session_name
 * @var string $title
 * @var string $titletruedolibarrversion
 * @var string $urllogo
 * @var int<0,1> $forgetpasslink
 */
// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf)) {
	print "Error, template page can't be called as URL";
	exit(1);
}

// DDOS protection
$size = (empty($_SERVER['CONTENT_LENGTH']) ? 0 : (int) $_SERVER['CONTENT_LENGTH']);
if ($size > 10000) {
	$langs->loadLangs(array("errors", "install"));
	httponly_accessforbidden('<center>'.$langs->trans("ErrorRequestTooLarge").'.<br><a href="'.DOL_URL_ROOT.'">'.$langs->trans("ClickHereToGoToApp").'</a></center>', 413, 1);
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

'
@phan-var-force HookManager $hookmanager
@phan-var-force string $action
@phan-var-force string $captcha
@phan-var-force int<0,1> $dol_hide_leftmenu
@phan-var-force int<0,1> $dol_hide_topmenu
@phan-var-force int<0,1> $dol_no_mouse_hover
@phan-var-force int<0,1> $dol_optimize_smallscreen
@phan-var-force int<0,1> $dol_use_jmobile
@phan-var-force string $focus_element
@phan-var-force string $login
@phan-var-force string $main_authentication
@phan-var-force string $main_home
@phan-var-force string $password
@phan-var-force string $session_name
@phan-var-force string $titletruedolibarrversion
@phan-var-force string $urllogo
@phan-var-force int<0,1> $forgetpasslink
';

/**
 * @var HookManager $hookmanager
 * @var string $action
 * @var string $captcha
 * @var string $message
 * @var string $title
 */


/*
 * View
 */

header('Cache-Control: Public, must-revalidate');

if (GETPOST('dol_hide_topmenu')) {
	$conf->dol_hide_topmenu = 1;
}
if (GETPOST('dol_hide_leftmenu')) {
	$conf->dol_hide_leftmenu = 1;
}
if (GETPOST('dol_optimize_smallscreen')) {
	$conf->dol_optimize_smallscreen = 1;
}
if (GETPOST('dol_no_mouse_hover')) {
	$conf->dol_no_mouse_hover = 1;
}
if (GETPOST('dol_use_jmobile')) {
	$conf->dol_use_jmobile = 1;
}

// If we force to use jmobile, then we reenable javascript
if (!empty($conf->dol_use_jmobile)) {
	$conf->use_javascript_ajax = 1;
}

$php_self = empty($php_self) ? dol_escape_htmltag($_SERVER['PHP_SELF']) : $php_self;
if (!empty($_SERVER["QUERY_STRING"]) && dol_escape_htmltag($_SERVER["QUERY_STRING"])) {
	$php_self .= '?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]);
}
if (!preg_match('/mainmenu=/', $php_self)) {
	$php_self .= (preg_match('/\?/', $php_self) ? '&' : '?').'mainmenu=home';
}
if (preg_match('/'.preg_quote('core/modules/oauth', '/').'/', $php_self)) {
	$php_self = DOL_URL_ROOT.'/index.php?mainmenu=home';
}
$php_self = preg_replace('/(\?|&amp;|&)action=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)actionlogin=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)afteroauthloginreturn=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)username=[^&]*/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)entity=\d+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)massaction=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(\?|&amp;|&)token=[^&]+/', '\1', $php_self);
$php_self = preg_replace('/(&amp;)+/', '&amp;', $php_self);

// Javascript code on logon page only to detect user tz, dst_observed, dst_first, dst_second
$arrayofjs = array(
	'/core/js/dst.js'.(empty($conf->dol_use_jmobile) ? '' : '?version='.urlencode(DOL_VERSION))
);

// We display application title instead Login term
if (getDolGlobalString('MAIN_APPLICATION_TITLE')) {
	$titleofloginpage = getDolGlobalString('MAIN_APPLICATION_TITLE');
} else {
	$titleofloginpage = $langs->trans('Login');
}
$titleofloginpage .= ' @ '.$titletruedolibarrversion; // $titletruedolibarrversion is defined by dol_loginfunction in security2.lib.php. We must keep the @, some tools use it to know it is login page and find true dolibarr version.

$disablenofollow = 1;
if (!preg_match('/'.constant('DOL_APPLICATION_TITLE').'/', $title)) {
	$disablenofollow = 0;
}
if (getDolGlobalString('MAIN_OPTIMIZEFORTEXTBROWSER')) {
	$disablenofollow = 0;
}

// If OpenID Connect is set as an authentication
if (getDolGlobalInt('MAIN_MODULE_OPENIDCONNECT', 0) > 0 && isset($conf->file->main_authentication) && preg_match('/openid_connect/', $conf->file->main_authentication)) {
	// Set a cookie to transfer rollback page information
	$prefix = dol_getprefix('');
	if (empty($_COOKIE["DOL_rollback_url_$prefix"])) {
		dolSetCookie('DOL_rollback_url_'.$prefix, $_SERVER['REQUEST_URI'], time() + 3600);	// $_SERVER["REQUEST_URI"] is for example /mydolibarr/mypage.php
	}

	// Auto redirect if OpenID Connect is the only authentication
	if ($conf->file->main_authentication === 'openid_connect') {
		// Avoid redirection hell
		if (empty(GETPOST('openid_mode'))) {
			dol_include_once('/core/lib/openid_connect.lib.php');
			header("Location: " . openid_connect_get_url(), true, 302);
		} elseif (!empty($_SESSION['dol_loginmesg'])) {
			// Show login error without the login form
			print '<div class="center login_main_message"><div class="error">' . dol_escape_htmltag($_SESSION['dol_loginmesg']) . '</div></div>';
		}
		// We shouldn't continue executing this page
		exit();
	}
}

top_htmlhead('', $titleofloginpage, 0, 0, $arrayofjs, array(), 1, $disablenofollow);

$helpcenterlink = getDolGlobalString('MAIN_HELPCENTER_LINKTOUSE');

$colorbackhmenu1 = '60,70,100'; // topmenu
if (!isset($conf->global->THEME_ELDY_TOPMENU_BACK1)) {
	$conf->global->THEME_ELDY_TOPMENU_BACK1 = $colorbackhmenu1;
}
$colorbackhmenu1 = getDolUserString('THEME_ELDY_ENABLE_PERSONALIZED') ? getDolUserString('THEME_ELDY_TOPMENU_BACK1', $colorbackhmenu1) : getDolGlobalString('THEME_ELDY_TOPMENU_BACK1', $colorbackhmenu1);
$colorbackhmenu1 = implode(',', colorStringToArray($colorbackhmenu1)); // Normalize value to 'x,y,z'

print "<!-- BEGIN PHP TEMPLATE LOGIN.TPL.PHP -->\n";

if (getDolGlobalString('ADD_UNSPLASH_LOGIN_BACKGROUND')) {
	// For example $conf->global->ADD_UNSPLASH_LOGIN_BACKGROUND = 'https://source.unsplash.com/random'?>
	<body class="body bodylogin" style="background-image: url('<?php echo dol_escape_htmltag(getDolGlobalString('ADD_UNSPLASH_LOGIN_BACKGROUND')); ?>'); background-repeat: no-repeat; background-position: center center; background-attachment: fixed; background-size: cover; background-color: #ffffff;">
	<?php
} else {
	?>
	<body class="body bodylogin"<?php print !getDolGlobalString('MAIN_LOGIN_BACKGROUND') ? '' : ' style="background-size: cover; background-position: center center; background-attachment: fixed; background-repeat: no-repeat; background-image: url(\''.DOL_URL_ROOT.'/viewimage.php?cache=1&noalt=1&modulepart=mycompany&file=logos/'.urlencode(getDolGlobalString('MAIN_LOGIN_BACKGROUND')).'\')"'; ?>>
	<?php
}
?>

<?php if (empty($conf->dol_use_jmobile)) { ?>
<script>
$(document).ready(function () {
	/* Set focus on correct field */
	<?php if ($focus_element) {
		?>$('#<?php echo $focus_element; ?>').focus(); <?php
	} ?>		// Warning to use this only on visible element
});
</script>
<?php } ?>

<div class="login_center center"<?php
if (!getDolGlobalString('ADD_UNSPLASH_LOGIN_BACKGROUND')) {
	$backstyle = 'background: linear-gradient('.((!empty($conf->browser->layout) && $conf->browser->layout == 'phone') ? '0deg' : '4deg').', var(--colorbackbody) 52%, rgb('.$colorbackhmenu1.') 52.1%);';
	// old style:  $backstyle = 'background-image: linear-gradient(rgb('.$colorbackhmenu1.',0.3), rgb(240,240,240));';
	$backstyle = getDolGlobalString('MAIN_LOGIN_BACKGROUND_STYLE', $backstyle);
	print !getDolGlobalString('MAIN_LOGIN_BACKGROUND') ? ' style="background-size: cover; background-position: center center; background-attachment: fixed; background-repeat: no-repeat; '.$backstyle.'"' : '';
}
?>>
<div class="login_vertical_align">


<form id="login" name="login" method="post" action="<?php echo $php_self; ?>">

<input type="hidden" name="token" value="<?php echo newToken(); ?>" />
<input type="hidden" name="actionlogin" id="actionlogin" value="login">
<input type="hidden" name="loginfunction" id="loginfunction" value="loginfunction" />
<input type="hidden" name="backtopage" value="<?php echo GETPOST('backtopage'); ?>" />
<!-- Add fields to store and send local user information. This fields are filled by the core/js/dst.js -->
<input type="hidden" name="tz" id="tz" value="" />
<input type="hidden" name="tz_string" id="tz_string" value="" />
<input type="hidden" name="dst_observed" id="dst_observed" value="" />
<input type="hidden" name="dst_first" id="dst_first" value="" />
<input type="hidden" name="dst_second" id="dst_second" value="" />
<input type="hidden" name="screenwidth" id="screenwidth" value="" />
<input type="hidden" name="screenheight" id="screenheight" value="" />
<input type="hidden" name="dol_hide_topmenu" id="dol_hide_topmenu" value="<?php echo $dol_hide_topmenu; ?>" />
<input type="hidden" name="dol_hide_leftmenu" id="dol_hide_leftmenu" value="<?php echo $dol_hide_leftmenu; ?>" />
<input type="hidden" name="dol_optimize_smallscreen" id="dol_optimize_smallscreen" value="<?php echo $dol_optimize_smallscreen; ?>" />
<input type="hidden" name="dol_no_mouse_hover" id="dol_no_mouse_hover" value="<?php echo $dol_no_mouse_hover; ?>" />
<input type="hidden" name="dol_use_jmobile" id="dol_use_jmobile" value="<?php echo $dol_use_jmobile; ?>" />



<!-- Title with version -->
<div class="login_table_title center" tabindex="-1" title="<?php echo dol_escape_htmltag($title); ?>">
<?php
if ($disablenofollow) {
	echo '<a class="login_table_title" tabindex="-1" href="https://www.dolibarr.org" target="_blank" rel="noopener noreferrer external">';
}
echo dol_escape_htmltag($title);
if ($disablenofollow) {
	echo '</a>';
}
?>
</div>



<div class="login_table">

<div id="login_line1">

<div id="login_left">
<img alt="" src="<?php echo $urllogo; ?>" id="img_logo" />
</div>

<br>

<div id="login_right">

<div class="tagtable left centpercent" title="<?php echo $langs->trans("EnterLoginDetail"); ?>">

<!-- Login -->
<?php if (!isset($conf->file->main_authentication) || $conf->file->main_authentication != 'googleoauth') { ?>
<div class="trinputlogin">
<div class="tagtd nowraponall center valignmiddle tdinputlogin">
	<?php if (getDolGlobalString('MAIN_OPTIMIZEFORTEXTBROWSER')) {
		?><label for="username" class="hidden"><?php echo $langs->trans("Login"); ?></label><?php
	} ?>
<!-- <span class="span-icon-user">-->
<span class="fa fa-user"></span>
<input type="text" id="username" maxlength="255" placeholder="<?php echo $langs->trans("Login"); ?>" name="username" class="flat input-icon-user minwidth150" value="<?php echo dol_escape_htmltag($login); ?>" tabindex="1" autofocus="autofocus" autocapitalize="off" autocomplete="on" spellcheck="false" autocorrect="off" />
</div>
</div>

<!-- Password -->
<div class="trinputlogin">
<div class="tagtd nowraponall center valignmiddle tdinputlogin" id="tdpasswordlogin">
	<?php if (getDolGlobalString('MAIN_OPTIMIZEFORTEXTBROWSER')) {
		?><label for="password" class="hidden"><?php echo $langs->trans("Password"); ?></label><?php
	} ?>
<!--<span class="span-icon-password">-->
<span class="fa fa-key"></span>
<input type="password" id="password" maxlength="128" placeholder="<?php echo $langs->trans("Password"); ?>" name="password" class="flat input-icon-password minwidth150" value="<?php echo dol_escape_htmltag($password); ?>" tabindex="2" autocomplete="<?php echo !getDolGlobalString('MAIN_LOGIN_ENABLE_PASSWORD_AUTOCOMPLETE') ? 'off' : 'on'; ?>" />
	<?php
	include_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';
	print showEyeForField('togglepassword', 'password');
	?>
</div></div>
<?php } ?>


<?php
if (!empty($captcha)) {
	// Add a variable param to force not using cache (jmobile)
	$php_self = preg_replace('/[&\?]time=(\d+)/', '', $php_self); // Remove param time
	if (preg_match('/\?/', $php_self)) {
		$php_self .= '&time='.dol_print_date(dol_now(), 'dayhourlog');
	} else {
		$php_self .= '?time='.dol_print_date(dol_now(), 'dayhourlog');
	}

	// List of directories where we can find captcha handlers
	$dirModCaptcha = array_merge(array('main' => '/core/modules/security/captcha/'), (isset($conf->modules_parts['captcha']) && is_array($conf->modules_parts['captcha'])) ? $conf->modules_parts['captcha'] : array());
	$fullpathclassfile = '';
	foreach ($dirModCaptcha as $dir) {
		$fullpathclassfile = dol_buildpath($dir."modCaptcha".ucfirst($captcha).'.class.php', 0, 2);
		if ($fullpathclassfile) {
			break;
		}
	}

	if ($fullpathclassfile) {
		include_once $fullpathclassfile;
		$captchaobj = null;

		// Charging the numbering class
		$classname = "modCaptcha".ucfirst($captcha);
		if (class_exists($classname)) {
			/** @var ModeleCaptcha $captchaobj */
			$captchaobj = new $classname($db, $conf, $langs, null);
			'@phan-var-force ModeleCaptcha $captchaobj';

			if (is_object($captchaobj) && method_exists($captchaobj, 'getCaptchaCodeForForm')) {
				print $captchaobj->getCaptchaCodeForForm($php_self); // @phan-suppress-current-line PhanUndeclaredMethod
			} else {
				print 'Error, the captcha handler '.get_class($captchaobj).' does not have any method getCaptchaCodeForForm()';
			}
		} else {
			print 'Error, the captcha handler class '.$classname.' was not found after the include';
		}
	} else {
		print 'Error, the captcha handler '.$captcha.' has no class file found modCaptcha'.ucfirst($captcha);
	}
}

if (!empty($morelogincontent)) {
	if (is_array($morelogincontent)) {
		foreach ($morelogincontent as $format => $option) {
			if ($format == 'table') {
				echo '<!-- Option by hook -->';
				echo $option;
			}
		}
	} else {
		echo '<!-- Option by hook -->';
		echo $morelogincontent;
	}
}

?>

</div>

</div> <!-- end div login_right -->

</div> <!-- end div login_line1 -->


<div id="login_line2" style="clear: both">


<!-- Button Connection -->
<?php if (!isset($conf->file->main_authentication) || $conf->file->main_authentication != 'googleoauth') { ?>
<br>
<div id="login-submit-wrapper">
<input type="submit" class="button" value="&nbsp; <?php echo $langs->trans('Connection'); ?> &nbsp;" tabindex="5" />
</div>
<?php } ?>


<?php
if (isset($conf->file->main_authentication) && $conf->file->main_authentication == 'googleoauth') {
	$forgetpasslink = '';
}

if ($forgetpasslink || $helpcenterlink) {
	$moreparam = '';
	if ($dol_hide_topmenu) {
		$moreparam .= (strpos($moreparam, '?') === false ? '?' : '&').'dol_hide_topmenu='.$dol_hide_topmenu;
	}
	if ($dol_hide_leftmenu) {
		$moreparam .= (strpos($moreparam, '?') === false ? '?' : '&').'dol_hide_leftmenu='.$dol_hide_leftmenu;
	}
	if ($dol_no_mouse_hover) {
		$moreparam .= (strpos($moreparam, '?') === false ? '?' : '&').'dol_no_mouse_hover='.$dol_no_mouse_hover;
	}
	if ($dol_use_jmobile) {
		$moreparam .= (strpos($moreparam, '?') === false ? '?' : '&').'dol_use_jmobile='.$dol_use_jmobile;
	}

	echo '<br>';
	echo '<div class="center" style="margin-top: 5px;">';
	if ($forgetpasslink) {
		$url = DOL_URL_ROOT.'/user/passwordforgotten.php'.$moreparam;
		if (getDolGlobalString('MAIN_PASSWORD_FORGOTLINK')) {
			$url = getDolGlobalString('MAIN_PASSWORD_FORGOTLINK');
		}
		echo '<a class="alogin" href="'.dol_escape_htmltag($url).'">';
		echo $langs->trans('PasswordForgotten');
		echo '</a>';
	}

	if ($forgetpasslink && $helpcenterlink) {
		echo '&nbsp;-&nbsp;';
	}

	if ($helpcenterlink) {
		echo '<a class="alogin" href="'.dol_escape_htmltag($helpcenterlink).'" target="_blank" rel="noopener noreferrer">';
		echo $langs->trans('NeedHelpCenter');
		echo '</a>';
	}
	echo '</div>';
}

if (getDolGlobalInt('MAIN_MODULE_OPENIDCONNECT', 0) > 0 && isset($conf->file->main_authentication) && preg_match('/openid/', $conf->file->main_authentication)) {
	dol_include_once('/core/lib/openid_connect.lib.php');
	$langs->load("users");

	print '<div class="center" style="margin-top: 20px; margin-bottom: 10px">';
	print '<div class="loginbuttonexternal">';

	if (!getDolGlobalString("MAIN_AUTHENTICATION_OPENID_URL")) {
		$url = openid_connect_get_url();
	} else {
		$url = getDolGlobalString('MAIN_AUTHENTICATION_OPENID_URL').'&state=' . openid_connect_get_state();
	}
	if (!empty($url)) {
		print '<a class="alogin" href="'.$url.'">'.$langs->trans("LoginUsingOpenID").'</a>';
	} else {
		$langs->load("errors");
		print '<span class="warning">'.$langs->trans("ErrorOpenIDSetupNotComplete", 'MAIN_AUTHENTICATION_OPENID_URL').'</span>';
	}

	print '</div>';
	print '</div>';
}

if (isset($conf->file->main_authentication) && preg_match('/google/', $conf->file->main_authentication) && strpos($conf->browser->ua, 'DoliDroid') === false) {
	$langs->load("users");

	echo '<div class="center" style="margin-top: 20px; margin-bottom: 10px">';

	/*global $dolibarr_main_url_root;

	// Define $urlwithroot
	$urlwithouturlroot = preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', trim($dolibarr_main_url_root));
	$urlwithroot = $urlwithouturlroot.DOL_URL_ROOT; // This is to use external domain name found into config file
	//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

	//$shortscope = 'userinfo_email,userinfo_profile';
	$shortscope = 'openid,email,profile';	// For openid connect

	$oauthstateanticsrf = bin2hex(random_bytes(128/8));
	$_SESSION['oauthstateanticsrf'] = $shortscope.'-'.$oauthstateanticsrf;
	$urltorenew = $urlwithroot.'/core/modules/oauth/google_oauthcallback.php?shortscope='.$shortscope.'&state=forlogin-'.$shortscope.'-'.$oauthstateanticsrf;

	//$url = $urltorenew;
	 */

	print '<input type="hidden" name="beforeoauthloginredirect" id="beforeoauthloginredirect" value="">';
	print '<a class="alogin" href="#" onclick="console.log(\'Set beforeoauthloginredirect value\'); jQuery(\'#beforeoauthloginredirect\').val(\'google\'); $(this).closest(\'form\').submit(); return false;">';
	print '<div class="loginbuttonexternal">';
	print img_picto('', 'google', 'class="pictofixedwidth"');
	print $langs->trans("LoginWith", "Google");
	print '</div>';
	print '</a>';
	print '</div>';
}

?>

</div> <!-- end login line 2 -->

</div> <!-- end login table -->


</form>


<?php
$message = '';
// Show error message if defined
if (!empty($_SESSION['dol_loginmesg'])) {
	$message = $_SESSION['dol_loginmesg'];	// By default this is an error message
}
if (!empty($message)) {
	if (!empty($conf->use_javascript_ajax)) {
		if (preg_match('/<!-- warning -->/', $message)) {	// if it contains this comment, this is a warning message
			$message = str_replace('<!-- warning -->', '', $message);
			dol_htmloutput_mesg($message, array(), 'warning');
		} else {
			dol_htmloutput_mesg($message, array(), 'error');
		}
		print '<script>
			$(document).ready(function() {
				$(".jnotify-container").addClass("jnotify-container-login");
			});
		</script>';
	} else {
		?>
		<div class="center login_main_message">
		<?php
		if (preg_match('/<!-- warning -->/', $message)) {	// if it contains this comment, this is a warning message
			$message = str_replace('<!-- warning -->', '', $message);
			print '<div class="warning" role="alert">';
		} else {
			print '<div class="error" role="alert">';
		}
		print dol_escape_htmltag($message);
		print '</div>'; ?>
		</div>
		<?php
	}
}

// Add commit strip
if (getDolGlobalString('MAIN_EASTER_EGG_COMMITSTRIP')) {
	include_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
	if (substr($langs->defaultlang, 0, 2) == 'fr') {
		$resgetcommitstrip = getURLContent("https://www.commitstrip.com/fr/feed/");
	} else {
		$resgetcommitstrip = getURLContent("https://www.commitstrip.com/en/feed/");
	}
	if ($resgetcommitstrip && $resgetcommitstrip['http_code'] == '200') {
		if (LIBXML_VERSION < 20900) {
			// Avoid load of external entities (security problem).
			// Required only if LIBXML_VERSION < 20900
			// @phan-suppress-next-line PhanDeprecatedFunctionInternal
			libxml_disable_entity_loader(true);
		}

		$xml = simplexml_load_string($resgetcommitstrip['content'], 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
		// @phan-suppress-next-line PhanPluginUnknownObjectMethodCall
		$little = $xml->channel->item[0]->children('content', true);
		print preg_replace('/width="650" height="658"/', '', $little->encoded);
	}
}

?>

<?php if ($main_home) {
	?>
	<div class="center login_main_home paddingtopbottom <?php echo !getDolGlobalString('MAIN_LOGIN_BACKGROUND') ? '' : ' backgroundsemitransparent boxshadow'; ?>" style="max-width: 70%">
	<?php echo $main_home; ?>
	</div><br>
	<?php
}
?>

<!-- authentication mode = <?php echo $main_authentication ?> -->
<!-- cookie name used for this session = <?php echo $session_name ?> -->
<!-- urlfrom in this session = <?php echo isset($_SESSION["urlfrom"]) ? $_SESSION["urlfrom"] : ''; ?> -->

<!-- Common footer is not used for login page, this is same than footer but inside login tpl -->

<?php

print getDolGlobalString('MAIN_HTML_FOOTER');

if (!empty($morelogincontent) && is_array($morelogincontent)) {
	foreach ($morelogincontent as $format => $option) {
		if ($format == 'js') {
			echo "\n".'<!-- Javascript by hook -->';
			echo $option."\n";
		}
	}
} elseif (!empty($moreloginextracontent)) {
	echo '<!-- Javascript by hook -->';
	echo $moreloginextracontent;
}

// Can add extra content
$parameters = array();
$dummyobject = new stdClass();
$result = $hookmanager->executeHooks('getLoginPageExtraContent', $parameters, $dummyobject, $action);
print $hookmanager->resPrint;

?>


</div>
</div><!-- end of center -->


</body>
</html>
<!-- END PHP TEMPLATE -->
