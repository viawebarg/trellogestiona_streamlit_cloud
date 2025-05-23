<?php
/* Copyright (C) 2022	SuperAdmin		<test@dolibarr.com>
 * Copyright (C) 2023	William Mead	<william.mead@manchenumerique.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    core/triggers/interface_99_modWebhook_WebhookTriggers.class.php
 * \ingroup webhook
 * \brief   Example trigger.
 *
 * Put detailed description here.
 *
 * \remarks You can create other triggers by copying this one.
 * - File name should be either:
 *      - interface_99_modWebhook_MyTrigger.class.php
 *      - interface_99_all_MyTrigger.class.php
 * - The file must stay in core/triggers
 * - The class name must be InterfaceMytrigger
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
require_once DOL_DOCUMENT_ROOT.'/webhook/class/target.class.php';

/**
 *  Class of triggers for Webhook module
 */
class InterfaceWebhookTriggers extends DolibarrTriggers
{
	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "demo";
		$this->description = "Webhook triggers.";
		$this->version = self::VERSIONS['dev'];
		$this->picto = 'webhook';
	}

	/**
	 * Function called when a Dolibarr business event is done.
	 * All functions "runTrigger" are triggered if file of function is inside directory core/triggers.
	 *
	 * @param string 		$action 	Event action code
	 * @param CommonObject 	$object 	Object
	 * @param User 			$user 		Object user
	 * @param Translate 	$langs 		Object langs
	 * @param Conf 			$conf 		Object conf
	 * @return int              		Return integer <0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		if (!isModEnabled('webhook')) {
			return 0; // If module is not enabled, we do nothing
		}

		require_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

		// Or you can execute some code here
		$nbPosts = 0;
		$errors = 0;
		$static_object = new Target($this->db);
		$target_url = $static_object->fetchAll();	// TODO Replace this with a search with filter on $action trigger to avoid to filter later.

		if (is_numeric($target_url) && $target_url < 0) {
			dol_syslog("Error Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
			$this->errors = array_merge($this->errors, $static_object->errors);
			return -1;
		}

		if (!is_array($target_url)) {
			// No webhook found
			return 0;
		}

		$sendmanualtriggers = (!empty($object->context['sendmanualtriggers']) ? $object->context['sendmanualtriggers'] : "");
		foreach ($target_url as $key => $tmpobject) {
			// Set list of all triggers for this targetinto $actionarray
			$actionarraytmp = explode(",", $tmpobject->trigger_codes);
			$actionarray = array();
			foreach ($actionarraytmp as $val) {
				$actionarray[] = trim($val);
			}

			// Test on Target status
			$testontargetstatus = ($tmpobject->status == Target::STATUS_AUTOMATIC_TRIGGER || ($tmpobject->status == Target::STATUS_MANUAL_TRIGGER && !empty($sendmanualtriggers)));
			if (((!empty($object->context["actiontrigger"]) && in_array($object->context["actiontrigger"], array("sendtrigger", "testsend"))) || $testontargetstatus) && in_array($action, $actionarray)) {
				// Build the answer object
				$resobject = new stdClass();
				$resobject->triggercode = $action;
				$resobject->object = dol_clone($object, 2);

				if (property_exists($resobject->object, 'fields')) {
					unset($resobject->object->fields);
				}
				if (property_exists($resobject->object, 'error')) {
					unset($resobject->object->error);
				}
				if (property_exists($resobject->object, 'errors')) {
					unset($resobject->object->errors);
				}

				$jsonstr = json_encode($resobject);

				$headers = array(
					'Content-Type: application/json'
					//'Accept: application/json'
				);

				$method = 'POSTALREADYFORMATED';
				if (getDolGlobalString('WEBHOOK_POST_SEND_DATA_AS_PARAM_STRING')) {		// For compatibility with v20- versions
					$method = 'POST';
				}

				// warning; the test page use its own call
				$response = getURLContent($tmpobject->url, $method, $jsonstr, 1, $headers, array('http', 'https'), 2, -1);

				if (empty($response['curl_error_no']) && $response['http_code'] >= 200 && $response['http_code'] < 300) {
					$nbPosts++;
				} else {
					$errormsg = "The WebHook for ".$action." failed to get URL ".$tmpobject->url." with httpcode=".(!empty($response['http_code']) ? $response['http_code'] : "")." curl_error_no=".(!empty($response['curl_error_no']) ? $response['curl_error_no'] : "");

					if ($tmpobject->type == Target::TYPE_BLOCKING) {
						$errors++;
						$this->errors[] = $errormsg;

						dol_syslog($errormsg, LOG_ERR);
					} else {
						dol_syslog($errormsg, LOG_WARNING);
					}
					/*if (!empty($response['content'])) {
						$this->errors[] = dol_trunc($response['content'], 200);
					}*/
				}
			}
		}

		dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id." -> nbPost=".$nbPosts);

		if (!empty($errors)) {
			return $errors * -1;
		}

		return $nbPosts;
	}
}
