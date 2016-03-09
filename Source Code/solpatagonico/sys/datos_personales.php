<?
	define ('CFG_section_id','');
	include_once('../inc/conf.inc.php');

	$action = $_POST['action'];
	if( $action == 'Guardar' ){
		$formato = '';
		if( 
			$_POST['clave'] != "" && 
			(
				$_POST['clave'] != $_POST['clave2'] ||
				validate::pass( $_POST['clave'], CFG_passFormat, get_black_list( $USER->id ) ) !== true 
			)
		){
			$error = '<ul>'.'<li>La contraseña no cumple los requerimientos mínimos de seguridad.</li>';
			if( $_POST['clave'] != $_POST['clave2'] ){
				$ret_validate = '<li>Las contraseñas no coinciden.</li>';
			} else {
				$ret_validate = validate::pass( $_POST['clave'], CFG_passFormat, get_black_list( $USER->id ) );
			}
			$error .= ( $ret_validate !== true ? $ret_validate : '' );
			$error .= '</ul>';
			$action = 'frmModificar';
		}
	}
	if( $action == 'Guardar' ){
		$record["nombre"] = $_POST['nombre'];
		$record["direccion"] = $_POST['direccion'];
		$record["localidad"] = $_POST['localidad'];
		$record["cp"] = $_POST['cp'];
		$record["provincia"] = $_POST['provincia'];
		$record["pais"] = $_POST['pais'];
		$record["celular"] = $_POST['celular'];
		$record["fax"] = $_POST['fax'];
		$record["cumple"] = $_POST['cumple'];
		$record["telefono"] = $_POST['telefono'];
		$record["mail"] = $_POST['mail'];
		$record["last_user_id"] = $USER->id;
		$record["last_user_ip"] = $_SERVER['REMOTE_ADDR'];
		$record["last_user_date"] = date( "y-m-d H:i:s " );
		if( $_POST['clave'] != '' ){
			$clave = crypt_pass( $_POST['clave'] );
			$record["clave"] = $clave;
		}
		$ok = $db->Execute( get_sql( 'UPDATE', CFG_usersTable, $record, "id = $USER->id" ) ); 
		if( $clave != '' && $ok ) {
			write_log( 'cc' );
			$record['id_usuario'] = $USER->id;
			$record['clave'] = $clave;
			$record['fecha'] = time();
			$ok = $db->Execute( get_sql( 'INSERT', CFG_passwordsTable, $record, '' ) );
		}
		$rs = $db->SelectLimit( "SELECT * FROM ".CFG_usersTable." WHERE id = ".$USER->id, 1 );
		$USER = $rs->FetchObject(false);
		$action = '';
	}
?>
<html>
<head>
	<title>Datos personales</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css">
</head>

<body>
<?
	if( in( $action, '','frmModificar') ){
		Form::validate();
		$modificar = $action == 'frmModificar';
?>
<script language="javascript" type="text/javascript">
<!--
	function enviar(){
<?
	if( $modificar ){
?>
		var f = document.getElementById('form1');
		if( validate( f, 1 ) ){
			if( f.clave.value == '<?=$USER->usuario?>' || f.clave.value == f.mail.value ){
				alert( 'La clave debe ser distinta del nombre de usuario y del mail' );
				f.clave.focus();
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
<? 
	} else {
?>
		return true;
<? 
	}
?>
	}
-->
</script>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return enviar()">
<input type="hidden" name="action" value="<?=$modificar?'Guardar':'frmModificar'?>" />
<table width="640" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr> 
		<th colspan="2" class="titulosizquierda" align="center" style="font-size: 14px; font-weight: bold">Datos de Usuario</th>
	</tr>
	<? if( $error != '' ){ ?>
	<tr> 
		<td class="cuadro1izquierda" colspan="2"><span class="error"><?=$error?></span></td>
	</tr>
	<? } ?>
	<tr> 
		<td class="cuadro1izquierda" width="150">Usuario</td>
		<td class="cuadro1" width="490">&nbsp;<?=$USER->usuario?></td>
	</tr>
	<tr class="cuadro1"> 
		<td class="cuadro1izquierda">Clave</td>
		<td class="cuadro1">
<? 
	if( $modificar ){
?>
		<input style="width: 85%" id="clave" name="clave" type="password" Vvalidar="notrequired:str(6,100):El clave debe tener mas de seis caracteres.">
<?
	} else {
?>
		**********
<?
	}
?>
		</td>
	</tr>
<?
	if( $modificar ){
?>
	<tr> 
		<td class="cuadro1izquierda">Repita la Clave</td>
		<td class="cuadro1"><input style="width: 85%" id="clave2" name="clave2" type="password" Vvalidar="equal('clave'):Las claves no coinciden.">			</td>
	</tr>
<?
	}
?>
	<tr> 
		<td class="cuadro1izquierda">Grupo</td>
		<td class="cuadro1"><?=$db->GetOne("SELECT nombre FROM ".CFG_groupsTable." WHERE id = ".$USER->id_grupo )?>			&nbsp;</td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda">Nombre</td>
		<td class="cuadro1"><? if($modificar){?><input style="width: 85%" name="nombre" type="text" value="<?=printif( $_POST['nombre'], $USER->nombre ) ?>" validate="str:Debe ingresar nombre."><?}else{?><?=$USER->nombre?><?}?>&nbsp;</td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda">Direcci&oacute;n</td>
		<td class="cuadro1"><? if($modificar){?><input style="width: 85%" name="direccion" type="text" value="<?=printif( $_POST['direccion'], $USER->direccion ) ?>"><?}else{?><?=$USER->direccion?><?}?>&nbsp;</td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda">Localidad</td>
		<td class="cuadro1"><? if($modificar){?>
			<input style="width: 85%" name="localidad" type="text" value="<?= printif( $_POST['localidad'], $USER->localidad ) ?>"><?}else{?><?=$USER->localidad?><?}?>
			&nbsp;</td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">CP</td>
		<td class="cuadro1"><? if($modificar){?>
			<input style="width: 85%" name="cp" type="text" value="<?= printif( $_POST['cp'], $USER->cp ) ?>"><? }else{ ?><?=$USER->cp?><? } ?>
			&nbsp;</td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda">Tel&eacute;fono</td>
		<td class="cuadro1"><?if($modificar){?><input style="width: 85%" name="telefono" type="text" value="<?= printif( $_POST['telefono'], $USER->telefono ) ?>"><?}else{?><?=$USER->telefono?><?}?>&nbsp;</td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda">Mail</td>
		<td class="cuadro1">
<?
	if($modificar){
?>
		<input style="width: 85%" name="mail" type="text" value="<?= printif( $_POST['mail'], $USER->mail ) ?>"  validate="email:Debe ingresar un email válido.">
<?
	}else{
?>
		<?=$USER->mail?>
<?
	}
?>	&nbsp;	</td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Bloqueado</td>
		<td class="cuadro1"><?= $USER->bloqueado ? '[ SI ]' : '[ NO ]' ?></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Saltea Mantenimiento</td>
		<td class="cuadro1"><?=$USER->skip_maintenance||$db->getOne("SELECT skip_maintenance FROM ".CFG_groupsTable." WHERE id = ".$USER->id_grupo) ? '[ SI ]' : '[ NO ]'?></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Ultima actividad</td>
		<td class="cuadro1">[<?=printif( fecha::format( $USER->timestamp_sesion, "d/m/Y H:i:s", "timestamp" ) )
		/*=printif( fecha::timestamp2normalyhora( $USER->timestamp_sesion ) )*/?>] [<?=printif($USER->last_ip)?>] </td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Ultima modificaci&oacute;n</td>
		<td class="cuadro1">[<?=fecha::iso2normal(
			substr( $USER->last_user_date, 0, 10 ) )." ".substr( $USER->last_user_date, 11, 8)?>]			[<?=printif($USER->last_user_ip)?>]
<?=$db->getone( "SELECT concat(nombre,' ','[',usuario,']') 
			FROM ".CFG_usersTable." WHERE id = '".$USER->last_user_id."'" ) ?> </td>
	</tr>
	<tr> 
		<td class="cuadro2izquierda">&nbsp;</td>
		<td class="cuadro2"><input type="submit" value="<?=$modificar?'Aceptar':'Modificar'?>" >&nbsp;
			<input type="button" value="Cancelar" onClick="location = '<?=$_SERVER['PHP_SELF']?>'" /></td>
	</tr>
</table>
</form>
<?
	}
?>
</body>
</html>