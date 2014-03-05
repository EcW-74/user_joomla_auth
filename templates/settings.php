<form id="user_joomla_auth" action="#" method="post">
	<fieldset class="personalblock">
		<legend><strong><?php p($l->t('Joomla Authentication Backend'));?></strong></legend>
		<p>
			<label for="joomla_db_host"><?php p($l->t('Server'));?></label>
			<input type="text" id="joomla_db_host" name="joomla_db_host" value="<?php p($_['joomla_db_host']); ?>"
			       title="<?php p($l->t('IP or DNS name of the server where the Joomla database is located (default localhost)'));?>">
		</p>
		<p>
			<label for="joomla_db_user"><?php p($l->t('User'));?></label>
			<input type="text" id="joomla_db_user" name="joomla_db_user" value="<?php p($_['joomla_db_user']); ?>"
			       title="<?php p($l->t('Joomla database user name.'));?>">
		</p>	
		<p>
			<label for="joomla_db_password"><?php p($l->t('Password'));?></label>
			<input type="password" id="joomla_db_password" name="joomla_db_password" value="<?php p($_['joomla_db_password']); ?>"
			       title="<?php p($l->t('Joomla database user password.'));?>">
		</p>
		<p>
			<label for="joomla_db_database"><?php p($l->t('Database'));?></label>
			<input type="text" id="joomla_db_database" name="joomla_db_database" value="<?php p($_['joomla_db_database']); ?>"
			       title="<?php p($l->t('Joomla database.'));?>">
		</p>
		<p>
			<label for="joomla_db_prefix"><?php p($l->t('Prefix'));?></label>
			<input type="text" id="joomla_db_prefix" name="joomla_db_prefix" value="<?php p($_['joomla_db_prefix']); ?>"
			       title="<?php p($l->t('Joomla database table prefix.'));?>">
		</p>
		<p>
			<label for="joomla_user_group"><?php p($l->t('UserGroup'));?></label>
			<input type="text" id="joomla_user_group" name="joomla_user_group" value="<?php p($_['joomla_user_group']); ?>"
			       title="<?php p($l->t('Only Joomla users from this Joomla user group are allowed loggin to owncloud. If the group is not availabel in owncloud, than it\'s created'));?>">
		</p>
		<br />
		<p>
			<label for="joomla_update_user_data"><?php p($l->t('Update user data after login?'));?></label>
			<input type="checkbox" id="joomla_update_user_data" name="joomla_update_user_data" <?php p((($_['joomla_update_user_data'] != false) ? 'checked="checked"' : '')); ?>>
		</p>
		<br />
		<p>
			<input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
			<input type="submit" value="Save" />
		</p>
	</fieldset>
</form>
