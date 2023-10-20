<?php
/* Copyright (C) 2005-2009	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2009-2017	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2014		Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2023		Udo Tamm			<dev@dolibit.de>
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
 *  \file       htdocs/core/triggers/interface_20_all_Logevents.class.php
 *  \ingroup    core
 *  \brief      Trigger file for log events
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for security audit events
 */
class InterfaceLogevents extends DolibarrTriggers
{
	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name 		= preg_replace('/^Interface/i', '', get_class($this));
		$this->family 		= "core";
		$this->description  = "Triggers of this module allows to add security event records inside Dolibarr.";
		$this->version 		= self::VERSION_DOLIBARR;  // VERSION_ 'DEVELOPMENT' or 'EXPERMENTAL' or 'DOLIBARR'
		$this->picto 		= 'technic';
	}

	/**
	 * Function called when a Dolibarrr security audit event is done.
	 * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
	 *
	 * @param string		$action		Event action code
	 * @param Object		$object     Object
	 * @param User			$user       Object user
	 * @param Translate		$langs      Object langs
	 * @param conf			$conf       Object conf
	 * @return int         				<0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		if (!empty($conf->global->MAIN_LOGEVENTS_DISABLE_ALL)) {
			return 0; // Log events is disabled (hidden features)
		}

		$key = 'MAIN_LOGEVENTS_'.$action;
		//dol_syslog("xxxxxxxxxxx".$key);
		if (empty($conf->global->$key)) {
			return 0; // Log events not enabled for this action
		}

		if (empty($conf->entity)) {
			$conf->entity = $entity; // forcing of the entity if it's not defined (ex: in login form)
		}

		$date = dol_now();

		/* Actions */

		$text = '';
		$desc = '';
		$langs->load("users");

		// Actions
		if ($action == 'USER_LOGIN') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = "(UserLogged,".$object->login.")";
			$desc = "(UserLogged,".$object->login.")";

			// USER_LOGIN_FAILED
		} elseif ($action == 'USER_LOGIN_FAILED') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

			// USER_LOGOUT
		} elseif ($action == 'USER_LOGOUT') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = "(UserLogoff,".$object->login.")";
			$desc = "(UserLogoff,".$object->login.")";

			// USER_CREATE
		} elseif ($action == 'USER_CREATE') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("NewUserCreated", $object->login);
			$desc = $langs->transnoentities("NewUserCreated", $object->login);

			// USER_MODIFY
		} elseif ($action == 'USER_MODIFY') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("EventUserModified", $object->login);
			$desc = $langs->transnoentities("EventUserModified", $object->login);

			// USER_NEW_PASSWORD
		} elseif ($action == 'USER_NEW_PASSWORD') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("NewUserPassword", $object->login);
			$desc = $langs->transnoentities("NewUserPassword", $object->login);

			// USER ENABLED/DISABLED
		} elseif ($action == 'USER_ENABLEDISABLE') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			if ($object->statut == 0) {
				$text = $langs->transnoentities("UserEnabled", $object->login);
				$desc = $langs->transnoentities("UserEnabled", $object->login);
			}
			if ($object->statut == 1) {
				$text = $langs->transnoentities("UserDisabled", $object->login);
				$desc = $langs->transnoentities("UserDisabled", $object->login);
			}

			// USER_DELETE
		} elseif ($action == 'USER_DELETE') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("UserDeleted", $object->login);
			$desc = $langs->transnoentities("UserDeleted", $object->login);

			// USERGROUP_CREATE
		} elseif ($action == 'USERGROUP_CREATE') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("NewGroupCreated", $object->name);
			$desc = $langs->transnoentities("NewGroupCreated", $object->name);

			// USERGROUP_MODIFY
		} elseif ($action == 'USERGROUP_MODIFY') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("GroupModified", $object->name);
			$desc = $langs->transnoentities("GroupModified", $object->name);

			// USERGROUP_DELETE
		} elseif ($action == 'USERGROUP_DELETE') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
			// Initialize data (date,duree,text,desc)
			$text = $langs->transnoentities("GroupDeleted", $object->name);
			$desc = $langs->transnoentities("GroupDeleted", $object->name);
		}

		// Add more information into desc from the context property
		if (!empty($object->context['audit'])) {
			$desc .= (empty($desc) ? '' : ' - ').$object->context['audit'];
		}

		// Add entry in event table
		include_once DOL_DOCUMENT_ROOT.'/core/class/events.class.php';

		$event = new Events($this->db);
		$event->type = $action;
		$event->dateevent = $date;
		$event->label = $text;
		$event->description = $desc;
		$event->user_agent = (empty($_SERVER["HTTP_USER_AGENT"]) ? '' : $_SERVER["HTTP_USER_AGENT"]);
		$event->authentication_method = (empty($object->context['authentication_method']) ? '' : $object->context['authentication_method']);

		$result = $event->create($user);
		if ($result > 0) {
			return 1;
		} else {
			$error = "Failed to insert security event: ".$event->error;
			$this->errors[] = $error;
			$this->error = $error;

			dol_syslog(get_class($this).": ".$error, LOG_ERR);
			return -1;
		}
	}
}
