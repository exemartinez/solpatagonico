<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Stock';
	$name		= 'Stock';
	$titleForm	= 'Stock';
	$msgDelete	= '¿Esta seguro que desea eliminar este Productos?';
	# Variables de programación
	$tabla		= "producto";
	$campos		= "codigo,id_estado_producto,id_rubro,nombre,descripcion,presentacion,punto_reposicion,cantidad_real,
	cantidad_reserva,comentario,tamanio_pedido,iva";
	registrar( $campos );
	$id = intval( $id );
	if( !isset( $ocultar_eliminados ) ) $ocultar_eliminados = 1;
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Text($codigo) ) $error .= "<li>Debe ingresar un codigo.</li>";
		if ( !validate::Integer($id_estado_producto) ) $error .= "<li>Debe seleccionar un Estado.</li>";			
		if ( !validate::Integer($id_rubro) ) $error .= "<li>Debe seleccionar un Rubro.</li>";			
		if ( !validate::Text($nombre) ) $error .= "<li>Debe ingresar un Nombre.</li>";
		if ( 
			$db->GetOne( "
				SELECT COUNT(*) 
				FROM ".$tabla." 
				WHERE codigo='".$codigo."'".( $action == 'Modificar' ? " AND id != '".$id."'" : '' ) )
		){
			$error .= "<li>Codigo existente, ingrese otro.</li>";
			unset( $codigo );
		}
		if ( !validate::Integer( $punto_reposicion, '', '', '', '', 0 ) ){
			$error .= "<li>Debe ingresar un Punto de reposici&oacute;n v&aacute;lido (n&uacute;merico).</li>";
			unset( $punto_reposicion );
		}
		if( $tamanio_pedido != '' && !validate::Integer( $tamanio_pedido, '', '', '', '', 0 ) ){
			$error .= "<li>Debe ingresar un Tamaño de pedido v&aacute;lido (n&uacute;merico).</li>";
			unset( $tamanio_pedido );
		}
		if ( !validate::Integer( $cantidad_real, '', '', '', '', 0 ) ){
			$error .= "<li>Debe ingresar una Cantidad real v&aacute;lido (n&uacute;merico).</li>";
			unset( $cantidad_real );
		}
		if ( !validate::Integer( $cantidad_reserva, '', '', '', '', 0 ) ){
			$error .= "<li>Debe ingresar una Cantidad reserva v&aacute;lido (n&uacute;merico).</li>";			
			unset( $cantidad_reserva );
		}
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if( in( $action, 'Stock', 'StockCompra' ) ){
		if( 
			( $action == 'Stock' && $mod_stock == '' ) || 
			( $mod_stock && !validate::Integer( $mod_stock ) ) || 
			( validate::Integer( $mod_stock ) && intval( $mod_stock ) < 0 )
		) 
			$error .= "<li>Debe ingresar una Cantidad real de stock v&aacute;lido mayor o igual a cero (solo n&uacute;meros)</li>";
		if( 
			( $action == 'StockCompra' && !$new_stock ) || 
			( $new_stock && !validate::Integer( $new_stock ) ) || 
			( validate::Integer( $new_stock ) && $new_stock <= 0 )
		) 
			$error .= "<li>Debe ingresar una Stock Ingresado por compra v&aacute;lido mayor a cero (solo n&uacute;meros)</li>";				
		if( $action == 'StockCompra' && !validate::Float( $precio ) ) 
			$error .= "<li>Debe ingresar un Precio unitario válido (solo números).</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		} else {
			if( validate::Integer( $mod_stock ) ) 
				$record["cantidad_real"] = intval( $mod_stock );
			if( validate::Integer( $new_stock ) ) 
				$record["cantidad_real"] = intval( $cantidad_real ) + intval( $new_stock );
			if( $action == 'StockCompra' && validate::Float( $precio ) ){
				$precio = str_replace( ',', '.', $precio ); 
				$stock_real = intval( $db->GetOne( "SELECT cantidad_real FROM ".$tabla." WHERE id = '".$id."'" ) );
				$ppp = doubleval( $db->GetOne( "SELECT ppp FROM ".$tabla." WHERE id = '".$id."'" ) );
				$record["ppp"] = ( ( $stock_real * $ppp ) + ( $new_stock * $precio ) ) / ( $stock_real + $new_stock );
			}
			
			$ok = $db->Execute( get_sql( 'UPDATE', $tabla, $record, "id = $id" ) );
			if( !$ok ){
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			} else {			
				# Si el stock es inferior al actual se debe re-reservar en todos los items del producto
				if( validate::Integer( $mod_stock ) && $mod_stock < $cantidad_real ){
					$db->Execute( "
						UPDATE pedido p 
						LEFT JOIN item_pedido i ON p.id = i.id_pedido 
						SET p.id_estado_pedido = 2 
						WHERE i.id_estado_item = 3 AND i.id_producto = '".$id."'
					" );
					$db->Execute( "UPDATE item_pedido SET id_estado_item = 2 WHERE id_estado_item = 3 AND id_producto = '".$id."'" );
					$db->Execute( "UPDATE ".$tabla." SET cantidad_reserva = 0 WHERE id='".$id."'" );
				}
			
				# Ejecutando tarea de reserva.
				ob_start();
				ob_implicit_flush(0);
					include( 'tarea_reservar.php' );
					$html = ob_get_contents();
				ob_end_clean();
			
				$error = '';
				$action = '';
			}
		}
	}
	
	if ( $action == 'Agregar' ){
		$record["fecha_alta"] = date( "Y-m-d H:i:s" );

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
		if( $restaurar ){
			$record["fecha_baja"] = '';
		}
		
		$ok = $db->Execute( get_sql( 'UPDATE', $tabla, $record, "id = $id" ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$error = '';
			$action = '';
		}
	}
	
	if( $action == 'Precios' ){
		if( !validate::float( $precio ) ){
			$error = '<li>Debe ingresar un Precio v&aacute;lido.</li>';
			$action = 'frm'.$action; 
		} else {
			$precio = str_replace( ',', '.', $precio );
			$ok = $db->Execute( "UPDATE lista_precio_vta SET fecha_baja=NOW() WHERE id_producto='".$id."' AND fecha_baja is NULL" );
			$ok = $db->Execute( "INSERT INTO lista_precio_vta ( fecha_alta, precio, id_producto ) VALUES ( NOW(), '".$precio."', '".$id."' )" );
			if( !$ok ){
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			} else {
				$error = '';
				$action = '';
			}
		}
	}
	
	if ( $action == 'Borrar' ){
		//$ok = $db->execute( "DELETE FROM ".$tabla." WHERE id = '".$id."' " );
		$ok = $db->execute( "UPDATE ".$tabla." SET fecha_baja=NOW() WHERE id = '".$id."' " );
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
			"SELECT p.*, r.nombre rubro
			FROM ".$tabla." p
			LEFT JOIN rubro r ON r.id = p.id_rubro
			WHERE 1=1 
				AND p.nombre LIKE '".html_entity_decode( $nombre_list )."%' ".
				( $codigo_list != '' ? " AND p.codigo LIKE '".html_entity_decode( $codigo_list )."%'" : '' ).
				( $ocultar_eliminados ? " AND ( p.fecha_baja is NULL OR p.fecha_baja = '0000-00-00 00:00:00' )" : '' ).
				( $estado_lst ? " AND p.id_estado_producto='".$estado_lst."'" : "" ).
				( $rubro_lst ? " AND p.id_rubro='".$rubro_lst."'" : "" ).
			" ORDER BY p.nombre" ,
			$cur_page,
			20,
			25,
			"nombre_list,ocultar_eliminados",
			''
		);	
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="746" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="6" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td class="cuadro1izquierda" colspan="6">
					<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
						
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">Código:</td>
                                		<td><input name="codigo_list" type="text" id="codigo_list" value="<?=prepare_var( $codigo_list )?>" style="width:100% " /></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" />	</td>
                               		</tr>
                                	<tr>
                                		<td width="120" class="text">Nombre:</td>
                                		<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" style="width:100% " /></td>
                                		<td width="100">&nbsp;</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Estado:</td>
                                		<td><?
							$rs = $db->Execute( "
								SELECT nombre, id 
								FROM estado_producto 
								ORDER BY nombre
							" );
							echo $rs->GetMenu2(
								'estado_lst',
								$estado_lst,
								": -- Seleccione un estado -- ",
								false,
								1,
								'id="estado_lst" style="width: 100%"'
							);
										?></td>
                                		<td>&nbsp;</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Rubro:</td>
                                		<td><?
							$rs = $db->Execute( "
								SELECT nombre, id 
								FROM rubro 
								ORDER BY nombre
							" );
							echo $rs->GetMenu2(
								'rubro_lst',
								$rubro_lst,
								": -- Seleccione una rubro -- ",
								false,
								1,
								'id="rubro_lst" style="width: 100%"'
							);
										?></td>
                                		<td>&nbsp;</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Ocultar eliminados:</td>
                                		<td><select name="ocultar_eliminados" id="ocultar_eliminados" style="width:100% ">
                                        	<option value="0">No</option>
                                        	<option value="1" <?=( $ocultar_eliminados == 1 ? 'selected' : '' )?>>Si</option>
                                        	</select></td>
                                		<td>&nbsp;</td>
                               		</tr>
                                	</table></td>
						</tr>
				</table>				</td>
			</tr>	
			<tr>
				<td class="sombra" colspan="6">
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
				<td width="130" class="titulosizquierda">C&oacute;digo</td>
				<td width="170" class="titulos">Nombre</td>
				<td width="180" class="titulos">Rubro</td>
				<td width="90" class="titulos">Stock</td>
				<td width="90" class="titulos">S. Reservado</td>
				<td width="50" class="titulos">&nbsp;</td>
			</tr>
<?
		if( $paginas->num_rows () < 1) {
?>
			<tr> 
				<td colspan="6" align="center" class="cuadro1izquierda"><span class="error">No se encuentran registros coincidentes</span></td>
			</tr>
<?
		} else {
			while( $reg = $paginas->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
			<tr>
				<td align="left" class="<?=$class?>izquierda"><?=$reg->codigo?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>"><?=printif($reg->rubro)?></td>
				<td class="<?=$class?>"><?=printif($reg->cantidad_real)?></td>
				<td class="<?=$class?>"><?=printif($reg->cantidad_reserva)?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr><?
				if( $ACTIONS->AdministrarStock && !validate::date( substr( $reg->fecha_baja, 0, 10 ) ) ){
						?><td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmStock&id=<?=$reg->id?>"><img 
						src="../sys_images/stock.gif" border="0" alt="Stock"></a></td><?
						?><td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmStockCompra&id=<?=$reg->id?>"><img 
						src="../sys_images/stockcompra.gif" border="0" alt="Stock Compra"></a></td><?						
				}
					?></tr>
				</table></td>
			</tr>
<?
			}
		}
?>
		<tr>
		    <td class="sombra" colspan="6">
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

	if ( in($action,'frmStock') ) {
		$id = intval($id);
		$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE id = $id", 1 );
		$reg = $rs->FetchObject(false);
		
		Form::validate ();
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" colspan="2" align="center" style="font-size: 14px; font-weight: bold">Gestionando Stock</th>
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
			<td width="150" class="cuadro1izquierda">C&oacute;digo</td>
		    <td width="490" class="cuadro1"><?=$reg->codigo?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Nombre</td>
        	<td class="cuadro1"><?=$reg->nombre?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Descripci&oacute;n</td>
        	<td class="cuadro1"><?=printif( $reg->descripcion )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Rubro</td>
        	<td class="cuadro1"><?=$db->GetOne( "SELECT nombre FROM rubro WHERE id=".$reg->id_rubro )?></td>
		</tr>
		<tr>
			<td class="cuadro1izquierda">IVA</td>
			<td class="cuadro1"><?=$reg->iva?>%</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Presentaci&oacute;n</td>
        	<td class="cuadro1"><?=printif( $reg->presentacion )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Punto de reposici&oacute;n </td>
        	<td class="cuadro1"><?=$reg->punto_reposicion?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Tama&ntilde;o de pedido </td>
        	<td class="cuadro1"><?=$reg->tamanio_pedido?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Cantidad real </td>
        	<td class="cuadro1"><?=intval( $reg->cantidad_real)?><input 
			type="hidden" name="cantidad_real" id="cantidad_real" style="width:95% " value="<?=$reg->cantidad_real?>" /></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Cantidad de reserva</td>
        	<td class="cuadro1"><?=intval( $reg->cantidad_reserva)?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Comentarios</td>
        	<td class="cuadro1"><?=printif( $reg->comentarios )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Estado</td>
        	<td class="cuadro1"><?=$db->GetOne( "SELECT nombre FROM estado_producto WHERE id=".$reg->id_estado_producto )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Fecha de alta </td>
        	<td class="cuadro1"><?=printif( fecha::iso2normal( substr( $reg->fecha_alta, 0, 10 ) ).' '.substr( $reg->fecha_alta, 10 ) )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Cantidad real en Stock </td>
        	<td class="cuadro1"><input type="text" name="mod_stock" id="mod_stock" style="width: 100%"></td>
		</tr>		
		<tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="Stock" />
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
	
	if ( in($action,'frmStockCompra') ) {
		$id = intval($id);
		$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE id = $id", 1 );
		$reg = $rs->FetchObject(false);
		
		Form::validate ();
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" colspan="2" align="center" style="font-size: 14px; font-weight: bold">Gestionando Stock de Compra</th>
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
			<td width="150" class="cuadro1izquierda">C&oacute;digo</td>
		    <td width="490" class="cuadro1"><?=$reg->codigo?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Nombre</td>
        	<td class="cuadro1"><?=$reg->nombre?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Descripci&oacute;n</td>
        	<td class="cuadro1"><?=printif( $reg->descripcion )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Rubro</td>
        	<td class="cuadro1"><?=$db->GetOne( "SELECT nombre FROM rubro WHERE id=".$reg->id_rubro )?></td>
		</tr>
		<tr>
			<td class="cuadro1izquierda">IVA</td>
			<td class="cuadro1"><?=$reg->iva?>%</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Presentaci&oacute;n</td>
        	<td class="cuadro1"><?=printif( $reg->presentacion )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Punto de reposici&oacute;n </td>
        	<td class="cuadro1"><?=$reg->punto_reposicion?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Tama&ntilde;o de pedido </td>
        	<td class="cuadro1"><?=$reg->tamanio_pedido?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Cantidad real </td>
        	<td class="cuadro1"><?=intval( $reg->cantidad_real)?><input 
			type="hidden" name="cantidad_real" id="cantidad_real" style="width:95% " value="<?=$reg->cantidad_real?>" /></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Cantidad de reserva</td>
        	<td class="cuadro1"><?=intval( $reg->cantidad_reserva)?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Comentarios</td>
        	<td class="cuadro1"><?=printif( $reg->comentarios )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Estado</td>
        	<td class="cuadro1"><?=$db->GetOne( "SELECT nombre FROM estado_producto WHERE id=".$reg->id_estado_producto )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Fecha de alta </td>
        	<td class="cuadro1"><?=printif( fecha::iso2normal( substr( $reg->fecha_alta, 0, 10 ) ).' '.substr( $reg->fecha_alta, 10 ) )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Stock Ingresado por Compra</td>
        	<td class="cuadro1"><input type="text" name="new_stock" id="new_stock" value="<?=$new_stock?>" style="width: 100%"></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Precio Unitario</td>
        	<td class="cuadro1"><input type="text" name="precio" id="precio" value="<?=$precio?>" style="width: 100%"></td>
		</tr>		
		<tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="StockCompra" />
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