/*
 * &s2p_history=s2p履歴テーブル名;string;s2p_history &paging=履歴ページング数;number;10 &scheduled_switch=定期配信;number;0 &scheduled_time=定期配信日時指定;string;a:3:{i:0|a:2:{s:1:'w'|s:0:''|s:1:'t'|s:2:'--'|}i:1|a:2:{s:1:'w'|s:0:''|s:1:'t'|s:2:'--'|}i:2|a:2:{s:1:'w'|s:0:''|s:1:'t'|s:2:'--'|}} &update_alert=配信通知;number;0 &alert_mailto=配信通知先;string;a:0:{} &exclude_tables=除外テーブル設定;string;a:4:{i:0|s:17:'modx_active_users'|i:1|s:14:'modx_event_log'|i:2|s:16:'modx_manager_log'|i:3|s:20:'modx_system_settings'|} &exclude_paths=除外フォルダ・ファイル設定;string;assets/cache/*,manager/*,index.php &file_rename=ファイルリネーム設定;string;a:2:{i:0|s:41:'.htaccess,manager/includes/config.inc.php'|i:1|s:32:'.htaccess_stg,config.inc_stg.php'|} &server_ip=転送先サーバIP;string; &site_path=転送先絶対パス;string; &ssh_id=転送SSHアカウント;string; &ssh_pw=転送SSHパスワード;string;vQNTYpD &pro_url=転送先サイトURL;string;http://sgi-chs.senku.jp/ &stg_url=ステージングサイトURL;string;http://dev.sgiweb.info/sgi-chs/ &last_updatedon=最終配信時刻;number;1408438207
 */
include_once( MODX_BASE_PATH."assets/modules/s2pmodule/classes/s2pmodule.class.php" );
include_once( MODX_BASE_PATH."assets/modules/s2pmodule/classes/s2p_frontend.class.php" );
include_once( MODX_BASE_PATH."assets/modules/s2pmodule/classes/s2p_backend.class.php" );

$s2p = new s2pModule( $modx );
$s2p->ph = $s2p->getLang();
$s2p->ph[ "theme" ] = $s2p->getTheme();

$s2pf = new s2pModuleFrontend( $s2p, $modx );
$s2pb = new s2pModuleBackend( $s2p, $modx );

if( $s2pf->prohibition ) {
	echo $s2p->parseTemplate( "prohibition.tpl", $s2p->ph );
}
else {
	if( isset( $_REQUEST[ "tabAction" ] ) ) {
		$s2pb->handlePostback( $_REQUEST[ "tabAction" ] );
	}
	$s2pf->getViews();
	echo $s2p->parseTemplate( "main.tpl", $s2p->ph );
}
