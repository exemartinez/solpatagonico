<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Precios';
	$name		= 'Precio';
	$titleForm	= 'Precios';
	$msgDelete	= '¿Esta seguro que desea eliminar este Precio?';
	# Variables de programación
	$tabla		= "proveedor_producto";
	$campos		= "id_proveedor,id_producto,precio";
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Integer($id_proveedor) ) $error .= "<li>Debe seleccionar un Proveedor.</li>";
		if ( !validate::Integer($id_producto) ) $error .= "<li>Debe seleccionar un Producto.</li>";
		if ( !validate::Float($precio) ) $error .= "<li>Debe ingresar un Precio v&aacute;lido.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$precio = str_replace( ',', '.', $precio );
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		$ok = $db->Execute( get_sql( 'INSERT', $tabla, $record, '' ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$error = '';
			$action = '';
		}		
	}
	
	if ( $action == 'Modificar' ){
		$ok = $db->Execute( get_sql( 'UPDATE', $tabla, $record, "id = $id" ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$error = '';
			$action = '';
		}
	}
	
	if ( $action == 'Borrar' ){
		$ok = $db->execute( "DELETE FROM ".$tabla." WHERE id = '".$id."' " );
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
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
</head>

<body>
<?
	if ( in($action,'','buscar') ) {
		$paginas = new pager(
			"SELECT precio.id, precio.precio, prov.razonsocial, prod.nombre
			FROM ".$tabla." precio 
			LEFT JOIN proveedor prov ON precio.id_proveedor = prov.id 
			LEFT JOIN producto prod ON precio.id_producto = prod.id 
			WHERE 1=1 AND ( prov.fecha_baja is NULL OR prov.fecha_baja = '0000-00-00 00:00:00' )".
				( $proveedor_lst != '' ? " AND precio.id_proveedor = '".$proveedor_lst."' " : '' ).
				( $producto_lst ? " AND precio.id_producto = '".$producto_lst."'" : '' ).
			" ORDER BY precio.id_proveedor" ,
			$cur_page,
			20,
			25,
			"proveedor_lst,producto_lst",
			''
		);	
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="4" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td class="cuadro1izquierda" colspan="4">
					<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
						<tr>
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&cuit=<?=$cuit_list?>"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&cuit=<?=$cuit_list?>">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">Producto:</td>
                                		<td><?
					$rs = $db->Execute( "
						SELECT nombre, id 
						FROM producto 
						WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00'
						ORDER BY nombre
					" );
					echo $rs->GetMenu2(
						'producto_lst',
						$producto_lst,
						': -- Seleccione un producto -- ',
						false,
						1,
						'id="producto_lst" style="width: 100%"'
					);
										?></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" />	</td>
                               		</tr>
                                	<tr>
                                		<td width="120" class="text">Proveedor:</td>
                                		<td><?
					$rs = $db->Execute( "
						SELECT razonsocial, id 
						FROM proveedor 
						WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00'
						ORDER BY razonsocial
					" );
					echo $rs->GetMenu2(
						'proveedor_lst',
						$proveedor_lst,
						': -- Seleccione un proveedor -- ',
						false,
						1,
						'id="proveedor_lst" style="width: 100%"'
					);
										?></td>
                                		<td width="100">&nbsp;</td>
                               		</tr>
                           	</table></td>
						</tr>
					</table>				</td>
			</tr>	
			<tr>
				<td class="sombra" colspan="4">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class=text>
						<tr>
							<td class="text" align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
							<td align="right" class="text">
<?
		if( $paginas->get_total_pages() > 0 ) {
?>
								P&aacute;g<?=$paginas->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$paginas->get_navigator(); ?>&nbsp;
<?
		}
?>							</td>
						</tr>		
					</table>				</td>
			</tr>		
			<tr>
				<td class="titulosizquierda">Raz&oacute;n Social</td>
				<td width="150" class="titulos">Producto</td>
				<td width="80" class="titulos">Precio</td>
				<td width="60" class="titulos">&nbsp;</td>
			</tr>
<?
		if( $paginas->num_rows () < 1) {
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
				<td class="<?=$class?>izquierda"><?=$reg->razonsocial?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>"><?=$reg->precio?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img 
src="../sys_images/list.gif" border="0" alt="Modificar" /></a></td>
						<td width="20" align="center"><a href="javascript: if (confirm('<?=( $reg->borrado ? $msgUnDelete : $msgDelete )?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?><?=( $reg->borrado ? '&br=0' : '&br=1')?>'}"><img src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a></td>
					</tr>
				</table></td>
			</tr>
<?
			}
		}
?>
		<tr>
		    <td class="sombra" colspan="4">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class=text>
					<tr>
						<td class="text" align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
						<td align="right" class="text">
<?
		if( $paginas->get_total_pages() > 0 ) {
?>
							P&aacute;g<?=$paginas->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$paginas->get_navigator(); ?>&nbsp;
<?
		}
?>						</td>
					</tr>		
		    </table>		    </td>
		</tr>		
	</table>
</form>
<?
	} // Fin de Accion

	if ( in($action,'frmAgregar','frmModificar') ) {
		if ( $action == 'frmModificar' ) {
			$id = intval($id);
			$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE id = $id", 1 );
			$reg = $rs->FetchObject(false);
		}
		
		Form::validate ();
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" colspan="2" align="center" style="font-size: 14px; font-weight: bold"><?=$action=='frmAgregar'?'Agregar':'Modificar'?> <?=$name?></th>
		</tr>
<?
		if ( $error ) {
?>
		<tr height="30">
			<td class="cuadro1izquierda" colspan="2"><span class="error"><?=$error?></span></td>
		</tr>
<?
		}	
?>				
		<tr> 
			<td width="150" class="cuadro1izquierda">*Proveedor</td>
		    <td width="490" class="cuadro1"><?
		$rs = $db->Execute( "
			SELECT razonsocial, id 
			FROM proveedor 
			WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' 
			ORDER BY razonsocial
		" );
		echo $rs->GetMenu2(
			'id_proveedor',
			printif( $id_proveedor, $reg->id_proveedor),
			': -- Seleccione un Proveedor -- ',
			false,
			1,
			'id="id_proveedor" style="width: 100%"'
		);
			?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">*Producto</td>
        	<td class="cuadro1"><?
		$rs = $db->Execute( "
			SELECT nombre, id FROM producto 
			WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' 
			ORDER BY nombre
		" );
		echo $rs->GetMenu2(
			'id_producto',
			printif( $id_producto, $reg->id_producto),
			': -- Seleccione un Producto -- ',
			false,
			1,
			'id="id_producto" style="width: 100%"'
		);
			?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">*Precio</td>
        	<td class="cuadro1"><input name="precio" type="text" value="<?=prepare_var( printif( $precio, $reg->precio ) )?>" 
			style="width:100% " /></td>
		</tr>
		<tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
				<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->id?>" />			</td>
		    <td class="cuadro2"><input name="aceptar" type="submit"  value="Aceptar"/>
           	<input name="button" type="button"  onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
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
</body>
</html>