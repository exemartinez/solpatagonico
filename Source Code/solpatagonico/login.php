<?
	$action = $_POST['action'];
	define ('authenticate', ( $action=='ModificarClave' ? true : false ) );
    define( 'authorize', false );
	include_once ('./inc/conf.inc.php');

	if( $action == 'ModificarClave' ){
		$black_list = get_black_list( $USER->id );
		if( validate::Pass( $_POST['clave'], CFG_passFormat, $black_list ) === true ){
			$ok = $db->Execute( "UPDATE ".CFG_usersTable." SET clave = '".crypt_pass( $_POST['clave'] )."', clave_fecha = '".date("Y-m-d H:i:s")."', pass_renew = 0 WHERE id = '".$USER->id."'" ); 
			if( $ok ) {
				write_log( 'cc' );
				$ok = $db->Execute( "INSERT INTO ".CFG_passwordsTable." (id_usuario, clave, fecha) VALUES ('".$USER->id."', '".crypt_pass( $_POST['clave'] )."', '".time()."')'" );
			}
			header( "Location: ".CFG_indexPage."?menu=".$_POST['menu'] );
		} else {
			$error = '<li>La contraseña no cumple con los requerimientos de seguridad.</li><ul>'.validate::Pass( $_POST['clave'], CFG_passFormat, $black_list ).'</ul>';
			$action = 'frm'.$action;
		}
	}
	
	if( $action == 'authenticate' ) {
		$ret = authenticate( $_POST['usuario'], $_POST['clave'] );
		$rs = $db->SelectLimit( "SELECT skip_maintenance FROM ".CFG_groupsTable." WHERE id = ".$USER->id_grupo, 1 );
		if( $rs ){
			$skip_maintenance = $rs->Fields("skip_maintenance");
		}
		if(
		    $ret # Usuario autenticado
		    && !$USER->bloqueado # El usuario no esta bloqueado
		    && (
				( 
					CFG_maintenance && ( # El sistema esta en mantenimiento
						$skip_maintenance # El grupo del usuario saltea el mantenimiento
						|| $USER->skip_maintenance # El usuario saltea el mantenimiento
					)
				)
				|| !CFG_maintenance # El sistema opera normalmente
		    )
		 ){
		 	if(
				//1 == 0 && # No realiza el chequeo de vencimiento
				CFG_inactiveAccountExpiration # Esta seteada la configuracion de vencimiento de cuentas
				&& fecha::adddate(
					date( "Y-m-d", $USER->last_login ),
					CFG_inactiveAccountExpiration
				) <= date( "Y-m-d" ) # la cuenta esta vencida
				&& $USER->last_login
			){
				$error = 'Ha superado el límite de inactividad en el sistema.<br>Se ha bloqueado el usuario '.$_POST['usuario'];
				$ok = $db->Execute( "UPDAE ".CFG_usersTable." SET bloqueado = 1, fecha_bloqueo = '".time()."' WHERE ".CFG_authUserField." = '".$_POST['usuario']."'" );					
				if( $ok ){
					write_log( 'bu', $_POST['usuario'] );									
				}
				$action = '';
			} else {
				if( 
					(
						(
							CFG_passExpires # Las contraseñas tienen vencimiento
							|| $USER->pass_expires # La contraseña del usuario vence
						) 
						&& fecha::addDate( $USER->clave_fecha, CFG_expirationDays ) <= date("Y-m-d")
					)
					|| $USER->pass_renew # El usuario debe cambiar la contraseña
				){
					$action = 'frmModificarClave';
					if( !$USER->pass_renew ){
						write_log( 'afcc' );
					}
				} else {
					# Acceso satisfactorio
					$ok = $db->Execute( "UPDATE ".CFG_usersTable." SET last_login = '".time()."' WHERE id = '".$USER->id."'" );
					header( "Location: ".CFG_indexPage."?menu=".$_POST['menu'] );
					unset( $_SESSION['login_count'][$USER->usuario] );
					write_log( 'as' );
					$action = '';
				}
			}
		} else {
		    if( CFG_maintenance ){
				$error = 'Sistema cerrado por tareas de mantenimiento.<br>Intentalo mas tarde.';
				write_log( 'afsm' );
		    } elseif( $USER->bloqueado ){
				$error = 'Usuario bloqueado.';
				write_log( 'afub' );
		    } else {
				$tmp_user = $db->getone("SELECT ".CFG_authUserField." FROM ".CFG_usersTable." WHERE ".CFG_authUserField." = '".$_POST['usuario']."'" );
				if( $tmp_user ){
					write_log( 'afci', $_POST['usuario'] );				
				} else {
					write_log( 'afui', $_POST['usuario'] );				
				}
				if ( $_SESSION['login_count'][$_POST['usuario']] <= CFG_wrongPassLimit ){
					$_SESSION['login_count'][$_POST['usuario']]++;
					$error = 'Error en nombre de usuario y/o contraseña.';					
				} else {
					$error = 'Error en nombre de usuario y/o contraseña.<br>Se ha bloqueado el usuario '.$_POST['usuario'];									
					$ok = $db->Execute( "UPDATE ".CFG_usersTable." SET bloqueado = 1, fecha_bloqueo = '".time()."' WHERE ".CFG_authUserField." = '".$_POST['usuario']."'" );					
					if( $ok ){
						write_log( 'bu', $_POST['usuario'] );									
					}
				}
		    }
		    logout( false );
		    $action = '';
		}
	}
?>
<html>
<head>
<title><?=CFG_site?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="styles/estilos.css" rel="stylesheet" type="text/css">
</head>
<body><? include "sys/cabecera.inc.php"?><br><br>
<?
	if( $action == '' ){
?>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
	<input type="hidden" name="action" value="authenticate" />
	<table border="0" cellpadding="0" cellspacing="0" align="center" width="300">
		<tr>
			<td colspan="2" align="center" class="titulosizquierda">Area restringida </td>
		</tr>
<?
		if( $error ){
?>
		<tr align="center">
			<td colspan="2" class="cuadro1izquierda"><span class="error"><?=printif( $error );?></span></td>
		</tr>
<?
		}
?>
		<tr>
			<td class="cuadro1izquierda" align="right">Usuario:</td>
			<td class="cuadro1" width=200><input type="text" name="usuario" id="usuario" 
				value="<?=$_POST['usuario']?>" /></td>
		</tr>
		<tr>
			<td class="cuadro1izquierda" align="right">Contrase&ntilde;a:</td>
			<td class="cuadro1"><input type="password" name="clave" id="clave" /></td>
		</tr>
		<tr>
			<td class="cuadro1izquierda" align="right">Menu:</td>
			<td class="cuadro1"><select name=menu>
			    <option value=1>Horizontal</option>
			    <option value=2>Vertical</option>
			</select></td>
		</tr>		
		<tr>
			<td class="cuadro1izquierda" align="right">&nbsp;</td>
			<td class="cuadro1"><input type="submit" class="boton"
				name="aceptar" value="Aceptar" /></td>
		</tr> 
		<tr>
			<td class="cuadro1izquierda" align="center" colspan="2">Si desea recuperar la contrase&#241;a haga click <a href="recupero.php">aqu&#237;</a></td>
		</tr> 		
		<tr><td colspan="2" class="sombra">&nbsp;</td></tr>
	</table>
</form>
<script language="JavaScript" type="text/javascript">
<!--
	document.form1.usuario.focus();
//-->
</script> 
<?
	} elseif( $action == 'frmModificarClave' ){
		Form::validate();
?>
<form name="formulario" method="post" action="<?= $_SERVER['PHP_SELF']?>" onSubmit="return false">
<input type="hidden" name="action" value="ModificarClave">
<input type="hidden" name="menu" value="<?=$_POST['menu']?>">
	<table width="400" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center"> 
			<th height="29" colspan="2" class="titulosizquierda" align="center">Debe cambiar su clave</th>
		</tr>
		<?
		if( $error != '' ){
		?>
		<tr>
			<td colspan="2" class="cuadro1izquierda"><span class="error"><?=$error?></span></td>
		</tr>
		<?
		}
		?>
		<tr> 
			<td width="150" class="cuadro1izquierda">Usuario</td>
			<td width="250" class="cuadro1"><?= $USER->usuario ?></td>
		</tr>
		<tr> 
			<td class="cuadro1izquierda">Contraseña</td>
			<td class="cuadro1"><input id="clave" style="width:95%"
			    name="clave" type="password" validar="str(6,100):La contraseña debe tener mas de seis caracteres.">
			</td>
		</tr>
		<tr> 
			<td class="cuadro1izquierda">Repita la Contraseña</td>
			<td class="cuadro1"><input id="clave2" name="clave2" style="width:95%" 
			    type="password" class="fields" validar="equal('clave'):Las ccontraseñas no coinciden."> </td>
		</tr>
		<tr> 
			<td class="cuadro2izquierda">&nbsp;</td>
			<td class="cuadro2">
			<script language="javascript" type="text/javascript">
			<!--
				function enviar(){
					var f = document.formulario;
					if( validate( f, 1 ) )
					{
						if( f.clave.value == '<?= $USER->USUARIO ?>' )
						{
							alert( 'La clave no puede ser el nombre de usuario o un email' );
							f.clave.focus();
						}
						else
						{
							f.submit();
						}
					}
				}
			-->
			</script>
			<input type="submit" value="Guardar" onClick="enviar()">
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript" type="text/javascript">
<!--
	document.formulario.clave.focus();
//-->
</script> 
<?
	}
?>
</body>
</html>
