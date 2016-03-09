<?
	define( 'authorize', false );
	include( "../inc/conf.inc.php" );

	if( $_POST['action'] == 'cambiar_clave' ) {
		if( 
			$_POST['clave'] != ""  ||
			validate::pass( $_POST['clave'], CFG_passFormat, get_black_list( $USER->id ) ) !== true 
		){
			$error = '<li>La contraseña no cumple los requerimientos mínimos de seguridad.</li>';
			$error .= '<ul>'.validate::pass( $_POST['clave'], CFG_passFormat, get_black_list( $USER->id ) ).'</ul>';
		} else {
			$record["clave"] = addslashes( call_user_func( CFG_passFunction, $_POST['clave'] ) );
			$insertSQL = $autdb->Execute( get_sql( 'UPDATE', CFG_usersTable, $record, "id_usuario = $USER->id" ) ); 
			if( $insertSQL ) {
				$USER->debe_cambiar_clave = 0;
				write_log( 'cc' );
			}
		}
	}
?>
<html>
<head>
	<title><?=CFG_site?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../<?=CFG_styleDir.CFG_styleFile?>">
</head>

<body>
<table width="75%" border="0" cellspacing="0" cellpadding="5" valign="middle" align="center" class=text>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr> 
		<td align="center" valign="top">
<?
		if( defined( 'CFG_sys_logo' ) ){
?>
			<img src="../<?=CFG_sys_logo?>" border="0" /><?
		}
?>
		</td>
	</tr>
	<tr> 
		<td align="center"><b><?=CFG_site?><br><br>Bienvenido/a <?=$USER->nombre?>.</b></td>
	</tr>
</table>
<br>
<br>
<?
	if( $USER->debe_cambiar_clave ) {
		form::validate();
?>
<script language="javascript" type="text/javascript">
<!--
	function enviar() {
		var f = document.formulario;
		if( validate( f, 1 ) ) {
			if( f.clave.value == '<?=$USER->usuario ?>' || f.clave.value == '<?=$USER->mail ?>' ) {
				alert( 'La clave debe ser distinta del nombre de usuario y del email' );
				f.clave.focus();
			} else {
				f.submit();
			}
		}
	}
//-->
</script>
<form action="<?=$_SERVER['PHP_SELF']?>" name="formulario" onSubmit="return false" method="post">
<input type="hidden" name="action" id="action" value="cambiar_clave" />
<table width="50%"  border="0" align="center" cellpadding="5" cellspacing="0">
	<tr>
		<th colspan="2" class="titulosizquierda" align="center">Debe ingresar una clave nueva</th>
	</tr>
	<? if( $error != '' ){ ?>
	<tr> 
		<td class="cuadro1izquierda" colspan="2"><span class="error"><?=$error?></span></td>
	</tr>
	<? } ?>
	<tr>
		<td width="30%" class="cuadro1izquierda">Nueva Clave:</td>
		<td width="70%" align="center" class="cuadro1"><input 
			type="password" name="clave" id="clave" style="width: 90%" 
			validate="str(6,100):La clave debe ser mayor a seis caracteres"></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Repetir Clave:</td>
		<td align="center" class="cuadro1"><input type="password" name="clave2" id="clave2" 
			validate="equal('clave'):Las claves no coinciden" style="width: 90%"></td>
	</tr>
	<tr>
		<td class="cuadro2izquierda">&nbsp;</td>
		<td class="cuadro1"><input type="button" name="cambiar" onClick="enviar()" value="Cambiar"></td>
	</tr>
</table>
</form>
<?
	}
?>
</body>
</html>