<?php
	# Codifica cualquier texto pasado, lo valida e incrementa para que sea unico
	function seo_encode( $original, $table = '', $field = '', $id = '', $key_field = 'id' ){
	    global $db;

		$letras = "a-z";
		$digitos = "0-9";
		$guion = "\\-";
		
		$code = html_entity_decode( $original );
		
		$replace = array(
			"Á|á|À|à|Ä|ä|Â|â|Ã|ã" => "a",
			"É|é|È|è|Ë|ë|Ê|ê" => "e",
			"Í|í|Ì|ì|Ï|ï|Î|î" => "i",
			"Ó|ó|Ò|ò|Ö|ö|Ô|ô|Õ|õ" => "o",
			"Ú|ú|Ù|ù|Ü|ü|Û|û" => "u",
			"Ç|ç" => "c",
			"Ñ|ñ" => "n"
		);
		foreach( $replace as $key => $value ){
			$code = preg_replace( "/[".$key."]/i", $value, $code );
		}
		$code = strtolower( $code );
		$code = preg_replace( "/[^".$letras.$digitos.$guion.$guion_bajo.$punto."]/i", '-', $code );
		$code = preg_replace( "/\\-\\-+/", "-", $code );
		$code = substr( $code, strlen( $code )-1 ) == '-' ? substr( $code, 0, strlen( $code )-1 ) : $code;
		
		if( $table != '' && $field != '' && $key_field != '' ){
			$count = 0;
			$exit = false;
			while( !$exit ){
				$check = $code.( $count > 0 ? $count : '' );
				$rs = $db->execute( "SELECT * 
					FROM ".$table." 
					WHERE ".$field." LIKE '".$check."' ".
					( $id != '' ? " AND ".$key_field." != '".$id."' " : "" )
				);
				if( $rs->recordCount() ){
					$count++;
					$exit = false;
				} else {
					$code = $code.( $count > 0 ? $count : '' );
					$exit = true;
				}
			}
		}
	    return $code;
	}
?>