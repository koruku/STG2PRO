<?php

define( "MODX_API_MODE", true );
include( "/home/sgi2/chs/index.php" );
$modx = new DocumentParser;
$modx->db->connect();
$modx->getSettings();

include_once( MODX_BASE_PATH . "assets/modules/s2pmodule/classes/s2pmodule.class.php" );
include_once( MODX_BASE_PATH . "assets/modules/s2pmodule/classes/s2p_frontend.class.php" );
include_once( MODX_BASE_PATH . "assets/modules/s2pmodule/classes/s2p_backend.class.php" );

$s2p = new s2pModule( $modx );
$s2p->ph = $s2p->getLang();

$s2pf = new s2pModuleFrontend( $s2p, $modx );
$s2pb = new s2pModuleBackend( $s2p, $modx );

if( $s2pf->judgmentalUpdate() ) {
	$s2pb->handlePostback( "tabAutoMatically" );
	unlink( MODX_BASE_PATH . "assets/modules/s2pmodule/classes/temp/.done_e2s" );
}
?>