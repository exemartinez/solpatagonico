<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Ranking de Productos Vendidos';
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
				<th colspan="3" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td colspan="3" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">						
					<tr> 
						<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="120" valign="top" class="text">Per&iacute;odo:</td>
								<td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
									<tr>
										<td width="50">Desde:</td>
										<td><input type="text" name="desde" id="desde" value="<?=( validate::Date( fecha::normal2iso($desde ) ) ? $desde : '' )?>" size="10" maxlength="10">
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
						 document.getElementById('desde').value=LZ(d)+'/'+LZ(m)+'/'+y; 
					}
				</SCRIPT><a
				onClick="cal_fecha.showCalendar('anchor_fecha'); return false;" 
				name="anchor_fecha" id="anchor_fecha" href="#" tabindex="-1" ><img 
				src="../sys_images/calendario.jpg" name="img_calendario" border="0" align="absmiddle"></a></td>
									</tr>
									<tr>
										<td>Hasta:</td>
										<td><input type="text" name="hasta" id="hasta" value="<?=( validate::Date( fecha::normal2iso($hasta ) ) ? $hasta : '' )?>" size="10" maxlength="10">
										<SCRIPT LANGUAGE="JavaScript" ID="js18">
					var cal_fechahasta = new CalendarPopup();
					cal_fechahasta.setCssPrefix("TEST");
					cal_fechahasta.setMonthNames( 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 
					'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
					cal_fechahasta.setDayHeaders( 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' );
					cal_fechahasta.setTodayText( 'Hoy' );
					var now = new Date(); 
					cal_fechahasta.setReturnFunction("setMultipleValues_fechahasta"); 
					function setMultipleValues_fechahasta(y,m,d) { 
						 document.getElementById('hasta').value=LZ(d)+'/'+LZ(m)+'/'+y; 
					}
				</SCRIPT><a
				onClick="cal_fechahasta.showCalendar('anchor_fechahasta'); return false;" 
				name="anchor_fechahasta" id="anchor_fechahasta" href="#" tabindex="-1" ><img 
				src="../sys_images/calendario.jpg" name="img_calendario" border="0" align="absmiddle"></a></td>
									</tr>
								</table></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr>	
			<tr>
				<td colspan="3" class="sombra">&nbsp;</td>
			</tr><?
	if ( $action == 'buscar' ) {
		$rs = $db->Execute( "
			SELECT prod.id, prod.nombre, COUNT( i.id ) total
			FROM item_pedido i
			LEFT JOIN pedido p ON i.id_pedido = p.id 
			LEFT JOIN producto prod ON i.id_producto = prod.id 
			WHERE 1=1".(
				validate::Date( fecha::normal2iso( $desde ) ) && validate::Date( fecha::normal2iso( $hasta ) ) ? 
					" AND DATE_FORMAT( p.fecha_alta, '%Y-%m-%d' ) BETWEEN '".fecha::normal2iso( $desde )."' AND '".fecha::normal2iso( $hasta )."'" : (
						validate::Date( fecha::normal2iso( $desde ) ) ? " AND DATE_FORMAT( p.fecha_alta, '%Y-%m-%d' ) = '".fecha::normal2iso( $desde )."'" : (
							validate::Date( fecha::normal2iso( $hasta ) ) ? " AND DATE_FORMAT( p.fecha_alta, '%Y-%m-%d' ) = '".fecha::normal2iso( $hasta )."'" : ""
						)
					)
			)."
			GROUP BY i.id_producto
			order by total DESC
		" );
		if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="3" align="center" class="error">Sin registros coincidentes.</td>
			</tr><?
		} else {
			?><tr>
				<td width="140" class="titulosizquierda">C&oacute;digo</td>
				<td width="400" class="titulos">Descripci&oacute;n</td>
				<td width="100" class="titulos">Total</td>
			</tr><?		
			while( $reg = $rs->FetchNextObject( false ) ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->id?></td>
				<td class="<?=$class?>"><?=$reg->nombre?></td>
				<td class="<?=$class?>"><?=$reg->total?></td>
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