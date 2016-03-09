<?
	define( 'cachear', true );
	define( 'authorize', false );
    	include_once '../inc/conf.inc.php' ;
	
	if( !CFG_debug && file_exists( CFG_libPath."javascript/xmenu_c.js"  ) ){
		include_once( CFG_libPath."javascript/xmenu_c.js" );
	} else {
		include_once( CFG_libPath."javascript/xmenu.js" );
	}
?>