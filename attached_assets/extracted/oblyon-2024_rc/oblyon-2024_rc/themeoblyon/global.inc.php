<?php if (! defined('ISLOADEDBYSTEELSHEET')) die('Must be call by steelsheet'); ?>
/* <style type="text/css" > */

/* ============================================================================== */
/* Default styles                                                                 */
/* ============================================================================== */

:root {
	--fontawesomeFamily: "<?php print $fontawesomeFamily; ?>";
	--fontawesomeBrands: "<?php print $fontawesomeBrands; ?>";
    --fontawesomeWeight: <?php echo getDolGlobalString('MAIN_FONTAWESOME_WEIGHT', '900'); ?>;
	--bgcolor: <?php print $bgcolor; ?>;
	--colorbackhmenu1: rgb(<?php print $colorbackhmenu1; ?>);
	--colorbackvmenu1: rgb(<?php print $colorbackvmenu1; ?>);
	--colorbacktitle1: rgb(<?php print $colorbacktitle1; ?>);
	--colorbacktabcard1: rgb(<?php print $colorbacktabcard1; ?>);
	--colorbacktabactive: rgb(<?php print $colorbacktabactive; ?>);
	--colorbacklineimpair1: rgb(<?php print $colorbacklineimpair1; ?>);
	--colorbacklineimpair2: rgb(<?php print $colorbacklineimpair2; ?>);
	--colorbacklinepair1: rgb(<?php print $colorbacklinepair1; ?>);
	--colorbacklinepair2: rgb(<?php print $colorbacklinepair2; ?>);
	--colorbacklinepairhover: rgb(<?php print $colorbacklinepairhover; ?>);
	--colorbacklinepairchecked: rgb(<?php print $colorbacklinepairchecked; ?>);
	--colorbacklinebreak: rgb(<?php print $colorbacklinebreak; ?>);
	--colorbackbody: rgb(<?php print $colorbackbody; ?>);
	--colorbackmobilemenu: #f8f8f8;
	--colorbackgrey: #f0f0f0;
    --colorfline: <?php echo $colorfline; ?>;
	--colortexttitlenotab: rgb(<?php print $colortexttitlenotab; ?>);
	--colortexttitlenotab2: rgb(<?php print $colortexttitlenotab2; ?>);
	--colortexttitle: rgba(<?php print $colortexttitle; ?>, 0.9);
	--colortexttitlelink: rgba(<?php print $colortexttitlelink; ?>, 0.9);
	--colortext: rgb(<?php print $colortext; ?>);
	--colortextlink: rgb(<?php print $colortextlink; ?>);
	--colortextbackhmenu: #<?php print $colortextbackhmenu; ?>;
	--colortextbackvmenu: #<?php print $colortextbackvmenu; ?>;
	--colortopbordertitle1: rgb(<?php print $colortopbordertitle1; ?>);
	--listetotal: #888888;
	--inputbackgroundcolor: <?php echo $colorBckgrdInput; ?>;
	--color1BckgrdInfobox: <?php echo $color1BckgrdInfobox; ?>;
	--color2BckgrdInfobox: <?php echo $color2BckgrdInfobox; ?>;
	--colorBorderActionColumn: <?php echo $colorBorderActionColumn; ?>;
	--inputbordercolor: rgba(0,0,0,.15);
	--tooltipbgcolor: <?php print $toolTipBgColor; ?>;
	--tooltipfontcolor : <?php print $toolTipFontColor; ?>;
	--oddevencolor: #202020;
	--colorboxstatsborder: <?php echo $bgnavtop ?>;
	--dolgraphbg: rgba(255,255,255,0);
	--fieldrequiredcolor: #400030;
    --fontfamilydol:<?php print $fontlisted; ?>;
    --fontsize:<?php print $fontsize; ?>;
	--colortextbacktab: <?php print $colorTextTabActive; ?>;
	--colorboxiconbg: #eee;
	--refidnocolor:#444;
	--tableforfieldcolor:#666;
	--amountremaintopaycolor:#880000;
	--amountpaymentcomplete:#008800;
	--amountremaintopaybackcolor:none;
	--productlinestockod: #002200;
	--productlinestocktoolow: #884400;
	--infoboxmoduleenabledbgcolor : linear-gradient(0.4turn, #fff, #fff, #fff, #e4efe8);
	--invertratiofilter: <?php print $invertratiofilter; ?>;
	--colorfdatedefault: <?php print $colorfdatedefault; ?>;
    --colorfdateselected: <?php print $colorfdateselected; ?>;
}

/*------------------------------------*\
#Eric Meyer's Reset CSS v2.0
\*------------------------------------*/

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed,
figure, figcaption, footer, header, hgroup,
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font-family: var(--fontfamilydol);
/*	font: inherit;	*/
/*	vertical-align: middle;	*/
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure,
footer, header, hgroup, menu, nav, section {
    display: block;
}
body {
    line-height: 1;
}
blockquote, q {
    quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
    content: '';
    content: none;
}
table {
    border-collapse: collapse;
    border-spacing: 0;
}

/*------------------------------------*\
#BASE
\*------------------------------------*/

html {
    box-sizing: border-box;
}
*, *:before, *:after {
    box-sizing: inherit;
}

html, body {
    height: 100%;
    font-size: 100%;
    font-family: var(--fontfamilydol);
}

body {
<?php print 'direction: '.$langs->trans("DIRECTION").";\n"; ?>
<?php if (GETPOST("optioncss") == 'print') {	?>
    background-color: #fff !important;
<?php } else { ?>
    background-color: var(--bgcolor)!important;
<?php } ?>
    color: var(--colorfline) !important;
    font-family: var(--fontfamilydol)!important;
<?php if (empty($dol_use_jmobile) || 1==1) { ?>
    font-size: <?php print $fontsize; ?>px;
<?php } ?>
    -webkit-font-smoothing: subpixel-antialiased;
    margin: 0;
}

/* Style used to protect html content in output to avoid attack by replacing full page with js content */
.sensiblehtmlcontent * {
	position: static !important;
}

.thumbstat { font-weight: bold !important; }

th a { font-weight: <?php echo ($useboldtitle ? 'bold' : 'normal'); ?> !important; }
a.tab { font-weight: 500 !important; }

a:link, a:visited, a:hover, a:active, .classlink { color: var(--colortextlink); text-decoration: none;  }
a:hover { text-decoration: underline; color: var(--colortextlink); }
a.commonlink { color: var(--colortextlink) !important; text-decoration: none; }
th.liste_titre a div div:hover, th.liste_titre_sel a div div:hover { text-decoration: underline; }
/*
tr.liste_titre th.liste_titre_sel:not(.maxwidthsearch), tr.liste_titre td.liste_titre_sel:not(.maxwidthsearch),
tr.liste_titre th.liste_titre:not(.maxwidthsearch), tr.liste_titre td.liste_titre:not(.maxwidthsearch) { opacity: 0.8; }
*/
/* th.liste_titre_sel a, th.liste_titre a, td.liste_titre_sel a, td.liste_titre a { color: #766; } */

input {
	font-size: unset;
}
input, input.flat, textarea, textarea.flat, form.flat select, select, select.flat, .dataTables_length label select {
	background-color: var(--inputbackgroundcolor);
	color: var(--colortext);
	border-radius: 2px;
}
select.vmenusearchselectcombo {
	background-color: unset;
}

table.liste th.wrapcolumntitle.liste_titre:not(.maxwidthsearch), table.liste td.wrapcolumntitle.liste_titre:not(.maxwidthsearch) {
	overflow: hidden;
	white-space: nowrap;
	max-width: 100px;
	text-overflow: ellipsis;
}
.liste_titre input[name=month_date_when], .liste_titre input[name=monthvalid], .liste_titre input[name=search_ordermonth], .liste_titre input[name=search_deliverymonth],
.liste_titre input[name=search_smonth], .liste_titre input[name=search_month], .liste_titre input[name=search_emonth], .liste_titre input[name=smonth], .liste_titre input[name=month], .liste_titre select[name=month],
.liste_titre input[name=month_lim], .liste_titre input[name=month_start], .liste_titre input[name=month_end], .liste_titre input[name=month_create],
.liste_titre input[name=search_month_lim], .liste_titre input[name=search_month_start], .liste_titre input[name=search_month_end], .liste_titre input[name=search_month_create],
.liste_titre input[name=search_month_update], .liste_titre input[name=search_month_start], .liste_titre input[name=search_month_end],
.liste_titre input[name=day_date_when], .liste_titre input[name=dayvalid], .liste_titre input[name=search_orderday], .liste_titre input[name=search_deliveryday],
.liste_titre input[name=search_sday], .liste_titre input[name=search_day], .liste_titre input[name=search_eday], .liste_titre input[name=sday], .liste_titre input[name=day], .liste_titre select[name=day],
.liste_titre input[name=day_lim], .liste_titre input[name=day_start], .liste_titre input[name=day_end], .liste_titre input[name=day_create],
.liste_titre input[name=search_day_lim], .liste_titre input[name=search_day_start], .liste_titre input[name=search_day_end], .liste_titre input[name=search_day_create],
.liste_titre input[name=search_day_create], .liste_titre input[name=search_day_start], .liste_titre input[name=search_day_end],
.liste_titre input[name=search_day_date_when], .liste_titre input[name=search_month_date_when], .liste_titre input[name=search_year_date_when],
.liste_titre input[name=search_dtstartday], .liste_titre input[name=search_dtendday], .liste_titre input[name=search_dtstartmonth], .liste_titre input[name=search_dtendmonth],
select#date_startday, select#date_startmonth, select#date_endday, select#date_endmonth, select#reday, select#remonth,
input[name=duration_value], input[name=durationhour]
{
	margin-right: 4px !important;
}
input[type=submit], input[type=submit]:hover {
	margin-left: 5px;
}
input[type=checkbox], input[type=radio] {
	margin: 0 3px 0 3px;
}
input, input.flat, form.flat select, select, select.flat, .dataTables_length label select {
	border: none;
}
input, input.flat, textarea, textarea.flat, form.flat select, select, select.flat, .dataTables_length label select {
	font-family: var(--fontfamilydol);
	outline: none;
	margin: 0px 0px 0px 0px;
	border<?php echo !getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT') ? '-bottom' : ''; ?>: solid 1px var(--inputbordercolor);
}

input {
	line-height: 1.3em;
	padding: 5px;
}
.liste_titre input {
	padding: 5px;
    font-family: var(--fontfamilydol);
}
select {
	padding-top: 5px;
	padding-right: 4px;
	padding-bottom: 5px;
	padding-left: 2px;
}
input, select {
	margin-left: 0px;
	margin-bottom: 1px;
	margin-top: 1px;
}
input.button:hover {
	-webkit-box-shadow: 0px 0px 6px 1px rgb(50 50 50 / 40%), 0px 0px 0px rgb(60 60 60 / 10%);
	box-shadow: 0px 0px 6px 1px rgb(50 50 50 / 40%), 0px 0px 0px rgb(60 60 60 / 10%);
}
input.button:focus {
	border-bottom: 0;
}

input.button.massactionconfirmed {
	margin: 4px;
}

input:invalid, select:invalid {
	border-color: #ea1212;
}














/**
* Headings
*/

h1, h2, h3, h4, h5, h6 {
    font-family: var(--fontfamilydol);
    font-weight: normal;
    font-style: normal;
    color: var(--colorfline);
    text-rendering: optimizeLegibility;
    margin-top: 0.2rem;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

h1 { font-size: 2.125rem; }

h2 { font-size: 1.6875rem; }

h3 { font-size: 1.375rem; }

h4 { font-size: 1.125rem; }

h5 { font-size: 1.125rem; }

h6 { font-size: 1rem; }


form {
    padding:0px;
    margin:0px;
}
div.float
{
    float:<?php print $left; ?>;
}
div.floatright
{
    float:<?php print $right; ?>;
}
.block
{
    display:block;
}
.inline-block
{
    display:inline-block;
}
.inline-blockimp
{
    display:inline-block !important;
}
.largenumber {
    font-size: 1.4em;
}
button[name='button_search_x'] span.fa.fa-search {
    font-size: 1.3em;
}
button[name='button_removefilter_x'] span.fa.fa-remove {
    opacity: 0.5;
    font-size: 1.3em;
}
button:focus {
    outline: none;
}
.fa-info-circle {
    padding-<?php echo $left; ?>: 3px;
}
.line-height-large {
    line-height: 1.8em;
}

/* th a, .thumbstat, a.tab { font-weight: bold !important; } */

th .button {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    -moz-border-radius:0px !important;
    -webkit-border-radius:0px !important;
    border-radius:0px !important;
}
.maxwidthsearch {		/* Max width of column with the search picto */
    width: 54px;
}

.valigntop {
    vertical-align: top;
}
.valignmiddle {
    vertical-align: middle;
}
.valignbottom {
    vertical-align: bottom;
}
.valigntextbottom {
    vertical-align: text-bottom;
}
.centpercent {
	width: 100%;
}
.centpercentimp {
    width: 100% !important;
}
.centpercentwithout1imp {
    width: calc(100% - 1px) !important;
}
.centpercentwithoutmenu {
    width: calc(100% - 200px);
}
.quatrevingtpercent, .inputsearch {
	width: 80%;
}
.soixantepercent {
	width: 60%;
}
.quatrevingtquinzepercent {
	width: 95%;
}
.quatrevingtpercentminusx {
	width: calc(80% - 52px);
}
textarea.centpercent {
	width: 96%;
}
.small, small {
    font-size: 85%;
}
.large {
    font-size: 125%;
}
.double {
    font-size: 2em;
}

.h1 .small, .h1 small, .h2 .small, .h2 small, .h3 .small, .h3 small, h1 .small, h1 small, h2 .small, h2 small, h3 .small, h3 small {
    font-size: 65%;
}
.h1 .small, .h1 small, .h2 .small, .h2 small, .h3 .small, .h3 small, .h4 .small, .h4 small, .h5 .small, .h5 small, .h6 .small, .h6 small, h1 .small, h1 small, h2 .small, h2 small, h3 .small, h3 small, h4 .small, h4 small, h5 .small, h5 small, h6 .small, h6 small {
    font-weight: 400;
    line-height: 1;
    color: #777;
}

.flip {
    transform: scaleX(-1) translate(<?php print ($left == 'left' ? '' : '-'); ?>2px, 0);
}
.rotate90 {
    transform: rotate(90deg) translate(0, <?php print ($left == 'left' ? '' : '-'); ?>2px);
}

.center {
    text-align: center;
    margin: 0px auto;
}
.centerimp {
    text-align: center !important;
}
.alignstart {
    text-align: start;
}
.start {
    text-align: start;
}
.end {
    text-align: end;
}
.left {
    text-align: <?php print $left; ?>;
}
.right {
    text-align: <?php print $right; ?>;
}
.justify {
    text-align: justify;
}
.pull-left {
    float: left!important;
}
.pull-right {
    float: right!important;
}
.nowrap {
    white-space: <?php print ($dol_optimize_smallscreen?'normal':'nowrap'); ?>;
}
.liste_titre .nowrap {
    white-space: nowrap;
}
.nowraponall {	/* no wrap on all devices */
    white-space: nowrap;
}
.wrapimp {
    white-space: normal !important;
}
.wordwrap {
    word-wrap: break-word;
}
.wordbreakimp {
    word-break: break-word;
}
.wordbreak {
    word-break: break-all;
}
.bold {
    font-weight: bold !important;
}
.nobold {
    font-weight: normal !important;
}
.uppercase {
    text-transform: uppercase;
}
.nounderline {
    text-decoration: none;
}
.nounderlineimp {
    text-decoration: none !important;
}
.nopadding {
    padding: 0;
}
.nopaddingleft {
    padding-left: 0;
}
.nopaddingright {
    padding-right: 0;
}
.nopaddingleftimp {
    padding-left: 0 !important;
}
.nopaddingrightimp {
    padding-right: 0 !important;
}
.paddingleft {
    padding-<?php print $left; ?>: 4px;
}
.paddingleftimp {
    padding-<?php print $left; ?>: 4px !important;
}
.paddingleft2 {
    padding-<?php print $left; ?>: 2px;
}
.paddingleft2imp {
    padding-<?php print $left; ?>: 2px !important;
}
.paddingright {
    padding-<?php print $right; ?>: 4px;
}
.paddingrightimp {
    padding-<?php print $right; ?>: 4px !important;
}
.paddingright2 {
    padding-<?php print $right; ?>: 2px;
}
.paddingright2imp {
    padding-<?php print $right; ?>: 2px !important;
}
.paddingtop {
    padding-top: 4px;
}
.paddingtop2 {
    padding-top: 2px;
}
.paddingbottom {
    padding-bottom: 4px;
}
.paddingbottom2 {
    padding-bottom: 2px;
}
.marginleft2 {
    margin-<?php print $left; ?>: 2px;
}
.marginright2 {
    margin-<?php print $right; ?>: 2px;
}
.nomarginleft {
    margin-<?php print $left; ?>: unset;
}
.nomarginright {
    margin-<?php print $right; ?>: unset;
}
.nowidthimp {
    width: unset !important;
}
.cursordefault {
    cursor: default;
}
.cursorpointer {
    cursor: pointer;
}
.classfortooltiponclick .fa-question-circle {
    cursor: pointer;
}
.cursormove {
    cursor: move;
}
.cursornotallowed {
    cursor: not-allowed;
}
.cursorwait {
    cursor: wait;
}
.backgroundblank {
    background-color: #fff;
}
.nobackground, .nobackground tr {
    background: unset !important;
}
.checkboxattachfilelabel {
    font-size: 0.85em;
    opacity: 0.7;
}
.borderimp {
    border: 1px solid #888 !important;
}
.text-warning {
    color : <?php print $textWarning ; ?>
}
.longmessagecut {
    max-height: 250px;
    max-width: 100%;
    overflow-y: auto;
}
div.urllink {
    padding: 5px;
    margin-top: 5px;
    margin-bottom: 5px;
    /* border: 1px solid #ccc; */
    border-radius: 5px;
    /* width: fit-content; */
    background-color: #f0f0f8;
    opacity: 0.8;
}
div.urllink, div.urllink a {
    color: #339 !important;
}

i.fa-mars::before, i.fa-venus::before, i.fa-genderless::before, i.fa-transgender::before  {
    color: #888 !important;
    opacity: 0.4;
    padding-<?php echo $left; ?>: 3px;
}
.stockmovemententry {
    color: #080;
    transform: rotate(0.25turn);
    font-size: 1.2em;
}
.stockmovementexit {
    color: #968822;
    transform: rotate(0.3turn);
    font-size: 1.2em;
}
.stockmovement {
    font-size: 1.4em;
}

body[class*="colorblind-"] .text-warning{
    color : <?php print $colorblind_deuteranopes_textWarning ; ?>
}
.text-success {
    color : <?php print $textSuccess ; ?>
}
body[class*="colorblind-"] .text-success{
    color : <?php print $colorblind_deuteranopes_textSuccess ; ?>
}
.text-danger {
    color : <?php print $textDanger ; ?>
}

.editfielda span.fa-pencil-alt, .editfielda span.fa-trash {
    color: var(--colortextlink) !important;
}
.editfielda span.fa-pencil-alt:hover, .editfielda span.fa-trash:hover {
    color: <?php print $colorftitle; ?> !important;
}
a.editfielda.nohover *:hover:before {
    color: #ccc !important;
}

.fawidth30 {
    width: 20px;
}
.floatnone {
    float: none !important;
}

span.fa.fa-plus-circle.paddingleft {
    padding-right: 4px;
    padding-top: 3px;
    padding-bottom: 2px;
}

.size15x { font-size: 1.5em !important; }
.fa-toggle-on, .fa-toggle-off, .size2x { font-size: 2em; }
.websiteselectionsection .fa-toggle-on, .websiteselectionsection .fa-toggle-off,
.asetresetmodule .fa-toggle-on, .asetresetmodule .fa-toggle-off,
.tdwebsitesearchresult .fa-toggle-on, .tdwebsitesearchresult .fa-toggle-off
{
    font-size: 1.5em; vertical-align: text-bottom;
}

.divoverflow {
    overflow: hidden;
    white-space: nowrap;
    vertical-align: middle;
    text-overflow: ellipsis;
}

/* Themes for badges */
<?php include dol_buildpath($path.'/theme/'.$theme.'/badges.inc.php', 0); ?>

/**
* Links
*/

a {
    color: var(--colorfline); /* @new */
    font-family: var(--fontfamilydol);
    font-weight: normal;
    text-decoration: none;
}

a:hover {
    cursor: pointer;
}

a:hover, a:focus {
	color: var(--colortextlink);
    text-decoration: underline;
}

a.commonlink
/* ,a.reposition */
{
    /* color: <?php print $colorfline; ?> !important; */
    color: #f4f4f4 !important;
    text-decoration: none;
}

hr {
    border: 1px dashed #777;
    height: 0;
    margin-top: 10px;
    margin-bottom: 10px;
}

/**
* Hide/display
*/

<?php if (! empty($dol_optimize_smallscreen)) { ?>
    .hideonsmartphone { display: none; }

    .noenlargeonsmartphone {
        width: 50px !important;
        display: inline !important;
    }
<?php } ?>

div.visible,
tr.visible {
    display: block;
}

div.hidden,
td.hidden {
    display: none;
}

.opacityhigh {
    opacity: 0.85;
}
.optiongrey, .opacitymedium {
    opacity: 0.7;
}
.opacitytransp {
    opacity: 0;
}

select:invalid {
    color: gray;
}
input:disabled, textarea:disabled, select[disabled='disabled']
{
	background: var(--inputbackgroundcolor);
}

input.liste_titre {
    box-shadow: none !important;
}
input.removedfile {
    padding: 0px !important;
    border: 0px !important;
    vertical-align: text-bottom;
}
input[type=file ]    { background-color: transparent; border-top: none; border-left: none; border-right: none; box-shadow: none; }
input[type=checkbox] { background-color: transparent; border: none; box-shadow: none; }
input[type=radio]    { background-color: transparent; border: none; box-shadow: none; }
input[type=image]    { background-color: transparent; border: none; box-shadow: none; }
input:-webkit-autofill {
    background-color: #FDFFF0 !important;
    background-image:none !important;
    -webkit-box-shadow: 0 0 0 50px #FDFFF0 inset;
}
::-webkit-input-placeholder { color:#ccc; }
input:-moz-placeholder { color:#ccc; }
input[name=price], input[name=weight], input[name=volume], input[name=surface], input[name=sizeheight], input[name=net_measure], select[name=incoterm_id] { margin-right: 6px; }
fieldset { border: 1px solid #AAAAAA !important; }
.legendforfieldsetstep { padding-bottom: 10px; }
input#onlinepaymenturl, input#directdownloadlink {
    opacity: 0.7;
}

div#moretabsList, div#moretabsListaction {
    z-index: 5;
}

hr {
	border: 0;
	border-top: 1px solid <?php print $colorbacklinebreak ?>;
}
.tabBar hr {
	margin-top: 20px;
	margin-bottom: 17px;
}

.button, .buttonDelete, input[name="sbmtConnexion"] {
    margin-bottom: 0;
    margin-top: 0;
    margin-left: 5px;
    margin-right: 5px;
    font-family: var(--fontfamilydol);
    display: inline-block;
    padding: 4px 14px;
    text-align: center;
    cursor: pointer;
    text-decoration: none !important;
    background-color: #f5f5f5;
    /*
    background-image: -moz-linear-gradient(to top,  #ffffff, #e6e6e6);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
    background-image: -webkit-linear-gradient(to top,  #ffffff, #e6e6e6);
    background-image: -o-linear-gradient(to top,  #ffffff, #e6e6e6);
    background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
    */

    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
    -webkit-border-radius: 2px;
    border-radius: 1px;

    font-weight: bold;
    /* text-transform: capitalize; */
    color: #444;
}
.button:focus, .buttonDelete:focus  {
    -webkit-box-shadow: 0px 0px 5px 1px rgba(0, 0, 60, 0.2), 0px 0px 0px rgba(60,60,60,0.1);
    box-shadow: 0px 0px 5px 1px rgba(0, 0, 60, 0.2), 0px 0px 0px rgba(60,60,60,0.1);
}
.button:hover, .buttonDelete:hover   {
    /* warning: having a larger shadow has side effect when button is completely on left of a table */
    -webkit-box-shadow: 0px 0px 1px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px rgba(60,60,60,0.1);
    box-shadow: 0px 0px 1px 1px rgba(0, 0, 0, 0.2), 0px 0px 0px rgba(60,60,60,0.1);
}
.button:disabled, .buttonDelete:disabled, .button.disabled {
    opacity: 0.4;
    box-shadow: none;
    -webkit-box-shadow: none;
    cursor: auto;
}
.buttonRefused {
    pointer-events: none;
    cursor: default;
    opacity: 0.4;
    box-shadow: none;
    -webkit-box-shadow: none;
}
.button_search, .button_removefilter {
    border: unset;
    background-color: unset;
    line-height: 2;
}
.button_search:hover, .button_removefilter:hover {
    cursor: pointer;
}
td button.liste_titre span {
    color: <?php print $colorftitle; ?>;
}

div.extra_inline_chkbxlst, div.extra_inline_checkbox {
	min-width:150px;
}

/* ============================================================================== */
/*	Module website 																  */
/* ============================================================================== */

.websitebar {
    border-bottom: 1px solid #888;
    background: #eee;
}
.websitebar .button, .websitebar .buttonDelete
{
    padding: 2px 5px 3px 5px !important;
    margin: 2px 4px 2px 4px	!important;
    line-height: normal;
}
.websiteselection {
    display: inline-block;
    padding-left: 10px;
    vertical-align: middle;
    line-height: 29px;
}
.websitetools {
    float: right;
    padding-top: 2px;
}
.websiteiframenoborder {
    border: 0px;
}

/**
* RTL direction
*/

td[align="left"] {
    text-align: <?php print $left; ?>;
}
td[align="right"] {
    text-align: <?php print $right; ?>;
}

/**
* Dragging lines
*/

.dragClass {
    color: #002255;
}
td.showDragHandle {
    cursor: move;
}
.tdlineupdown {
    white-space: nowrap;
}


/**
* Images Styles
*/

img {
    border: 0;
    vertical-align: middle;
}

img[src*=pdf]		{ vertical-align: sub !important; }
img[src*=globe]		{ vertical-align: sub !important; }
img[src*=star]		{ vertical-align: baseline; }
input[type=image]	{ vertical-align: middle; }
img[src*=stcomm]	{ vertical-align: text-top; }


/**
* Graphs Styles
*/

.dolgraphtitlecssboxes + div, #stats {
    margin: 0 auto;
}

.pieLabelBackground {
    background-color: #333 !important;
    color: #f7f7f7;
    opacity: 1;
}

.jPicker .Icon {
	vertical-align: middle;
	margin-<?php print $left; ?>: .5em;
}

/**
* Form Elements
*/

<?php if (empty($dol_use_jmobile)) { ?>

    input:focus, textarea:focus, button:focus, select:focus {
        box-shadow: 0 0 2px #8091bf;
    }

    textarea,
    input[type=text],
    input[type=password],
    input[type=email],
    input[type=number],
    input[type=search],
    input[type=tel],
    input[type=url],
    .titlewrap input,
    select {
        border-color: rgba(0,0,0, .24);
        box-shadow: inset 0 1px 2px rgba(0,0,0, .07);
    }

    textarea:focus, button:focus {
        /* v6 box-shadow: 0 0 4px #8091BF; */
        border: 1px solid #aaa !important;
    }
    input:focus, textarea:focus, button:focus, select:focus {
        border-bottom: 1px solid #666;
    }
    input.select2-input {
        border-bottom: none ! important;
    }
    .select2-choice {
        border: none;
        border-bottom:  solid 1px rgba(0,0,0,.2) !important;	/* required to avoid to lose bottom line when focus is lost on select2. */
    }

	.cke_top {
    	background: var(--colorbacktitle1) !important;
    }
	.cke_dialog_ui_vbox_child {
    	color: var(--tooltipfontcolor) !important;
    }
	.cke_reset_all textarea, .cke_reset_all input[type="text"], .cke_reset_all input[type="password"] {
    	color: var(--tooltipfontcolor) !important;
    }
	textarea.cke_source	{
		box-shadow: none;
		background-color:var(--inputbackgroundcolor) !important;
		color: var(--colortext) !important;
	}
    textarea.cke_source:focus
    {
        box-shadow: none;
    }

	select.cke_dialog_ui_input_select {
		color: var(--tooltipfontcolor) !important;
	}
    .liste_titre .flat, .liste_titre select.flat {
        margin: 2px;
        /* padding: 2px 4px; */
    }

    input, textarea, select {
        border-color: rgba(0,0,0, .24);
        box-shadow: inset 0 1px 2px rgba(0,0,0, .07);
        margin:3px 10px 3px 0;
    }
<?php } ?> /* end if (empty($dol_use_jmobile)) */

section.setupsection {
    padding: 20px;
    /* background-color: var(--colorbacktitle1); */
    background-color: #f0f0f0;
    border-radius: 5px;
}

.field-error-icon { color: #ea1212 !important; }

select.flat, form.flat select {
    font-weight: normal;
    font-size: unset;
    height: 2em;
}

input:disabled,
select:disabled {
	background-color: var(--inputbackgroundcolor);
    cursor: not-allowed;
}

input.liste_titre {
    box-shadow: none !important;
}

.listactionlargetitle .liste_titre {
    line-height: 24px;
}

input.removedfile {
    border: 0 !important;
    padding: 0 !important;
}
input.removedassigned  {
    padding: 2px !important;
    vertical-align: text-bottom;
    margin-bottom: -3px;
}
input.smallpadd {	/* Used for timesheet input */
    padding-left: 0px !important;
    padding-right: 0px !important;
}
input.buttongen {
    vertical-align: middle;
}
input.buttonpayment, button.buttonpayment, div.buttonpayment {
    min-width: 290px;
    margin-bottom: 15px;
    margin-top: 15px;
    height: 64px;
    background-image: none;
    line-height: 24px;
    padding: 8px;
    background: none;
    text-align: center;
    border: 0;
    background-color: #9999bb;
    white-space: normal;
    box-shadow: 1px 1px 4px #bbb;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
    max-width: 350px;
}
input.short {
    width: 40px;
}
.nofocusvisible:focus-visible {
    outline: none;
}

div.buttonpayment input:focus {
    color: #008;
}
.buttonpaymentsmall {
    font-size: 0.65em;
    padding-left: 5px;
    padding-right: 5px;
}
div.buttonpayment input {
    background-color: unset;
    color: #fff;
    border-bottom: unset;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
}
input.buttonpaymentcb {
    background-image: url(<?php echo dol_buildpath($path.'/theme/common/credit_card.png',1) ?>);
    background-size: 26px;
    background-repeat: no-repeat;
    background-position: 5px 5px;
}
input.buttonpaymentcheque {
    background-image: url(<?php echo dol_buildpath($path.'/theme/common/cheque.png',1) ?>);
    background-repeat: no-repeat;
    background-position: 8px 7px;
}
input.buttonpaymentcheque {
    background-image: url(<?php echo dol_buildpath($path.'/paypal/img/object_paypal.png',1) ?>);
    background-repeat: no-repeat;
    background-position: 5px 4px;
}
input.buttonpaymentpaypal {
    background-image: url(<?php echo dol_buildpath($path.'/paypal/img/object_paypal.png',1) ?>);
    background-repeat: no-repeat;
    background-position: 8px 7px;
}
input.buttonpaymentpaybox {
    background-image: url(<?php echo dol_buildpath($path.'/paybox/img/object_paybox.png',1) ?>);
    background-repeat: no-repeat;
    background-position: 8px 7px;
}
input.buttonpaymentstripe {
    background-image: url(<?php echo dol_buildpath($path.'/stripe/img/object_stripe.png',1) ?>);
    background-repeat: no-repeat;
    background-position: 8px 7px;
}
.logopublicpayment #dolpaymentlogo {
    max-height: 80px;
    max-width: 300px;
    image-rendering: -webkit-optimize-contrast;		/* better rendering on public page header */
}

a.butStatus {
    padding-left: 5px;
    padding-right: 5px;
    background-color: transparent;
    color: var(--colortext) !important;
    border: 2px solid var( --butactionbg) !important;
    margin: 0 0.45em !important;
}

span.userimg.notfirst, div.userimg.notfirst {
    margin-left: -5px;
}
div.userimg.notfirst {
    display: block-inline;
}
/* Used for timesheets */
span.timesheetalreadyrecorded input {
    border: none;
    border-bottom: solid 1px rgba(0,0,0,0.4);
    margin-right: 1px !important;
    min-width: 40px;
}
td.onholidaymorning, td.onholidayafternoon {
    background-color: #fdf6f2;
}
td.onholidayallday {
    background-color: #f4eede;
}
td.onholidayallday:not(.weekend) input {
    background-color: #f8f7f0;
}
td.weekend {	/* must be after td.onholidayallday */
    background-color: #eee;
}
td.actionbuttons a {
    padding-left: 6px;
}
/*
td.leftborder, td.hide0 {
    border-left: 1px solid #ccc;
}
td.leftborder, td.hide6 {
    border-right: 1px solid #ccc;
}
*/
td.rightborder {
    border-right: 1px solid #ccc;
}

td.amount, span.amount, div.amount, b.amount {
    color: #006666;
}
td.actionbuttons a {
    padding-left: 6px;
}
select.flat, form.flat select, .pageplusone {
    font-weight: normal;
    font-size: unset;
}
input.pageplusone {
    padding-bottom: 4px;
    padding-top: 4px;
    margin-right: 4px;
}
.paginationlastpage a {
    padding-left: 8px;
}

.saturatemedium {
    filter: saturate(0.8);
}
.optionblue {
    color: rgb(<?php echo $colortextlink; ?>);
}
.optiongrey, .opacitymedium {
    opacity: 0.4;
}
.opacitymediumbycolor {
    color: rgba(0, 0, 0, 0.4);
}
.opacitylow {
    opacity: 0.6;
}
.opacityhigh {
    opacity: 0.24;
}
.opacitytransp {
    opacity: 0;
}
.colorwhite {
    color: #fff;
}
.colorgrey {
    color: #888 !important;
}
.colorblack {
}
.fontsizeunset {
    font-size: unset !important;
}
.vmirror {
    transform: scale(1, -1);
}
.hmirror {
    transform: scale(-1, 1);
}

select:invalid, select.--error {
    color: gray;
}
.select2-results .select2-highlighted.optionblue {
    color: #FFF !important;
}
select:invalid {
    color: gray;
}
input:disabled, textarea:disabled, select[disabled='disabled']
{
	background: var(--inputbackgroundcolor);
}

input.liste_titre {
    box-shadow: none !important;
}
input.removedfile {
    padding: 0px !important;
    border: 0px !important;
    vertical-align: text-bottom;
}
textarea:disabled {
    background:#ddd;
}
input[type=file ]    { background-color: transparent; border-top: none; border-left: none; border-right: none; box-shadow: none; }
input[type=checkbox] { background-color: transparent; border: none; box-shadow: none; }
input[type=radio]    { background-color: transparent; border: none; box-shadow: none; }
input[type=image]    { background-color: transparent; border: none; box-shadow: none; }
input:-webkit-autofill {
	background-color: var(--inputbackgroundcolor) !important;
    background-image:none !important;
    -webkit-box-shadow: 0 0 0 50px #FDFFF0 inset;
	color: var(--colortext) !important;
}
::-webkit-input-placeholder { color:#ccc; }
input:-moz-placeholder { color:#ccc; }
input[name=price], input[name=weight], input[name=volume], input[name=surface], input[name=sizeheight], select[name=incoterm_id] { margin-right: 6px; }
input[name=surface] { margin-right: 4px; }
fieldset { border: 1px solid #AAAAAA !important; }
.legendforfieldsetstep { padding-bottom: 10px; }
input#onlinepaymenturl, input#directdownloadlink {
    opacity: 0.7;
}

<?php if (! empty($dol_use_jmobile)) { ?>
    legend { margin-bottom: 8px; }
<?php } ?>


/**
* Buttons
*/

.button,
.button:link,
.button:active,
.button:visited {
    background-color: <?php print $colorButtonAction1; ?>;
    /* border: 1px solid #c0c0c0; */
    /* border-color: <?php print $colorButtonAction1; ?>; */
    /* box-shadow: inset 0 1px 0 rgba(235,235,235, .6); */
    /* -webkit-box-shadow: inset 0 1px 0 rgba(235,235,235, .6); */
    /* -webkit-border-radius: 0.30em; */
    /* -moz-border-radius: 0.30em; */
    border: none;
    border-radius: 0.30em;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    margin: .2em .5em;
    /* margin: 2px 1px; */
    padding: .5em 1em;
    transition: all .3s ease-in-out;
    -moz-transition: all .3s ease-in-out;
    -webkit-transition: all .3s ease-in-out;
}

.button:hover, .button:focus {
    background-color: <?php print $colorButtonAction2; ?>;
    border-color: <?php print $colorButtonAction2; ?>;
    box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    -webkit-box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    color: #fff;
}

.button:disabled {
    background-color: #ddd;
    cursor: not-allowed;
}

table[summary] .button[name=viewcal] {
    width: inherit!important;
    min-width: 120px;
}

.liste_titre input[type=submit] {
    background-color: #444;
    border-color: #555;
    box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    -webkit-box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    color: #fff;
    padding: .4em .8em;
}

.liste_titre input[type=submit]:hover {
    background-color: #333;
    border-color: #444;
}

div.noborder .button { padding: .4em .8em; }

#blockvmenusearch .button {
    background-color: #444;
    border: 1px solid #c0c0c0;
    border-color: #555;
    box-shadow: inset 0 1px 0 rgba(150, 172, 180, .6);
    -webkit-box-shadow: inset 0 1px 0 rgba(150, 172, 180, .6);
    color: #fff;
    font-size: inherit;
    margin: 0em .5em;
    padding: 7px 8px;
}

#blockvmenusearch .button:hover {
    background-color: #333;
    border-color: #444;
    box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    -webkit-box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    color: #fff;
}

.buttonajax {
    background-image: url(<?php echo $img_button; ?>);
    background-position: bottom;
    border: 0;
    border-radius: 0 5px 0 5px;
    -moz-border-radius: 0 5px 0 5px;
    -webkit-border-radius: 0 5px 0 5px;
    box-shadow: 4px 4px 4px rgba(0,0,0, .24);
    -moz-box-shadow: 4px 4px 4px rgba(0,0,0, .24);
    -webkit-box-shadow: 4px 4px 4px rgba(0,0,0, .24);
    margin: 0em .5em;
    padding: .1em .7em;
}

form {
    padding: 0;
    margin: 0;
}

th .button {
    border-radius: 0 !important;
    -moz-border-radius: 0 !important;
    -webkit-border-radius: 0 !important;
    box-shadow: none !important;
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
}

/**
* Action Buttons
*/

div.divButAction { margin-bottom: 1.5em; }

a.butActionNew>span.fa-plus-circle { padding-left: 6px; font-size: 1.5em; }
a.butActionNewRefused>span.fa-plus-circle { padding-left: 6px; font-size: 1.5em; }

.butAction,
.butActionDelete,
.butActionRefused,
.butActionNewRefused {
    background-color: <?php echo $colorbline; ?>;
    color: var(--colorfline);
    font-weight: 500;
    margin: 0 <?php echo ($dol_optimize_smallscreen?'.3':'.5'); ?>em;
    padding: .3em <?php echo ($dol_optimize_smallscreen?'.4':'.7'); ?>em;
    white-space: nowrap;
    transition: all .3s ease-in-out;
    -moz-transition: all .3s ease-in-out;
    -webkit-transition: all .3s ease-in-out;
}

.butAction:hover,
.butActionNew:hover,
.butActionDelete:hover,
.butActionRefused:hover {
    color: #f7f7f7;
    text-decoration: none;
}

.butAction {
    -webkit-box-shadow: inset 0 1px 0 rgba(170, 200, 210, .6);
    box-shadow: inset 0 1px 0 rgba(170, 200, 210, .6);
}

.butAction:hover, .butAction:active {
    background-color: <?php print $maincolor; ?>;
    -webkit-box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
    box-shadow: inset 0 1px 0 rgba(235,235,235, .6);
}

.butActionDelete {
    border: 1px solid #f07b6e;
    -webkit-box-shadow: inset 0 1px 0 rgba(210, 170, 170, .6);
    box-shadow: inset 0 1px 0 rgba(210, 170, 170, .6);
}

.butActionDelete:hover, .butActionDelete:active {
    background-color: #f07b6e;
    -webkit-box-shadow: inset 0 1px 0 rgba(210, 170, 170, .6);
    box-shadow: inset 0 1px 0 rgba(210, 170, 170, .6);
}

.butActionRefused {
    font-weight: normal !important;
    background-color: #ddd;
    border: 1px solid rgba(0,0,0, .12);
    -webkit-box-shadow: inset 0 1px 0 rgba(170, 170, 170, .6);
    box-shadow: inset 0 1px 0 rgba(170, 170, 170, .6);
    color: var(--colorfline);
    opacity: .6;
}

.butActionRefused:hover, .butActionRefused:active {
    background-color: #666;
    cursor: not-allowed;
}

<?php if (getDolGlobalString('MAIN_BUTTON_HIDE_UNAUTHORIZED')) { ?>
    .butActionRefused { display: none; }
<?php } ?>

span.butAction,
span.butActionDelete {
    cursor: pointer;
}


/**
* State Ok, Warning, Error
*/
.ok      { color: #114466; }
.warning { color: #887711 !important; }
.error   { color: #b62512 !important; font-weight: bold; }
.green   { color: #118822 !important; }

.bloc_success {
    background-color: #33cc66;
    color: #fff;
    display: inline-block;
    margin-bottom: .5em;
    padding: 1em;
}

.bloc_warning {
    background-color: #f07b6e;
    color: #fff;
    display: inline-block;
    margin-bottom: .5em;
    padding: 1em;
}

div.ok {
    color: #114466;
}

/* Warning message */
div.warning {
	border-<?php print $left; ?>: solid 5px <?php print $colorWarningBorder; ?>;
    padding-top: 8px;
    padding-left: 10px;
    padding-right: 4px;
    padding-bottom: 8px;
    margin: 0.5em 0em 0.5em 0em;
	background: <?php print $colorWarningBg; ?>;
	color: <?php echo $colorWarningTxt; ?>;
}

/* Error message */
div.error {
	border-<?php print $left; ?>: solid 5px <?php print $colorErrorBorder; ?> !important;
	text-align: <?php print $left; ?> !important;
    padding-top: 8px;
    padding-left: 10px;
    padding-right: 4px;
    padding-bottom: 8px;
    margin: 0.5em 0em 0.5em 0em;
	background: <?php print $colorErrorBg; ?>;
	color: <?php echo $colorErrorTxt; ?>;
	font-size: unset !important;
}

/* Info admin */
div.info {
	border-<?php print $left; ?>: solid 5px <?php print $colorInfoBorder; ?>;
    padding-top: 8px;
    padding-left: 10px;
    padding-right: 4px;
    padding-bottom: 8px;
    margin: 0.5em 0em 0.5em 0em;

/* Warning message */
	background: <?php print $colorInfoBg; ?>;
	color: <?php echo $colorInfoTxt; ?>;

/* Error message */
}


/*
 *   Liens Payes/Non payes
 */

a.normal:link { font-weight: normal }
a.normal:visited { font-weight: normal }
a.normal:active { font-weight: normal }
a.normal:hover { font-weight: normal }

a.impayee:link { font-weight: bold; color: #550000; }
a.impayee:visited { font-weight: bold; color: #550000; }
a.impayee:active { font-weight: bold; color: #550000; }
a.impayee:hover { font-weight: bold; color: #550000; }


/*
*  External web site
*/

.framecontent {
    width: 100%;
    height: 100%;
}

.framecontent iframe {
    width: 100%;
    height: 100%;
}

/*
*  Other
*/
.movable {
    cursor: move;
}
.borderrightlight
{
    border-right: 1px solid #DDD;
}
#formuserfile {
    margin-top: 4px;
}
#formuserfile_link {
    margin-left: 1px;
}
.listofinvoicetype {
    height: 28px;
    vertical-align: middle;
}
.divsocialnetwork:not(:first-child) {
    padding-left: 20px;
}
div.divsearchfield {
    /*float: <?php print $left; ?>;*/
    display: inline-block;
    margin-<?php print $right; ?>: 12px;
    margin-<?php print $left; ?>: 2px;
    margin-top: 4px;
    margin-bottom: 4px;
    padding-left: 2px;
}
.divfilteralone {
    background-color: rgba(0, 0, 0, 0.08);
    border-radius: 5px;
    padding-left: 5px;
}
.divsearchfieldfilter {
    text-overflow: clip;
    overflow: auto;
    padding-bottom: 5px;
    opacity: 0.6;
    font-size: small;
}
.divadvancedsearchfield:first-child {
    margin-top: 3px;
}
.divadvancedsearchfield {
    float: left;
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 2px;
    padding-top: 2px;
}
.divadvancedsearchfield span.select2.select2-container.select2-container--default {
    /* padding-bottom: 4px; */
}
.search_component_params {
    /*display: flex; */
    -webkit-flex-flow: row wrap;
    flex-flow: row wrap;
    background: #fff;
    padding-top: 3px;
    padding-bottom: 3px;
    padding-<?php echo $left; ?>: 0;
    padding-<?php echo $right; ?>: 0;
    border-bottom: solid 1px var(--inputbordercolor);
    height: 24px;
    border-radius: 3px;
}
.search_component_searchtext {
    padding-top: 2px;
}
.search_component_params_text, .search_component_params_text:focus {
    border-bottom: none;
    width: auto;
    margin: 0 !important;
    padding: 3px;
}
.tagsearch {
    padding: 2px;
    padding-right: 4px;
    padding-bottom: 3px;
    background: #ddd;
    border-radius: 4px;
}
.tagsearchdelete {
    color: #999;
    cursor: pointer;
    display: inline-block;
    font-weight: bold;
    margin-right: 2px;
    padding-left: 4px;
}

.caretleftaxis {
    margin-left: -13px;
    margin-top: -1px;
    position: absolute;
}
.caretdownaxis {
    margin-left: -12px;
    margin-top: 0;
    position: absolute;
}

.a-filter, .a-mesure {
    border-radius: 50px;
	background: var(--colorbacktabactive);
	color: var(--colortexttitlenotab);
    padding: 8px 10px 8px 6px;
}
.a-filter:before {
    content: "\f0b0";
}
.a-mesure:before {
    content: "\f080";
}
.a-filter:before, .a-mesure:before {
    font-family: "Font Awesome 5 Free";
    font-weight: 600;
    padding-right: 5px;
    padding-left: 5px;
}
.a-filter-disabled, .a-mesure-disabled {
    border-radius: 50px;
    background: var(--colorbacktitle1);
    padding: 8px;
    opacity: 0.6;
}


/* ============================================================================== */
/* Styles for scan tool                                                           */
/* ============================================================================== */

div.div-for-modal {
    /* display: none; */
    position:absolute;
    top:calc(50% - 200px);
    left:calc(50% - 250px);
    width:500px;  /* adjust as per your needs */
    height:400px;   /* adjust as per your needs */
    background: #fff;
    border: 1px solid #bbb;
    box-shadow: 2px 2px 20px #ddd;
    z-index: 100;
}

#scantoolmessage {
    height: 3em;
    border: none;
    overflow-y: auto;
}

div.div-for-modal-topright {
    /* display: none; */
    position: fixed;
    top: 0;
    right: 0;
    width:50%;  /* adjust as per your needs */
    height:320px;   /* adjust as per your needs */
    background: #fff;
    border: 1px solid #bbb;
    box-shadow: 2px 2px 20px #ddd;
    z-index: 1100;
}


<?php
// Add a nowrap on smartphone, so long list of field used for filter are overflowed with clip
if ($conf->browser->layout == 'phone') {
	?>
.divsearchfieldfilter {
    white-space: nowrap;
}
<?php } ?>
div.confirmmessage {
    padding-top: 6px;
}
ul.attendees {
	padding-top: 0;
	padding-bottom: 0;
	padding-left: 0;
	margin-top: 0;
	margin-bottom: 0;
}
ul.attendees li {
	list-style-type: none;
	padding-top:1px;
	padding-bottom:1px;
}
.googlerefreshcal {
    padding-top: 4px;
    padding-bottom: 4px;
}
.paddingtopbottom {
	padding-top: 10px;
	padding-bottom: 10px;
}
.checkallactions {
    margin-top: 2px;		/* left must be same than right to keep checkbox centered */
    margin-left: 2px;		/* left must be same than right to keep checkbox centered */
    vertical-align: middle;
}
select.flat.selectlimit {
    max-width: 62px;
}
.selectlimit, .marginrightonly {
    margin-right: 10px !important;
}
.marginleftonly {
	margin-<?php echo $left; ?>: 10px !important;
}
.marginleftonlyshort {
	margin-<?php echo $left; ?>: 4px !important;
}
.nomarginleft {
	margin-<?php echo $left; ?>: 0px !important;
}
.margintoponly {
	margin-top: 10px !important;
}
.marginbottomonly {
	margin-bottom: 10px !important;
}
.marginbottomonlyshort {
    margin-bottom: 3px !important;
}
.nomargintop {
    margin-top: 0 !important;
}
.nomarginbottom {
    margin-bottom: 0 !important;
}
.selectlimit, .selectlimit:focus {
    border-left: none !important;
    border-top: none !important;
    border-right: none !important;
    outline: none;
}
.strikefordisabled {
    text-decoration: line-through;
}
.widthdate {
    width: 130px;
}
/* using a tdoverflowxxx make the min-width not working */
.tdnooverflowimp {
    text-overflow: unset;
}
.tdoverflow {
    max-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.spanoverflow {
    overflow-x: clip;
    text-overflow: ellipsis;
}
.tdoverflowmax50 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 50px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax60 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax80 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax80imp {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 80px !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax100 {
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax100imp {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 100px !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax125 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 125px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax150 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax200 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax250 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax300 {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax400 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 400px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowmax500 {			/* For tdoverflow, the max-midth become a minimum ! */
    max-width: 500px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tdoverflowauto {
    max-width: 0;
    overflow: auto;
}
.divintdwithtwolinesmax {
    width: 75px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}
.twolinesmax {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}
.tablelistofcalendars {
    margin-top: 25px !important;
}
.navselectiondate {
    width: 250px;
}

/* Styles for amount on card */
table.paymenttable td.amountpaymentcomplete, table.paymenttable td.amountremaintopay, table.paymenttable td.amountremaintopayback {
    padding-top: 0px;
    padding-bottom: 0px;
}
.amountalreadypaid {
}
.amountpaymentcomplete {
	color: #008800;
	font-weight: bold;
	font-size: 1.2em;
}
.amountremaintopay {
	color: #880000;
	font-weight: bold;
	font-size: 1.2em;
}
.amountremaintopayback {
	font-weight: bold;
	font-size: 1.2em;
}
.amountpaymentneutral {
    font-weight: bold;
    font-size: 1.2em;
}
.onlinepaymentbody .amountpaymentcomplete {
    background-color: #008800;
    color: #fff;
    padding: 5px;
    border-radius: 5px;
}
.savingdocmask {
    margin-top: 6px;
    margin-bottom: 12px;
}
#builddoc_form ~ .showlinkedobjectblock {
    margin-top: 20px;
}

/* For the long description of module */
.moduledesclong p img,.moduledesclong p a img {
    max-width: 90% !important;
    height: auto !important;
}
.imgdoc {
    margin: 18px;
    border: 1px solid #ccc;
    box-shadow: 1px 1px 25px #aaa;
    max-width: calc(100% - 56px);
}
.fa-file-text-o, .fa-file-code-o, .fa-file-powerpoint-o, .fa-file-excel-o, .fa-file-word-o, .fa-file-o, .fa-file-image-o, .fa-file-video-o, .fa-file-audio-o, .fa-file-archive-o, .fa-file-pdf-o {
    color: <?php print $maincolor; ?>;
}
.fa-15 {
    font-size: 1.5em;
}
.fa-trash, .fa-crop, .fa-pencil {
    font-size: 1.4em;
}

/* DOL_XXX for future usage (when left menu has been removed). If we do not use datatable */
/*.table-responsive {
width: calc(100% - 330px);
margin-bottom: 15px;
overflow-y: hidden;
-ms-overflow-style: -ms-autohiding-scrollbar;
}*/

/* Style used for most tables */
.div-table-responsive, .div-table-responsive-no-min {
    overflow-x: auto;
    min-height: 0.01%;
}
.div-table-responsive {
    line-height: 155%;
}
/* Style used for full page tables with field selector and no content after table (priority before previous for such tables) */
div.fiche>form>div.div-table-responsive, div.fiche>form>div.div-table-responsive-no-min {
    overflow-x: auto;
}

div.fiche>form>div.div-table-responsive {
    min-height: 392px;
}
div.fiche>div.tabBar>form>div.div-table-responsive {
    min-height: 392px;
}
div.fiche {
	/* text-align: justify; */
}

.flexcontainer {
<?php
	if (!empty($conf->browser->browsername) && in_array($conf->browser->browsername, array('chrome', 'firefox', 'safari'))) {
		echo 'display: inline-flex;'."\n";
	}
?>
    flex-flow: row wrap;
    justify-content: flex-start;
}


.thumbstat {
    flex: 1 1 116px;
}
.thumbstat150 {
    flex: 1 1 150px;
}
.thumbstat, .thumbstat150 {
    flex-grow: 1;
    flex-shrink: 1;
    /* flex-basis: 140px; */
    min-width: 150px;
    justify-content: flex-start;
    align-self: flex-start;
}

select.selectarrowonleft {
    direction: rtl;
}
select.selectarrowonleft option {
    direction: ltr;
}

.img-skinthumb {
    width: 160px;
    height: 100px;
}

/* To avoid message boxes being too large because code not wrapping */
.longmessagecut pre {
    white-space: break-spaces;
}

/* ============================================================================== */
/* Styles to hide objects														  */
/* ============================================================================== */

.clearboth  { clear:both; }

.hideobject { display: none; }
.minwidth25  { min-width: 25px; }
.minwidth50  { min-width: 50px; }
.minwidth75  { min-width: 75px; }
/* rule for not too small screen only */
@media only screen and (min-width: <?php echo (!empty($nbtopmenuentries) && !empty($fontsize) ? round($nbtopmenuentries * $fontsize * 3.4, 0) + 7 : 7); ?>px)
{
    .width20  { width: 20px; }
    .width25  { width: 25px; }
    .width50  { width: 50px; }
    .width75  { width: 75px; }
    .width100 { width: 100px; }
    .width200 { width: 200px; }
    .minwidth100 { min-width: 100px; }
    .minwidth150 { min-width: 150px; }
    .minwidth200 { min-width: 200px; }
    .minwidth250 { min-width: 250px; }
    .minwidth300 { min-width: 300px; }
    .minwidth400 { min-width: 400px; }
    .minwidth500 { min-width: 500px; }
    .minwidth50imp  { min-width: 50px !important; }
    .minwidth75imp  { min-width: 75px !important; }
    .minwidth100imp { min-width: 100px !important; }
    .minwidth200imp { min-width: 200px !important; }
    .minwidth250imp { min-width: 250px !important; }
    .minwidth300imp { min-width: 300px !important; }
    .minwidth400imp { min-width: 400px !important; }
    .minwidth500imp { min-width: 500px !important; }
}
.widthauto { width: auto; }
.width20  { width: 20px; }
.width25  { width: 25px; }
.width40  { width: 40px; }
.width50  { width: 50px; }
.width75  { width: 75px; }
.width100 { width: 100px; }
.width125 { width: 125px; }
.width150 { width: 150px; }
.width200 { width: 200px; }
.width250 { width: 250px; }
.width300 { width: 300px; }
.width400 { width: 400px; }
.width500 { width: 500px; }
.maxwidth25  { max-width: 25px; }
.maxwidth40  { max-width: 40px; }
.maxwidth50  { max-width: 50px; }
.maxwidth75  { max-width: 75px; }
.maxwidthdate  { max-width: 85px; }
.maxwidth100 { max-width: 100px; }
.maxwidth125 { max-width: 125px; }
.maxwidth150 { max-width: 150px; }
.maxwidth200 { max-width: 200px; }
.maxwidth250 { max-width: 250px; }
.maxwidth300 { max-width: 300px; }
.maxwidth400 { max-width: 400px; }
.maxwidth500 { max-width: 500px; }
.maxwidth750 { max-width: 750px; }
.maxwidth1000 { max-width: 1000px; }
.maxwidth50imp  { max-width: 50px !important; }
.maxwidth75imp  { max-width: 75px !important; }

.minwidth100onall { min-width: 100px !important; }
.minwidth200onall { min-width: 200px !important; }
.minwidth250onall { min-width: 250px !important; }

.minheight20 { min-height: 20px; }
.minheight30 { min-height: 30px; }
.minheight40 { min-height: 40px; }
.titlefieldcreate { width: 20%; }
.titlefield       { /* width: 25%; */ min-width: 250px; width: 25%; }
.titlefieldmiddle { width: 45%; }
.titlefieldmax45 { max-width: 45%; }
.imgmaxwidth180 { max-width: 180px; }
.imgmaxheight50 { max-height: 50px; }

.width20p { width:20%; }
.width25p { width:25%; }
.width40p { width:40%; }
.width50p { width:50%; }
.width60p { width:60%; }
.width75p { width:75%; }
.width80p { width:80%; }
.width100p { width:100%; }

/* Force values for small screen 1400 */
@media only screen and (max-width: 1400px)
{
    .titlefield { /* width: 30% !important; */ }
    .titlefieldcreate { width: 30% !important; }
    .minwidth50imp  { min-width: 50px !important; }
    .minwidth75imp  { min-width: 75px !important; }
    .minwidth100imp { min-width: 100px !important; }
    .minwidth125imp { min-width: 125px !important; }
    .minwidth150imp { min-width: 150px !important; }
    .minwidth200imp { min-width: 200px !important; }
    .minwidth250imp { min-width: 250px !important; }
    .minwidth300imp { min-width: 300px !important; }
    .minwidth400imp { min-width: 300px !important; }
    .minwidth500imp { min-width: 300px !important; }

    .linkedcol-element {
        min-width: unset;
    }
}

/* Force values for small screen 1000 */
@media only screen and (max-width: 1000px)
{
    .maxwidthonsmartphone { max-width: 100px; }
    .minwidth50imp  { min-width: 50px !important; }
    .minwidth75imp  { min-width: 75px !important; }
    .minwidth100imp { min-width: 100px !important; }
    .minwidth125imp { min-width: 125px !important; }
    .minwidth150imp { min-width: 110px !important; }
    .minwidth200imp { min-width: 110px !important; }
    .minwidth250imp { min-width: 115px !important; }
    .minwidth300imp { min-width: 120px !important; }
    .minwidth400imp { min-width: 150px !important; }
    .minwidth500imp { min-width: 250px !important; }
}

select.widthcentpercentminusx, span.widthcentpercentminusx:not(.select2-selection):not(.select2-dropdown), input.widthcentpercentminusx {
    width: calc(100% - 52px) !important;
    display: inline-block;
}
select.widthcentpercentminusxx, span.widthcentpercentminusxx:not(.select2-selection), input.widthcentpercentminusxx {
    width: calc(100% - 70px) !important;
    display: inline-block;
}

/* Force values for small screen 767 */
@media only screen and (max-width: 767px)
{
	div.refidno {
		font-size: <?php print is_numeric($fontsize) ? ($fontsize+3).'px' : $fontsize; ?> !important;
	}
    .divadvancedsearchfield {
        padding-left: 5px;
        padding-right: 5px;
    }

    div.divphotoref {
        padding-right: 10px !important;
    }

    .hideonsmartphone { display: none; }
    .hideonsmartphoneimp { display: none !important; }

    .margintoponsmartphone { margin-top: 6px; }

    span.pictotitle {
        margin-<?php echo $left; ?>: 0 !important;
    }
    div.fiche>table.table-fiche-title {
        margin-top: 7px !important;
        margin-bottom: 15px !important;
    }

    select.minwidth100imp, select.minwidth100, select.minwidth200, select.minwidth200imp, select.minwidth300 {
        width: calc(100% - 40px) !important;
        min-width: 100px;
        display: inline-block;
    }
    select.widthcentpercentminusxx, span.widthcentpercentminusxx:not(.select2-selection), input.widthcentpercentminusxx {
        width: calc(100% - 70px) !important;
        display: inline-block;
    }

    input.maxwidthinputfileonsmartphone {
        width: 175px;
    }

    input.buttonpayment, button.buttonpayment, div.buttonpayment {
        min-width: 270px;
    }

    .smallonsmartphone {
        font-size: 0.8em;
    }

    .nopaddingtoponsmartphone {
        padding-top: 0 !important;
    }
    .nopaddingbottomonsmartphone {
        padding-bottom: 0 !important;
    }
}

/* Force values for small screen 570 */
@media only screen and (max-width: 570px)
{
    body {
        font-size: <?php print is_numeric($fontsize) ? ($fontsize+3).'px' : $fontsize; ?>;
    }
    div.refidno {
        font-size: <?php print is_numeric($fontsize) ? ($fontsize+3).'px' : $fontsize; ?> !important;
    }

    .divmainbodylarge { margin-left: 20px !important; margin-right: 20px !important; }

    .tdoverflowonsmartphone {
        max-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tdoverflowmax100onsmartphone {			/* For tdoverflow, the max-midth become a minimum ! */
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tdoverflowmax150onsmartphone {			/* For tdoverflow, the max-midth become a minimum ! */
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .border tbody tr, .border tbody tr td, div.tabBar table.border tr, div.tabBar table.border tr td, div.tabBar div.border .table-border-row, div.tabBar div.border .table-key-border-col, div.tabBar div.border .table-val-border-col {
        height: 40px !important;
    }

    div.tabs div.tab a.tab  {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .quatrevingtpercent, .inputsearch {
        width: 95%;
    }

    select {
        padding-top: 4px;
        padding-bottom: 5px;
    }

    .login_table .tdinputlogin {
        min-width: unset !important;
    }

    input, input[type=text], input[type=password], select, textarea     {
        min-width: 20px;
    }
    .trinputlogin input[type=text] {
        max-width: 140px;
    }
    .vmenu .searchform input {
        max-width: 138px;	/* length of input text in the quick search box when using a smartphone and without dolidroid */
    }

    .noenlargeonsmartphone { width : 50px !important; display: inline !important; }
    .maxwidthonsmartphone, #search_newcompany.ui-autocomplete-input { max-width: 100px; }
    .maxwidth50onsmartphone { max-width: 40px; }
    .maxwidth75onsmartphone { max-width: 50px; }
    .maxwidth100onsmartphone { max-width: 70px; }
    .maxwidth125onsmartphone { max-width: 100px; }
    .maxwidth150onsmartphone { max-width: 120px; }
    .maxwidth150onsmartphoneimp { max-width: 120px !important; }
    .maxwidth200onsmartphone { max-width: 200px; }
    .maxwidth250onsmartphone { max-width: 250px; }
    .maxwidth300onsmartphone { max-width: 300px; }
    .maxwidth400onsmartphone { max-width: 400px; }
    .minwidth50imp  { min-width: 50px !important; }
    .minwidth75imp  { min-width: 75px !important; }
    .minwidth100imp { min-width: 100px !important; }
    .minwidth125imp { min-width: 125px !important; }
    .minwidth150imp { min-width: 110px !important; }
    .minwidth200imp { min-width: 110px !important; }
    .minwidth250imp { min-width: 115px !important; }
    .minwidth300imp { min-width: 120px !important; }
    .minwidth400imp { min-width: 150px !important; }
    .minwidth500imp { min-width: 250px !important; }
    .titlefield { width: auto; min-width: unset; }
    .titlefieldcreate { width: auto; }

    #tooltip {
        position: absolute;
        width: <?php print dol_size(300, 'width'); ?>px;
    }

    /* intput, input[type=text], */
    select {
        width: 98%;
        min-width: 40px;
    }

    div.divphotoref {
        padding-<?php echo $right; ?>: 5px;
        padding-bottom: 5px;
    }
    img.photoref, div.photoref {
        border: none;
        -webkit-box-shadow: none;
        box-shadow: none;
        padding: 4px;
        height: 20px;
        width: 20px;
        object-fit: contain;
    }

    div.statusref {
        padding-right: 10px;
        max-width: 55%;
    }
    div.statusref img {
        padding-right: 3px !important;
    }
    div.statusrefbis {
        padding-right: 3px !important;
    }
    /* TODO
    div.statusref {
        padding-top: 0px !important;
        padding-left: 0px !important;
        border: none !important;
       }
    */

    input.buttonpayment {
        min-width: 300px;
    }
}
.linkobject { cursor: pointer; }
/*
table.tableforfield tr>td:first-of-type, div.tableforfield div.tagtr>div.tagtd:first-of-type {
    color: var(--colorfline);
}
*/
<?php if (GETPOST('optioncss', 'aZ09') == 'print') { ?>
.hideonprint { display: none !important; }
<?php } ?>


/* ============================================================================== */
/* Styles for dragging lines													  */
/* ============================================================================== */

.dragClass {
    color: #002255;
}
td.showDragHandle {
    cursor: move;
}
.tdlineupdown {
    white-space: nowrap;
    min-width: 10px;
}

/*------------------------------------*\
#Positioning Areas
\*------------------------------------*/

#id-container:before,
#id-container:after {
    content: ' ';
    display: table;
}

#id-container:after {
    clear: both;
}

#id-container {
    table-layout: fixed;
}
div.login_block_other {
	display: inline-block;
	vertical-align: middle;
	clear: <?php echo $disableimages ? 'none' : 'both'; ?>;
	padding-top: 0;
	text-align: right;
	margin-right: 8px;
	max-width: 200px;
}

.login_block_getinfo {
    text-align: center;
}
.login_block_getinfo div.login_block_user {
    display: block;
}
.login_block_getinfo .atoplogin, .login_block_getinfo .atoplogin:hover {
    color: var(--colorfline) !important;
    font-weight: normal !important;
}

#id-right,
#id-left {
    display: table-cell;
    <?php if (getDolGlobalString('OBLYON_HIDE_LEFTMENU') || !empty($conf->dol_optimize_smallscreen)) { ?>
        float: left;
    <?php } else { ?>
        float: none;
    <?php } ?>
    vertical-align: top;
}

.side-nav {
	vertical-align: top;
<?php if (getDolGlobalString('OBLYON_STICKY_LEFTBAR')) { ?>
	position: fixed;
    z-index: 90;
	overflow-y: auto !important;
	overflow-x: hidden;
<?php } else { ?>
    display: table-cell;
    <?php if (getDolGlobalString('OBLYON_HIDE_LEFTMENU') || !empty($conf->dol_optimize_smallscreen)) { ?>
        float: left;
    <?php } else { ?>
        float: none;
    <?php } ?>
<?php } ?>
<?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
    background-color: <?php print $bgnavtop; ?>;
<?php } else { ?>
    background-color: <?php print $bgnavleft; ?>;
<?php } ?>
}

#id-right {
<?php if (GETPOST("optioncss") == 'print') { ?>
    padding-top: 10px;
    <?php } elseif(getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
        <?php if (getDolGlobalString('OBLYON_PADDING_RIGHT_BOTTOM')) { ?>
            padding-bottom: 40px;
        <?php } ?>
        <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
            padding-top: 52px;
        <?php } else { ?>
            padding-top: 64px;
        <?php } ?>
    <?php } else { ?>
        padding-top: 10px;
    <?php } ?>
    width: 100%;
    <?php if (getDolGlobalString('OBLYON_STICKY_LEFTBAR')) { ?>
        <?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU')) { ?>
            padding-left: 40px;
        <?php } else { ?>
            padding-left: 230px;
        <?php } ?>
        width: 100vw;
    <?php } ?>
}

#id-left {
    <?php if (!getDolGlobalString('OBLYON_HIDE_LEFTMENU') && empty($conf->dol_optimize_smallscreen)) { ?>
        <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
            <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
                padding-top: 40px;
            <?php } else { ?>
                padding-top: 54px;
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php if (!getDolGlobalString('OBLYON_FULLSIZE_TOPBAR')) { ?>
        <?php if(!getDolGlobalString('OBLYON_STICKY_LEFTBAR')) { ?>
            position: relative;
        <?php } else { ?>
            position: fixed;
        <?php } ?>
    <?php } ?>
    <?php if (!getDolGlobalString('OBLYON_HIDE_LEFTMENU') && empty($conf->dol_optimize_smallscreen) && (!getDolGlobalString('OBLYON_FULLSIZE_TOPBAR') || !getDolGlobalString('OBLYON_SHOW_COMPNAME'))) { ?>
        z-index: 92;
    <?php } else { ?>
        z-index: 90;
    <?php } ?>
}

#id-top {
    background-color: <?php print $bgnavtop; ?>;
    z-index: 91;
}

div.fiche {
	margin-<?php print $left; ?>: <?php print (GETPOST('optioncss', 'aZ09') == 'print'?6:(empty($conf->dol_optimize_smallscreen)?'15':'6')); ?>px;
	margin-<?php print $right; ?>: <?php print (GETPOST('optioncss', 'aZ09') == 'print'?6:(empty($conf->dol_optimize_smallscreen)?'15':'6')); ?>px;
	<?php if (! empty($dol_hide_leftmenu)) print 'margin-bottom: 12px;'."\n"; ?>
	<?php if (! empty($dol_hide_leftmenu)) print 'margin-top: 12px;'."\n"; ?>
}
body.onlinepaymentbody div.fiche {	/* For online payment page */
	margin: 20px !important;
}
div.fiche>table:first-child {
	margin-bottom: 15px !important;
}
div.fiche>table.table-fiche-title {
    margin-bottom: 12px;
}
div.fichecenter {
    clear: both;	/* This is to have div fichecenter that are true rectangles */
    width: 100%;
}
div.fichecenterbis {
    margin-top: 8px;
}
div.fichethirdleft {
    <?php if ($conf->browser->layout != 'phone') {
        print "float: ".$left.";\n";
    } ?>
    <?php if ($conf->browser->layout != 'phone') {
        print "width: calc(50% - 14px);\n";
    } ?>
    <?php if ($conf->browser->layout == 'phone') {
        print "padding-bottom: 6px;\n";
    } ?>
}
div.fichetwothirdright {
<?php if ($conf->browser->layout != 'phone') {
    print "float: ".$right.";\n";
} ?>
<?php if ($conf->browser->layout != 'phone') {
    print "width: calc(50% - 14px);\n";
} ?>
<?php if ($conf->browser->layout == 'phone') {
    print "padding-bottom: 6px\n";
} ?>
}
div.fichehalfleft {
    <?php if ($conf->browser->layout != 'phone') {
        print "float: ".$left.";\n";
    } ?>
    <?php if ($conf->browser->layout != 'phone') {
        print "width: calc(50% - 14px);\n";
    } ?>
}
div.fichehalfright {
    <?php if ($conf->browser->layout != 'phone') {
        print "float: ".$right.";\n";
    } ?>
    <?php if ($conf->browser->layout != 'phone') {
        print "width: calc(50% - 14px);\n";
    } ?>
}
div.fichehalfright {
    <?php if ($conf->browser->layout == 'phone') {
        print "margin-top: 10px;\n";
    } ?>
}

/*div.firstcolumn div.box {
	padding-right: 10px;
}
div.secondcolumn div.box {
	padding-left: 10px;
}*/

/* Force values for small screen */
@media only screen and (max-width: 1000px)
{
    div.fiche {
        margin-<?php print $left; ?>: <?php print (GETPOST('optioncss', 'aZ09') == 'print' ? 6 : ($dol_hide_leftmenu ? '6' : '20')); ?>px;
        margin-<?php print $right; ?>: <?php print (GETPOST('optioncss', 'aZ09') == 'print' ? 8 : 6); ?>px;
    }
    div.fichecenter {
        width: 100%;
        clear: both;	/* This is to have div fichecenter that are true rectangles */
    }
    div.fichecenterbis {
        margin-top: 8px;
    }
    div.fichethirdleft {
        float: none;
        width: auto;
        padding-bottom: 6px;
    }
    div.fichetwothirdright {
        float: none;
        width: auto;
        padding-bottom: 6px;
    }
    div.fichetwothirdright div.ficheaddleft {
    	padding-left: 0;
	}
    div.fichehalfleft {
        float: none;
        width: auto;
    }
    div.fichehalfright {
        float: none;
        width: auto;
    }
    div.fichehalfright {
    	margin-top: 10px;
    }
    div.firstcolumn div.box {
		padding-right: 0px;
	}
	div.secondcolumn div.box {
		padding-left: 0px;
	}
}

/* Force values on one colum for small screen */
@media only screen and (max-width: 1599px)
{
    div.fichehalfleft-lg {
    	float: none;
    	width: auto;
    }
    div.fichehalfright-lg {
    	float: none;
    	width: auto;
    }

    .fichehalfright-lg .fichehalfright {
    	padding-left:0;
    }
}

/* For table into table into card */
div.fichehalfright tr.liste_titre:first-child td table.nobordernopadding td {
    padding: 0 0 0 0;
}
div.nopadding {
	padding: 0 !important;
}

.containercenter {
	display : table;
	margin : 0px auto;
}

#pictotitle, .pictotitle {
    margin-<?php echo $right; ?>: 8px;
    margin-bottom: 4px;
}
.pictoobjectwidth {
    width: 14px;
}
.pictosubstatus {
    padding-left: 2px;
    padding-right: 2px;
}
.pictostatus {
    width: 15px;
    vertical-align: middle;
    margin-top: -3px
}
.pictowarning, .pictopreview {
    padding-<?php echo $left; ?>: 3px;
}
.pictowarning {
    vertical-align: text-bottom;
}
.pictomodule {
	width: 14px;
}
.fiche .arearef img.pictoedit, .fiche .arearef span.pictoedit,
.fiche .fichecenter img.pictoedit, .fiche .fichecenter span.pictoedit,
.tagtdnote span.pictoedit {
    opacity: 0.4;
}
.pictofixedwidth {
    text-align: <?php echo $left; ?>;
    width: 20px;
    padding-right: 0;
}
.colorthumb {
	padding-left: 1px !important;
	padding-right: 1px;
	padding-top: 1px;
	padding-bottom: 1px;
	width: 44px;
	text-align:center;
}
div.attacharea {
	padding-top: 18px;
	padding-bottom: 10px;
}
div.attachareaformuserfileecm {
	padding-top: 0;
	padding-bottom: 0;
}
div.arearef {
<?php if (getDolGlobalString('FIX_AREAREF_CARD')) { ?>
	position: sticky;
    z-index: 4;
	<?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        <?php if (getDolGlobalString('FIX_STICKY_TABS_CARD') && getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
    	top: 84px;
	    <?php } elseif (getDolGlobalString('FIX_STICKY_TABS_CARD') || getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
        top: 42px;
        <?php } else { ?>
	    top: 0px;
	    <?php } ?>
	<?php } else { ?>
        <?php if (getDolGlobalString('FIX_STICKY_TABS_CARD') && getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
            top: 84px;
        <?php } elseif (getDolGlobalString('FIX_STICKY_TABS_CARD') || getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
            top: 42px;
        <?php } else { ?>
            top: 0px;
        <?php } ?>
	<?php } ?>
	background: inherit;
    padding-bottom: 20px;
    border-bottom: 1px solid #DDD;
<?php } else { ?>
    padding-bottom: 10px;
<?php } ?>
	padding-top: 2px;
	margin-bottom: 10px;
}
div.arearefnobottom {
	padding-top: 2px;
	padding-bottom: 4px;
}
div.heightref {
	min-height: 80px;
}
div.divphotoref {
	padding-<?php echo $right; ?>: 20px;
}
div.paginationref {
	padding-bottom: 10px;
}
/* TODO
div.statusref {
   	padding: 10px;
   	border: 1px solid #bbb;
   	border-radius: 6px;
} */
div.statusref {
	float: right;
	padding-left: 12px;
	margin-top: 8px;
	margin-bottom: 10px;
	clear: both;
}
div.statusref img {
   	vertical-align: text-bottom;
   	width: 18px;
}
div.statusrefbis {
    padding-left: 8px;
   	padding-right: 9px;
   	vertical-align: text-bottom;
}
img.photoref, div.photoref {
	border: 1px solid #DDD;
    -webkit-box-shadow: 0px 0px 6px #DDD;
    box-shadow: 0px 0px 6px #DDD;
    padding: 4px;
	height: 80px;
	width: 80px;
    object-fit: contain;
}
img.fitcontain {
    object-fit: contain;
}
div.photoref {
	display:table-cell;
	vertical-align:middle;
	text-align:center;
}
img.photorefnoborder {
    padding: 2px;
	height: 48px;
	width: 48px;
    object-fit: contain;
    border: 1px solid #AAA;
    border-radius: 100px;
}
.underrefbanner {
}
.underbanner {
	border-bottom: <?php echo $borderwidth ?>px solid var(--colortopbordertitle1);
	/* border-bottom: 2px solid rgb(<?php echo $colorbackhmenu1 ?>); */
}
.trextrafieldseparator td {
    /* border-bottom: 2px solid rgb(<?php echo $colorbackhmenu1 ?>) !important; */
	border-bottom: 2px dashed var(--colortopbordertitle1) !important;
}

.tdhrthin {
	margin: 0;
	padding-bottom: 0 !important;
}

/*------------------------------------*\
#Top Menu
\*------------------------------------*/

#tmenu_tooltipinvert .db-menu__society,
#tmenu_tooltip .db-menu__society { /* for v3.5 */
    display: inline-block;
    float: <?php print $left; ?>;
    /*margin: 0 10px;*/
    padding: 0 5px 0 5px;
    max-width: 210px;
    text-align: <?php print $left; ?>;

    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
    -khtml-user-select: none; /* Konqueror HTML */
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* Internet Explorer/Edge */
    user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}

#tmenu_tooltipinvert .db-menu__society a,
#tmenu_tooltip .db-menu__society a { /* for v3.5 */
    color: #fff;
    display: inline;
    font-weight: 500;
    height: 40px;
    line-height: 40px;
    padding: 0 5px;
    text-decoration: none;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: all .4s ease-in-out;
    -moz-transition: all .4s ease-in-out;
    -webkit-transition: all .4s ease-in-out;
}

#tmenu_tooltipinvert .db-menu__society a:hover,
#tmenu_tooltip .db-menu__society a:hover { /* for v3.5 */
    color: <?php print $maincolor; ?>;
}

/*
* Main Navigation
*/

#tmenu_tooltip {
    <?php if (GETPOST("optioncss") == 'print') { ?>
        display: none;
    <?php } else { ?>
        display: block;
        overflow: auto;
        width: 100%;
        background-color: <?php print $bgnavtop; ?>;
        <?php if (!empty($usecss3)) { ?>
            <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
                box-shadow: 0 1px 2px rgba(0, 0, 0, .4) !important;
                -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .4) !important;
                -webkit-animation: fade 500ms;
            <?php } ?>
            transition: max-height .2s ease-in-out;
            -moz-transition: max-height .2s ease-in-out;
            -webkit-transition: max-height .2s ease-in-out;
        <?php } ?>
        <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
            max-height: 40px;
        <?php } else { ?>
            max-height: 54px;
        <?php } ?>
        margin: 0;
        padding-<?php print $right; ?>: 195px;
        z-index: 95;
        <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        <?php } else { ?>
            position: relative;
        <?php } ?>
    <?php } ?>
}

#tmenu_tooltip:hover {
    max-height: 540px;
}

.main-nav {
    <?php if (GETPOST("optioncss") == 'print') { ?>
        display: none;
    <?php } else { ?>
        /*background-color: rgb(<?php echo (!empty($colorback1) ? $colorback1 : 0); ?>);*/
        color: #fcfcfc;
        font-size: 13px;
        margin: 0;
        padding: 0;
        position: relative;
        text-decoration: none;
        white-space: nowrap;
    <?php } ?>
}

.main-nav__list {
    list-style: none;
    margin-bottom: 20px;
    padding: 0;
}

.main-nav__item {
    <?php if (!getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        float: <?php print $left; ?>;
        height: <?php print $heightmenu; ?>px;
    <?php } ?>
    display: block;
    margin: 0;
    padding: 0;
    position: relative;
}

.main-nav__item {
    background-color: <?php print $bgnavtop; ?>;
}

.main-nav__item:hover {
    background-color: <?php print $bgnavtop_hover; ?>;
    color: <?php print $bgnavtop_txt; ?>;
}

.main-nav__item.is-sel a {
	<?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
		background-color: <?php print $bgnavleft_hover; ?>;
		color: <?php print $bgnavleft_txt_active; ?>;
	<?php } else { ?>
    background-color: <?php print $bgnavtop_hover; ?>;
		color: <?php print $bgnavtop_txt_active; ?>;
        /*
        border-style: solid;
        border-width: 6px 10px 6px 0px;
        border-color: transparent var(--bgcolor) transparent transparent;
        */
    <?php } ?>
}


#tmenu_tooltip .main-nav__list {
    margin: 0;
    padding: 0;
    text-align: center;
    z-index: 30;
}

#tmenu_tooltip .main-nav__item {
    display: block;
    float: <?php print $left; ?>;
    position: relative;
    <?php if (getDolGlobalString('OBLYON_HIDE_TOPICONS')) { ?>
        height: 54px;
        line-height: 54px;
    <?php } else { ?>
        height: 54px;
    <?php } ?>
}

.main-nav__item.tmenusel {
    background-color: <?php print $bgnavtop_hover; ?>;
}

.main-nav__item.tmenusel .main-nav__link {
    font-weight: bold !important;
}

.main-nav__item.tmenusel:hover {
    color: #fff;
}

.main-nav__item.tmenusel .main-nav__link:hover {
    color: #fff;
    font-weight: bold;
}



/*
.main-nav__item:hover a,
.main-nav__list:visited li a {
color: #eee;
display: block;
font-weight: normal;
height: 54px;
padding: 0 6px;
text-decoration: none;
transition: all .2s ease-in-out;
-moz-transition: all .2s ease-in-out;
-webkit-transition: all .2s ease-in-out;
}*/

#tmenu_tooltip .tmenu li:hover .main-nav__link,
.main-nav__item:hover .main-nav__link,
.main-nav__item .main-nav__link:focus {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        color: <?php print $bgnavleft_txt; ?>;
    <?php } else { ?>
        color: <?php print $bgnavtop_txt; ?>;
    <?php } ?>
}

.main-nav__link {
	color: <?php print $bgnavtop_txt; ?> !important;
    display: block;
    font-family: var(--fontfamilydol);
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
}

#tmenu_tooltip .main-nav__link {
    height: 54px;
    <?php if (getDolGlobalString('OBLYON_HIDE_TOPICONS')) { ?>
        font-weight: 500;
        line-height: 54px;
        padding: 0 8px;
    <?php } else { ?>
        padding: 0 6px;
    <?php } ?>
}

.main-nav__link.is-disabled {
    cursor: not-allowed;
    opacity: .6;
}
.main-nav__link.is-disabled:hover {
    color: #888;
}

.db-nav .main-nav__link {
    text-decoration: none;
}

/**
* Secondary Navigation
*/

#tmenu_tooltipinvert .pushy-btn,
#tmenu_tooltip .pushy-btn { /* for v3.5 */
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        font-size: 18px !important;
        height: 40px;
        line-height: 40px;
    <?php } else { ?>
        font-size: 18px !important;
        height: 54px;
        line-height: 54px;
    <?php } ?>
}

#tmenu_tooltipinvert {
<?php if (GETPOST("optioncss") == 'print') { ?>
    display: none;
<?php } else { ?>
    display: inline-table;
    overflow: auto;
    width: 100%;
    background-color: <?php print $bgnavleft; ?>;
    <?php if (!empty($usecss3)) { ?>
        <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
            /*
            box-shadow: 0 1px 2px rgba(0, 0, 0, .4) !important;
            -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .4) !important;
            */
            -webkit-animation: fade 500ms;
        <?php } ?>
        transition: max-height .2s ease-in-out;
        -moz-transition: max-height .2s ease-in-out;
        -webkit-transition: max-height .2s ease-in-out;
    <?php } ?>
    max-height: 40px;
    <?php print $left; ?>: 0;
    margin: 0;
    padding-<?php print $right; ?>: 160px;
    z-index: 95;
    <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
    <?php } else { ?>
        position: relative;
    <?php } ?>
<?php } ?>
}

#tmenu_tooltipinvert:hover {
    max-height: 400px;
}

.sec-nav.is-inverted {
    display: inline-block;
    <?php if(!getDolGlobalString('OBLYON_FULLSIZE_TOPBAR') && !getDolGlobalString('OBLYON_SHOW_COMPNAME') && !getDolGlobalString('OBLYON_HIDE_LEFTMENU') && empty($conf->dol_optimize_smallscreen)) { ?>
        margin-<?php print $left; ?>: 10px;
    <?php } else { ?>
        margin-<?php print $left; ?>: 10px;
    <?php } ?>
    list-style: none;
}

.sec-nav.is-inverted .sec-nav__item.item-heading,
.sec-nav.is-inverted .sec-nav__item.is-disabled {
    background-color: <?php print $bgnavleft; ?>;
    float: <?php print $left; ?>;
    position: relative;
    padding: 0;
    z-index: 40;
}

.sec-nav.is-inverted .sec-nav__item.item-heading:hover {
    background-color: <?php print $bgnavleft_hover; ?>;
}

.sec-nav.is-inverted .sec-nav__link {
    font-size: 13px;
    white-space: nowrap;
}

.sec-nav.is-inverted .sec-nav__link:hover,
.sec-nav.is-inverted .sec-nav__link:focus {
    /* color: <?php print $maincolor; ?>; */
}

.sec-nav.is-inverted .sec-nav__item.item-heading > .sec-nav__link,
.sec-nav.is-inverted .sec-nav__item.is-disabled > .sec-nav__link {
    display: block;
    line-height: 40px;
    <?php if (getDolGlobalString('OBLYON_HIDE_TOPICONS')) { ?>
        font-weight: 500;
    <?php } else { ?>
        font-weight: normal;
    <?php } ?>
    padding: 0 8px;
}

.sec-nav.is-inverted .sec-nav__item.is-disabled > .sec-nav__link {
    cursor: not-allowed;
}

li.item-heading:hover > .sec-nav__link {
    background-color: <?php print $bgnavleft_hover; ?>;
    color: <?php print $bgnavleft_txt_hover; ?>;
}

li.sec-nav__sub-item {
    color: <?php print $bgnavleft_txt; ?>;
    list-style: none;
}
li.sec-nav__sub-item:hover, li.sec-nav__sub-item sec-nav__link:hover {
	background-color: <?php print $bgnavleft_hover; ?>;
	color: <?php print $bgnavleft_txt_hover; ?>;
}
li.sec-nav__sub-item:focus, li.sec-nav__sub-item sec-nav__link:focus {
	background-color: <?php print $bgnavleft_hover; ?>;
	color: <?php print $bgnavleft_txt_active; ?>;
}

.caret {
    content: '';
    color: inherit;
    display: inline-block;
    height: 0;
    vertical-align: baseline;
    width: 0;
    padding-bottom: 2px;
}

.caret--top {
    border-top: 4px solid #eee;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
}

.caret--left {
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    border-left: 4px solid #eee;
    margin-right: .1em;
}

.caret--right {
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    border-right: 4px solid #eee;
    margin-left: .1em;
}

.sec-nav.is-inverted li.item-heading:hover .caret--top {
	border-top-color: <?php print $bgnavleft_txt_hover; ?>;
}

.sec-nav__sub-list .item-level2:hover .caret--left {
	border-left-color: <?php print $bgnavleft_txt_hover; ?>;
}

.sec-nav__sub-list .item-level2:hover .caret--right {
	border-right-color: <?php print $bgnavleft_txt_hover; ?>;
}

.sec-nav__sub-list .item-level3:hover .caret--left {
	border-left-color: <?php print $bgnavleft_txt_hover; ?>;
}

.sec-nav__sub-list .item-level3:hover .caret--right {
	border-right-color: <?php print $bgnavleft_txt_hover; ?>;
}

/**
* Submenus
*/

.sec-nav.is-inverted .sec-nav__sub-list {
	background-color: <?php print $bgnavleft_hover; ?>;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.055);
    display: none;
    list-style: none;
    opacity: 0;
    padding-top: 0;
    padding-bottom: 5px;
    padding-inline-start: 0;
    -webkit-transiton: opacity 0.2s;
    -moz-transition: opacity 0.2s;
    -ms-transition: opacity 0.2s;
    -o-transition: opacity 0.2s;
    -transition: opacity 0.2s;
}

.sec-nav.is-inverted .sec-nav__item:hover .sec-nav__sub-list {
    display: block;
    position: absolute;
    opacity: 1;
    visibility: visible;
}

.sec-nav.is-inverted .sec-nav__sub-item {
    float: none;
    padding: 0;
}
.sec-nav.is-inverted .sec-nav__sub-item:hover, .sec-nav.is-inverted .sec-nav__link:hover {
	background-color: <?php print $bgnavleft; ?>;
}

.sec-nav.is-inverted .sec-nav__sub-list .item-level1 .sec-nav__link {
    display: block;
    padding: 0.6em 1em;

}

.sec-nav.is-inverted .sec-nav__sub-list .item-level2 .sec-nav__link {
    display: block;
    padding: 0.5em 1.2em;
}

.sec-nav.is-inverted .sec-nav__sub-list .item-level3 .sec-nav__link {
    display: block;
    padding: 0.4em 1.4em;
}

.sec-nav.is-inverted .sec-nav__link.is-disabled {
    display: block;
    padding: 0.6em 1em;
}


/**
* Login Block
*/
div.login_block {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavleft; ?>;
        height: 40px;
    <?php } else { ?>
        background-color: <?php print $bgnavtop; ?>;
        height: 54px;
    <?php } ?>
    /* padding-right: 10px; */
    <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
        position: fixed !important;
    <?php } else { ?>
        position: absolute !important;
    <?php } ?>
    top: 0;
    <?php print $right; ?>: 0px;
    z-index: 100;
    <?php if (GETPOST("optioncss") == 'print') { ?>
        display: none;
    <?php } ?>
}

div.login_block a {
	color: <?php print $bgnavtop_txt; ?>;
	display: inline-block;
}
div.login_block span.aversion {
    <?php if(getDolGlobalString('OBLYON_DISABLE_VERSION')) { ?>
        display: none !important;
    <?php } else { ?>
        <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
            color: <?php echo $bgnavleft_txt; ?>;
        <?php } else { ?>
            color: <?php echo $bgnavtop_txt; ?>;
        <?php } ?>
        filter: contrast(0.7);
    <?php } ?>
}
div.login_block table {
	display: inline;
}
div.login {
	white-space:nowrap;
	font-weight: bold;
	float: right;
}
div.login a {
	color: <?php print $bgnavtop_txt; ?>;
}
div.login a:hover {
	color: <?php print $maincolor; ?>;
	text-decoration:underline;
}

div.login_block:after {
    /*content: '\f013';*/
    color: <?php print $bgnavtop_txt; ?>;
    font-family: var(--fontawesomeFamily) !important;
    font-size: 20px;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        line-height: 40px;
    <?php } else { ?>
        line-height: 54px;
    <?php } ?>
}

div.login_block:hover:after {
    color: <?php print $maincolor; ?>;
}
div.login_block_user {
	display: inline-block;
	vertical-align: middle;
    /*clear: left;*/
    /*float: <?php print $left; ?>;*/
    margin-right: 0px;
}

div.login_block_user .login a,
div.login_block_user a {
    display: table-cell;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        font-size: 13px;
    <?php } ?>
    font-family: <?php print $fontmainmenu; ?>;
    font-weight: 500;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        height: 40px;
    <?php } else { ?>
        height: 54px;
    <?php } ?>
    max-width: 300px;
    overflow: hidden;
    padding: 0 3px;
    text-overflow: ellipsis;
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
    vertical-align: middle;
}

div.login_block_user > .classfortooltip.login_block_elem2 {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        height: 40px;
    <?php } else { ?>
        height: 54px;
    <?php } ?>
}

div.login_block_other {
    display: inline-block;
    clear: <?php echo $disableimages ? 'none' : 'both'; ?>;
}

.login_block_other {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background: <?php print $bgnavleft; ?>;
    <?php } else { ?>
        background: <?php print $bgnavtop; ?>;
    <?php } ?>
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        display: none;
    <?php } ?>
    /* position: absolute; */
    right: 0;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        top: 40px;
        height: 40px;
        line-height: 36px;
    <?php } else { ?>
        top: 54px;
        height: 54px;
        line-height: 50px;
    <?php } ?>
    padding-top: 0;
    text-align: right;
    margin-right: 3px;
}

.login_block:hover > .login_block_other {
    /* display: block; */
}

.login_block_user a img.loginphoto {
    display: none;
}

.login_block_elem a span.atoplogin, .login_block_elem span.atoplogin {
	vertical-align: middle;
}

.login_block_elem {
    float: <?php print $left; ?>;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavleft; ?>;
        height: 40px;
    <?php } else { ?>
        background-color: <?php print $bgnavtop; ?>;
        height: 54px;
    <?php } ?>
    padding: 0;
}

.login_block_elem.classfortooltip {
    margin: 0;
}

.login_block_elem a,
.login_block td.classfortooltip a {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        color: <?php print $bgnavleft_txt; ?>;
        font-size: 16px;
        height: 40px;
        line-height: 36px;
    <?php } else { ?>
        color: <?php print $bgnavtop_txt; ?>;
        font-size: 18px;
        height: 54px;
        line-height: 50px;
    <?php } ?>
    display: block;
    font-family: var(--fontfamilydol);
    padding: 0 3px;
    text-decoration: none;
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
}

.login_block_elem a:hover,
.login_block td.classfortooltip a:hover {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        color: <?php print $bgnavleft_txt_hover; ?>;
    <?php } else { ?>
        color: <?php print $bgnavtop_txt_hover; ?>;
    <?php } ?>
}

.atoplogin, .atoplogin:hover {
<?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
    color: <?php print $bgnavleft_txt; ?> !important;
<?php } else { ?>
    color: <?php print $bgnavtop_txt; ?> !important;
<?php } ?>
}
.login_block_getinfo {
    text-align: center;
}
.login_block_getinfo div.login_block_user {
    display: block;
}
.login_block_getinfo .atoplogin, .login_block_getinfo .atoplogin:hover {
	color: #333 !important;
	font-weight: normal !important;
}
.alogin, .alogin:hover {
	font-weight: normal !important;
	padding-top: 2px;
}
.alogin:hover, .atoplogin:hover {
	text-decoration:underline !important;
}
span.fa.atoplogin, span.fa.atoplogin:hover {
	font-size: 16px;
	text-decoration: none !important;
}
.atoplogin #dropdown-icon-down, .atoplogin #dropdown-icon-up {
	font-size: 0.7em;
}

.login_block_elem img.printer,
.login_block_elem img.login,
.login_block_elem img.help,
.login_block td.classfortooltip img.printer,
.login_block td.classfortooltip img.login,
.login_block td.classfortooltip img.help {
    vertical-align: baseline;
}

img.login, img.printer, img.help, img.entity {
    /* padding: 0px 0px 0px 4px; */
    /* margin: 0px 0px 0px 8px; */
    text-decoration: none;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        color: <?php print $bgnavleft_txt; ?>;
    <?php } else { ?>
        color: <?php print $bgnavtop_txt; ?>;
    <?php } ?>
    font-weight: bold;

}

.userimg.atoplogin img.userphoto, .userimgatoplogin img.userphoto {		/* size for user photo in login bar */
    width: <?php echo $disableimages ? '26' : '32'; ?>px;
    height: <?php echo $disableimages ? '26' : '32'; ?>px;
    border-radius: 50%;
    background-size: contain;
	border: 1px solid;
	border-color: rgba(255, 255, 255, 0.2);
}
img.userphoto {				/* size for user photo in lists */
    border-radius: 0.72em;
    width: 1.4em;
    height: 1.4em;
    background-size: contain;
    vertical-align: middle;
}

img.userphotosmall {		/* size for user photo in lists */
    border-radius: 0.6em;
    width: 1.2em;
    height: 1.2em;
    background-size: contain;
    vertical-align: middle;
	background-color: #FFF;
}
img.userphoto[alt="Gravatar avatar"], img.photouserphoto.dropdown-user-image[alt="Gravatar avatar"] {
	background: #fff;
}
form[name="addtime"] img.userphoto {
	border: 1px solid #444;
}

.span-icon-user {
    background-image: url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/object_user.png',1); ?>);
    background-repeat: no-repeat;
}

.span-icon-password {
    background-image: url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/lock.png',1); ?>);
    background-repeat: no-repeat;
}

/*
.span-icon-user input, .span-icon-password input {
margin-right: 30px;
}
*/

.login_block td.classfortooltip { height: 40px; }

.login_block .classfortooltip:hover,
.login_block .classfortooltip:focus {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavleft_hover; ?>;
    <?php } else { ?>
        background-color: <?php print $bgnavtop_hover; ?>;
    <?php } ?>
}

div.login_block table { display: inline; }

/* db inf v3.5 */
td div.login {
    white-space: nowrap;
    padding: 0;
    margin: 0;
    font-weight: bold;
    color: #f4f4f4;
}

div.login a,
div.login_block_user a {
    color: #f4f4f4;
    font-size: 13px;
}

div.login a:hover {
    color: <?php print $maincolor; ?>;
    text-decoration: inherit;
}

.alogin {
    font-weight: normal !important;
    font-size: <?php echo $fontsizesmaller; ?>px !important;
}

.alogin:hover {
    text-decoration: underline !important;
    color: <?php print $maincolor; ?> !important;
}


/*------------------------------------*\
#Left Menu
\*------------------------------------*/

/**
* Company Name
*/

.db-menu__society {
    margin: 0;
    padding: 0;
}

.db-menu__society h1 {
    color: #fff;
    font-size: 1.5em;
    font-weight: bold;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

.vmenu .db-menu__society {
    padding: 10px 0;
}


/**
* Logo Block
*/

.db-menu__logo {
    background-color: <?php print $logo_background_color ?>;
    <?php if (getDolGlobalString('OBLYON_LOGO_PADDING') && getDolGlobalString('OBLYON_LOGO_PADDING') == "padding") { ?>
        padding: 10px;
        max-height: 180px;
    <?php } else { ?>
        padding: 0;
        max-height: 200px;
    <?php } ?>
}

.db-menu__logo__link {
    display: block;
    <?php if(getDolGlobalString('OBLYON_COLOR_LOGO_BCKGRD')) { ?>
        background: <?php print $logo_background_color; ?>;
    <?php } else { ?>
        background: #FFF;
    <?php } ?>
    margin: 0;
}

.db-menu__logo__img {
    <?php if (getDolGlobalString('OBLYON_LOGO_PADDING') && getDolGlobalString('OBLYON_LOGO_PADDING') == "padding") { ?>
        max-height: 140px;
    <?php } else { ?>
        max-height: 120px;
    <?php } ?>
    <?php if (getDolGlobalString('OBLYON_LOGO_SIZE')) { ?>
        height: 80px;
    <?php } else { ?>
        height: auto;
    <?php }	?>
    max-width: 100%;
    width: auto;
}


/**
* Secondary Navigation
*/

.sec-nav__list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.sec-nav__item {
    display: block;
}

.vmenu .sec-nav__item.item-heading > .sec-nav__link {
    background-color: <?php print $bgnavleft_hover; ?>;
    font-weight: bold;
    display: block;
    line-height: 1em;
    padding: 10px;
    <?php if (getDolGlobalString('OBLYON_HIDE_LEFTICONS')) { ?>
        font-weight: 500;
    <?php } ?>
}

.sec-nav { color: <?php print $bgnavleft; ?>; }

.sec-nav .sec-nav__link {
    color: <?php print $bgnavleft_txt; ?>;
    font-size: <?php print $fontsize; ?>px;
    font-family: var(--fontfamilydol);
    font-weight: normal;
    text-align: <?php print $left; ?>;
    text-decoration: none;
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
}

.sec-nav .sec-nav__link:hover {
	background-color: <?php print $bgnavleft_hover; ?>;
	color: <?php print $bgnavleft_txt_hover; ?>;
}
.sec-nav .sec-nav__link:focus {
	background-color: <?php print $bgnavleft_hover; ?>;
	color: <?php print $bgnavleft_txt_active; ?>;
}

.vmenu .sec-nav__item.item-heading {
    margin-bottom: 15px;
}

.sec-nav__sub-list {
    background-color: <?php print $bgnavleft; ?>;
    padding-top: 5px;
    /* padding-inline-start: 1.5em; */
}

.sec-nav__sub-list .item-level1 {
    padding: 0.3em 0.8em 0.3em 0;
}

.sec-nav__sub-list .item-level2 {
    padding: 0.2em 1em 0.3em 0;
}

.sec-nav__sub-list .item-level3 {
    padding: 0.2em 1em 0.3em 0;
}

.sec-nav__sub-item.is-disabled {
    opacity: .6;
    padding: 0.3em 0.8em 0.3em 0;
}

.sec-nav .sec-nav__link.is-disabled {
    cursor: not-allowed;
}


/**
* Main Navigation
*/

.main-nav.is-inverted .main-nav__link {
line-height: 35px;
<?php if (getDolGlobalString('OBLYON_HIDE_LEFTICONS')) { ?>
    padding-<?php print $left; ?>: 10px;
    font-weight: 500;
<?php } ?>
overflow: hidden;
text-overflow: ellipsis;
}

.main-nav.is-inverted {
font-size: 14px;
}

/**
* Society Name Block
*/
.blockvmenusocietyname {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavtop_hover; ?>;
    <?php } else { ?>
        background-color: <?php print $bgnavleft_hover; ?>;
    <?php } ?>
    padding: 10px 0 10px 0;
}

.blockvmenusocietyname span {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        color: <?php print $bgnavtop_txt; ?>;
    <?php } else { ?>
        color: <?php print $bgnavleft_txt; ?>;
    <?php } ?>
    padding: 5px 10px 5px 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: bold;
}

/**
* Search Block
*/

.blockvmenusearch {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavtop; ?>;
        border-bottom: 1px solid <?php print $bgnavtop_hover; ?>;
    <?php } else { ?>
        background-color: <?php print $bgnavleft; ?>;
        border-bottom: 1px solid <?php print $bgnavleft_hover; ?>;
    <?php } ?>
    box-shadow: 0 0 1px rgba(0,0,0, .04);
    -webkit-box-shadow: 0 0 1px rgba(0,0,0, .04);
    clear: both;
    padding: 10px;
    text-decoration: none;
}

.blockvmenusearch .menu_titre {
    margin: 8px 0 1px 0;
}

.blockvmenusearch a:link,
.blockvmenusearch a:visited,
.blockvmenusearch a:active {
    color: #eee;
    font-family: <?php print $fontmenusearch; ?>;
    font-size:<?php print $fontsize; ?>px;
    text-align: <?php print $left; ?>;
}

.blockvmenusearch a:hover { color: <?php print $maincolor; ?>; }


/**
* Bookmarks Block
*/

.blockvmenubookmarks {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavtop; ?>;
        border-bottom: 1px solid <?php print $bgnavtop_hover; ?>;
    <?php } else { ?>
        background-color: <?php print $bgnavleft; ?>;
        border-bottom: 1px solid <?php print $bgnavleft_hover; ?>;
    <?php } ?>
    box-shadow: 0 0 1px rgba(0,0,0, .04);
    -webkit-box-shadow: 0 0 1px rgba(0,0,0, .04);
    clear: both;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
}

.blockvmenubookmarks .menu_titre {
    margin: 8px 0 1px 0;
    text-align: <?php print $left; ?>;
}

.blockvmenubookmarks .menu_titre a { font-size: 13px; }

.blockvmenubookmarks .menu_titre img:hover {
    background-image: url(img/object_bookmark_full.png);
}

.blockvmenubookmarks .menu_contenu {
    max-width: 230px;
    overflow: hidden;
    padding: 2px 6px;
    text-overflow: ellipsis;
}

.blockvmenubookmarks a:link,
.blockvmenubookmarks a:visited,
.blockvmenubookmarks a:active{
    color: var(--colorfline);
    font-family: <?php print $fontmenubookmarks; ?>;
    font-size:<?php print $fontsize; ?>px;
}

.blockvmenubookmarks a.vmenu:link,
.blockvmenubookmarks a.vmenu:visited { color: var(--colorfline); }

.blockvmenubookmarks a.vmenu:hover,
.blockvmenubookmarks a.vsmenu:hover { color: <?php print $maincolor; ?>; }


/**
* Help Block
*/

.blockvmenuhelp {
    <?php if (empty($conf->dol_optimize_smallscreen) || getDolGlobalString('OBLYON_REDUCE_LEFTMENU')) { ?>
        <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
            background-color: <?php print $bgnavtop; ?>;
        <?php } else { ?>
            background-color: <?php print $bgnavleft; ?>;
        <?php } ?>
        color: <?php print $maincolor ?>;
        font-family: <?php print $fontmenuhelp; ?>;
        margin: 0;
        text-align: center;
    <?php } else { ?>
        display: none;
    <?php } ?>
}

.blockvmenuhelp a {
    font-family: <?php print $fontmenuhelp; ?>;
    font-size: <?php print $fontsize; ?>px;
    display: inline-block;
}

.blockvmenuhelp a.help:link,
.blockvmenuhelp a.help:visited,
.blockvmenuhelp a.help:active {
    color: <?php echo $bgnavleft_txt; ?>;
    font-size: <?php print $fontsizesmaller; ?>px;
    font-weight: normal;
    text-align: <?php print $left; ?>;
    text-decoration: none;
}

.blockvmenuhelp a:hover {
    color: <?php print $maincolor; ?> !important;
}

.blockvmenuhelp a[href*="http://www.dolibarr."] {
    padding: 15px 0 5px;
    font-size: 15px;
}

.blockvmenuhelp a.help img {
    vertical-align: top;
}

.blockvmenuhelp:last-child {
    padding: 10px 0 10px 0;
}

.helppresent, .helppresent:hover {
    /* color: #f3e4ac !important; */
}
.helppresentcircle {
    /*
    color: rgb(<?php echo $colorbackhmenu1 ?>);
    filter: invert(0.8);
    */
    color: <?php print $badgeSecondary; ?>;
    margin-<?php echo $left ?>: -7px;
    display: inline-block;
    margin-top: -10px;
    font-size: x-small;
    vertical-align: super;
    opacity: 0.95;
}

/*------------------------------------*\
#Pushy Left Menu
\*------------------------------------*/

#id-left {
<?php if (getDolGlobalString('OBLYON_HIDE_LEFTMENU') || !empty($conf->dol_optimize_smallscreen)) { ?>
    position: <?php print ($conf->dol_optimize_smallscreen) ? 'fixed;' : 'absolute;'?>
    <?php if (!getDolGlobalString('OBLYON_STICKY_TOPBAR') && getDolGlobalString('OBLYON_EFFECT_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_LEFTMENU') == "push" ) { ?>
        top: 0;
    <?php } else { ?>
        <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
            top: 40px;
        <?php } else { ?>
            top: 54px;
        <?php } ?>
    <?php } ?>

    background-color: <?php print $bgnavleft; ?>;
    <?php if (!empty($usecss3)) { ?>
        box-shadow: 0 1px 2px rgba(0, 0, 0, .4);
        -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .4);
    <?php } ?>
    max-width: 265px;
    overflow: hidden;
    -webkit-overflow-scrolling: touch;
    <?php if (getDolGlobalString('OBLYON_EFFECT_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_LEFTMENU') == "push" && !empty($usecss3)) { ?>
        <?php print $left; ?>: 0;

        -webkit-transform: translate3d(-265px,0,0);
        -moz-transform: translate3d(-265px,0,0);
        -ms-transform: translate3d(-265px,0,0);
        -o-transform: translate3d(-265px,0,0);
        transform: translate3d(-265px,0,0);
    <?php } else { ?>
        <?php print $left; ?>: -270px;
    <?php } ?>
<?php } ?>
}

@media all and (orientation:landscape) {
    @media only screen and (max-height: 500px) {
        #id-left {
            max-height: 300px;
            overflow-y: auto;
        }
        #id-left::-webkit-scrollbar {
            display: none;
        }
    }
}

<?php if (getDolGlobalString('OBLYON_HIDE_LEFTMENU') || !empty($conf->dol_optimize_smallscreen)) { ?>
    #id-left, #id-container, .push {
    <?php if (getDolGlobalString('OBLYON_EFFECT_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_LEFTMENU') == "push") { ?>
        -webkit-transition: -webkit-transform .3s cubic-bezier(.16, .68, .43, .99);
        -moz-transition: -moz-transform .3s cubic-bezier(.16, .68, .43, .99);
        -o-transition: -o-transform .3s cubic-bezier(.16, .68, .43, .99);
        transition: transform .3s cubic-bezier(.16, .68, .43, .99);
    <?php } else { ?>
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        transition: all 0.3s ease;
    <?php } ?>
    }

    .container-push {
    <?php if (getDolGlobalString('OBLYON_EFFECT_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_LEFTMENU') == "push") { ?>
        -webkit-transform: translate3d(265px,0,0);
        -moz-transform: translate3d(265px,0,0);
        -ms-transform: translate3d(265px,0,0);
        -o-transform: translate3d(265px,0,0);
        transform: translate3d(265px,0,0);
    <?php } ?>
    }

    /**
    * Coming Feature: OVERLAY when LEFTMENU hidden
    */
    /*.pushy-active .site-overlay {
    display: block;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 99;
    background-color: rgba(0,0,0,0.5);
    -webkit-animation: fade 500ms;
    -moz-animation: fade 500ms;
    -ms-animation: fade 500ms;
    -o-animation: fade 500ms;
    animation: fade 500ms;
    }*/

    .pushy-active {
    -webkit-animation: fade 500ms;
    -moz-animation: fade 500ms;
    -ms-animation: fade 500ms;
    -o-animation: fade 500ms;
    animation: fade 500ms;
    overflow-x: hidden;
    overflow-y: auto;
    height: 100%;
    }

    .pushy-open {
    <?php if (getDolGlobalString('OBLYON_EFFECT_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_LEFTMENU') == "push" ) { ?>
        -webkit-transform: translate3d(0,0,0);
        -moz-transform: translate3d(0,0,0);
        -ms-transform: translate3d(0,0,0);
        -o-transform: translate3d(0,0,0);
        transform: translate3d(0,0,0);
    <?php } else { ?>
        <?php print $left; ?>: 0 !important;
    <?php } ?>
    }

    /**
    * Coming Feature: OVERLAY when LEFTMENU hidden
    */
    /*
    <?php if (getDolGlobalString('OBLYON_OVERLAY_LEFTMENU')) { ?>
        #id-right::after {
        background: rgba(0,0,0,0.3);
        display: none;
        opacity: 0;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        z-index: 1;
        width: 100%;
        height: 100%;
        -webkit-transform: translate3d(100%,0,0);
        transform: translate3d(100%,0,0);
        -webkit-transition: opacity 0.3s, -webkit-transform 0s 0.3s;
        transition: opacity 0.3s, transform 0s 0.3s;
        }

        .pushy-active #id-right::after {
        opacity: 1;
        display: block;
        -webkit-transition: opacity 0.3s;
        transition: opacity 0.3s;
        -webkit-transform: translate3d(0,0,0);
        transform: translate3d(0,0,0);
        }
    <?php } ?>
    */


    .pushy-btn {
        background-color: <?php print $bgnavtop; ?>;
        color: <?php print $bgnavtop_txt; ?>;
        display: inline-block;
        float: <?php print $left; ?>;
        font-size: 24px;
        height: 54px;
        line-height: 54px;
        padding: 0 10px;
        cursor: pointer;
    }

    .pushy-btn:hover {
        background-color: <?php print $bgnavtop_hover; ?>;
        color: <?php print $bgnavtop_txt_hover; ?>;
    }

    .pushy-active .pushy-btn {
        background-color: <?php print $bgnavtop_hover; ?>;
        color: <?php print $bgnavtop_txt_active; ?>;
    }

<?php } ?> /* end HIDE_LEFTMENU */

/*------------------------------------*\
    #Oblyon Main and Sec Nav Icons
\*------------------------------------*/

.main-nav .icon {
<?php if (getDolGlobalString('OBLYON_HIDE_TOPICONS') && !getDolGlobalString('MAIN_MENU_INVERT')) { ?>
    display: none;
<?php } else { ?>
    display: block;
<?php } ?>
    float: none;
    height: 34px;
    line-height: 36px;
    min-width: 40px;
    position: relative;
}

.main-nav.is-inverted .icon {
<?php if (getDolGlobalString('OBLYON_HIDE_LEFTICONS')) { ?>
    display: none;
<?php } ?>
    float: <?php print $left; ?>;
    height: 35px;
    line-height: 35px;
    margin: 0;
    position: relative;
    text-align: center;
    width: 40px;
}

.main-nav .icon {
    font-size: 18px;
}

.sec-nav .icon {
<?php if (!getDolGlobalString('MAIN_MENU_INVERT') && getDolGlobalString('OBLYON_HIDE_LEFTICONS')) { ?>
    display: none;
<?php } ?>
    float: <?php print $left; ?>;
    margin-<?php print $right; ?>: 5px;
}

.sec-nav.is-inverted .icon {
<?php if (getDolGlobalString('OBLYON_HIDE_TOPICONS')) { ?>
    display: none;
<?php } ?>
    height: 40px;
    line-height: 40px;
}

.sec-nav .icon {
    font-size: 14px;
}


.mainmenu.accounting {
    background: none !important;
}

/* ============================================================================== */
/* Fa-icons                                                                       */
/* ============================================================================== */
<?php include dol_buildpath($path.'/theme/'.$theme.'/main_menu_fa_icons.inc.php', 0); ?>

/*------------------------------------*\
#Top Menu (eldy style)
\*------------------------------------*/

/**
* Main Navigation
*/

<?php
if (! empty($conf->dol_optimize_smallscreen))
{
    $minwidthtmenu=0;
    $heightmenu=19;
}
else
{
    $minwidthtmenu=66;
    $heightmenu=52;
}
?>

.tmenudiv {
    <?php if (GETPOST("optioncss") == 'print') {	?>
        display: none;
    <?php } else { ?>
        color: #fcfcfc;
        display: block;
        font-size: 13px;
        font-weight: normal;
        margin: 0;
        padding: 0;
        position: relative;
        text-decoration: none;
        white-space: nowrap;
    <?php } ?>
}

#tmenu_tooltip ul.tmenu {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: center;
    z-index: 30;
}

.vmenu ul.tmenu {
    margin-bottom: 20px;
}

li.tmenu,
li.tmenusel {
    display: block;
    margin: 0;
    padding: 0;
    position: relative;
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
}

#tmenu_tooltip li.tmenu,
#tmenu_tooltip li.tmenusel {
    display: block;
    float: <?php print $left; ?>;
    position: relative;
    <?php if (getDolGlobalString('OBLYON_HIDE_TOPICONS')) { ?>
        height: 54px;
        line-height: 54px;
    <?php } else { ?>
        height: 54px;
        min-width: <?php print $minwidthtmenu; ?>px;
    <?php } ?>
}

li.tmenusel {
    background-color: <?php print $maincolor; ?>;
    color: #fff;
}

li.tmenu:hover {
    background-color: <?php print $bgnavtop_hover; ?>;
    color: <?php print $topmenu_hover; ?>;
}

#tmenu_tooltip li.tmenu {
    background-color: <?php print $bgnavtop; ?>;
}

#tmenu_tooltip li.tmenu:hover {
    background-color: <?php print $bgnavtop_hover; ?>;
}


/* Liens menu vertical */

div.tmenudisabled,
a.tmenudisabled {
    cursor: not-allowed;
    opacity: .6;
}

a.tmenu:link,
a.tmenu:visited,
a.tmenudisabled {
    display: block;
    font-weight: normal;
    /* padding: 0 5px; */
    text-decoration: none;
    white-space: nowrap;
}
a.tmenu:link,
a.tmenudisabled {
	color: <?php print $bgnavtop_txt; ?> !important;
}

a.tmenu:active {
    color: <?php print $bgnavtop_txt_active; ?> !important;
    margin: 0;
}

a.tmenu:hover {
    color: <?php print $bgnavtop_txt_hover; ?> !important;
    margin: 0;
}

a.tmenuimage:hover + a.tmenu {
    color: <?php print $maincolor; ?> !important;
}

.tmenu li a,
.tmenu:visited li a,
.tmenu:hover li a {
    font-weight: normal;
}

a.tmenusel:hover,
a.tmenusel:active {
    color: #fff;
    font-weight: bold !important;
}

li.tmenusel a,
li.tmenusel a:hover,
li.tmenusel a:active,
li.tmenusel a:link {
    color: #fff!important;
    font-weight: bold!important;
}

li.tmenuend {
    display: none;
}

div.tmenuleft {
    float: <?php print $left; ?>;
    height: <?php print $heightmenu+4; ?>px;
    margin-top: -4px;
}

div.tmenucenter {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        height: 40px;
        line-height: 40px;
    <?php } else { ?>
        height: <?php print $heightmenu+2; ?>px;
    <?php } ?>
    padding: 0;
    width: 100%;
}

/*
.main-nav__list .mainmenuaspan {
    <?php if (empty($conf->dol_optimize_smallscreen)) {
        if (getDolGlobalString('OBLYON_HIDE_LEFTICONS')) {	?>
            padding: 14px !important;
        <?php } else { ?>
            padding: 14px 0 !important;
        <?php }
    } else { ?>
        display: none;
    <?php } ?>
}
*/


/**
* Secondary Navigation
*/
div.blockvmenulogo
{
    border-bottom: 0 !important;
}
.menulogocontainer {
    margin: <?php echo $disableimages?'0':'3'; ?>px;
    margin-left: 11px;
    margin-right: 9px;
    padding: 0;
    height: <?php echo $disableimages?'20':'32'; ?>px;
    /* width: 100px; */
    max-width: 100px;
    vertical-align: middle;
}
.backgroundforcompanylogo {
    background-color: <?php echo $logo_background_color ?>;
    <?php if (getDolGlobalString('OBLYON_LOGO_PADDING') && getDolGlobalString('OBLYON_LOGO_PADDING') == "padding") { ?>
        padding: 0 5px 0 5px;
    <?php } else { ?>
        padding: 0;
    <?php } ?>
}
.menulogocontainer img.mycompany {
    object-fit: contain;
    width: inherit;
    height: inherit;
}
#mainmenutd_companylogo::after, #mainmenutd_menu::after {
    content: unset !important;
}
li#mainmenutd_companylogo .tmenucenter {
    width: unset;
    background-color: <?php echo $logo_background_color ?>;
}
li#mainmenutd_companylogo {
    min-width: unset !important;
}
<?php if ($disableimages) { ?>
    li#mainmenutd_home {
        min-width: unset !important;
    }
    li#mainmenutd_home .tmenucenter {
        width: unset;
    }
<?php } ?>

.blockvmenupairinvert {
    margin: 0;
    padding: 0;
    position: relative;
}

#tmenu_tooltipinvert div.menu_titre {
    float: <?php print $left; ?>;
}

#tmenu_tooltipinvert a.vmenu {
    color: <?php $bgnavleft_txt; ?>;
    display: block;
    font-size: 13px;
    line-height: 40px;
    padding: 0 9px;
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
}

#tmenu_tooltipinvert div.menu_contenu {
    /*display: none; @bug improve display lev 1 and 2 */
}

#tmenu_tooltipinvert div.menu_titre:hover {
    background-color: <?php print $bgnavleft_hover; ?>;
}

#tmenu_tooltipinvert div.menu_titre:hover + div.menu_contenu {
    display: block;
}

#tmenu_tooltipinvert img {
    vertical-align: text-bottom;
}



/*------------------------------------*\
#Left Menu (eldy style)
\*------------------------------------*/

/**
* Secondary Navigation
*/

div.vmenu {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        background-color: <?php print $bgnavtop; ?>;
    <?php } else { ?>
        background-color: <?php print $bgnavleft; ?>;
    <?php } ?>
    float: <?php print $left; ?>;
    margin-<?php print $right; ?>: 0;
    padding: 0;
    padding-bottom: 0;
    position: relative;
    z-index: 5;
    <?php if (empty($conf->dol_optimize_smallscreen)) { ?>
        <?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU')) { ?>
            max-width: 40px;
        <?php } else { ?>
            min-width: 230px;
            max-width: 230px;
        <?php } ?>
        <?php if (getDolGlobalString('OBLYON_HIDE_LEFTMENU') || !empty($conf->dol_optimize_smallscreen)) { ?>
            width: 230px;
        <?php } else { ?>
            width: 100%;
        <?php } ?>
    <?php } ?>
    min-height: 100vh;
    -webkit-transition-property: max-width;
    -webkit-transition-duration: 0.2s;
    -webkit-transition-timing-function: linear;
    transition-property: max-width;
    transition-duration: 0.2s;
    transition-timing-function: linear;
}

<?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU') == "hover") { ?>
    .vmenu:hover {
        max-width: 230px;
        <?php if (!getDolGlobalString('MAIN_MENU_INVERT')) { ?>
            min-width: 230px;
        <?php } ?>
        <?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU') == "hover") { ?>
            min-width: 230px;
        <?php } ?>
        -webkit-transition-property: max-width;
        -webkit-transition-duration: 0.2s;
        -webkit-transition-timing-function: linear;
        transition-property: max-width;
        transition-duration: 0.2s;
        transition-timing-function: linear;
    }
<?php } ?>

<?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU') == "hover") { ?>
    .vmenu.sec-nav__link:hover {
        min-width: unset !important;
    }
<?php } ?>

.vmenu {
    <?php if (GETPOST("optioncss") == 'print') { ?>
        display: none;
    <?php } ?>
}

.vmenu .blockvmenupair div.menu_titre,
.vmenu .blockvmenuimpair div.menu_titre {
    display: block;
}

.vmenu .blockvmenupair div.menu_titre a,
.vmenu .blockvmenuimpair div.menu_titre a {
    background-color: <?php print $bgnavleft_hover; ?>;
    color: #eee;
    display: block;
    padding: 8px;
    transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -webkit-transition: all .2s ease-in-out;
}

a.vmenu:link,
a.vmenu:visited,
a.vmenu:hover,
a.vmenu:active,
span.vmenu {
    font-size:<?php print $fontsize; ?>px;
    font-weight: normal;
    text-align: <?php print $left; ?>;
    text-decoration: none;
}

.vmenu div.blockvmenupair div.menu_titre a:hover,
.vmenu div.blockvmenuimpair div.menu_titre a:hover {
    color: <?php print $maincolor; ?>;
}

font.vmenudisabled	{
    color: #93a5aa;
    font-size:<?php print $fontsize; ?>px;
    font-weight: bold;
    text-align: <?php print $left; ?>;
}


/* sub-items */

.vmenu div.blockvmenupair .menu_contenu,
.vmenu div.blockvmenuimpair .menu_contenu {
    padding: 4px;
}

.vmenu div.blockvmenupair .menu_contenu:first-child,
.vmenu div.blockvmenuimpair .menu_contenu:first-child {
margin-top: 10px;
}

.vmenu .blockvmenupair div.menu_contenu a,
.vmenu .blockvmenuimpair div.menu_contenu a {
color: #eee;
font-weight: normal;
margin: 1px 1px 1px 8px;
text-decoration: none;
}

a.vsmenu:link,
a.vsmenu:visited,
a.vsmenu:active {
font-weight: normal;
}

.vmenu .blockvmenupair div.menu_contenu a:hover,
.vmenu .blockvmenuimpair div.menu_contenu a:hover {
color: <?php print $maincolor; ?>;
}

font.vsmenudisabled {
color: #93a5aa;
font-size:<?php print $fontsize; ?>px;
font-weight: normal;
text-align: <?php print $left; ?>;
}

font.vsmenudisabledmargin {
margin: 1px 1px 1px 8px;
}

a.vsmenu img{
vertical-align: bottom;
}



.vmenu div.blockvmenupair,
.vmenu div.blockvmenuimpair {
background-color: <?php print $bgnavleft; ?>;
padding: 0;
text-align: <?php print $left; ?>;
}

div.blockvmenuimpair:first-child { padding: 0; }

.vmenu .menu_top {
margin-top: 2.5px;
}

.vmenu .menu_end {
margin-bottom: 5px;
}


td.barre {
background-color: #b3c5cc;
border-right: 1px solid #000;
border-bottom: 1px solid #000;
color: #000;
text-align: <?php print $left; ?>;
text-decoration: none;
}

td.barre_select {
background-color: #b3c5cc;
color: #000;
}

td.photo {
background-color: #f4f4f4;
border: 1px solid #b3c5cc;
color: #000;
}

.vmenusearchselectcombo {
width: 100%;
}

/**
* Main Navigation
*/

/*------------------------------------*\
#Eldy Navigation Icons
\*------------------------------------*/

<?php if (empty($conf->dol_optimize_smallscreen)) { ?>

    .mainmenu {
    background-position: center center;
    background-repeat: no-repeat;
    background-size: 24px;
    margin-<?php print $left; ?>: 0;
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        float: <?php print $left; ?>;
        height: 40px;
        margin-<?php print $right; ?>: 5px;
        width: 40px;
    <?php } else { ?>
        height: 36px;
        min-width: 40px;
    <?php }

    if (getDolGlobalString('OBLYON_HIDE_TOPICONS') && !getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        display: none;
    <?php } elseif (getDolGlobalString('OBLYON_HIDE_LEFTICONS') && getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        display: none;
    <?php } else { ?>
        display: block;
    <?php } ?>
    }

    <?php
}	// End test if not phone
?>

<?php
// Add here more div for other menu entries. moduletomainmenu=array('module name'=>'name of class for div')
$moduletomainmenu=array('user'=>'','syslog'=>'','societe'=>'companies','projet'=>'project','propale'=>'commercial','commande'=>'commercial','produit'=>'products','service'=>'products','stock'=>'products','don'=>'accountancy','tax'=>'accountancy','banque'=>'accountancy','facture'=>'accountancy','compta'=>'accountancy','accounting'=>'accountancy','adherent'=>'members','import'=>'tools','export'=>'tools','mailing'=>'tools','contrat'=>'commercial','ficheinter'=>'commercial','ticket'=>'ticket','deplacement'=>'commercial','fournisseur'=>'companies','ftp'=>'','externalsite'=>'','barcode'=>'','fckeditor'=>'','categorie'=>'','opensurvey' => '', 'bittorrent'=>'', 'cron'=>'', 'scanner'=>'', 'reports'=>'');
$mainmenuused='home';

foreach($conf->modules as $val) {
    $mainmenuused.=','.(isset($moduletomainmenu[$val])?$moduletomainmenu[$val]:$val);
}
//var_dump($mainmenuused);
$mainmenuusedarray=array_unique(explode(',',$mainmenuused));

$generic=1;
// Put here list of menu entries when the div.mainmenu.menuentry was previously defined
$divalreadydefined=array('home','companies','products','commercial','externalsite','accountancy','project','tools','members','agenda','ftp','holiday','hrm','bookmark','cashdesk','ecm','geoipmaxmind','gravatar','clicktodial','paypal','stripe','webservices','website','ticket','mrp');
// Put here list of menu entries we are sure we don't want
$divnotrequired=array('multicurrency','salaries','margin','opensurvey','paybox','expensereport','incoterm','prelevement','propal','workflow','notification','supplier_proposal','cron','product','productbatch','expedition');
foreach($mainmenuusedarray as $val)
{
    if (empty($val) || in_array($val,$divalreadydefined)) continue;
    if (in_array($val,$divnotrequired)) continue;
    //print "XXX".$val;

    // Search img file in module dir
    $found=0;
    $url='';
    foreach($conf->file->dol_document_root as $dirroot) {
        if (file_exists($dirroot."/".$val."/img/".$val.".png")) {
            $url=dol_buildpath('/'.$val.'/img/'.$val.'.png', 1);
            $found=1;
            break;
        }
    }

    if ( $found ) {
        print ".mainmenu.".$val.", .icon--".$val." {\n";

        print "  background: url(".$url.") no-repeat center;\n";
        print "  background-size: 22px;\n";
        print "}\n";
    } else {
        print "/* A mainmenu entry but img file ".$val.".png not found (check /".$val."/img/".$val.".png), so we use a generic one */\n";
        print ".mainmenu.".$val.":before, .icon--".$val.":before {\n";
        print "  content: '\\f152';\n";
        print "}\n";
    }
}
//End of part to add more div class css
?>

/*------------------------------------*\
#Login Page
\*------------------------------------*/
<?php
include dol_buildpath($path.'/theme/'.$theme.'/login.inc.php', 0);
?>

/*------------------------------------*\
#Main Panes
\*------------------------------------*/

/*
*	PANES and CONTENT-DIVs
*/

#mainContent,
#leftContent .ui-layout-pane {
overflow: auto;
padding: 0;
}

#mainContent,
#leftContent .ui-layout-center {
overflow: auto;	/* add scrolling to content-div */
padding: 0;
position: relative; /* contain floated or positioned elements */
}


#containerlayout .layout-with-no-border {
border: 0 !important;
border-width: 0 !important;
}

#containerlayout .layout-padding {
padding: 2px !important;
}

#containerlayout .ui-layout-pane { /* all 'panes' */
background-color: #fff;
border: 1px solid #bbb;
/* DO NOT add scrolling (or padding) to 'panes' that have a content-div,
otherwise you may get double-scrollbars - on the pane AND on the content-div
*/
padding: 0;
overflow: auto;
}

/* (scrolling) content-div inside pane allows for fixed header(s) and/or footer(s) */
#containerlayout .ui-layout-content {
overflow: auto; /* add scrolling to content-div */
padding: 10px;
position: relative; /* contain floated or positioned elements */
}


/**
* Toolbar ECM and Filemanager
*/

.largebutton {
background-repeat: repeat-x !important;
border: 1px solid rgba(0,0,0, .32) !important;
box-shadow: 4px 4px 4px rgba(0,0,0, .24);
-moz-box-shadow: 4px 4px 4px rgba(0,0,0, .24);
-webkit-box-shadow: 4px 4px 4px rgba(0,0,0, .24);
padding: 0 4px 0 4px !important;
margin-bottom: 1em;
}

a.toolbarbutton {
height: 30px;
margin-top: 0;
margin-left: 4px;
margin-right: 4px;
}

img.toolbarbutton {
height: 30px;
margin-top: 1px;
}


/**
* RESIZER-BARS
*/

.ui-layout-resizer { /* all 'resizer-bars' */
width: <?php echo (empty($conf->dol_optimize_smallscreen)?'8':'24'); ?>px !important;
}

.ui-layout-resizer-hover {	 /* affects both open and closed states */
}

/* NOTE: It looks best when 'hover' and 'dragging' are set to the same color,
otherwise color shifts while dragging when bar can't keep up with mouse */
/*.ui-layout-resizer-open-hover ,*/ /* hover-color to 'resize' */
.ui-layout-resizer-dragging {	 /* resizer beging 'dragging' */
    background-color: #ddd;
    width: <?php echo (empty($conf->dol_optimize_smallscreen)?'8':'24'); ?>px;
}

.ui-layout-resizer-dragging {	 /* CLONED resizer being dragged */
    border-left:	1px solid #bbb;
    border-right: 1px solid #bbb;
}

/* NOTE: Add a 'dragging-limit' color to provide visual feedback when resizer hits min/max size limits */
.ui-layout-resizer-dragging-limit { /* CLONED resizer at min or max size-limit */
    background-color: #e1a4a4; /* red */
}

.ui-layout-resizer-closed {
    background-color: #ddd;
}

.ui-layout-resizer-closed:hover {
    background-color: #edd;
}

.ui-layout-resizer-sliding {		/* resizer when pane is 'slid open' */
    filter:	alpha(opacity=10);
    opacity: .10; /* show only a slight shadow */
}

.ui-layout-resizer-sliding-hover {	/* sliding resizer - hover */
    filter:	alpha(opacity=100);
    opacity: 1; /* on-hover, show the resizer-bar normally */
}

/* sliding resizer - add 'outside-border' to resizer on-hover */
/* this sample illustrates how to target specific panes and states */
/*.ui-layout-resizer-north-sliding-hover	{ border-bottom-width:	1px; }
.ui-layout-resizer-south-sliding-hover	{ border-top-width:		 1px; }
.ui-layout-resizer-west-sliding-hover	 { border-right-width:	 1px; }
.ui-layout-resizer-east-sliding-hover	 { border-left-width:		1px; }
*/


/**
* TOGGLER-BUTTONS
*/

.ui-layout-toggler {
    <?php if (empty($conf->dol_optimize_smallscreen)) { ?>
        background-color: #ddd;
        border-top: 1px solid #aaa; /* match pane-border */
        border-right: 1px solid #aaa; /* match pane-border */
        border-bottom: 1px solid #aaa; /* match pane-border */
        top: 5px !important;
    <?php } else { ?>
        diplay: none;
    <?php } ?>
}

.ui-layout-toggler-open {
    height: 54px !important;
    width: <?php echo (empty($conf->dol_optimize_smallscreen)?'7':'22'); ?>px !important;
    -moz-border-radius: 0 10px 10px 0;
    -webkit-border-radius: 0 10px 10px 0;
    border-radius: 0 10px 10px 0;
}

.ui-layout-toggler-closed {
    height: <?php echo (empty($conf->dol_optimize_smallscreen)?'54':'2'); ?>px !important;
    width: <?php echo (empty($conf->dol_optimize_smallscreen)?'7':'22'); ?>px !important;
    -moz-border-radius: 0 10px 10px 0;
    -webkit-border-radius: 0 10px 10px 0;
    border-radius: 0 10px 10px 0;
}

.ui-layout-toggler .content {	/* style the text we put INSIDE the togglers */
    color:					#666;
    font-size:<?php print $fontsize; ?>px;
    font-weight:		bold;
    width:					100%;
    padding-bottom: .35ex; /* to 'vertically center' text inside text-span */
}

/* hide the toggler-button when the pane is 'slid open' */
.ui-layout-resizer-sliding	ui-layout-toggler {
    display: none;
}

.ui-layout-north {
    height: <?php print (empty($conf->dol_optimize_smallscreen)?'54':'21'); ?>px !important;
}


/**
* ECM
*/

#containerlayout .ecm-layout-pane { /* all 'panes' */
    background-color: #fff;
    border: 1px solid #bbb;
    /* DO NOT add scrolling (or padding) to 'panes' that have a content-div,
    otherwise you may get double-scrollbars - on the pane AND on the content-div
    */
    overflow: auto;
    padding: 0;
}

/* (scrolling) content-div inside pane allows for fixed header(s) and/or footer(s) */
#containerlayout .ecm-layout-content {
    overflow: auto; /* add scrolling to content-div */
    padding: 10px;
    position: relative; /* contain floated or positioned elements */
}

.ecm-layout-toggler {
    background-color: #ccc;
    border-top: 1px solid #aaa; /* match pane-border */
    border-right: 1px solid #aaa; /* match pane-border */
    border-bottom: 1px solid #aaa; /* match pane-border */
}

.ecm-layout-toggler-open {
    border-radius: 0 10px 10px 0;
    -moz-border-radius: 0 10px 10px 0;
    -webkit-border-radius: 0 10px 10px 0;
    height: 48px !important;
    width: 6px !important;
}

.ecm-layout-toggler-closed {
    height: 48px !important;
    width: 6px !important;
}

.ecm-layout-toggler .content {	/* style the text we put INSIDE the togglers */
    color: #666;
    font-size:<?php print $fontsize; ?>px;
    font-weight: bold;
    width: 100%;
    padding-bottom: .35ex; /* to 'vertically center' text inside text-span */
}

#ecm-layout-west-resizer {
    width: 6px !important;
}

.ecm-layout-resizer	{ /* all 'resizer-bars' */
    border: 1px solid #bbb;
    border-width: 0;
}

.ecm-layout-resizer-closed {
}

.ecm-in-layout-center {
    border-left: 1px !important;
    border-right: 0 !important;
    border-top: 0 !important;
}

.ecm-in-layout-south {
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 0 !important;
    padding: 4px 0 4px 4px !important;
}


/* ============================================================================== */
/* Tabs                                                                           */
/* ============================================================================== */
div.tabs {
	text-align: <?php print $left; ?>;
	margin-top: 15px;
	/* margin-left: 6px; */
	margin-bottom: -1px;
	padding-left: 3px;
	padding-right: 6px;
	clear: both;
    font-weight: normal;
    height: 100%;
}

div.tabsElem {
	margin-top: 1px;
	margin-left: 5px;
}	/* To avoid overlap of tabs when not browser */

div.tabsElem:hover,
div.tabsElem a.tab:hover {
	background-color: <?php print $bgnavleft_hover; ?>;
	color: <?php print $bgnavleft_txt_hover; ?>;
}

div.tabBar {
    background-color: <?php echo $colorbline; ?>;
    border: 1px solid rgba(0,0,0, .16);
    box-shadow: 0 1px 1px rgba(0,0,0, .04);
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0, .04);
    color: var(--colorfline);
    margin-bottom: 10px;
    padding-top: 8px;
    padding-left: <?php echo ($dol_optimize_smallscreen?'4':'8'); ?>px;
    padding-right: <?php echo ($dol_optimize_smallscreen?'4':'8'); ?>px;
    padding-bottom: 8px;
    width: auto;
}

div.tabsAction {
    margin: 20px 0 10px 0;
    padding: 0;
    text-align: <?php print $right; ?>;
    <?php if (getDolGlobalString('FIX_ABSOLUTE_BUTTONS_ACTION_CARD')) { ?>
        position: sticky;
        z-index: 4;
        bottom: 0;
        <?php if (GETPOST("optioncss") == 'print') {	?>
            background-color: #fff !important;
        <?php } else { ?>
            background-color: var(--bgcolor) !important;
        <?php } ?>
    <?php } ?>
}

div.tabactive,
div.tabactive a.tab {
	background-color: var(--colorbacktabactive);
	color: var(--colortextbacktab);
    height: 38px;
}

a.tabTitle {
    color: #666;
    font-weight: normal;
    margin: 0 10px;
    padding: 4px 6px;
    text-decoration: none;
    white-space: nowrap;
}

.imgTabTitle {
    max-height: 14px;
}

a.tab {
    color: var(--colorfline);
    font-weight: normal;
}

a.tab:hover, a.tab:focus {
    background-color: rgba(0,0,0, .10);
    color: <?php print $maincolor; ?>;
}

a.tabimage {
    color: #434956;
    font-family: var(--fontfamilydol);
    text-decoration: none;
    white-space: nowrap;
}

td.tab {
    background-color: <?php echo $colorbline; ?>;
    border: 1px solid rgba(0,0,0, .16) !important;
    box-shadow: 0 1px 1px rgba(0,0,0, .04);
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0, .04);
    margin: 5px;
    padding: 0 .5em;
}

table.notopnoleft td.liste_titre {
    border: 1px solid rgba(0,0,0, .16) !important;
    box-shadow: 0 1px 1px rgba(0,0,0, .04);
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0, .04);
    margin: 0 0 2px 0;
    padding: .8em .5em!important;
}

span.tabspan {
    background-color: #dee7ec;
    color: #434956;
    margin: 0 .2em;
    padding: 0 6px;
    text-decoration: none;
    white-space: nowrap;
}

div.tabBar ul li {
    margin-<?php print $left; ?>: 30px !important;
}

/* Payment Screen : Pointer cursor in the autofill image */
.AutoFillAmount {
	cursor:pointer;
}

div.popuptabset {
    background-color: <?php echo $colorbline; ?>;
    padding: 5px;
    border: 1px solid #e5e5e5;
}

div.popuptab {
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
}

@media only screen and (max-width: 570px)
{

}

/* ============================================================================== */
/* Buttons for actions                                                            */
/* ============================================================================== */

div.tabsAction {
	margin: 20px 0em 30px 0em;
	padding: 0em 0em;
	text-align: right;
}
div.tabsActionNoBottom {
	margin-bottom: 0px;
}
div.tabsAction > a {
	margin-bottom: 16px !important;
}

a.tabTitle {
	color: rgba(0,0,0,0.4) !important;
	text-shadow:1px 1px 1px #ffffff;
	font-family: var(--fontfamilydol);
	font-weight: normal !important;
	padding: 4px 6px 2px 0px;
	margin-<?php print $right; ?>: 10px;
	text-decoration: none;
	white-space: nowrap;
}
.tabTitleText {
	display: none;
}
.imgTabTitle {
	max-height: 14px;
}
div.tabs div.tabsElem:first-of-type a.tab {
	margin-left: 0px !important;
}

a.tabunactive {
	color: rgb(<?php echo $colortextlink; ?>) !important;
}
a.tab:link, a.tab:visited, a.tab:hover, a.tab#active {
	font-family: var(--fontfamilydol);
	padding: 12px 14px 13px;
	text-decoration: none;
	white-space: nowrap;

	background-image: none !important;
}

.tabactive, a.tab#active {
    color: var(--colortextbacktab) !important;
    background: var(--colorbacktabcard1) !important;
	margin: 0 0.2em 0 0.2em !important;
	text-decoration: none;

	border: 1px solid rgba(0,0,0, .16);
	border-bottom: none;
}
.tabunactive, a.tab#unactive {
    border: 1px solid rgba(0,0,0, .16);
	border-bottom: 0px !important;
	height: 38px;
}
a.tabimage {
	color: #434956;
	font-family: var(--fontfamilydol);
	text-decoration: none;
	white-space: nowrap;
}

td.tab {
	background: #dee7ec;
}

span.tabspan {
	background: #dee7ec;
	color: #434956;
	font-family: var(--fontfamilydol);
	padding: 0px 6px;
	margin: 0em 0.2em;
	text-decoration: none;
	white-space: nowrap;
	-webkit-border-radius:4px 4px 0px 0px;
	border-radius:4px 4px 0px 0px;

	border-<?php print $right; ?>: 1px solid #555555;
	border-<?php print $left; ?>: 1px solid #D8D8D8;
	border-top: 1px solid #D8D8D8;
}

/* ============================================================================== */
/* Buttons for actions                                                            */
/* ============================================================================== */
<?php include dol_buildpath($path.'/theme/'.$theme.'/btn.inc.php', 0); ?>

/* ============================================================================== */
/* Tables                                                                         */
/* ============================================================================== */
.allwidth {
    width: 100%;
}

#undertopmenu {
    background-repeat: repeat-x;
    margin-top: <?php echo ($dol_hide_topmenu?'6':'0'); ?>px;
}

.paddingrightonly {
    border-collapse: collapse;
    border: 0;
    margin-left: 0;
    spacing-left: 0;
    padding-<?php print $left; ?>: 0;
    padding-<?php print $right; ?>: 4px;
}

.nocellnopadd {
    list-style-type: none;
    margin: 0 !important;
    padding: 0 !important;
}

.noborderspacing {
    border-spacing: 0;
}
tr.nocellnopadd td.nobordernopadding, tr.nocellnopadd td.nocellnopadd
{
    border: 0px;
}

.unsetcolor {
    color: unset !important;
}

.smallpaddingimp {
    padding: 4px !important;
    padding-left: 7px !important;
    padding-right: 7px !important;
}
input.button[name="upload"] {
    padding: 5px !important;
    font-size: 0.9em;
}
input.button.smallpaddingimp, input.buttonreset.smallpaddingimp {
    font-size: 0.8em;
}
input.buttonreset {
    margin-top: 3px;
    margin-bottom: 3px;
    padding: 8px 15px;
    text-decoration: underline;
    color: rgb(<?php echo $colortextlink; ?>);
    background-color: transparent;
    cursor: pointer;
}
.nopaddingleft {
    padding-<?php print $left; ?>: 0px;
}
div.tabs.nopaddingleft {
    padding-<?php print $left; ?>: 0px;
}
.nopaddingright {
    padding-<?php print $right; ?>: 0px;
}
.nopaddingtopimp {
    padding-top: 0px !important;
}
.nopaddingbottomimp {
    padding-bottom: 0px !important;
}

.notopnoleft {
    border: 0;
    border-collapse: collapse;
    margin-bottom: 10px;
    padding-top: 0;
    padding-<?php print $left; ?>: 0;
    padding-<?php print $right; ?>: 16px;
    padding-bottom: 4px;
}

.notopnoleftnoright {
    border: 0;
    border-collapse: collapse;
    margin: 0;
    padding-top: 0;
    padding-left: 0;
    padding-right: 0;
    padding-bottom: 4px;
}

table.border {
    border: 1px solid #f2f2f2;
    border-collapse: collapse;
}

table.border td {
	border: 1px solid var(--colortopbordertitle1);
    border-collapse: collapse;
    padding: 5px 2px 5px 2px;
    vertical-align: middle;
}

table.border td img { margin: 0 .1em; }

td.border {
    border: 1px solid #000;
}

/* Main boxes */

table.noborder,
table.formdoc,
div.noborder {
    border: 1px solid rgba(0,0,0, .16);
    border-collapse: separate !important;
    border-spacing: 0;
    box-shadow: 0 1px 1px rgba(0,0,0, .08);
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0, .08);
    margin: 0 0 2px 0;
    /*padding: 1px 2px 1px 2px;*/
    width: 100%;
}

table.noborder[summary="list_of_modules"] tr.oddeven { line-height: 2.2em; }

table.noborder tr, div.noborder form {
    line-height: 1.7em;
}

/* boxes padding */
/* table titles main page */
table.noborder th { padding: 3px; }

table.noborder th:first-child { padding-<?php print $left; ?>: 10px; }

table.noborder th:last-child { padding-<?php print $right; ?>: 10px; }

/* table content all pages */
table.noborder td, div.noborder form, div.noborder form div, table.tableforservicepart1 td, table.tableforservicepart2 td {
    padding: 4px 6px 4px 6px;			/* t r b l */
    vertical-align: unset;
}

table.noborder td:first-child { padding-<?php print $left; ?>: 10px !important; }

table.noborder td:last-child, div.noborder form div:last-child { padding-<?php print $right; ?>: 10px; }

/* titles others pages */
table.noborder .liste_titre td { padding: 3px; }

table.noborder .liste_titre td:first-child { padding-<?php print $left; ?>: 10px; }

table.noborder .liste_titre td:last-child { padding-<?php print $right; ?>: 10px; }

form#searchFormList div.liste_titre { padding: 3px 10px; }

.liste_titre_filter {
    background: <?php print $colorbtitle; ?> !important;
}

/* table liste -bank- e-mailing */
table.liste .liste_titre td, table.liste .liste_total td {
}

table.liste .liste_titre th { padding: 5px; }


/* templates avec form au lieu de table */
div.noborder form div { padding: 3px; }

div.noborder form>div:first-child { padding-<?php print $left; ?>: 10px; }

div.noborder form div:last-child { padding-<?php print $right; ?>: 10px; }

table.nobordernopadding td img {
    margin-<?php print $left; ?>: .2em;
}

.flat+img {
    margin-<?php print $left; ?>: .4em;
}

table.nobordernopadding {
    border-collapse: collapse !important;
    border: 0;
}
table.nobordernopadding tr {
    border: 0 !important;
    padding: 0;
}

table.nobordernopadding tr td {
    border: 0 !important;
    padding: 0 3px 0 0;
    vertical-align: unset !important;
}
table.border tr td table.nobordernopadding tr td {
    padding-top: 0;
    padding-bottom: 0;
}
td.borderright {
    border: none;	/* to erase value for table.nobordernopadding td */
    border-right-width: 1px !important;
    border-right-color: #BBB !important;
    border-right-style: solid !important;
}


/* For lists */
table.liste {
    border: 1px solid rgba(0,0,0, .42);
    border-collapse: collapse;
    margin-bottom: 2px;
    margin-top: 2px;
    width: 100%;
}

table.liste .oddeven td { padding: 2px 5px; }

table .liste_titre td { padding: 2px; }

table.liste td a img {
	vertical-align: middle;
	max-height: <?php print $tblImageMaxHeight; ?>px;
}


.tagtable, .table-border { display: table; }
.tagtr, .table-border-row	{ display: table-row; }
.tagtd, .table-border-col, .table-key-border-col, .table-val-border-col { display: table-cell; }
.confirmquestions .tagtr .tagtd:not(:first-child)  { padding-left: 10px; }


tr.liste_titre,
tr.liste_titre_sel,
form.liste_titre,
form.liste_titre_sel {
    height: 20px !important;
}

div.liste_titre {
    padding: 6px;
    margin-bottom: 12px;
}

div.liste_titre,
tr.liste_titre,
tr.liste_titre_sel,
form.liste_titre,
form.liste_titre_sel {
    background-color: <?php print $colorbtitle; ?>;
    color: <?php print $colorftitle; ?>;
    font-family: var(--fontfamilydol);
    font-size: 1em;
    font-weight: normal;
    line-height: 1em;
    text-align: <?php echo $left; ?>;
    white-space: normal;
}

div.liste_titre a,
tr.liste_titre a,
tr.liste_titre th a,
tr.liste_titre_sel a,
th.liste_titre_sel a,
form.liste_titre a,
form.liste_titre_sel a {
    color: <?php print $colorftitle; ?> !important;
}

.liste_titre_sel { font-weight: bold!important; }

tr.liste_titre th,
th.liste_titre,
tr.liste_titre td,
td.liste_titre,
form.liste_titre div,
div.liste_titre {
    font-family: var(--fontfamilydol);
    font-weight: normal;
    /* border-bottom: 1px solid #FDFFFF;*/
    white-space: normal;
    padding-left: 5px;
}

table td.liste_titre a:link,
table td.liste_titre a:visited,
table td.liste_titre a:active { color: #eee; }

table td.liste_titre a:hover { color: <?php print $maincolor; ?>; }

table.noborder tr td a:link,
table.noborder tr td a:visited,
table.noborder tr td a:active,
table.noborder tr th a:link,
table.noborder tr th a:visited,
table.noborder tr th a:active {
    color: var(--colorfline);
    font-family: <?php echo $fontlisted; ?>;
}

table.noborder tr td a:hover { color: <?php echo $colorfline_hover; ?>; }

table.noborder tr td a.button,
table.noborder tr td a.button:hover { color: #fff; }


.liste tr.liste_titre:nth-child(3) {
    background-color: <?php print $colorbtitle; ?>;
}

tr.liste_titre:nth-child(3) {
    background-color: <?php print $colorbtitle; ?>;
}

tr.liste_titre_sel th,
th.liste_titre_sel,
tr.liste_titre_sel td,
td.liste_titre_sel,
form.liste_titre_sel div {
 /*   background-color: #333;
    color: #f7f7f7;
    font-weight: normal;
    text-decoration: none;
    white-space: normal;	*/
}

th.liste_titre>img,
th.liste_titre_sel>img {
    padding-<?php print $left; ?>: 5px;
}

input.liste_titre {
    background: transparent;
    border: 0;
    margin: inherit;
    padding: 0;
}

tr.liste_total,
form.liste_total {
    background-color: <?php echo $colorbline; ?>;
}

tr.liste_total td,
form.liste_total div {
    height: 20px;
    border-top: 1px solid rgba(0,0,0, .42);
    color: <?php echo $maincolor; ?>;
    font-weight: normal;
    white-space: normal;
    padding: 0 5px 0 5px;
}

tr.liste_total td[align=right],
form.liste_total td[align=right] {
    color: #3c6;
    font-weight: bold;
}

/* Disable shadows */
.noshadow,
div.tabBar .noborder {
    box-shadow: 0 0 0 rgba(0,0,0, .24) !important;
    -moz-box-shadow: 0 0 0 rgba(0,0,0, .24) !important;
    -webkit-box-shadow: 0 0 0 rgba(0,0,0, .24) !important;
}
div.tabBar div.border .table-border-row, div.tabBar div.border .table-key-border-col, div.tabBar .table-val-border-col {
	vertical-align: middle;
}
div .tdtop {
    vertical-align: top !important;
    padding-top: 5px !important;
    padding-bottom: 0px !important;
}

#tablelines tr.liste_titre td, .paymenttable tr.liste_titre td, .margintable tr.liste_titre td, .tableforservicepart1 tr.liste_titre td {
    border-bottom: 1px solid #AAA !important;
}
#tablelines tr td {
    height: unset;
}

/*
 *  Boxes
 */

.box {
    <?php if (!getDolGlobalString('FIX_STICKY_HEADER_CARD')) { ?>
        overflow-x: auto;
    <?php } ?>
    min-height: 40px;
    padding-right: 0px;
    padding-left: 0px;
    /*padding-bottom: 25px;*/
    padding-bottom: 10px;
}
.ficheaddleft div.boxstats, .ficheaddright div.boxstats {
    border: none;
}
.boxstatsborder {
    /* border: 1px solid #CCC !important; */
}
.boxstats, .boxstats130 {
    display: inline-block;
    margin-left: 8px;
    margin-right: 8px;
    margin-top: 5px;
    margin-bottom: 5px;
    text-align: center;
	<?php if(getDolGlobalString('OBLYON_INFOXBOX_BACKGROUND')) { ?>
		background: <?php print getDolGlobalString('OBLYON_INFOXBOX_BACKGROUND'); ?> !important;
	<?php } else { ?>
    background: #fcfcfc;
	<?php } ?>
	border-left: 6px solid var(--colorboxstatsborder);
    border-radius: 0px;
}
.boxstats, .boxstats130, .boxstatscontent {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.boxstats {
    width: 100%;
    height: 59px;
    /* padding: 3px; */
}
.boxstats {
    padding-left: 3px;
    padding-right: 3px;
    padding-top: 2px;
    padding-bottom: 2px;
    width: 118px;
}
.tabBar .fichehalfright .boxstats {
    padding-top: 8px;
    padding-bottom: 4px;
}
.boxstatscontent {
    padding: 3px;
}
.boxstatsempty {
    width: 121px;
    padding-left: 3px;
    padding-right: 3px;
    margin-left: 8px;
    margin-right: 8px;
}
.boxstats150empty {
    width: 158px;
    padding-left: 3px;
    padding-right: 3px;
    margin-left: 8px;
    margin-right: 8px;
}

@media only screen and (max-width: 767px)
{
    .boxstats, .boxstats130 {
        margin: 3px;
        border: 1px solid rgba(0,0,0, .24);
        box-shadow: none;
        background: #ddd;
    }
    .thumbstat {
        flex: 1 1 110px;
    }
    .thumbstat150 {
        flex: 1 1 110px;
    }
    .dashboardlineindicator {
        float: left;
        padding-left: 5px;
    }
    .boxstats130 {
        width: 148px;
    }
    .boxstats {
        width: 100px;
    }
}
.boxstats:hover {
    box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.20);
}
span.boxstatstext {
    opacity: 0.7;
    line-height: 18px;
	color: var(--colortextlink);
}
span.boxstatstext img, a.dashboardlineindicatorlate img {
    border: 0;
}
a img {
    border: 0;
}
.boxstatsindicator.thumbstat150 {	/* If we remove this, box position is ko on ipad */
    display: inline-flex;
}
span.boxstatsindicator {
    font-size: 130%;
    font-weight: normal;
    line-height: 29px;
}
span.dashboardlineindicator, span.dashboardlineindicatorlate {
    font-size: 130%;
    font-weight: normal;
}
a.dashboardlineindicatorlate:hover {
    text-decoration: none;
}
.dashboardlineindicatorlate img {
    width: 16px;
}
span.dashboardlineok {
    color: #008800;
}
span.dashboardlineko {
    color: #FFF;
    /*color: #8c4446 ! important;
	padding-left: 1px;*/

    font-size: 80%;
}
.dashboardlinelatecoin {
    float: right;
    position: relative;
    text-align: right;
    top: -27px;
    right: 2px;
    padding: 0px 5px 0px 5px;
    border-radius: .25em;

    background-color: #9f4705;
}
.imglatecoin {
    padding: 1px 3px 1px 1px;
    margin-left: 4px;
    margin-right: 2px;
    background-color: #8c4446;
    color: #FFFFFF ! important;
    border-radius: .25em;
    display: inline-block;
    vertical-align: middle;
}
.boxtable {
    margin-bottom: 8px !important;
    border-bottom-width: 1px;

	border-top: <?php echo $borderwidth ?>px solid var(--colortopbordertitle1);
    /* border-top: 2px solid rgb(<?php echo $colorbackhmenu1 ?>) !important; */
}
table.noborder.boxtable tr td {
    height: unset;
}
.boxtablenotop {
    border-top-width: 0 !important;
}
.boxtablenobottom {
    border-bottom-width: 0 !important;
}
.boxtable .fichehalfright, .boxtable .fichehalfleft {
    min-width: 275px;	/* increasing this, make chart on box not side by side on laptops */
}
.tdboxstats {
    text-align: center;
}
.boxworkingboard .tdboxstats {
    padding-left: 1px !important;
    padding-right: 1px !important;
}
a.valignmiddle.dashboardlineindicator {
    line-height: 30px;
}

tr.box_titre {
    height: 26px;

    /* TO MATCH BOOTSTRAP */
    /*background: #ddd;
    color: #000 !important;*/

    /* TO MATCH ELDY */
    background: <?php print $colorbtitle; ?>;
    color: <?php print $colorftitle; ?>;
    font-family: var(--fontfamilydol), sans-serif;
    font-weight: <?php echo $useboldtitle?'bold':'normal'; ?>;
    border-bottom: 1px solid #FDFFFF;
    white-space: nowrap;
}

tr.box_titre td.boxclose {
    width: 90px;
}
img.boxhandle, img.boxclose {
    padding-left: 5px;
}

.formboxfilter {
    vertical-align: middle;
    margin-bottom: 6px;
}
.formboxfilter input[type=image]
{
    top: 5px;
    position: relative;
}
.boxfilter {
    margin-bottom: 2px;
    margin-right: 1px;
}
.prod_entry_mode_free, .prod_entry_mode_predef {
    height: 26px !important;
    vertical-align: middle;
}

.modulebuilderbox {
    border: 1px solid #888;
    padding: 16px;
}

/*
*  External web site
*/

.framecontent {
    width: 100%;
    height: 100%;
}

.framecontent iframe {
    width: 100%;
    height: 100%;
}



/*
*  Other
*/
.opened-dash-board-wrap {
    margin-bottom: 25px;
}

div.boximport {
    min-height: unset;
}

.product_line_stock_ok { color: #002000; }
.product_line_stock_too_low { color: #884400; }

.fieldrequired { color: var(--colorfline); font-weight: bold; }

.widthpictotitle { width: 40px; font-size: 1.5em; text-align: <?php echo $left; ?>; }

.dolgraphtitle { margin-top: 6px; margin-bottom: 4px; }
.dolgraphtitlecssboxes { /* margin: 0px; */ }
.legendColorBox, .legendLabel { border: none !important; }
div.dolgraph div.legend, div.dolgraph div.legend div { background-color: rgba(255,255,255,0) !important; }
div.dolgraph div.legend table tbody tr { height: auto; }
td.legendColorBox { padding: 2px 2px 2px 0 !important; }
td.legendLabel { padding: 2px 2px 2px 0 !important; }

label.radioprivate {
    white-space: nowrap;
}

.photo {
    border: 0px;
}
.photowithmargin {
    margin-bottom: 2px;
    margin-top: 2px;
}
.photowithborder {
border: 1px solid #f0f0f0;
}
.photointoolitp {
    margin-top: 8px;
    float: left;
    /*text-align: center; */
}
.photodelete {
    margin-top: 6px !important;
}

.nographyet
{
    content:url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/nographyet.svg',1) ?>);
    display: inline-block;
    opacity: 0.1;
    background-repeat: no-repeat;
}
.nographyettext
{
    opacity: 0.5;
}

table.notopnoleftnoright div.titre {
    font-size: 13px;
    text-transform: uppercase;
}

div.titre {
	color: <?php print $colorStitle; ?>;
    font-weight: bold;
    font-size: 1.1em;
    text-decoration: none;
    padding-top: 5px;
    padding-bottom: 5px;
}

table.table-fiche-title .col-title div.titre{
    line-height: 40px;
}
table.table-fiche-title {
    margin-bottom: 5px;
}


/*div.backgreypublicpayment { background-color: #f0f0f0; padding: 20px; border-bottom: 1px solid #ddd; }	*/
.backgreypublicpayment a { color: #222 !important; }
.poweredbypublicpayment {
    float: right;
    top: 8px;
    right: 8px;
    position: absolute;
    font-size: 0.8em;
    color: #222;
    opacity: 0.3;
}

#dolpaymenttable { min-width: 320px; font-size: 16px; }	/* Width must have min to make stripe input area visible */
#tablepublicpayment { border: 1px solid #CCCCCC !important; width: 100%; padding: 20px; }
/* #tablepublicpayment .CTableRow1  { background-color: #F0F0F0 !important; }	*/
#tablepublicpayment tr.liste_total { border-bottom: 1px solid #CCCCCC !important; }
#tablepublicpayment tr.liste_total td { border-top: none; }

.titlepublicpayment {
	font-size: 24px;
}

td.CTableRow1 {
	padding: 4px 1px 4px 4px; /* t r b l */
}

td.CTableRow2 {
	padding: 4px 4px 4px 12px; /* t r b l */
}

div#login_left, div#login_right {
    min-width: 150px !important;
    max-width: 200px !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
	vertical-align: middle;
}
div.login_block {
    height: 40px !important;
}

.divmainbodylarge { margin-left: 40px; margin-right: 40px; }
#divsubscribe { max-width: 900px; }
#tablesubscribe { width: 100%; }

div#card-element {
    border: 1px solid #ccc;
}
div#card-errors {
    color: #fa755a;
    text-align: center;
    padding-top: 3px;
    max-width: 320px;
}

/*
*   Liens Payes/Non payes
*/

a.normal:link { font-weight: normal }
a.normal:visited { font-weight: normal }
a.normal:active { font-weight: normal }
a.normal:hover { font-weight: normal }

a.impayee:link { font-weight: bold; color: #550000; }
a.impayee:visited { font-weight: bold; color: #550000; }
a.impayee:active { font-weight: bold; color: #550000; }
a.impayee:hover { font-weight: bold; color: #550000; }



/*------------------------------------*\
#Form confirmation
\*------------------------------------*/

/**
* When Ajax JQuery is used
*/

.ui-dialog-titlebar {
}

.ui-dialog-content {
font-size: <?php print $fontsize; ?>px !important;
}


/**
* When HTML is used
*/

table.valid {
    background-color: #f07b6e;
    border: 1px solid #e0796e;
    box-shadow: 0 1px 1px rgba(0,0,0, .04);
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0, .04);
    margin: .5em 0em;
    padding: 1.2em 1.5em;
}

table.valid img { vertical-align: sub; }

.validtitre { font-weight: bold; }

/*------------------------------------*\
#Tooltips
\*------------------------------------*/
/* For tooltip using dialog */
.ui-dialog.highlight.ui-widget.ui-widget-content.ui-front {
    z-index: 97;
}
div.ui-tooltip {
    max-width: <?php print dol_size(600,'width'); ?>px !important;
}
.mytooltip {
    width: <?php print dol_size(450,'width'); ?>px;
    border-top: solid 1px #BBBBBB;
    border-<?php print $left; ?>: solid 1px #BBBBBB;
    border-<?php print $right; ?>: solid 1px #444444;
    border-bottom: solid 1px #444444;
    padding: 5px 20px;
    border-radius: 0;
    box-shadow: 0 0 4px grey;
    margin: 2px;
    font-stretch: condensed;
}

/*------------------------------------*\
#Calc Module
\*------------------------------------*/

#imageCalc {

}

.login_block_elem img.calculator-trigger,
.login_block_other img.calculator-trigger {
    display: block;
    margin: 0 !important;
    padding: 12px !important;
}

.calculator-popup {
    top: 56px !important;
    width: 260px !important;
}


/*------------------------------------*\
#BreadCrumb Module
\*------------------------------------*/

.breadCrumb {
    border: none !important;
    margin-bottom: 10px;
    /* margin-left: 20px;
    margin-right: 15px;*/
}

/* ============================================================================== */
/* Calendar date picker                                                           */
/* ============================================================================== */

.ui-datepicker-calendar .ui-state-default, .ui-datepicker-calendar .ui-widget-content .ui-state-default,
.ui-datepicker-calendar .ui-widget-header .ui-state-default, .ui-datepicker-calendar .ui-button,
html .ui-datepicker-calendar .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active
{
    border: unset;
}

div#ui-datepicker-div {
    width: 300px;
    box-shadow: 2px 5px 15px #aaa;
    border: unset;
    padding-left: 5px;
    padding-right: 5px;
    padding-top: 5px;
    z-index: 102 !important;    /* 102 is the minimum value because the formcofirm popup form is set to 101, so if we use the date picker on formcofirm... */
}
.ui-datepicker .ui-datepicker table {
    font-size: unset;
}
.ui-datepicker .ui-widget-header {
    border: unset;
    background: unset;
}

.ui-datepicker .ui-datepicker-header {
	background: var(--bgcolor) !important;
	border: none;
}

.ui-state-highlight, .ui-widget-content .ui-state-highlight
{
    color: var(--colorfdatedefault) !important;
	font-weight: bolder !important;
}

img.datecallink { padding-left: 2px !important; padding-right: 2px !important; }

select.ui-datepicker-year {
    margin-left: 2px !important;
}
.ui-datepicker-trigger {
	filter: invert(<?php print $invertratiofilter; ?>%);
    vertical-align: middle;
    cursor: pointer;
    padding-left: 2px;
    padding-right: 2px;
}

.bodyline {
    -webkit-border-radius: 4px;
    border-radius: 4px;
    border: 1px #E4ECEC outset;
    padding: 0px;
    margin-bottom: 5px;
}

table.dp {
    width: 180px;
    background-color: <?php echo $colorbline; ?>;
    /*border-top: solid 2px #f4f4f4;
    border-<?php print $left; ?>: solid 2px #f4f4f4;
    border-<?php print $right; ?>: solid 1px #222222;
    border-bottom: solid 1px #222222; */
    padding: 0px;
    border-spacing: 0px;
    border-collapse: collapse;
}
.dp td, .tpHour td, .tpMinute td{padding:2px; font-size:10px;}
    /* Barre titre */
    .dpHead,.tpHead,.tpHour td:Hover .tpHead{
    font-weight:bold;
    background-color: #888;
    color:white;
    font-size:11px;
    cursor:auto;
}
/* Barre navigation */
.dpButtons,.tpButtons {
    text-align:center;
    background-color: #888;
    color:#FFFFFF;
    font-weight:bold;
    cursor:pointer;
}
.dpButtons:Active,.tpButtons:Active{border: 1px outset black;}
.dpDayNames td,.dpExplanation {background-color:#D9DBE1; font-weight:bold; text-align:center; font-size:11px;}
.dpExplanation{ font-weight:normal; font-size:11px;}
.dpWeek td{text-align:center}

.dpToday,.dpReg,.dpSelected{
    cursor:pointer;
}
.dpToday{font-weight:bold; color:black; background-color:#f4f4f4;}
.dpReg:Hover,.dpToday:Hover{background-color:black;color:white}

/* Jour courant */
.dpSelected{background-color:#0B63A2;color:white;font-weight:bold; }

.tpHour{border-top:1px solid #f4f4f4; border-right:1px solid #f4f4f4;}
.tpHour td {border-left:1px solid #f4f4f4; border-bottom:1px solid #f4f4f4; cursor:pointer;}
.tpHour td:Hover {background-color:black;color:white;}

.tpMinute {margin-top:5px;}
.tpMinute td:Hover {background-color:black; color:white; }
.tpMinute td {background-color:#D9DBE1; text-align:center; cursor:pointer;}

/* Bouton X fermer */
.dpInvisibleButtons
{
    border-style:none;
    background-color:transparent;
    padding:0px;
    font-size: 0.85em;
    border-width:0px;
    vertical-align:middle;
    cursor: pointer;
}
.datenowlink
{
    color: rgb(<?php echo $colortextlink; ?>);
}

.categtextwhite, .treeview .categtextwhite.hover {
    color: #fff !important;
}
.categtextblack {
    color: #000 !important;
}


/* ============================================================================== */
/*  Show/Hide                                                                     */
/* ============================================================================== */

div.visible {
	display: block;
}

div.hidden, div.hiddenforpopup, header.hidden, tr.hidden, td.hidden,
img.hidden, span.hidden, br.hidden, div.showifmore {
	display: none;
}
.unvisible {
	visibility: hidden;
}
tr.visible {
	display: block;
}

/* ============================================================================== */
/*  Module website                                                                */
/* ============================================================================== */

.previewnotyetavailable {
    opacity: 0.5;
}

.websiteformtoolbar {
    position: sticky;
    top: <?php echo empty($dol_hide_topmenu) ? ($disableimages ? '32px' : '52px') : '0'; ?>;
    z-index: 1002;	/* Dolibarr menu is 1001, Website menu is 1002 */
}

.exampleapachesetup {
    overflow-y: auto;
    height: 100px;
    font-size: 0.8em;
    border: 1px solid #aaa;
}

span[phptag] {
    background: #ddd; border: 1px solid #ccc; border-radius: 4px;
}

.nobordertransp {
    border: 0px;
    background-color: transparent;
    background-image: none;
}
.bordertransp {
    background-color: transparent;
    background-image: none;
    border: none;
    font-weight: normal;
}
.websitebar .button.bordertransp, .websitebar .fa-plus-circle.btnTitle-icon {
    color: unset;
    text-decoration: unset !important;
    margin: 0px 4px 0px 4px  !important
}

.websitebar {
    border-bottom: 1px solid #ccc;
    background: #e6e6e6;
    display: inline-block;
    z-index: 1000;
}
.centpercent.websitebar {
    width: calc(100% - 10px);
    padding: 5px 5px 5px 5px;
    font-size: 0.94em;
}
.websitebar .buttonDelete, .websitebar .button {
    text-shadow: none;
}
.websitebar .button, .websitebar .buttonDelete
{
    padding: 4px 5px 4px 5px !important;
    margin: 2px 4px 2px 4px  !important;
    /*	line-height: normal; */
    background: #f5f5f5 !important;
    border: 1px solid #ccc !important;
}
.websiteselection {
    /* display: inline-block; */
    padding-<?php echo $right; ?>: 10px;
    vertical-align: middle;
    line-height: 2.2em;
}
.websiteselectionsection {
    font-size: 0.85em;
}
.websiteselection span {
    vertical-align: middle;
}
.websitetools {
    float: right;
}
.websiteselection, .websitetools {
    /* margin-top: 3px;
    padding-top: 3px;
    padding-bottom: 3px; */
}
.websiteinputurl {
    display: inline-block;
    vertical-align: middle;
    line-height: 26px;
}
.websiteiframenoborder {
    border: 0px;
}
span.websiteselection span.select2.select2-container.select2-container--default {
    margin: 0 0 0 4px;
}
span.websitebuttonsitepreview, a.websitebuttonsitepreview {
    vertical-align: middle;
}
span.websitebuttonsitepreview img, a.websitebuttonsitepreview img {
    width: 26px;
    display: inline-block;
}
span.websitebuttonsitepreviewdisabled img, a.websitebuttonsitepreviewdisabled img {
    opacity: 0.2;
}
.websitehelp {
    vertical-align: middle;
    float: right;
    padding-top: 8px;
}
.websiteselectionsection {
    border-left: 1px solid #bbb;
    border-right: 1px solid #bbb;
    margin-left: 0px;
    padding-left: 8px;
    margin-right: 5px;
}
.websitebar input#previewpageurl {
    line-height: 1em;
}

.websitebar input.bordertransp {
    line-height: normal !important;
}

#divbodywebsite section p {
    margin: unset;
}

/* ============================================================================== */
/*  Module agenda                                                                 */
/* ============================================================================== */

.dayevent .tagtr:first-of-type {
    height: 24px;
}
.agendacell {
	height: 60px;
}
table.cal_month	{
	border-spacing: 0px;
}
table.cal_month td:first-child  {
	border-left: 0px;
}
table.cal_month td:last-child {
	border-right: 0px;
}
.cal_current_month {
	border-top: 0;
	border-left: solid 1px var(--colortopbordertitle1);
	border-right: 0;
	border-bottom: solid 1px var(--colortopbordertitle1);
}
.cal_current_month_peruserleft {
	border-top: 0;
	border-left: solid 2px #6C7C7B;
	border-right: 0;
	border-bottom: solid 1px var(--colortopbordertitle1);
}
.cal_current_month_oneday {
	border-right: solid 1px var(--colortopbordertitle1);
}
.cal_other_month {
	border-top: 0;
	border-left: solid 1px #C0C0C0;
	border-right: 0;
	border-bottom: solid 1px #C0C0C0;
}
.cal_other_month_peruserleft {
	border-top: 0;
	border-left: solid 2px #6C7C7B !important;
	border-right: 0;
}
.cal_current_month_right {
	border-right: solid 1px var(--colortopbordertitle1);
}
.cal_other_month_right {
	border-right: solid 1px #C0C0C0;
}
.cal_other_month {
	background: <?php echo $colorbline; ?>;
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_past_month	{
	background: <?php echo $colorbline; ?>;
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_current_month {
	background: var(--colorbacktitle1);
	border-left: solid 1px var(--colortopbordertitle1);
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_current_month_peruserleft {
	background: var(--colorbacktitle1);
	border-left: solid 2px #6C7C7B;
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_today {
	background: var(--inputbackgroundcolor);
	border-left: solid 1px var(--colortopbordertitle1);
	border-bottom: solid 1px var(--colortopbordertitle1);
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_today_peruser {
	background: var(--inputbackgroundcolor);
	border-right: solid 1px var(--colortopbordertitle1);
	border-bottom: solid 1px var(--colortopbordertitle1);
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_today_peruser_peruserleft {
	background: var(--inputbackgroundcolor);;
	border-left: solid 2px #6C7C7B;
	border-right: solid 1px var(--colortopbordertitle1);
	border-bottom: solid 1px var(--colortopbordertitle1);
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 1px;
	padding-top: 0px;
	padding-bottom: 0px;
}
.cal_past          { }
.cal_peruser {
	padding: 0px;
}
.cal_impair {
	background: var(--colorbacklinepair2);
}
.cal_today_peruser_impair {
	background: var(--colorbacklinepair2);
}
.peruser_busy {
	background: var(--inputbackgroundcolor);
}
.peruser_notbusy {
	background: var(--inputbackgroundcolor);
	opacity: 0.5;
}
table.cal_event	{
	border: none;
	border-collapse: collapse;
	margin-bottom: 1px;
	-webkit-border-radius: 3px;
	border-radius: 3px; min-height: 20px;
	background: <?php print $maincolor; ?> !important;
}
table.cal_event td {
	border: none;
	padding-<?php print $left; ?>: 2px;
	padding-<?php print $right; ?>: 2px;
	padding-top: 0px;
	padding-bottom: 0px;
}
table.cal_event td.cal_event {
	padding: 4px 4px !important;
}
table.cal_event td.cal_event_right {
	padding: 4px 4px !important;
}
.cal_event {
	font-size: 1em;
}
.cal_event a:link {
	color: #111111;
	font-weight: normal !important;
}
.cal_event a:visited {
	color: #111111;
	font-weight: normal !important;
}
.cal_event a:active {
	color: #111111;
	font-weight: normal !important;
}
.cal_event_busy a:hover {
	color: #111111;
	font-weight: normal !important;
	color:rgba(255,255,255,.75);
}
.cal_event_busy      { }
.cal_peruserviewname {
	max-width: 140px; height: 22px;
}

.topmenuimage {
    background-size: 28px auto;
}

.paginationafterarrows > .button_search > .fa.fa-search{
    color: rgb(<?php print $colortext; ?>);
}

/* ============================================================================== */
/*  Ajax - Combo list for autocompletion                                          */
/* ============================================================================== */

.ui-widget-content {
    border: solid 1px rgba(0,0,0,.3);
    background: var(--colorbackbody) !important;
    color: var(--colortext) !important;
}

.ui-widget-content a {
    color: var(--colortext) !important;
}

/*.ui-widget-header {
	background: var(--colorbacktitle);
}*/

.ui-autocomplete-loading {
    background: white url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/working.gif', 1) ?>) right center no-repeat;
}
.ui-autocomplete {
    position:absolute;
    width:auto;
    font-size: 1.0em;
    background-color: var(--inputbackgroundcolor);
    border:1px solid #888;
    margin:0px;
    /*	       padding:0px; This make combo crazy */
}
.ui-autocomplete ul {
    list-style-type:none;
    margin:0px;
    padding:0px;
}
.ui-autocomplete ul li.selected {
    background-color: var(--inputbackgroundcolor);
}
.ui-autocomplete ul li {
    list-style-type:none;
    display:block;
    margin:0;
    padding:2px;
    height:18px;
    cursor:pointer;
}

/* ============================================================================== */
/*  jQuery - jeditable for inline edit                                            */
/* ============================================================================== */

.editkey_textarea, .editkey_ckeditor, .editkey_string, .editkey_email, .editkey_numeric, .editkey_select, .editkey_autocomplete {
    background: url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/edit.png', 1) ?>) right top no-repeat;
    cursor: pointer;
    margin-right: 3px;
    margin-top: 3px;
}

.editkey_datepicker {
    background: url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/calendar.png', 1) ?>) right center no-repeat;
    margin-right: 3px;
    cursor: pointer;
    margin-right: 3px;
    margin-top: 3px;
}

.editval_textarea.active:hover, .editval_ckeditor.active:hover, .editval_string.active:hover, .editval_email.active:hover, .editval_numeric.active:hover, .editval_select.active:hover, .editval_autocomplete.active:hover, .editval_datepicker.active:hover {
    background: white;
    cursor: pointer;
}

.viewval_textarea.active:hover, .viewval_ckeditor.active:hover, .viewval_string.active:hover, .viewval_email.active:hover, .viewval_numeric.active:hover, .viewval_select.active:hover, .viewval_autocomplete.active:hover, .viewval_datepicker.active:hover {
    background: white;
    cursor: pointer;
}

.viewval_hover {
    background: white;
}

/* ============================================================================== */
/*  Holiday                                                                       */
/* ============================================================================== */

#types .btn {
    cursor: pointer;
}

#types .btn-primary {
    font-weight: bold;
}

#types form {
    padding: 20px;
}

#types label {
    display:inline-block;
    width:100px;
    margin-right: 20px;
    padding: 4px;
    text-align: right;
    vertical-align: top;
}

#types input.text, #types textarea {
    width: 400px;
}

#types textarea {
    height: 100px;
}

/*------------------------------------*\
#jQuery Modules
\*------------------------------------*/

/**
* Tooltips
*/

#tooltip {
    background-color: #fffff0;
    border-top: solid 1px #bbb;
    border-<?php print $left; ?>: solid 1px #bbb;
    border-<?php print $right; ?>: solid 1px #444;
    border-bottom: solid 1px #444;
    opacity: 1;
    padding: 2px;
    position: absolute;
    width: <?php print dol_size(450,'width'); ?>px;
    z-index: 97;
}

/**
* Gantt
*/

td.gtaskname {
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ============================================================================== */
/* Admin Menu                                                                     */
/* ============================================================================== */

/* CSS for treeview */
.treeview ul { background-color: transparent !important; margin-top: 0 !important; /* margin-bottom: 4px !important; padding-top: 2px !important; */ }
.treeview li { background-color: transparent !important; padding: 0 0 0 20px !important; min-height: 30px; }
.treeview .hitarea { width: 20px !important; margin-left: -20px !important; margin-top: 3px; }
.treeview li table { min-height: 30px; }
.treeview .hover { color: var(--colortextlink) !important; text-decoration: underline !important; }


/* ============================================================================== */
/*  Show Excel tabs                                                               */
/* ============================================================================== */

.table_data
{
    border-style:ridge;
    border:1px solid;
}
.tab_base
{
    background:#C5D0DD;
    font-weight:bold;
    border-style:ridge;
    border: 1px solid;
    cursor:pointer;
}
.table_sub_heading
{
    background:#CCCCCC;
    font-weight:bold;
    border-style:ridge;
    border: 1px solid;
}
.table_body
{
    background:#F0F0F0;
    font-weight:normal;
    font-family:sans-serif;
    border-style:ridge;
    border: 1px solid;
    border-spacing: 0px;
    border-collapse: collapse;
}
.tab_loaded
{
    background:#222222;
    color:white;
    font-weight:bold;
    border-style:groove;
    border: 1px solid;
    cursor:pointer;
}


/* ============================================================================== */
/*  CSS for color picker                                                          */
/* ============================================================================== */

div.jPicker table.jPicker {
    padding-bottom: 20px;
    padding-right: 20px;
    padding-left: 20px;
}
table.jPicker tr:first-of-type td {
    height: 2px !important;
    line-height: 2px;
}
.jPicker .Move {
    background: unset !important;
    border: unset !important;
}
.jPicker .Preview div span {
    border: unset !important;
    width: unset !important;
    height: 50% !important;
}
table.jPicker {
    border-radius: 5px;
    border: 1px solid #bbb !important;
    background-color: var(--colorbackbody) !important;
    box-shadow: 0px 0px 10px #ccc;
}
.jPicker .Grid {
    background-image: unset !important;
}
.jPicker .Grid span.QuickColor {
    border: unset !important;
}
.jPicker td.Radio {
    min-width: 34px;
}
.jPicker td.Text {
    white-space: nowrap;
}
.jPicker td.Text input {
    height: 1em !important;
}
.jPicker .Preview div {
    height: 36px !important;
}
.jPicker input[type="button"] {
    background: var(--butactionbg);
    color: var(--textbutaction);
    border-radius: 4px;
    border-collapse: collapse;
    border: none;
}

A.color, A.color:active, A.color:visited {
    position : relative;
    display : block;
    text-decoration : none;
    width : 10px;
    height : 10px;
    line-height : 10px;
    margin : 0px;
    padding : 0px;
    border : 1px inset white;
}
A.color:hover {
    border : 1px outset white;
}
A.none, A.none:active, A.none:visited, A.none:hover {
    position : relative;
    display : block;
    text-decoration : none;
    width : 10px;
    height : 10px;
    line-height : 10px;
    margin : 0px;
    padding : 0px;
    cursor : default;
    border : 1px solid #b3c5cc;
}
.tblColor {
    display : none;
}
.tdColor {
    padding : 1px;
}
.tblContainer {
    background-color : #b3c5cc;
}
.tblGlobal {
    position : absolute;
    top : 0px;
    left : 0px;
    display : none;
    background-color : #b3c5cc;
    border : 2px outset;
}
.tdContainer {
    padding : 5px;
}
.tdDisplay {
    width : 50%;
    height : 20px;
    line-height : 20px;
    border : 1px outset white;
}
.tdDisplayTxt {
    width : 50%;
    height : 24px;
    line-height : 12px;
    font-family : <?php print $fontlist ?>;
    font-size : 8pt;
    color : black;
    text-align : center;
}
.btnColor {
    width : 100%;
    font-family : <?php print $fontlist ?>;
    font-size : 10pt;
    padding : 0px;
    margin : 0px;
}
.btnPalette {
    width : 100%;
    font-family : <?php print $fontlist ?>;
    font-size : 8pt;
    padding : 0px;
    margin : 0px;
}
.colorselector {
    border: solid 1px #ddd !important;
}

/* Style to overwrites JQuery styles */
.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
    /* border: 1px solid #888; */
    background: var(--colorbacktitle1);
    color: unset;
    font-weight: bold;
}
.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover {
    background: var(--colorbackhmenu1);
    color: var(--colorfdateselected) !important;
    border: 1px solid var(--colorbackhmenu1);
}
.ui-menu {
    padding: 5px;
    /*
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
        */
}
.ui-menu .ui-menu-item a {
    text-decoration:none;
    display:block;
    padding:.5em .6em;
    line-height:1.5;
    font-weight: normal;
    font-family:<?php echo $fontlist; ?>;
    font-size:1em;
}
.ui-widget {
    font-family:<?php echo $fontlist; ?>;
}
/* .ui-button { margin-left: -2px; <?php print(preg_match('/chrome/', $conf->browser->name) ? 'padding-top: 1px;' : ''); ?> } */
.ui-button { margin-left: -2px; }
.ui-button-icon-only .ui-button-text { height: 8px; }
.ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text { padding: 2px 0px 6px 0px; }
.ui-button-text
{
    line-height: 1em !important;
}
.ui-autocomplete-input { margin: 0; padding: 4px; }


/* ============================================================================== */
/*  CKEditor                                                                      */
/* ============================================================================== */

body.cke_show_borders {
    margin: 5px !important;
}

.cke_dialog {
    border: 1px #bbb solid ! important;
}
/*.cke_editor table, .cke_editor tr, .cke_editor td
{
	border: 0px solid #FF0000 !important;
}
span.cke_skin_kama { padding: 0 !important; }*/
.cke_wrapper { padding: 4px !important; }
a.cke_dialog_ui_button
{
    font-family: <?php print $fontlist ?> !important;
    background-image: url(<?php echo $img_button ?>) !important;
    background-position: bottom !important;
    border: 1px solid #C0C0C0 !important;
    -webkit-border-radius:0px 5px 0px 5px !important;
    border-radius:0px 5px 0px 5px !important;
    -webkit-box-shadow: 3px 3px 4px #DDD !important;
    box-shadow: 3px 3px 4px #DDD !important;
}
.cke_dialog_ui_hbox_last
{
    vertical-align: bottom !important;
}
.cke_dialog_ui_hbox_first {
    vertical-align: middle !important;
}
.cke_combo_text {
    width: 40px !important;
}
/*
.cke_editable
{
	line-height: 1.4 !important;
	margin: 6px !important;
}
*/
a.cke_dialog_ui_button_ok span {
    text-shadow: none !important;
    color: #333 !important;
}
a.cke_button, a.cke_combo_button {
    height: 18px !important;
}
div.cke_notifications_area .cke_notification_warning {
    visibility: hidden;
}

/* CSS To hide the picto menu on smartphone, except when maximize */
@media only screen and (max-width: 768px)
{
    .cke_inner:not(.cke_maximized) .cke_toolbar_separator,
    .cke_inner:not(.cke_maximized) .cke_combo,
    .cke_inner:not(.cke_maximized) .cke_button:not(.cke_button__maximize) {
        display: none;
    }
}

/* ============================================================================== */
/*  ACE editor                                                                    */
/* ============================================================================== */
.ace_editor {
    border: 1px solid #ddd;
    margin: 0;
}
.aceeditorstatusbar {
    margin: 0;
    padding: 0;
    padding-<?php echo $left; ?>: 10px;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ebebeb;
    height: 2.2em;
    line-height: 2.2em;
}
.ace_status-indicator {
    color: gray;
    position: relative;
    right: 0;
    border-left: 1px solid;
}
pre#editfilecontentaceeditorid {
    margin-top: 5px;
}


/* ============================================================================== */
/*  File upload                                                                   */
/* ============================================================================== */

.template-upload {
    height: 72px !important;
}


/* ============================================================================== */
/*  Custom reports                                                                */
/* ============================================================================== */

.customreportsoutput, .customreportsoutputnotdata {
    padding-top: 20px;
}
.customreportsoutputnotdata {
    text-align: center;
}

/* ============================================================================== */
/*  Holiday                                                                       */
/* ============================================================================== */

#types .btn {
    cursor: pointer;
}

#types .btn-primary {
    font-weight: bold;
}

#types form {
    padding: 20px;
}

#types label {
    display:inline-block;
    width:100px;
    margin-right: 20px;
    padding: 4px;
    text-align: right;
    vertical-align: top;
}

#types input.text, #types textarea {
    width: 400px;
}

#types textarea {
    height: 100px;
}


/* ============================================================================== */
/*  Comments                                                                   	  */
/* ============================================================================== */

#comment div {
    box-sizing:border-box;
}
#comment .comment {
    border-radius:7px;
    margin-bottom:10px;
    overflow:hidden;
}
#comment .comment-table {
    display:table;
    height:100%;
}
#comment .comment-cell {
    display:table-cell;
}
#comment .comment-info {
    font-size:0.8em;
    border-right:1px solid #dedede;
    margin-right:10px;
    width:160px;
    text-align:center;
    background:rgba(255,255,255,0.5);
    vertical-align:middle;
    padding:10px 2px;
}
#comment .comment-info a {
    color:inherit;
}
#comment .comment-right {
    vertical-align:top;
}
#comment .comment-description {
    padding:10px;
    vertical-align:top;
}
#comment .comment-delete {
    width: 100px;
    text-align:center;
    vertical-align:middle;
}
#comment .comment-delete:hover {
    background:rgba(250,20,20,0.8);
}
#comment .comment-edit {
    width: 100px;
    text-align:center;
    vertical-align:middle;
}
#comment .comment-edit:hover {
    background:rgba(0,184,148,0.8);
}
#comment textarea {
    width: 100%;
}



/* ============================================================================== */
/*  JSGantt                                                                       */
/* ============================================================================== */

div.scroll2 {
    width: <?php print isset($_SESSION['dol_screenwidth']) ? max((int) $_SESSION['dol_screenwidth'] - 830, 450) : '450'; ?>px !important;
}

div#GanttChartDIVglisthead, div#GanttChartDIVgcharthead {
    line-height: 2;
}

.gtaskname div, .gtaskname, .gstartdate div, .gstartdate, .genddate div, .genddate {
    font-size: unset !important;
}

div.gantt, .gtaskheading, .gmajorheading, .gminorheading, .gminorheadingwkend {
    font-size: unset !important;
    font-weight: normal !important;
    color: #000 !important;
}
div.gTaskInfo {
    background: #f0f0f0 !important;
}
.gtaskblue {
    background: rgb(108,152,185) !important;
}
.gtaskgreen {
    background: rgb(160,173,58) !important;
}
td.gtaskname {
    overflow: hidden;
    text-overflow: ellipsis;
}
td.gminorheadingwkend {
    color: #888 !important;
}
td.gminorheading {
    color: #666 !important;
}
.glistlbl, .glistgrid {
    width: 582px !important;
}
/*.gtaskname div, .gtaskname {
	min-width: 250px !important;
	max-width: 250px !important;
	width: 250px !important;
}*/
.gtaskname div, .gtaskname {
    min-width: 250px !important;
    max-width: 500px !important;
    width: unset !important;
}
.gpccomplete div, .gpccomplete {
    min-width: 40px !important;
    max-width: 40px !important;
    width: 40px !important;
}
td.gtaskheading.gstartdate, td.gtaskheading.genddate {
    white-space: break-spaces;
}
.gtasktableh tr:nth-child(2) td:nth-child(2), .gtasktableh tr:nth-child(2) td:nth-child(3), .gtasktableh tr:nth-child(2) td:nth-child(4), .gtasktableh tr:nth-child(2) td:nth-child(5), .gtasktableh tr:nth-child(2) td:nth-child(6), .gtasktableh tr:nth-child(2) td:nth-child(7) {
    color: transparent !important;
    border-left: none;
    border-right: none;
    border-top: none;
}

/* ============================================================================== */
/*  jFileTree                                                                     */
/* ============================================================================== */

.ecmfiletree {
    width: 99%;
    height: 99%;
    padding-left: 2px;
    font-weight: normal;
}

.fileview {
    width: 99%;
    height: 99%;
    background: #FFF;
    padding-left: 2px;
    padding-top: 4px;
    font-weight: normal;
}

div.filedirelem {
    position: relative;
    display: block;
    text-decoration: none;
}

ul.filedirelem {
    padding: 2px;
    margin: 0 5px 5px 5px;
}
ul.filedirelem li {
    list-style: none;
    padding: 2px;
    margin: 0 10px 20px 10px;
    width: 160px;
    height: 120px;
    text-align: center;
    display: block;
    float: <?php print $left; ?>;
    border: solid 1px #DDDDDD;
}

ul.ecmjqft {
    line-height: 32px;
    padding: 0px;
    margin: 0px;
    font-weight: normal;
}

ul.ecmjqft li {
    list-style: none;
    padding: 0px;
    padding-left: 20px;
    margin: 0px;
    white-space: nowrap;
    display: block;
}

ul.ecmjqft a {
    line-height: 24px;
    vertical-align: middle;
    color: unset;
    padding: 0px 0px;
    font-weight:normal;
    display: inline-block !important;
}
ul.ecmjqft > a {
    width: calc(100% - 100px);
    overflow: hidden;
    white-space: break-spaces;
    word-break: break-all;
}
ul.ecmjqft a:active {
    font-weight: bold !important;
}
ul.ecmjqft a:hover {
    text-decoration: underline;
}
div.ecmjqft {
    vertical-align: middle;
    display: inline-block !important;
    text-align: <?php echo $right; ?>;
    float: <?php echo $right; ?>;
    right:4px;
    clear: both;
    height: 16px;
}
#ecm-layout-north {
    min-height: 40px;
}
#ecm-layout-north div.attachareaformuserfileecm {
    padding-bottom: 0px;
}
div#ecm-layout-west {
    width: 380px;
    vertical-align: top;
}
div#ecm-layout-center {
    width: calc(100% - 405px);
    vertical-align: top;
    float: <?php echo $right; ?>;
}

.ecmjqft LI.directory { font-weight:normal; background: url(<?php echo dol_buildpath($path.'/theme/common/treemenu/folder2.png', 1); ?>) left top no-repeat; background-position-y: 8px; }
.ecmjqft LI.expanded { font-weight:normal; background: url(<?php echo dol_buildpath($path.'/theme/common/treemenu/folder2-expanded.png', 1); ?>) left top no-repeat; background-position-y: 8px; }
.ecmjqft LI.wait { font-weight:normal; background: url(<?php echo dol_buildpath('/theme/'.$theme.'/img/working.gif', 1); ?>) left top no-repeat; }


/* ============================================================================== */
/*  jNotify                                                                       */
/* ============================================================================== */

.jnotify-container {
    position: fixed !important;
<?php if (getDolGlobalString('MAIN_JQUERY_JNOTIFY_BOTTOM')) { ?>
    top: auto !important;
    bottom: 4px !important;
<?php } ?>
    text-align: center;
    min-width: <?php echo $dol_optimize_smallscreen ? '200' : '480'; ?>px;
    width: auto;
    max-width: 1024px;
    padding-left: 10px !important;
    padding-right: 10px !important;
    padding-top: 10px !important;
    word-wrap: break-word;
}
.jnotify-container .jnotify-notification .jnotify-message {
    font-weight: normal;
    text-align: start;
    word-break: break-word;
}
.jnotify-container .jnotify-notification-warning .jnotify-close, .jnotify-container .jnotify-notification-warning .jnotify-message {
    color: #a28918 !important;
}
.jnotify-container .jnotify-close {
    top: 4px !important;
    font-size: 1.6em !important;
}

/* use or not ? */
div.jnotify-background {
    opacity : 0.95 !important;
    -webkit-box-shadow: 2px 2px 4px #8888 !important;
    box-shadow: 2px 2px 4px #8888 !important;
}

/* jnotify for the login page */
.bodylogin .jnotify-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 100000;
    max-width: unset;
    padding-left: unset !important;
    padding-right: unset !important;
    padding-top: unset !important;
}
.bodylogin .jnotify-container .jnotify-notification {
    margin: unset !important;
}
.bodylogin .jnotify-container .jnotify-notification .jnotify-background {
    border-radius: unset !important;
}
.bodylogin .jnotify-container .jnotify-notification .jnotify-message {
    text-align: center;
    font-size: 1.1em;
    font-weight: bold;
}


/* ============================================================================== */
/*  blockUI                                                                      */
/* ============================================================================== */

/*div.growlUI { background: url(check48.png) no-repeat 10px 10px }*/
div.dolEventValid h1, div.dolEventValid h2 {
    color: #567b1b;
    background-color: #e3f0db;
    padding: 5px 5px 5px 5px;
    text-align: left;
}
div.dolEventError h1, div.dolEventError h2 {
    color: #a72947;
    background-color: #d79eac;
    padding: 5px 5px 5px 5px;
    text-align: left;
}

/* ============================================================================== */
/*  Maps                                                                          */
/* ============================================================================== */

.divmap, #google-visualization-geomap-embed-0, #google-visualization-geomap-embed-1, #google-visualization-geomap-embed-2 {
}


/* ============================================================================== */
/*  Datatable                                                                     */
/* ============================================================================== */

table.dataTable tr.odd td.sorting_1, table.dataTable tr.even td.sorting_1 {
    background: none !important;
}
.sorting_asc  { background: url('<?php echo dol_buildpath('/theme/'.$theme.'/img/sort_asc.png', 1); ?>') no-repeat center right !important; }
.sorting_desc { background: url('<?php echo dol_buildpath('/theme/'.$theme.'/img/sort_desc.png', 1); ?>') no-repeat center right !important; }
.sorting_asc_disabled  { background: url('<?php echo dol_buildpath('/theme/'.$theme.'/img/sort_asc_disabled.png', 1); ?>') no-repeat center right !important; }
.sorting_desc_disabled { background: url('<?php echo dol_buildpath('/theme/'.$theme.'/img/sort_desc_disabled.png', 1); ?>') no-repeat center right !important; }
.dataTables_paginate {
    margin-top: 8px;
}
.paginate_button_disabled {
    opacity: 1 !important;
    color: #888 !important;
    cursor: default !important;
}
.paginate_disabled_previous:hover, .paginate_enabled_previous:hover, .paginate_disabled_next:hover, .paginate_enabled_next:hover
{
    font-weight: normal;
}
.paginate_enabled_previous:hover, .paginate_enabled_next:hover
{
    text-decoration: underline !important;
}
.paginate_active
{
    text-decoration: underline !important;
}
.paginate_button
{
    font-weight: normal !important;
    text-decoration: none !important;
}
.paging_full_numbers {
    height: inherit !important;
}
.paging_full_numbers a.paginate_active:hover, .paging_full_numbers a.paginate_button:hover {
    background-color: var(--colorbackbody) !important;
}
.paging_full_numbers, .paging_full_numbers a.paginate_active, .paging_full_numbers a.paginate_button {
    background-color: var(--colorbackbody) !important;
    border-radius: inherit !important;
}
.paging_full_numbers a.paginate_button_disabled:hover, .paging_full_numbers a.disabled:hover {
    background-color: var(--colorbackbody) !important;
}
.paginate_button, .paginate_active {
    border: 1px solid #ddd !important;
    padding: 6px 12px !important;
    margin-left: -1px !important;
    line-height: 1.42857143 !important;
    margin: 0 0 !important;
}

/* For jquery plugin combobox */
/* Disable this. It breaks wrapping of boxes
.ui-corner-all { white-space: nowrap; } */

.ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled, .paginate_button_disabled {
    opacity: .35;
    background-image: none;
}

div.dataTables_length {
    float: right !important;
    padding-left: 8px;
}
div.dataTables_length select {
    background: #fff;
}
.dataTables_wrapper .dataTables_paginate {
    padding-top: 0px !important;
}

/* confirmation box */

.ui-state-default,
.ui-widget-header .ui-state-default,
.ui-widget-content .ui-state-default {
	background-color: unset !important;
	color: var(--colortext);
}

.ui-widget-header {
background-color: #ccc!important;
}

.ui-dialog .ui-dialog-content { padding-top: 1em!important }

.ui-corner-all,
.ui-corner-bottom,
.ui-corner-right,
.ui-corner-br {
border-bottom-right-radius: 0!important;
-moz-border-radius-bottomright: 0!important;
-webkit-border-bottom-right-radius: 0!important;
-khtml-border-bottom-right-radius: 0!important;
}

.ui-corner-all,
.ui-corner-bottom,
.ui-corner-left,
.ui-corner-bl {
border-bottom-left-radius: 0!important;
-moz-border-radius-bottomleft: 0!important;
-webkit-border-bottom-left-radius: 0!important;
-khtml-border-bottom-left-radius: 0!important;
}

.ui-corner-all,
.ui-corner-top,
.ui-corner-right,
.ui-corner-tr {
border-top-right-radius: 0!important;
-moz-border-radius-topright: 0!important;
-webkit-border-top-right-radius: 0!important;
-khtml-border-top-right-radius: 0!important;
}

.ui-corner-all,
.ui-corner-top,
.ui-corner-left,
.ui-corner-tl{
border-bottom-top-radius: 0!important;
-moz-border-radius-topleft: 0!important;
-webkit-border-top-left-radius: 0!important;
-khtml-border-top-left-radius: 0!important;
}




/* ============================================================================== */
/*  Select2                                                                       */
/* ============================================================================== */

span.select2-selection--single.flat[aria-disabled="true"] span.select2-selection__rendered {
    opacity: 0.5;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: var(--colorbackhmenu1);
    color: var(--colortextbackhmenu);
}
.select2-container--default .select2-results__option--highlighted[aria-selected] span {
    color: #fff !important;
}

span.select2.select2-container.select2-container--default {
    text-align: initial;
    <?php if (!getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT')) { ?>
    border-left: none;
    border-top: none;
    border-right: none;
    <?php } ?>
}
span.select2.select2-container.select2-container--default {
    <?php if (!getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT')) { ?>
    /*border-bottom: solid 1px var(--inputbordercolor);*/
    <?php } ?>
}

input.select2-input {
    border-bottom: none ! important;
}
.select2-choice {
    border: none;
    border-bottom: solid 1px var(--inputbordercolor) !important;	/* required to avoid to lose bottom line when focus is lost on select2. */
}
.select2-results .select2-highlighted.optionblue {
    color: #FFF !important;
}
.select2-container .select2-selection--multiple {
    min-height: 28px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    margin-top: 5px !important;
    border: none;
}
.select2-container--focus span.select2-selection.select2-selection--single {
    border-bottom: 1px solid var(--inputbordercolor) !important;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.blockvmenusearch .select2-container--default .select2-selection--single,
.blockvmenubookmarks .select2-container--default .select2-selection--single
{
    background-color: var(--colorbackvmenu1);
}
.select2-container--default .select2-selection--single {
    background-color: var(--inputbackgroundcolor);
}
#blockvmenusearch .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--colortextbackvmenu);
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--colortext);
    /* background-color: var(--inputbackgroundcolor); */
}
.select2-default {
    color: #999 !important;
}
.select2-choice, .select2-container .select2-choice {
    border-bottom: solid 1px rgba(0,0,0,.4);
}
.select2-container .select2-choice > .select2-chosen {
    margin-right: 23px;
}
.select2-container .select2-choice .select2-arrow {
    border-radius: 0;
    background: transparent;
}
.select2-container-multi .select2-choices {
    background-image: none;
}
.select2-container .select2-choice {
    color: #000;
    border-radius: 0;
}
.selectoptiondisabledwhite {
    background: #FFFFFF !important;
}
.select2-arrow {
    border: none;
    border-left: none !important;
    background: none !important;
}
.select2-choice
{
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
}
.select2-drop.select2-drop-above {
    box-shadow: none !important;
}
.select2-container--open .select2-dropdown--above {
    border-bottom: solid 1px var(--inputbordercolor);
}
.select2-drop.select2-drop-above.select2-drop-active {
    border-top: 1px solid #ccc;
    border-bottom: solid 1px var(--inputbordercolor);
}
.select2-container--default .select2-selection--single
{
    outline: none;
    <?php if (!getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT')) { ?>
    border-top: none;
    border-left: none;
    border-right: none;
    <?php } ?>

    border<?php echo !getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT') ? '-bottom' : ''; ?>: solid 1px var(--inputbordercolor);

    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border-radius: 3px;
}
.select2-container--focus .select2-container--default .select2-selection--single {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-top: none;
    border-left: none;
    border-right: none;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.select2-container--default .select2-selection--multiple {
    border-bottom: solid 1px var(--inputbordercolor);
    border-top: none;
    border-left: none;
    border-right: none;
    border-radius: 3px;
    background: var(--inputbackgroundcolor);
    line-height: normal;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    line-height: 1.4em;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: var(--color1BckgrdInfobox);
    margin-top: 4px !important;
}
.select2-selection--multiple input.select2-search__field {
    border-bottom: none !important;
}.select2-container--default .select2-selection--single
 {
     outline: none;
 <?php if (!getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT')) { ?>
     border-top: none;
     border-left: none;
     border-right: none;
 <?php } ?>

     border<?php echo !getDolGlobalString('THEME_SHOW_BORDER_ON_INPUT') ? '-bottom' : ''; ?>: solid 1px var(--inputbordercolor);

     -webkit-box-shadow: none !important;
     box-shadow: none !important;
     border-radius: 3px;
 }
.select2-container--focus .select2-container--default .select2-selection--single {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-top: none;
    border-left: none;
    border-right: none;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.select2-container--default .select2-selection--multiple {
    border-bottom: solid 1px var(--inputbordercolor);
    border-top: none;
    border-left: none;
    border-right: none;
    border-radius: 3px;
    background: var(--inputbackgroundcolor);
    line-height: normal;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    line-height: 1.4em;
}
.select2-selection--multiple input.select2-search__field {
    border-bottom: none !important;
}

.select2-search__field
{
    outline: none;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
    border-bottom: solid 1px var(--inputbordercolor) !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    /* color: black; */
}
.select2-container-active .select2-choice, .select2-container-active .select2-choices
{
    outline: none;
    border-top: none;
    border-left: none;
    border-bottom: none;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    background-color: var(--colorbackvmenu1);
}
.select2-dropdown {
	border: 1px solid var(--colorboxstatsborder);
	background-color: var(--colorbacklineimpair1) !important;
}
.select2-dropdown-open {
	background-color: var(--colorbacklineimpair1) !important;
}
.select2-dropdown-open .select2-choice, .select2-dropdown-open .select2-choices
{
    outline: none;
    border-top: none;
    border-left: none;
    border-bottom: none;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
	background-color: var(--colorbacklineimpair1) !important;
}
.select2-disabled
{
    color: #888;
}
.select2-drop.select2-drop-above.select2-drop-active, .select2-drop {
    border-radius: 0;
}
.select2-drop.select2-drop-above {
    border-radius:  0;
}
.select2-dropdown-open.select2-drop-above .select2-choice, .select2-dropdown-open.select2-drop-above .select2-choices {
    background-image: none;
    border-radius: 0 !important;
}
div.select2-drop-above
{
    background: var(--colorbackvmenu1);
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
}
.select2-drop-active
{
    border: 1px solid #ccc;
    padding-top: 4px;
}
.select2-search input {
    border: none;
}
a span.select2-chosen
{
    font-weight: normal !important;
}
.select2-container .select2-choice {
    background-image: none;
    /* line-height: 24px; */
}
.select2-results .select2-no-results, .select2-results .select2-searching, .select2-results .select2-ajax-error, .select2-results .select2-selection-limit
{
    background: var(--colorbackvmenu1);
}
.select2-results {
    max-height:	400px;
}
.select2-results__option {
    word-break: break-word;
    text-align: <?php echo $left; ?>;
}
.select2-container.select2-container-disabled .select2-choice, .select2-container-multi.select2-container-disabled .select2-choices {
    background-color: var(--colorbackvmenu1);
    background-image: none;
    border: none;
    cursor: default;
}
.select2-container-disabled .select2-choice .select2-arrow b {
    opacity: 0.4;
}
.select2-container-multi .select2-choices .select2-search-choice {
    margin-bottom: 3px;
}
.select2-dropdown-open.select2-drop-above .select2-choice, .select2-dropdown-open.select2-drop-above .select2-choices, .select2-container-multi .select2-choices,
.select2-container-multi.select2-container-active .select2-choices
{
    border-bottom: 1px solid #ccc;
    border-right: none;
    border-top: none;
    border-left: none;

}
.select2-container--default .select2-results>.select2-results__options{
    max-height: 400px;
}
.select2-container--default .select2-results__option[aria-selected=true] {
	/*background-color: var(--inputbackgroundcolor);*/
    color: var(--colortext);
}

/* Special case for the select2 add widget */
#addbox .select2-container .select2-choice > .select2-chosen, #actionbookmark .select2-container .select2-choice > .select2-chosen {
    text-align: <?php echo $left; ?>;
    opacity: 0.4;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: unset;
    opacity: 0.4;
}
span#select2-boxbookmark-container, span#select2-boxcombo-container {
    text-align: <?php echo $left; ?>;
    opacity: 0.4;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 6px;
}
/* Style used before the select2 js is executed on boxcombo */
#boxbookmark.boxcombo, #boxcombo.boxcombo {
    text-align: left;
    opacity: 0.4;
    border-bottom: solid 1px rgba(0,0,0,.4) !important;
    height: 26px;
    line-height: 24px;
    padding: 0 0 2px 0;
    vertical-align: top;
}

/* To emulate select 2 style */
.select2-container-multi-dolibarr .select2-choices-dolibarr .select2-search-choice-dolibarr {
    padding: 3px 5px 3px 5px;
    margin: 0 0 2px 3px;
    position: relative;
    line-height: 13px;
    color: #333;
    cursor: default;
    border: 1px solid #aaaaaa;
    border-radius: 3px;
    -webkit-box-shadow: 0 0 2px #fff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
    box-shadow: 0 0 2px #fff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
    background-clip: padding-box;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-color: #e4e4e4;
    background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), color-stop(100%, #eee));
    background-image: -webkit-linear-gradient(to top,  #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);
    background-image: -moz-linear-gradient(to top,  #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);
    background-image: linear-gradient(to bottom, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);
}
.select2-container-multi-dolibarr .select2-choices-dolibarr .select2-search-choice-dolibarr a {
    font-weight: normal;
}
.select2-container-multi-dolibarr .select2-choices-dolibarr li {
    float: left;
    list-style: none;
}
.select2-container-multi-dolibarr .select2-choices-dolibarr {
    height: auto !important;
    height: 1%;
    margin: 0;
    padding: 0 5px 0 0;
    position: relative;
    cursor: text;
    overflow: hidden;
}
.select2-results__option {
    padding: 8px;
}
span.select2-dropdown--below {
    margin-top: -1px;
    min-width: 100px;
}
span.select2-dropdown--above {
    margin-bottom: -1px;
    min-width: 100px;
}

.parentonrightofpage {
    direction: rtl;
}

select.multiselectononeline {
    padding: 0;
    vertical-align: middle;
    min-height: unset;
    height: 2.2em !important;
    opacity: 0;
    /* width: 1px !important; */
}

@media only screen and (min-width: 768px)
{
    /* CSS to have the dropdown boxes larger that the input search area */
    .select2-container.select2-container--open:not(.graphtype, .limit, .combolargeelem):not(.yesno) .select2-dropdown.ui-dialog {
        min-width: 230px !important;
    }
    .select2-container.select2-container--open:not(.graphtype, .limit, .combolargeelem):not(.yesno) .select2-dropdown--below:not(.onrightofpage),
    .select2-container.select2-container--open:not(.graphtype, .limit, .combolargeelem):not(.yesno) .select2-dropdown--above:not(.onrightofpage) {
        min-width: 230px !important;
    }
    .onrightofpage span.select2-dropdown.ui-dialog.select2-dropdown--below,
    .onrightofpage span.select2-dropdown.ui-dialog.select2-dropdown--above {
        min-width: 140px !important;
    }
    .combolargeelem.select2-container.select2-container--open .select2-dropdown.ui-dialog {
        min-width: 320px !important;
    }

    .select2-container--open .select2-dropdown--below {
        border-top: 1px solid var(--inputbordercolor);
        /* border-top: 1px solid #aaaaaa; */
    }
}

/* must be after the other .select2-container.select2-container--open .select2-dropdown.ui-dialog */
.limit.select2-container.select2-container--open .select2-dropdown.ui-dialog {
    min-width: 100px !important;
}

/* ============================================================================== */
/*  For categories                                                                */
/* ============================================================================== */

.noborderoncategories {
    border: none !important;
    border-radius: 5px !important;
    box-shadow: none;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
}
span.noborderoncategories a, li.noborderoncategories a {
    line-height: normal;
}
span.noborderoncategories {
    padding: 3px 5px 3px 5px;
}
.categtextwhite, .treeview .categtextwhite.hover {
color: #fff !important;
}
.categtextblack {
color: #000 !important;
}


/* ============================================================================== */
/*  External lib multiselect with checkbox                                        */
/* ============================================================================== */

.multi-select-menu {
    z-index: 10;
}

.multi-select-container {
    display: inline-block;
    position: relative;
}

.multi-select-menu {
    position: absolute;
    left: 0;
    top: 0.8em;
    float: left;
    min-width: 100%;
    background: var(--inputbackgroundcolor);
    margin: 1em 0;
    padding: 0.4em 0;
    border: 1px solid #aaa;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    display: none;
}

div.multi-select-menu[role="menu"] {
    min-width: 220px !important;
}

.multi-select-menu input {
    margin-right: 0.3em;
    vertical-align: 0.1em;
}

.multi-select-button {
    display: inline-block;
    max-width: 20em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
    background-color: var(--inputbackgroundcolor);
    cursor: default;

    border: none;
    border-bottom: solid 1px var(--inputbordercolor);
    padding: 5px;
    padding-left: 2px;
    height: 17px;
}
.multi-select-button:focus {
    outline: none;
    border-bottom: 1px solid #666;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.multi-select-button:after {
    content: "";
    display: inline-block;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0.5em 0.23em 0em 0.23em;
    border-color: #444 transparent transparent transparent;
    margin-left: 0.4em;
}

.multi-select-container--open .multi-select-menu { display: block; }

.multi-select-container--open .multi-select-button:after {
    border-width: 0 0.4em 0.4em 0.4em;
    border-color: transparent transparent #999 transparent;
}

.multi-select-menuitem {
    clear: both;
    float: left;
    padding-left: 5px
}
label.multi-select-menuitem {
    line-height: 24px;
    text-align: start;
}

/* ============================================================================== */
/*  Native multiselect with checkbox                                              */
/* ============================================================================== */

ul.ulselectedfields {
    z-index: 95;			/* To have the select box appears on first plan even when near buttons are decorated by jmobile */
}

dl.dropdown {
    margin:0px;
    padding:0px;
    margin-left: 2px;
    margin-right: 2px;
    vertical-align: middle;
    display: inline-block;
}

#linktoobjectname {
    width:400px;
}

.dropdown dd, .dropdown dt {
    margin:0px;
    padding:0px;
}
.dropdown ul {
    margin: -1px 0 0 0;
    text-align: <?php echo $left; ?>;
}
.dropdown dd {
    position:relative;
}
.dropdown dt a {
    display:block;
    overflow: hidden;
    border:0;
}
.dropdown dt a span, .multiSel span {
    cursor:pointer;
    display:inline-block;
    padding: 0 3px 2px 0;
}
.maxwidthsearch .dropdown dt a span, .multiSel span {
    padding: 3px 3px 2px 3px;
}
.dropdown span.value {
    display:none;
}
.dropdown dd ul {
    background-color: var(--inputbackgroundcolor);
    box-shadow: 1px 1px 10px #aaa;
    display:none;
<?php echo $right; ?>:0px;						/* pop is align on right */
    padding: 0 0 0 0;
    position:absolute;
    top:2px;
    list-style:none;
    max-height: 264px;
    overflow: auto;
    border-radius: 4px;
    z-index: 1;
}
.dropdown dd ul.selectedfieldsleft {
<?php echo $right; ?>: auto;
}
.dropdown dd ul li {
    white-space: nowrap;
    font-weight: normal;
    padding: 7px 8px 7px 8px;
    /* color: var(--colortext); */
    color: var(--colortext);
}
.dropdown dd ul li:hover {
    background: #eee;
}
.dropdown dd ul li input[type="checkbox"] {
    margin-<?php echo $right; ?>: 3px;
}
.dropdown dd ul li a, .dropdown dd ul li span {
    padding: 3px;
    display: block;
}
.dropdown dd ul li span {
    color: #888;
}
/*.dropdown dd ul li a:hover {
	background-color: var(--inputbackgroundcolor);
}*/
dd.dropdowndd ul li {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

/* ============================================================================== */
/* Kanban                                                                         */
/* ============================================================================== */

.info-box-label {
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ============================================================================== */
/*  Markdown rendering                                                             */
/* ============================================================================== */

.imgmd {
    width: 90%;
}
.moduledesclong h1 {
    padding-top: 10px;
    padding-bottom: 20px;
}

/* ============================================================================== */
/*  JMobile - Android                                                             */
/* ============================================================================== */

.searchpage .tagtr .tagtd {
    padding-top: 2px;
    padding-bottom: 2px;
}
.searchpage .tagtr .tagtd .button {
    background: unset;
    border: unset;
}
.searchpage .searchform input {
    font-size: 1.15em;
}

li.ui-li-divider .ui-link {
    color: #FFF !important;
}
.ui-btn {
    margin: 0 2px;
}
a.ui-link, a.ui-link:hover, .ui-btn:hover, span.ui-btn-text:hover, span.ui-btn-inner:hover {
    text-decoration: none !important;
}
.ui-body-c {
    background: #fff;
}

.ui-btn-inner {
    min-width: .4em;
    padding-left: 6px;
    padding-right: 6px;
    font-size: <?php print is_numeric($fontsize) ? $fontsize.'px' : $fontsize; ?>;
    /* white-space: normal; */		/* Warning, enable this break the truncate feature */
}
.ui-btn-icon-right .ui-btn-inner {
    padding-right: 30px;
}
.ui-btn-icon-left .ui-btn-inner {
    padding-left: 30px;
}
.ui-select .ui-btn-icon-right .ui-btn-inner {
    padding-right: 30px;
}
.ui-select .ui-btn-icon-left .ui-btn-inner {
    padding-left: 30px;
}
.ui-select .ui-btn-icon-right .ui-icon {
    right: 8px;
}
.ui-btn-icon-left > .ui-btn-inner > .ui-icon, .ui-btn-icon-right > .ui-btn-inner > .ui-icon {
    margin-top: -10px;
}
select {
    /* display: inline-block; */	/* We can't set this. This disable ability to make */
    overflow:hidden;
    white-space: nowrap;			/* Enabling this make behaviour strange when selecting the empty value if this empty value is '' instead of '&nbsp;' */
    text-overflow: ellipsis;
}
.fiche .ui-controlgroup {
    margin: 0px;
    padding-bottom: 0px;
}
div.ui-controlgroup-controls div.tabsElem {
    margin-top: 2px;
}
div.ui-controlgroup-controls div.tabsElem a {
    -webkit-box-shadow: 0 -3px 6px rgba(0,0,0,.2);
    box-shadow: 0 -3px 6px rgba(0,0,0,.2);
}
div.ui-controlgroup-controls div.tabsElem a#active {
    -webkit-box-shadow: 0 -3px 6px rgba(0,0,0,.3);
    box-shadow: 0 -3px 6px rgba(0,0,0,.3);
}

a.tab span.ui-btn-inner
{
    border: none;
    padding: 0;
}

.ui-link {
    color: <?php echo $colortext; ?>;
}
.liste_titre .ui-link {
    color: <?php print $colorftitle; ?> !important;
}

a.ui-link {
    word-wrap: break-word;
}

/* force wrap possible onto field overflow does not works */
.formdoc .ui-btn-inner
{
    white-space: normal;
    overflow: hidden;
    text-overflow: clip; /* "hidden" : do not exists as a text-overflow value (https://developer.mozilla.org/fr/docs/Web/CSS/text-overflow) */
}

/* Warning: setting this may make screen not beeing refreshed after a combo selection */
/*.ui-body-c {
background: #fff;
}*/

div.ui-radio, div.ui-checkbox {
    display: inline-block;
    border-bottom: 0px !important;
}
.ui-checkbox input, .ui-radio input {
    height: auto;
    width: auto;
    margin: 4px;
    position: static;
}
div.ui-checkbox label+input, div.ui-radio label+input {
    position: absolute;
}
.ui-mobile fieldset {
    padding-bottom: 10px; margin-bottom: 4px; border-bottom: 1px solid #AAAAAA !important;
}

ul.ulmenu {
    border-radius: 0;
    -webkit-border-radius: 0;
}

.ui-field-contain label.ui-input-text {
    vertical-align: middle !important;
}
.ui-mobile fieldset {
    border-bottom: none !important;
}

/* Style for first level menu with jmobile */
.ui-li .ui-btn-inner a.ui-link-inherit, .ui-li-static.ui-li {
    padding: 1em 15px;
    display: block;
}
.ui-btn-up-c {
    font-weight: normal;
}
.ui-focus, .ui-btn:focus {
    -webkit-box-shadow: none;
    box-shadow: none;
}
.ui-bar-b {
    /*border: 1px solid #888;*/
    border: none;
    background: none;
    text-shadow: none;
    color: var(--colortexttitlenotab) !important;
}
.ui-bar-b, .lilevel0 {
    background-repeat: repeat-x;
    border: none;
    background: none;
    text-shadow: none;
    color: var(--colortexttitlenotab) !important;
}
.alilevel0 {
    font-weight: normal !important;
}

.ui-li.ui-last-child, .ui-li.ui-field-contain.ui-last-child {
    border-bottom-width: 0px !important;
}
.alilevel0 {
    color: <?php print $colorftitle; ?> !important;
}
.ulmenu {
    box-shadow: none !important;
    border-bottom: 1px solid #ccc;
}
.ui-btn-icon-right {
    border-right: 1px solid #ccc !important;
}
.ui-body-c {
    border: 1px solid #ccc;
    text-shadow: none;
}
.ui-btn-up-c, .ui-btn-hover-c {
    /* border: 1px solid #ccc; */
    text-shadow: none;
}
.ui-body-c .ui-link, .ui-body-c .ui-link:visited, .ui-body-c .ui-link:hover {
    color: rgb(<?php echo $colortextlink; ?>);
}
.ui-btn-up-c .vsmenudisabled {
    color: #<?php echo $colorshadowtitle; ?> !important;
    text-shadow: none !important;
}
/*
.ui-btn-up-c {
    background: transparent;
}

div.tabsElem a.tab {
    background: transparent;
}

.ui-controlgroup-horizontal .ui-btn.ui-first-child {
-webkit-border-top-left-radius: 6px;
border-top-left-radius: 6px;
}
.ui-controlgroup-horizontal .ui-btn.ui-last-child {
-webkit-border-top-right-radius: 6px;
border-top-right-radius: 6px;
}*/

.alilevel1 {
	color: var(--colortexttitlenotab) !important;
}
.lilevel1 {
    border-top: 2px solid #444;
    background: #fff ! important;
}
.lilevel1 div div a {
    font-weight: bold !important;
}
.lilevel2
{
    padding-left: 22px;
    background: #fff ! important;
}
.lilevel3
{
    padding-left: 44px;
    background: #fff ! important;
}
.lilevel4
{
    padding-left: 66px;
    background: #fff ! important;
}
.lilevel5
{
    padding-left: 88px;
    background: #fff ! important;
}

/* ============================================================================== */
/*  POS                                                                           */
/* ============================================================================== */

.menu_choix1 a {
    background: url('<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/menus/money.png', 1) ?>') top left no-repeat;
    background-position-y: 15px;
}

.menu_choix2 a {
    background: url('<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/menus/home.png', 1) ?>') top left no-repeat;
    background-position-y: 15px;
}
.menu_choix1,.menu_choix2 {
    font-size: 1.4em;
    text-align: left;
    border: 1px solid #666;
    margin-right: 20px;
}
.menu_choix1 a, .menu_choix2 a {
    display: block;
    color: #fff;
    text-decoration: none;
    padding-top: 18px;
    padding-left: 54px;
    font-size: 14px;
    height: 40px;
}
.menu_choix1 a:hover,.menu_choix2 a:hover {
    color: #6d3f6d;
}
.menu li.menu_choix1 {
    padding-top: 6px;
    padding-right: 10px;
    padding-bottom: 2px;
}
.menu li.menu_choix2 {
    padding-top: 6px;
    padding-right: 10px;
    padding-bottom: 2px;
}
@media only screen and (max-width: 767px)
{
    .menu_choix1 a, .menu_choix2 a {
        background-size: 36px 36px;
        background-position-y: 6px;
        padding-left: 40px;
    }
    .menu li.menu_choix1, .menu li.menu_choix2 {
        padding-left: 4px;
        padding-right: 0;
    }
    .liste_articles {
        margin-right: 0 !important;
    }
}

/* ============================================================================== */
/*  Public                                                                        */
/* ============================================================================== */

/* The theme for public pages */
.public_body {
    margin: 20px;
}
.public_border {
    border: 1px solid #888;
}
.publicnewmemberform div.tabBarWithBottom {
    border: 1px solid #e8e8e8;
    padding: 30px;
    border-radius: 8px;
    background-color: var(--colorbackgrey);
    /*box-shadow: 2px 2px 10px #ddd;*/
}

.publicnewmemberform #tablesubscribe {
    color: var(--colortextbackvmenu);
}

@media only screen and (max-width: 768px)
{
    .publicnewmemberform div.tabBarWithBottom {
        padding: 10px;
    }
}

/* ============================================================================== */
/* Ticket module                                                                  */
/* ============================================================================== */

#KWwithajax ul {
    padding-left: 20px;
}

.ticketpublictable td {
    height: 2.2em;
}

.ticketpublicarea {
    margin-left: 15%;
    margin-right: 15%;
}
.publicnewticketform {
    /* margin-top: 25px !important; */
}
.ticketlargemargin {
    padding-left: 50px;
    padding-right: 50px;
    padding-top: 30px;
}
@media only screen and (max-width: 768px)
{
    .ticketlargemargin {
        padding-left: 5px; padding-right: 5px;
        padding-top: 10px;
    }
    .ticketpublicarea {
        margin-left: 10px;
        margin-right: 10px;
    }
}

#cd-timeline {
    position: relative;
    padding: 2em 0;
    margin-bottom: 2em;
}
#cd-timeline::before {
    /* this is the vertical line */
    content: '';
    position: absolute;
    top: 0;
    left: 18px;
    height: 100%;
    width: 4px;
    background: #d7e4ed;
}
@media only screen and (min-width: 1170px) {
    #cd-timeline {
        margin-bottom: 3em;
    }
    #cd-timeline::before {
        left: 50%;
        margin-left: -2px;
    }
}

.cd-timeline-block {
    position: relative;
    margin: 2em 0;
}
.cd-timeline-block:after {
    content: "";
    display: table;
    clear: both;
}
.cd-timeline-block:first-child {
    margin-top: 0;
}
.cd-timeline-block:last-child {
    margin-bottom: 0;
}
@media only screen and (min-width: 1170px) {
    .cd-timeline-block {
        margin: 4em 0;
    }
    .cd-timeline-block:first-child {
        margin-top: 0;
    }
    .cd-timeline-block:last-child {
        margin-bottom: 0;
    }
}

.cd-timeline-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 0 0 4px white, inset 0 2px 0 rgba(0, 0, 0, 0.08), 0 3px 0 4px rgba(0, 0, 0, 0.05);
    background: #d7e4ed;
}
.cd-timeline-img img {
    display: block;
    width: 24px;
    height: 24px;
    position: relative;
    left: 50%;
    top: 50%;
    margin-left: -12px;
    margin-top: -12px;
}
.cd-timeline-img.cd-picture {
    background: #75ce66;
}
.cd-timeline-img.cd-movie {
    background: #c03b44;
}
.cd-timeline-img.cd-location {
    background: #f0ca45;
}
@media only screen and (min-width: 1170px) {
    .cd-timeline-img {
        width: 60px;
        height: 60px;
        left: 50%;
        margin-left: -30px;
        /* Force Hardware Acceleration in WebKit */
        -webkit-transform: translateZ(0);
        -webkit-backface-visibility: hidden;
    }
    .cssanimations .cd-timeline-img.is-hidden {
        visibility: hidden;
    }
    .cssanimations .cd-timeline-img.bounce-in {
        visibility: visible;
        -webkit-animation: cd-bounce-1 0.6s;
        -moz-animation: cd-bounce-1 0.6s;
        animation: cd-bounce-1 0.6s;
    }
}

@-webkit-keyframes cd-bounce-1 {
    0% {
        opacity: 0;
        -webkit-transform: scale(0.5);
    }

    60% {
        opacity: 1;
        -webkit-transform: scale(1.2);
    }

    100% {
        -webkit-transform: scale(1);
    }
}
@-moz-keyframes cd-bounce-1 {
    0% {
        opacity: 0;
        -moz-transform: scale(0.5);
    }

    60% {
        opacity: 1;
        -moz-transform: scale(1.2);
    }

    100% {
        -moz-transform: scale(1);
    }
}
@keyframes cd-bounce-1 {
    0% {
        opacity: 0;
        -webkit-transform: scale(0.5);
        -moz-transform: scale(0.5);
        -ms-transform: scale(0.5);
        -o-transform: scale(0.5);
        transform: scale(0.5);
    }

    60% {
        opacity: 1;
        -webkit-transform: scale(1.2);
        -moz-transform: scale(1.2);
        -ms-transform: scale(1.2);
        -o-transform: scale(1.2);
        transform: scale(1.2);
    }

    100% {
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -o-transform: scale(1);
        transform: scale(1);
    }
}
.cd-timeline-content {
    position: relative;
    margin-left: 60px;
    background: white;
    border-radius: 0.25em;
    padding: 1em;
    background-image: -o-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
    background-image: -moz-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
    background-image: -webkit-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
    background-image: -ms-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
    background-image: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
}
.cd-timeline-content:after {
    content: "";
    display: table;
    clear: both;
}
.cd-timeline-content h2 {
    color: #303e49;
}
.cd-timeline-content .cd-date {
    font-size: 13px;
    font-size: 0.8125rem;
}
.cd-timeline-content .cd-date {
    display: inline-block;
}
.cd-timeline-content p {
    margin: 1em 0;
    line-height: 1.6;
}

.cd-timeline-content .cd-date {
    float: left;
    padding: .2em 0;
    opacity: .7;
}
.cd-timeline-content::before {
    content: '';
    position: absolute;
    top: 16px;
    right: 100%;
    height: 0;
    width: 0;
    border: 7px solid transparent;
    border-right: 7px solid white;
}
@media only screen and (min-width: 768px) {
    .cd-timeline-content h2 {
        font-size: 20px;
        font-size: 1.25rem;
    }
    .cd-timeline-content {
        font-size: 16px;
        font-size: 1rem;
    }
    .cd-timeline-content .cd-read-more, .cd-timeline-content .cd-date {
        font-size: 14px;
        font-size: 0.875rem;
    }
}
@media only screen and (min-width: 1170px) {
    .cd-timeline-content {
        margin-left: 0;
        padding: 1.6em;
        width: 43%;
    }
    .cd-timeline-content::before {
        top: 24px;
        left: 100%;
        border-color: transparent;
        border-left-color: white;
    }
    .cd-timeline-content .cd-read-more {
        float: left;
    }
    .cd-timeline-content .cd-date {
        position: absolute;
        width: 55%;
        left: 115%;
        top: 6px;
        font-size: 16px;
        font-size: 1rem;
    }
    .cd-timeline-block:nth-child(even) .cd-timeline-content {
        float: right;
    }
    .cd-timeline-block:nth-child(even) .cd-timeline-content::before {
        top: 24px;
        left: auto;
        right: 100%;
        border-color: transparent;
        border-right-color: white;
    }
    .cd-timeline-block:nth-child(even) .cd-timeline-content .cd-read-more {
        float: right;
    }
    .cd-timeline-block:nth-child(even) .cd-timeline-content .cd-date {
        left: auto;
        right: 115%;
        text-align: right;
    }

}

/* Pagination */
div.refidpadding {
    /* padding-top: 3px; */
}
div.refid {
    font-weight: bold;
	color: var(--colortexttitlenotab);
    font-size: 160%;
}
div.refidno	{
    padding-top: 2px;
    font-weight: normal;
    color: var(--colorfline);
    font-size: <?php print $fontsize ?>px;
    line-height: 21px;
}
div.refidno form {
    display: inline-block;
}
div.pagination {
    float: right;
}
div.pagination a {
    font-weight: normal;
}
div.pagination ul
{
    list-style: none;
    display: inline-block;
    padding-left: 0px;
    padding-right: 0px;
    margin: 0;
}
div.pagination li {
    display: inline-block;
    padding-left: 0px;
    padding-right: 0px;
    padding-top: 6px;
    padding-bottom: 5px;
}
.pagination {
    display: inline-block;
    padding-left: 0;
    border-radius: 4px;
}

div.pagination li.pagination a,
div.pagination li.pagination span {
    padding: 6px 12px;
    padding-top: 8px;
    line-height: 1.42857143;
    color: var(--colortextlink);
    text-decoration: none;
}
div.pagination li.pagination span.inactive {
    cursor: default;
    color: var(--colortextlink);
}

div.pagination li.litext {
    padding-top: 8px;
}
div.pagination li.litext a {
    border: none;
    padding-right: 10px;
    padding-left: 4px;
    font-weight: bold;
}
div.pagination li.noborder a:focus,
div.pagination li.noborder a:hover {
    border: none;
    background-color: transparent;
}
div.pagination li:first-child a,
div.pagination li:first-child span {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}
div.pagination li:last-child a,
div.pagination li:last-child span {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
div.pagination li a:hover,
div.pagination li span:hover,
div.pagination li a:focus,
div.pagination li span:focus {
	color: var(--colortextbacktab);
	background-color: transparent;
    border-color: rgba(0,0,0, .24);
}
div.pagination li .active a,
div.pagination li .active span,
div.pagination li .active a:hover,
div.pagination li .active span:hover,
div.pagination li .active a:focus,
div.pagination li .active span:focus {
    z-index: 2;
    color: #fff;
    cursor: default;
    background-color: <?php print $maincolor; ?>;
    border-color: #337ab7;
}
div.pagination .disabled span,
div.pagination .disabled span:hover,
div.pagination .disabled span:focus,
div.pagination .disabled a,
div.pagination .disabled a:hover,
div.pagination .disabled a:focus {
    color: #777;
    cursor: not-allowed;
    background-color: #fff;
    border-color: rgba(0,0,0, .24);
}
div.pagination li.pagination .active {
    text-decoration: underline;
    box-shadow: none;
}
.paginationafterarrows .nohover {
    box-shadow: none !important;
}
div.pagination li.paginationafterarrows {
    margin-left: 10px;
}
.paginationatbottom {
    margin-top: 9px;
}

/* Set the color for hover lines */
.oddeven:hover, .evenodd:hover, .impair:hover, .pair:hover
{
    background: <?php echo $colorbline_hover; ?> !important;		/* Must be background to be stronger than background of odd or even */
	color: <?php echo $colorfline_hover; ?> !important;
}
.tredited, .tredited td {
    background: <?php echo $colorbline_hover; ?> !important;   /* Must be background to be stronger than background of odd or even */
	color: <?php echo $colorfline_hover; ?> !important;
    border-bottom: 0 !important;
}
.treditedlinefordate {
    background: <?php echo $colorbline_hover; ?> !important;   /* Must be background to be stronger than background of odd or even */
	color: <?php echo $colorfline_hover; ?> !important;
    border-bottom: 0px;
}
<?php if ($colorbline_hover) { ?>
.highlight {
    background: <?php echo $colorbline_hover; ?> !important;   /* Must be background to be stronger than background of odd or even */
	color: <?php echo $colorfline_hover; ?> !important;
}
<?php } ?>

.nohover:hover {
    background: unset;
}
.nohoverborder:hover {
    border: unset;
    box-shadow: unset;
    -webkit-box-shadow: unset;
}
.oddeven, .evenodd, .impair, .nohover .impair:hover, tr.impair td.nohover, .tagtr.oddeven
{
    font-family: var(--fontfamilydol);
    margin-bottom: 1px;
	color: var(--colortext);
}
.impair, .nohover .impair:hover, tr.impair td.nohover
{
    background: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?>;
}
#GanttChartDIV {
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?>;
}

.oddeven, .evenodd, .pair, .nohover .pair:hover, tr.pair td.nohover, .tagtr.oddeven {
    font-family: var(--fontfamilydol);
    margin-bottom: 1px;
	color: var(--colortext);
}
.pair, .nohover .pair:hover, tr.pair td.nohover {
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?>;
}

table.dataTable tr.oddeven {
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> !important;
}

/* For no hover style */
td.oddeven, tr.nohover td, form.nohover, form.nohover:hover {
    /*
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> !important;
    background: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> !important;
    */
}
td.evenodd {
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> !important;
    background: #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> !important;
}
.trforbreak td {
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklinebreak)); ?> !important;
}
.trforbreak td, table.noborder tr.trforbreak td a:link {
    color: #000;
}

table.dataTable td {
    padding: 5px 8px 5px 8px !important;
}
tr.pair td, tr.impair td, form.impair div.tagtd, form.pair div.tagtd, div.impair div.tagtd, div.pair div.tagtd, div.liste_titre div.tagtd {
    padding: 7px 8px 7px 8px;
    border-bottom: 1px solid #ddd;
}
form.pair, form.impair {
    font-weight: normal;
}
form.tagtr:last-of-type div.tagtd, tr.pair:last-of-type td, tr.impair:last-of-type td {
    border-bottom: 0px !important;
}
tr.nobottom td {
    border-bottom: 0px !important;
}
div.tableforcontact form.tagtr:last-of-type div.tagtd {
    border-bottom: 1px solid #ddd !important;
}
tr.pair td .nobordernopadding tr td, tr.impair td .nobordernopadding tr td {
    border-bottom: 0px !important;
}
table.nobottomiftotal tr.liste_total td {
	background-color: <?php print colorDarker($colorbtitle, 5); ?>;
    border-bottom: 0px !important;
}
table.nobottom, td.nobottom {
    border-bottom: 0px !important;
}
div.liste_titre .tagtd {
    vertical-align: middle;
}
div.liste_titre {
    min-height: 26px !important;	/* We cant use height because it's a div and it should be higher if content is more. but min-height does not work either for div */

    padding-top: 2px;
    padding-bottom: 2px;
}
div.liste_titre_bydiv {
    border-top-width: <?php echo $borderwidth ?>px;
	border-top-color: var(--colortopbordertitle1);
    border-top-style: solid;

    border-collapse: collapse;
    display: table;
    padding: 2px 0px 2px 0;
    box-shadow: none;
    /*width: calc(100% - 1px);	1px more, i don't know why so i remove */
    width: calc(100%);
}
tr.liste_titre, tr.liste_titre_sel, form.liste_titre, form.liste_titre_sel, table.dataTable.tr, tagtr.liste_titre
{
    height: 26px !important;
}
div.colorback	/* for the form "assign user" on time spent view */
{
    background: #f8f8f8;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ddd;
}
div.liste_titre_bydiv, .liste_titre div.tagtr, tr.liste_titre, tr.liste_titre_sel, .tagtr.liste_titre, .tagtr.liste_titre_sel, form.liste_titre, form.liste_titre_sel, table.dataTable thead tr
{
    /*background: rgb(<?php echo $colorbacktitle1; ?>);*/
    /*font-weight: <?php echo $useboldtitle ? 'bold' : 'normal'; ?>;*/
    font-weight: normal;

    color: <?php print $colorftitle; ?>;
    font-family: var(--fontfamilydol);
    text-align: <?php echo $left; ?>;
}
tr.liste_titre th, tr.liste_titre td, th.liste_titre
{
	border-bottom: 1px solid var(--colortopbordertitle1);
}
tr.liste_titre:first-child th, tr:first-child th.liste_titre {
    /*    border-bottom: 1px solid #ddd ! important; */
    border-bottom: unset;
}
tr.liste_titre th, th.liste_titre, tr.liste_titre td, td.liste_titre, form.liste_titre div
{
    font-family: var(--fontfamilydol);
    font-weight: <?php echo $useboldtitle ? 'bold' : 'normal'; ?>;
    vertical-align: middle;
    height: 38px;
}
tr.liste_titre th a, th.liste_titre a, tr.liste_titre td a, td.liste_titre a, form.liste_titre div a, div.liste_titre a {
    text-shadow: none !important;
}
tr.liste_titre_topborder td {
    border-top-width: <?php echo $borderwidth; ?>px;
	border-top-color: var(--colortopbordertitle1);
    border-top-style: solid;
}
.liste_titre td a {
    text-shadow: none !important;
    color: <?php print $colorftitle; ?>;
}
.liste_titre td a.notasortlink {
    color: rgb(<?php echo $colortextlink; ?>);
}
.liste_titre td a.notasortlink:hover {
    background: transparent;
}
tr.liste_titre:last-child th.liste_titre, tr.liste_titre:last-child th.liste_titre_sel, tr.liste_titre td.liste_titre, tr.liste_titre td.liste_titre_sel, form.liste_titre div.tagtd {				/* For last line of table headers only */
    /* border-bottom: 1px solid #ddd; */
    border-bottom: unset;
}

div.liste_titre {
    padding-left: 3px;
}
tr.liste_titre_sel th, th.liste_titre_sel, tr.liste_titre_sel td, td.liste_titre_sel, form.liste_titre_sel div
{
	font-family: var(--fontfamilydol);
	color: <?php print $colorftitle; ?> !important;
	font-weight: bold;
	background-color: <?php print colorDarker($colorbtitle, 5); ?>;
	/* Test
	text-decoration: underline;
	border-bottom: 8px solid <?php print $colorftitle; ?> !important;
	border-radius: 0.25rem;
	*/
}
input.liste_titre {
    background: transparent;
    border: 0px;
}
.listactionlargetitle .liste_titre {
    line-height: 24px;
}
.noborder tr.liste_total td, tr.liste_total td, form.liste_total div, .noborder tr.liste_total_wrap td, tr.liste_total_wrap td, form.liste_total_wrap div {
	color: var(--colortext);
	font-weight: bold;
}
.noborder tr.liste_total td, tr.liste_total td, form.liste_total div {
    white-space: nowrap;
}
.noborder tr.liste_total_wrap td, tr.liste_total_wrap td, form.liste_total_wrap div {
    white-space: normal;
}
form.liste_total div {
    border-top: 1px solid #DDDDDD;
}
tr.liste_sub_total, tr.liste_sub_total td {
    border-bottom: 1px solid #aaa;
}
/* to avoid too much border on contract card */
.tableforservicepart1 .impair, .tableforservicepart1 .pair, .tableforservicepart2 .impair, .tableforservicepart2 .pair {
    background: #FFF;
}
.tableforservicepart1 tbody tr td, .tableforservicepart2 tbody tr td {
    border-bottom: none;
}
table.tableforservicepart1:first-of-type tr:first-of-type td {
    border-top: 1px solid #888;
}
table.tableforservicepart1 tr td {
    border-top: 0px;
}

.paymenttable, .margintable {
    /*border-top-width: <?php echo $borderwidth ?>px !important;
	border-top-color: <?php echo $colortopbordertitle1 ?> !important;
	border-top-style: solid !important;*/
    border-top: none !important;
    margin: 0px 0px 0px 0px !important;
}
table.noborder.paymenttable {
    border-bottom: none !important;
}
.paymenttable tr td:first-child, .margintable tr td:first-child
{
    padding-left: 2px;
}
.paymenttable, .margintable tr td {
    height: 22px;
}

/* Disable-Enable shadows */
.noshadow {
    -webkit-box-shadow: 0px 0px 0px #DDD !important;
    box-shadow: 0px 0px 0px #DDD !important;
}
.shadow {
    -webkit-box-shadow: 2px 2px 5px #CCC !important;
    box-shadow: 2px 2px 5px #CCC !important;
}

div.tabBar .noborder {
    -webkit-box-shadow: 0px 0px 0px #DDD !important;
    box-shadow: 0px 0px 0px #DDD !important;
}

#tablelines tr.liste_titre td, .paymenttable tr.liste_titre td, .margintable tr.liste_titre td, .tableforservicepart1 tr.liste_titre td {
	border-bottom: 1px solid var(--colortopbordertitle1) !important;
}
#tablelines tr td {
    height: unset;
}

/* Prepare to remove class pair - impair */
.noborder > tbody > tr:nth-child(even):not(.liste_titre), .liste > tbody > tr:nth-child(even):not(.liste_titre),
div:not(.fichecenter):not(.fichehalfleft):not(.fichehalfright):not(.ficheaddleft) > .border > tbody > tr:nth-of-type(even):not(.liste_titre), .liste > tbody > tr:nth-of-type(even):not(.liste_titre),
div:not(.fichecenter):not(.fichehalfleft):not(.fichehalfright):not(.ficheaddleft) .oddeven.tagtr:nth-of-type(even):not(.liste_titre)
{
    background: linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair2)); ?> 100%);
    background: -o-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair2)); ?> 100%);
    background: -moz-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair2)); ?> 100%);
    background: -webkit-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair2)); ?> 100%);
    background: -ms-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair2)); ?> 100%);
}
.noborder > tbody > tr:nth-child(even):not(:last-child) td:not(.liste_titre), .liste > tbody > tr:nth-child(even):not(:last-child) td:not(.liste_titre),
.noborder .oddeven.tagtr:nth-child(even):not(:last-child) .tagtd:not(.liste_titre)
{
	border-bottom: 1px solid var(--colortopbordertitle1);
}

.noborder > tbody > tr:nth-child(odd):not(.liste_titre), .liste > tbody > tr:nth-child(odd):not(.liste_titre),
div:not(.fichecenter):not(.fichehalfleft):not(.fichehalfright):not(.ficheaddleft) > .border > tbody > tr:nth-of-type(odd):not(.liste_titre), .liste > tbody > tr:nth-of-type(odd):not(.liste_titre),
div:not(.fichecenter):not(.fichehalfleft):not(.fichehalfright):not(.ficheaddleft) .oddeven.tagtr:nth-of-type(odd):not(.liste_titre)
{
    background: linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair2)); ?> 100%);
    background: -o-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair2)); ?> 100%);
    background: -moz-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair2)); ?> 100%);
    background: -webkit-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair2)); ?> 100%);
    background: -ms-linear-gradient(to bottom, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair1)); ?> 85%, #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair2)); ?> 100%);
}
.noborder > tbody > tr:nth-child(odd):not(:last-child) td:not(.liste_titre), .liste > tbody > tr:nth-child(odd):not(:last-child) td:not(.liste_titre),
.noborder .oddeven.tagtr:nth-child(odd):not(:last-child) .tagtd:not(.liste_titre)
{
	border-bottom: 1px solid var(--colortopbordertitle1);
}

ul.noborder li:nth-child(even):not(.liste_titre) {
    background-color: #<?php echo colorArrayToHex(colorStringToArray($colorbacklinepair2)); ?> !important;
}

/* ============================================================================== */
/*	Multiselect with checkbox													  */
/* ============================================================================== */

ul.ulselectedfields {
    z-index: 90;			/* To have the select box appears on first plan even when near buttons are decorated by jmobile */
}
dl.dropdown {
    margin:0px;
    padding:0px;
    margin-left: 2px;
    margin-right: 2px;
    vertical-align: text-bottom;
    display: inline-block;
}
.dropdown dd, .dropdown dt {
    margin:0px;
    padding:0px;
}
.dropdown ul {
    margin: -1px 0 0 0;
    text-align: left;
}
.dropdown dd {
    position:relative;
}
.dropdown dt a {
    display:block;
    overflow: hidden;
    border:0;
}
.dropdown dt a span, .multiSel span {
    cursor:pointer;
    display:inline-block;
    padding: 0 3px 2px 0;
}
.dropdown span.value {
    display:none;
}
.dropdown dd ul {
	background-color: <?php print $bgnavtop_hover; ?>;
	border: 1px solid var(--colorboxstatsborder);
    display:none;
    right:0px;						/* pop is align on right */
    padding: 2px 15px 2px 5px;
    position:absolute;
    top:2px;
    list-style:none;
    max-height: 264px;
    overflow: auto;
}
.dropdown dd ul.selectedfieldsleft {
    <?php echo $right; ?>: auto;
}
.dropdown dd ul li {
    white-space: nowrap;
    font-weight: normal;
    padding: 2px;
}
.dropdown dd ul li:hover {
	background: var(--colorbacklinepairhover);
}
.dropdown dd ul li input[type="checkbox"] {
	margin-<?php echo $right; ?>: 3px;
}
.dropdown dd ul li a, .dropdown dd ul li span {
    padding: 3px;
    display: block;
}
.dropdown dd ul li span {
}
.dropdown dd ul li a:hover,
.dropdown dd ul li a:focus {
    background-color:#fff;
}

img.loginphoto {
    border-radius: 2px;
    width: 16px;
    height: 16px;
}
.span-icon-user {
    background: url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/object_user.png',1); ?>) no-repeat scroll 7px 7px;
}
.span-icon-password {
    background-image: url(<?php echo dol_buildpath($path.'/theme/'.$theme.'/img/lock.png',1); ?>);
    background-repeat: no-repeat;
}

/* ============================================================================== */
/* Compatibility Multicompany													  */
/* ============================================================================== */
#entity {
    width: 280px !important;
    padding-left: 10px;
}

.dropdown-mc-image {
    color: #ffffff ;
}

.atoplogin #mc-dropdown-icon {
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
    color: <?php print $bgnavleft_txt; ?>;
    <?php } else { ?>
    color: <?php print $bgnavtop_txt; ?>;
    <?php } ?>
}

/* ============================================================================== */
/* Compatibility Infrassearch													  */
/* ============================================================================== */
input#sew_keyword {
	background-color: var(--inputbackgroundcolor) !important;
    width: 100% !important;
    line-height: 28px;
}

/* ============================================================================== */
/*  Markdown rendering                                                             */
/* ============================================================================== */

.imgmd {
	width: 90%;
}
.moduledesclong h1 {
	padding-top: 10px;
	padding-bottom: 20px;
}


/* ============================================================================== */
/*  JMobile - Android                                                             */
/* ============================================================================== */

.searchpage .tagtr .tagtd {
	padding-bottom: 3px;
}
.searchpage .tagtr .tagtd .button {
	background: unset;
	border: unset;
}

li.ui-li-divider .ui-link {
	color: #FFF !important;
}
.ui-btn {
	margin: 0 2px;
}
a.ui-link, a.ui-link:hover, .ui-btn:hover, span.ui-btn-text:hover, span.ui-btn-inner:hover {
	text-decoration: none !important;
}
.ui-body-c {
	background: #fff;
}

.ui-btn-inner {
	min-width: .4em;
	padding-left: 6px;
	padding-right: 6px;
	font-size: <?php print is_numeric($fontsize) ? $fontsize.'px' : $fontsize; ?>;
	/* white-space: normal; */		/* Warning, enable this break the truncate feature */
}
.ui-btn-icon-right .ui-btn-inner {
	padding-right: 30px;
}
.ui-btn-icon-left .ui-btn-inner {
	padding-left: 30px;
}
.ui-select .ui-btn-icon-right .ui-btn-inner {
	padding-right: 30px;
}
.ui-select .ui-btn-icon-left .ui-btn-inner {
	padding-left: 30px;
}
.ui-select .ui-btn-icon-right .ui-icon {
	right: 8px;
}
.ui-btn-icon-left > .ui-btn-inner > .ui-icon, .ui-btn-icon-right > .ui-btn-inner > .ui-icon {
	margin-top: -10px;
}
select {
	/* display: inline-block; */	/* We can't set this. This disable ability to make */
	overflow:hidden;
	white-space: nowrap;			/* Enabling this make behaviour strange when selecting the empty value if this empty value is '' instead of '&nbsp;' */
	text-overflow: ellipsis;
}
.fiche .ui-controlgroup {
	margin: 0px;
	padding-bottom: 0px;
}
div.ui-controlgroup-controls div.tabsElem
{
	margin-top: 2px;
}
div.ui-controlgroup-controls div.tabsElem a
{
	-webkit-box-shadow: 0 -3px 6px rgba(0,0,0,.2);
	box-shadow: 0 -3px 6px rgba(0,0,0,.2);
}
div.ui-controlgroup-controls div.tabsElem a#active {
	-webkit-box-shadow: 0 -3px 6px rgba(0,0,0,.3);
	box-shadow: 0 -3px 6px rgba(0,0,0,.3);
}

a.tab span.ui-btn-inner
{
	border: none;
	padding: 0;
}

.ui-link {
	color: <?php echo $colortext; ?>;
}
.liste_titre .ui-link {
	color: <?php print $colorftitle; ?> !important;
}

a.ui-link {
	word-wrap: break-word;
}

/* force wrap possible onto field overflow does not works */
.formdoc .ui-btn-inner
{
	white-space: normal;
	overflow: hidden;
	text-overflow: clip; /* "hidden" : do not exists as a text-overflow value (https://developer.mozilla.org/fr/docs/Web/CSS/text-overflow) */
}

/* Warning: setting this may make screen not beeing refreshed after a combo selection */
/*.ui-body-c {
	background: #fff;
}*/

div.ui-radio, div.ui-checkbox
{
	display: inline-block;
	border-bottom: 0px !important;
}
.ui-checkbox input, .ui-radio input {
	height: auto;
	width: auto;
	margin: 4px;
	position: static;
}
div.ui-checkbox label+input, div.ui-radio label+input {
	position: absolute;
}
.ui-mobile fieldset
{
	padding-bottom: 10px; margin-bottom: 4px; border-bottom: 1px solid #AAAAAA !important;
}

ul.ulmenu {
	border-radius: 0;
	-webkit-border-radius: 0;
}

.ui-field-contain label.ui-input-text {
	vertical-align: middle !important;
}
.ui-mobile fieldset {
	border-bottom: none !important;
}

/* Style for first level menu with jmobile */
.ui-li .ui-btn-inner a.ui-link-inherit, .ui-li-static.ui-li {
	padding: 1em 15px;
	display: block;
}
.ui-btn-up-c {
	font-weight: normal;
}
.ui-focus, .ui-btn:focus {
	-webkit-box-shadow: none;
	box-shadow: none;
}
.ui-bar-b {
	/*border: 1px solid #888;*/
	border: none;
	background: none;
	text-shadow: none;
	color: var(--colortexttitlenotab) !important;
}
.ui-bar-b, .lilevel0 {
	background-repeat: repeat-x;
	border: none;
	background: none;
	text-shadow: none;
	color: var(--colortexttitlenotab) !important;
}
.alilevel0 {
	font-weight: normal !important;
}

.ui-li.ui-last-child, .ui-li.ui-field-contain.ui-last-child {
	border-bottom-width: 0px !important;
}
.alilevel0 {
	color: <?php print $colorftitle; ?> !important;
	background: var(--colorbackmobilemenu);
}
.ulmenu {
	box-shadow: none !important;
	border-bottom: 1px solid #ccc;
}
.ui-btn-icon-right {
	border-right: 1px solid #ccc !important;
}
.ui-body-c {
	border: 1px solid #ccc;
	text-shadow: none;
}
.ui-btn-up-c, .ui-btn-hover-c {
	/* border: 1px solid #ccc; */
	text-shadow: none;
}
.ui-body-c .ui-link, .ui-body-c .ui-link:visited, .ui-body-c .ui-link:hover {
	color: rgb(<?php echo $colortextlink; ?>);
}
.ui-btn-up-c .vsmenudisabled {
	color: #<?php echo $colorshadowtitle; ?> !important;
	text-shadow: none !important;
}
.alilevel1 {
	color: var(--colortexttitlenotab) !important;
}
.lilevel1 {
	border-top: 2px solid #444;
	background: #fff ! important;
}
.lilevel1 div div a {
	font-weight: bold !important;
}
.lilevel2
{
	padding-left: 22px;
	background: #fff ! important;
}
.lilevel3
{
	padding-left: 44px;
	background: #fff ! important;
}
.lilevel4
{
	padding-left: 66px;
	background: #fff ! important;
}
.lilevel5
{
	padding-left: 88px;
	background: #fff ! important;
}



/* ============================================================================== */
/*  POS                                                                           */
/* ============================================================================== */

.menu_choix1,.menu_choix2 {
	font-size: 1.4em;
	text-align: left;
	border: 1px solid #666;
	margin-right: 20px;
}
.menu_choix1 a, .menu_choix2 a {
	display: block;
	color: #fff;
	text-decoration: none;
	padding-top: 18px;
	padding-left: 10px;
	font-size: 14px;
	height: 38px;
}
.menu_choix1 a:hover,.menu_choix2 a:hover {
	color: #6d3f6d;
}
.menu li.menu_choix1 {
	padding-top: 6px;
	padding-right: 10px;
	padding-bottom: 2px;
}
.menu li.menu_choix2 {
	padding-top: 6px;
	padding-right: 10px;
	padding-bottom: 2px;
}
@media only screen and (max-width: 767px)
{
	.menu_choix1 a, .menu_choix2 a {
		background-size: 36px 36px;
		height: 30px;
		padding-left: 40px;
	}
	.menu li.menu_choix1, .menu li.menu_choix2 {
		padding-left: 4px;
		padding-right: 0;
	}
	.liste_articles {
		margin-right: 0 !important;
	}
}


/* ============================================================================== */
/*  Public                                                                        */
/* ============================================================================== */

/* The theme for public pages */
.public_body {
	margin: 20px;
}
.public_border {
	border: 1px solid #888;
}



/* ============================================================================== */
/* Ticket module                                                                  */
/* ============================================================================== */

.ticketpublictable td {
	height: 28px;
}

.ticketpublicarea {
	width: 100%;
    margin-left: 0;
    margin-right: 0;
}
.ticketpublicarea > p {
        line-height: 3;
}
.ticketpublicarea .marginbottomonly
{
        margin-bottom: 10px !important;
}
.ticketform .bigrounded {
        white-space: normal;
        word-wrap: break-word;
}
.bigrounded > span {
    margin: 5px;
}

.publicnewticketform {
	/* margin-top: 25px !important; */
}
.ticketlargemargin {
	padding-left: 50px;
	padding-right: 50px;
	padding-top: 30px;
}
@media only screen and (max-width: 767px)
{
	.ticketlargemargin {
		padding-left: 5px; padding-right: 5px;
		padding-top: 10px;
	}
	.ticketpublicarea {
		margin-left: 10px;
		margin-right: 10px;
	}
}

#cd-timeline {
	position: relative;
	padding: 2em 0;
	margin-bottom: 2em;
}
#cd-timeline::before {
	/* this is the vertical line */
	content: '';
	position: absolute;
	top: 0;
	left: 18px;
	height: 100%;
	width: 4px;
	background: #d7e4ed;
}
@media only screen and (min-width: 1170px) {
	#cd-timeline {
		margin-bottom: 3em;
	}
	#cd-timeline::before {
		left: 50%;
		margin-left: -2px;
	}
}

.cd-timeline-block {
	position: relative;
	margin: 2em 0;
}
.cd-timeline-block:after {
	content: "";
	display: table;
	clear: both;
}
.cd-timeline-block:first-child {
	margin-top: 0;
}
.cd-timeline-block:last-child {
	margin-bottom: 0;
}
@media only screen and (min-width: 1170px) {
	.cd-timeline-block {
		margin: 4em 0;
	}
	.cd-timeline-block:first-child {
		margin-top: 0;
	}
	.cd-timeline-block:last-child {
		margin-bottom: 0;
	}
}

.cd-timeline-img {
	position: absolute;
	top: 0;
	left: 0;
	width: 40px;
	height: 40px;
	border-radius: 50%;
	box-shadow: 0 0 0 4px white, inset 0 2px 0 rgba(0, 0, 0, 0.08), 0 3px 0 4px rgba(0, 0, 0, 0.05);
	background: #d7e4ed;
}
.cd-timeline-img img {
	display: block;
	width: 24px;
	height: 24px;
	position: relative;
	left: 50%;
	top: 50%;
	margin-left: -12px;
	margin-top: -12px;
}
.cd-timeline-img.cd-picture {
	background: #75ce66;
}
.cd-timeline-img.cd-movie {
	background: #c03b44;
}
.cd-timeline-img.cd-location {
	background: #f0ca45;
}
@media only screen and (min-width: 1170px) {
	.cd-timeline-img {
		width: 60px;
		height: 60px;
		left: 50%;
		margin-left: -30px;
		/* Force Hardware Acceleration in WebKit */
		-webkit-transform: translateZ(0);
		-webkit-backface-visibility: hidden;
	}
	.cssanimations .cd-timeline-img.is-hidden {
		visibility: hidden;
	}
	.cssanimations .cd-timeline-img.bounce-in {
		visibility: visible;
		-webkit-animation: cd-bounce-1 0.6s;
		-moz-animation: cd-bounce-1 0.6s;
		animation: cd-bounce-1 0.6s;
	}
}

@-webkit-keyframes cd-bounce-1 {
	0% {
		opacity: 0;
		-webkit-transform: scale(0.5);
	}

	60% {
		opacity: 1;
		-webkit-transform: scale(1.2);
	}

	100% {
		-webkit-transform: scale(1);
	}
}
@-moz-keyframes cd-bounce-1 {
	0% {
		opacity: 0;
		-moz-transform: scale(0.5);
	}

	60% {
		opacity: 1;
		-moz-transform: scale(1.2);
	}

	100% {
		-moz-transform: scale(1);
	}
}
@keyframes cd-bounce-1 {
	0% {
		opacity: 0;
		-webkit-transform: scale(0.5);
		-moz-transform: scale(0.5);
		-ms-transform: scale(0.5);
		-o-transform: scale(0.5);
		transform: scale(0.5);
	}

	60% {
		opacity: 1;
		-webkit-transform: scale(1.2);
		-moz-transform: scale(1.2);
		-ms-transform: scale(1.2);
		-o-transform: scale(1.2);
		transform: scale(1.2);
	}

	100% {
		-webkit-transform: scale(1);
		-moz-transform: scale(1);
		-ms-transform: scale(1);
		-o-transform: scale(1);
		transform: scale(1);
	}
}
.cd-timeline-content {
	position: relative;
	margin-left: 60px;
	background: <?php echo (!empty($colorbline_hover) ? $colorbline_hover : 'white'); ?>;
	border-radius: 0.25em;
	padding: 1em;
	background-image: -o-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
	background-image: -moz-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
	background-image: -webkit-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
	background-image: -ms-linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
	background-image: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(230,230,230,0.4) 100%);
}
.cd-timeline-content:after {
	content: "";
	display: table;
	clear: both;
}
.cd-timeline-content h2 {
	color: #303e49;
}
.cd-timeline-content .cd-date {
	font-size: 13px;
	font-size: 0.8125rem;
}
.cd-timeline-content .cd-date {
	display: inline-block;
}
.cd-timeline-content p {
	margin: 1em 0;
	line-height: 1.6;
}

.cd-timeline-content .cd-date {
	float: left;
	padding: .2em 0;
	opacity: .7;
}
.cd-timeline-content::before {
	content: '';
	position: absolute;
	top: 16px;
	right: 100%;
	height: 0;
	width: 0;
	border: 7px solid transparent;
	border-right: 7px solid <?php echo (!empty($colorbline_hover) ? $colorbline_hover : 'white'); ?>;
}
@media only screen and (min-width: 768px) {
	.cd-timeline-content h2 {
		font-size: 20px;
		font-size: 1.25rem;
	}
	.cd-timeline-content {
		font-size: 16px;
		font-size: 1rem;
	}
	.cd-timeline-content .cd-read-more, .cd-timeline-content .cd-date {
		font-size: 14px;
		font-size: 0.875rem;
	}
}
@media only screen and (min-width: 1170px) {
	.cd-timeline-content {
		margin-left: 0;
		padding: 1.6em;
		width: 43%;
	}
	.cd-timeline-content::before {
		top: 24px;
		left: 100%;
		border-color: transparent;
		border-left-color: <?php echo (!empty($colorbline_hover) ? $colorbline_hover : 'white'); ?>;
	}
	.cd-timeline-content .cd-read-more {
		float: left;
	}
	.cd-timeline-content .cd-date {
		position: absolute;
		width: 55%;
		left: 115%;
		top: 6px;
		font-size: 16px;
		font-size: 1rem;
	}
	.cd-timeline-block:nth-child(even) .cd-timeline-content {
		float: right;
	}
	.cd-timeline-block:nth-child(even) .cd-timeline-content::before {
		top: 24px;
		left: auto;
		right: 100%;
		border-color: transparent;
		border-right-color: <?php echo (!empty($colorbline_hover) ? $colorbline_hover : 'white'); ?>;
	}
	.cd-timeline-block:nth-child(even) .cd-timeline-content .cd-read-more {
		float: right;
	}
	.cd-timeline-block:nth-child(even) .cd-timeline-content .cd-date {
		left: auto;
		right: 115%;
		text-align: right;
	}

}


/* ============================================================================== */
/* CSS style for debugbar                                                         */
/* ============================================================================== */

div.phpdebugbar * {
	font-weight: unset;
}
span.phpdebugbar-tooltip.phpdebugbar-tooltip-extra-wide, span.phpdebugbar-tooltip.phpdebugbar-tooltip-wide {
	width: 250px !important;
}
.phpdebugbar-indicator span.phpdebugbar-tooltip {
	opacity: .95 !important;
}
a.phpdebugbar-tab.phpdebugbar-active {
	background-image: unset !important;
}
.phpdebugbar-fa-tags:before {
	content: "\f121";
	font-weight: 600 !important;
}
.phpdebugbar-fa-tasks:before {
	content: "\f550";
	font-weight: 600 !important;
}
.phpdebugbar-fa-tags, .phpdebugbar-fa-tasks, .phpdebugbar-indicator .fa {
    font-family: var(--fontawesomeFamily) !important;
    font-weight: var(--fontawesomeWeight);
}
div.phpdebugbar-widgets-messages li.phpdebugbar-widgets-list-item span.phpdebugbar-widgets-value.phpdebugbar-widgets-warning:before,
div.phpdebugbar-widgets-messages li.phpdebugbar-widgets-list-item span.phpdebugbar-widgets-value.phpdebugbar-widgets-error:before,
div.phpdebugbar-widgets-exceptions a.phpdebugbar-widgets-editor-link:before,
div.phpdebugbar-widgets-sqlqueries span.phpdebugbar-widgets-database:before,
div.phpdebugbar-widgets-sqlqueries span.phpdebugbar-widgets-duration:before,
div.phpdebugbar-widgets-sqlqueries span.phpdebugbar-widgets-memory:before,
div.phpdebugbar-widgets-sqlqueries span.phpdebugbar-widgets-row-count:before,
div.phpdebugbar-widgets-sqlqueries span.phpdebugbar-widgets-copy-clipboard:before,
div.phpdebugbar-widgets-sqlqueries span.phpdebugbar-widgets-stmt-id:before,
div.phpdebugbar-widgets-templates span.phpdebugbar-widgets-render-time:before,
div.phpdebugbar-widgets-templates span.phpdebugbar-widgets-memory:before,
div.phpdebugbar-widgets-templates span.phpdebugbar-widgets-param-count:before,
div.phpdebugbar-widgets-templates span.phpdebugbar-widgets-type:before,
div.phpdebugbar-widgets-templates a.phpdebugbar-widgets-editor-link:before
{
    font-family: var(--fontawesomeFamily) !important;
}

/* ============================================================================== */
/* CSS style used for jCrop                                                       */
/* ============================================================================== */

.jcrop-holder { background: unset !important; }


/* ============================================================================== */
/* CSS style used for jFlot                                                       */
/* ============================================================================== */

.dol-xaxis-vertical .flot-x-axis .flot-tick-label.tickLabel {
	text-orientation: sideways;
	font-weight: 400;
	writing-mode: vertical-rl;
	white-space: nowrap;
}

/* ============================================================================== */
/* For copy-paste feature                                                         */
/* ============================================================================== */

span.clipboardCPValueToPrint, div.clipboardCPValueToPrint  {
    display: inline-block;
}
span.clipboardCPValue.hidewithsize {
    width: 0 !important;
    display: inline-block;	/* this will be modify on the fly by the copy-paste js code in lib_foot.js.php to have copy feature working */
    color: transparent;
    white-space: nowrap;
    overflow-x: hidden;
    vertical-align: middle;
}
div.clipboardCPValue.hidewithsize {
    width: 0 !important;
    display: none;
    color: transparent;
    white-space: nowrap;
}

.clipboardCPShowOnHover .clipboardCPButton {
    display: none;
}

/* To make a div popup, we must use a position absolute inside a position relative */
.clipboardCPText {
    position: relative;
}
.clipboardCPTextDivInside {
    position: absolute;
    background: #f8f8fa;
    color: #888;
    border: 1px solid #E0E0E0;
    opacity: 1;
    z-index: 20;
    padding: 2px;
    padding-left: 5px;
    padding-right: 5px;
    top: -5px;
    left: 0px;
    border-radius: 5px;
    white-space: nowrap;
    font-size: 0.9em;
    box-shadow: 1px 1px 6px #ddd;
}


/* ============================================================================== */
/* CSS style used for hrm skill/rank (may be we can remove this)                  */
/* ============================================================================== */

.radio_js_bloc_number {
    display:inline-block;
    padding:5px 7px;
    min-width:20px;
    border-radius:3px;
    border:1px solid #ccc;
    background:#eee;
    color:#555;
    cursor:pointer;
    margin:2px;
    text-align:center;
}
.radio_js_bloc_number.selected {
    transition:0.2s ease background;
    background:#888;
    color:#fff;
    border-color:#555;
}


/* ============================================================================== */
/* Virtual business card                                                          */
/* ============================================================================== */

.virtualcard-div {
    overflow: hidden;
    vertical-align: top;
    /* background: #aaa; */
}

#virtualcard-iframe {
    border: 40px solid #aaa;
    vertical-align: top;
    width: 10%;
    min-width: 100px;
    border-radius: 10px;
    aspect-ratio: 0.6;
}
.nopointervent {
    pointer-events: none;
}
.scalepreview {
    /* transform: scale(0.5); */
    zoom: 0.20;
    /* filter: blur(4px); */
}

/* ============================================================================== */
/* Drag & drop card feature                                                       */
/* ============================================================================== */
.cssDragDropArea {
    position: relative;
}
.highlightDragDropArea {
    border: 2px #000 dashed !important;
    background-color: #bbbbbb !important;
}
.highlightDragDropArea * :not(.dragDropAreaMessage *) {
    opacity:0.7;
    filter: blur(3px) grayscale(100%);
}
.dragDropAreaMessage {
    position: absolute;
    left:50%;
    top:50%;
    transform: translate(-50%, -50%);
    text-align:center;
    font-size: 2em;
}

/* ============================================================================== */
/* CSS style used for color jPicker                                               */
/* ============================================================================== */

table.jPicker {
    border: 1px solid #bbb !important;
}

/* ============================================================================== */
/* CSS style used for survey                                                      */
/* ============================================================================== */

.opensurveydescription * {
    width: 100%;
}
.imgopensurveywizard
{
    padding: 0 4px 0 4px;
}
.survey_borders {
    margin-left: 100px;
    margin-right: 100px;
    text-align: start;
}
.survey_intro {
    background-color: #f0f0f0;
    padding: 15px;
    border-radius: 8px;
}
.survey_borders .resultats .nom {
    text-align: <?php echo $left; ?>
}
.survey_borders .resultats .sujet, .survey_borders .resultats .jour {
    min-width: 100px;
}

/* ============================================================================== */
/* CSS style used for BookCal                                                     */
/* ============================================================================== */

.center.bookingtab {
    margin-left: 20px;
}
#bookinghoursection {
    width: 145px;
    height: 320px;
    overflow-y: auto;
    overflow-x: hidden;
    text-align: left;
}
.bookcalform {
    border: 1px solid #000;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

/* ============================================================================== */
/* Global options      														      */
/* ============================================================================== */
div.fiche>form>div.div-table-responsive, div.fiche>form>div.div-table-responsive-no-min {
    overflow-x: unset;
}
.div-table-responsive-no-min, div.div-table-responsive {
    overflow-x: unset;
}
.ui-datepicker {
    /*z-index: 5 !important;*/
}

/* ============================================================================== */
/* Sticky table headers columns												      */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_STICKY_HEADER_CARD')) { ?>
	div.fiche>form>div.div-table-responsive, div.fiche>form>div.div-table-responsive-no-min {
		overflow-x: unset;
	}
	.div-table-responsive-no-min, div.div-table-responsive {
		 overflow-x: unset;
    }
	tr.liste_titre th:not(#ajaxloaded_tablelines th) {
	    position: sticky;
        <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
            <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
                top: 40px;
            <?php } else { ?>
                top: 54px;
            <?php } ?>
        <?php } else { ?>
            top: 0;
        <?php } ?>
        background-color: <?php print $colorbtitle; ?>;
        z-index: 1;
    }
    tr.liste_titre.box_titre th {
        top: unset !important;
    }
    .publicnewticketform2 th {
        top: unset !important;
    }
    .fichehalfleft  tr.liste_titre th:not(#ajaxloaded_tablelines th),
    .fichehalfright  tr.liste_titre th:not(#ajaxloaded_tablelines th) {
        top: unset;
    }

    <?php if (getDolGlobalString('FIX_STICKY_TABS_CARD')) { ?>
    #id-right > .fiche > form[action*="perday.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > .tabBar > form[action*="perday.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > form[action*="perweek.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > .tabBar > form[action*="perweek.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > form[action*="permonth.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > .tabBar > form[action*="permonth.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > form[action*="bankrecords.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > .tabBar > form[action*="bankrecords.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > form[action*="prelink.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th),
    #id-right > .fiche > .tabBar > form[action*="prelink.php"] tr.liste_titre th:not(#ajaxloaded_tablelines th) {
        top: 104px !important;
    }
    <?php } ?>
<?php } ?>

/* ============================================================================== */
/* Sticky tabs card WIP     												      */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_STICKY_TABS_CARD')) { ?>
div.tabs:first-of-type, .fiche > div.tabs
{
    position: sticky;
    <?php if (getDolGlobalString('OBLYON_STICKY_TOPBAR')) { ?>
        top: 41px;
    <?php } else { ?>
        top: 0;
    <?php } ?>
    border-bottom: solid 1px #cccccc !important;
    background-color: var(--bgcolor);
    margin: 0 auto 0 0 !important;
    height: auto;
    z-index: 50;
}

#dialogforpopup .tabs {
    top: unset !important;
}
<?php } ?>

/* ============================================================================== */
/* Sticky table 1st column  												      */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_STICKY_COLUMN_FIRST') || getDolGlobalString('OBLYON_STICKY_COLUMN_FIRST')) { ?>
@media (min-width: 768px) {
    #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > * > :first-of-type:not(.actioncolumn),
    #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > * > :first-of-type,
    #id-right > .fiche > form[action*="bankrecords.php"] div.div-table-responsive > table > tbody > * > :first-of-type,
    #id-right > .fiche > .tabBar > form[action*="bankrecords.php"] div.div-table-responsive > table > tbody > * > :first-of-type,
    #id-right > .fiche > form[action*="prelink.php"] div.div-table-responsive > table > tbody > * > :first-of-type,
    #id-right > .fiche > .tabBar > form[action*="prelink.php"] div.div-table-responsive > table > tbody > * > :first-of-type {
        position: sticky;
        <?php if (getDolGlobalString('OBLYON_STICKY_LEFTBAR') && !getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU')) { ?>
            left: 230px;
        <?php } elseif (getDolGlobalString('OBLYON_STICKY_LEFTBAR') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU')) { ?>
            left: 38px;
        <?php } else { ?>
            left: 0;
        <?php } ?>
        z-index: 2;
        background-color: <?php print $colorbtitle; ?>;
        /* background: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?>;*/
        border-right: 1px solid var(--colorBorderActionColumn);
    }

    #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > * > :first-of-type.actioncolumn,
    #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > * > :first-of-type:has(.checkforselect) {
        position: sticky;
        <?php if (getDolGlobalString('OBLYON_STICKY_LEFTBAR') && !getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU')) { ?>
            left: 230px;
        <?php } elseif (getDolGlobalString('OBLYON_STICKY_LEFTBAR') && getDolGlobalString('OBLYON_EFFECT_REDUCE_LEFTMENU')) { ?>
            left: 38px;
        <?php } else { ?>
            left: 0;
        <?php } ?>
        z-index: 1;
        background-color: <?php print $colorbtitle; ?>;
        /* background: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?>;*/
        border-right: 1px solid var(--colorBorderActionColumn);
    }

    .multichoicedoc {
        left: 240px !important;
        top: -10px;
    }

    <?php if (getDolGlobalString('MAIN_CHECKBOX_LEFT_COLUMN')) { ?>
        .dropdown dd ul:not(.ulselectedfields ) {
            left: 60px;
        }
    <?php } else { ?>
        .dropdown dd ul {
            right: 30px;
        }
    <?php } ?>
}
<?php } ?>

/* ============================================================================== */
/* Sticky table last column			    							      */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_STICKY_COLUMN_LAST') || getDolGlobalString('OBLYON_STICKY_COLUMN_LAST')) { ?>
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table > tbody > * > :last-of-type,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table > tbody > * > :last-of-type,
#id-right > .fiche > form[action*="bankrecords.php"] div.div-table-responsive > table > tbody > * > :last-of-type,
#id-right > .fiche > .tabBar > form[action*="bankrecords.php"] div.div-table-responsive > table > tbody > * > :last-of-type,
#id-right > .fiche > form[action*="prelink.php"] div.div-table-responsive > table > tbody > * > :last-of-type,
#id-right > .fiche > .tabBar > form[action*="prelink.php"] div.div-table-responsive > table > tbody > * > :last-of-type {
    position: sticky;
    right: 0;
    z-index: 1;
    background-color: <?php print $colorbtitle; ?>;
    /* background: #<?php echo colorArrayToHex(colorStringToArray($colorbacklineimpair1)); ?>; */
    border-left: 1px solid #bbbbbb;
}
<?php } ?>

/* ============================================================================== */
/* Sticky total bar                                                               */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_STICKY_TOTAL_BAR')) { ?>
#id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > form[action*="perday.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > .tabBar > form[action*="perday.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > form[action*="perweek.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > .tabBar > form[action*="perweek.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > form[action*="permonth.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > .tabBar > form[action*="permonth.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > form[action*="bankrecords.php"] div.div-table-responsive > table tr.liste_total,
#id-right > .fiche > .tabBar > form[action*="bankrecords.php"] div.div-table-responsive > table tr.liste_total {
        position: sticky;
        <?php if (getDolGlobalString('FIX_STICKY_GRANDTOTAL_BAR')) { ?>
            bottom: 42px;
        <?php } else { ?>
            bottom: 0;
        <?php } ?>
        z-index: 2;
    }
<?php } ?>

/* ============================================================================== */
/* Sticky grand total bar  WIP                                                    */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_STICKY_GRANDTOTAL_BAR')) { ?>
    #id-right > .fiche > form[action*="list.php"] div.div-table-responsive > table tr.liste_grandtotal,
    #id-right > .fiche > .tabBar > form[action*="list.php"] div.div-table-responsive > table tr.liste_grandtotal {
        position: sticky;
        bottom: 0;
        z-index: 2;
    }
<?php } ?>

/* ============================================================================== */
/* Fix title in list        													  */
/* ============================================================================== */
<?php if (getDolGlobalString('FIX_TITLE_IN_LIST') && (float) DOL_VERSION >= 18.0) { ?>
    td.nobordernopadding.widthpictotitle.valignmiddle.col-picto {
        position: sticky;
        left: 10px;
    }
    td.nobordernopadding.valignmiddle.col-title {
        position: sticky;
        left: 40px;
    }
    td.nobordernopadding.center.valignmiddle.col-center {
        position: sticky;
        left: 300px;
        right: 200px;
        z-index: 1;
    }
    td.nobordernopadding.valignmiddle.right.col-right {
        position: sticky;
        right: 10px;
    }
<?php } ?>

/* ============================================================================== */
/* Option to remove Kanban view in list                                           */
/* ============================================================================== */
<?php if (getDolGlobalString('DISABLE_KANBAN_VIEW_IN_LIST') || getDolGlobalString('OBLYON_DISABLE_KANBAN_VIEW_IN_LIST')) { ?>
    .paginationafterarrows > .reposition {
        display: none;
    }
	/* pour éviter que le bouton d'ajout de consommation de temps soit masqué par la ligne précédente (/projet/tasks/time.php) */
    .paginationafterarrows > .reposition.btnTitlePlus {
        display: inline-block;
    }
<?php } ?>

/* ============================================================================== */
/* CSS style used for small screen												  */
/* ============================================================================== */
.topmenuimage {
    background-size: 22px auto;
    top: 2px;
}
.imgopensurveywizard
{
    padding: 0 4px 0 4px;
}

/* rule to reduce inverted top menu */
@media only screen and (max-width: 1200px)
{
    #tmenu_tooltipinvert .sec-nav__item {
        max-width: 120px;
    }
    #tmenu_tooltipinvert .sec-nav__item .icon {
        display: none;
    }
    .sec-nav__link {
        overflow: hidden;
        text-overflow: ".";
    }
}

/* rule to reduce inverted top menu */
@media only screen and (max-width: 1024px)
{
    #tmenu_tooltipinvert .sec-nav__item {
        max-width: 100px;
    }
    #tmenu_tooltipinvert .sec-nav__item .icon {
        display: none;
    }
    .sec-nav__sub-item {
        overflow-wrap: break-word;
    }

    div.vmenu {
    <?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU')) { ?>
        max-width: 40px;
    <?php } else { ?>
        min-width: 210px;
        max-width: 100%;
    <?php } ?>
    }

    .vmenusearchselectcombo {
         min-width: 150px;
         max-width: 100%;
     }
    .sec-nav.is-inverted {
    <?php if (getDolGlobalString('OBLYON_FULLSIZE_TOPBAR') || !empty($conf->dol_optimize_smallscreen)) { ?>
            margin-<?php print $left; ?>: 10px;
        <?php } else { ?>
            margin-<?php print $left; ?>: 10px;
        <?php } ?>
    }

    <?php if (!getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        #id-left {
            z-index: 96;
        }
    <?php } ?>
}

/* rule to reduce inverted top menu */
@media only screen and (max-width: 905px)
{
    #tmenu_tooltip {
        padding-<?php print $right; ?>: 92px;
    }

    #tmenu_tooltipinvert .sec-nav__item {
        max-width: 80px;
    }
}

/* rule to reduce top menu */
@media only screen and (max-width: 767px)
{
    #tmenu_tooltip .main-nav__item {
        max-width: 66px;
    }
    .main-nav__link {
        overflow: hidden;
        text-overflow: '.';
    }

    #tmenu_tooltipinvert .sec-nav__item {
        max-width: 60px;
    }
    #tmenu_tooltipinvert .sec-nav__item .icon {
        display: none;
    }

    div.vmenu {
    <?php if (getDolGlobalString('OBLYON_REDUCE_LEFTMENU')) { ?>
        max-width: 40px;
    <?php } else { ?>
        min-width: 130px;
    <?php } ?>
    }

    .vmenusearchselectcombo {
        min-width: 110px;
    }

    .sec-nav.is-inverted {
        <?php if (getDolGlobalString('OBLYON_FULLSIZE_TOPBAR') || !empty($conf->dol_optimize_smallscreen)) { ?>
            margin-<?php print $left; ?>: 5px;
        <?php } else { ?>
            margin-<?php print $left; ?>: 5px;
        <?php } ?>
    }
    .div-table-responsive {
        line-height: 120%;
    }
    .imgopensurveywizard, .imgautosize { width:95%; height: auto; }

    #tooltip {
        position: absolute;
        width: <?php print dol_size(350, 'width'); ?>px;
    }

    div.tabBar {
        padding-left: 0px;
        padding-right: 0px;
        -webkit-border-radius: 0;
        border-radius: 0px;
        border-right: none;
        border-left: none;
    }

    .box-flex-container {
        margin: 0 0 0 -8px !important;
    }

    #tmenu_tooltipinvert .pushy-btn,
    #tmenu_tooltip .pushy-btn { /* for v3.5 */
    <?php if (getDolGlobalString('MAIN_MENU_INVERT')) { ?>
        font-size: 18px !important;
        height: 40px;
        line-height: 40px;
    <?php } else { ?>
        font-size: 18px !important;
        height: 54px;
        line-height: 54px;
    <?php } ?>
    }

    div.login_block {
        font-size: 16px !important;
        padding-right: 5px;
    }

    .menulogocontainer {
        display: none;
    }

    .main-nav .icon {
        font-size:14px;
    }

    #tmenu_tooltip .main-nav__link {
        padding: 0 1px;
        max-width: 40px;
    }
}

/* nboftopmenuentries = <?php echo (!empty($nbtopmenuentries) ? $nbtopmenuentries : 0) ?>, fontsize=<?php echo (!empty($fontsize) ? $fontsize : 0) ?> */
/* rule to reduce top menu - 1st reduction */
@media only screen and (max-width: <?php echo (!empty($nbtopmenuentries) && !empty($fontsize) ? round($nbtopmenuentries * $fontsize * 6.7, 0) + 8 : 8); ?>px)
{
    div.tmenucenter {
        max-width: <?php echo (!empty($fontsize) ? round($fontsize * 4) : 0); ?>px;	/* size of viewport */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #<?php echo $colortextbackhmenu; ?>;
    }
    .mainmenuaspan {
        font-size: 10px;
    }
    .topmenuimage {
        background-size: 26px auto;
        margin-top: 0px;
    }
    li.tmenu, li.tmenusel {
        min-width: 32px;
    }
    div.mainmenu {
        min-width: auto;
    }
    div.tmenuleft {
        display: none;
    }
    #tmenu_tooltipinvert .sec-nav__item .icon {
        display: none;
    }
}

/* rule to reduce top menu - 2nd reduction */
@media only screen and (max-width: <?php echo (!empty($nbtopmenuentries) && !empty($fontsize) ? round($nbtopmenuentries * $fontsize * 4.5, 0) + 8 : 8); ?>px)
{
    div.tmenucenter {
        max-width: <?php echo (!empty($fontsize) ? round($fontsize * 2) : 0); ?>px;	/* size of viewport */
        text-overflow: clip;
    }
    .mainmenuaspan {
        font-size: 10px;
        padding-left: 0;
        padding-right: 0;
    }
    .topmenuimage {
        background-size: 20px auto;
        margin-top: 2px;
    }
    #tmenu_tooltipinvert .sec-nav__item .icon {
        display: none;
    }
}

/* rule to reduce top menu - 3rd reduction */
@media only screen and (max-width: 570px)
{
    #id-right {
        padding-left: unset;
    }
    /* Reduce login top right info */
    .usertext.atoplogin {
        display: none;
    }
    div#tmenu_tooltip, #tmenu_tooltipinvert {
        <?php if (GETPOST("optioncss") == 'print') {	?>
            display:none;
        <?php } else { ?>
            padding-<?php echo $right; ?>: 92px;
        <?php } ?>
    }
    div.login_block {
		vertical-align: middle;
        max-width: 120px;
        padding-right: 3px;
        display: inline-flex;
    }
    div.login_block_other {
        display: block;
        width: auto;
        min-width: 40px;
    }
    div.login_block_other .inline-block {
        display: block;
        width: auto;
    }
    li.tmenu, li.tmenusel {
        min-width: 30px;
    }

    div.tmenucenter {
        text-overflow: clip;
    }
    .topmenuimage {
        background-size: 20px auto;
        margin-top: 2px !important;
    }
    div.mainmenu {
        min-width: 20px;
    }
    .div-table-responsive {
        line-height: 120%;
    }
    #tooltip {
        position: absolute;
        width: <?php print dol_size(300,'width'); ?>px;
    }

    select {
        width: 98%;
        min-width: 0 !important;
    }
    div.divphotoref {
        padding-right: 5px;
    }
    img.photoref, div.photoref {
        border: none;
        -webkit-box-shadow: none;
        box-shadow: none;
        padding: 4px;
        height: 20px;
        width: 20px;
        object-fit: contain;
    }

    .titlefield {
        width: auto !important;		/* We want to ignore the 30%, try to use more if you can */
    }
    .tableforfield>tr>td:first-child {
        max-width: 100px;			/* but no more than 100px */
    }

    .main-nav .icon {
        font-size:12px;
    }

    #tmenu_tooltipinvert .sec-nav__item {
        max-width: 50px;
    }

    #tmenu_tooltipinvert .sec-nav__item .icon {
        display: none;
    }

    .sec-nav.is-inverted {
    <?php if (getDolGlobalString('OBLYON_SHOW_COMPNAME') || getDolGlobalString('OBLYON_FULLSIZE_TOPBAR') || !empty($conf->dol_optimize_smallscreen)) { ?>
        margin-<?php print $left; ?>: 1px;
    <?php } else { ?>
        margin-<?php print $left; ?>: 1px;
    <?php } ?>
    }

    #tmenu_tooltipinvert div.menu_contenu {
        display: none;
    }

    div.fiche {
        margin: 0 3px 0 3px;
    }

    table.table-fiche-title .col-title div.titre{
        line-height: unset;
    }

    input#addedfile {
        width: 95%;
    }
}

<?php
include dol_buildpath($path.'/theme/'.$theme.'/dropdown.inc.php', 0);
include dol_buildpath($path.'/theme/'.$theme.'/info-box.inc.php', 0);
include dol_buildpath($path.'/theme/'.$theme.'/progress.inc.php', 0);
include dol_buildpath($path.'/theme/'.$theme.'/timeline.inc.php', 0);

// Compatibility module
include dol_buildpath($path.'/theme/'.$theme.'/modules.inc.php', 0);
