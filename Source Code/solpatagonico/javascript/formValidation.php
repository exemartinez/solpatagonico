<?
	define ('authenticate', false);
	include_once('../inc/conf.inc.php');

	if( !CFG_debug and file_exists( CFG_libPath."javascript/formValidation_c.js" ) ){
		include_once( CFG_libPath."javascript/formValidation_c.js" );
	} else {
		include_once( CFG_libPath."javascript/formValidation.js" );
	}
?>