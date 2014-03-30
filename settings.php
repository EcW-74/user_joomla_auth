<?php

/**
* ownCloud - user_joomla_auth
*
* @author Enrico Walther
* @copyright 2014 Enrico Walther <oc@kleinhain.de>
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

OC_Util::checkAdminUser();
OCP\Util::addstyle('user_joomla_auth', 'settings');

if($_POST) {
	// CSRF check
	OCP\JSON::callCheck();

	if(isset($_POST['joomla_db_host'])) {
		OC_CONFIG::setValue('user_joomla_auth_db_host', strip_tags($_POST['joomla_db_host']));
	}

	if(isset($_POST['joomla_db_user'])) {
		OC_CONFIG::setValue('user_joomla_auth_db_user', strip_tags($_POST['joomla_db_user']));
	}

	if(isset($_POST['joomla_db_password'])) {
		OC_CONFIG::setValue('user_joomla_auth_db_password', strip_tags($_POST['joomla_db_password']));
	}

	if(isset($_POST['joomla_db_database'])) {
		OC_CONFIG::setValue('user_joomla_auth_db_database', strip_tags($_POST['joomla_db_database']));
	}
	
	if(isset($_POST['joomla_db_prefix'])) {
		OC_CONFIG::setValue('user_joomla_auth_db_prefix', strip_tags($_POST['joomla_db_prefix']));
	}
	
	if(isset($_POST['joomla_user_group'])) {
		OC_CONFIG::setValue('user_joomla_auth_user_group', strip_tags($_POST['joomla_user_group']));
	}
	
	if(isset($_POST['joomla_update_user_data'])) {
		OC_CONFIG::setValue('user_joomla_auth_update_user_data', strip_tags($_POST['joomla_update_user_data']));
	}
}

// fill template
$tmpl = new OC_Template( 'user_joomla_auth', 'settings');
$tmpl->assign( 'joomla_db_host', OC_Config::getValue( "user_joomla_auth_db_host" ));
$tmpl->assign( 'joomla_db_user', OC_Config::getValue( "user_joomla_auth_db_user" ));
$tmpl->assign( 'joomla_db_password', OC_Config::getValue( "user_joomla_auth_db_password" ));
$tmpl->assign( 'joomla_db_database', OC_Config::getValue( "user_joomla_auth_db_database" ));
$tmpl->assign( 'joomla_db_prefix', OC_Config::getValue( "user_joomla_auth_db_prefix" ));
$tmpl->assign( 'joomla_user_group', OC_Config::getValue( "user_joomla_auth_user_group" ));
$tmpl->assign( 'joomla_update_user_data', OC_Config::getValue( "user_joomla_auth_update_user_data" ));

return $tmpl->fetchPage();
