<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Proveedores de un Producto';
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
	<SCRIPT LANGUAGE="JavaScript" SRC="<?=CFG_jsPath?>CalendarPopup.js"></SCRIPT>
</head>

<body>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="5" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td colspan="5" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">						
					<tr> 
						<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="120" class="text">C&oacute;digo:</td>								
								<td><input type="text" name="codigo" id="codigo" value="<?=$codigo?>" style="width: 100%"></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr><?
	if( $codigo ){
		$rs = $db->Execute( "SELECT * FROM producto WHERE codigo like '".$codigo."%'" );
		if( $rs->RecordCount() ){
			$reg = $rs->FetchObject( false );
			while( $reg = $rs->FetchNextObject( false ) ){
			?><tr>
				<td colspan="5" class="cuadro2izquierda">Producto: [<?=$reg->codigo?>] - <?=$reg->nombre?></td>
			</tr><?
			}
		}
	}
			?><tr>
				<td colspan="5" class="sombra">&nbsp;</td>
			</tr><?
	if ( $action == 'buscar' && $codigo ) {
		$rs = $db->Execute( "
			SELECT prov.cuit, prov.razonsocial, prov.telefono, prov.contacto, pp.precio 
			FROM proveedor_producto pp 
			LEFT JOIN proveedor prov ON pp.id_proveedor = prov.id 
			LEFT JOIN producto p ON pp.id_producto = p.id 
			WHERE 1=1".(
				$codigo ? " AND p.codigo like '".$codigo."%'" : ''
			)."
		" );
		if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="5" align="center" class="error">Sin registros coincidentes o el c&oacute;digo 
				ingresado no es v&aacute;lido.</td>
			</tr><?
		} else {
			?><tr>
				<td width="80" class="titulosizquierda">CUIT</td>
				<td width="250" class="titulos">Raz&oacute;n Social</td>
				<td width="80" class="titulos">Tel&eacute;fono</td>
				<td width="150" class="titulos">Contacto</td>
				<td width="80" class="titulos">Precio</td>
			</tr><?		
			while( $reg = $rs->FetchNextObject( false ) ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->cuit?></td>
				<td class="<?=$class?>"><?=$reg->razonsocial?></td>
				<td class="<?=$class?>"><?=$reg->telefono?></td>
				<td class="<?=$class?>"><?=$reg->contacto?></td>
				<td class="<?=$class?>"><?=$reg->precio?></td>
			</tr><?
			}
			?><tr>
					<td colspan="5" class="sombra">&nbsp;</td>
			</tr><?
		}
		?></table>
</form>
<?
	} // Fin de Accion
?>
</body>
</html>