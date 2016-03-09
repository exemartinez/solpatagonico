<?	
	define( 'authorize', false );

	require_once 'Contact_Vcard_Build.php';
	include_once '../inc/conf.inc.php';

	$rs = $db->Execute( "SELECT * FROM contactos WHERE id = '$id'" );
	$reg = $rs->FetchObject(false);

	$vcard = new Contact_Vcard_Build();

	// Nombre
	$vcard->setFormattedName( $reg->nombre.' '.$reg->apellido );
	$vcard->setName( 
		$reg->apellido, // Apellido
		$reg->nombre, // Nombre
		'', // Segundo Nombre
		'', // Titulo
		'' // ?
	);

	// Empresa
	$vcard->addOrganization( $reg->empresa );

	// CUIT y Observaciones
	$vcard->setNote( 'CUIT '.$reg->cuit." | ".$reg->obs );	

	// Email
	$vcard->addEmail( $reg->mail );
	$vcard->addParam( 'TYPE', 'WORK' );

	// Direccion
	$vcard->addAddress( '', '', $reg->dir, $reg->cp, $reg->localidad, $reg->provincia, $reg->pais );
	$vcard->addParam( 'TYPE', 'WORK' );

	// Telefono
	$vcard->addTelephone( $reg->tel );
	$vcard->addParam( 'TYPE', 'WORK' );

	// Fax
	$vcard->addTelephone( $reg->fax );
	$vcard->addParam( 'TYPE', 'FAX' );

	// Celular
	$vcard->addTelephone( $reg->celular );
	$vcard->addParam( 'TYPE', 'CELL' );

	$text = $vcard->fetch();

	header( "Pragma: public" );
	header( "Cache-control: private" );
	header( "Content-Type: application/force-download" );
	header( "Content-Disposition: attachment;filename=vcard.vcf" );
	print_r($text);
?>