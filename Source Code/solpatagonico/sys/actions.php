<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Acciones';
	$name		= 'Acción';
	$titleForm	= 'Acciones';
	$msgDelete	= '¿Desea borrar esta Acción?';
	# Variables de programación
	$campos		= "accion, valores, descripcion";
	registrar( $campos.",action,id,cur_page" );
	$id = intval($id);
	$where		= "id = '$id'";
	$record = gen_record( $campos );
	
	if ( in($action, 'Agregar', 'Modificar') ) {
		$error = '';
		if( !validate::Text($accion) ) $error .= "<li>Complete un nombre de Acción.</li>";
		if( !validate::Text($valores) ) $error .= "<li>Complete los valores permitidos para la acción (Valores separados por comas).</li>";
		if( strpos( $accion, " " ) ) $error .= "<li>No se permiten caracteres de espacio.</li>";
		if( $error ) $action = 'frm'.$action;
	}
	switch ($action) {
		case 'Agregar':
			$ok = $db->Execute( get_sql( 'INSERT', CFG_actionsTable, $record, '' ) );
			if( !$ok ){
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			} else {
				$error = '';
				$action = '';
			}
			break;
		case 'Modificar':
			$ok = $db->Execute( get_sql( 'UPDATE', CFG_actionsTable, $record, "id = '$id'" ) );
			if( $ok ){
				$action = '';
			} else {
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			}
			break;
		case 'Borrar':
			$ok = $db->execute( "DELETE FROM ".CFG_actionsTable." WHERE id = '$id'" );
			if( !$ok ){
				$error = '<li>Error al borrar el registro.</li>';
			} else {
				$db->execute( "DELETE FROM ".CFG_groupsPrivilegesTable." WHERE id_accion = '$id'" );
				$error = '';
			}
			$action = '';
			break;		
	}
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
</head>

<body>
<?
	if( in( $action, '', 'buscar' ) ){
		$paginas = new pager(
			"SELECT * FROM ".CFG_actionsTable." WHERE accion LIKE '%$nombre_list%' ORDER BY accion",
			$cur_page,
			20,
			25,
			'nombre_list',
			''
		);	
?>
<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="buscar" />
<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<th colspan="4" class="titulosizquierda"><?=$title?></th>
	</tr>
	<tr> 
		<td colspan="4" class="cuadro1izquierda">
			<table border="0" cellpadding="3" cellspacing="0" width="100%" class="text">
				<tr>
					<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar"><img src="../sys_images/add.gif" 
						border="0" align="absmiddle" /></a> <a 
						href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar">Agregar <?=$name?></a></td>
				</tr>
				<tr> 
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="80">Acci&oacute;n:</td>
								<td><input name="nombre_list" type="text" id="nombre_list" value="<?=$nombre_list?>" 
									style="width:95% " /></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>		
	<tr>
		<td colspan="4" class="sombra">
			<table width=100% border=0 cellpadding="0" cellspacing="0" class="text">
				<tr>
					<td align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
					<td align="right">
<?
		if( $paginas->get_total_pages() > 0 ) {
?>
					P&aacute;g<?=$paginas->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$paginas->get_navigator(); ?>&nbsp;
<?
		}
?>
					</td>
				</tr>		
			</table>
		</td>
	</tr>		
	<tr align="center"> 
		<td width="150" align="left" class="titulosizquierda">Acciones</td>
		<td width="360" class="titulos">Descripción</td>
		<td width="100" align="left" class="titulos">Valores</td>
		<td width="30" class="titulos">&nbsp;</td>
	</tr>
<?
		if ($paginas->num_rows () < 1) {
?>
	<tr> 
		<td colspan="4" align="center" class="cuadro1izquierda"><span class="error">No se encuentran registros coincidentes</span></td>
	</tr>
<?
		} else {
			while( $reg = $paginas->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
	<tr> 
		<td class="<?=$class?>izquierda" align="left"><?=$reg->accion?></td>
		<td class="<?=$class?>"><?=printif(nl2br($reg->descripcion))?></td>
		<td class="<?=$class?>" align="left"><?=str_replace(',','<br>',$reg->valores)?></td>	
		<td align="center" class="<?=$class?>"><a 
			href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img 
			src="../sys_images/list.gif" border="0" /></a>&nbsp;<a href="javascript: if (confirm ('<?=$msgDelete?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?>'; }"><img
			src="../sys_images/delete.gif" alt="Eliminar Registro" border="0" /></a>
		</td>
	</tr>
<?
			}
		}
?>
	<tr>
		<td class="sombra" colspan="4">
			<table width=100% border=0 cellpadding="0" cellspacing="0" class="text">
				<tr>
					<td align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
					<td align="right">
<?
		if( $paginas->get_total_pages() > 0 ) {
?>
						P&aacute;g<?=$paginas->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$paginas->get_navigator(); ?>&nbsp;
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
	} // Fin de Accion

	if ( in($action,'frmAgregar','frmModificar') ) {
		if( $action == 'frmModificar' ){
			$rs = $db->selectlimit( "SELECT * FROM ".CFG_actionsTable." WHERE id = '$id'", 1 );
			if( $rs ){
				$reg = $rs->fetchObject(false);
			}
		}
	
		Form::validate ();
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<th class="titulosizquierda" colspan="2" align="center"><?=$action=='frmAgregar'?'Agregar':'Modificar'?> <?=$name?></th>
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
		<td width="150" class="cuadro1izquierda"><?=$error?'<font color="#FF0000">*&nbsp;&nbsp;&nbsp;</font>':''?> Acción</td>
		<td class="cuadro1" width="490"><input name="accion" type="text" value="<?=printif ($reg->accion, $accion)?>" style="width:95% " validar="str:Debe ingrear una acción." /></td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda"><?=$error?'<font color="#FF0000">*&nbsp;&nbsp;&nbsp;</font>':''?> Valores</td>
		<td class="cuadro1"><input name="valores" type="text" value="<?=printif ($reg->valores, $valores)?>" style="width:95% " validar="str:Debe ingresar valores." /></td>
	</tr>		
	<tr>
	    <td class="cuadro1izquierda">Descripcion</td>
	    <td class="cuadro1"><textarea name="descripcion" rows="5" style="width:95%"><?=printif($reg->descripcion,$descripcion)?></textarea></td>
	</tr>
	<tr> 
		<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
		<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->id?>" /></td>
	    <td class="cuadro2"><input name="aceptar" type="submit" class="button" value="Aceptar"/>
		<input name="button" type="button" class="button" onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
	</tr>
	<tr>
		<td colspan="2" class="sombra">&nbsp;</td>
	</tr>
</table>
</form>
<br>
<?
	}
?>
<br>
</body>
</html>