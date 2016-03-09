<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Pedidos de un Cliente';
	
	require_once CFG_libPath.'xajax/xajax.inc.php';
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_cliente");
	
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
				WHERE cuit LIKE '".$cod_cliente."%' OR razonsocial LIKE '".$cod_cliente."%' 
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
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
	<SCRIPT LANGUAGE="JavaScript" SRC="<?=CFG_jsPath?>CalendarPopup.js"></SCRIPT>
	<? $xajax->printJavascript( '../javascript/', 'xajax.js' ); ?>
</head>

<body>
<?
	if( $id_cliente ){
		$rs = $db->Execute( "SELECT * FROM cliente WHERE id='".$id_cliente."'" );
		$reg = $rs->FetchObject( false );
	}
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="4" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td colspan="4" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">						
					<tr> 
						<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="120" class="text">CUIT:</td>								
								<td><input type="text" name="cuit" id="cuit" value="<?=$cuit?>" 
								onKeyUp="xajax_bajar_cliente( this.value );" style="width: 100%"></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
							<tr id="tr_cliente" style="display: <?=( $id_cliente ? 'block' : 'none' )?>;">
								<td class="text">&nbsp;<input type="hidden" name="id_cliente" id="id_cliente" 
				value="<?=$id_cliente?>"></td>
								<td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
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
								<td>&nbsp;</td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr>	
			<tr>
				<td colspan="4" class="sombra">&nbsp;</td>
			</tr><?
	if ( $action == 'buscar' ) {
		$rs = $db->Execute( "
			SELECT p.*, c.razonsocial, s.nombre sucursal, s.direccion, s.telefono, e.nombre estado
			FROM pedido p 
			LEFT JOIN cliente c ON p.id_cliente = c.id 
			LEFT JOIN sucursal s ON p.id_sucursal = s.id 
			LEFT JOIN estado_item e ON p.id_estado_pedido = e.id 
			WHERE 1=1".(
				$id_cliente ? " AND c.id = '".$id_cliente."'" : ''
			)."
		" );
		if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="4" align="center" class="error">Sin registros coincidentes o el CUIT ingresado no es v&aacute;lido.</td>
			</tr><?
		} else {
			while( $reg = $rs->FetchNextObject( false ) ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td colspan="4" class="titulosizquierda">Datos de Cliente</td>
			</tr>
			<tr>
				<td colspan="4" class="<?=$class?>izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="0" 
				class="text">
					<tr>
						<td width="130" valign="top"><strong>Estado del pedido:</strong></td>
						<td><?=$reg->estado?></td>
					</tr>
					<tr>
						<td valign="top"><strong>Cliente:</strong></td>
						<td>[<?=$reg->id_cliente?>] <?=$reg->razonsocial?></td>
					</tr>
					<tr>
						<td valign="top"><strong>Sucursal:</strong></td>
						<td><?=$reg->sucursal?> - <?=$reg->direccion?> - <?=$reg->telefono?></td>
					</tr>
					<tr>
						<td valign="top"><strong>Pedido:</strong></td>
						<td>[<?=$reg->id?>] <?=fecha::iso2normal( substr( $reg->fecha_alta, 0, 10 ) )?> 
						<?=substr( $reg->fecha_alta, 10 )?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table></td>
			</tr><?
				$aux = $db->Execute( "
					SELECT p.id, p.nombre, i.cantidad, e.nombre estado
					FROM item_pedido i 
					LEFT JOIN producto p ON i.id_producto = p.id 
					LEFT JOIN estado_item e ON i.id_estado_item = e.id 
					WHERE i.id_pedido = '".$reg->id."' 
					ORDER BY p.nombre
				" );				
			?><tr>
				<td width="80" class="titulosizquierda">C&oacute;digo</td>
				<td width="360" class="titulos">Descripci&oacute;n</td>
				<td width="50" class="titulos">Cant.</td>
				<td width="150" class="titulos">Estado</td>
			</tr><?
				while( $item = $aux->FetchNextObject( false ) ){				
			?><tr>
				<td class="<?=$class?>izquierda"><?=$item->id?></td>
				<td class="<?=$class?>"><?=$item->nombre?></td>
				<td class="<?=$class?>"><?=$item->cantidad?></td>
				<td class="<?=$class?>"><?=$item->estado?></td>
			</tr><?
				}
		?><tr>
		    <td colspan="4" class="sombra">&nbsp;</td>
		</tr><?
			}
		}
	?></table>
</form>
<?
	} // Fin de Accion
?>
</body>
</html>