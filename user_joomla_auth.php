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

class OC_USER_JOOMLA_AUTH extends OC_User_Backend {
	public $joomla_auth_db_host;
	public $joomla_auth_db_user;
	public $joomla_auth_db_database;
	public $joomla_auth_db_prefix;
	public $db_con;
	public $update_user_data;

	public function __construct() {
		$this->joomla_auth_db_host = OC_Config::getValue( "user_joomla_auth_db_host" );
		$this->joomla_auth_db_user = OC_Config::getValue( "user_joomla_auth_db_user" );
		$this->joomla_auth_db_database = OC_Config::getValue( "user_joomla_auth_db_database" );
		$this->joomla_auth_db_prefix = OC_Config::getValue( "user_joomla_auth_db_prefix" );
		$this->joomla_auth_user_group=OC_Config::getValue( "user_joomla_auth_user_group" );
		$this->update_user_data = OC_Config::getValue( "user_joomla_auth_update_user_data" );
		$this->db_con=$this->getDbResource();
	}

	public function deleteUser($uid) {
		// Can't delete user
		OC_Log::write('OC_USER_JOOMLA_AUTH', 'Not possible to delete users from web frontend using Joomla user backend', OC_Log::ERROR);
		return false;
	}

	/**
	 * @brief Check if the password is correct
	 * @param $uid The username
	 * @param $password The password
	 * @returns true/false
	 *
	 * Check if the password is correct without logging in the user
	 */
	public function checkPassword( $uid, $password ) {
		$con=$this->db_con;
		if($con==false)
		{
			return false;
		}

		$query="SELECT id, username, password FROM ".$this->joomla_auth_db_prefix."users WHERE username='".$uid."'";
		$res = mysql_query($query, $con);

		if($row=mysql_fetch_object($res))
		{
			$hashparts = explode (':' , $row->password);
			$userhash = md5($password.$hashparts[1]);
			if($hashparts[0]==$userhash)
			{
				OC_Log::write('OC_USER_JOOMLA_AUTH','uid 1: '.$row->username, OC_Log::ERROR);
				if($this->isUserInGroup($row->id, $uid))
				{
					return $row->username;
				}
			}
			else
			{
				OC_Log::write('OC_USER_JOOMLA_AUTH', "Wrong password for ".$uid."!" , OC_Log::ERROR);
			}
		}
		else
		{
			OC_Log::write('OC_USER_JOOMLA_AUTH', "User ".$uid." doesn't exist in Joomla database!" , OC_Log::ERROR);
		}
		return false;
		
		return false;
	}


	public function userExists($uid) {
		return false;
	}

	/**
	 * @return bool
	 */
	public function hasUserListings() {
		return false;
	}
	
	/*
	 * check whether the user in the correct group
	 */
	private function isUserInGroup($id, $username)
	{
		$con=$this->db_con;
		if($con==false)
		{
			return false;
		}

		$query="SELECT id FROM ".$this->joomla_auth_db_prefix."usergroups WHERE title='".$this->joomla_auth_user_group."'";
		$res = mysql_query($query, $con);

		if($row=mysql_fetch_object($res))
		{
			$query="SELECT * FROM ".$this->joomla_auth_db_prefix."user_usergroup_map WHERE user_id='".$id."' AND group_id='".$row->id."'";
			$res = mysql_query($query, $con);
			if($row=mysql_fetch_object($res))
			{
				return true;
			}
			else
			{
				OC_Log::write('OC_USER_JOOMLA_AUTH', "User ".$username." not in group ".$this->joomla_auth_user_group."!", OC_Log::ERROR);
			}
		}
		else
		{
			OC_Log::write('OC_USER_JOOMLA_AUTH', "Group ".$this->joomla_auth_user_group." doesn't exist in Joomla database!", OC_Log::ERROR);
		}
		return false;
	}
	
	/*
	 * connect to database table
	 */
	private function getDbResource()
	{
		$joomla_auth_db_password=OC_Config::getValue( "user_joomla_auth_db_password" );
		$con=mysql_connect($this->joomla_auth_db_host, $this->joomla_auth_db_user, $joomla_auth_db_password);
		if($con==false)
		{
			OC_Log::write('OC_USER_JOOMLA_AUTH', "Error while connecting to Joomla database server ".$this->joomla_auth_db_host."!", OC_Log::ERROR);
			return false;
		}

		$db=mysql_select_db($this->joomla_auth_db_database, $con);
		if($db==false)
		{
			OC_Log::write('OC_USER_JOOMLA_AUTH', "Could not found Joomla database ".$this->joomla_auth_db_database."!", OC_Log::ERROR);
			return false;
		}
		
		return $con;
	}
}
