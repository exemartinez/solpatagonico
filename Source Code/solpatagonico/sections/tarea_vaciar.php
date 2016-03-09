<?
	include_once '../inc/conf.inc.php';
	
	$db->debug = true;
	
	$ok = $db->Execute( "TRUNCATE item_pedido" );
	$ok = $db->Execute( "TRUNCATE pedido" );
	$ok = $db->Execute( "TRUNCATE remito_factura" );
	$ok = $db->Execute( "UPDATE producto SET cantidad_reserva = 0, cantidad_real = 100" );
?>
Se vaciaron las tablas: item_pedido, pedido y remito_factura<br />
Se actualizaron todos los productos poniendo en 0 (cero) la cantidad de reservas y en 100 (cien) la cantidad real.