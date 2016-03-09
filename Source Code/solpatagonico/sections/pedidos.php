<?
	include_once '../inc/conf.inc.php';
	require_once CFG_libPath.'xajax/xajax.inc.php';
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_cliente");
	$xajax->registerFunction("bajar_sucursal");	
	$xajax->registerFunction("bajar_producto");		
	
	$xajax->registerFunction("chequear_stock");	
	
	$xajax->processRequests();
	
	function bajar_cliente( $cod_cliente ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_cliente' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'id_cliente' ).value = '';" );
		if( $cod_cliente ){
			$rs = $db->Execute( "
				SELECT * 
				FROM cliente 
				WHERE cuit LIKE '".$cod_cliente."%' OR razonsocial LIKE '".html_entity_decode( $cod_cliente )."%' 
				AND ( fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' )
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_cliente' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'id_cliente' ).value = '".$reg->id."';" );			
				$objResponse->addScript( "document.getElementById( 'td_cuit' ).innerHTML = '".$reg->cuit."';" );
				$objResponse->addScript( "document.getElementById( 'td_razonsocial' ).innerHTML = '".$reg->razonsocial."';" );
				$objResponse->addScript( "document.getElementById( 'td_iibb' ).innerHTML = '".$reg->nroiibb."';" );
				$objResponse->addScript( "document.getElementById( 'td_direccion' ).innerHTML = '".$reg->direccion."';" );
				$objResponse->addScript( "document.getElementById( 'td_telefono' ).innerHTML = '".$reg->telefono."';" );
			}
		}
		return $objResponse;
	}
	
	function bajar_sucursal( $cod_sucursal, $id_cliente ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_sucursal' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'id_sucursal' ).value = '';" );
		if( $cod_sucursal && $id_cliente ){
			$rs = $db->Execute( "
				SELECT s.*, z.nombre zona 
				FROM sucursal s 
				LEFT JOIN zona z ON s.id_zona = z.id  
				WHERE s.nombre LIKE '".html_entity_decode( $cod_sucursal )."%' AND s.id_cliente = '".$id_cliente."' 
				AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' )
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_sucursal' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'id_sucursal' ).value = '".$reg->id."';" );			
				$objResponse->addScript( "document.getElementById( 'td_nombre' ).innerHTML = '".$reg->nombre."';" );
				$objResponse->addScript( "document.getElementById( 'td_contacto' ).innerHTML = '".$reg->contacto."';" );
				$objResponse->addScript( "document.getElementById( 'td_zona' ).innerHTML = '".$reg->zona."';" );
				$objResponse->addScript( "document.getElementById( 'td_direccion_suc' ).innerHTML = '".$reg->direccion."';" );
				$objResponse->addScript( "document.getElementById( 'td_telefono_suc' ).innerHTML = '".$reg->telefono."';" );
			}
		}
		return $objResponse;
	}
	
	function bajar_producto( $cod_producto ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addAssign( "td_proveedores", "innerHTML", "" );
		$objResponse->addAssign( "td_producto", "innerHTML", "" );
		$objResponse->addAssign( "id_producto", "value", "" );
		$objResponse->addAssign( "nom_producto", "value", "" );
		$objResponse->addAssign( "codigo_producto", "value", "" );
		if( $cod_producto ){
			$rs = $db->Execute( "
				SELECT * 
				FROM producto 
				WHERE codigo LIKE '".html_entity_decode( $cod_producto )."%' 
				AND ( fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' )
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );

				$objResponse->addAssign( "td_proveedores", "innerHTML", '<a href="reportes_85.php?action=buscar&codigo='.$reg->codigo.'" target="_blank">Ver proveedores</a>' );
				$objResponse->addAssign( "td_producto", "innerHTML", '['.$reg->codigo.'] '.$reg->nombre.': '.$reg->descripcion );
				$objResponse->addAssign( "id_producto", "value", $reg->id );
				$objResponse->addAssign( "nom_producto", "value", $reg->nombre );
				$objResponse->addAssign( "codigo_producto", "value", $reg->codigo );
			}
		}
		return $objResponse;
	}

	function chequear_stock( $cantidad, $id ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		if( !validate::Integer( $id ) ) $objResponse->addAlert( "Debe seleccionar un producto valido" );
		else if( 
			!validate::Integer( $cantidad ) || 
			( validate::Integer( $cantidad ) && $cantidad <= 0 ) 
		) $objResponse->addAlert( "Debe ingresar una cantidad valida mayor a cero" );
		else if( $cantidad && $id ){
			$rs = $db->Execute( "
				SELECT * 
				FROM producto 
				WHERE id = '".$id."' 
			" );
			$reg = $rs->FetchObject( false );
			//if( ( $reg->cantidad_real - $reg->cantidad_reserva - $cantidad ) < 0 ){
			if( 0 ){
				$objResponse->addAlert( "El stock no alcanza para cumplir con la cantidad ingresada.\n\nStock real actual: ".$reg->cantidad_real."\nStock reservado actual: ".$reg->cantidad_reserva."\nCantidad ingresada: ".$cantidad );
			} else {
				$objResponse->addScriptCall( "agregar_producto" );
			}
		}
		return $objResponse;
	}
	
	
	# Variables de diseño
	$title		= 'Pedidos';
	$name		= 'Pedido';
	$titleForm	= 'Pedidos';
	$msgCancel	= '¿Desea cancelar la carga del pedido actual?\nSe perderan los datos ingresados';	
	$msgDelete	= '¿Esta seguro que desea eliminar el registro?';
	# Variables de programación
	$tabla		= "pedido";
	$tabla_item	= "item_pedido";
	$campos		= "id_cliente,id_sucursal,id_estado_pedido,fecha_alta";	
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( $action == 'Agregar' ) {
		if ( !validate::Integer($id_cliente) ) 
			$error .= "<li>Debe seleccionar un Cliente.</li>";
		if ( !validate::Integer($id_sucursal) ) 
			$error .= "<li>Debe seleccionar una Sucursal.</li>";			
		if ( !count($items) ) 
			$error .= "<li>Debe seleccionar al menos un producto.</li>";			
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		$record['id_estado_pedido'] = 1; // Estado: Pendiente inicial
		$record['fecha_alta'] = date( "Y-m-d H:i:s" );
		$ok = $db->Execute( get_sql( 'INSERT', $tabla, $record, '' ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$id_pedido = mysql_insert_id();
			foreach( $items as $id_producto => $cantidad ){
				$ok = $db->Execute( "
					INSERT INTO ".$tabla_item." 
					VALUES ( 
						'',
						'',
						'1',
						'".$id_pedido."',
						'".$cantidad."',
						'".$id_producto."'						
					)" );
			}
			$error = '';
			$action = 'Ok';
			
			# Ejecutando tarea de reserva.
			ob_start();
			ob_implicit_flush(0);
				include( 'tarea_reservar.php' );
				$html = ob_get_contents();
			ob_end_clean();
		}		
	}
	
	if( $action == 'Modificar' ){
		$ids = '';
		$sep = '';
		foreach( $items as $id_producto => $cantidad ){
			if( $id_producto ) $ids .= $sep.$id_producto;
			$sep = ',';
			$aux = $db->GetRow( "
				SELECT * 
				FROM item_pedido 
				WHERE id_producto='".$id_producto."' AND id_pedido='".$id."'
			" );
			if( !$aux ){ # Nuevo producto
				$ok = $db->Execute( "
					INSERT INTO ".$tabla_item." 
					VALUES ( 
						'',
						'',
						'1',
						'".$id."',
						'".$cantidad."',
						'".$id_producto."'						
					)" 
				);			
			} elseif( $aux['id_estado_item']< 3 ){ # Producto existente
				$ok = $db->Execute( "
					UPDATE ".$tabla_item." 
					SET cantidad = '".$cantidad."'
					WHERE id_producto = '".$id_producto."' AND id_pedido='".$id."'
				" );
			}
		}
		if( $ids ) $db->Execute( "DELETE FROM ".$tabla_item." WHERE id_pedido='".$id."' AND id_producto NOT IN (".$ids.")" );
		$error = '';
		$action = '';
		
		# Ejecutando tarea de reserva.
		ob_start();
		ob_implicit_flush(0);
			include( 'tarea_reservar.php' );
			$html = ob_get_contents();
		ob_end_clean();
	}
	
	if ( $action == 'Borrar' ){
		$ok = $db->execute( "UPDATE ".$tabla." SET fecha_baja=NOW(), id_estado_pedido=7 WHERE id = '".$id."' " );
		/*
		$rs = $db->Execute( "SELECT * FROM item_pedido WHERE id_pedido = '".$id."' " );
		while( $item = $rs->FetchNextObject( false ) ){
			if( $item->id_estado_item == 3 ){ # Reservado
				$ok = $db->Execute( "
					UPDATE producto 
					SET cantidad_reserva=cantidad_reserva+".$item->cantidad." 
					WHERE id='".$item->id_producto."'
				" );
			}
		}
		*/
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
	<SCRIPT LANGUAGE="JavaScript" SRC="<?=CFG_jsPath?>CalendarPopup.js"></SCRIPT>
	<? $xajax->printJavascript( '../javascript/', 'xajax.js' ); ?>
	<script>
	<!--
		function seleccionar(){
			if( document.getElementById('id_cliente').value == '' ){
				alert( 'Debe seleccionar un Cliente' );
				document.getElementById('cod_cliente').focus();
			} else if( document.getElementById('id_sucursal').value == '' ){
				alert( 'Debe seleccionar una Sucursal' );
				document.getElementById('cod_sucursal').focus();
			} else {
				document.form1.target = '_self';
				document.getElementById( 'action' ).value = 'frm' + document.getElementById( 'action' ).value;
				document.form1.submit();
			}
		}
		
		function seleccionar_sucursal( id ){
			var elem = opener.document.getElementById( 'cod_sucursal' )
			
			elem.value = id;			
			try { elem.onkeyup() } catch ( all ) { }
			self.close();
		}		
		
		function seleccionar_cliente( id ){
			var elem = opener.document.getElementById( 'cod_cliente' )
			
			elem.value = id;			
			try { elem.onkeyup() } catch ( all ) { }
			seleccionar_sucursal( '' );
			self.close();
		}
		
		function seleccionar_producto( id ){
			var elem = opener.document.getElementById( 'cod_producto' )
			
			elem.value = id;			
			try { elem.onkeyup() } catch ( all ) { }
			self.close();
		}

		function enter( funcion ){
			if( event.keyCode == 13 ){
				eval( funcion );
			}
		}	
		
		function preview(){
			if( document.getElementById( 'tbl_items' ).rows.length == 1 ){
				alert( 'Debe seleccionar al menos 1 producto' );
				document.getElementById( 'cod_producto' ).focus();
				location.href = '#Bottom';
			} else {
				document.form1.target = '_blank';
				document.getElementById( 'action' ).value = 'Preview';
				document.form1.submit();
			}
		}
		
		function aprobar(){
			opener.document.getElementById( 'form1' ).target = '_self';
			if( 
				opener.document.getElementById( 'id' ).value != '' && 
				opener.document.getElementById( 'id' ).value != '0'
			){
				opener.document.getElementById( 'action' ).value = 'Modificar';
			} else {
				opener.document.getElementById( 'action' ).value = 'Agregar';
			}
			opener.document.getElementById( 'form1' ).submit();
			window.close();		
		}
		
		function limpiar(){
			document.getElementById( 'td_producto' ).innerHTML = '';
			document.getElementById( 'cod_producto' ).value = '';
			document.getElementById( 'id_producto' ).value = '';
			document.getElementById( 'nom_producto' ).value = '';
			document.getElementById( 'codigo_producto' ).value = '';
			document.getElementById( 'cantidad' ).value = '';
		}	
		
		function check_stock(){
			var cantidad = document.getElementById( 'cantidad' ).value;
			var id = document.getElementById( 'id_producto' ).value;			
			
			try{				
				var aux_tr_index = document.getElementById( 'prods_'+id_producto.value ).parentNode.parentNode.sourceIndex;
			} catch(all){
				var aux_tr_index = 0;
			}
			if( aux_tr_index > 0 ){
				var aux2 = document.getElementById( 'prods_'+id_producto.value ).childNodes(2);
				cantidad = parseInt( cantidad ) + parseInt( aux2.innerHTML );
			}
			xajax_chequear_stock( cantidad, id );
			//agregar_producto();
		}

		function agregar_producto(){
			var tabla = document.getElementById( 'tbl_items' );
			var id_producto = document.getElementById( 'id_producto' );
			var nom_producto = document.getElementById( 'nom_producto' );
			var codigo_producto = document.getElementById( 'codigo_producto' );				
			var cod_producto = document.getElementById( 'cod_producto' );
			var cantidad = document.getElementById( 'cantidad' );
			
			if( id_producto.value == '' ){
				alert( 'Debe seleccionar un producto' );
				document.getElementById( 'cod_producto' ).focus();
			} else if( !Number( parseInt( cantidad.value ) ) ){
				alert( 'Debe ingresar una cantidad valida.' );
				cantidad.value = '';
				cantidad.focus();
			} else if( parseInt( cantidad.value ) < 1 ){
				alert( 'Debe ingresar una cantidad superior a 0.' );
				cantidad.value = '';
				cantidad.focus();
			} else {
				try{
					var aux_tr_index = document.getElementById( 'prods_'+id_producto.value ).parentNode.parentNode.sourceIndex;
					try{
						var aux_nomod = document.getElementById( 'del_'+id_producto.value ).name;
					} catch(all) {
						var aux_nomod = '';
					}
				} catch(all){
					var aux_tr_index = 0;
				}
				if( aux_tr_index > 0 ){ // Sumando
					if( aux_nomod != '' ){ // Solo sumar si tiene definido el id / name de la imagen de Delete
						var aux2 = document.getElementById( 'prods_'+id_producto.value ).childNodes(2);
						var aux3 = document.getElementById( 'prods_'+id_producto.value ).childNodes(3);
						aux2.innerHTML = parseInt( aux2.innerHTML ) + parseInt( cantidad.value );
						aux3.innerHTML = '<img src="../sys_images/delete.gif" border="0" style="cursor: pointer;" onClick="eliminar_producto(this);" id="del_' + id_producto.value + '" name="del_' + id_producto.value + '">';
						aux3.innerHTML += '<input type="hidden" name="items[' + id_producto.value + ']" value="' + parseInt( aux2.innerHTML ) + '">';
					}
				} else { // Agregando
					obj_tr = document.createElement('tr');
					obj_tr.name = 'prods_'+id_producto.value;
					obj_tr.id = 'prods_'+id_producto.value;
					
					obj_td_codigo = document.createElement('td');
					obj_td_nombre = document.createElement('td');
					obj_td_cantidad = document.createElement('td');
					obj_td_botones = document.createElement('td');
					
					obj_td_cantidad.align = 'right';
					obj_td_botones.align = 'center';	
					
					obj_td_codigo.className = 'cuadro1izquierda';
					obj_td_nombre.className = 'cuadro1';
					obj_td_cantidad.className = 'cuadro1';
					obj_td_botones.className = 'cuadro1';
					
					obj_td_codigo.innerHTML = codigo_producto.value;
					obj_td_nombre.innerHTML = nom_producto.value;
					obj_td_cantidad.innerHTML = cantidad.value;
					obj_td_botones.innerHTML = '<img src="../sys_images/delete.gif" border="0" style="cursor: pointer;" onClick="eliminar_producto(this);" id="del_' + id_producto.value + '" name="del_' + id_producto.value + '">';
					obj_td_botones.innerHTML += '<input type="hidden" name="items[' + id_producto.value + ']" value="' + cantidad.value + '">';
										
					obj_tr.appendChild( obj_td_codigo );
					obj_tr.appendChild( obj_td_nombre );
					obj_tr.appendChild( obj_td_cantidad );
					obj_tr.appendChild( obj_td_botones );
					
					tabla.rows( tabla.rows.length - 1 ).insertAdjacentElement( 'afterEnd', obj_tr );
				}
				limpiar();
				location.href='#Bottom';
				cod_producto.focus();
			}
		}
		
		function eliminar_producto( elem ){		
			elem.parentNode.parentNode.removeNode( true );
		}
	-->
	</script>
</head>

<body>
<?
	if ( in($action,'','buscar') ) {
		$paginas = new pager(
			"SELECT p.id, CONCAT( c.razonsocial, ' > ', s.nombre ) cliente, e.nombre estado, p.id_estado_pedido, p.fecha_alta 
			FROM ".$tabla." p 
			LEFT JOIN estado_pedido e ON p.id_estado_pedido = e.id 
			LEFT JOIN cliente c ON p.id_cliente = c.id 
			LEFT JOIN sucursal s ON p.id_sucursal = s.id 
			ORDER BY p.id DESC" ,
			$cur_page,
			20,
			25,
			"",
			''
		);	
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="860" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="5" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td class="cuadro1izquierda" colspan="5">
					<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
						<tr>
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar"><img src="../sys_images/add.gif" border="0" 
							align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
					</table>				</td>
			</tr>	
			<tr>
				<td class="sombra" colspan="5">
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
				<td width="90" class="titulosizquierda">Nro. de Pedido</td>
				<td width="130" class="titulos">Fecha</td>
				<td width="455" class="titulos">Cliente / Sucursal</td>
				<td width="125" class="titulos">Estado del pedido</td>
				<td width="60" class="titulos">&nbsp;</td>
			</tr>
<?
		if( $paginas->num_rows () < 1) {
?>
			<tr> 
				<td colspan="5" align="center" class="cuadro1izquierda"><span class="error">No se encuentran registros coincidentes</span></td>
			</tr>
<?
		} else {
			while( $reg = $paginas->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
			<tr>
				<td align="left" class="<?=$class?>izquierda"><?=$reg->id?></td>
				<td align="left" class="<?=$class?>"><?=fecha::iso2normal( substr( $reg->fecha_alta, 0, 10 ) ).' '.substr( $reg->fecha_alta, 10 )?></td>
				<td align="left" class="<?=$class?>"><?=$reg->cliente?></td>
				<td align="center" class="<?=$class?>"><?=$reg->estado?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr>
						<td width="33%" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=View&id=<?=$reg->id?>"><img src="../sys_images/search.gif" 
					border="0" alt="Ver" /></a></td>
						<td width="33%" align="center"><?
				if( in( $reg->id_estado_pedido, 1, 2 ) ){ # Hasta Reservado
					?><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img src="../sys_images/list.gif" 
					border="0" alt="Modificar" /></a><?
				}
						?></td>
						<td width="33%" align="center"><?
				if( 
					$reg->id_estado_pedido == 1 ||
					( 
						$reg->id_estado_pedido == 2 && 
						!$db->GetOne( "SELECT COUNT(*) FROM item_pedido WHERE id_pedido='".$reg->id."' AND id_estado_item > 2" )
					)
				){ # Solo estado inicial
					?><a href="javascript:if( confirm( '<?=$msgDelete?>' ) ){ location.href='<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?>' }">
					<img src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a><?
				}
						?></td>
					</tr>
				</table></td>
			</tr>
<?
			}
		}
?>
		<tr>
		    <td class="sombra" colspan="5">
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

	if( in( $action, 'frmAgregar', 'frmModificar' ) ){
		if( $action == 'frmModificar' ){
			$rs = $db->Execute( "SELECT * FROM pedido WHERE id='".$id."'" );
			$reg = $rs->FetchObject( false );
			
			$fecha = fecha::iso2normal( $reg->fecha_alta );
			$id_cliente = $reg->id_cliente;
			$id_sucursal = $reg->id_sucursal;
		}
	?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="<?=( $action == 'frmAgregar' ? 'Agregar' : 'Modificar' )?>" />
	<input type="hidden" name="id" id="id" value="<?=$id?>" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="2" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr><?
		if( $error ){
			?><tr>
				<td colspan="2" class="cuadro1izquierda"><span class="error"><?=$error?></span></td>
			</tr><?
		}
			?>
			<tr>
				<td class="cuadro1izquierda">Cliente</td>
				<td class="cuadro1"><?
		if( !$id_cliente ){
				?><input type="text" name="cod_cliente" id="cod_cliente" value="<?=prepare_var( $cod_cliente )?>" 
				onKeyUp="xajax_bajar_cliente( this.value ); enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: void open( 
				'pedidos.php?action=list_clientes', 'buscador', 
				'popup width=660 height=500 scrollbars=yes resizable=yes')" 
				target="main" tabindex="-1"><img src="../sys_images/search.gif" border="0" align="absmiddle" 
				alt="Buscar Clientes"></a><?
		} else {
				?>&nbsp;<?
		}
				?></td>
			</tr><?
		if( $id_cliente ){
			$rs = $db->Execute( "SELECT * FROM cliente WHERE id='".$id_cliente."'" );
			$reg = $rs->FetchObject( false );
		}
			?><tr id="tr_cliente" style="display: <?=( $id_cliente ? 'block' : 'none' )?>;">
				<td class="cuadro1izquierda">&nbsp;<input type="hidden" name="id_cliente" id="id_cliente" 
				value="<?=$id_cliente?>"></td>
				<td class="cuadro1"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
					<tr>
						<th class="titulosizquierda" colspan="2">Datos del Cliente</th>
					</tr>
					<tr>
						<td width="100" class="cuadro1izquierda">CUIT:</td>
						<td id="td_cuit" class="cuadro1"><?=$reg->cuit?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Raz&oacute;n Social:</td>
						<td id="td_razonsocial" class="cuadro1"><?=$reg->razonsocial?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Ingresos Brutos:</td>
						<td id="td_iibb" class="cuadro1"><?=$reg->nroiibb?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Direcci&oacute;n:</td>
						<td id="td_direccion" class="cuadro1"><?=$reg->direccion?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Tel&eacute;fono:</td>
						<td id="td_telefono" class="cuadro1"><?=$reg->telefono?></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Sucursal</td>
				<td class="cuadro1"><?
		if( !$id_sucursal ){
				?><input type="text" name="cod_sucursal" id="cod_sucursal" value="<?=prepare_var( $cod_sucursal )?>" 
				onKeyUp="xajax_bajar_sucursal( this.value, document.getElementById( 'id_cliente' ).value ); 
				enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: if( document.getElementById('id_cliente').value != '' ){ void open( 
				'pedidos.php?action=list_sucursales&id_cliente='+document.getElementById('id_cliente').value, 'buscador', 
				'popup width=660 height=450 scrollbars=yes resizable=yes') } else { alert( 'Debe seleccionar un cliente primero.' ); }" 
				target="main" tabindex="-1"><img src="../sys_images/search.gif" border="0" align="absmiddle" 
				alt="Buscar Sucursales"></a><?
		} else {
				?>&nbsp;<?
		}
				?></td>
			</tr><?
		if( $id_sucursal ){
			$rs = $db->Execute( "
				SELECT s.*, z.nombre zona 
				FROM sucursal s 
				LEFT JOIN zona z ON s.id_zona = z.id 
				WHERE s.id='".$id_sucursal."'
			" );
			$reg = $rs->FetchObject( false );
		}
			?><tr id="tr_sucursal" style="display: <?=( $id_sucursal ? 'block' : 'none' )?>;">
				<td class="cuadro1izquierda">&nbsp;<input type="hidden" name="id_sucursal" id="id_sucursal" 
				value="<?=$id_sucursal?>"></td>
				<td class="cuadro1"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
					<tr>
						<th class="titulosizquierda" colspan="2">Datos de la Sucursal</th>
					</tr>
					<tr>
						<td width="100" class="cuadro1izquierda">Nombre:</td>
						<td id="td_nombre" class="cuadro1"><?=$reg->nombre?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Contacto</td>
						<td id="td_contacto" class="cuadro1"><?=$reg->contacto?></td>
					</tr>
					<tr>
						<td width="100" class="cuadro1izquierda">Zona:</td>
						<td id="td_zona" class="cuadro1"><?=$reg->zona?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Direcci&oacute;n:</td>
						<td id="td_direccion_suc" class="cuadro1"><?=$reg->direccion?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">Tel&eacute;fono:</td>
						<td id="td_telefono_suc" class="cuadro1"><?=$reg->telefono?></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input name="seleccionar_cliente" type="button" value="Seleccionar" 
				onClick="seleccionar()" <?=( $id_cliente && $id_sucursal ? 'style="display: none"' : '' )?>/>&nbsp;<input 
				name="cancelar" type="button" value="Cancelar" 
				onClick="if( confirm( '<?=$msgCancel?>' ) ){ location = '<?=$_SERVER['PHP_SELF']?>'; }" /></td>
			</tr><?
		if( $id_cliente && $id_sucursal ){
			?><tr>
				<td width="150" class="cuadro1izquierda">Productos</td>
				<td width="490" class="cuadro1"><table width="100%" border="0" cellspacing="4" cellpadding="0" class="text">
					<tr>
						<td colspan="2"><input type="text" name="cod_producto" id="cod_producto" 
						onKeyUp="xajax_bajar_producto( this.value )">&nbsp;<a 
						href="javascript: void open( 
						'pedidos.php?action=list_productos', 'buscador', 
						'popup width=660 height=450 scrollbars=yes resizable=yes')" 
						target="main" tabindex="-1"><img src="../sys_images/search.gif" border="0" align="absmiddle" 
						alt="Buscar Productos"></a></td>
						<td width="50%" id="td_producto">&nbsp;</td>
					</tr>
					<tr>
						<td width="25%"  align="right" id="td_proveedores">&nbsp;</td>
						<td width="25%"  align="right">Cantidad:</td>
						<td><input type="text" name="cantidad" id="cantidad" 
						onKeyUp="enter( 'check_stock()' );">&nbsp;<img src="../sys_images/mas.gif" border="0" 
						alt="Agregar Item" onClick="check_stock()" style="cursor: pointer;"><input type="hidden" 
						name="id_producto" id="id_producto" value="" /><input type="hidden" name="nom_producto" 
						id="nom_producto" value="" /><input type="hidden" name="codigo_producto" id="codigo_producto" 
						value="" /></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input name="preview_pedido" type="button" value="Ver Pedido" onClick="preview()" />&nbsp;<input 
				name="cancelar" type="button" value="Cancelar" 
				onClick="if( confirm( '<?=$msgCancel?>' ) ){ location = '<?=$_SERVER['PHP_SELF']?>'; }" /></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">&nbsp;</td>
				<td class="cuadro1"><table width="100%" border="0" cellpadding="0" cellspacing="0" id="tbl_items">
					<tr>
						<td width="70" class="cuadro2izquierda" align="center">C&oacute;digo</td>
						<td class="cuadro2">Producto</td>
						<td width="55" class="cuadro2">Cantidad</td>
						<td width="25" class="cuadro2">&nbsp;</td>
					</tr><?
			if( $action == 'frmModificar' ){
				# solo modificar estado item 1 y 2
				$rs = $db->Execute( "
					SELECT p.codigo, p.nombre, i.cantidad, i.id_producto, i.id_estado_item, e.nombre estado  
					FROM item_pedido i 
					LEFT JOIN producto p ON i.id_producto = p.id 
					LEFT JOIN estado_item e ON i.id_estado_item = e.id 
					WHERE i.id_pedido = '".$id."' 
					ORDER BY p.codigo, p.nombre
				" );
				while( $item = $rs->FetchNextObject( false ) ){
					?><tr id="prods_<?=$item->id_producto?>">
						<td class="cuadro1izquierda" align="center"><?=$item->codigo?></td>
						<td class="cuadro1"><?=$item->nombre?> [<?=$item->estado?>]</td>
						<td class="cuadro1" align="right"><?=$item->cantidad?></td>
						<td class="cuadro1" align="center"><?
					if( $item->id_estado_item < 3 ){
						?><img src="../sys_images/delete.gif" border="0" style="cursor: pointer;" 
						onClick="eliminar_producto(this);" id="del_<?=$item->id_producto?>" name="del_<?=$item->id_producto?>"><?
					} else {
						?>&nbsp;<?
					}
					?><input type="hidden" name="items[<?=$item->id_producto?>]" value="<?=$item->cantidad?>"></td>
					</tr><?					
				}
			}
				?></table></td>
			</tr><?
		}
	?></table>
	<br>
	<a name="Bottom"></a>
</form><?
	}
	
	if( in( $action, 'Preview', 'View' ) ){
		?><table align="center" width="640" cellpadding="3" cellspacing="0" border="0"><?
		if( $action == 'Preview' ){
			$rs = $db->Execute( "SELECT * FROM cliente WHERE id='".$id_cliente."'" );
		}
		if( $action == 'View' ){
			$rs = $db->Execute( "
				SELECT c.* 
				FROM cliente c 
				LEFT JOIN ".$tabla." p ON c.id = p.id_cliente
				WHERE p.id='".$id."'
			" );
		}
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2">Datos del Cliente</th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">CUIT:</td>
				<td width="490" class="cuadro1"><?=$reg->cuit?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Raz&oacute;n Social:</td>
				<td class="cuadro1"><?=$reg->razonsocial?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Ingresos Brutos:</td>
				<td class="cuadro1"><?=$reg->nroiibb?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Direcci&oacute;n:</td>
				<td class="cuadro1"><?=$reg->direccion?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Tel&eacute;fono:</td>
				<td class="cuadro1"><?=$reg->telefono?></td>
			</tr><?
		if( $action == 'Preview' ){
			$rs = $db->Execute( "
				SELECT s.*, z.nombre zona 
				FROM sucursal s 
				LEFT JOIN zona z ON s.id_zona = z.id 
				WHERE s.id='".$id_sucursal."' 
			" );
		}
		if( $action == 'View' ){
			$rs = $db->Execute( "
				SELECT s.*, z.nombre zona 
				FROM sucursal s 
				LEFT JOIN zona z ON s.id_zona = z.id 
				LEFT JOIN ".$tabla." p ON s.id = p.id_sucursal
				WHERE p.id='".$id."'
			" );
		}
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2">Datos de la Sucursal</th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">Nombre:</td>
				<td width="490" class="cuadro1"><?=$reg->nombre?></td>
			</tr>
			<tr class="text">
				<td class="cuadro1izquierda">Contacto</td>
				<td class="cuadro1"><?=$reg->contacto?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Zona:</td>
				<td class="cuadro1"><?=$reg->zona?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Direcci&oacute;n:</td>
				<td class="cuadro1"><?=$reg->direccion?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Tel&eacute;fono:</td>
				<td class="cuadro1"><?=$reg->telefono?></td>
			</tr>
			<tr>
				<th class="titulosizquierda" colspan="2">Pedido</th>
			</tr>
			<tr>
				<td colspan="2" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="70" class="cuadro2izquierda">C&oacute;digo</td>
						<td class="cuadro2">Producto</td>
						<td class="cuadro2" width="55">Cantidad</td>
					</tr><?
		if( $action == 'Preview' ){
			if( is_array( $items ) ){
				foreach( $items as $id_producto => $cantidad ){
					$reg = $db->GetRow( "SELECT * FROM producto WHERE id='".$id_producto."'" );
					?><tr>
						<td class="cuadro1izquierda"><?=$reg['codigo']?></td>
						<td class="cuadro1"><?=$reg['nombre']?></td>
						<td class="cuadro1" align="right"><?=$cantidad?></td>
					</tr><?
				}
			}
		}
		if( $action == 'View' ){
			$rs = $db->Execute( "
				SELECT p.codigo, p.nombre, i.cantidad, e.nombre estado 
				FROM ".$tabla_item." i 
				LEFT JOIN ".$tabla." ped ON i.id_pedido = ped.id 
				LEFT JOIN producto p ON i.id_producto = p.id 
				LEFT JOIN estado_item e ON i.id_estado_item = e.id 
				WHERE ped.id = '".$id."'
			" );
			while( $reg = $rs->FetchNextObject( false ) ){
					?><tr>
						<td class="cuadro1izquierda"><?=$reg->codigo?></td>
						<td class="cuadro1"><?=$reg->nombre?> [<?=$reg->estado?>]</td>
						<td class="cuadro1" align="right"><?=$reg->cantidad?></td>
					</tr><?
			}
		}
				?></table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><?
		if( $action == 'Preview' ){
				?><input type="button" name="aprobar" value="Aprobar" 
				onClick="aprobar();">&nbsp;<input 
				type="button" name="cancelar" value="Cancelar" 
				onClick="window.close();"><?
		}
		if( $action == 'View' ){
				?><input 
				type="button" name="Volver" value="Volver" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'"><?
		}
				?></td>
			</tr>
		</table>
<?
	}
	
	if( $action == 'list_clientes' ){
		?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="list_clientes" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="3" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td class="cuadro1izquierda" colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
					<tr>
						<td width="120" class="text">CUIT:</td>
						<td><input name="cuit_list" type="text" id="cuit_list" value="<?=prepare_var( $cuit_list )?>" 
						style="width:100% " /></td>
						<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
					</tr>
					<tr>
						<td width="120" class="text">Cliente:</td>
						<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" 
						style="width:100% " /></td>
						<td width="100">&nbsp;</td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td colspan="3" class="sombra">&nbsp;</td>
			</tr>
			<tr>
				<td width="120" class="titulosizquierda">CUIT</td>
				<td width="490" class="titulos">Raz&oacute;n Social</td>
				<td width="30" class="titulos">&nbsp;</td>
			</tr><?
		$rs = $db->Execute( "
			SELECT * 
			FROM cliente 
			WHERE 1=1 AND ( fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' ) ".
				( $cuit_list != '' ? "AND cuit LIKE '".html_entity_decode( $cuit_list )."%' " : '' ).
				( $nombre_list ? " AND razonsocial LIKE '".html_entity_decode( $nombre_list )."%'" : '' ).
			" ORDER BY cuit
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->cuit?></td>
				<td class="<?=$class?>"><?=$reg->razonsocial?></td>
				<td class="<?=$class?>" align="center"><a href="javascript:seleccionar_cliente( '<?=$reg->cuit?>' );"><img 
				src="../sys_images/mas.gif" border="0" alt="Seleccione al Cliente"></a></td>
			</tr><?
		}
		?></table>
	</form><?
	}

	if( $action == 'list_sucursales' ){
		?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="list_sucursales" />
	<input type="hidden" name="id_cliente" id="id_cliente" value="<?=$id_cliente?>" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="3" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td class="cuadro1izquierda" colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
					<tr>
						<td width="120" class="text">Sucusal:</td>
						<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" 
						style="width:100% " /></td>
						<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
					</tr>
					<tr>
						<td class="text">Zona:</td>
						<td><?
					$rs = $db->Execute( "
						SELECT nombre, id 
						FROM zona 
						ORDER BY nombre
					" );
					echo $rs->GetMenu2(
						'zona_lst',
						$zona_lst,
						": -- Seleccione una zona -- ",
						false,
						1,
						'id="zona_lst" style="width: 100%"'
					);
						?></td>
						<td>&nbsp;</td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td colspan="3" class="sombra">&nbsp;</td>
			</tr>
			<tr>
				<td width="160" class="titulosizquierda">Zona</td>
				<td width="450" class="titulos">Raz&oacute;n Social &gt; Sucursal</td>
				<td width="30" class="titulos">&nbsp;</td>
			</tr><?
		$rs = $db->Execute( "
			SELECT s.id, s.nombre, s.fecha_baja, c.razonsocial cliente, z.nombre zona 
			FROM sucursal s 
			LEfT JOIN cliente c ON s.id_cliente = c.id 
			LEfT JOIN zona z ON s.id_zona = z.id 
			WHERE 1=1 AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) 
				AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' ) 
				AND s.nombre LIKE '".html_entity_decode( $nombre_list )."%' 
				AND s.id_cliente='".$id_cliente."'".(
				$zona_lst ? " AND s.id_zona='".$zona_lst."'" : "" 
			)." ORDER BY s.nombre
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->zona?></td>
				<td class="<?=$class?>"><?=$reg->cliente.' > '.$reg->nombre?></td>
				<td class="<?=$class?>" align="center"><a href="javascript:seleccionar_sucursal( '<?=$reg->nombre?>' );"><img 
				src="../sys_images/mas.gif" border="0" alt="Seleccione a la Sucursal"></a></td>
			</tr><?
		}
		?></table>
	</form><?
	}	

	if( $action == 'list_productos' ){
		?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="list_productos" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="4" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td class="cuadro1izquierda" colspan="4"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
					<tr>
						<td width="120" class="text">Código:</td>
						<td><input name="codigo_list" type="text" id="codigo_list" value="<?=prepare_var( $codigo_list )?>" 
						style="width:100% " /></td>
						<td width="100"><input name="submit" type="submit" value="Buscar" />	</td>
					</tr>
					<tr>
						<td width="120" class="text">Nombre:</td>
						<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" 
						style="width:100% " /></td>
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
				</table></td>
			</tr>
			<tr>
				<td colspan="4" class="sombra">&nbsp;</td>
			</tr>
			<tr>
				<td width="120" class="titulosizquierda">C&oacute;digo</td>
				<td width="245" class="titulos">Nombre</td>
				<td width="245" class="titulos">Rubro</td>
				<td width="30" class="titulos">&nbsp;</td>
			</tr><?
		$rs = $db->Execute( "
			SELECT p.*, r.nombre rubro
			FROM producto p
			LEFT JOIN rubro r ON r.id = p.id_rubro
			WHERE 1=1 
				AND ( p.fecha_baja is NULL OR p.fecha_baja = '0000-00-00 00:00:00' )
				AND p.nombre LIKE '".html_entity_decode( $nombre_list )."%' ".
				( $codigo_list != '' ? " AND p.codigo LIKE '".html_entity_decode( $codigo_list )."'" : '' ).
				( $estado_lst ? " AND p.id_estado_producto='".$estado_lst."'" : "" ).
				( $rubro_lst ? " AND p.id_rubro='".$rubro_lst."'" : "" ).
			" ORDER BY p.nombre
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td align="left" class="<?=$class?>izquierda"><?=$reg->codigo?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>"><?=printif($reg->rubro)?></td>
				<td class="<?=$class?>" align="center"><a href="javascript:seleccionar_producto( '<?=$reg->codigo?>' );"><img 
				src="../sys_images/mas.gif" border="0" alt="Seleccione al Producto"></a></td>
			</tr><?
		}
		?></table>
	</form><?
	}	
	
	if( $action == 'Ok' ){
		?><table width="640" border="0" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<th class="titulosizquierda" colspan="4" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td colspan="2" class="cuadro1izquierda" align="center">El pedido ha sido ingresado.</td>
			</tr>
			<tr>
				<td class="cuadro2izquierda" width="150">&nbsp;</td>
				<td class="cuadro2" width="490"><input type="button" value="Seguir cargando" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>?action=frmAgregar'">&nbsp;<input type="button" 
				value="Volver" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'"></td>
			</tr>
		</table><?
	}
?></body>
</html>