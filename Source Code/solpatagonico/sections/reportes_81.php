<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Reportando Clientes seg&uacute;n estado de pedido';
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
				<th colspan="4" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td colspan="4" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">						
					<tr> 
						<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="120" class="text">Estado de pedido:</td>
								<td><?
	$rs = $db->Execute( "SELECT nombre, id FROM estado_pedido ORDER BY nombre" );
	echo $rs->GetMenu2(
		'id_estado_pedido',
		$id_estado_pedido,
		": -- Todos -- ",
		false,
		1,
		'id="id_estado_pedido" style="width: 100%"'
	);
								?></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
							<tr>
								<td class="text" valign="top">Per&iacute;odo:</td>
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
				$id_estado_pedido ? " AND p.id_estado_pedido='".$id_estado_pedido."'" : ''
			).(
				validate::Date( fecha::normal2iso( $desde ) ) && validate::Date( fecha::normal2iso( $hasta ) ) ? 
					" AND DATE_FORMAT( p.fecha_alta, '%Y-%m-%d' ) BETWEEN '".fecha::normal2iso( $desde )."' AND '".fecha::normal2iso( $hasta )."'" : (
						validate::Date( fecha::normal2iso( $desde ) ) ? " AND DATE_FORMAT( p.fecha_alta, '%Y-%m-%d' ) = '".fecha::normal2iso( $desde )."'" : (
							validate::Date( fecha::normal2iso( $hasta ) ) ? " AND DATE_FORMAT( p.fecha_alta, '%Y-%m-%d' ) = '".fecha::normal2iso( $hasta )."'" : ""
						)
					)
			)."
		" );
		if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="4" align="center" class="error">Sin registros coincidentes.</td>
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