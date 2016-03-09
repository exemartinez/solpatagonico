<?
	include_once "../inc/conf.inc.php";
	require_once CFG_libPath."xajax/xajax.inc.php";
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_cliente");
	$xajax->registerFunction("bajar_sucursal");
	$xajax->registerFunction("seleccionar");	
	
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
					AND ( c.cuit LIKE '".html_entity_decode( $cod_cliente )."%' OR c.razonsocial LIKE '".html_entity_decode( $cod_cliente )."%' )
					AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) 
					AND i.id_estado_item = '5'
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
					AND s.nombre LIKE '".html_entity_decode( $cod_sucursal )."%' 
					AND s.id_cliente = '".$id_cliente."'
					AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' ) 
					AND i.id_estado_item = '5'
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
	
	function seleccionar( $datos ){
		global $db;
	
		$objResponse = new xajaxResponse("iso-8859-1");
		if( !validate::Date( fecha::normal2iso( $datos['fecha'] ) ) ){
			$objResponse->addScript( "alert( 'Debe ingresar una fecha valida' );" );
			$objResponse->addScript( "document.form1.fecha.value='';" );			
			$objResponse->addScript( "document.form1.fecha.focus();" );
		} elseif( $db->GetOne( "SELECT COUNT(*) FROM remito_factura WHERE fecha_factura > '".fecha::normal2iso( $datos['fecha'] )."'" ) ){
			$objResponse->addScript( "alert( 'Debe ingresar una fecha superior ya que existen facturas con fechas superiores a la ingresada' );" );
			$objResponse->addScript( "document.form1.fecha.value='';" );			
			$objResponse->addScript( "document.form1.fecha.focus();" );		
		} elseif( !validate::Integer($datos['nro_factura'] ) ){
			$objResponse->addScript( "alert( 'Debe ingresar un nro. de factura' );" );
			$objResponse->addScript( "document.form1.nro_factura.focus();" );				
		} elseif( $db->GetOne( "SELECT COUNT(*) FROM remito_factura WHERE nro_factura >= '".$datos['nro_factura']."'" ) ){
			$objResponse->addScript( "alert( 'Debe ingresar otro nro. de factura ya que el ingresado ya se encuentra registrado o es inferior a alguna de las ingresadas' );" );
			$objResponse->addScript( "document.form1.nro_factura.value='';" );			
			$objResponse->addScript( "document.form1.nro_factura.focus();" );
		} else {
			$objResponse->addScript( "document.form1.target = '_self';" );
			$objResponse->addScript( "document.getElementById( 'action' ).value = 'frmAgregar';" );
			$objResponse->addScript( "document.form1.submit();" );
		}
		return $objResponse;
	}
	
	# Variables de diseño
	$title		= 'Facturas';
	$name		= 'Factura';
	$titleForm	= 'Facturas';
	$msgCancel	= '¿Desea cancelar la carga de la facturas actual?\nSe perderan los datos ingresados';
	$msgDelete	= '¿Esta seguro que desea eliminar el registro?';
	# Variables de programación
	$tabla		= "remito";
	$campos		= "fecha_remito,dni_transportista,patente,id_cliente,nro_factura,id_sucursal";
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( $action == 'Agregar' ) {
		if ( !validate::Date( fecha::normal2iso( $fecha ) ) ) 
			$error .= "<li>Debe ingresar una fecha v&aacute;lida.</li>";
		if ( !validate::Integer($id_cliente) ) 
			$error .= "<li>Debe seleccionar un Cliente.</li>";
		if ( !validate::Integer($id_sucursal) ) 
			$error .= "<li>Debe seleccionar una Sucursal.</li>";			
		if ( !count($items) ) 
			$error .= "<li>Debe seleccionar al menos un remito a facturar.</li>";			
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		foreach( $items as $id_remito ){
			$ok = $db->Execute( "
				UPDATE 
				remito_factura 
				SET fecha_factura = '".fecha::normal2iso( $fecha )."', nro_factura = '".$nro_factura."'
				WHERE id='".$id_remito."'
			" );
			
			$ok = $db->Execute( "
				UPDATE 
				item_pedido 
				SET id_estado_item = 6
				WHERE id_remito='".$id_remito."' AND id_estado_item = '5'
			" );
		}
		$error = '';
		$action = 'Ok';
		$db->debug = false;		
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
				xajax_seleccionar( xajax.getFormValues( 'form1' ) );
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
			document.form1.target = '_blank';
			document.getElementById( 'action' ).value = 'Preview';
			document.form1.submit();
		}
		
		function imprimir_comprobante( redireccion ){
			window.print();
			if( confirm( '¿Se imprimieron correctamente los comprobantes?' ) ){
				location.href = redireccion;
			} else if( confirm( '¿Desea volver a imprimir los comprobantes?' ) ){
				imprimir_comprobante( redireccion );
			} else {
				location.href = redireccion;
			}
		}
	-->
	</script>
</head>

<body><?
	if ( in($action,'','buscar') ) {
		$paginas = new pager(
			"SELECT DISTINCT p.*, c.razonsocial, s.nombre sucursal
			FROM remito_factura p 
			LEFT JOIN item_pedido ip ON p.id = ip.id_remito 
			LEFT JOIN pedido ped ON ip.id_pedido = ped.id 
			LEFT JOIN cliente c ON ped.id_cliente = c.id 
			LEFT JOIN sucursal s ON c.id = s.id_cliente 
			WHERE p.nro_factura != '' 
			GROUP BY p.nro_factura 
			ORDER BY p.nro_factura DESC",
			$cur_page,
			20,
			25,
			"",
			''
		);	
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="660" cellpadding="3" cellspacing="0" border="0">
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
				<td width="90" class="titulosizquierda">Nro. de Factura</td>
				<td width="80" class="titulos">Fecha</td>
				<td width="230" class="titulos">Cliente</td>
				<td width="230" class="titulos">Sucursal</td>
				<td width="30" class="titulos">&nbsp;</td>
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
				<td align="left" class="<?=$class?>izquierda"><?=printif( $reg->nro_factura )?></td>
				<td align="left" class="<?=$class?>"><?=printif( fecha::iso2normal( $reg->fecha_factura ) )?></td>
				<td align="left" class="<?=$class?>"><?=$reg->razonsocial?></td>
				<td align="left" class="<?=$class?>"><?=$reg->sucursal?></td>
				<td align="center" class="<?=$class?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=View&id=<?=$reg->id?>"><img 
						src="../sys_images/search.gif" border="0" alt="Ver" /></a></td>
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
	
	if( $action == 'frmAgregar' ){
	?>
    <form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="Agregar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="2" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td width="150" class="cuadro1izquierda">Fecha</td>
				<td width="490" class="cuadro1"><?
			if( !$id_cliente ){
				?><input type="text" name="fecha" id="fecha" 
				value="<?=$fecha!=''?$fecha:date("d/m/Y")?>" validate="date:Ingrese una fecha válida" 
				onKeyUp="enter( 'seleccionar()' );" />
				<SCRIPT LANGUAGE="JavaScript" ID="js18">
					var cal_fecha = new CalendarPopup();
					cal_fecha.setCssPrefix("TEST");
					cal_fecha.setMonthNames( 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 
					'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
					cal_fecha.setDayHeaders( 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' );
					cal_fecha.setTodayText( 'Hoy' );
					var now = new Date(); 
					cal_fecha.setReturnFunction("setMultipleValues_fecha"); 
					function setMultipleValues_fecha(y,m,d) { 
						 document.getElementById('fecha').value=LZ(d)+'/'+LZ(m)+'/'+y; 
					}
				</SCRIPT><a
				onClick="cal_fecha.showCalendar('anchor_fecha'); return false;" 
				name="anchor_fecha" id="anchor_fecha" href="#" tabindex="-1" ><img 
				src="../sys_images/calendario.jpg" name="img_calendario" border="0" align="absmiddle"></a><?
			} else {
				?><input type="hidden" id="fecha" name="fecha" value="<?=$fecha?>"><?=$fecha?><?
			}
				?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Nro. Factura</td>
				<td class="cuadro1"><?
			if( !$id_cliente ){
				?><input type="text" name="nro_factura" id="nro_factura" value="<?=prepare_var( $nro_factura )?>" maxlength="40" 
				validate="str:Ingrese un Nro. de Factura" onKeyUp="enter( 'seleccionar()' );" /><?
			} else {
				?><input type="hidden" id="nro_factura" name="nro_factura" value="<?=$nro_factura?>"><?=$nro_factura?><?
			}
				?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Cliente</td>
				<td class="cuadro1"><?
			if( !$id_cliente ){
				?><input type="text" name="cod_cliente" id="cod_cliente" value="<?=prepare_var( $cod_cliente )?>" 
				onKeyUp="xajax_bajar_cliente( this.value ); enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: void open( 
				'<?=$_SERVER['PHP_SELF']?>?action=list_clientes', 'buscador', 
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
				'<?=$_SERVER['PHP_SELF']?>?action=list_sucursales&id_cliente='+document.getElementById('id_cliente').value, 'buscador', 
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
	if( $id_cliente && $id_sucursal && $fecha && nro_factura ){
			?><tr>
				<td colspan="2" class="cuadro1izquierda"><table width="100%" border="0" cellpadding="0" cellspacing="0" 
				id="tbl_items">
					<tr>
						<th width="25" class="titulosizquierda">&nbsp;</th>
						<th width="90" class="titulos" align="center">Nro. Remito</th>
						<th width="90" class="titulos">Fecha Remito</th>
						<th class="titulos">Total s/IVA</th>
					</tr><?
		$rs = $db->Execute( "
			SELECT r.id, r.fecha_remito, SUM( item_pedido.cantidad * lista_precio_vta.precio ) total
			FROM remito_factura r 
			LEFT JOIN item_pedido ON item_pedido.id_remito = r.id 
			LEFT JOIN pedido ON pedido.id = item_pedido.id_pedido 
			LEFT JOIN lista_precio_vta ON item_pedido.id_producto = lista_precio_vta.id_producto 
				AND ( lista_precio_vta.fecha_baja is NULL OR lista_precio_vta.fecha_baja = '0000-00-00 00:00:00' )
			WHERE pedido.id_cliente = '".$id_cliente."' AND item_pedido.id_estado_item = '5' 
			GROUP BY r.id
			ORDER BY r.id
		" );
		if( $rs->RecordCount() ){
			while( $reg = $rs->FetchNextObject( false ) ){
					?>
					<tr>
						<td class="cuadro1izquierda"><input type="checkbox" name="items[]" 
						value="<?=$reg->id?>"></td>
						<td class="cuadro1" align="center"><?=$reg->id?></td>
						<td align="right" class="cuadro1"><?=fecha::iso2normal( $reg->fecha_remito )?></td>
						<td align="right" class="cuadro1">$ <?=sprintf( "%01.2f", $reg->total )?></td>
					</tr>
					<?
			}
		} else {
					?>
					<tr>
						<td colspan="4" class="cuadro1izquierda" align="center"><span 
						class="error">No hay Remitos pendientes de Facturaci&oacute;n.</span></td>
					</tr>
					<?
		}
				?>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input name="preview_pedido" type="button" value="Ver Factura" onClick="preview()" 
				/>&nbsp;<input name="cancelar" type="button" value="Cancelar" 
				onClick="if( confirm( '<?=$msgCancel?>' ) ){ location = '<?=$_SERVER['PHP_SELF']?>'; }" /></td>
			</tr>
			<?
	}
	?></table>
	<br>
	<a name="Bottom"></a>
</form><?
	}
	
	if( in( $action, 'Preview', 'View' ) ){
		if( $action == 'View' ){
			$id_cliente = $db->GetOne( "
				SELECT p.id_cliente
				FROM remito_factura r 
				LEFT JOIN item_pedido i ON r.id = i.id_remito 
				LEFT JOIN pedido p ON i.id_pedido = p.id 
				WHERE r.id = '".$id."'
			" );
			$id_sucursal = $db->GetOne( "
				SELECT p.id_sucursal
				FROM remito_factura r 
				LEFT JOIN item_pedido i ON r.id = i.id_remito 
				LEFT JOIN pedido p ON i.id_pedido = p.id 
				WHERE r.id = '".$id."'
			" );
			$nro_factura = $db->GetOne( "
				SELECT nro_factura
				FROM remito_factura
				WHERE id = '".$id."'
			" );
			$items = array();
			$rs = $db->Execute( "SELECT * FROM remito_factura WHERE nro_factura = '".$nro_factura."'" );
			while( $aux = $rs->FetchNextObject( false ) ){
				$items[] = $aux->id;
			}
		}
		?><table align="center" width="800" cellpadding="3" cellspacing="0" border="0"><?
		$rs = $db->Execute( "SELECT * FROM cliente WHERE id='".$id_cliente."'" );
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2">Datos del Cliente</th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">CUIT:</td>
				<td width="650" class="cuadro1"><?=$reg->cuit?></td>
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
				<th class="titulosizquierda" colspan="2">Pedido</th>
			</tr>
			<tr>
				<td colspan="2" class="cuadro1izquierda"><table width="100%" border="0" cellpadding="0" cellspacing="0" 
				id="tbl_items2">
					<tr>
						<td width="80" class="cuadro2izquierda">Cantidad</td>
						<td width="80" class="cuadro2">Codigo</td>
						<td class="cuadro2">Producto</td>
						<td width="80" class="cuadro2">Precio</td>
						<td width="80" class="cuadro2">Al&iacute;cuota IVA</td>
						<td width="80" class="cuadro2">SubTotal</td>
					</tr>
					<?
		$rs = $db->Execute( "
			SELECT r.id nro_remito, SUM( i.cantidad ) cantidad, p.codigo, p.nombre producto, l.precio, p.iva
			FROM item_pedido i 
			LEFT JOIN producto p ON i.id_producto = p.id 
			LEFT JOIN lista_precio_vta l ON i.id_producto = l.id_producto 
				AND ( l.fecha_baja is NULL OR l.fecha_baja = '0000-00-00 00:00:00' )
			LEFT JOIN pedido ON i.id_pedido = pedido.id 
			LEFT JOIN remito_factura r ON i.id_remito = r.id 
			WHERE r.id IN (".( count( $items ) ? implode( ',', $items ) : '0' ).")
			GROUP BY i.id_producto 
			ORDER BY r.id, p.codigo, p.nombre 
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
					?><tr>
						<td align="right" class="cuadro1izquierda"><?=$reg->cantidad?></td>
						<td align="center" class="cuadro1"><?=$reg->codigo?></td>
						<td class="cuadro1"><?=$reg->producto?></td>
						<td align="right" class="cuadro1">$ <?=$reg->precio?></td>
						<td align="right" class="cuadro1"><?=$reg->iva?>%</td>
						<td align="right" class="cuadro1">$ <?=sprintf( "%01.2f", $reg->precio * $reg->cantidad )?></td>
					</tr>
				<?
		}
				?></table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><?
		if( $action == 'Preview' ){
				?><input type="button" name="aprobar" value="Aprobar" 
				onClick="opener.document.getElementById( 'form1' ).target = '_self';opener.document.getElementById( 'action' ).value = 'Agregar';opener.document.getElementById( 'form1' ).submit();window.close();">&nbsp;<input 
				type="button" name="cancelar" value="Cancelar" 
				onClick="window.close();"><?
		} else {
				?><input type="button" name="cancelar" value="Volver" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">&nbsp;<input type="button" 
				value="Visualizar Factura <?=$nro_factura?>" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>?action=Print&nro_factura=<?=$nro_factura?>&id_cliente=<?=$id_cliente?>&id_sucursal=<?=$id_sucursal?>'"><?
		}
		?></td>
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
				AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) 
				AND i.id_estado_item = '5'".
				( $cuit_list != '' ? " AND c.cuit LIKE '%".html_entity_decode( $cuit_list )."%' " : '' ).
				( $nombre_list ? " AND c.razonsocial LIKE '%".html_entity_decode( $nombre_list )."%'" : '' ).
			" ORDER BY c.cuit 
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
			SELECT DISTINCT s.*, c.razonsocial cliente, z.nombre zona 
			FROM item_pedido i 
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN sucursal s ON p.id_sucursal = s.id 
			LEFT JOIN cliente c ON s.id_cliente = c.id 
			LEFT JOIN zona z ON s.id_zona = z.id
			WHERE 1=1 
				AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' )
				AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' )".
				( $nombre_list != '' ? " AND s.nombre LIKE '%".html_entity_decode( $nombre_list )."%'" : "" )."
				AND s.id_cliente = '".$id_cliente."' 
				AND i.id_estado_item = '5'".
				( $zona_lst ? " AND s.id_zona='".$zona_lst."'" : "" )." 
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
				<td colspan="2" class="cuadro1izquierda" align="center">La factura ha sido ingresada.</td>
			</tr>
			<tr>
				<td class="cuadro2izquierda" width="150">&nbsp;</td>
				<td class="cuadro2" width="490"><input type="button" value="Seguir facturando" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">&nbsp;<input type="button" 
				value="Visualizar Factura <?=$nro_factura?>" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>?action=Print&nro_factura=<?=$nro_factura?>&id_cliente=<?=$id_cliente?>&id_sucursal=<?=$id_sucursal?>'"></td>
			</tr>
		</table><?
	}
	
	if( $action == 'Print' ){
		//echo "FUNCIONA ".$id_cliente;
		?><style media="print">
		<!--
			#botonera {
				display: none;
			}
		-->
		</style><table align="center" width="800" cellpadding="3" cellspacing="0" border="0" class="text"><?
		$rs = $db->Execute( "SELECT * FROM cliente WHERE id='".$id_cliente."'" );
		$reg = $rs->FetchObject( false );
			?>
			<tr>
				<td><strong>Raz&oacute;n Social:</strong></td>
				<td colspan="3"><?=$reg->razonsocial?></td>
			</tr>
			<tr>
				<td width="120"><strong>Direcci&oacute;n:</strong></td>
				<td width="436"><?=$reg->direccion?></td>
				<td width="80"><strong>Tel&eacute;fono:</strong></td>
				<td width="140"><?=$reg->telefono?></td>
			</tr>
			<tr>
				<td><strong>Ingresos Brutos:</strong></td>
				<td><?=$reg->nroiibb?></td>
				<td><strong>CUIT:</strong></td>
				<td><?=$reg->cuit?></td>
			</tr><?
		$rs = $db->Execute( "
			SELECT s.*, z.nombre zona 
			FROM sucursal s 
			LEFT JOIN zona z ON s.id_zona = z.id 
			WHERE s.id='".$id_sucursal."' 
		" );
		$reg = $rs->FetchObject( false );
			?>
			<tr>
				<td><strong>Sucursal:</strong></td>
				<td><?=$reg->nombre?></td>
				<td><strong>Zona:</strong></td>
				<td><?=$reg->zona?></td>
			</tr>
			<tr>
				<td><strong>Direcci&oacute;n:</strong></td>
				<td><?=$reg->direccion?></td>
				<td><strong>Tel&eacute;fono:</strong></td>
				<td><?=$reg->telefono?></td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			
			<tr>
				<td colspan="4"><table width="100%" border="0" cellpadding="3" cellspacing="0" 
				id="tbl_items2" class="text">
					<tr>
						<td width="90" align="center"><strong>Remito</strong></td>
						<td width="90" align="center"><strong>Fecha Remito</strong></td>
						<td align="right"><strong>SubTotal</strong></td>
					</tr><?
		$count = 0;
		$subtotal = 0;
		$iva = 0;
		$rs = $db->Execute( "
			SELECT r.id, r.fecha_remito, SUM( i.cantidad * l.precio ) subtotal, SUM( ( i.cantidad * l.precio * p.iva ) / 100 ) iva
			FROM item_pedido i 
			LEFT JOIN producto p ON i.id_producto = p.id 
			LEFT JOIN lista_precio_vta l ON i.id_producto = l.id_producto 
				AND ( l.fecha_baja is NULL OR l.fecha_baja = '0000-00-00 00:00:00' )
			LEFT JOIN pedido ON i.id_pedido = pedido.id 
			LEFT JOIN remito_factura r ON i.id_remito = r.id 
			WHERE r.nro_factura = '".$nro_factura."'
			GROUP BY i.id_producto 
			ORDER BY r.id, p.codigo, p.nombre 
			LIMIT 0,10
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$subtotal += $reg->subtotal;
			$iva += $reg->iva;
			
					?><tr>
						<td align="right"><?=$reg->id?></td>
						<td align="center"><?=fecha::iso2normal( $reg->fecha_remito )?></td>
						<td align="right">$ <?=sprintf( "%01.2f", $reg->subtotal )?></td>
					</tr><?
			$count++;
		}
		for( $i = $count; $i < 10; $i++ ){
					?><tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr><?
		}
					?><tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td align="center"><strong>Subtotal</strong></td>
						<td align="center"><strong>IVA</strong></td>
						<td align="right"><strong>Total</strong></td>
					</tr>
					<tr>
						<td align="right">$ <?=sprintf( "%01.2f", $subtotal );?></td>
						<td align="right">$ <?=sprintf( "%01.2f", $iva );?></td>
						<td align="right">$ <?=sprintf( "%01.2f", $subtotal + $iva );?></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr id="botonera">
				<td colspan="4"><!--<input type="button" value="Imprimir" 
				onClick="imprimir_comprobante( '<?=$_SERVER['PHP_SELF']?>' )">&nbsp;--><input type="button" value="Cancelar Visualización" onClick="location.href='<?=$_SERVER['PHP_SELF']?>';"></td>
			</tr>
		</table><?
	}	
?></body>
</html>