<?php
/* Copyright (C) 2015       Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2015       Víctor Ortiz Pérez      <victor@accett.com.mx>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
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
 * or see https://www.gnu.org/
 */

/**
 *  \file			htdocs/core/lib/functionsnumtoword.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains all frequently used functions.
 */

/**
 * Function to return a number into a text.
 * May use module NUMBERWORDS if found.
 *
 * @param	float       $num			Number to convert (must be a numeric value, like reported by price2num())
 * @param	Translate   $langs			Language
 * @param	string      $currency		''=number to translate | 'XX'=currency code to use into text
 * @param	boolean     $centimes		false=no cents/centimes | true=there is cents/centimes
 * @return 	string|false			    Text of the number
 */
function dol_convertToWord($num, $langs, $currency = '', $centimes = false)
{
	//$num = str_replace(array(',', ' '), '', trim($num));	This should be useless since $num MUST be a php numeric value
	if (!$num) {
		return false;
	}

	if ($centimes && strlen((string) $num) == 1) {
		$num *= 10;
	}

	if (isModEnabled('numberwords')) {
		$concatWords = $langs->getLabelFromNumber((string) $num, $currency);
		return $concatWords;
	} else {
		$TNum = explode('.', (string) $num);

		$num = abs((int) $TNum[0]);
		$words = array();
		$list1 = array(
			'',
			$langs->transnoentitiesnoconv('one'),
			$langs->transnoentitiesnoconv('two'),
			$langs->transnoentitiesnoconv('three'),
			$langs->transnoentitiesnoconv('four'),
			$langs->transnoentitiesnoconv('five'),
			$langs->transnoentitiesnoconv('six'),
			$langs->transnoentitiesnoconv('seven'),
			$langs->transnoentitiesnoconv('eight'),
			$langs->transnoentitiesnoconv('nine'),
			$langs->transnoentitiesnoconv('ten'),
			$langs->transnoentitiesnoconv('eleven'),
			$langs->transnoentitiesnoconv('twelve'),
			$langs->transnoentitiesnoconv('thirteen'),
			$langs->transnoentitiesnoconv('fourteen'),
			$langs->transnoentitiesnoconv('fifteen'),
			$langs->transnoentitiesnoconv('sixteen'),
			$langs->transnoentitiesnoconv('seventeen'),
			$langs->transnoentitiesnoconv('eighteen'),
			$langs->transnoentitiesnoconv('nineteen')
		);
		$list2 = array(
			'',
			$langs->transnoentitiesnoconv('ten'),
			$langs->transnoentitiesnoconv('twenty'),
			$langs->transnoentitiesnoconv('thirty'),
			$langs->transnoentitiesnoconv('forty'),
			$langs->transnoentitiesnoconv('fifty'),
			$langs->transnoentitiesnoconv('sixty'),
			$langs->transnoentitiesnoconv('seventy'),
			$langs->transnoentitiesnoconv('eighty'),
			$langs->transnoentitiesnoconv('ninety'),
			$langs->transnoentitiesnoconv('hundred')
		);
		$list3 = array(
			'',
			$langs->transnoentitiesnoconv('thousand'),
			$langs->transnoentitiesnoconv('million'),
			$langs->transnoentitiesnoconv('billion'),
			$langs->transnoentitiesnoconv('trillion'),
			$langs->transnoentitiesnoconv('quadrillion')
		);

		$num_length = strlen((string) $num);
		$levels = (int) (($num_length + 2) / 3);
		$max_length = $levels * 3;
		$num = substr('00'.$num, -$max_length);
		$num_levels = str_split($num, 3);
		$nboflevels = count($num_levels);
		for ($i = 0; $i < $nboflevels; $i++) {
			$levels--;
			$hundreds = (int) ((int) $num_levels[$i] / 100);
			$hundreds = ($hundreds ? ' '.$list1[$hundreds].' '.$langs->transnoentities('hundred').($hundreds == 1 ? '' : 's').' ' : '');
			$tens = (int) ((int) $num_levels[$i] % 100);
			$singles = '';
			if ($tens < 20) {
				$tens = ($tens ? ' '.$list1[$tens].' ' : '');
			} else {
				$tens = (int) ($tens / 10);
				$tens = ' '.$list2[$tens].' ';
				$singles = (int) ((int) $num_levels[$i] % 10);
				$singles = ' '.$list1[$singles].' ';
			}
			$words[] = $hundreds.$tens.$singles.(($levels && (int) ($num_levels[$i])) ? ' '.$list3[$levels].' ' : '');
		} //end for loop
		$commas = count($words);
		if ($commas > 1) {
			$commas -= 1;
		}
		$concatWords = implode(' ', $words);
		// Delete multi whitespaces
		$concatWords = trim(preg_replace('/[ ]+/', ' ', $concatWords));

		if (!empty($currency)) {
			$concatWords .= ' '.$currency;
		}

		// If we need to write cents call again this function for cents
		$decimalpart = empty($TNum[1]) ? '' : preg_replace('/0+$/', '', $TNum[1]);

		if ($decimalpart) {
			if (!empty($currency)) {
				$concatWords .= ' '.$langs->transnoentities('and');
			}

			$concatWords .= ' '.dol_convertToWord((float) $decimalpart, $langs, '', true);
			if (!empty($currency)) {
				$concatWords .= ' '.$langs->transnoentities('centimes');
			}
		}
		return $concatWords;
	}
}


/**
 * Function to return number or amount in text.
 *
 * @param	float 	    $numero			Number to convert
 * @param	Translate	$langs			Language
 * @param	string	    $numorcurrency	'number' or 'amount'
 * @return 	string|int  	       			Text of the number or -1 in case TOO LONG (more than 1000000000000.99)
 *
 * @deprecated Use dol_convertToWord instead
 */
function dolNumberToWord($numero, $langs, $numorcurrency = 'number')
{
	// If the number is negative convert to positive and return -1 if it is too long
	if ($numero < 0) {
		$numero *= -1;
	}
	if ($numero >= 1000000000001) {
		return -1;
	}

	// Get 2 decimals to cents, another functions round or truncate
	$strnumber = number_format($numero, 10);
	$len = strlen($strnumber);
	$parte_decimal = '00';  // For static analysis, strnumber should contain '.'
	for ($i = 0; $i < $len; $i++) {
		if ($strnumber[$i] == '.') {
			$parte_decimal = $strnumber[$i + 1].$strnumber[$i + 2];
			break;
		}
	}

	/* Dolibarr 3.6.2 doesn't have $langs->default, why ask $lang like a parameter in case it exists? */
	if (((is_object($langs) && $langs->getDefaultLang(0) == 'es_MX') || (!is_object($langs) && $langs == 'es_MX')) && $numorcurrency == 'currency') {
		if ($numero >= 1 && $numero < 2) {
			return ("UN PESO ".$parte_decimal." / 100 M.N.");
		} elseif ($numero >= 0 && $numero < 1) {
			return ("CERO PESOS ".$parte_decimal." / 100 M.N.");
		} elseif ($numero >= 1000000 && $numero < 1000001) {
			return ("UN MILL&OacuteN DE PESOS ".$parte_decimal." / 100 M.N.");
		} elseif ($numero >= 1000000000000 && $numero < 1000000000001) {
			return ("UN BILL&OacuteN DE PESOS ".$parte_decimal." / 100 M.N.");
		} else {
			$entexto = "";
			$number = $numero;
			if ($number >= 1000000000) {
				$CdMMillon = (int) ($numero / 100000000000);
				$numero -= $CdMMillon * 100000000000;
				$DdMMillon = (int) ($numero / 10000000000);
				$numero -= $DdMMillon * 10000000000;
				$UdMMillon = (int) ($numero / 1000000000);
				$numero -= $UdMMillon * 1000000000;
				$entexto .= hundreds2text($CdMMillon, $DdMMillon, $UdMMillon);
				$entexto .= " MIL ";
			} else {
				$CdMMillon = 0;
				$DdMMillon = 0;
				$UdMMillon = 0;
			}
			if ($number >= 1000000) {
				$CdMILLON = (int) ($numero / 100000000);
				$numero -= $CdMILLON * 100000000;
				$DdMILLON = (int) ($numero / 10000000);
				$numero -= $DdMILLON * 10000000;
				$udMILLON = (int) ($numero / 1000000);
				$numero -= $udMILLON * 1000000;
				$entexto .= hundreds2text($CdMILLON, $DdMILLON, $udMILLON);
				if (!$CdMMillon && !$DdMMillon && !$UdMMillon && !$CdMILLON && !$DdMILLON && $udMILLON == 1) {
					$entexto .= " MILL&OacuteN ";
				} else {
					$entexto .= " MILLONES ";
				}
			}

			if ($number >= 1000) {
				$cdm = (int) ($numero / 100000);
				$numero -= $cdm * 100000;
				$ddm = (int) ($numero / 10000);
				$numero -= $ddm * 10000;
				$udm = (int) ($numero / 1000);
				$numero -= $udm * 1000;
				$entexto .= hundreds2text($cdm, $ddm, $udm);
				if ($cdm || $ddm || $udm) {
					$entexto .= " MIL ";
				}
			} else {
				$ddm = 0;
				$cdm = 0;
				$udm = 0;
			}
			$c = (int) ($numero / 100);
			$numero -= $c * 100;
			$d = (int) ($numero / 10);
			$u = (int) $numero - $d * 10;
			$entexto .= hundreds2text($c, $d, $u);
			if (!$cdm && !$ddm && !$udm && !$c && !$d && !$u && $number > 1000000) {
				$entexto .= " DE";
			}
			$entexto .= " PESOS ".$parte_decimal." / 100 M.N.";
		}
		return $entexto;
	}
	return -1;
}

/**
 * hundreds2text
 *
 * @param integer $hundreds     Hundreds
 * @param integer $tens         Tens
 * @param integer $units        Units
 * @return string
 */
function hundreds2text($hundreds, $tens, $units)
{
	if ($hundreds == 1 && $tens == 0 && $units == 0) {
		return "CIEN";
	}
	$centenas = array("CIENTO", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS");
	$decenas = array("", "", "TREINTA ", "CUARENTA ", "CINCUENTA ", "SESENTA ", "SETENTA ", "OCHENTA ", "NOVENTA ");
	$veintis = array("VEINTE", "VEINTIUN", "VEINTID&OacuteS", "VEINTITR&EacuteS", "VEINTICUATRO", "VEINTICINCO", "VEINTIS&EacuteIS", "VEINTISIETE", "VEINTIOCHO", "VEINTINUEVE");
	$diecis = array("DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECIS&EacuteIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE");
	$unidades = array("UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE");
	$entexto = "";
	if ($hundreds != 0) {
		$entexto .= $centenas[$hundreds - 1];
	}
	if ($tens > 2) {
		if ($hundreds != 0) {
			$entexto .= " ";
		}
		$entexto .= $decenas[$tens - 1];
		if ($units != 0) {
			$entexto .= " Y ";
			$entexto .= $unidades[$units - 1];
		}
		return $entexto;
	} elseif ($tens == 2) {
		if ($hundreds != 0) {
			$entexto .= " ";
		}
		$entexto .= " ".$veintis[$units];
		return $entexto;
	} elseif ($tens == 1) {
		if ($hundreds != 0) {
			$entexto .= " ";
		}
		$entexto .= $diecis[$units];
		return $entexto;
	}
	if ($units != 0) {
		if ($hundreds != 0 || $tens != 0) {
			$entexto .= " ";
		}
		$entexto .= $unidades[$units - 1];
	}
	return $entexto;
}
