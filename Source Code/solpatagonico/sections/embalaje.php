<?
	include_once '../inc/conf.inc.php';
	require_once CFG_libPath.'xajax/xajax.inc.php';
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_cliente");
	$xajax->registerFunction("bajar_sucursal");	
	
	$xajax->processRequests();
	
	function bajar_cliente( $cod_cliente ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_cliente' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'id_cliente' ).value = '';" );
		if( $cod_cliente ){
			$rs = $db->Execute( "
				SELECT DISTINCT c.* 
				FROM item_pedido i 
				LEFT JOIN pedido p ON i.id_pedido = p.id 
				LEFT JOIN cliente c ON p.id_cliente = c.id 
				WHERE 1=1
					AND ( c.cuit LIKE '".$cod_cliente."%' OR c.razonsocial LIKE '".html_entity_decode( $cod_cliente )."%' ) 
					AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) 
					AND i.id_estado_item = '3'
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
				SELECT DISTINCT s.*, z.nombre zona 
				FROM item_pedido i 
				LEFT JOIN pedido p ON i.id_pedido = p.id 
				LEFT JOIN sucursal s ON p.id_sucursal = s.id 
				LEFT JOIN zona z ON s.id_zona = z.id 
				WHERE 1=1 
					AND s.id_cliente = '".$id_cliente."' 
					AND ( s.nombre LIKE '".html_entity_decode( $cod_sucursal )."%' ) 
					AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' ) 
					AND i.id_estado_item = '3'
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_sucursal' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'id_sucursal' ).value = '".$reg->id."';" );			
				$objResponse->addScript( "document.getElementById( 'td_nombre' ).innerHTML = '".$reg->nombre."';" );
				$objResponse->addScript( "document.getElementById( 'td_zona' ).innerHTML = '".$reg->zona."';" );
				$objResponse->addScript( "document.getElementById( 'td_direccion_suc' ).innerHTML = '".$reg->direccion."';" );
				$objResponse->addScript( "document.getElementById( 'td_telefono_suc' ).innerHTML = '".$reg->telefono."';" );
			}
		}
		return $objResponse;
	}
	
	# Variables de diseño
	$title		= 'Embalajes';
	$name		= 'Embalaje';
	$titleForm	= 'Embalajes';
	$msgCancel	= '¿Desea cancelar la carga del embalaje actual?\nSe perderan los datos ingresados';	
	# Variables de programación
	$tabla		= "remito_factura";
	$tabla_item	= "item_pedido";
	$campos		= "";	
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( $action == 'Agregar' ) {
		if ( !count($items) ) 
			$error .= "<li>Debe seleccionar al menos un producto a embalar.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		foreach( $items as $id_producto => $cantidad ){
			$ok = $db->Execute( "
				UPDATE ".$tabla_item." i  
				LEFT JOIN pedido ON i.id_pedido = pedido.id
				SET i.id_estado_item = '4' 
				WHERE i.id_producto='".$id_producto."' AND pedido.id_cliente = '".$id_cliente."' 
					AND pedido.id_sucursal = '".$id_sucursal."' AND i.id_estado_item = '3' 
			" );		
			$ok = $db->Execute( "
				UPDATE producto 
				SET 
					cantidad_real= cantidad_real - ".$cantidad.", 
					cantidad_reserva = cantidad_reserva - ".$cantidad." 
				WHERE id='".$id_producto."'
			" );			
		}
		$error = '';
		$action = 'Ok';
		
		# Ejecutando tarea de actualizacion de pedido.
		ob_start();
		ob_implicit_flush(0);
			include( 'tarea_actualizar_pedido.php' );
			$html = ob_get_contents();
		ob_end_clean();
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
				document.getElementById( 'action' ).value = '';
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
		
		function enter( funcion ){
			if( event.keyCode == 13 ){
				eval( funcion );
			}
		}	
		
		function preview(){
			if( document.getElementById( 'tbl_items' ).rows.length == 1 ){
				alert( 'Debe seleccionar al menos 1 producto' );
				location.href = '#Bottom';
			} else {
				document.form1.target = '_blank';
				document.getElementById( 'action' ).value = 'Preview';
				document.form1.submit();
			}
		}		
	-->
	</script>
</head>

<body><?
	if( $action == '' ){
	?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="Agregar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="2" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">Cliente</td>
				<td width="490" class="cuadro1"><?
			if( !$id_cliente ){
				?><input type="text" name="cod_cliente" id="cod_cliente" value="<?=prepare_var( $cod_cliente )?>" 
				onKeyUp="xajax_bajar_cliente( this.value ); enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: void open( 
				'embalaje.php?action=list_clientes', 'buscador', 
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
				'embalaje.php?action=list_sucursales&id_cliente='+document.getElementById('id_cliente').value, 'buscador', 
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
				<td colspan="2" class="cuadro1izquierda"><table width="100%" border="0" cellpadding="0" cellspacing="0" 
				id="tbl_items">
					<tr>
						<th width="25" class="titulosizquierda">&nbsp;</th>
						<th width="70" class="titulos" align="center">C&oacute;digo</th>
						<th class="titulos">Producto</th>
						<th width="55" class="titulos">Cantidad</th>						
					</tr><?
		$rs = $db->Execute( "
			SELECT p.id, p.codigo, p.nombre, SUM( i.cantidad ) cantidad 
			FROM item_pedido i 
			LEFT JOIN producto p ON i.id_producto = p.id 
			LEFT JOIN pedido ON i.id_pedido = pedido.id 
			LEFT JOIN sucursal s ON pedido.id_sucursal = s.id 
			LEFT JOIN zona z ON s.id_zona = z.id 
			WHERE pedido.id_cliente = '".$id_cliente."' 
				AND pedido.id_sucursal = '".$id_sucursal."' 
				AND i.id_estado_item = '3' 
			GROUP BY i.id_producto 
			ORDER BY s.nombre, z.nombre
		" );
		if( $rs->RecordCount() ){
			while( $reg = $rs->FetchNextObject( false ) ){
					?><tr>
						<td width="25" class="cuadro1izquierda"><input type="checkbox" name="items[<?=$reg->id?>]" 
						value="<?=$reg->cantidad?>"></td>
						<td width="70" class="cuadro1" align="center"><?=$reg->codigo?></td>
						<td class="cuadro1"><?=$reg->nombre?></td>
						<td width="55" class="cuadro1"><?=$reg->cantidad?></td>						
					</tr><?
			}
		} else {
					?><tr>
						<td colspan="4" class="cuadro1izquierda" align="center"><span 
						class="error">Sin pedidos pendientes.</span></td>
					</tr><?
		}
				?></table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input name="preview_pedido" type="button" value="Ver Embalaje" onClick="preview()" 
				/>&nbsp;<input name="cancelar" type="button" value="Cancelar" 
				onClick="if( confirm( '<?=$msgCancel?>' ) ){ location = '<?=$_SERVER['PHP_SELF']?>'; }" /></td>
			</tr><?
	}
	?></table>
	<br>
	<a name="Bottom"></a>
</form><?
	}
	
	if( $action == 'Preview' ){
		?><table align="center" width="640" cellpadding="3" cellspacing="0" border="0"><?
		$rs = $db->Execute( "SELECT * FROM cliente WHERE id='".$id_cliente."'" );
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2" style="font-size: 14px; font-weight: bold">Datos del Cliente</th>
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
		$rs = $db->Execute( "
			SELECT s.*, z.nombre zona 
			FROM sucursal s 
			LEFT JOIN zona z ON s.id_zona = z.id 
			WHERE s.id='".$id_sucursal."' 
		" );
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2">Datos de la Sucursal</th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">Nombre:</td>
				<td width="490" class="cuadro1"><?=$reg->nombre?></td>
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
				<th class="titulosizquierda" colspan="2">Productos</th>
			</tr>
			<tr>
				<td colspan="2" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="70" class="cuadro2izquierda">C&oacute;digo</td>
						<td class="cuadro2">Producto</td>
						<td class="cuadro2" width="55">Cantidad</td>
					</tr><?
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
				?></table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input type="button" name="aprobar" value="Aprobar" 
				onClick="opener.document.getElementById( 'form1' ).target = '_self';opener.document.getElementById( 'action' ).value = 'Agregar';opener.document.getElementById( 'form1' ).submit();window.close()">&nbsp;<input 
				type="button" name="cancelar" value="Cancelar" 
				onClick="window.close();"></td>
			</tr>
		</table><?
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
			SELECT DISTINCT c.* 
			FROM item_pedido i 
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN cliente c ON p.id_cliente = c.id 
			WHERE 1=1 
				AND i.id_estado_item = '3' 
				AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) ".
				( $cuit_list != '' ? "AND c.cuit LIKE '%".html_entity_decode( $cuit_list )."%' " : '' ).
				( $nombre_list ? " AND c.razonsocial LIKE '%".html_entity_decode( $nombre_list )."%'" : '' ).
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
			SELECT DISTINCT s.*, z.nombre zona, c.razonsocial cliente 
			FROM item_pedido i 
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN sucursal s ON p.id_sucursal = s.id 
			LEFT JOIN cliente c ON s.id_cliente = c.id 
			LEFT JOIN zona z ON s.id_zona = z.id 
			WHERE 1=1 
				AND s.id_cliente = '".$id_cliente."'".(
				$nombre_list != '' ? " AND ( s.nombre LIKE '".html_entity_decode( $nombre_list )."%' )" : ""
			).(
				$zona_lst ? " AND s.id_zona = '".$zona_lst."'" : "" 
			)."
				AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' ) 
				AND i.id_estado_item = '3' 
			ORDER BY s.nombre
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

	if( $action == 'Ok' ){
		?><table width="640" border="0" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<th class="titulosizquierda" colspan="4" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td colspan="2" class="cuadro1izquierda" align="center">El embalaje ha sido registrado.</td>
			</tr>
			<tr>
				<td class="cuadro2izquierda" width="150">&nbsp;</td>
				<td class="cuadro2" width="490"><input type="button" value="Seguir embalando" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'"></td>
			</tr>
		</table><?
	}
?></body>
</html>