
<form id="useroptions" name="useroptions" method="post" action="me2.php">
	<input name="_useroptions_submit" type="hidden" value="1" />
	<input name="changepass" type="hidden" id="changepass" value="0" />

<!--	<table cellpadding="0" cellspacing="0" class="useroptions">-->
	<table class="optionstbl noborder">
		<tr>
			<td class="right halfwidth">Display name :</td>
			<td class="halfwidth"><input name="displayname" type="text" id="displayname" value="<?= $_SESSION['displayname'] ?>" size="32" maxlength="32" /></td>
		</tr>
		<tr>
			<td class="right halfwidth">Timezone :</td>
			<td class="halfwidth">
				<select name="timezone" id="timezone">
				<?
				foreach ($timezones as $timezone) {
					if ($timezone == $_SESSION['timezone']) {
						print '	<option value="' . $timezone . '" selected="selected">' . $timezone . '</option>' . PHP_EOL;
					} else {
						print '	<option value="' . $timezone . '">' . $timezone . '</option>' . PHP_EOL;
					}
				}
				?>
				</select>
			</td>
		</tr>
		<tr id="pwdtrigger" class="pwdtrigger">
			<td class="right halfwidth"> <a onclick="javascript:changepassword('show');return false;" class="showLink"><img src="images/down.gif" width="5" height="12" alt="down"/> Change Password</a> </td>
			<td class="halfwidth"> &nbsp;</td>
		</tr>
		<tr id="changepasstable" class="changepasstable" style="display: none;">
			<td colspan="2" class="fullwidth">

			<table class="fullwidth noborder">
				<tr>
					<td class="right halfwidth">Current Password :</td>
					<td class="halfwidth"><input name="oldpassword" type="password" id="oldpassword" size="30" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="right halfwidth">New Password : </td>
					<td class="halfwidth"><input name="password" type="password" id="password" size="30" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="right halfwidth"><small>(Again) </small>New Password :</td>
					<td class="halfwidth"><input name="password2" type="password" id="password2" size="30" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="right halfwidth"><a onclick="javascript:changepassword('hide');return false;" class="hideLink"><img src="images/up.gif" width="5" height="12" alt="up"/> Don't Change Password</a></td>
					<td class="halfwidth">&nbsp;</td>
				</tr>
			</table>

			</td>
			</tr>
		<tr>
			<td class="right halfwidth">
<!--			  <input type="reset" name="Reset" id="Reset" value="Reset" />  &nbsp; &nbsp; &nbsp;-->
			</td>
			<td class="halfwidth">
				<input type="submit" name="Submit" id="Submit" value="Save" />
				&nbsp; &nbsp; &nbsp; <a href="index.php">Cancel</a>
			</td>
		</tr>
	</table>
</form>
