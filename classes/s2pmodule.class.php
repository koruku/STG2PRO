<?php

class s2pModule {
	var $modx = null;
	var $moduleId = null;
	var $lang = array();
	var $ph = array();
	var $theme = "";
	var $fileRegister = array();

	function __construct(&$modx) {
		$this->modx = $modx;
		$this->ph[ "cookiepath" ] = MODX_BASE_URL;

		if( isset( $_REQUEST[ "id" ] ) ) $this->moduleId = $_REQUEST[ "id" ];
		else {
			$res = $this->modx->db->select( "`id`", $this->modx->getFullTableName( "site_modules" ), "`name` LIKE 's2p Module'" );
			$this->moduleId = $this->modx->db->getValue( $res );
		}

		// s2pモジュールディレクトリ定数定義
		$path = explode( "/", __DIR__ );
		array_pop( $path );
		$path = implode( "/", $path ) . "/";
		define( S2P_MODULE_PATH, $path );
		define( S2P_MODULE_ASSETS, "assets/modules/s2pmodule/" );

 		// s2p履歴テーブル名定数定義
		if( !defined( S2P_HISTORY ) && $this->modx->event->params[ "s2p_history" ] !== NULL )
			define( S2P_HISTORY, $this->modx->db->config[ "table_prefix" ] . $this->modx->event->params[ "s2p_history" ] );
		else
			define( S2P_HISTORY, "modx_s2p_history" );
	}

	function getLang() {
		$_lang = array();
		$ph = array();
		$managerLanguage = $this->modx->config[ "manager_language" ];
		$managerLanguage = "japanese-utf8";
		$userId = $this->modx->getLoginUserID();
		if( !empty( $userId ) ) {
			$lang = $this->modx->db->select( "`setting_value`", $this->modx->getFullTableName( "user_settings" ), "`setting_name` LIKE 'manager_language' AND user=" . $userId );
	
			if( $this->modx->db->getRecordCount( $lang ) > 0 ) {
				$row = $this->modx->db->getRow( $lang );
				$managerLanguage = $row[ "setting_value" ];
			}
		}

		include MODX_BASE_PATH . "manager/includes/lang/english.inc.php";
		if( $managerLanguage != "english" ) {
			if( file_exists( MODX_BASE_PATH . "manager/includes/lang/" . $managerLanguage . ".inc.php" ) ) {
	 			include MODX_BASE_PATH . "manager/includes/lang/" . $managerLanguage . ".inc.php";
			}
		}
		
		include MODX_BASE_PATH . "assets/modules/s2pmodule/lang/english.inc.php";
		if( $managerLanguage != "english" ) {
			if( file_exists( MODX_BASE_PATH . "assets/modules/s2pmodule/lang/" . $managerLanguage . ".inc.php" ) ) {
				include MODX_BASE_PATH . "assets/modules/s2pmodule/lang/" . $managerLanguage . ".inc.php";
			}
		}
		$this->lang = $_lang;
		foreach( $_lang as $key => $value ) {
			$ph[ "lang." . $key ] = $value;
		}
		return $ph;
	}

	function getTheme() {
		$theme = $this->modx->config[ "manager_theme" ];
		$this->theme = ( $theme != "" ) ? "/" . $theme : "";
		return $this->theme;
	}

	function getFileContents($file) {
		if( empty( $file ) ) {
			return false;
		}
		else {
			$file = MODX_BASE_PATH . "assets/modules/s2pmodule/templates/" . $file;
			if( array_key_exists( $file, $this->fileRegister ) ) {
				return $this->fileRegister[ $file ];
			}
			else {
				$contents = file_get_contents( $file );
				$this->fileRegister[ $file ] = $contents;
				return $contents;
			}
		}
	}

	function loadTemplates() {
		$this->fileGetContents( "main.tpl" );
	}

	function parseTemplate( $tpl, $values = array() ) {
		$tpl = array_key_exists( $tpl, $this->fileRegister ) ? $this->fileRegister[ $tpl ] : $this->getFileContents( $tpl );
		if( $tpl ) {
			foreach( $values as $key => $value ) {
				$tpl = str_replace( "[+" . $key . "+]", $value, $tpl );
			}
			$tpl = preg_replace( "/(\[\+.*?\+\])/" ,"" , $tpl );
			return $tpl;
		}
		else {
			return "";
		}
	}

	function loadParameters( $mode ) {
		$res = $this->modx->db->select( "*", $this->modx->getFullTableName( "site_modules" ), "`id` = '" . $this->moduleId . "'" );
		$content = $this->modx->db->getRow( $res );
		$parameters = array();
		if( !empty( $content[ "properties" ] ) ) {
			$tmpParams = explode( "&", $content[ "properties" ] );
			for( $x = 1; $x < count( $tmpParams ); $x++ ) {
				$pTmp = explode( "=", $tmpParams[ $x ] );
				$pvTmp = explode( ";", trim( $pTmp[ 1 ] ) );
				$pvTmp[ 2 ] = str_replace( array( "|", "'" ), array( ";", "\"" ), $pvTmp[ 2 ] );
				switch( $mode ) {
					case "all":
						$parameters[ $pTmp[ 0 ] ] = array( "desc" => $pvTmp[ 0 ], "type" => $pvTmp[ 1 ], "val" => $pvTmp[ 2 ] );
						break;
					case "val":
						$parameters[ $pTmp[ 0 ] ] = $pvTmp[ 2 ];
						break;
				}
			}
		}
		return $parameters;
	}

	function saveParameters( $set_array ) {
		$parameters = $this->loadParameters( "all" );
		foreach( $set_array as $key => $val ) $parameters[ $key ][ "val" ] = strval( $val );
		$tmpParams = array();
		foreach( $parameters as $key => $param ) {
			$tmpParams[] = $key . "=" . implode( ";", str_replace( array( ";", "\"" ), array( "|", "'" ), $param ) );
		}
		$properties = "&" . implode( " &", $tmpParams );
		$this->modx->db->update( array( "properties" => $this->modx->db->escape( $properties ) ), $this->modx->getFullTableName( "site_modules" ), "`id` = '" . $this->moduleId . "'" );
	}
}
?>
