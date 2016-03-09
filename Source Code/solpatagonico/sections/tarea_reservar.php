<?
	include_once '../inc/conf.inc.php';
	
	$ids_lectura = ( $ids_lectura ? $ids_lectura : '1,2' );
	$pedidos_actualizados = 0;
	$pedidos_a_actualizar = 0;
	$rs = $db->Execute( "
		SELECT * 
		FROM pedido 
		WHERE id_estado_pedido IN ( ".$ids_lectura." ) 
		ORDER BY fecha_alta
	" );	
	if( $rs->RecordCount() ){
		$pedidos_a_actualizar = $rs->RecordCount();
		while( $pedido = $rs->FetchNextObject( false ) ){
			$actualizar = 0;
			$items = $db->Execute( "
				SELECT * 
				FROM item_pedido 
				WHERE id_pedido='".$pedido->id."' AND id_estado_item IN ( ".$ids_lectura." )
			" );
			while( $item = $items->FetchNextObject( false ) ){
				$producto = $db->GetRow( "SELECT * FROM producto WHERE id='".$item->id_producto."'" );
				if( ( $item->cantidad + $producto['cantidad_reserva'] ) <= $producto['cantidad_real'] ){
					# Actualizando la cantidad reservado del producto en stock.
					$db->Execute( "
						UPDATE producto 
						SET cantidad_reserva = '".( $item->cantidad + $producto['cantidad_reserva'] )."' 
						WHERE id='".$item->id_producto."' 
					" );
					# Cambiando a estado: Reservado.
					$db->Execute( "
						UPDATE item_pedido 
						SET id_estado_item='3' 
						WHERE id='".$item->id."'
					" );
					
					$actualizar = 1;
				} else {
					# Cambiando a estado: Pendiente de Stock.
					$db->Execute( "
						UPDATE item_pedido 
						SET id_estado_item='2' 
						WHERE id='".$item->id."'
					" );
					
					# Cambiando a estado: Pendiente de Stock.
					$db->Execute( "
						UPDATE pedido 
						SET id_estado_pedido='2' 
						WHERE id='".$pedido->id."'
					" );
				}
			}
			if( $actualizar ){
				$pedidos_actualizados++;
			}
			# Cambiamos a estado: Reservado.
			if( !$db->GetOne( "SELECT COUNT(*) FROM item_pedido WHERE id_pedido='".$pedido->id."' AND id_estado_item IN ( 1, 2 )" ) ){
				$db->Execute( "
					UPDATE pedido 
					SET id_estado_pedido='3' 
					WHERE id='".$pedido->id."'
				" );				
			}
		}
	}
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
</head>

<body>
	<br>
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" style="font-size: 14px; font-weight: bold">Tarea de reserva de productos.</th>
		</tr>
		<tr> 
			<td class="cuadro1izquierda"><?
				if( $pedidos_actualizados ){
					?>Se ha<?=( $pedidos_actualizados > 1 ? 'n' : '' )?> generado reservas en
					<strong><?=$pedidos_actualizados?></strong> pedido<?=( $pedidos_actualizados > 1 ? 's' : '' )?>.<?
				} else {
					?>No se han generado reservadas de productos.<?
				}
				?><br>
					<br><?
				$pedidos_a_actualizar = $pedidos_a_actualizar - $pedidos_actualizados;
				if( $pedidos_a_actualizar ){
					?>Hay <strong><?=$pedidos_a_actualizar?></strong> pedido<?=( $pedidos_a_actualizar > 1 ? 's' : '' )?> 
					pendientes de reserva. Verifique su stock.<?
				} else {
					?>No hay pedidos pendientes de reserva.<?
				}
			?></td>
		</tr>
	</table>
</body>
</html>