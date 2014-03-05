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

/**
 * This class contains all hooks.
 */
class OC_USER_JOOMLA_AUTH_Hooks {
	static public function post_login($params) {
		$uid = $params['uid'];
		$password = $params['password'];
		$joomla_backend = new OC_USER_JOOMLA_AUTH();
		
		if($joomla_backend->db_con)
		{
			$joomla_username = joomla_username($joomla_backend, $uid);
			if($joomla_username)
			{
				$joomla_display_name = joomla_display_name($joomla_backend, $uid);
				$joomla_email = joomla_email($joomla_backend, $uid);
				
				if (!OC_User::userExists($uid)) {
					if (preg_match( '/[^a-zA-Z0-9 _\.@\-]/', $uid)) {
						OC_Log::write('OC_USER_JOOMLA_AUTH','Invalid username "'.$uid.'", allowed chars "a-zA-Z0-9" and "_.@-" ',OC_Log::ERROR);
						return false;
					}
					else {
						if(!isset($password)) {
							$password = OC_Util::generate_random_bytes(20);
						}
						OC_Log::write('OC_USER_JOOMLA_AUTH','Creating new user: '.$uid, OC_Log::DEBUG);
						OC_User::createUser($uid, $password);
						if(OC_User::userExists($uid)) {
							OC_Util::setupFS($uid);
							if ($joomla_email) {
								update_mail($uid, $joomla_email);
							}
							if (isset($joomla_backend->joomla_auth_user_group)) {
								update_groups($uid, $joomla_backend->joomla_auth_user_group);
							}
							if ($joomla_display_name) {
								OC_User::setDisplayName($uid, $joomla_display_name);
							}
						}
					}
				}
				else {
					if ($joomla_backend->update_user_data) {
						OC_Util::setupFS($uid);
						OC_Log::write('OC_USER_JOOMLA_AUTH','Updating data of the user: '.$uid,OC_Log::DEBUG);
						if($joomla_email) {
							update_mail($uid, $joomla_email);
						}
						if (isset($joomla_backend->joomla_auth_user_group)) {
							update_groups($uid, $joomla_backend->joomla_auth_user_group);
						}
						if ($joomla_display_name) {
							OC_User::setDisplayName($uid, $joomla_display_name);
						}
						if (isset($password)) {
							OC_User::setPassword($uid, $password);
						}
					}
				}
			}
		}
		
		return true;
	}
}

function joomla_username($joomla_backend, $uid)
{
	$query="SELECT username FROM ".$joomla_backend->joomla_auth_db_prefix."users WHERE username='".$uid."'";
	$res = mysql_query($query, $joomla_backend->db_con);

	if($row=mysql_fetch_object($res))
	{
		return $row->username;
	}
	
	return false;
}

function joomla_passwordhash($joomla_backend, $uid)
{
	$query="SELECT password FROM ".$joomla_backend->joomla_auth_db_prefix."users WHERE username='".$uid."'";
	$res = mysql_query($query, $joomla_backend->db_con);

	if($row=mysql_fetch_object($res))
	{
		return explode (':' , $row->password);
	}
	
	return false;
}

function joomla_display_name($joomla_backend, $uid)
{
	$query="SELECT name FROM ".$joomla_backend->joomla_auth_db_prefix."users WHERE username='".$uid."'";
	$res = mysql_query($query, $joomla_backend->db_con);

	if($row=mysql_fetch_object($res))
	{
		return $row->name;
	}
	
	return false;
}

function joomla_email($joomla_backend, $uid)
{
	$query="SELECT email FROM ".$joomla_backend->joomla_auth_db_prefix."users WHERE username='".$uid."'";
	$res = mysql_query($query, $joomla_backend->db_con);

	if($row=mysql_fetch_object($res))
	{
		return $row->email;
	}
	
	return false;
}

function update_mail($uid, $email) {
	if ($email != OC_Preferences::getValue($uid, 'settings', 'email', '')) {
		OC_Preferences::setValue($uid, 'settings', 'email', $email);
		OC_Log::write('OC_USER_JOOMLA_AUTH','Set email "'.$email.'" for the user: '.$uid, OC_Log::DEBUG);
	}
}

function update_groups($uid, $group) {
	if (preg_match( '/[^a-zA-Z0-9 _\.@\-]/', $group)) {
		OC_Log::write('OC_USER_JOOMLA_AUTH','Invalid group "'.$group.'", allowed chars "a-zA-Z0-9" and "_.@-" ',OC_Log::ERROR);
	}
	else {
		if (!OC_Group::groupExists($group)) {
			OC_Group::createGroup($group);
			OC_Log::write('OC_USER_JOOMLA_AUTH','New group created: '.$group, OC_Log::DEBUG);
		}
		
		if (!OC_Group::inGroup($uid, $group)) {
			OC_Group::addToGroup($uid, $group);
			OC_Log::write('OC_USER_JOOMLA_AUTH','Added "'.$uid.'" to the group "'.$group.'"', OC_Log::DEBUG);
		}
	}
}
