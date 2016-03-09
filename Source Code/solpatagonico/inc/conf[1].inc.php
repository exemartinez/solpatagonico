<?
	##########################################
	##  Archivo de configuración FRAMEWORK. ##
	##########################################

	# Configuración general del sistema
	define( 'CFG_site', 'Sistema de Distribuci&oacute;n - Sol Patag&oacute;nico' );
	define( 'CFG_mailError', 'soporte@e4system.com.ar' );
	
	# Paths	
	define( 'CFG_url','' );
	define( 'CFG_dir','solpatagonico/' );
	define( 'CFG_libPath', $_SERVER['DOCUMENT_ROOT'].'/'.CFG_url.CFG_dir.'_lib/1.1/' );
	define( 'CFG_realPath', $_SERVER['DOCUMENT_ROOT'].'/'.CFG_url.CFG_dir );
	define( 'CFG_incPath', CFG_realPath.'inc/' );
	define( 'CFG_virtualPath', ($_SERVER['SERVER_PORT']==80?'http':'https').'://'.$_SERVER['HTTP_HOST'].'/'.CFG_dir);
	define( 'CFG_sessionPath', '/'.CFG_dir );
	define( 'CFG_debug', false );
	# Constantes de Base de Datos
	define( 'CFG_language', 'es_ar' );
	define( 'CFG_DB_host', 'localhost' );
	define( 'CFG_DB_type', 'mysql' );
	define( 'CFG_DB_db', 'solpatagonico' );
	define( 'CFG_DB_user', 'root' );
	define( 'CFG_DB_pass', '' );
	# Paths
	define( 'CFG_styleDir', 'styles/' );
	define( 'CFG_styleFile', 'estilos.css' );
	define( 'CFG_virtualIncPath',  CFG_virtualPath.'inc/' );
	define( 'CFG_stylePath', CFG_virtualPath.CFG_styleDir );
	define( 'CFG_jsPath', CFG_virtualPath.'javascript/' );
	define( 'CFG_imgPath', CFG_virtualPath.'img/' );
	define( 'CFG_framework', true );

	# Constantes de Base de Datos
	define( "CFG_DB_dsn", CFG_DB_type."://".CFG_DB_user.":".CFG_DB_pass."@".CFG_DB_host."/".CFG_DB_db.(CFG_debug?"?debug":"") );
	define( 'CFG_privilegesTable', 'sys_permitions' );
	define( 'CFG_groupsPrivilegesTable', 'sys_groups_actions' );
	define( 'CFG_usersTable', 'sys_users' );
	define( 'CFG_sectionsTable', 'sys_sections' );
	define( 'CFG_groupsTable', 'sys_groups' );
	define( 'CFG_actionsTable', 'sys_actions' );
	define( 'CFG_configTable', 'sys_config' );
	define( 'CFG_logsTable', 'sys_logs' );
	define( 'CFG_passwordsTable', 'sys_user_passwords' );

	# Constantes para la funcion de autentificación.
	define( 'CFG_authTable', CFG_usersTable );
	define( 'CFG_authUserField', 'usuario' );
	define( 'CFG_authPassField', 'clave' );
	define( 'CFG_passFunction', 'plain' );
	define( 'CFG_loginFile', 'login.php' );
	define( 'CFG_loginPage', CFG_virtualPath.CFG_loginFile );
	define( 'CFG_indexPage', 'index.php' );
	define( 'CFG_actionVar', 'action' );
	define( 'CFG_closeOnLogout', false );

	# Inclusión de las librerias utilizadas en este sistema.
	define('ADODB_ASSOC_CASE', 0);
	include_once( CFG_libPath.'adodb/adodb.inc.php' );
	$ADODB_LANG = 'es';
	$db = NewADOConnection( CFG_DB_dsn );
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	include_once( CFG_libPath.'func.inc.php' );
	include_once( CFG_libPath.'fecha.inc.php' );
	include_once( CFG_libPath.'form.inc.php' );
	include_once( CFG_libPath.'cacheControl.inc.php' );
	include_once( CFG_libPath.'image.inc.php' );
	include_once( CFG_libPath.'pager.inc.php' );
	include_once( CFG_libPath.'email.inc.php' );
	include_once( CFG_libPath.'framework_func.inc.php' );
	include_once( CFG_libPath.'validate.inc.php' );
	include_once( CFG_libPath.'adv.inc.php' );
	include_once( CFG_libPath.'sendmail.inc.php' );
	
	define( 'CFG_kickOffTimeOut', get_conf_value( 'auto_logoff' ) );
	define( 'CFG_refreshTimeOut', floor( CFG_kickOffTimeOut/2 )+1 );
	define( 'CFG_maintenance', get_conf_value( 'maintenance' ) );
	define( 'CFG_passExpires', get_conf_value( 'pass_expires' ) );
	define( 'CFG_expirationDays', get_conf_value( 'expiration_days' ) );
	define( 'CFG_wrongPassLimit', get_conf_value( 'wrong_pass_limit' ) );	
	define( 'CFG_passFormat', get_conf_value( 'pass_format' ) );	
	define( 'CFG_passBlackList', get_conf_value( 'pass_black_list' ) );	
	define( 'CFG_passBlockUserData', get_conf_value( 'pass_block_user_data' ) );	
	define( 'CFG_passBlockUserDataFields', "usuario,nombre,direccion,mail" );	
	define( 'CFG_passBlockHistory', get_conf_value( 'pass_block_history' ) );
	define( 'CFG_inactiveAccountExpiration', get_conf_value( 'inactive_account_expiration' ) );
	define( 'CFG_logging', get_conf_value( 'log_activity' ) );		
	
	include_once( CFG_libPath.'authentication.inc.php' );
	include_once( CFG_libPath.'actions.inc.php' );

	if( !$ACTIONS ){
		$ACTIONS = new actions( $USER->id_grupo );
	}
	
	function is_cuit( $S ){
		$v2 = (substr($S,0,1) * 5 +
		substr($S,1,1)  * 4 +
		substr($S,2,1)  * 3 +
		substr($S,3,1)  * 2 +
		substr($S,4,1)  * 7 +
		substr($S,5,1)  * 6 +
		substr($S,6,1)  * 5 +
		substr($S,7,1)  * 4 +
		substr($S,8,1)  * 3 +
		substr($S,9,1) * 2) % 11;
		$v3 = 11 - $v2;
		switch ($v3) {
			case 11 : 
				$v3 = 0; 
			break;
			case 10 : 
				$v3 = 9; 
			
			break;
		}
		
		return substr($S,10,1) == $v3;	
	}
?>