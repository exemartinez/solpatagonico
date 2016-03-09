<?
	include_once('../inc/conf.inc.php');

	$msgDelete	= '¿Esta seguro que desea eliminar este Usuario?\nSi lo elimina, se eliminará también el historial de registros del usuario.';
	$msgUnique	= 'El nombre de usuario ingresado ya existe, ingrese otro.';
    
	$campos = 'usuario,nombre,direccion,bloqueado,localidad,cp,provincia,pais,celular,fax,cumple,telefono,mail,id_grupo,nota,skip_maintenance,pass_expires,pass_renew';
	registrar( $campos.",clave,clave2,lst_id_grupo,order,action,id" );
	$bloqueado = intval($bloqueado);
	$skip_maintenance = intval($skip_maintenance);
	$pass_expires = intval($pass_expires);
	$pass_renew = intval($pass_renew);
	$id = intval($id);
	$record = gen_record( $campos );

	if ( in($action,'Agregar','Modificar') ) {
		$error = "";
		$black_list = get_black_list( $id );
		$tmp_list = split( ',', CFG_passBlockUserDataFields );
		foreach( $tmp_list as $value ){
			if (${strtolower($value)} != "") $black_list .= ($black_list!=''?',':'').${strtolower($value)};
		}
		if( !validate::Text($nombre) ) $error .= "<li>Debe ingresar un Nombre.</li>";
		if( !validate::Integer( $usuario ) || strlen( intval( $usuario ) ) != 8 ) 
			$error .= "<li>Debe ingresar un Usuario (solo n&uacute;meros, 8 d&iacute;gitos).</li>";
		if( $action == 'Agregar' && !validate::Text($clave,"","") ) $error .= "<li>Debe ingresar una Clave.</li>";
		if( !validate::Equal($clave,$clave2) ) $error .= "<li>Deben coincidir las claves.</li>";			
		if( 
			( $clave != "" || $action == 'Agregar' )
			&& validate::pass($clave, CFG_passFormat, $black_list ) !== true 
		) {
			$error .= "<li>La contraseña no cumple los requerimientos mínimos de seguridad.</li><ul>".validate::pass($clave, CFG_passFormat, $black_list )."</ul>";
		}
		if( $telefono && !preg_match( '/^[0-9]+([-]*[0-9]+)*$/', $telefono ) ) 
			$error .= '<li>Debe ingresar un Telefono v&aacute;lido (n&uacute;meros y/o guiones)';
		if( !validate::Email($mail) ) $error .= "<li>Debe ingresar un email.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		} else {
			$clave = crypt_pass($clave);
		}
	}

	if( $action == 'Agregar' ){
		$rs = $db->getrow( "SELECT * FROM ".CFG_usersTable." WHERE usuario = '$usuario'" );
		if( $rs ){
		    $error = '<li>'.$msgUnique.'</li>';
		    $action = 'frm'.$action;
		} else {
			$record['clave'] = $clave;
			$record['clave_fecha'] = fecha::adddate(date( "Y-m-d" ), -1*(CFG_expirationDays + 1))." 00:00:00";
			$record["owner_id"] = $USER->id;
			$record["owner_ip"] = $_SERVER['REMOTE_ADDR'];
			$record["owner_date"] = date( "y-m-d H:i:s " );				
			$ok = $db->Execute( get_sql( 'INSERT', CFG_usersTable, $record, '' ) );
			if( !$ok ){
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			} else {
				$record['id_usuario'] = $id;
				$record['clave'] = $clave;
				$record['fecha'] = time();			
				$ok = $db->Execute( get_sql( 'INSERT', CFG_passwordsTable, $record, '' ) );
				$error = '';
				$action = '';
			}
		}
	}
	if( $action == 'Modificar' ){
		$bloqueado_anterior = $db->getone("SELECT bloqueado FROM ".CFG_usersTable." WHERE id = '$id'");
		$grupo_anterior = $db->getone("SELECT id_grupo FROM ".CFG_usersTable." WHERE id = '$id'");
		$rs = $db->getRow( "SELECT * FROM ".CFG_usersTable." WHERE usuario = '$usuario' AND id <> '$id'" );
		if( $rs ){
		    $error = '<li>'.$msgUnique.'</li>';
		    $action = 'frm'.$action;
		} else {
			if( $clave != '' ){
				$record['clave'] = $clave;
				$record['clave_fecha'] = date( "Y-m-d H:i:s" );
			}
			if( !$bloqueado_anterior && $bloqueado ){
				$record['fecha_bloqueo'] = time();
			}
			$record["last_user_id"] = $USER->id;
			$record["last_user_ip"] = $_SERVER['REMOTE_ADDR'];
			$record["last_user_date"] = date( "y-m-d H:i:s " );	
			$ok = $db->Execute( get_sql( 'UPDATE', CFG_usersTable, $record, "id = $id" ) );							
			if( $ok ){
				if( $clave != '' ) {
					write_log( 'cc', $usuario );
					unset($record);
					$record['id_usuario'] = $id;
					$record['clave'] = $clave;
					$record['fecha'] = time();
					$ok = $db->Execute( get_sql( 'INSERT', CFG_passwordsTable, $record, '' ) );
				}
				if( $grupo_anterior != $id_grupo ) write_log( 'cg', $usuario );
				if( !$bloqueado_anterior && $bloqueado ) write_log( 'bu', $usuario );
				if( $bloqueado_anterior && !$bloqueado ){
					$record['last_login'] = time();
					$ok = $db->Execute( get_sql( 'UPDATE', CFG_usersTable, $record, "id = '".$id."'" ) );
					write_log( 'dbu', $usuario );
				}
				$action = '';
			} else {
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			}
		}
	}
	if( $action == 'Borrar' ){
		$ok = $db->execute( "DELETE FROM ".CFG_usersTable." WHERE id = $id" );
		if( !$ok ){
			$error = '<li>Error al borrar el usuario.</li>';
		} else {
			$error = '';
		}
		$action = '';
	}
?>
<html>
<head>
	<title>Usuarios de sistema</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css">
</head>

<body>
<?
	if( $action == '' ) {
		$filtros = "&lst_usuario=".$lst_usuario."&lst_nombre=".$lst_nombre."&lst_id_grupo=".$lst_grupo;
		if( trim( $orden ) == '' ) $orden = 'usuario';
		$pager = new pager(
			"SELECT u.*, g.nombre as grupo
			FROM ".CFG_usersTable." u
			JOIN ".CFG_groupsTable." g ON g.id = u.id_grupo
			WHERE ".
				( $USER->id_grupo != 1 ? "u.id_grupo <> 1" : '1 = 1').
				( $lst_usuario != '' ? " AND usuario = '$lst_usuario'" : "").
				( $lst_nombre != '' ? " AND u.nombre LIKE '%$lst_nombre%'" : "").
				( $lst_id_grupo != '' ? " AND g.id = '$lst_id_grupo'" : "").
			" ORDER BY ".$orden,
			$_REQUEST['cur_page'],
			20,
			25,
			$_REQUEST,
			''
		);
?>
<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="GET">
<table width="640" cellpadding="3" cellspacing="0" border="0" align="center">
	<tr>
		<th class="titulosizquierda" align="center" colspan="5" style="font-size: 14px; font-weight: bold">Usuarios</th>
	</tr>
<?
		if ( $error != '' ){
?>
		<tr> 
			<td colspan="5" class="cuadro1izquierda"><span class="error"><?=$error?></span></td>
		</tr>
<?
		}
?>
	<tr>
		<td class="cuadro1izquierda" colspan="5">
			<table border="0" cellpadding="3" cellspacing="0" width="100%" class="text">
				<tr>
					<td colspan="2"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&usuario=<?=$lst_usuario?>" class="text"><img 
					src="../sys_images/add.gif" border=0 align="absmiddle"> Agregar Usuario</a></td>
				</tr>
			</table>
			<table border="0" cellpadding="3" cellspacing="0" width="100%" class="text">
				<tr>
					<td width="80">Usuario:&nbsp;</td>
					<td><input type="text" name="lst_usuario" id="lst_usuario" value="<?=$lst_usuario?>" style="width:95%" /></td>
					<td width="80">Nombre:&nbsp;</td>
					<td><input type="text" name="lst_nombre" value="<?=$lst_nombre?>" style="width:95%" /></td>
					<td width="100"><input type="submit" value="Buscar"></td>
				</tr>
				<tr>
					<td width="80">Grupo:&nbsp;</td>
					<td><?
					$rs = $db->execute( "SELECT nombre,id FROM ".CFG_groupsTable.(
						$USER->ID_GRUPO != 1 ? " WHERE id != 1" : '' )." ORDER BY nombre" );
					echo $rs->GetMenu2(
						'lst_id_grupo',
						$lst_id_grupo,
						":Todos",
						false,
						1,
						'style="width:95%"'
					);?></td>				
					<td>&nbsp;</td>    
					<td>&nbsp;</td> 
				    <td width="100">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="sombra" colspan="5">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="text">
				<tr>
					<td>&nbsp;Registros: <?=$pager->get_first_pos()?> al <?=$pager->get_last_pos()?> de <?=$pager->get_total_records()?></td>
					<td align="right">
<?
		if( $pager->get_total_pages() > 0 ) {
?>
						P&aacute;g<?=$pager->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$pager->get_navigator(); ?>&nbsp;
<?
		}
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="110" class="titulosizquierda" <?=( strstr($orden,"usuario") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=usuario ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=usuario DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Usuario</td>
		<td width="200" class="titulos" <?=( strstr($orden,"u.nombre") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=u.nombre ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=u.nombre DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Nombre</td>
		<td width="130" class="titulos" <?=( strstr($orden,"timestamp_sesion") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=timestamp_sesion ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=timestamp_sesion DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Ultima actividad</td>
		<td width="140" class="titulos" <?=( strstr($orden,"g.nombre") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=g.nombre ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=g.nombre DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Grupo</td>
		<td class="titulos" width="60">&nbsp;</td>
<?
		if( $pager->get_page_records() < 1 ){
?>
	<tr> 
		<td colspan="5" align="center" class="cuadro1izquierda"><span class="error">No hay registros coincidentes</span></td>
	</tr>
<?
		} else {
			while( $usuario = $pager->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
	<tr> 
		<td class="<?=$class?>izquierda"><?=printif( $usuario->usuario )?></td>
		<td class="<?=$class?>"><?=printif( $usuario->nombre )?></td>
		<td class="<?=$class?>"><?=$usuario->id_sesion != '' ? printif( fecha::timestamp2normalyhora( $usuario->timestamp_sesion ) ) : '&nbsp;'?></td>
		<td class="<?=$class?>"><?=printif( $usuario->grupo )?></td>
		<td class="<?=$class?>" align="center"><table border="0" align="left">
			<tr>
				<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?id=<?=$usuario->id?>&action=frmModificar"><img
src="../sys_images/list.gif" alt="Modificar Registro" border="0" /></a></td>
				<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?id=<?=$usuario->id?>&action=frmVer"><img
src="../sys_images/search.gif" alt="Ver" border="0" /></a></td>
				<td width="20" align="center"><a href="javascript: if (confirm ('<?=$msgDelete?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$usuario->id?>'; }"> <img src="../sys_images/delete.gif" alt="Eliminar Registro" border="0" /></a></td>
			</tr>
		</table></td>
	</tr>
<?
			}
		}
?>
	<tr>
		<td class="sombra" colspan="5">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="text">
				<tr>
					<td>&nbsp;Registros: <?=$pager->get_first_pos()?> al <?=$pager->get_last_pos()?> de <?=$pager->get_total_records()?></td>
					<td align="right">
<?
		if( $pager->get_total_pages() > 0 ) {
?>
					P&aacute;g<?=$pager->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$pager->get_navigator(); ?>&nbsp;
<?
		}
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?
	 }

	if( $action == 'logs' ){
		if( trim( $order ) == '' ) $order = 'fecha DESC';
		$logs = dbi::select( 'view_logs', '*', "id_usuario = $id", $order );
		$usuario = $db->record(
			"view_adm_usuarios",
			"id = $id"
		);
?>
<table align="center" width="600" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<th colspan="4" class="cuadro1">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class=text>
				<tr>
					<td colspan="2" class="titulos">Usuario: <a href="mailto:<?=$usuario->mail?>"><?= "$usuario->usuario - $usuario->nombre"?></a></td>
				</tr>
				<tr>
					<td class="titulos" width="52%"><a href="<?=$_SERVER['PHP_SELF']?>">Volver</a></td>
					<td class="titulos" width="48%" align="right">Total: <?=$logs->num_rows()?></td>
				</tr>
			</table>
		</th>
	</tr>
	<tr>
		<td height="5" colspan="6" class="sombra_titulo"></td>
	</tr>
	<tr align="center">
		<td class="titulosizquierda"><a href="<?=$_SERVER['PHP_SELF']?>?id=<?= $id?>&action=log&order=fecha DESC">Fecha</a></td>
		<td class="titulos"><a href="<?=$_SERVER['PHP_SELF']?>?id=<?= $id?>&action=log&order=seccion">Seccion</a></td>
		<td class="titulos"><a href="<?=$_SERVER['PHP_SELF']?>?id=<?= $id?>&action=log&order=accion">Accion</a></td>
		<td class="titulos"><a href="<?=$_SERVER['PHP_SELF']?>?id=<?= $id?>&action=log&order=ip">IP</a></td>
	</tr>
<?
		if( $logs->num_rows() < 1 ){
?>
	<tr>
		<td colspan="8" align="center" class="cuadro1izquierda">Ningun registro</td>
	</tr>
<?
		} else {
			while( $log = $logs->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2':'cuadro1';
?>
	<tr>
		<td class="<?= $class?>izquierda" align="center"><?=$log->fecha?></td>
		<td class="<?= $class?>" align="center"><?="$log->seccion ID: $log->id_registro" ?></td>
		<td class="<?= $class?>" align="center"><?=$log->accion?></td>
		<td class="<?= $class?>" align="center"><?=$log->ip?></td>
	</tr>
<?
			}
		}
?>
</table>
<?
	 }

	if( in( $action, 'frmAgregar', 'frmModificar', 'frmVer' ) ){
		if( in( $action, 'frmModificar', 'frmVer' ) ){
			$id = intval($id);
			$rs = $db->SelectLimit( "SELECT * FROM ".CFG_usersTable." WHERE id = $id", 1 );
			$reg = $rs->FetchObject(false);
		}

		Form::validate();
?>
<form name="formulario" method="post" action="<?= $_SERVER['PHP_SELF']?>" onSubmit="return false">
<table width="640" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <th colspan="2" class="titulosizquierda" style="font-size: 14px; font-weight: bold">Usuario</th>
  </tr>
  <?
		if ( $error != '' ){
?>
  <tr>
    <td colspan="2" class="cuadro1izquierda"><span class="error">
      <?=$error?>
    </span></td>
  </tr>
  <?
		}
?>
  <tr>
    <td class="cuadro1izquierda" width="150"><?=($action!='frmVer'?'*':'')?>Usuario</td>
    <td class="cuadro1" width="490">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->usuario );
		} else {
?>
	<input style="width: 85%" name="usuario" type="text" value="<?= printif( $usuario, $reg->usuario ) ?>" validar="int:El nombre de usuario debe ser numérico de 8 digitos.">
<?
		}
?>	
	</td>
  </tr><?
 		if( $action == 'frmAgregar' ){
  ?><tr>
    <td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Clave</td>
    <td class="cuadro1"><input style="width: 85%" id="clave" name="clave" type="password" validar="<? if ($action == 'frmModificar' ){ ?>notrequired:<? } ?>str(6,100):El clave debe tener mas de seis caracteres."></td>
  </tr>
  <tr>
    <td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Repita la Clave</td>
    <td class="cuadro1"><input style="width: 85%" id="clave2" name="clave2" type="password" validar="equal('clave'):Las claves no coinciden."></td>
  </tr><?
  		}
  ?><tr>
    <td class="cuadro1izquierda">Grupo</td>
    <td class="cuadro1"><?
		if( $action == 'frmVer' ){
			echo printif( $db->GetOne( "SELECT nombre FROM ".CFG_groupsTable." WHERE id='".$reg->id_grupo."'" ) );
		} else {
					$rs = $db->execute( "SELECT nombre,id FROM ".CFG_groupsTable.(
						$USER->id_grupo != 1 ? " WHERE id != 1" : '' )." ORDER BY nombre" );
					echo $rs->GetMenu2(
						'id_grupo',
						$reg->id_grupo,
						"",
						false,
						1,
						'style="width:85%"'
					);
		}
					?></td>
  </tr>
  <tr>
    <td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Nombre</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->nombre );
		} else {
?>
	<input style="width: 85%" name="nombre" type="text" value="<?= printif( $_POST['nombre'], $reg->nombre ) ?>" validate="str:Debe ingresar nombre.">
<?
		}
?>	
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Direcci&oacute;n</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->direccion );
		} else {
?>
	<input style="width: 85%" name="direccion" type="text" value="<?= printif( $_POST['direccion'], $reg->direccion ) ?>">
<?
		}
?>
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Localidad</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->localidad );
		} else {
?>
	<input style="width: 85%" name="localidad" type="text" value="<?= printif( $_POST['localidad'], $reg->localidad ) ?>">
<?
		}
?>
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">CP</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->cp );
		} else {
?>
	<input style="width: 85%" name="cp" type="text" value="<?= printif( $_POST['cp'], $reg->cp ) ?>">
<?
		}
?>
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Tel&eacute;fono</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->telefono );
		} else {
?>
	<input style="width: 85%" name="telefono" type="text" value="<?= printif( $_POST['telefono'], $reg->telefono ) ?>">
<?
		}
?>
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>e-mail</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->mail );
		} else {
?>
	<input style="width: 85%" name="mail" type="text" value="<?= printif( $_POST['mail'], $reg->mail ) ?>"  validate="email:Debe ingresar un email válido.">
<?
		}
?>
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Nota</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->nota );
		} else {
?>
	<textarea style="width: 85%" name="nota" wrap="VIRTUAL"><?= htmlentities( printif( $_POST['nota'], $reg->nota ) ) ?></textarea>
<?
		}
?>
	</td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Bloqueado</td>
    <td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->bloqueado?'Si':'No' );
		} else {
?>
	<input name="bloqueado" <?= printif( $_POST['bloqueado'], $reg->bloqueado ) ? 'checked' : '' ?> type="checkbox" id="bloqueado" value="1">
<?
		}
?>
	</td>
  </tr>
  <tr style="display: <?=( $USER->id_grupo == 1 ? 'block' : 'none' )?>">
    <td class="cuadro1izquierda">Saltea Mantenimiento</td>
    <td class="cuadro1"><?
		if( $action == 'frmBaja' || ( $USER->id_grupo == 1 && $action != 'frmVer' ) ){
		    ?>
        <input type="checkbox" name="skip_maintenance" value="1" <?=$reg->skip_maintenance?'checked':''?> />
        <?
		} else {
		    echo $reg->skip_maintenance ? 'Si' : 'No';
		}
?>
    </td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Vence contraseña</td>
    <td class="cuadro1"><?
		if( in( $action, 'frmBaja', 'frmVer' ) ){
		    echo $reg->skip_maintenance ? 'Si' : 'No';
		} else {
			?>
        <input type="checkbox" name="pass_expires" value="1" <?=$reg->pass_expires?'checked':''?> />
        <?
		}
?>
    </td>
  </tr>
  <tr>
    <td class="cuadro1izquierda">Forzar cambio de contraseña</td>
    <td class="cuadro1"><?
		if( in( $action, 'frmBaja', 'frmVer' ) ){
		    echo $reg->pass_renew ? 'Si' : 'No';
		} else {
			?>
        <input type="checkbox" name="pass_renew" value="1" <?=$reg->pass_renew||$action=='frmAgregar'?'checked':''?> />
        <?
		}
?>
    </td>
  </tr>
  <?
		if ($action == 'frmModificar') {
			if( $reg->last_ip != ""){ 
?>
  <tr>
    <td class="cuadro1izquierda">Ultima actividad</td>
    <td class="cuadro1">[
        <?=printif( fecha::format( $reg->timestamp_sesion, "d/m/Y H:i:s", "timestamp" ) )?>
        ] [
        <?=printif($reg->last_ip)?>
        ]</td>
  </tr>
<?
			}
			if( $reg->owner_id != 0 ){
?>
  <tr>
    <td class="cuadro1izquierda">Creaci&oacute;n</td>
    <td class="cuadro1">[
        <?=fecha::iso2normal(
			substr( $reg->owner_date, 0, 10 ) )." ".substr( $reg->owner_date, 11, 8)?>
        ] [
        <?=printif($reg->owner_ip)?>
        ]
        <?=$db->getone( "SELECT concat(nombre,' ','[',usuario,']') 
			FROM ".CFG_usersTable." WHERE id = '".$reg->owner_id."'" ) ?>
    </td>
  </tr>
<?
			}
			if( $reg->last_user_id != 0 ){
?>
  <tr>
    <td class="cuadro1izquierda">Ultima modificaci&oacute;n</td>
    <td class="cuadro1">[
        <?=fecha::iso2normal(
			substr( $reg->last_user_date, 0, 10 ) )." ".substr( $reg->last_user_date, 11, 8)?>
        ] [
        <?=printif($reg->last_user_ip)?>
        ]
        <?=$db->getone( "SELECT concat(nombre,' ','[',usuario,']') 
			FROM ".CFG_usersTable." WHERE id = '".$reg->last_user_id."'" ) ?>
    </td>
  </tr>
<?
			}
		}
?>
  <tr>
    <td  class="cuadro2izquierda" align="center" >&nbsp;</td>
    <td class=cuadro2><script language="javascript" type="text/javascript">
			<!--
				function enviar() {
					var f = document.formulario;
					if( validate( f, 1 ) )
					{
							f.submit();
					}
				}
			-->
			</script>
        <input name="id" type="hidden" id="id" value="<?= $reg->id?>">
        <input type="hidden" name="action" value="<?= $action == 'frmAgregar' ? 'Agregar' : 'Modificar' ?>">
        <? /*<input type="button" value="Aceptar">&nbsp;<input type="button" value="Cancelar" name="Button" onClick="javascript:location = '<?=$_SERVER['PHP_SELF']?>?id_padre=<?=$id_padre?>'"> */ ?>
<?
		if( $action != 'frmVer' ){
?>			
			<input name="aceptar" type="submit"  value="Aceptar" onClick="enviar()"/>
<?
		}
?>
           	<input name="button" type="button"  onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="<?=$action!='frmVer'?'Cancelar':'Volver'?>" />
		</td>
  </tr>
</table>
</form>
<?
	}
?>
</body>
</html>