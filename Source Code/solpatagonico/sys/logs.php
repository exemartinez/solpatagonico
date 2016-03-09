<?
	include_once('../inc/conf.inc.php');

	registrar( "id_grupo,id_usuario,order,tipo_entrada,action,id,desde,hasta" );
?>
<html>
<head>
	<title>Logs de sistema</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css">
</head>

<body>
<?
	if( $action == '' ) {
		$filtros = "&id_usuario=".$id_usuario."&id_grupo=".$id_grupo."&tipo_entrada=".$tipo_entrada."&desde=".$desde."&hasta=".$hasta;
		$orden = ( $orden ? $orden : "l.fecha DESC" );
		$pager = new pager(
			"SELECT l.*
			FROM ".CFG_logsTable." l
			JOIN ".CFG_usersTable." u ON u.id = l.id_usuario
			JOIN ".CFG_groupsTable." g ON g.id = u.id_grupo
			WHERE 1=1".
				( $USER->id_grupo != 1 ? " AND g.id != 1" : '' ).
				( $id_usuario != '' ? " AND l.id_usuario = '$id_usuario'" : "").
				( $id_grupo != '' ? " AND u.id_grupo = '$id_grupo'" : "").
				( $tipo_entrada != '' ? " AND l.tipo_entrada = '$tipo_entrada'" : "").
				(
					validate::date(fecha::normal2iso($desde))
					&& validate::date(fecha::normal2iso($hasta))
					? " AND l.fecha BETWEEN '".fecha::normal2timestamp($desde)."' AND '".(fecha::normal2timestamp($hasta)+86399)."'"
					: (
						validate::date(fecha::normal2iso($desde))
						? " AND l.fecha >= '".fecha::normal2timestamp($desde)."'"
						: (
							validate::date(fecha::normal2iso($hasta))
							? " AND l.fecha <= '".(fecha::normal2timestamp($hasta)+86399)."'"
							:''
						)
					)
				).
			" ORDER BY ".( $orden ? $orden : 'fecha DESC' ),
			$_REQUEST['cur_page'],
			20,
			25,
			'id_usuario,id_grupo,tipo_entrada,desde,hasta',
			''
		);
		form::validate();
?>
<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="GET" onSubmit="return validate(this)">
<table width="640" cellpadding="3" cellspacing="0" border="0" align="center">
	<tr>
		<th class="titulosizquierda" align="center" colspan="5">Historial</th>
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
					<td width="80">Usuario:&nbsp;</td>
					<td colspan="3"><?
					$rs = $db->execute( "SELECT CONCAT(nombre,CONCAT(' [',CONCAT(usuario,']'))),id FROM ".CFG_usersTable.(
						$USER->id_grupo != 1 ? " WHERE id_grupo != 1" : '' )." ORDER BY nombre" );
					echo $rs->GetMenu2(
						'id_usuario',
						$id_usuario,
						":Todos",
						false,
						1,
						'style="width:100%"'
					);?></td>
					<td width="100"><input type="submit" name="buscar" value="Buscar" /></td>
				</tr>
				<tr>
					<td width="80">Grupo:&nbsp;</td>
					<td colspan="3"><?
					$rs = $db->execute( "SELECT nombre,id FROM ".CFG_groupsTable.(
						$USER->id_grupo != 1 ? " WHERE id != 1" : '' )." ORDER BY nombre" );
					echo $rs->GetMenu2(
						'id_grupo',
						$id_grupo,
						":Todos",
						false,
						1,
						'style="width:100%"'
					);?></td>				    
				    <td width="100">&nbsp;</td>
				</tr>
				<tr>
					<td width="80">Evento:&nbsp;</td>
					<td colspan="3"><?
					$rs = $db->execute( "SELECT DISTINCT(tipo_entrada) FROM ".CFG_logsTable." ORDER BY tipo_entrada" );
					echo $rs->GetMenu2(
						'tipo_entrada',
						$tipo_entrada,
						":Todos",
						false,
						1,
						'style="width:100%"'
					);?></td>				    
				    <td width="100">&nbsp;</td>
				</tr>
				<tr>
					<td>Desde</td>
					<td width="185"><input type="text" name="desde" value="<?=$desde?>" style="width:100%" validate="notrequired:date:El formato de la fecha es incorrecto."></td>
				    <td>Hasta</td>
				    <td width="185"><input type="text" name="hasta" value="<?=$hasta?>" style="width:100%" validate="notrequired:date:El formato de la fecha es incorrecto."></td>
				    <td width="100">&nbsp;</td>
				</tr>
			</table>		</td>
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
		<td width="105" class="titulosizquierda" <?=( strstr($orden,"l.fecha") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=l.fecha ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=l.fecha DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Fecha</td>
		<td width="107" class="titulos" <?=( strstr($orden,"u.nombre") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=u.nombre ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=u.nombre DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Usuario</td>
		<td class="titulos" <?=( strstr($orden,"l.tipo_entrada") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=l.tipo_entrada ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=l.tipo_entrada DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Evento</td>
		<td width="100" class="titulos" <?=( strstr($orden,"l.ip") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=l.ip ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=l.ip DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> IP</td>
		<td class="titulos" width="25">&nbsp;</td>
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
		<td class="<?=$class?>izquierda"><?=$usuario->fecha != '' ? printif( fecha::timestamp2normalyhora( $usuario->fecha ) ) : '&nbsp;'?></td> 
		<td class="<?=$class?>"><?=printif( $usuario->usuario )?></td>
		<td class="<?=$class?>"><?=printif( $usuario->tipo_entrada )?></td>
		<td class="<?=$class?>"><?=printif( $usuario->ip )?></td>
		<td class="<?=$class?>" align="center"><a 
			href="<?=$_SERVER['PHP_SELF']?>?id=<?=$usuario->id?>&action=frmVer"><img
			src="../sys_images/search.gif" alt="Ver Registro" width="22" height="22" border="0" /></a>			</td>
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
	 } elseif( $action == 'frmVer' ){
		$rs = $db->selectlimit( "SELECT l.*, u.nombre
			FROM ".CFG_logsTable." l
			JOIN ".CFG_usersTable." u ON u.id = l.id_usuario
			JOIN ".CFG_groupsTable." g ON g.id = u.id_grupo
			WHERE l.id = '$id'", 1 );
		if( $rs ){
			$log = $rs->fetchObject(false);
		}
?>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
<table width="640" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<th colspan="2" class="titulosizquierda" align="center">Historial</th>
	</tr>
	<tr>
		<td width="150" class="cuadro1izquierda">Fecha</td>
		<td class="cuadro1"><?=$log->fecha != '' ? printif( fecha::timestamp2normalyhora( $log->fecha ) ) : '&nbsp;'?></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Usuario </td>
		<td class="cuadro1"><?=printif($log->nombre)?> [<?=printif( $log->usuario )?>]</td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Evento</td>
		<td class="cuadro1"><?=printif( $log->tipo_entrada )?></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">IP</td>
		<td class="cuadro1"><?=printif( $log->ip )?></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">User Agent </td>
		<td class="cuadro1"><?=printif( $log->user_agent )?></td>
	</tr>
	<tr>
		<td class="cuadro2izquierda">&nbsp;</td>
		<td class="cuadro2"><input type="submit" value="Aceptar"></td>
	</tr>
</table>
</form>
<?
	}
?>
</body>
</html>