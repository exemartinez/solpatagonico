<?
	session_start();

	if( !defined('CFG_actionVar') )		define ( 'CFG_actionVar', 'action' );
	if( !defined('CFG_closeOnLogout') )	define ( 'CFG_closeOnLogout', false );
	if( !defined('CFG_kickOffTimeOut') )	define ( 'CFG_kickOffTimeOut', 3600 );
	if( !defined('CFG_refreshTimeOut') )	define ( 'CFG_refreshTimeOut', 1800 );

	if( !defined('authenticate') || authenticate == true ) authenticate();
	if( !defined('authorize') || authorize == true ) authorize();

	if( $_REQUEST[CFG_actionVar] == 'logout' ) logout();
		
	/**
	* authenticate
	* Autentica usuarios en la seccion privada del sitio (Implementada en Sessiones).	
	*  
	* @param string $user_login usuario ingreado a validar.
	* @param string $pass_login clave ingreada a validar.
	* @return booleano.	
	*/	
	function authenticate( $user_login = '', $pass_login = '', $autdb = '' ) {
		global $USER; # Globals definitions
		
		if( $autdb == '' ) $autdb = NewADOConnection( CFG_DB_dsn ); # Object initialization
		
		# Constants configuration
		$CFG_authTable		= ( defined( 'CFG_authTable' ) ? CFG_authTable : 'users' );
		$CFG_authModuleTable	= ( defined( 'CFG_authModuleTable' ) ? CFG_authModuleTable : 'users') ;
		$CFG_authModuleDb	= ( (defined( 'CFG_authModuleDb' ) && CFG_authModuleDb != '') ? ' LEFT JOIN '.CFG_authModuleDb.".".$CFG_authModuleTable." USING(id)" : '' );
		$CFG_authUserField	= ( defined( 'CFG_authUserField' ) ? CFG_authUserField : 'user' );
		$CFG_authPassField	= ( defined( 'CFG_authPassField' ) ? CFG_authPassField : 'pass' );
		$CFG_authAditionalParams = ( defined( 'CFG_authAditionalParams' ) ? CFG_authAditionalParams : '' );

		$sessionPath = CFG_sessionPath ? CFG_sessionPath : '/';
		if( defined( 'CFG_passFunction' ) ) {
			$pass_login = crypt_pass( $pass_login );
			/* 
			if( strtolower( CFG_passFunction ) != 'plain' ) {
				$pass_login = addslashes( call_user_func( CFG_passFunction, $pass_login ) );
			}
			*/
		} else {
			print_error( 'Debe especificar la constante "CFG_passFunction", use "plain" para texto plano,<br>"mysql" para utilizar la funcion interna password de MySQL o<br>escriba el nombre de la funcion que sera llamada con la clave como parametro.' );
		}

		if( $CFG_authModuleDb ) {
			$from		= CFG_authDb.".".$CFG_authTable;
			$fromModule	= $from.$CFG_authModuleDb;
			$where		= CFG_authDb.".".$CFG_authTable.'.'.$CFG_authUserField." = '$user_login' 
			    AND ".CFG_authDb.".".$CFG_authTable.'.'.$CFG_authPassField." = '$pass_login'".$CFG_authAditionalParams;
		} else {
			$from		= $CFG_authTable;
			$fromModule	= $from;
			$where		= $CFG_authTable.'.'.$CFG_authUserField." = '$user_login' 
			    AND ".$CFG_authTable.'.'.$CFG_authPassField." = '$pass_login'".$CFG_authAditionalParams;
		}
	
		if( $user_login != '' && $pass_login != '' ) {
			# Logueando
			$rs = $autdb->execute( "SELECT * FROM $from WHERE $where" );
			$numRows = $rs->RecordCount();
			if( $numRows != 1 ) { # Error: More than one user with the same data
				unset($_SESSION['session_id1']);
				$aux = $_SESSION['login_count'];
				@session_destroy();
				session_start();
				$_SESSION['login_count'] = $aux;
				return false;
			} else { # User authenticated
				$ID_unico = md5(uniqid(rand()));
				$insertSQL = $autdb->Execute( "UPDATE ".$from." SET id_sesion = '".$ID_unico."', timestamp_sesion = '".time()."', last_ip = '".$_SERVER['REMOTE_ADDR']."' WHERE ".$where ); 
				$rs = $autdb->SelectLimit( "SELECT * FROM $fromModule WHERE $where", 1 );
				$USER = $rs->FetchObject(false);
				$_SESSION['session_id1'] = $USER->id_sesion;
				return true;
			}
		} else { 
			# Refrescando sesion
			if ( $_SESSION['session_id1'] == '' ) {
				# Perdiste la sesion 
				unset( $_SESSION['session_id1'] );
				$aux = $_SESSION['login_count'];
				@session_destroy();
				session_start();
				$_SESSION['login_count'] = $aux;
				if ( CFG_loginPage ) {
?>
					<script language="javascript" type="text/javascript">
						top.location.replace ('<?=CFG_loginPage?>');
					</script>
<?
					flush();
					exit();
				}
			} else { 
				# La sesion aun existe
				$rs = $autdb->execute( "SELECT * FROM $from WHERE id_sesion = '".$_SESSION['session_id1']."'" );
				$numRows = $rs->RecordCount();
				$row = $rs->FetchObject(false);
				if( $numRows != 1 || ( time() - $row->timestamp_sesion >= CFG_kickOffTimeOut ) ){
					# Hay mas de 1 sesion con el mismo id o se vencio el tiempo de inactividad
					$_SESSION['session_id1'] = '';
					$aux = $_SESSION['login_count'];
					@session_destroy();
					session_start();
					$_SESSION['login_count'] = $aux;
					if ( CFG_loginPage ) {
?>
						<script language="javascript" type="text/javascript">
							top.location.replace ('<?=CFG_loginPage?>');
						</script>
<?
						flush();
						exit();
					}
				} else {
					# Permanece en tiempo y forma
					if( time() - $row->timestamp_sesion >= CFG_refreshTimeOut ){
						$old_id = $_SESSION['session_id1'];
						$_SESSION['session_id1'] = md5(uniqid(rand()));
						$insertSQL = $autdb->Execute( "UPDATE ".$from." SET id_sesion = '".$_SESSION['session_id1']."',timestamp_sesion = '".time()."', last_ip = '".$_SERVER['REMOTE_ADDR']."' WHERE id_sesion = '$old_id'" ); 
					}
					$rs = $autdb->SelectLimit( "SELECT * FROM $fromModule 
						WHERE id_sesion = '".$_SESSION['session_id1']."'", 1 );
					$USER = $rs->FetchObject(false);
					return true;
				}
			}
		}
	} # End Function authenticate

	/**
	* logout
	* Generar el logout del sistema, elimando la sesion.
	*  
	* @param boolean $redirect true si desea redireccion al archivo de login, false en caso contrario.
	*/	
	function logout( $redirect = true ) {	
		$_SESSION['session_id1'] = '';
		unset($_SESSION['session_id1']);
		$aux = $_SESSION['login_count'];
		@session_destroy();
		session_start();
		$_SESSION['login_count'] = $aux;
		if( $redirect ){
		    if ( defined('CFG_closeOnLogout') && CFG_closeOnLogout === true ) {
		?>
			<script language="javascript" type="text/javascript">
				top.opener = top;
				top.close();
			</script>
		<?
		    } else {
				if ( CFG_loginPage ) {
		?>
			<script language="javascript" type="text/javascript">
				top.location.replace ('<?=CFG_loginPage?>');
			</script>
		<?
					flush();
					exit();
				}
		    }
		}
	} # End Function logout
	
	/**
	* authorize
	* Chequea los permisos de usuario en el archivo que esta leyendo.
	*  
	* @return booleano.
	*/		
	function authorize(){
	    global $USER;
	    if( $USER ){
			$tmp = '.'.substr( $_SERVER['PHP_SELF'], strlen( CFG_dir ), strlen( $_SERVER['PHP_SELF'] ) );
			$tmp2 = substr( $tmp, 2, strlen( $tmp ) );
			if( CFG_privilegesTable != '' && CFG_sectionsTable != '' ){
				$autdb = NewADOConnection( CFG_DB_dsn ); # Object initialization
				$rs = $autdb->SelectLimit(
					"SELECT id
					FROM ".CFG_sectionsTable."
					WHERE vinculo = '$tmp' OR vinculo = '$tmp2'",
					1
				);
				$id_seccion = $rs->Fields("ID");
				$rs = $autdb->SelectLimit( "SELECT * FROM ".CFG_privilegesTable."
					WHERE id_seccion = '$id_seccion' AND id_grupo = '$USER->id_grupo'",
					1
				);
				if( !$rs ){
					die( "PERMISO DENEGADO" );
				}
			} else {
				/* $array_allowed = array( './index.php', 'index.php' ); */
				if( !in_array( $tmp, $array_allowed ) ){
					die( "PERMISO DENEGADO" );
				}
			}
	    } else {
			return false;
	    }
	} # End Function authorization
	
	/**
	* crypt_pass
	* Devuelve una contraseña encriptada segun CFG_passFunction.
	*  
	* @param string $pass La contraseña a encriptar.
	* @return string La contraseña encriptada.
	*/		
	function crypt_pass( $pass ){
		if( defined( 'CFG_passFunction' ) && $pass != '' ){
			if ( strtolower( CFG_passFunction ) != 'plain' ){
				$pass = addslashes( call_user_func( CFG_passFunction, $pass ) );
			}
		}
		return $pass;
	}
?>