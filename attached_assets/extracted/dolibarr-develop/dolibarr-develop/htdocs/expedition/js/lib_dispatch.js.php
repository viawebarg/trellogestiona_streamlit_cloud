<?php
// Copyright (C) 2014 Cedric GROSS		<c.gross@kreiz-it.fr>
// Copyright (C) 2017 Francis Appels	<francis.appels@z-application.com>
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <https://www.gnu.org/licenses/>.
// or see https://www.gnu.org/

/**
 * \file       htdocs/expedition/js/lib_dispatch.js.php
 * \brief      File that include javascript functions used for dispatching qty/stock/lot
 */

if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', 1);
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1);
}
if (!defined('NOLOGIN')) {
	define('NOLOGIN', 1);
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', 1);
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}

session_cache_limiter('public');

require_once '../../main.inc.php';

// Define javascript type
top_httphead('text/javascript; charset=UTF-8');
// Important: Following code is to avoid page request by browser and PHP CPU at each Dolibarr page access.
if (empty($dolibarr_nocache)) {
	header('Cache-Control: max-age=10800, public, must-revalidate');
} else {
	header('Cache-Control: no-cache');
}

?>
/**
 * addDispatchLine
 * Adds new table row for dispatching to multiple stock locations or multiple lot/serial
 *
 * @param	index	int		index of product line. 0 = first product line
 * @param	type	string	type of dispatch ('batch' = batch dispatch, 'dispatch' = non batch dispatch)
 * @param	mode	string	'qtymissing' will create new line with qty missing, 'lessone' will keep 1 in old line and the rest in new one
 */
function addDispatchLine(index, type, mode) {
	mode = mode || 'qtymissing'

	console.log("expedition/js/lib_dispatch.js.php addDispatchLine Split line type="+type+" index="+index+" mode="+mode);

	var lineId = '';
	var typeArr = type.split('-');
	if (typeArr.length > 0) {
		lineId = typeArr[1];
	}
	var $row0 = $("tr[name='"+type+'_0_'+index+"']");
	var $dpopt = $row0.find('.hasDatepicker').first().datepicker('option', 'all'); // get current datepicker options to apply the same to the cloned datepickers
	var $row = $row0.clone(true); 		// clone first batch line to jQuery object
	var nbrTrs = $("tr[name^='"+type+"_'][name$='_"+index+"']").length; // count nb of tr line with attribute name that starts with 'batch_' or 'dispatch_', and end with _index
	var qtyOrdered = parseFloat($("#qty_ordered_0_"+index).val()); 		// Qty ordered is same for all rows

	var qty = parseFloat($("#qty"+lineId+"_"+(nbrTrs - 1)+"_"+index).val());
	if (isNaN(qty)) {
		qty = '';
	}

	console.log("expedition/js/lib_dispatch.js.php addDispatchLine Split line="+lineId+" nbrTrs="+nbrTrs+" qtyOrdered="+qtyOrdered+" qty="+qty);

	var	qtyDispatched;

	if (mode === 'lessone') {
		qtyDispatched = parseFloat($("#qty_dispatched_0_" + index).val()) + 1;
	}
	else {
		qtyDispatched = parseFloat($("#qty_dispatched_0_" + index).val()) + qty;
		// If user did not reduced the qty to dispatch on old line, we keep only 1 on old line and the rest on new line
		if (qtyDispatched == qtyOrdered && qtyDispatched > 1) {
			qtyDispatched = parseFloat($("#qty_dispatched_0_" + index).val()) + 1;
			mode = 'lessone';
		}
	}
	console.log("expedition/js/lib_dispatch.js.php qtyDispatched=" + qtyDispatched + " qtyOrdered=" + qtyOrdered+ " qty=" + qty);

	if (qty <= 1) {
		window.alert("Remain quantity to dispatch is too low to be split");
	} else {
		var oldlineqty = qtyDispatched;
		var newlineqty = qtyOrdered - qtyDispatched;
		if (newlineqty <= 0) {
			newlineqty = qty - 1;
			oldlineqty = 1;
			$("#qty"+lineId+"_"+(nbrTrs - 1)+"_"+index).val(oldlineqty);
		}

		//replace tr suffix nbr
		$row.html($row.html().replace(/_0_/g, "_" + nbrTrs + "_"));

		// jquery's deep clone is incompatible with date pickers (the clone shares data with the original)
		// so we destroy and rebuild the new date pickers
		setTimeout(() => {
			$row.find('.hasDatepicker').each((i, dp) => {
				$(dp).removeData()
					.removeClass('hasDatepicker');
				$(dp).next('img.ui-datepicker-trigger').remove();
				$(dp).datepicker($dpopt);
			});
		}, 0);

		// create new select2 to avoid duplicate id of cloned one for warehouse
		$row.find("select[name='"+'entrepot'+lineId+'_'+nbrTrs+'_'+index+"']").select2();
		// create new select2 to avoid duplicate id of cloned one for lot / serial number
		$row.find("select[name='"+'lot_number'+lineId+'_'+nbrTrs+'_'+index+"']").select2();
		// TODO find solution to copy selected option to new select
		// TODO find solution to keep new tr's after page refresh
		//clear value
		$row.find("input[name^='qty']").val('');
		//change name of new row
		$row.attr('name', type+'_'+nbrTrs+'_'+index);
		//insert new row before last row
		$("tr[name^='"+type+"_'][name$='_"+index+"']:last").after($row);

		// remove cloned select2 with duplicate id for warehouse
		$("#s2id_entrepot"+lineId+"_"+nbrTrs+'_'+index).detach();			// old way to find duplicated select2 component
		$(".csswarehouse"+lineId+"_"+nbrTrs+"_"+index + ":first-child").parent("span.selection").parent(".select2").detach();

		// remove cloned select2 with duplicate id for lot / serial number
		$("#s2id_lot_number"+lineId+"_"+nbrTrs+'_'+index).detach();			// old way to find duplicated select2 component
		$(".csslotnumber"+lineId+"_"+nbrTrs+"_"+index + ":first-child").parent("span.selection").parent(".select2").detach();

		/*  Suffix of lines are:  _ trs.length _ index  */
		$("#qty"+lineId+"_"+nbrTrs+"_"+index).focus();
		$("#qty_dispatched_0_"+index).val(oldlineqty);

		//hide all buttons then show only the last one
		$("tr[name^='"+type+"_'][name$='_"+index+"'] .splitbutton").hide();
		$("tr[name^='"+type+"_'][name$='_"+index+"']:last .splitbutton").show();

		$("#reset"+lineId+"_"+(nbrTrs)+"_"+index).click(function (event) {
			event.preventDefault();
			id = $(this).attr("id");
			id = id.split("reset"+lineId+"_");
			idrow = id[1];
			idlast = $("tr[name^='"+type+"_'][name$='_"+index+"']:last .qtydispatchinput").attr("id");
			if (idlast == $("#qty"+lineId+"_"+idrow).attr("id")) {
				console.log("expedition/js/lib_dispatch.js.php Remove trigger for tr name = "+type+"_"+idrow);
				$('tr[name="'+type+'_'+idrow+'"').remove();
				$("tr[name^='"+type+"_'][name$='_"+index+"']:last .splitbutton").show();
			} else {
				console.log("expedition/js/lib_dispatch.js.php Reset trigger for id = qty_"+idrow);
				$("#qty"+lineId+"_"+idrow).val("");
			}
		});

		if (mode === 'lessone')
		{
			qty = 1; // keep 1 in old line
			$("#qty"+lineId+"_"+(nbrTrs-1)+"_"+index).val(qty);
		}
		$("#qty"+lineId+"_"+nbrTrs+"_"+index).val(newlineqty);
		// Store arbitrary data for dispatch qty input field change event
		$("#qty"+lineId+"_" + (nbrTrs - 1) + "_" + index).data('qty', qty);
		$("#qty"+lineId+"_" + (nbrTrs - 1) + "_" + index).data('type', type);
		$("#qty"+lineId+"_" + (nbrTrs - 1) + "_" + index).data('index', index);
		// Update dispatched qty when value dispatch qty input field changed
		//$("#qty_" + (nbrTrs - 1) + "_" + index).change(this.onChangeDispatchLineQty);
		//set focus on lot of new line (if it exists)
		$("#lot_number"+lineId+"_"+(nbrTrs)+"_"+index).focus();
		//Clean bad values
		$("tr[name^='"+type+"_'][name$='_"+index + "']:last").data("remove", "remove");
		$("#lot_number_"+(nbrTrs) + "_"+index).val("")
		$("#idline"+lineId+"_"+(nbrTrs)+"_"+index).val("-1")
		$("#qty"+lineId+"_"+(nbrTrs)+"_"+index).data('expected', "0");
		//$("input[type='hidden']#lot_number_" + (nbrTrs) + "_" + index).remove();
		$("#lot_number"+lineId+"_"+(nbrTrs)+"_"+index).removeAttr("disabled");
	}
}

/**
 * onChangeDispatchLineQty
 *
 * Change event handler for dispatch qty input field,
 * recalculate qty dispatched when qty input has changed.
 * If qty is more than qty ordered reset input qty to max qty to dispatch.
 *
 * element requires arbitrary data qty (value before change), type (type of dispatch) and index (index of product line)
 */
function onChangeDispatchLineQty(element) {
	var type = $(element).data('type'),
		qty = parseFloat($(element).data('expected')),
		changedQty, nbrTrs, dispatchingQty, qtyOrdered, qtyDispatched;
		id = $(element).attr("id");
		id = id.split("_");
		index = id[2];

	if (index >= 0 && type && qty >= 0) {
		nbrTrs = $("tr[name^='"+type+"_'][name$='_"+index+"']").length;
		qtyChanged = parseFloat($(element).val()) - qty; // qty changed
		qtyDispatching = parseFloat($(element).val()); // qty currently being dispatched
		qtyOrdered = parseFloat($("#qty_ordered_0_"+index).val()); // qty ordered
		qtyDispatched = parseFloat($("#qty_dispatched_0_"+index).val()); // qty already dispatched

		console.log("onChangeDispatchLineQty qtyChanged: "+qtyChanged+" qtyDispatching: "+qtyDispatching+" qtyOrdered: "+qtyOrdered+" qtyDispatched: "+qtyDispatched);

		if ((qtyChanged) <= (qtyOrdered - (qtyDispatched + qtyDispatching))) {
			$("#qty_dispatched_0_" + index).val(qtyDispatched + qtyChanged);
		} else {
			/*console.log("eee");
			$(element).val($(element).data('expected'));*/
		}
		$(element).data('expected', $(element).val());
	}
}
