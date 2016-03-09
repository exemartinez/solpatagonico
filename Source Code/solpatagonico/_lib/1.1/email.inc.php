<?
	/**
	* Libreria de Email
	* Utilizada para hacer envios de correo segun estandares.
	*  
	*/
	
	define( 'MAIL_REGEXP', '/^[a-zA-Z0-9]+(([-._][a-zA-Z0-9]+)|([a-zA-Z0-9]*))+\@[a-zA-Z0-9]+(([-._][a-zA-Z0-9]+)|([a-zA-Z0-9]*))+\.[.a-zA-Z0-9]+$/i' );

	class email {
		var $from;				# Remitente
		var $to;				# Destinatario
		var $subject;				# Asunto
		var $html;				# Cuerpo en HTML del mensaje
		var $attach_related;			# Adjuntos relacionados
		var $attach_mixed;			# Adjuntos
		var $text;				# Cuerpo en TXT/PLAIN del mensaje
		var $cc;				# Copias
		var $bcc;				# Copias ocultas
		var $reply_to;				# Direccion de respuestas
		var $nl;				# Impresion de nueva linea
		var $base64;				# Impresion de nueva linea

		function email( $from, $to, $subject, $html='', $attach_related=NULL, $attach_mixed=NULL, $text=NULL, $cc=NULL, $bcc=NULL, $reply_to=NULL, $nl="\n", $base64 = 1, $debug = CFG_debug ) {
			$this->from 			= $from;
			$this->to				= $to;
			$this->subject 			= $subject;
			$this->html				= $html;
			$this->attach_related	= $attach_related;
			$this->attach_mixed		= $attach_mixed;
			$this->text				= $text;
			$this->cc				= $cc;
			$this->bcc				= $bcc;
			$this->reply_to			= $reply_to;
			$this->nl				= $nl;
			$this->base64			= $base64;

			return true;
		} # END function email

		/**
		* is_mail
		* Validacion de email.
		* @author Sebastían Gomila <sgomila@virtualdata.com.ar>
		* @param string $mail es el email a validar.
		* @return booleano.
		*/
		function is_mail( $mail ) {
			if( !is_string( $mail ) ) return false;
			else {
				if ( preg_match( MAIL_REGEXP, $mail ) ) return true;
				else return false;
			}
		} # END function is_mail

		/**
		* encode_iso88591
		* Codificacion del texto.
		* @author Sebastían Gomila <sgomila@virtualdata.com.ar>
		* @param string $string es el texto a codificar.
		* @return string codificado segun iso-8859-1.
		*/
		function encode_iso88591( $string ) {
			$text = '';
			if ( $string != '' ) {
				$text = '=?ISO-8859-1?Q?';
				for ( $i = 0 ; $i < strlen($string) ; $i++ ) {
					$val = ord($string[$i]);
					$val = dechex($val);
					$text .= '='.$val;
				} 
				$text .= '?='; 				
			}
			return $text; 
		} # END function encode_iso88591

		/**
		* getfile
		* Obtiene el binario del descriptor.
		* @author Sebastían Gomila <sgomila@virtualdata.com.ar>
		* @param midex $file descriptor del archivo de lectura.
		* @return binario del descriptor, del archivo a leer.
		*/
		function getfile( $file ) {
			if( file_exists( $file ) ) {
				$fp = fopen( $file, 'r' );
				$blob = fread( $fp , filesize( $file ) );
				fclose( $fp );
				if( $blob ) return $blob;
			}
			return false;
		} # END function getfile

		/**
		* getarray
		* Obtiene el arreglo de mails separados por comas para hacer el envio.
		* @author Sebastían Gomila <sgomila@virtualdata.com.ar>
		* @param mixed $mails arreglo de mails o string con mail.
		* @return string con mails y sus nombres codificados en iso-8859-1.
		*/
		function getarray( $mails ) {
			if ( $mails != '' ) {
				if ( !is_array($mails) ) $ret = ( !Email::is_mail($mails) ? '' : $mails );
				else {
					$ret = "";
					$sep = "";
					foreach( $mails as $name => $mail) {
						if ( Email::is_mail($mail) ) {
							$ret .= $sep.Email::encode_iso88591( ( is_numeric($name) ? $mail : $name ) )." <".$mail.">";
							$sep = ",";
						}
					}
				}
				return $ret;
			}			
		} # END function getarray

		/**
		* send
		* Envio del email.
		* @author Sebastían Gomila <sgomila@virtualdata.com.ar>
		* @param string $from remitente.
		* @param mixed $to destinatario, pueden ser varios pasando un arreglo asociativo.
		* @param string $subject asunto.
		* @param string $html texto del mensaje en formato html.
		* @param array $attach_related arreglo de archivos adjuntos.
		* @param array $attach_mixed arreglo de archivos adjuntos dentro del cuerpo.
		* @param string $texto texto del mensaje en formato texto.
		* @param mixed $cc destinatario, pueden ser varios pasando un arreglo asociativo.
		* @param mixed $bcc destinatario, pueden ser varios pasando un arreglo asociativo.
		* @param string $reply_to email de reply to.
		* @param string $nl salto de linea.
		* @param int $base64 si esta en 1 codifica todo segun base64, 0 no codifica.
		* @param int $debug si esta en 1 envia los emails a CFG_erroMail, 0 envia al destinatario ingresado.
		* @param string $return_path email de return path.
		* @return booleano.
		*/
		function send(
				$from = NULL,
				$to = NULL,
				$subject = NULL,
				$html='',
				$attach_related = NULL,
				$attach_mixed = NULL,
				$text = NULL,
				$cc = NULL,
				$bcc = NULL,
				$reply_to = NULL,
				$nl = "\n",
				$base64 = 1,
				$debug = CFG_debug,
				$return_path = NULL
			) {

			# Validaciones
			if ( !Email::is_mail($from) ) return false;
			$reply_to = ( $reply_to ? $reply_to : $from );
			$return_path = ( $return_path ? $return_path : $reply_to );		
			if ( !Email::is_mail($reply_to) ) return false;
			if ( !Email::is_mail($return_path) ) return false;

			# Parseamos por si hay arreglos
			$to = Email::getarray( $to );

			# Validaciones de los arreglos
			if ( $to == "" ) return false;
			if ( $cc != "" ) {
				$cc = Email::getarray( $cc );
				if ( $cc == "" ) return false;
			}
			if ( $bcc != "" ) {
				$bcc = Email::getarray( $bcc );
				if ( $bcc == "" ) return false;
			}

			$boundary = uniqid( rand() );	

			# Armamos cabeceras de attachments
			$mixed = false;
			if (is_array ($attach_related)) {
				$mixed = true;
				foreach ( $attach_related as $attach ) {
					if ( file_exists( $attach['tmp_name'] ) ) {
						$att_rel .= "--".$boundary.$nl;
						$att_rel .= "Content-Type: ".$attach['type'].";".$nl;
						$att_rel .= "\t"."name=\"".$attach['name']."\"".$nl;
						$att_rel .= "Content-Transfer-Encoding: base64".$nl;
						$att_rel .= "Content-ID: <".$attach['name'].">".$nl;
						$att_rel .= $nl.chunk_split( ($base64 ? base64_encode(email::getfile($attach['tmp_name'])) : email::getfile($attach['tmp_name'])) ).$nl;
					} elseif ($attach['bin'] != '' && $attach['bin'] !== NULL) {
						$att_rel .= "--".$boundary.$nl;
						$att_rel .= "Content-Type: ".$attach['type'].";".$nl;
						$att_rel .= "\t"."name=\"".$attach['name']."\"".$nl;
						$att_rel .= "Content-Disposition: attachment;filename=".$attach['name']."\"".$nl;
						$att_rel .= "Content-Transfer-Encoding: base64".$nl;
						$att_rel .= "Content-ID: <".$attach['name'].">".$nl;
						$att_rel .= $nl.chunk_split( ($base64 ? base64_encode($attach['bin']) : $attach['bin']) ).$nl;					
					}
				}
			}

			if (is_array ($attach_mixed)) {
				$mixed = true;
				foreach ( $attach_mixed as $attach ) {
					if ( file_exists( $attach['tmp_name'] ) ) {
						$att_mixed .= "--".$boundary.$nl;
						$att_mixed .= "Content-Type: ".$attach['type'].";".$nl."\t"."name=\"".$attach['name']."\"".$nl;
						$att_mixed .= "Content-ID: <".$attach['name'].">".$nl;
						$att_mixed .= "Content-Transfer-Encoding: base64".$nl.$nl;
						$att_mixed .= chunk_split( ($base64 ? base64_encode(email::getfile($attach['tmp_name'])) : email::getfile($attach['tmp_name'])) ).$nl;
					}
				}
			}

			$sub_boundary = $mixed ? uniqid( rand() ) : $boundary;
			
			# Armamos cabeceras de texto / html
			if( $mixed ) {
				$msg = "--".$boundary.$nl."Content-Type: multipart/alternative;".$nl;
				$msg .= "\t"."boundary=\"$sub_boundary\"".$nl.$nl;
			}
			$msg .= "--".$sub_boundary.$nl."Content-Type: text/plain".$nl;
			$msg .= "Content-Transfer-Encoding: quoted-printable".$nl.$nl;
			$msg .= chunk_split( $text ? $text : 'Este mensaje debe visualizarse con un cliente de correo electronico que soporte HTML.' ).$nl.$nl;
 			if ( $html != '' ) {
				$msg .= "--".$sub_boundary.$nl."Content-Type: text/html; charset=\"ISO-8859-1\"".$nl;
				$msg .= "Content-Transfer-Encoding: base64".$nl;
				$msg .= $nl.chunk_split( ($base64 ? base64_encode($html) : $html) ).$nl.$nl;
			}
			if( $mixed ) {
				$msg .= "--".$sub_boundary."--".$nl.$nl;
			}

			$msg = $msg.$att_rel.$att_mixed.$nl."--".$boundary."--";

			# Armamos cabeceras de datos
			$headers = "";				
			$headers .= "From: ".$from.$nl;
			$headers .= "Reply-To: ".$reply_to.$nl;	
			$headers .= "Return-Path: <".$return_path.">".$nl;
			$headers .= "MIME-Version: 1.0".$nl;
			$headers .= "Content-Type: multipart/".( $mixed ? 'mixed' : 'alternative' ).";".$nl."\t";
			if( $mixed ) {
				$headers .= "type=\"multipart/alternative\";".$nl."\t";
			}
			$headers .= "boundary=\"".$boundary."\"".$nl.$nl;
			$headers .= "This is a multi-part message in MIME format.".$nl.$nl;

			# Envio del mail
			if ( mail( ($debug ? CFG_mailError : $to), email::encode_iso88591($subject), $msg, $headers ) ) return true;
			else return false;
		} #END Function send
	}
?>