<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Productos pendientes de Stock';
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
				<th colspan="6" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			
			<tr>
				<td colspan="6" class="sombra">&nbsp;</td>
			</tr><?
	$rs = $db->Execute( "
		SELECT * 
		FROM producto
		WHERE cantidad_real - cantidad_reserva < punto_reposicion 
			AND ( fecha_baja IS NULL OR  fecha_baja = '0000-00-00 00:00:00' )
		ORDER BY nombre
	" );
	if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="6" align="center" class="error">Sin registros coincidentes.</td>
			</tr><?
	} else {
			?><tr>
				<td width="80" class="titulosizquierda">C&oacute;digo</td>
				<td width="320" class="titulos">Descripci&oacute;n</td>
				<td width="60" class="titulos">TP</td>
				<td width="60" class="titulos">C. Real</td>
				<td width="60" class="titulos">C. Res</td>
				<td width="60" class="titulos">Pto. Rep.</td>
			</tr><?		
		while( $reg = $rs->FetchNextObject( false ) ){
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->codigo?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>"><?=$reg->tamanio_pedido?></td>
				<td class="<?=$class?>"><?=$reg->cantidad_real?></td>
				<td class="<?=$class?>"><?=$reg->cantidad_reserva?></td>
				<td class="<?=$class?>"><?=$reg->punto_reposicion?></td>
			</tr><?
		}
			?><tr>
					<td colspan="6" class="sombra">&nbsp;</td>
			</tr>
		</table>
</form>
<?
	} // Fin de Accion
?>
</body>
</html>