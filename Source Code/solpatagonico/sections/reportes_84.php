<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Productos de un Proveedor';
	require_once CFG_libPath.'xajax/xajax.inc.php';
	
	$xajax = new xajax();
	$xajax->setCharEncoding("iso-8859-1");
	
	$xajax->registerFunction("bajar_proveedor");
	
	$xajax->processRequests();
	
	function bajar_proveedor( $cod_proveedor ){
		global $db;
		
		$objResponse = new xajaxResponse("iso-8859-1");
		$objResponse->addScript( "document.getElementById( 'tr_proveedor' ).style.display = 'none';" );
		$objResponse->addScript( "document.getElementById( 'id_proveedor' ).value = '';" );
		if( $cod_proveedor != '' ){
			$rs = $db->Execute( "
				SELECT * 
				FROM proveedor 
				WHERE cuit LIKE '".$cod_proveedor."%' OR razonsocial LIKE '".$cod_proveedor."%' 
			" );
			if( $rs->RecordCount() ){
				$reg = $rs->FetchObject( false );
				$objResponse->addScript( "document.getElementById( 'tr_proveedor' ).style.display = 'block';" );
				$objResponse->addScript( "document.getElementById( 'id_proveedor' ).value = '".$reg->id."';" );			
				$objResponse->addScript( "document.getElementById( 'td_cuit' ).innerHTML = '".$reg->cuit."';" );
				$objResponse->addScript( "document.getElementById( 'td_razonsocial' ).innerHTML = '".$reg->razonsocial."';" );
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
	if( $id_proveedor ){
		$rs = $db->Execute( "SELECT * FROM proveedor WHERE id='".$id_proveedor."'" );
		$reg = $rs->FetchObject( false );
	}
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="3" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td colspan="3" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">						
					<tr> 
						<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="120" class="text">CUIT:</td>								
								<td><input type="text" name="cuit" id="cuit" value="<?=$cuit?>" 
								onKeyUp="xajax_bajar_proveedor( this.value );" style="width: 100%"></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
							<tr id="tr_proveedor" style="display: <?=( $id_proveedor ? 'block' : 'none' )?>;">
								<td class="text">&nbsp;<input type="hidden" name="id_proveedor" id="id_proveedor" 
				value="<?=$id_proveedor?>"></td>
								<td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
									<tr>
										<th class="titulosizquierda" colspan="2">Datos del Proveedor</th>
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
				<td colspan="3" class="sombra">&nbsp;</td>
			</tr><?
	if ( $action == 'buscar' && $cuit && $id_proveedor ) {
		$rs = $db->Execute( "
			SELECT p.id, p.nombre, pp.precio 
			FROM proveedor_producto pp 
			LEFT JOIN proveedor prov ON pp.id_proveedor = prov.id 
			LEFT JOIN producto p ON pp.id_producto = p.id 
			WHERE 1=1".(
				$id_proveedor ? " AND prov.id='".$id_proveedor."'" : ''
			)."
		" );
		if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="3" align="center" class="error">Sin registros coincidentes o el CUIT ingresado no es v&aacute;lido.</td>
			</tr><?
		} else {
			?><tr>
				<td width="80" class="titulosizquierda">C&oacute;digo</td>
				<td width="410" class="titulos">Descripci&oacute;n</td>
				<td width="150" class="titulos">Precio</td>
			</tr><?		
			while( $reg = $rs->FetchNextObject( false ) ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->id?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>"><?=$reg->precio?></td>
			</tr><?
			}
			?><tr>
					<td colspan="3" class="sombra">&nbsp;</td>
			</tr><?
		}
		?></table>
</form>
<?
	} // Fin de Accion
?>
</body>
</html>