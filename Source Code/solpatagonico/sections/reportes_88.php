<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Remitos Facturables';
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
				<th colspan="2" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			
			<tr>
				<td colspan="2" class="sombra">&nbsp;</td>
			</tr><?
	$rs = $db->Execute( "
		SELECT * 
		FROM remito_factura
		WHERE fecha_factura is NULL OR fecha_factura = '0000-00-00'
		ORDER BY fecha_remito DESC
	" );
	if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="2" align="center" class="error">Sin registros coincidentes.</td>
			</tr><?
	} else {
			?><tr>
				<td width="110" class="titulosizquierda">C&oacute;digo del remito</td>
				<td width="530" class="titulos">Fecha del Remito</td>
				</tr><?		
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->id?></td>
				<td class="<?=$class?>"><?=fecha::iso2normal( $reg->fecha_remito )?></td>
				</tr><?
		}
			?><tr>
					<td colspan="2" class="sombra">&nbsp;</td>
			</tr>
		</table>
</form>
<?
	} // Fin de Accion
?>
</body>
</html>