<?
	include_once '../inc/conf.inc.php';
	require_once CFG_libPath.'xajax/xajax.inc.php';
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_camionero");
	$xajax->registerFunction("bajar_transporte");	
	
	$xajax->processRequests();
	
	function bajar_camionero( $cod_camionero ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_camionero' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'dni_transportista' ).value = '';" );
		if( $cod_camionero ){
			$rs = $db->Execute( "
				SELECT DISTINCT u.* 
				FROM item_pedido i 
				LEFT JOIN remito_factura r ON i.id_remito = r.id 
				LEFT JOIN pedido p ON i.id_pedido = p.id 
				LEFT JOIN sys_users u ON r.dni_transportista = u.dni 
				WHERE 1=1 
					AND ( i.id_remito != 0 AND i.id_remito IS NOT NULL ) 
					AND ( u.nombre LIKE '".html_entity_decode( $cod_camionero )."%' OR u.dni LIKE '".html_entity_decode( $cod_camionero )."%' )
					 AND i.id_estado_item = '4'
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_camionero' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'dni_transportista' ).value = '".$reg->dni."';" );			
				$objResponse->addScript( "document.getElementById( 'td_nombre' ).innerHTML = '".$reg->nombre."';" );
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
				SELECT DISTINCT t.* 
				FROM item_pedido i 
				LEFT JOIN remito_factura r ON i.id_remito = r.id 
				LEFT JOIN pedido p ON i.id_pedido = p.id 
				LEFT JOIN sys_users u ON r.dni_transportista = u.dni  
				LEFT JOIN sys_users_tipo_transporte ut ON u.id = ut.id_usuario 
				LEFT JOIN transporte t ON ut.id_tipo = t.id_tipo 
				WHERE 1=1 
					AND ( i.id_remito != 0 AND i.id_remito IS NOT NULL ) 
					AND ( t.nombre LIKE '".html_entity_decode( $cod_transporte )."%' OR t.patente LIKE '".html_entity_decode( $cod_transporte )."%' ) 
					AND u.dni = '".$dni_transportista."' 
					AND i.id_estado_item = '4'
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
	
	# Variables de diseño
	$title		= 'Recepcion de remitos';
	$name		= 'Recepcion';
	$titleForm	= 'Recepcion de remitos';
	$msgCancel	= '¿Desea cancelar la carga de los remitos entregagos actuales?\nSe perderan los datos ingresados';	
	# Variables de programación
	$tabla		= "remito_factura";
	$tabla_item	= "item_pedido";
	$campos		= "fecha_remito,dni_transportista,patente";	
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( $action == 'Agregar' ) {
		if ( !count($items) ) 
			$error .= "<li>Debe seleccionar al menos un remito a actualizar.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		foreach( $items as $id_remito ){
			$ok = $db->Execute( "
				UPDATE ".$tabla_item." 
				SET id_estado_item='5' 
				WHERE id_remito='".$id_remito."' AND id_estado_item = '4'
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
			if( document.getElementById('dni_transportista').value == '' ){
				alert( 'Debe seleccionar un Transportista' );
				document.getElementById('cod_camionero').focus();
			} else if( document.getElementById('patente').value == '' ){
				alert( 'Debe seleccionar un Transporte' );
				document.getElementById('cod_transporte').focus();
			} else {
				document.form1.target = '_self';
				document.getElementById( 'action' ).value = '';
				document.form1.submit();
			}
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
				alert( 'Debe seleccionar al menos 1 remito' );
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
				<td width="150" class="cuadro1izquierda">Transportista</td>
				<td width="490" class="cuadro1"><?
			if( !$dni_transportista ){
				?><input type="text" name="cod_camionero" id="cod_camionero" value="<?=prepare_var( $cod_camionero )?>" 
				onKeyUp="xajax_bajar_camionero( this.value ); enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: void open( 
				'<?=$_SERVER['PHP_SELF']?>?action=list_camioneros', 'buscador', 
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
						<td id="td_nombre" class="cuadro1"><?=$reg->nombre?></td>
					</tr>
					<tr>
						<td class="cuadro1izquierda">DNI:</td>
						<td id="td_dni" class="cuadro1"><?=$reg->dni?></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td class="cuadro1izquierda">Trasporte</td>
				<td class="cuadro1"><?
			if( !$patente ){
				?><input type="text" name="cod_transporte" id="cod_transporte" value="<?=prepare_var( $cod_transporte )?>" 
				onKeyUp="xajax_bajar_transporte( this.value, document.getElementById( 'dni_transportista' ).value ); 
				enter( 'seleccionar()' );" />
				&nbsp;<a 
				href="javascript: if( document.getElementById('dni_transportista').value != '' ){ void open( 
				'<?=$_SERVER['PHP_SELF']?>?action=list_transportes&dni_transportista='+document.getElementById('dni_transportista').value, 'buscador', 
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
						<th class="titulosizquierda" colspan="2">Datos de Transporte</th>
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
				id="tbl_items"><?
		$rs = $db->Execute( "
			SELECT DISTINCT r.id, c.razonsocial, s.nombre   
			FROM remito_factura r 
			LEFT JOIN item_pedido i ON r.id = i.id_remito 
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN cliente c ON p.id_cliente = c.id 
			LEFT JOIN sucursal s ON p.id_sucursal = s.id 
			WHERE r.patente = '".$patente."' AND r.dni_transportista = '".$dni_transportista."' AND i.id_estado_item = '4' 
		" );
		if( $rs->RecordCount() ){
					?><tr>
						<th width="25" class="titulosizquierda">&nbsp;</th>
						<th width="70" class="titulos" align="center">Nro. Remito</th>
						<th class="titulos">Cliente</th>
					</tr><?
			while( $reg = $rs->FetchNextObject( false ) ){
					?><tr>
						<td width="25" class="cuadro1izquierda"><input type="checkbox" name="items[]" 
						value="<?=$reg->id?>"></td>
						<td width="70" class="cuadro1" align="center"><?=$reg->id?></td>
						<td class="cuadro1"><?=$reg->razonsocial?> > <?=$reg->nombre?></td>
						</tr><?
			}
		} else {
					?><tr>
						<td colspan="3" class="cuadro1izquierda" align="center"><span 
						class="error">Sin remitos pendientes.</span></td>
				</tr><?
		}
				?></table></td>
			</tr>
			<tr>
				<td class="cuadro2izquierda">&nbsp;</td>
				<td class="cuadro2"><input name="preview_pedido" type="button" value="Ver Remitos Entregados" onClick="preview()" 
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
				<th class="titulosizquierda" colspan="2">Remitos</th>
			</tr>
			<tr>
				<td colspan="2" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="70" class="cuadro2izquierda">Nro.</td>
						<td class="cuadro2">Cliente</td>
					</tr><?
		if( is_array( $items ) ){
			foreach( $items as $id_remito ){
				$reg = $db->GetRow( "
					SELECT DISTINCT c.razonsocial, s.nombre, r.id   
					FROM remito_factura r 
					LEFT JOIN item_pedido i ON r.id = i.id_remito 
					LEFT JOIN pedido p ON i.id_pedido = p.id 
					LEFT JOIN cliente c ON p.id_cliente = c.id 
					LEFT JOIN sucursal s ON p.id_sucursal = s.id 
					WHERE r.id='".$id_remito."'
				" );
					?><tr>
						<td class="cuadro1izquierda"><?=$reg['id']?></td>
						<td class="cuadro1"><?=$reg['razonsocial']?> > <?=$reg['nombre']?></td>
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
			FROM item_pedido i 
			LEFT JOIN remito_factura r ON i.id_remito = r.id 
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN sys_users u ON r.dni_transportista = u.dni 
			WHERE 1=1 
				AND ( i.id_remito != 0 AND i.id_remito IS NOT NULL )
				AND i.id_estado_item = '4'".
				( $dni_list != '' ? " AND u.dni LIKE '".html_entity_decode( $dni_list )."%' " : '' ).
				( $nombre_list ? " AND u.nombre LIKE '".html_entity_decode( $nombre_list )."%'" : '' ).
			" ORDER BY u.dni 
		" );
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->dni?> <?=$reg->pedido?></td>
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
						<td class="text">Zona:</td>
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
			SELECT DISTINCT t.* 
			FROM item_pedido i 
			LEFT JOIN remito_factura r ON i.id_remito = r.id 
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN sys_users u ON r.dni_transportista = u.dni  
			LEFT JOIN sys_users_tipo_transporte ut ON u.id = ut.id_usuario 
			LEFT JOIN transporte t ON ut.id_tipo = t.id_tipo 
			WHERE 1=1 
				AND ( i.id_remito != 0 AND i.id_remito IS NOT NULL ) 
				AND i.id_estado_item = '4'".
				( $nombre_list ? " AND ( t.nombre LIKE '".html_entity_decode( $nombre_list )."%' OR t.patente LIKE '".html_entity_decode( $nombre_list )."%' )" : "" ).
				( $tipo_lst ? " AND t.id_tipo='".$tipo_lst."'" : "" )."
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
				<td colspan="2" class="cuadro1izquierda" align="center">Los remitos han sido actualizados.</td>
			</tr>
			<tr>
				<td class="cuadro2izquierda" width="150">&nbsp;</td>
				<td class="cuadro2" width="490"><input type="button" value="Seguir actualizando remitos" 
				onClick="location.href='<?=$_SERVER['PHP_SELF']?>'"></td>
			</tr>
		</table><?
	}
?></body>
</html>