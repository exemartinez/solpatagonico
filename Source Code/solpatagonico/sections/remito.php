<?
	include_once '../inc/conf.inc.php';
	require_once CFG_libPath.'xajax/xajax.inc.php';
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_camionero");
	$xajax->registerFunction("bajar_transporte");	
	$xajax->registerFunction("bajar_cliente");
	$xajax->registerFunction("bajar_sucursal");
	
	$xajax->processRequests();
	
	function bajar_camionero( $cod_camionero ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_camionero' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'dni_transportista' ).value = '';" );
		if( $cod_camionero ){
			$rs = $db->Execute( "
				SELECT DISTINCT u.* 
				FROM sys_users_tipo_transporte tt 
				LEFT JOIN sys_users u ON tt.id_usuario = u.id
				WHERE u.nombre LIKE '".html_entity_decode( $cod_camionero )."%' OR u.dni LIKE '".html_entity_decode( $cod_camionero )."%' 
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_camionero' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'dni_transportista' ).value = '".$reg->dni."';" );			
				$objResponse->addScript( "document.getElementById( 'td_nombreapellido' ).innerHTML = '".$reg->nombre."';" );
				$objResponse->addScript( "document.getElementById( 'td_dni' ).innerHTML = '".$reg->dni."';" );
			}
		}
		return $objResponse;
	}
	
	function bajar_transporte( $cod_transporte, $dni_transportista ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_transporte' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'patente' ).value = '';" );
		if( $cod_transporte && $dni_transportista ){
			$rs = $db->Execute( "
				SELECT t.*
				FROM transporte t 
				LEFT JOIN sys_users_tipo_transporte ut ON ut.id_tipo = t.id_tipo 
				LEFT JOIN sys_users u ON u.id = ut.id_usuario  
				WHERE 1=1
					AND ( t.nombre LIKE '".html_entity_decode( $cod_transporte )."%' OR t.patente LIKE '".html_entity_decode( $cod_transporte )."%' ) 
					AND u.dni = '".$dni_transportista."'
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_transporte' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'patente' ).value = '".$reg->patente."';" );			
				$objResponse->addScript( "document.getElementById( 'td_camion' ).innerHTML = '".$reg->nombre."';" );
				$objResponse->addScript( "document.getElementById( 'td_patente' ).innerHTML = '".$reg->patente."';" );
				$objResponse->addScript( "document.getElementById( 'td_capacidad' ).innerHTML = '".$reg->capacidad."';" );

			}
		}
		return $objResponse;
	}
	
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
					AND i.id_estado_item = '4' 
					AND ( i.id_remito is NULL OR i.id_remito = 0 ) 
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
					AND s.nombre LIKE '".html_entity_decode( $cod_sucursal )."%'
					AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' ) 
					AND i.id_estado_item = '4' 
					AND ( i.id_remito is NULL OR i.id_remito = 0 )
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
	$title		= 'Remitos';
	$name		= 'Remito';
	$titleForm	= 'Remitos';
	$msgCancel	= '¿Desea cancelar la carga del remito actual?\nSe perderan los datos ingresados';	
	# Variables de programación
	$tabla		= "remito_factura";
	$tabla_item	= "item_pedido";
	$campos		= "fecha_remito,dni_transportista,patente,id_cliente,id_sucursal";	
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( $action == 'Agregar' ) {
		if ( !count($items) ) 
			$error .= "<li>Debe seleccionar al menos un producto a ingresar.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		$record['fecha_remito'] = date( "Y-m-d H:i:s" );

		$ok = $db->Execute( get_sql( 'INSERT', $tabla, $record, '' ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$id_remito = mysql_insert_id();

			foreach( $items as $id_producto => $cantidad ){

				$ok = $db->Execute( "
					UPDATE ".$tabla_item." i  
					LEFT JOIN pedido ON i.id_pedido = pedido.id 
					SET i.id_remito='".$id_remito."' 
					WHERE i.id_producto='".$id_producto."' AND pedido.id_cliente = '".$id_cliente."' 
						AND pedido.id_sucursal = '".$id_sucursal."' AND i.id_estado_item = '4' 
						AND ( i.id_remito is NULL OR i.id_remito = 0 )
				" );	
			}
			$error = '';
			$action = 'Ok';
		}
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
			} else if( document.getElementById('dni_transportista').value == '' ){
				alert( 'Debe seleccionar un Transportista' );
				document.getElementById('cod_camionero').focus();
			} else if( document.getElementById('patente').value == '' ){
				alert( 'Debe seleccionar un Transporte' );
				document.getElementById('cod_transporte').focus();
			} else {
				document.form1.target = '_self';
				document.getElementById( 'action' ).value = 'frmAgregar';
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
		
		function seleccionar_transporte( id ){
			var elem = opener.document.getElementById( 'cod_transporte' )
			
			elem.value = id;			
			try { elem.onkeyup() } catch ( all ) { }
			self.close();
		}		
		
		function seleccionar_camionero( id ){
			var elem = opener.document.getElementById( 'cod_camionero' )
			
			elem.value = id;			
			try { elem.onkeyup() } catch ( all ) { }
			seleccionar_transporte( '' );
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
		$paginas = new pager(
			"SELECT p.*, c.razonsocial, s.nombre sucursal
			FROM remito_factura p 
			LEFT JOIN item_pedido ip ON p.id = ip.id_remito 
			LEFT JOIN pedido ped ON ip.id_pedido = ped.id 
			LEFT JOIN cliente c ON ped.id_cliente = c.id 
			LEFT JOIN sucursal s ON c.id = s.id_cliente 
			GROUP BY p.id
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
				<td width="90" class="titulosizquierda">Nro. de Remito</td>
                <td width="90" class="titulos">Nro. Factura</td>
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
				<td align="left" class="<?=$class?>izquierda"><?=printif( $reg->id )?></td>
    			<td align="left" class="<?=$class?>izquierda"><?=printif( $reg->nro_factura )?></td>
				<td align="left" class="<?=$class?>"><?=printif( fecha::iso2normal( $reg->fecha_remito ) )?></td>
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
	}
	
	if( $action == 'frmAgregar' ){
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
				<td width="150" class="cuadro1izquierda">Transportista</td>
				<td width="490" class="cuadro1"><?
			if( !$dni_transportista ){
				?><input type="text" name="cod_camionero" id="cod_camionero" value="<?=$cod_camionero?>" 
				onKeyUp="xajax_bajar_camionero( this.value ); enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: void open( 
				'remito.php?action=list_camioneros', 'buscador', 
				'popup width=660 height=500 scrollbars=yes resizable=yes')" 
				target="main" tabindex="-1"><img src="../sys_images/search.gif" border="0" align="absmiddle" 
				alt="Buscar Transportistas"></a><?
			} else {
				?>&nbsp;<?
			}
				?></td>
			</tr><?
			if( $dni_transportista ){
				$rs = $db->Execute( "SELECT * FROM sys_users WHERE dni='".$dni_transportista."'" );
				$reg = $rs->FetchObject( false );
			}
			?><tr id="tr_camionero" style="display: <?=( $dni_transportista ? 'block' : 'none' )?>;">
				<td class="cuadro1izquierda">&nbsp;<input type="hidden" name="dni_transportista" id="dni_transportista" 
				value="<?=$dni_transportista?>"></td>
				<td class="cuadro1"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
					<tr>
						<th class="titulosizquierda" colspan="2">Datos del Transportista</th>
					</tr>
					<tr>
						<td width="100" class="cuadro1izquierda">Nombre y Apellido:</td>
						<td id="td_nombreapellido" class="cuadro1"><?=$reg->nombre?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">DNI:</td>
						<td id="td_dni" class="cuadro1"><?=$reg->dni?></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Transporte</td>
				<td class="cuadro1"><?
			if( !$patente ){
				?><input type="text" name="cod_transporte" id="cod_transporte" value="<?=prepare_var( $cod_transporte )?>" 
				onKeyUp="xajax_bajar_transporte( this.value, document.getElementById( 'dni_transportista' ).value ); 
				enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: if( document.getElementById('dni_transportista').value != '' ){ void open( 
				'remito.php?action=list_transportes&dni_transportista='+document.getElementById('dni_transportista').value, 'buscador', 
				'popup width=660 height=450 scrollbars=yes resizable=yes') } else { alert( 'Debe seleccionar un transportista primero.' ); }" 
				target="main" tabindex="-1"><img src="../sys_images/search.gif" border="0" align="absmiddle" 
				alt="Buscar Transportes"></a><?
			} else {
				?>&nbsp;<?
			}
				?></td>
			</tr><?
			if( $patente ){
				$rs = $db->Execute( "
					SELECT * 
					FROM transporte
					WHERE patente='".$patente."'
				" );
				$reg = $rs->FetchObject( false );
			}
			?><tr id="tr_transporte" style="display: <?=( $patente ? 'block' : 'none' )?>;">
				<td class="cuadro1izquierda">&nbsp;<input type="hidden" name="patente" id="patente" 
				value="<?=$patente?>"></td>
				<td class="cuadro1"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
					<tr>
						<th class="titulosizquierda" colspan="2">Datos de la Transporte</th>
					</tr>
					<tr>
						<td width="100" class="cuadro1izquierda">Cami&oacute;n:</td>
						<td id="td_camion" class="cuadro1"><?=$reg->nombre?></td>
					</tr>
					<tr>
						<td width="100" class="cuadro1izquierda">Patente:</td>
						<td id="td_patente" class="cuadro1"><?=$reg->patente?></td>
					</tr>					
					<tr>
						<td width="100" class="cuadro1izquierda">Capacidad:</td>
						<td id="td_capacidad" class="cuadro1"><?=$reg->capacidad?></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input name="seleccionar_remito" type="button" value="Seleccionar" 
				onClick="seleccionar()" <?=( $dni_transportista && $patente ? 'style="display: none"' : '' )?>/>&nbsp;<input 
				name="cancelar" type="button" value="Cancelar" 
				onClick="if( confirm( '<?=$msgCancel?>' ) ){ location = '<?=$_SERVER['PHP_SELF']?>'; }" /></td>
			</tr><?
	if( $dni_transportista && $patente ){
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
			SELECT p.id, p.codigo, p.nombre, SUM( i.cantidad ) cantidad, pedido.id_sucursal, pedido.id id_pedido 
			FROM item_pedido i 
			LEFT JOIN producto p ON i.id_producto = p.id 
			LEFT JOIN pedido ON i.id_pedido = pedido.id 
			LEFT JOIN sucursal s ON pedido.id_sucursal = s.id 
			LEFT JOIN zona z ON s.id_zona = z.id 
			WHERE pedido.id_cliente = '".$id_cliente."' AND pedido.id_sucursal = '".$id_sucursal."' 
				AND i.id_estado_item = '4' AND ( i.id_remito is NULL OR i.id_remito = 0 ) 
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
				<td class="cuadro2"><input name="preview_pedido" type="button" value="Ver Remito" onClick="preview()" 
				/>&nbsp;<input name="cancelar" type="button" value="Cancelar" 
				onClick="if( confirm( '<?=$msgCancel?>' ) ){ location = '<?=$_SERVER['PHP_SELF']?>'; }" /></td>
			</tr><?
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
			$dni_transportista = $db->GetOne( "
				SELECT dni_transportista
				FROM remito_factura
				WHERE id = '".$id."'
			" );
			$patente = $db->GetOne( "
				SELECT patente
				FROM remito_factura
				WHERE id = '".$id."'
			" );
			$nro_factura = $db->GetOne( "
				SELECT nro_factura
				FROM remito_factura
				WHERE id = '".$id."'
			" );
			$items = array();
			$rs = $db->Execute( "SELECT * FROM item_pedido WHERE id_remito = '".$id."'" );
			while( $aux = $rs->FetchNextObject( false ) ){
				$items[$aux->id_producto] = $aux->cantidad;
			}
		}
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
			</tr><?
		$rs = $db->Execute( "SELECT * FROM sys_users WHERE dni='".$dni_transportista."'" );
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2">Datos del Transportista</th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">Nombre:</td>
				<td width="490" class="cuadro1"><?=$reg->nombre?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">DNI:</td>
				<td class="cuadro1"><?=$reg->dni?></td>
			</tr>
			</tr><?
		$rs = $db->Execute( "
			SELECT * 
			FROM transporte 
			WHERE patente='".$patente."' 
		" );
		$reg = $rs->FetchObject( false );
			?><tr>
				<th class="titulosizquierda" colspan="2">Datos del Transporte</th>
			</tr>
			<tr>
				<td width="150" class="cuadro1izquierda">Cami&oacute;n:</td>
				<td width="490" class="cuadro1"><?=$reg->nombre?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Patente:</td>
				<td class="cuadro1"><?=$reg->patente?></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Capacidad:</td>
				<td class="cuadro1"><?=$reg->capacidad?></td>
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
				<td class="cuadro2"><?
		if( $action == 'Preview' ){
				?><input type="button" name="aprobar" value="Aprobar" 
				onClick="opener.document.getElementById( 'form1' ).target = '_self';opener.document.getElementById( 'action' ).value = 'Agregar';opener.document.getElementById( 'form1' ).submit();window.close()">&nbsp;<input 
				type="button" name="cancelar" value="Cancelar" 
				onClick="window.close();"><?
		} else {
				?><input type="button" name="cancelar" value="Volver" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">&nbsp;<input type="button" 
				value="Visualizar Remito <?=$id?>" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>?action=Print&id=<?=$id?>&id_cliente=<?=$id_cliente?>&id_sucursal=<?=$id_sucursal?>'"><?
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
			WHERE 1=1".
				( $cuit_list != '' ? " AND c.cuit LIKE '".html_entity_decode($cuit_list)."%' " : "" ).
				( $nombre_list!='' ?  " AND c.razonsocial LIKE '".html_entity_decode($nombre_list)."%'" : "" ).
				" AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) 
				AND i.id_estado_item = '4' 
				AND ( i.id_remito is NULL OR i.id_remito = 0 ) 
			ORDER BY c.cuit
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
		//} catch ( all ) { }
		
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
				AND i.id_estado_item = '4' 
				AND ( i.id_remito is NULL OR i.id_remito = 0 ) 
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
	
	if( $action == 'list_camioneros' ){
		?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="list_camioneros" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="3" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td class="cuadro1izquierda" colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
					<tr>
						<td width="120" class="text">DNI:</td>
						<td><input name="dni_list" type="text" id="cuit_list" value="<?=prepare_var( $dni_list )?>" 
						style="width:100% " /></td>
						<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
					</tr>
					<tr>
						<td width="120" class="text">Nombre:</td>
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
				<td width="120" class="titulosizquierda">DNI</td>
				<td width="490" class="titulos">Nombre y Apellido</td>
				<td width="30" class="titulos">&nbsp;</td>
			</tr><?
		$rs = $db->Execute( "
			SELECT DISTINCT u.* 
			FROM sys_users_tipo_transporte tt 
			LEFT JOIN sys_users u ON tt.id_usuario = u.id 
			WHERE 1=1 ".
				( $dni_list != '' ? "AND u.dni LIKE '".html_entity_decode( $dni_list )."%' " : '' ).
				( $nombre_list ? " AND u.nombre LIKE '".html_entity_decode( $nombre_list )."%'" : '' ).
			" ORDER BY u.dni
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->dni?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>" align="center"><a href="javascript:seleccionar_camionero( '<?=$reg->dni?>' );"><img 
				src="../sys_images/mas.gif" border="0" alt="Seleccione al Transportista"></a></td>
			</tr><?
		}
		?></table>
	</form><?
	}

	if( $action == 'list_transportes' ){
		?><form name="form1" id="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="list_transportes" />
	<input type="hidden" name="dni_transportista" id="dni_transportista" value="<?=$dni_transportista?>" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="3" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr>
				<td class="cuadro1izquierda" colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
					<tr>
						<td width="120" class="text">Nombre:</td>
						<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" 
						style="width:100% " /></td>
						<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
					</tr>
					<tr>
						<td class="text">Marca:</td>
						<td><?
					$rs = $db->Execute( "
						SELECT nombre, id 
						FROM tipo_transporte
						ORDER BY nombre
					" );
					echo $rs->GetMenu2(
						'tipo_lst',
						$tipo_lst,
						": -- Seleccione un tipo de transporte -- ",
						false,
						1,
						'id="tipo_lst" style="width: 100%"'
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
				<td width="160" class="titulosizquierda">Patente</td>
				<td width="450" class="titulos">Nombre</td>
				<td width="30" class="titulos">&nbsp;</td>
			</tr><?
		$rs = $db->Execute( "
			SELECT t.*
			FROM transporte t 
			LEFT JOIN sys_users_tipo_transporte ut ON ut.id_tipo = t.id_tipo 
			LEFT JOIN sys_users u ON u.id = ut.id_usuario  
			WHERE 1=1".(
				$nombre_list ? " AND ( t.nombre LIKE '".html_entity_decode( $nombre_list )."%' 
					OR t.patente LIKE '%".html_entity_decode( $nombre_list )."%' )" : "" ).(
				$tipo_lst ? " AND t.id_tipo='".$tipo_lst."'" : "" )."
				AND u.dni = '".$dni_transportista."' 				
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->patente?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>" align="center"><a href="javascript:seleccionar_transporte( '<?=$reg->nombre?>' );"><img 
				src="../sys_images/mas.gif" border="0" alt="Seleccione al Transporte"></a></td>
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
				<td colspan="2" class="cuadro1izquierda" align="center">El remito ha sido ingresado. 
				<br>Remito Electr&oacute;nico Nro: <?=$id_remito?></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda" width="150">&nbsp;</td>
				<td class="cuadro2" width="490"><input type="button" value="Seguir generando remitos" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">&nbsp;<input type="button" 
				value="Visualizar Remito <?=$id_remito?>" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>?action=Print&id=<?=$id_remito?>&id_cliente=<?=$id_cliente?>&id_sucursal=<?=$id_sucursal?>'"></td>
			</tr>
		</table><?
	}
	if( $action == 'Print' ){
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
						<td width="90" align="center"><strong>Cantidad</strong></td>
						<td width="90" align="center"><strong>C&oacute;digo</strong></td>
						<td><strong>Producto</strong></td>
					</tr><?
		$rs = $db->Execute( "
			SELECT p.nombre producto, p.codigo, i.cantidad
			FROM item_pedido i 
			LEFT JOIN producto p ON i.id_producto = p.id 
			LEFT JOIN remito_factura r ON i.id_remito = r.id 
			WHERE r.id = '".$id."'
			ORDER BY p.codigo, p.nombre 
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			
					?><tr>
						<td align="center"><?=$reg->cantidad?></td>
						<td align="center"><?=$reg->codigo?></td>
						<td><?=$reg->producto?></td>
					</tr><?
		}
					?>
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