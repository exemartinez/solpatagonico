<?
	/**
	* byteFormat
	* Convierte una cantidad expresada en bytes a B, KB, MB, GB, TB según corresponda.
	*  
	* @param string $cant es el texto a formatear.
	* @return el texto en el formato.
	*/
	function byteFormat($cant){
		if($cant<1024) return str_replace('.00', '', sprintf("%01.2f B", $cant));
		elseif($cant<pow(1024, 2)) return str_replace('.00', '', sprintf("%01.2f KB", $cant/1024));
		elseif($cant<pow(1024, 3)) return str_replace('.00', '', sprintf("%01.2f MB", $cant/pow(1024, 2)));
		elseif($cant<pow(1024, 4)) return str_replace('.00', '', sprintf("%01.2f GB", $cant/pow(1024, 3)));
		else return str_replace('.00', '', sprintf("%01.2f TB", $cant/pow(1024, 4)));		
	} # END function byteFormat
		
	/**
	* array_average
	* Obtiene el promedio del arreglo.
	*  
	* @param mixed $array es el arreglo a recorrer.
	* @param boolean $countZero si es TRUE incluye la clave 0 (cero), en FALSE no.
	* @return el promedio del arreglo.
	*/
	function array_average($array, $countZero=TRUE){
		if(is_array($array)){
			$total=array_sum($array);
			$elements=0;
				
			if($countZero){
				$elements=count($array);
				return $elements>0?$total/$elements:0;
			} else {
				foreach($array as $value) if($value>0) $elements++;
				return $elements>0?$total/$elements:0;
			}
		} else return False;
	} # END function array_average
		
	/**
	* script_download
	* Imprime las funciones javascript necesarias para trabajas con el download binding/behavior. El resultado incluira un espacio en blanco como primer caracter, que es necesario para inicializar el buffer y evitar que se cuelge el download behavior. Puede eliminarse con este codigo en javascript: "resultado=resultado.replace(/^\s/,'');"
	*  
	*/
          function script_download(){
               ?>
                    <script type="text/javascript" defer>
                         try{
                              Event.prototype.__defineGetter__( "srcElement", function(){ var src = this.target; if( src.nodeType == Node.TEXT_NODE ) src = src.parentNode; return src; } );
                              Node.prototype.attachEvent = function( eventType, notify ){
                                   this.addEventListener( eventType.replace(/^on/, ""), notify, false );
                              }
                         }catch(err){
                         }

                         function bajar( archivo, funcion, tipo){
                              DownloadElement.CancelLoading();
                              if( tipo=='asincronico'){
                                   DownloadElement.LoadText( archivo, true, funcion);
                              }else{
                                   return funcion( DownloadElement.LoadText( archivo, false));
                              }
                         }

                         function mostrar_texto( resultado ){
                              document.getElementById('texto').innerHTML=resultado;
                         }
                    </script>
                    <span style="-moz-binding: url('<?= CFG_virtualIncPath ?>download.xml#download');behavior: url('<?= CFG_virtualIncPath ?>download.htc');" ondownloadready="DownloadElement = this; try{ init_download() }catch(err){}" ondownloadnotsupported="alert('Este navegador no soporta bajar archivos en segundo plano');">
                    </span>
               <?
          }
		  
	/**
	* print_error
	* Imprime un mensaje de error.
	*  
	* @param string $error es el texto de error a imprimir.
	*/
	function print_error($error){
		print '<br><span bgcolor="#FFFFFF"><font color="#FF0000"><b>Error:</b> '.$error.'.</font></span><br>';
		exit();
	} # END function print_error
	
	/**
	* getfile
	* Obtiene el contenido de un archivo dado.
	*  
	* @param mixed $file es el descriptor del archivo a leer.
	* @return false sino existe el archivo, sino el archivo.
	*/
	function getfile( $file ){
		if( file_exists( $file ) ){
			$fp = fopen( $file, 'r' );
			$blob = fread( $fp , filesize( $file ) );
			fclose( $fp );
			if( $blob ) return $blob;
		}
		return false;
	} # END function getfile

	/**
	* comboCountries
	* Imprime el combo de paises.
	*  
	* @param string $id es el nombre del combo.
	* @param string $selected es el nombre del pais seleccionado.
	* @param string $style es el estilo CSS aplicado.
	* @param string $html campo para agregar configuraciones adicionales, como ser size, multiple, etc.
	*/
	function comboCountries( $id, $selected = '', $style = '', $html = ''){
		$countries = new DBI('powerlib','powerlib','PowerLibPasswd','localhost');
		$countries->select( 'countries', 'name' );
		?>
		<select name="<?= $id?>" id="<?= $id?>" style="<?= $style?>" <?= $html?> >
		<?
		while( $country = $countries->fetch_object()){
		?>
			<option value="<?= $country->name?>" <?= $selected == $country->name ? 'selected' : '' ?>><?= $country->name?></option>
		<?
		}
		?>
		</select>
		<?
	} # END function comboCountries

	/**
	* ifn
	* Si la variable no esta seteada le asigna el valor dado.
	*  
	* @param mixed $variable es la variable a setar.
	* @param mixed $valor es el valor a setar en $variable si es NULL.
	*/
	function ifn( &$variable, $valor ){
		if( $variable==NULL ) $variable = $valor;
	} # END function ifn

	/**
	* ifs
	* Si la variable es string vacio le asigna el valor dado.
	*  
	* @param mixed $variable es la variable a setar.
	* @param mixed $valor es el valor a setar en $variable si esta vacia.
	*/
	function ifs( &$variable, $valor ){
		if( $variable == '' ) $variable = $valor;
	} # END function ifs

	/**
	* printif
	* Si la variable es string vacio devuelve el valor dado.
	*  
	* @param string $variable es la variable a validar.
	* @param string $valor es el valor de retorno si $variable es igual a $vacio.
	* @param string $vacio es el valor para validar $variable.
	* @return se devuelve $valor si $variable es igual a $vacio sino se devuelve $variable.
	*/
	function printif( $variable, $valor='&nbsp;', $vacio='' ){
		if( $variable == $vacio ) return $valor;
		return $variable;
	} # END function printif

	/**
	* in
	* Retorna TRUE si el valor de $variable es igual a uno de los otros argumentos.
	*  
	* @param mixed $variable es el valor a validar.
	* @return devuelve TRUE si $variable esta dentro de los argumentos.
	*/
	function in(&$variable){
		$args=func_get_args();
		for($i=1; $i<sizeof($args); $i++){
			if($variable==$args[$i]) return true;
		}
		return false;
	} # END function in

	/**
	* increment
	* Incrementa la "variable" en "step" desde "n" hasta "m".
	*  
	* @param mixed $variable es el valor a incrementar.
	* @param int $n es el valor de inicio.
	* @param int $m es el valor de fin.
	* @param int $step es el valor de salto.
	*/
	function increment( &$variable, $n, $m = null, $step = 1 ){
		$variable+=$step;
		if( $m ){
			if( $variable > $m ){
				$variable = $n;
			}
		}
		elseif( $variable > $n ){
			$variable = $n;
		}
	} # END function increment

	/**
	* remote_file_exists
	* Chequea si el archivo remoto existe.
	*  
	* @param mixed $url el archivo a chequear.
	* @return 1 si es invalido el HOST, 2 sino se puede conectar, true si lo encuentra y false sino.
	*/
	function remote_file_exists ($url){ 

		$head = ""; 
		$url_p = parse_url ($url); 
		   
		if (isset ($url_p["host"])) $host = $url_p["host"];
		else return 1;
		   
		if (isset ($url_p["path"])) $path = $url_p["path"];
		else $path = "";
		   
		$fp = fsockopen ($host, 80, $errno, $errstr, 20); 
		if (!$fp) return 2;
		else { 
			$parse = parse_url($url); 
			$host = $parse['host']; 
			   
			fputs($fp, "HEAD ".$url." HTTP/1.1\r\n"); 
			fputs($fp, "HOST: ".$host."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			$headers = ""; 
			while (!feof ($fp)) $headers .= fgets ($fp, 128); 
		} 
		fclose ($fp); 
		$arr_headers = explode("\n", $headers); 
		$return = false; 
		if (isset ($arr_headers[0])) $return = strpos ($arr_headers[0], "404") === false;
		return $return; 
	} # END function remote_file_exits

	/**
	* str2tel
	* Convierte una cadena a su equivalente en el teclado telefonico.
	*  
	* @param string $txt es el texto a parsear.
	* @return false sino es un texto o esta vacio, sino el nro telefonico.
	*/
	function str2tel( $txt ) {
		if ( $txt != '' && !eregi( '[^a-z0-9]+', $txt ) ) {
			$num_txt = $txt;
			$num_txt = eregi_replace( '[abc]', '2', $num_txt );
			$num_txt = eregi_replace( '[def]', '3', $num_txt );
			$num_txt = eregi_replace( '[ghi]', '4', $num_txt );
			$num_txt = eregi_replace( '[jkl]', '5', $num_txt );
			$num_txt = eregi_replace( '[mno]', '6', $num_txt );
			$num_txt = eregi_replace( '[pqrs]', '7', $num_txt );
			$num_txt = eregi_replace( '[tuv]', '8', $num_txt );
			$num_txt = eregi_replace( '[wxyz]', '9', $num_txt );
		return $num_txt;
		} else return false;
	} # END function str2tel

	function registrar( $variables ) {
	    $variables = explode( ',', $variables );
	    foreach( $variables as $var ) {
			$var = trim( $var );
			global ${$var};
			${$var} = isset($_POST[$var]) ? $_POST[$var] : ( isset($_GET[$var]) ? $_GET[$var] : ${$var} );
	    }
	}
	
	function registrar_metodo( $method, $variables ) {
	    $variables = explode( ',', $variables );
	    foreach( $variables as $var ) {
			global ${$var};
			eval( "\$$var = \$_".strtoupper( $method )."['$var'];" );
	    }
	}
	
	function prepare_var( $var ){
		if( get_magic_quotes_gpc() ) $var = stripslashes( $var );
		$var = htmlentities( $var, ENT_QUOTES );
		return $var;
	}

	function gen_record( $variables, $tolower = true ){
	    $variables = explode( ',', $variables );
	    foreach( $variables as $var ) {
			$var = trim( $var );
			global ${$var};
			$record[ ( $tolower ? strtolower($var) : strtoupper($var) ) ] = ( !is_array( ${$var} ) ? html_entity_decode( ${$var} ) : ${$var} );
	    }
		return $record;
	}
	
	function get_sql( $accion, $tabla, $campos, $where ){
		$sep = "";
		$campos_nombres = "";
		$campos_valores = "";
		$campos_update = "";
		foreach ( $campos as $campo_nombre => $campo_valor ){
			$campos_nombres .= $sep.$campo_nombre;
			$campos_valores .= $sep."'".$campo_valor."'";
			$campos_update .= $sep.$campo_nombre." = '".$campo_valor."'";			
			$sep = ",";
		}
		switch ( strtoupper( $accion ) ) {
			case 'INSERT':
				return "INSERT INTO ".$tabla." (".$campos_nombres.") VALUES (".$campos_valores.")".( $where != "" ? " WHERE ".$where : "" ); 
				break;
			case 'UPDATE':
				return "UPDATE ".$tabla." SET ".$campos_update.( $where != "" ? " WHERE ".$where : "" ); 
				break;				
		}
	}
	
	function array2object( $array ){
		if( is_array( $array ) && sizeof( $array ) > 0 ){
			foreach( $array as $key => $value ){
				$obj->{$key} = $value;
			}
		} else {
			$obj = false;
		}
		return $obj;
	}
?>