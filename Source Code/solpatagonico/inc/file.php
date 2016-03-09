<?
	define( 'authorize', false );
	define( 'authenticate', false );
	include_once( '../inc/conf.inc.php' );
	
	# Globalizamos las variables
	registrar( "id, section, name, download, id_name" );
	
	if( !$id_name ) $id_name = 'id';

    if( $id != '' && $section != '' && $name != '' ){
		$MIME = $name.'_mime';
		$NAME = $name.'_name';
		$row = $db->GetRow( "SELECT * FROM ".$section." WHERE ".$id_name." = '".$id."'" );	
		if ( $download ) {
			header( "Pragma: public" );
			header( "Cache-control: private" );
			header( "Content-Type: application/force-download" );
			header( "Content-Disposition: attachment;filename=".$row[$NAME] );
		} else {
			header( 'Content-Disposition: inline;filename="'.$row[$NAME].'" ' );
			header( "Content-Type: ".$row[$MIME] );
		}
		$rs = $db->Execute( "SELECT ".$name." FROM ".$section." WHERE ".$id_name." = '".$id."'" );	
		echo $db->BlobDecode( reset($rs->fields) );
		flush();
    }
?>
