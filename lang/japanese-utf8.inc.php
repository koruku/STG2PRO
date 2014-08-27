<?php
/**
 * S2P Module - japanese-utf8.inc.php
 *
 * Purpose: Contains the language strings for use in the module.
 * Author: Shohei Akita @ SENKU
 * For: MODS CMS (modx.com)
 * Date:2014/08/10 Version: 1.0.0
 * Initial translated:
 *
 */

//-- JAPANESE LANGUAGE FILE ENCODED IN UTF-8

//-- titles
$_lang[ "S2P_module_title" ] = " s2p Module";
$_lang[ "S2P_action_title" ] = " 操作を選択します";
$_lang[ "S2P_prohibition" ] = "<p style=\"color:#F00;\">本モジュールは本番サイトでの使用はできません。</p>";

//-- tabs
$_lang[ "S2P_settings" ] = "配信設定";
$_lang[ "S2P_manually" ] = "手動配信";
$_lang[ "S2P_exclusion" ] = "除外・リネーム設定";
$_lang[ "S2P_history" ] = "配信履歴";
$_lang[ "S2P_last_updatedon" ] = "最終配信時刻：";

//-- buttons
$_lang[ "S2P_close" ] = "閉じる";
$_lang[ "S2P_cancel" ] = "戻る";
$_lang[ "S2P_go" ] = "選択";
$_lang[ "S2P_save" ] = "更新";
$_lang[ "S2P_sort_another" ] = "別の整列";

$_lang[ "S2P_sun" ] = "日";
$_lang[ "S2P_mon" ] = "月";
$_lang[ "S2P_tue" ] = "火";
$_lang[ "S2P_wed" ] = "水";
$_lang[ "S2P_thu" ] = "木";
$_lang[ "S2P_fri" ] = "金";
$_lang[ "S2P_sat" ] = "土";
$_lang[ "S2P_hour" ] = "時";
$_lang[ "S2P_minutes" ] = "分";

//-- setting tab
$_lang[ "S2P_settings_sche" ] = "定期配信";
//$_lang[ "S2P_settings_sche_decs" ] = "";
$_lang[ "S2P_setting_sche_switch_on" ] = "ON";
$_lang[ "S2P_setting_sche_switch_off" ] = "OFF";

$_lang[ "S2P_settings_scheduled_date" ] = "定期配信日時指定";
//$_lang[ "S2P_settings_scheduled_date_desc" ] = "";

$_lang[ "S2P_settings_alert" ] = "配信通知";
$_lang[ "S2P_settings_alert_desc" ] = "「グローバル設定＞セキュリティ＞送信者メールアドレス」に送られます。";
$_lang[ "S3P_S2P_settings_alert_switch_on" ] = "ON";
$_lang[ "S3P_S2P_settings_alert_switch_off" ] = "OFF";

$_lang[ "S2P_settings_alert_mailto" ] = "配信通知先";
$_lang[ "S2P_settings_alert_mailto_desc" ] = "「グローバル設定＞セキュリティ＞送信者メールアドレス」に送られます。";

$_lang[ "S2P_settings_sever_settings" ] = "転送先サーバ設定";
//$_lang[ "S2P_settings_sever_settings_desc" ] = "";
$_lang[ "S2P_settings_sever_ip" ] = "転送先サーバIP";
$_lang[ "S2P_settings_site_path" ] = "転送先絶対パス";
$_lang[ "S2P_settings_ssh_id" ] = "転送SSHアカウント";

//-- manually tab
$_lang[ "S2P_manually_desc" ] = "「保存して配信する」ボタン押下で、配信設定の保存と即時配信が行われます。";
$_lang[ "S2P_manually_manual_deploy" ] = "s2p配信開始";

//-- exclusion tab
$_lang[ "S2P_exclusion_tables" ] = "除外テーブル設定";
$_lang[ "S2P_exclusion_tables_desc" ] = "<strong style=\"color:#F00;\">同期しない</strong>テーブルを選択";
$_lang[ "S2P_exclusion_tables_collumn_table_name" ] = "テーブル名";
$_lang[ "S2P_exclusion_tables_collumn_rows" ] = "件数";
$_lang[ "S2P_exclusion_tables_collumn_volume" ] = "データサイズ";

$_lang[ "S2P_exclusion_folders" ] = "除外フォルダ・ファイル設定";
$_lang[ "S2P_exclusion_folders_desc" ] = "<strong style=\"color:#F00;\">同期しない</strong>フォルダやファイルのパスを入力";

$_lang[ "S2P_file_rename" ] = "ファイルリネーム設定";
$_lang[ "S2P_file_rename_desc" ] = "STG上のファイルをリネームしてアップロード";

//-- hisotry tab
$_lang[ "S2P_history_updatedon" ] = "日時";
$_lang[ "S2P_history_updatedby" ] = "実行者";
$_lang[ "S2P_history_updated_docs" ] = "更新ドキュメント";
$_lang[ "S2P_history_doc_id" ] = "ID";
$_lang[ "S2P_history_pagetitle" ] = "タイトル";
$_lang[ "S2P_history_editedby" ] = "更新者";
$_lang[ "S2P_history_editedon" ] = "更新日時";
$_lang[ "S2P_history_doc_no_data" ] = "更新されたドキュメントはありません。";

//-- Mail template
$_lang[ "S2P_mail_template_subject" ] = "[+site_name+] - [+language+] [+date+] Update information";
$_lang[ "S2P_mail_template_body" ] = "[+site_name+] - [+language+] Updated at [+date+] [+time+]
add/modify contents URI below:
[+contents+]";
$_lang[ "S2P_mail_template_contents" ] = "[+new+] [+id+] [+url+]\n";

//-- Log
$_lang[ "S2P_log_config" ] = "S2P設定保存";
$_lang[ "S2P_log_manually" ] = "S2P手動配信";
$_lang[ "S2P_log_automatically" ] = "S2P自動配信";

?>