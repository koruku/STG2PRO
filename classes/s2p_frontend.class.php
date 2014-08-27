<?php

class s2pModuleFrontend {
	var $s2p = null;
	var $modx = null;
	var $prohibition = null;
	var $form_data = array();
	var $history_data = array();
	var $paging = 10;

	function __construct(&$s2p, &$modx) {
		$this->s2p = &$s2p;
		$this->modx = &$modx;

		// モジュール設定から設定項目を読み込む
		$parameters = $this->s2p->loadParameters( "val" );
		foreach( $parameters as $setting_name => $setting_value )
			$this->form_data[ $setting_name ] = $setting_value;

		// 設定された転送先IPと自分のIPが等しければ、本番サイトなので動作しない
		if( $_SERVER[ "SERVER_ADDR" ] == $this->form_data[ "server_ip" ] ) {
			$this->s2p->ph[ "message" ] = $this->s2p->lang[ "S2P_prohibition" ];
			$this->prohibition = true;
		}

		// ページング数を設定
		$this->paging = $this->modx->event->params[ "paging" ];
	}
	
	function getViews() {
		// モジュール設定から設定項目を読み込む
		$parameters = $this->s2p->loadParameters( "val" );
		foreach( $parameters as $setting_name => $setting_value )
			$this->form_data[ $setting_name ] = $setting_value;

		// 最終配信時刻を表示
		$this->s2p->ph[ "last_updatedon" ] = strftime( "%Y/%m/%d %H:%M:%S", $this->form_data[ "last_updatedon" ] );

		// 配信履歴の為のページング生成
		$res = $this->modx->db->select( "id", S2P_HISTORY );
		$total = $this->modx->db->getRecordCount( $res );
		$pagenate = ceil( intval( $total ) / $this->paging );

		$page = isset( $_REQUEST[ "p" ] ) ? $_REQUEST[ "p" ] : "1";
		if( $page != 1 )
			$page_output = "<p class=\"pagenate\"><a href=\"index.php?a=112&id=9&p=" . $page . "\">＜</a> ";
		else
			$page_output = "<p class=\"pagenate\">";
		for( $i = 1; $i <= $pagenate; $i++ ) {
			if( $i != $page )
				$page_output .= "<a href=\"index.php?a=112&id=9&p=" . $i . "\">" . $i . "</a> ";
			else
				$page_output .= $i . " ";
		}
		if( $page != $pagenate )
			$page_output .= "<a href=\"index.php?a=112&id=9&p=" . ( $page + 1 ) . "\">＞</a></p>";
		else
			$page_output .= "</p>";

		$this->s2p->ph[ "pagenate" ] = $page_output;

		// ページングに基づいた配信履歴データを取得
		$limit = ( ( intval( $page ) - 1 ) * $this->paging ) . ", " . $this->paging;
		$res = $this->modx->db->select( "*", S2P_HISTORY , "", "`id` DESC", $limit );
		$this->history_data = $this->modx->db->makeArray( $res );

		// テンプレートへ適用
		$this->renderSettings();
		$this->renderExclusion();
		$this->renderHistory();
	}
	
	function renderSettings() {
		// 定期配信
		$this->s2p->ph[ "scheduled_switch_" . $this->form_data[ "scheduled_switch" ] . "_checked"] = "checked=\"checked\"";

		// 定期配信日時指定
		// 時間セレクトボックス生成
		for( $i = 0; $i < 3; $i++ )
			for( $j = 0; $j < 24; $j++ )
				$this->s2p->ph[ "hour_" . $i ] .= "\t\t\t\t<option value=\"" . $j . "\" [+time_" . $i . "_hour_" . $j . "_selected+]>" . sprintf( "%02d", $j ) . "</option>\n";
		for( $i = 0; $i < 3; $i++ )
			for( $j = 0; $j < 60; $j++ )
				$this->s2p->ph[ "minute_" . $i ] .= "\t\t\t\t<option value=\"" . $j . "\" [+time_" . $i . "_minute_" . $j . "_selected+]>" . sprintf( "%02d", $j ) . "</option>\n";
		// データ適用
		$scheduled_time_array = unserialize( $this->form_data[ "scheduled_time" ] );
		foreach( $scheduled_time_array as $key => $arr ) {
			$week = explode( ",", $arr[ "w" ] );
			foreach( $week as $val ) $this->s2p->ph[ "date_" . $key . "_" . $val . "_checked"] = "checked=\"checked\"";
			if( strpos( $arr[ "t" ], "--" ) !== FALSE ) {
				$hour = "N"; $minute = "N";
			}
			else {
				$hour = intval( intval( $arr[ "t" ] ) / 3600 );
				$minute = intval( ( intval( $arr[ "t" ] ) - $hour * 3600 ) / 60 );
			}
			$this->s2p->ph[ "time_".$key."_hour_".$hour."_selected" ] = "selected=\"selected\"";
			$this->s2p->ph[ "time_".$key."_minute_".$minute."_selected" ] = "selected=\"selected\"";
		}

		// 配信通知
		$this->s2p->ph[ "update_alert_" . $this->form_data[ "update_alert" ] . "_checked"] = "checked=\"checked\"";

		// MODX管理アカウントを取得
		$res = $this->modx->db->select( "id,username", "modx_manager_users" );
		while( $row = $this->modx->db->getRow( $res ) ) {
			$this->s2p->ph[ "alert_mailto" ] .= "<label><input type=\"checkbox\" name=\"alert_mailto[]\" value=\"" . $row[ "id" ] . "\" [+alert_mailto_" . $row[ "id" ] . "+] />" . $row[ "username" ] . "</label>　";
		}
		// 配信通知先
		$alert_mailto = unserialize( $this->form_data[ "alert_mailto" ] );
		foreach( $alert_mailto as $val ) {
			$this->s2p->ph[ "alert_mailto_" . $val ] = "checked=\"checked\"";
		}

		// 転送先サーバ設定
		$this->s2p->ph[ "server_ip" ] = $this->form_data[ "server_ip" ];
		$this->s2p->ph[ "site_path" ] = $this->form_data[ "site_path" ];
		$this->s2p->ph[ "ssh_id" ] = $this->form_data[ "ssh_id" ];

		$this->s2p->ph['view.settings'] = $this->s2p->parseTemplate('settings.tpl', $this->s2p->ph);
	}   
	
	function renderExclusion() {
		// 除外テーブル設定
//		$sql = "SHOW TABLE STATUS FROM " . $this->modx->db->config["dbase"] . " LIKE '" . $this->modx->db->config["table_prefix"] . "%'";
		$sql = "SHOW TABLE STATUS FROM " . $this->modx->db->config["dbase"];
		$res = $this->modx->db->query( $sql );
		$i = 0;
		$exclude_tables = unserialize( $this->form_data[ "exclude_tables" ] );
		$exclude_tables = $exclude_tables === NULL ? array() : $exclude_tables;
		while( $row = $this->modx->db->getRow( $res ) ) {
			$name = $row[ "Name" ];
//			if( $name == S2P_CONFIG || $name == S2P_HISTORY ) continue;
			$rows = $row[ "Data_length" ];
			$volume = $this->modx->nicesize( $row[ "Index_length" ] + $row[ "Data_length" ] + $row[ "Data_free" ] );
			$style = $i % 2 != 0 ? "bgcolor=\"#EEEEEE\"" : "bgcolor=\"#FFFFFF\"";
			$i++;
			$checked = array_search( $name, $exclude_tables ) !== FALSE ? "checked=\"checked\"" : "";
			$tmp = <<<EOT
	<tr {$style}>
		<td style="text-align:right;">{$i}</td>
		<td style="text-align:left;"><label><input type="checkbox" name="exclude_tables[]" value="{$name}" {$checked}/><b style="color:#009933">{$name}</b></label></td>
		<td style="text-align:right;">{$rows}</td>
		<td style="text-align:right;">{$volume}</td>
	<tr>
EOT;
			$this->s2p->ph[ "tables" ] .= $tmp;
		}

		// 除外フォルダ設定
		$this->s2p->ph[ "exclude_paths" ] = str_replace( ",", "\n", $this->form_data[ "exclude_paths" ] );
		// ファイルリネーム設定
		$file_rename = unserialize( $this->form_data[ "file_rename" ] );
		$this->s2p->ph[ "file_rename_before" ] = str_replace( ",", "\n", $file_rename[ 0 ] );
		$this->s2p->ph[ "file_rename_after" ] = str_replace( ",", "\n", $file_rename[ 1 ] );

		$this->s2p->ph['view.exclusion'] = $this->s2p->parseTemplate('exclusion.tpl', $this->s2p->ph);
	}
		function renderHistory() {
			// 配信履歴
			$history_data = $this->history_data;
			foreach( $history_data as $arr ) {
				$this->s2p->ph[ "hist_id" ] = $arr[ "id" ];
				$this->s2p->ph[ "hist_updatedon" ] = strftime( "%Y/%m/%d<br />%H:%M:%S", $arr[ "updatedon" ] );
				$this->s2p->ph[ "hist_updatedby" ] = $arr[ "updatedby" ];
				$documents = explode( ",", $arr[ "documents" ] );
				// 更新ドキュメントリスト
				$this->s2p->ph[ "docs" ] = "";
				foreach( $documents as $docid ) {
					if( !empty( $docid ) ) {
						$document = $this->modx->getPageInfo( $docid, 1, "pagetitle, editedby, editedon");
						$pagetitle = $document[ "pagetitle" ];
						$editedon = strftime( "%Y/%m/%d %H:%M:%S", $document[ "editedon" ] );
						$user = $this->modx->getUserInfo( $document[ "editedby" ] );
						$user = $user[ "username" ];
						$this->s2p->ph[ "docs" ] .= <<<EOT
					<tr>
						<td>{$docid}</td>
						<td>{$pagetitle}</td>
						<td>{$user}</td>
						<td>{$editedon}</td>
					</tr>
EOT;
					}
					else {
						$no_data = $this->s2p->lang[ "S2P_history_doc_no_data" ];
						$this->s2p->ph[ "docs" ].= <<<EOT
					<tr>
						<td colspan="4">{$no_data}</td>
					</tr>
EOT;
					}
				}
				$this->s2p->ph[ "history" ] .= $this->s2p->parseTemplate( "hist.tpl", $this->s2p->ph );
			}
			$this->s2p->ph[ "view.history" ] = $this->s2p->parseTemplate( "history.tpl" , $this->s2p->ph );
		}

		function judgmentalUpdate(){
			// 定期配信がOFFがであれば何もせずに終了
			if( $this->form_data[ "scheduled_switch"] == 0 ) return FALSE;

			// s2pmodule/tempディレクトリに.done_e2sファイルがあればTRUE、無ければFALSEを返す。
			return file_exists( "assets/modules/s2pmodule/temp/.done_s2p" );
			
			// 最終配信時刻以降にドキュメントの更新がなければFALSEを返す
//			$last_updatedon = $this->form_data[ "last_updatedon" ];
//			$res = $this->modx->db->select( "*", $this->modx->getFullTableName( "site_content" ), "`editedon` > " . $last_updatedon . " AND `editedon` <= NOW()" );
//			$count = $this->modx->db->getRecordCount( $res );
//			return $count > 0 ? TRUE : FALSE;
		}
}
?>
