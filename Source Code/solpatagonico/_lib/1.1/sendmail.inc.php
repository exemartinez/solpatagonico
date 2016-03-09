<?
	# Resposable: Alejandro Quiroga as1@powersite.com.ar
	
	if( !function_exists( 'getfile' ) )
	{
		function getfile( $file )
		{
			if( file_exists( $file ) )
			{
				$fp = fopen( $file, 'r' );
					$blob = fread( $fp , filesize( $file ) );
				fclose( $fp );
				//$blob = addslashes( $blob );
				if( $blob ){
					return $blob;
				}
			}
			return false;
		}
	}

	function encode_iso88591($string) {
		$text = '';
		if ($string != '') {
			$text = '=?iso-8859-1?q?';
			for ( $i = 0 ; $i < strlen($string) ; $i++ ) {
				$val = ord($string[$i]);
				$val = dechex($val);
				$text .= '='.$val;
			} 
			$text .= '?='; 				
		}
		return $text; 
	} # END Function encode_iso88591
	
	function sendmail(
					$from,
					$to,
					$subject,
					$html,
					$text = null,
					$reply_to = null,
					$return_path = null,
					$attachs = null,
					$attach_type = 'related',
					$nl = "\n"
				)
	{
		$boundary = md5( rand() );
		$sub_boundary = md5( rand() );
		//$nl = "\n";
		
		$msg = "--".$boundary.$nl."Content-Type: multipart/alternative;".$nl."\tboundary=\"$sub_boundary\"".$nl.$nl;
		$msg .= "--".$sub_boundary.$nl."Content-Type: text/plain".$nl;
		$msg .= "Content-Transfer-Encoding: quoted-printable".$nl.$nl;
		$msg .= imap_8bit( ( $text ? $text : 'Este mensaje debe verse con un programa de correo electronico con soporte HTML' ) ).$nl.$nl;
		$msg .= "--".$sub_boundary.$nl."Content-Type: text/html; charset=\"iso-8859-1\"".$nl.$nl;
		//$msg .= "Content-Transfer-Encoding: quoted-printable$nl".$nl;
		$msg .= $html.$nl.$nl; // imap_8bit
		$msg .= "--".$sub_boundary."--".$nl.$nl;
		
		if( is_array( $attachs ) )
		{
			foreach( $attachs as $attach )
			{
				//$msg .= var_export( $attach, 1 );
				if( is_uploaded_file( $attach['tmp_name'] ) )
				{
					$msg .= "--".$boundary.$nl;
					$msg .= "Content-Type: ".$attach['type'].";".$nl."\tname=\"".$attach['name']."\"".$nl;
					$msg .= "Content-Disposition: ".( $attach_type == 'mixed' ? 'attachment; ' : '' )."filename=".$attach['name'].$nl;
					$msg .= "Content-ID: <".$attach['name'].">".$nl;
					$msg .= "Content-Transfer-Encoding: base64".$nl.$nl;
					$msg .= chunk_split( base64_encode( getfile( $attach['tmp_name'] ) ) ).$nl;
				}
			}
		}
		$msg .= $nl."--".$boundary."--";
		
		$reply_to = ( $reply_to ? $reply_to : $from );
		$return_path = ( $return_path ? $return_path : $reply_to );

		list( $from_user, $from_domain ) = explode( '@', is_array( $from ) ? $from[1] : $from, 2 );
		list( $to_user, $to_domain ) = explode( '@', is_array( $to ) ? $to[1] : $to, 2 );
		list( $reply_to_user, $reply_to_domain ) = explode( '@', is_array( $reply_to ) ? $reply_to[1] : $reply_to, 2 );
		list( $return_path_user, $return_path_domain ) = explode( '@', is_array( $return_path ) ? $return_path[1] : $return_path, 2 );
		
		$ok = mail(
			is_array( $to ) ? $to[1] : $to,
			encode_iso88591( $subject ),//*/"=?ISO-8859-1?Q?".imap_utf7_encode( $subject )."?=",
			$msg,
			/* "To: ".imap_rfc822_write_address( $to_user, $to_domain, ( is_array( $to ) ? $to[0] : $to ) )."$nl".*/
			"From: ".imap_rfc822_write_address( $from_user, $from_domain, is_array( $from ) ? $from[0] : $from ).$nl.
			"Reply-To: ".imap_rfc822_write_address( $reply_to_user, $reply_to_domain, is_array( $reply_to ) ? $reply_to[0] : $reply_to ).$nl.
			"Return-Path: ".imap_rfc822_write_address( $return_path_user, $return_path_domain, is_array( $return_path ) ? $return_path[0] : $return_path ).$nl.
			"MIME-Version: 1.0".$nl.
			"Content-Type: multipart/$attach_type;".$nl."\t".
			"type=\"multipart/alternative\";".$nl."\t".
			"boundary=\"$boundary\"".$nl.$nl.
			"This is a multi-part message in MIME format.".$nl.$nl
		);
		return $ok;
	}
?>
