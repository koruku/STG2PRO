<?php

define( "MODX_API_MODE", true );
include( "../../../../index.php" );
$modx = new DocumentParser;
$modx->db->connect();
$modx->getSettings();

include_once( MODX_BASE_PATH . "assets/modules/s2pmodule/classes/s2pmodule.class.php" );
include_once( MODX_BASE_PATH . "assets/modules/s2pmodule/classes/s2p_backend.class.php" );

$s2p = new s2pModule( $modx );
$s2pb = new s2pModuleBackend( $s2p, $modx );

echo $s2pb->applyDatabase();

?>