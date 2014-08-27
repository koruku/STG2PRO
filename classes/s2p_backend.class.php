<?php

class s2pModuleBackend {
	var $s2p = null;
	var $modx = null;
	var $dumper = null;
	var $now = null;

	function __construct(&$s2p, &$modx) {
		$this->s2p = &$s2p;
		$this->modx = &$modx;

		// MODX標準データベースダンプ用インスタンス【Mysqldumper】
		include_once( MODX_BASE_PATH . "manager/includes/mysql_dumper.class.inc.php" );
		$this->dumper = New Mysqldumper();
		$this->dumper->setDroptables( true );
		$this->dumper->database_server = $this->modx->db->config[ "host" ];
		$this->dumper->dbname = $this->modx->db->config[ "dbase" ];
		$this->dumper->table_prefix = $this->modx->db->config[ "table_prefix" ];
	}
	
	function handlePostback( $tabAction ) {
		switch( $tabAction ) {
			case "tabConfig":
				$this->changeConfig( $tabAction, $_REQUEST );
				break;
			case "tabManually":
				$this->changeConfig( "tabConfig", $_REQUEST );
			case "tabAutoMatically":
				$this->submitSite();
				$docs = $this->setHistory( $tabAction );
				$this->sendUpdateAlert( $docs );
				break;
		default:
			echo "No tab action defined";
			break;
		}
		return;
	}
	
	function changeConfig( $tabAction, $request ) {
		// 定期配信
		$set_array[ "scheduled_switch" ] = intval( $request[ "scheduled_switch" ] );

		// 定期配信日時指定
		if( !isset( $request[ "date" ] ) || !isset( $request[ "time" ] ) ) $set_array[ "scheduled_switch" ] = "0";
		$date = $request[ "date" ];
		$time = $request[ "time" ];
		$set_date = array();
		for( $i = 0; $i < count( $time ); $i++ ) {
			$set_date[ $i ] = array( "w" => "", "t" => "" );
			if( isset( $date[ $i ] ) && $time[ $i ][ 0 ] != "--" && $time[ $i ][ 0 ] != "--" ) {
				$set_date[ $i ][ "w" ] = implode( ",", $date[ $i ] );
				$set_date[ $i ][ "t" ] = intval( $time[ $i ][ 0 ] ) * 3600 + intval( $time[ $i ][ 1 ] ) * 60;
			}
			else {
				$set_date[ $i ][ "t" ] = "--";
			}
		}
		$d = "";
		foreach( $set_date as $arr ) $d .= implode( "", $arr );
		if( $d == "------" ) $set_array[ "scheduled_switch" ] = "0";

		// 予約時刻からcrontab.txtを生成してcrontabへ設定
		if( $set_array[ "scheduled_switch" ] != "0" ) {
			foreach( $set_date as $arr ) {
				if( empty( $arr[ "w" ] ) ) continue;
				$week = explode( ",", $arr[ "w" ] );
				$hour = intval( $arr[ "t" ] / 3600 );
				$minute = ( $arr[ "t" ] % 3600 ) / 60;

				foreach( $week as $w ) {
					$str .= "{$minute} {$hour} * * {$w} /usr/local/bin/php \"" . S2P_MODULE_PATH . "classes/s2p_cron.php\"\n";
				}
				file_put_contents( S2P_MODULE_PATH . "temp/crontab.txt", $str, LOCK_EX );
			}
			if( is_file( S2P_MODULE_PATH . "temp/crontab.txt" ) ) {
				shell_exec( "sudo crontab -u apache " . S2P_MODULE_PATH . "temp/crontab.txt" );
				unlink( S2P_MODULE_PATH . "temp/crontab.txt" );
			}
		}
		else {
			file_put_contents( S2P_MODULE_PATH . "temp/crontab.txt", "", LOCK_EX );
			if( is_file( S2P_MODULE_PATH . "temp/crontab.txt" ) ) {
				shell_exec( "sudo crontab -u apache " . S2P_MODULE_PATH . "temp/crontab.txt" );
				unlink( S2P_MODULE_PATH . "temp/crontab.txt" );
			}
//			$set_date = array_fill( 0, 3, array( "w" => "", "t" => "--" ) );
		}

		$set_array[ "scheduled_time" ] = str_replace( array( ";", "\"" ), array( "|", "'" ), serialize( $set_date ) );

		// 配信通知
		$set_array[ "update_alert" ] = intval( $request[ "update_alert" ] );

		// 配信通知先
		if( !$set_array[ "update_alert" ] ) $set_array[ "alert_mailto" ] = serialize( array() );
		else $set_array[ "alert_mailto" ] = str_replace( array( ";", "\"" ), array( "|", "'" ), serialize( $request[ "alert_mailto" ] ) );

		// 転送先サーバ設定
		$set_array[ "server_ip" ] = $request[ "server_ip" ];
		$set_array[ "site_path" ] = $request[ "site_path" ];
		$set_array[ "ssh_id" ] = $request[ "ssh_id" ];

		// 除外テーブル設定
		$set_array[ "exclude_tables" ] = str_replace( array( ";", "\"" ), array( "|", "'" ), serialize( $request[ "exclude_tables" ] ) );

		// 除外フォルダ・ファイル設定
		$set_array[ "exclude_paths" ] = str_replace( "\r\n", ",", $request[ "exclude_paths" ] );

		// ファイルリネーム設定
		$before = str_replace( "\r\n", ",", $request[ "file_rename_before" ] );
		$after = str_replace( "\r\n", ",", $request[ "file_rename_after" ] );
		$set_array[ "file_rename" ] = str_replace( array( ";", "\"" ), array( "|", "'" ), serialize( array( $before, $after ) ) );

		// モジュール設定へ保存
		$this->s2p->saveParameters( $set_array );

		// サイトリフレッシュ
		$this->modx->clearCache();

		// ManagerLogへ保存
		$this->logManagerLog( $tabAction );

		$this->s2p->ph[ "message" ] = "<p><font color=\"#F00\">s2p設定を更新しました。</font></p>";
		return;
	}
	
	function submitSite() {
		// サイトリフレッシュ
		$this->modx->clearCache();

		// データベースから設定項目を読み込む
		$config = $this->s2p->loadparameters( "val" );

		// 配信時間設定
		$this->now = time();

		// 転送先サーバ設定
		$server_ip = $config[ "server_ip" ];
		$site_path = $config[ "site_path" ];
		$ssh_id = $config[ "ssh_id" ];

		// ファイル転送コマンド生成
		$sh = "sudo rsync -rptv --stats -h --exclude='.thumb_*' --exclude='.editor_*'";
		// .htaccessファイル、config.inc.phpファイル、assets/cacheフォルダ、managerフォルダ、index.phpファイルはデフォルトで同期除外
		foreach( $file_rename_before as $val ) $sh .= " --exclude='" . $val . "'";
		$sh .= " --exclude='assets/cache/*'";
		$sh .= " --exclude='" . implode( "' --exclude='", explode( ",", $config[ "exclude_paths" ] ) ) . "'";
		$sh .= " --exclude='" . implode( "' --exclude='", $file_rename_before ) . "'";
		$sh .= " -e ssh ";
		$sh .= MODX_BASE_PATH . " ";
		$sh .= $ssh_id . "@" . $server_ip . ":" . $site_path;
		$output = array();
		exec( $sh, $output );

		// ファイルリネーム転送
		$file_rename = unserialize( $config[ "file_rename" ] );
		$file_rename_before = explode( ",", $file_rename[ 0 ] );
		$file_rename_after = explode( ",", $file_rename[ 1 ] );
		// .htaccessとconfig.inc.phpはデフォルトでリネームしてアップロード
		for( $i = 0; $i < count( $file_rename_before ); $i++ ) {
			$sh = "sudo scp -p -i /root/.ssh/id_rsa ";
			$sh .= MODX_BASE_PATH . $file_rename_before[ $i ] . " ";
			$sh .= $ssh_id . "@" . $server_ip . ":" . $site_path . $file_rename_after[ $i ];
			$output = array();
			exec( $sh, $output );
		}

		// データベースダンプファイル生成
		$sql = "SHOW TABLE STATUS FROM " . $this->modx->db->config[ "dbase" ] . " LIKE '" . $this->modx->db->config[ "table_prefix" ] . "%'";
		$res = $this->modx->db->query( $sql );
		$alltables = array();
		while( $row = $this->modx->db->getRow( $res ) ) $alltables[] = $row[ "Name" ];
		$exclude_tables = unserialize( $config[ "exclude_tables" ] );
		$exclude_tables = $exclude_tables === NULL ? array() : $exclude_tables;
		$dbtables = array_diff( $alltables, $exclude_tables );
		$this->dumper->setDBtables( $dbtables );
		file_put_contents( S2P_MODULE_PATH . "temp/modx_db_dump.sql", $this->dumper->createDump() );
		chmod( S2P_MODULE_PATH . "temp/modx_db_dump.sql", 0664 );

		// 本番サイトにデータベースを適用
		$sh = "sudo ssh -i /root/.ssh/id_rsa " . $ssh_id . "@" . $server_ip;
		$sh .= " mysql -u " . $this->modx->db->config[ "user" ];
		$sh .= " --password='" . $this->modx->db->config[ "pass" ] . "'";
		$sh .= " --default-character-set=utf8 " . $this->modx->db->config[ "dbase" ];
		$sh .= " < " . S2P_MODULE_PATH . "temp/modx_db_dump.sql";
		$output = array();
		exec( $sh, $output );

		// データベースダンプファイル削除
		unlink( S2P_MODULE_PATH . "temp/modx_db_dump.sql" );

		$this->s2p->ph[ "message" ] = "<p><font color=\"#F00\">手動s2pによる本番サーバへの配信を行いました。</font></p>";

		return $res;
	}

	function setHistory( $tabAction ) {
		$res = $this->modx->db->select( "updatedon", S2P_HISTORY, "", "`updatedon` DESC", "1" );
		$recent = $this->modx->db->getRow( $res );
		if( $recent ) $recent = intval( $recent[ "updatedon" ] );
		else $recent = $this->now;

		$res = $this->modx->db->select(
					"`id`",
					$this->modx->getFullTableName( "site_content" ),
					"`editedon` >= " . $recent . " AND `editedon` < NOW()",
					"`id` ASC"
				);
		$res = $this->modx->db->makeArray( $res );
		$docs = array();
		foreach( $res as $arr ) $docs[] = $arr[ "id" ];
		$docs = implode( ",", $docs );

		if( $tabAction == "tabAutoMatically" ) $user = "system";
		else $user = $this->modx->getLoginUserName();

		$history = array( "updatedon" => $this->now, "updatedby" => $user, "documents" => $docs );

		// モジュール設定へ最終配信時刻を保存
		$this->s2p->saveParameters( array( "last_updatedon" => $this->now ) );

		// 配信履歴を追加
		$this->modx->db->insert( $history, S2P_HISTORY, "`updatedon`, `updatedby`, `documents`" );

		// ManagerLogへ保存
		$this->logManagerLog( $tabAction );

		return $docs;
	}

	function sendUpdateAlert( $docs ) {
		// データベースから設定項目を読み込む
		$config = $this->s2p->loadparameters( "val" );

		// 配信通知 OFFまたは通知先が設定していなければ終了
		$alert_mailto = unserialize( $config[ "alert_mailto" ] );
		if( $config[ "update_alert" ] == "0" || empty( $alert_mailto ) ) return FALSE;

		// MODxMailerインスタンス生成
		$this->modx->loadExtension("MODxMailer");

		// 通知先アドレス取得
		$users = unserialize( $config[ "alert_mailto" ] );
		$alert_mailto = array();
		foreach( $users as $uid ) {
			$user_info = $this->modx->getUserInfo( $uid );
			$alert_mailto[] = array( "username" => $user_info[ "username" ], "email" => $user_info[ "email" ] );
		}

		// グローバル設定＞送信者メールアドレス（カンマ区切りだった場合、一番左のアドレス）
		$from = current( explode( ",", $this->modx->config[ "emailsender" ] ) );

		// メールプレースホルダ取得
		$mail_ph[ "site_name" ] = $this->modx->config[ "site_name" ];
		$mail_ph[ "language" ] = $this->modx->config[ "manager_language" ];
		$mail_ph[ "date" ] = strftime( "%Y/%m/%d", $this->now );
		$mail_ph[ "time" ] = strftime( "%H:%M:%S", $this->now );

		// メールタイトル
		$tpl = $this->s2p->lang[ "S2P_mail_template_subject" ];
		foreach( $mail_ph as $key => $val ) $tpl = str_replace( "[+" . $key . "+]", $val, $tpl );
		$subject = $tpl;

		// メール本文
		$docs = explode( ",", $docs );
		$mail_ph[ "contents" ] = "";
		if( empty( $docs ) )
			$body = "修正・更新されたドキュメントはありませんでした。";
		else {
			foreach( $docs as $doc_id ) {
				$doc = $this->modx->getPageInfo( $doc_id, 1, "id,editedon,createdon" );
				$ph[ "id" ] = $doc[ "id" ];
				$ph[ "new" ] = $doc[ "editedon" ] == $doc[ "createdon" ] ? "New" : "";
				$site_url = $config[ "pro_url" ];
				$ph[ "url" ] = $site_url . $this->modx->makeUrl( $doc_id );
				$contents = $this->s2p->lang[ "S2P_mail_template_contents"];
				foreach( $ph as $key => $val ) $contents = str_replace( "[+" . $key . "+]", $val, $contents );
				$mail_ph[ "contents" ] .= $contents;
			}
			$body = $this->s2p->lang[ "S2P_mail_template_body" ];
			foreach( $mail_ph as $key => $val ) $body = str_replace( "[+" . $key . "+]", $val, $body );
		}

		// メール生成・送信
		$this->modx->mail->IsHTML( FALSE );
		$this->modx->mail->From = $from;
		$this->modx->mail->Subject = $subject;
		$this->modx->mail->Body = $body;
		foreach( $alert_mailto as $user ) $this->modx->mail->AddAddress( $user[ "email" ], $user[ "username" ] );

		return $this->modx->mail->send();
	}
	
	function applyDatabase() {
		$source = file_get_contents( S2P_MODULE_PATH . "temp/modx_db_dump.sql" );
		unlink( S2P_MODULE_PATH . "temp/modx_db_dump.sql" );
		$this->dumper->import_sql( $source );
		$result = $_SESSION[ "result_msg" ];
		return $result;
	}

	function logManagerLog( $tabAction ) {
		// MODX標準管理操作ログ用インスタンス【logHandler】
		include_once( MODX_BASE_PATH . "manager/includes/log.class.inc.php" );
		$log = new logHandler;
		switch( $tabAction ) {
			case "tabConfig":
				$log->initAndWriteLog( $this->s2p->lang[ "S2P_log_config" ] );
				break;
			case "tabManually":
				$log->initAndWriteLog( $this->s2p->lang[ "S2P_log_manually" ] );
				break;
			case "tabAutoMatically":
				$log->initAndWriteLog( $this->s2p->lang[ "S2P_log_automatically" ], "0", "system", "112", $this->s2p->moduleId, "s2p Module" );
				break;
			default:
				break;
		}
	}
}
