<h3 style="margin-top: 0;">[+lang.S2P_settings_sche+]</h3>
<!-- <p>[+lang.S2P_settings_alt_desc+]</p> -->
<label><input type="radio" name="scheduled_switch" value="1" [+scheduled_switch_1_checked+]/> [+lang.S2P_setting_sche_switch_on+]</label>　
<label><input type="radio" name="scheduled_switch" value="0" [+scheduled_switch_0_checked+]/> [+lang.S2P_setting_sche_switch_off+]</label>
<div class="scheduled_date">
	<h3>[+lang.S2P_settings_scheduled_date+]</h3>
	<!-- <p>[+lang.S2P_settings_scheduled_date_desc+]</p> -->
	<p>①　
		<label><input type="checkbox" name="date[0][]" value="0" [+date_0_0_checked+]/>[+lang.S2P_sun+]</label>　
		<label><input type="checkbox" name="date[0][]" value="1" [+date_0_1_checked+]/>[+lang.S2P_mon+]</label>　
		<label><input type="checkbox" name="date[0][]" value="2" [+date_0_2_checked+]/>[+lang.S2P_tue+]</label>　
		<label><input type="checkbox" name="date[0][]" value="3" [+date_0_3_checked+]/>[+lang.S2P_wed+]</label>　
		<label><input type="checkbox" name="date[0][]" value="4" [+date_0_4_checked+]/>[+lang.S2P_thu+]</label>　
		<label><input type="checkbox" name="date[0][]" value="5" [+date_0_5_checked+]/>[+lang.S2P_fri+]</label>　
		<label><input type="checkbox" name="date[0][]" value="6" [+date_0_6_checked+]/>[+lang.S2P_sat+]</label>　
		<select name="time[0][]">
			<option value="--" [+time_0_hour_N_selected+]>--</option>
[+hour_0+]
		</select>[+lang.S2P_hour+]
		<select name="time[0][]">
			<option value="--" [+time_0_minute_N_selected+]>--</option>
[+minute_0+]
		</select>[+lang.S2P_minutes+]
	</p>
	<p>②　
		<label><input type="checkbox" name="date[1][]" value="0" [+date_1_0_checked+]/>[+lang.S2P_sun+]</label>　
		<label><input type="checkbox" name="date[1][]" value="1" [+date_1_1_checked+]/>[+lang.S2P_mon+]</label>　
		<label><input type="checkbox" name="date[1][]" value="2" [+date_1_2_checked+]/>[+lang.S2P_tue+]</label>　
		<label><input type="checkbox" name="date[1][]" value="3" [+date_1_3_checked+]/>[+lang.S2P_wed+]</label>　
		<label><input type="checkbox" name="date[1][]" value="4" [+date_1_4_checked+]/>[+lang.S2P_thu+]</label>　
		<label><input type="checkbox" name="date[1][]" value="5" [+date_1_5_checked+]/>[+lang.S2P_fri+]</label>　
		<label><input type="checkbox" name="date[1][]" value="6" [+date_1_6_checked+]/>[+lang.S2P_sat+]</label>　
		<select name="time[1][]">
			<option value="--" [+time_1_hour_N_selected+]>--</option>
[+hour_1+]
		</select>[+lang.S2P_hour+]
		<select name="time[1][]">
			<option value="--" [+time_1_minute_N_selected+]>--</option>
[+minute_1+]
		</select>[+lang.S2P_minutes+]
	</p>
	<p>③　
		<label><input type="checkbox" name="date[2][]" value="0" [+date_2_0_checked+]/>[+lang.S2P_sun+]</label>　
		<label><input type="checkbox" name="date[2][]" value="1" [+date_2_1_checked+]/>[+lang.S2P_mon+]</label>　
		<label><input type="checkbox" name="date[2][]" value="2" [+date_2_2_checked+]/>[+lang.S2P_tue+]</label>　
		<label><input type="checkbox" name="date[2][]" value="3" [+date_2_3_checked+]/>[+lang.S2P_wed+]</label>　
		<label><input type="checkbox" name="date[2][]" value="4" [+date_2_4_checked+]/>[+lang.S2P_thu+]</label>　
		<label><input type="checkbox" name="date[2][]" value="5" [+date_2_5_checked+]/>[+lang.S2P_fri+]</label>　
		<label><input type="checkbox" name="date[2][]" value="6" [+date_2_6_checked+]/>[+lang.S2P_sat+]</label>　
		<select name="time[2][]">
			<option value="--" [+time_2_hour_N_selected+]>--</option>
[+hour_2+]
		</select>[+lang.S2P_hour+]
		<select name="time[2][]">
			<option value="--" [+time_2_minute_N_selected+]>--</option>
[+minute_2+]
		</select>[+lang.S2P_minutes+]
	</p>
</div>
<br />
<h3>[+lang.S2P_settings_alert+]</h3>
<!-- <p>[+lang.S2P_settings_alert_desc+]</p> -->
<label><input type="radio" name="update_alert" value="1" [+update_alert_1_checked+]/> [+lang.S3P_S2P_settings_alert_switch_on+]</label>　
<label><input type="radio" name="update_alert" value="0" [+update_alert_0_checked+]/> [+lang.S3P_S2P_settings_alert_switch_off+]</label>
<div class="alert_mailto">
	<h3>[+lang.S2P_settings_alert_mailto+]</h3>
	<!-- <p>[+lang.S2P_settings_alert_mailto_desc+]</p> -->
	[+alert_mailto+]
</div>
<ul class="actionButtons" style="margin-top: 30px;"><li id="Button2" class="server"><a href="#">[+lang.S2P_settings_sever_settings+]</a></li></ul>
<div class="server_block">
	<h3>[+lang.S2P_settings_sever_ip+]</h3>
	<input type="text" name="server_ip" value="[+server_ip+]" />
	<h3>[+lang.S2P_settings_site_path+]</h3>
	<input type="text" name="site_path" size="40" value="[+site_path+]" />
	<h3>[+lang.S2P_settings_ssh_id+]</h3>
	<input type="text" name="ssh_id" value="[+ssh_id+]" />
</div>
