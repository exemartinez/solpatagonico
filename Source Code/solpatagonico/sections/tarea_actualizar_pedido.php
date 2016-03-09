<?
	include_once '../inc/conf.inc.php';
	
	$pedidos_a_actualizar = 0;
	$rs = $db->Execute( "
		SELECT * 
		FROM pedido 
		WHERE id_estado_pedido NOT IN ( 6, 7 )
	" );	
	if( $rs->RecordCount() ){
		$pedidos_a_actualizar = $rs->RecordCount();
		while( $pedido = $rs->FetchNextObject( false ) ){
			$db->Execute( "
				UPDATE pedido 
				SET id_estado_pedido='".$db->GetOne( "
					SELECT MIN( id_estado_item ) 
					FROM item_pedido 
					WHERE id_pedido='".$pedido->id."'
				" )."' 
				WHERE id='".$pedido->id."'
			" );
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
			<th class="titulosizquierda" style="font-size: 14px; font-weight: bold">Tarea de actualizaci&oacute;n de estados de pedidos.</th>
		</tr>
		<tr> 
			<td class="cuadro1izquierda"><?
				if( $pedidos_a_actualizar ){
					?>Se ha<?=( $pedidos_a_actualizar > 1 ? 'n' : '' )?> actualizado
					<strong><?=$pedidos_a_actualizar?></strong> pedido<?=( $pedidos_a_actualizar > 1 ? 's' : '' )?>.<?
				} else {
					?>No se han actualizado pedidos.<?
				}
			?></td>
		</tr>
	</table>
</body>
</html>