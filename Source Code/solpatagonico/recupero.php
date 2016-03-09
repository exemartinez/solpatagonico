<?
	define ('authenticate', false);
	include_once ('./inc/conf.inc.php');
	
	$ok = 0;

	if( $_POST['action'] == 'recuperar' && $_POST['mail'] != "") {
		$tmp = $db->SelectLimit( "SELECT * FROM ".CFG_usersTable." WHERE MAIL = '".$_POST['mail']."'", 1 );
		if( $tmp->RecordCount() > 0 ) {
			$usuario = $tmp->FetchObject(false);
		    $ok = 1;
		    $clave = rand();
		    if( CFG_passFunction != 'plain' ) {
				$db->Execute( "UPDATE ".CFG_usersTable." 
					SET CLAVE = '".addslashes( call_user_func( CFG_passFunction, $clave ) )."' 
					WHERE ID = '".$usuario->id."'" );
				write_log( 'cc', $usuario->usuario );
			}
			$nl = "\n";
			$subject = CFG_site.' - Recupero de datos de login';
		    $text = "Sus datos son:".$nl.$nl.
				"\tUsuario: ".$usuario->usuario.$nl.
				"\tContraseña: ".( CFG_passFunction != 'plain' ? $clave : $usuario->clave );
			@mail( $_POST['mail'], $subject, $text, "From: Administrador <info@e4system.com.ar>".$nl );
		} else {
			$error = 'El email ingresado no se encuentra registrador en el sistema, por favor ingrese otro.';			
		}
	}
	if ( $_POST['action'] != '' && $_POST['mail'] == "" ) $error = 'Debe ingresar un email valido.';
?>
<html>
<head>
<title><?=CFG_site?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="styles/estilos.css" rel="stylesheet" type="text/css">
</head>
<body><br><br>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
	<input type="hidden" name="action" value="recuperar" />
	<table border="0" cellpadding="0" cellspacing="0" align="center" width="300">
		<tr>
			<td colspan="2" align="center" class="titulosizquierda">Recupero de contrase&#241;a </td>
		</tr>
<?
	if( $error ){
?>
		<tr align="center">
			<td colspan="2" class="cuadro1izquierda"><span class="error"><?=printif( $error );?></span></td>
		</tr>
<?
	}
	if ( !$ok ){
?>
		<tr>
			<td class="cuadro1izquierda" align="right">Email:</td>
			<td class="cuadro1" width=200><input type="text" name="mail" id="mail" 
				value="" /></td>
		</tr>
		<tr>
			<td class="cuadro1izquierda" align="center" colspan="2"><input type="submit" class="boton"
				name="aceptar" value="Aceptar" />&nbsp;<input type="button" name="cancelar" 
				value="Cancelar" onClick="location = 'login.php'" /></td>
		</tr> 
<?
	} else {
?>
		<tr align="center">
			<td colspan="2" class="cuadro1izquierda">Su datos han sido enviados a su casilla de correo.<br><a href="login.php">Volver</a></td>
		</tr>
<?
	}
?>
	</table>
</form>
<?
	if ( !$ok ){
?>
<script language="JavaScript" type="text/javascript">
<!--
	document.form1.mail.focus();
//-->
</script> 
<?
	}
?>
</body>
</html>
