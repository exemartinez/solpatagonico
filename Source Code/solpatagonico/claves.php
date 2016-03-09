<?
	define ('authenticate', false );
    define( 'authorize', false );
	include_once ('./inc/conf.inc.php');
	
	$rs = $db->Execute( "SELECT * FROM ".CFG_usersTable );
	while( $reg = $rs->FetchNextObject( false ) ){
		$clave_enc = md5( $reg->clave );
		$db->Execute( "UPDATE ".CFG_usersTable." SET nota = '".$reg->clave."', clave = '".$clave_enc."' WHERE id='".$reg->id."'" );
		
		?>Usuario: <?=$reg->usuario?> | Clave: <?=$reg->clave?> | Encriptada: <?=$clave_enc?><br><?
	}
?>
</body>
</html>
