<?php
if (!defined('ISLOADEDBYSTEELSHEET')) die('Must be call by steelsheet'); ?>
/* <style type="text/css" > */



/* ============================================================================== */
/* Buttons for actions                                                            */
/* ============================================================================== */

/*div.divButAction {
    margin-bottom: 1.4em;
}*/
div.tabsAction > a.butAction, div.tabsAction > a.butActionRefused, div.tabsAction > a.butActionDelete,
div.tabsAction > span.butAction, div.tabsAction > span.butActionRefused, div.tabsAction > span.butActionDelete,
div.tabsAction > div.divButAction > span.butAction,
div.tabsAction > div.divButAction > span.butActionDelete,
div.tabsAction > div.divButAction > span.butActionRefused,
div.tabsAction > div.divButAction > a.butAction,
div.tabsAction > div.divButAction > a.butActionDelete,
div.tabsAction > div.divButAction > a.butActionRefused {
    margin-bottom: 1.4em !important;
    margin-right: 0 !important;
}
div.tabsActionNoBottom > a.butAction, div.tabsActionNoBottom > a.butActionRefused {
    margin-bottom: 0 !important;
}

span.butAction, span.butActionDelete {
    cursor: pointer;
}
.paginationafterarrows .butAction {
    font-size: 0.9em;
}
.butAction, .cke_dialog_ui_button_ok {
    background: <?php print $colorButtonAction1; ?> !important;
}
:not(.center) > .butActionRefused:last-child, :not(.center) > .butAction:last-child, :not(.center) > .butActionDelete:last-child {
    margin-<?php echo $right; ?>: 0px !important;
}
.butActionRefused, .butAction, .butAction:link, .butAction:visited, .butAction:hover, .butAction:active, .butActionDelete, .butActionDelete:link, .butActionDelete:visited, .butActionDelete:hover, .butActionDelete:active {
    text-decoration: none;
    /* text-transform: uppercase; */
    font-weight: bold;

    margin: 0em <?php echo ($dol_optimize_smallscreen ? '0.6' : '0.9'); ?>em !important;
    padding: 0.6em <?php echo ($dol_optimize_smallscreen ? '0.6' : '0.7'); ?>em;
    font-family: <?php print $fontlist ?>;
    display: inline-block;
    text-align: center;
    cursor: pointer;
    color: #fff;
    background: <?php print $colorButtonAction1; ?>;
    border: 0px;

    border-top-right-radius: 0.30em !important;
    border-bottom-right-radius: 0.30em !important;
    border-top-left-radius: 0.30em !important;
    border-bottom-left-radius: 0.30em !important;
}

.butActionNew, .butActionNewRefused, .butActionNew:link, .butActionNew:visited, .butActionNew:hover, .butActionNew:active {
    text-decoration: none;
    /* text-transform: capitalize; */
    font-weight: normal;

    margin: 0em 0.3em 0 0.3em !important;
    padding: 0.2em <?php echo ($dol_optimize_smallscreen ? '0.4' : '0.7'); ?>em 0.3em;
    font-family: <?php print $fontlist ?>;
    display: inline-block;
    /* text-align: center; New button are on right of screen */
    background: <?php print $colorButtonAction2; ?>;
    cursor: pointer;
}

.tableforfieldcreate a.butActionNew>span.fa-plus-circle, .tableforfieldcreate a.butActionNew>span.fa-plus-circle:hover,
.tableforfieldedit a.butActionNew>span.fa-plus-circle, .tableforfieldedit a.butActionNew>span.fa-plus-circle:hover,
span.butActionNew>span.fa-plus-circle, span.butActionNew>span.fa-plus-circle:hover,
a.butActionNewRefused>span.fa-plus-circle, a.butActionNewRefused>span.fa-plus-circle:hover,
span.butActionNewRefused>span.fa-plus-circle, span.butActionNewRefused>span.fa-plus-circle:hover,
a.butActionNew>span.fa-list-alt, a.butActionNew>span.fa-list-alt:hover,
span.butActionNew>span.fa-list-alt, span.butActionNew>span.fa-list-alt:hover,
a.butActionNewRefused>span.fa-list-alt, a.butActionNewRefused>span.fa-list-alt:hover,
span.butActionNewRefused>span.fa-list-alt, span.butActionNewRefused>span.fa-list-alt:hover
{
	font-size: 1em;
	padding-left: 0px;
}

a.butActionNew>span.fa, a.butActionNew>span.fa:hover,
span.butActionNew>span.fa, span.butActionNew>span.fa:hover,
a.butActionNewRefused>span.fa, a.butActionNewRefused>span.fa:hover,
span.butActionNewRefused>span.fa, span.butActionNewRefused>span.fa:hover
{
	padding-<?php echo $left; ?>: 6px;
	font-size: 1.5em;
	border: none;
	box-shadow: none; webkit-box-shadow: none;
}

.butAction:hover, .cke_dialog_ui_button_ok:hover {
	background: <?php print $colorButtonAction2; ?> !important;
    -webkit-box-shadow: 0px 1px 4px 1px rgba(50, 50, 50, 0.4), 0px 0px 0px rgba(60,60,60,0.1);
    box-shadow: 0px 1px 4px 1px rgba(50, 50, 50, 0.4), 0px 0px 0px rgba(60,60,60,0.1);
}
.dropdown-holder.open > .butAction {
    /** TODO use css var with hsl from --colortextlink to allow create darken or lighten color */
    -webkit-box-shadow: 5px 5px 0px rgba(0, 0, 0, 0.1), inset 0px 0px 200px rgba(0, 0, 0, 0.3); /* fix hover feedback : use "inset" background to easily darken background */
    box-shadow: 5px 5px 0px rgba(0, 0, 0, 0.1), inset 0px 0px 200px rgba(0, 0, 0, 0.3); /* fix hover feedback : use "inset" background to easily darken background */
}
.butActionNew:hover   {
    text-decoration: underline;
    box-shadow: unset !important;
}

.butActionDelete, .butActionDelete:link, .butActionDelete:visited, .butActionDelete:hover, .butActionDelete:active, .buttonDelete {
	background: <?php print $colorButtonDelete1; ?> !important;
    color: #ffffff;
}

.butActionDelete:hover {
	background: <?php print $colorButtonDelete2; ?> !important;
    -webkit-box-shadow: 0px 1px 4px 1px rgba(50, 50, 50, 0.4), 0px 0px 0px rgba(60,60,60,0.1);
    box-shadow: 0px 1px 4px 1px rgba(50, 50, 50, 0.4), 0px 0px 0px rgba(60,60,60,0.1);
}

.butActionRefused {
    text-decoration: none !important;
    /* text-transform: capitalize; */
    font-weight: bold !important;

    white-space: nowrap !important;
    cursor: not-allowed !important;
    margin: 0em <?php echo ($dol_optimize_smallscreen ? '0.6' : '0.9'); ?>em;
    padding: 0.6em <?php echo ($dol_optimize_smallscreen ? '0.6' : '0.7'); ?>em;
    font-family: <?php print $fontlist ?> !important;
    display: inline-block;
    text-align: center;
    cursor: pointer;
    color: #999 !important;
    background: unset;
    border: 0px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -webkit-box-shadow: 0px 1px 4px 1px rgba(50, 50, 50, 0.4), 0px 0px 0px rgba(60,60,60,0.1);
    box-shadow: 0px 1px 4px 1px rgba(50, 50, 50, 0.4), 0px 0px 0px rgba(60,60,60,0.1);
}

.butActionNewRefused, .butActionNewRefused:link, .butActionNewRefused:visited, .butActionNewRefused:hover, .butActionNewRefused:active {
    text-decoration: none !important;
    /* text-transform: capitalize; */
    font-weight: normal !important;

    white-space: nowrap !important;
    cursor: not-allowed !important;
    margin: 0em <?php echo ($dol_optimize_smallscreen ? '0.7' : '0.9'); ?>em;
    padding: 0.2em <?php echo ($dol_optimize_smallscreen ? '0.4' : '0.7'); ?>em;
    font-family: <?php print $fontlist ?> !important;
    display: inline-block;
    /* text-align: center;  New button are on right of screen */
    cursor: pointer;
    color: #999 !important;
    padding-top: 0.2em;
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
}

.butActionTransparent {
    color: #222 ! important;
    background-color: transparent ! important;
}


/*
TITLE BUTTON
 */

.btnTitle, a.btnTitle {
    display: inline-block;
    padding: 4px 12px 4px 12px;
    font-weight: 400;
    /* line-height: 1; */
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    box-shadow: none;
    text-decoration: none;
    position: relative;
    margin: 0 0 0 10px;
    min-width: 80px;
    text-align: center;
    border: none;
    font-size: 12px;
    font-weight: 300;
}

a.btnTitle.btnTitleSelected {
    border: 1px solid #ccc;
    border-radius: 3px;
}

.btnTitle > .btnTitle-icon {

}

.btnTitle > .btnTitle-label {
    color: #666666;
}

.btnTitle:hover, a.btnTitle:hover {
	border: 0px;
    border-radius: 3px;
    position: relative;
    margin: 0 0 0 10px;
    text-align: center;
    color: #ffffff;
    background-color: <?php print $colorButtonAction1; ?>;
    font-size: 12px;
    text-decoration: none;
    box-shadow: none;
}

.btnTitle.refused, a.btnTitle.refused, .btnTitle.refused:hover, a.btnTitle.refused:hover {
    color: #ffffff;
    cursor: not-allowed;
    background-color: <?php print $colorButtonDelete1; ?>;
}

.btnTitle:hover .btnTitle-label{
    color: #ffffff;
}

.btnTitle.refused .btnTitle-label, .btnTitle.refused:hover .btnTitle-label{
    color: #8a8a8a;
}

.btnTitle>.fa,
.btnTitle>.fal,
.btnTitle>.far {
    font-size: 20px;
    display: block;
}

div.pagination li:first-child a.btnTitle{
    margin-left: 10px;
}


.imgforviewmode {
	color: #aaa;
}

/* rule to reduce top menu - 2nd reduction: Reduce width of top menu icons again */
@media only screen and (max-width: <?php echo !getDolGlobalString('THEME_ELDY_WITDHOFFSET_FOR_REDUC2') ? round($nbtopmenuentries * 69, 0) + 130 : getDolGlobalString('THEME_ELDY_WITDHOFFSET_FOR_REDUC2'); ?>px)	/* reduction 2 */
{
	.btnTitle, a.btnTitle {
	    display: inline-block;
	    padding: 4px 4px 4px 4px;
		min-width: unset;
	}
}

/* rule to reduce top menu - 3rd reduction: The menu for user is on left */
@media only screen and (max-width: <?php echo !getDolGlobalString('THEME_ELDY_WITDHOFFSET_FOR_REDUC3') ? round($nbtopmenuentries * 47, 0) + 130 : getDolGlobalString('THEME_ELDY_WITDHOFFSET_FOR_REDUC3'); ?>px)	/* reduction 3 */
{
    .butAction, .butActionRefused, .butActionDelete {
        font-size: 0.9em;
    }
}

/* smartphone */
@media only screen and (max-width: 767px)
{
    .butAction, .butActionRefused, .butActionDelete {
        font-size: 0.85em;
    }
}

<?php if (getDolGlobalString('MAIN_BUTTON_HIDE_UNAUTHORIZED') && (!$user->admin)) { ?>
.butActionRefused, .butActionNewRefused, .btnTitle.refused {
    display: none !important;
}
<?php } ?>


/*
 * BTN LINK
 */

.btn-link{
	margin-right: 5px;
	border: 1px solid #ddd;
	color: #333;
	padding: 5px 10px;
	border-radius:1em;
	text-decoration: none !important;
}

.btn-link:hover{
	background-color: #ddd;
	border: 1px solid #ddd;
}
