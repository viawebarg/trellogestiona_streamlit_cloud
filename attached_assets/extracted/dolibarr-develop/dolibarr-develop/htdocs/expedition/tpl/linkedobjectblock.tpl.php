<?php
/* Copyright (C) 2012       Regis Houssin   <regis.houssin@inodbox.com>
 * Copyright (C) 2014       Marcos García   <marcosgdf@gmail.com>
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
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

// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf)) {
	print "Error, template page can't be called as URL";
	exit(1);
}


print "<!-- BEGIN PHP TEMPLATE expedition/tpl/linkedobjectblock.tpl.php -->\n";


global $user;

$langs = $GLOBALS['langs'];
'@phan-var-force Translate $langs';
$linkedObjectBlock = $GLOBALS['linkedObjectBlock'];
'@phan-var-force CommonObject[] $linkedObjectBlock';

// Load translation files required by the page
$langs->load("sendings");

$total = 0;
$ilink = 0;
foreach ($linkedObjectBlock as $key => $objectlink) {
	$ilink++;

	$trclass = 'oddeven';
	if ($ilink == count($linkedObjectBlock) && empty($noMoreLinkedObjectBlockAfter) && count($linkedObjectBlock) <= 1) {
		$trclass .= ' liste_sub_total';
	} ?>
	<tr class="<?php echo $trclass; ?>">
		<td><?php echo $langs->trans("Shipment"); ?></td>
		<td class="tdoverflowmax125"><?php echo $objectlink->getNomUrl(1); ?></td>
		<td class="tdoverflowmax125" title="<?php dolPrintHTMLForAttribute($objectlink->ref_customer); ?>"><?php echo dolPrintHTML($objectlink->ref_customer); ?></td>
		<td class="center"><?php echo dol_print_date($objectlink->date_delivery ? $objectlink->date_delivery : $objectlink->date_creation, 'day'); ?></td>
		<td class="right"><?php
		if ($user->hasRight('expedition', 'lire')) {
			$total += $objectlink->total_ht;
			echo price($objectlink->total_ht);
		} ?></td>
		<td class="right"><?php echo $objectlink->getLibStatut(3); ?></td>
		<td class="right">
			<?php
			// For now, shipments must stay linked to order, so link is not deletable
			if ($object->element != 'commande') {
				?>
			<a class="reposition" href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&token='.newToken().'&action=dellink&dellinkid='.$key; ?>"><?php echo img_picto($langs->transnoentitiesnoconv("RemoveLink"), 'unlink'); ?></a></td>				<?php
			} ?>
	</tr>
	<?php
}
if (count($linkedObjectBlock) > 1) {
	?>
	<tr class="liste_total <?php echo(empty($noMoreLinkedObjectBlockAfter) ? 'liste_sub_total' : ''); ?>">
		<td><?php echo $langs->trans("Total"); ?></td>
		<td></td>
		<td class="center"></td>
		<td class="center"></td>
		<td class="right"><?php echo price($total); ?></td>
		<td class="right"></td>
		<td class="right"></td>
	</tr>
	<?php
}

print "<!-- END PHP TEMPLATE -->\n";
