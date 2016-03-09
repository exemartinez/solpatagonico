<?
	/**
	* Libreria de Validacion
	* Permite validar datos
	*  
	*/
	
	class Validate {
		var $altText = "Debe ingresar un valor";			# Valor por defecto de los errores si no se setea
		var $genText = "Hubo errores en el formulario";			# Valor por defecto sin mostrar los errores
		var $errVault = array();					# Arreglo donde se guardan los textos de los errores de cada validacion

		/**
		* validate
		* Constructor de validacion.
		*  
		* @param string $altText es el texto de error por defecto.
		* @param string $getText es el texto de error por defecto general.
		*/
		function validate (
			$altText = "Debe ingresar un valor",			# Valor por defecto de los errores si no se setea
			$genText = "Hubo errores en el formulario"		# Valor por defecto sin mostrar los errores
			){
			$this->altText = $altText;
			$this->genText = $genText;
		} # END function pager		
		
		/**
		* Telefono
		* Validacion de Telefono. Acepta solo numeros y guines. En caso de error ingresa un texto al errVault.
		* @author Sebastián Gomila <sgomila@virtualdata.com.ar>
		* @param date $telefono es el telefono a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Telefono( $telefono, $altText = '', $varName = '', $requerido = 1 ) {
			if( $requerido || ( !$requerido && $telefono != '' ) ) {
				if( ereg( "^[0-9_\-]+$", $telefono ) ) return true;
				else {
				    if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				}
			} else return true;
		} # END CUIT

		/**
		* CUIT
		* Validacion de CUIT. En caso de error ingresa un texto al errVault.
		*  
		* @param date $cuit es el cuit a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function CUIT( $cuit, $altText = '', $varName = '', $requerido = 1 ) {
			if( $requerido || ( !$requerido && $cuit != '' ) ) {
				$coeficiente[0]=5;
				$coeficiente[1]=4;
				$coeficiente[2]=3;
				$coeficiente[3]=2;
				$coeficiente[4]=7;
				$coeficiente[5]=6;
				$coeficiente[6]=5;
				$coeficiente[7]=4;
				$coeficiente[8]=3;
				$coeficiente[9]=2;

				$resultado = 1;
				for( $i = 0; $i < strlen( $cuit ); $i++ ){
					# separo cualquier caracter que no tenga que ver con numeros
					if( ( Ord( substr( $cuit, $i, 1 ) ) >= 48 ) && ( Ord( substr( $cuit, $i, 1 ) ) <= 57 ) ){
						$cuit_rearmado = $cuit_rearmado . substr( $cuit, $i, 1 );
					}
				}
				
				if( strlen( $cuit_rearmado ) <> 11 ){ 
					# si to estan todos los digitos
					$resultado = 0;
				} else {
					$sumador = 0;
					# tomo el digito verificador
					$verificador = substr( $cuit_rearmado, 10, 1 );
	
					for( $i = 0; $i<= 9; $i++ ){ 
						# separo cada digito y lo multiplico por el coeficiente
						$sumador = $sumador + ( substr( $cuit_rearmado, $i, 1 ) ) * $coeficiente[$i]; 					
					}
	
					$resultado = $sumador % 11;
					# saco el digito verificador
					$resultado = 11 - $resultado;
					$veri_nro = intval( $verificador );
	
					if( $veri_nro <> $resultado ){
						$resultado = 0;
					}
				}

				if ( $resultado ) return true;
				else {
				    if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				}
			} else return true;
		} # END CUIT
		
		/**
		* Date
		* Validacion de fecha. En caso de error ingresa un texto al errVault.
		*  
		* @param date $fecha es la fecha a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Date( $fecha, $altText = '', $varName = '', $requerido = 1 ) {
			if( $requerido || ( !$requerido && $fecha != '' ) ) {
				list( $a , $m , $d ) = split( '-|/' , $fecha );
				if ( checkdate( intval($m) , intval($d) , intval($a) ) ) return true;
				else {
				    if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				}
			} else return true;
		} # END Date		

		/**
		* ISO2Timestamp
		* Conversión de fecha ISO a Timestamp.
		*  
		* @param date $fecha es la fecha a formatear.
		* @return fecha en formato Timestamp.
		*/
		function ISO2Timestamp( $fecha ) {
			if( Validate::Date( $fecha ) ){
				list( $a , $m , $d ) = split( '-|/' , $fecha , 3 );
				return mktime(0,0,0,$m,$d,$a);
			}
			return false;
		} # END ISO2Timestamp

		/**
		* Bisiesto
		* Validacion de bisiesto. En caso de error ingresa un texto al errVault.
		*  
		* @param date $fecha es la fecha a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Bisiesto( $fecha, $altText = '', $varName = '', $requerido = 1 ) {
			if ($requerido || ( !$requerido && Validate::Date($fecha) )) {
				if ( ( (substr($fecha,0,4) % 4 == 0) && (substr($fecha,0,4) % 100 != 0) ) || (substr($fecha,0,4) % 400 == 0) ) {
					return true;
				} else {
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				}
			} else return true;
		} # END Bisiesto

		/**
		* Hour
		* Validacion de hora. En caso de error ingresa un texto al errVault.
		*  
		* @param time $hour es la hora a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Hour( $hour, $altText = '', $varName = '', $requerido = 1){
			$hour_regexp = '/^[0-9]+$/i';
			
			if ( $requerido || ( !$requerido && $hour != "" ) ){
				$aux = split("[.:]",$hour);
				if (
					!preg_match($hour_regexp, $aux[0]) ||
					!preg_match($hour_regexp, $aux[1]) ||
					strlen($aux[0])>2 || 
					intval($aux[0])>23 || 
					intval($aux[0])<0 ||
					strlen($aux[1])>2 || 
					intval($aux[1])>59 || 
					intval($aux[1])<0
				){
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				} else return true;
			} else return true;
		} # END Hour

		/**
		* Date
		* Validacion de fecha. En caso de error ingresa un texto al errVault.
		*  
		* @param date $fecha es la fecha a validar.
		* @param date $minimo es la fecha minima para validar $fecha.
		* @param date $maximo es la fecha maxima para validar $fecha.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @param int $igual en caso de valor 1 se chequea a menor o mayor e igual, en 0 no se chequea a menor o mayor solamente.
		* @return booleano.
		*/
		function DateRange( $fecha, $minimo = '', $maximo = '', $altText = '', $varName = '', $requerido = 1, $igual = 1 ) {
			if( $requerido || ( !$requerido && $fecha != "" ) ) {
				if ( !Validate::Date($fecha) ) { # Validamos si $fecha es una fecha valida
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
				    return false;
				} elseif ( # Validamos con ambos extremos
					$minimo != "" &&
					$maximo != "" && 
					(
						(
							!Validate::Date($minimo) || 
							!Validate::Date($maximo)
						) ||
						(
							( Validate::ISO2Timestamp( $fecha ) < Validate::ISO2Timestamp( $minimo ) && $igual ) ||
							( Validate::ISO2Timestamp( $fecha ) <= Validate::ISO2Timestamp( $minimo ) && !$igual )
						) || 
						(
							( Validate::ISO2Timestamp( $fecha ) > Validate::ISO2Timestamp( $maximo ) && $igual ) ||
							( Validate::ISO2Timestamp( $fecha ) >= Validate::ISO2Timestamp( $maximo ) && !$igual )
						)
					)
				) {
				    if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
				    return false;
				} elseif ( # Validamos solo con minimo
					$minimo != "" && 
					$maximo == "" &&
					(
						!Validate::Date($minimo) || 
						( Validate::ISO2Timestamp( $fecha ) < Validate::ISO2Timestamp( $minimo ) && $igual ) ||
						( Validate::ISO2Timestamp( $fecha ) <= Validate::ISO2Timestamp( $minimo ) && !$igual )
					)
				) {
				    if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
				    return false;
				} elseif ( # Validamos solo maximo
					$minimo == "" &&
					$maximo != "" && 
					(
						!Validate::Date($maximo) || 
						( Validate::ISO2Timestamp( $fecha ) > Validate::ISO2Timestamp( $maximo ) && $igual ) ||
						( Validate::ISO2Timestamp( $fecha ) >= Validate::ISO2Timestamp( $maximo ) && !$igual )
					)
				) {
				    if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
				    return false;
				} else return true;
			} else return true;
		} # END DateRange	

		/**
		* Text
		* Validacion de texto. En caso de error ingresa un texto al errVault.
		*  
		* @param string $valor es el texto a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $longitud es el numero minimo de caracteres del texto.
		* @param int $maxlongitud es el numero maximo de caracteres del texto.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Text( $valor, $altText = '', $varName = '', $longitud = 1, $maxlongitud = 0, $requerido = 1 ){
			$valor = trim( $valor );
			if ( 
				( 
				    $requerido && 
				    ( 
					    $valor == "" || 
					    strlen($valor) < $longitud || 
					    ( strlen($valor) > $maxlongitud && $maxlongitud > 0 ) 
				    )
				) || 
				( 
				    !$requerido && 
				    (
					( strlen($valor) < $longitud && $longitud > 0 ) ||
					( strlen($valor) > $maxlongitud && $maxlongitud > 0 )
				    )
				) 
			    ) {
				if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
				}
				return false;
			} else return true;
		} # END Text
		
		/**
		* Equal
		* Validacion de 2 variables, para igualdad. En caso de error ingresa un texto al errVault.
		*  
		* @param string $valor1 es el texto a comparar con $valor2.
		* @param string $valor2 es el texto a comparar con $valor1.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Equal( $valor1, $valor2, $altText = '', $varName = '' ) {
			if ( $valor1 != $valor2 ) {
				if( $this ){
					if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
				}
				return false;
			} else return true;
		} # END Equal
		
		/**
		* Email
		* Validacion de email. En caso de error ingresa un texto al errVault.
		*  
		* @param string $email es el email a validar.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @return booleano.
		*/
		function Email( $mail, $altText = '', $varName = '', $requerido = 1 ) {
			$mail_regexp = '/^[a-zA-Z0-9]+(([-._][a-zA-Z0-9]+)|([a-zA-Z0-9]*))+\@[a-zA-Z0-9]+(([-._][a-zA-Z0-9]+)|([a-zA-Z0-9]*))+\.[.a-zA-Z0-9]+$/i';

			if( 
			    ( $requerido && !preg_match( $mail_regexp, $mail ) ) || 
			    ( !$requerido && $mail != "" && !preg_match( $mail_regexp, $mail )  ) 
			) {
				if( $this ){
					if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
				}
				return false;
			} else return true;
		} # END Email

		/**
		* Integer
		* Validacion de entero. En caso de error ingresa un texto al errVault.
		*  
		* @param string $valor es el texto a validar.
		* @param date $minimo es el entero minima para validar $valor.
		* @param date $maximo es el entero maximo para validar $valor.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @param int $igual en caso de valor 1 se chequea a menor o mayor e igual, en 0 no se chequea a menor o mayor solamente.
		* @return booleano.
		*/
		function Integer( $valor, $minimo = '', $maximo = '', $altText = '', $varName = '', $requerido = 1, $igual = 1 ){
			$int_regexp = '/^[0-9]+$/i';

			if ( $requerido || 
				( !$requerido && $valor != "" ) 
			) {
				if ( 
					$valor == "" || 
					!preg_match( $int_regexp, $valor ) ||
					!is_numeric( $valor )
				) {	# Chequeamos que $value sea un entero no nulo
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				} elseif ( # $minimo y $maximo pasados por parametro
					$minimo != "" && 
					$maximo != "" && 
					(
						(
							!is_numeric( $minimo ) ||
							!is_numeric( $maximo ) ||
							!preg_match( $int_regexp, $minimo ) ||							# $minimo no nulo y entero
							!preg_match( $int_regexp, $maximo ) ||							# $maximo no nulo y entero
							intval($minimo) >= intval($maximo)								# $minimo menor a $maximo
						) || 
						( 
							(
								( intval($valor) < intval($minimo) && $igual ) || 				# $valor menor o igual a $minimo ya que $igual=1
								( intval($valor) <= intval($minimo) && !$igual )				# $valor menor a $minimo ya que $igual=0
							) || 
							(
								( intval($valor) > intval($maximo) && $igual ) ||				# $valor mayor o igual a $maximo ya que $igual=1
								( intval($valor) >= intval($maximo) && !$igual )				# $valor mayor a $maximo ya que $igual=0
							)
						)
					)
				) { # Chequeamos si los $minimo y $maximo son enteros no nulos y que $valor este entre ellos
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;					
				} elseif ( # Solo $minimo pasado por parametro
					$minimo != "" &&
					$maximo == "" &&
					(
						!is_numeric( $minimo ) ||
						!preg_match( $int_regexp, $minimo ) ||
						( intval($valor) < intval($minimo) && $igual ) ||
						( intval($valor) <= intval($minimo) && !$igual )
					)
				) { # Chequeamos que $minimo sea entero no nulo y $valor sea mayor $minimo
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				} elseif ( # Solo $maximo pasado por parametro
					$minimo == "" &&
					$maximo != "" &&					
					(
						!is_numeric( $maximo ) ||
						!preg_match( $int_regexp, $maximo ) ||
						( intval($valor) > intval($maximo) && $igual ) ||
						( intval($valor) >= intval($maximo) && !$igual )
					)
				) { # Chequeamos que $maximo sea entero no nulo y $valor sea menor $maximo
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				}			
				else return true;
			} else return true;
		} # END Integer

		/**
		* Convert2Float
		* Reemplaza las , por . para el ISO de float de MySQL.
		*  
		* @param string $valor es el texto a convertir.
		* @return la variable convertida a ISO de float de MySQL.
		*/
		function Convert2Float( $valor ){
			if ( $valor != "" ) return str_replace(",", ".", $valor);
		} # END Convert

		/**
		* Float
		* Validacion de flotante o decimal. En caso de error ingresa un texto al errVault.
		*  
		* @param string $valor es el texto a validar.
		* @param date $minimo es el entero minima para validar $valor.
		* @param date $maximo es el entero maximo para validar $valor.
		* @param string $altText es el texto de error para no utilizar el texto por defecto.
		* @param string $varName es el nombre o titulo de la variable a validar para relacionarlo con el error.
		* @param string $format es el formato de entrada para cantidad de enteros y decimales en mantisa. Ejemplo: 10,2. 10 enteros, 2 decimales.
		* @param int $requerido en caso de valor 1 se chequea, en 0 no se chequea a menos que $fecha sea distinto de vacio.
		* @param int $igual en caso de valor 1 se chequea a menor o mayor e igual, en 0 no se chequea a menor o mayor solamente.
		* @return booleano.
		*/
		function Float( $valor, $minimo = '', $maximo = '', $altText = '', $varName = '', $format = '', $requerido = 1, $igual = 1 ){
			
			if ( $format != '' ) $aux = split(",", $format);
		    
			$float_regexp = '/^(([0-9]'.( $format != '' && $aux[0] != '' && is_numeric($aux[0]) ? '{1,'.intval($aux[0]).'}' : '+' ).'\.[0-9]'.( $format != '' && $aux[1] != '' && is_numeric($aux[1]) ? '{1,'.intval($aux[1]).'}' : '+' ).')|([0-9]'.( $format != '' && $aux[0] != '' && is_numeric($aux[0]) ? '{1,'.intval($aux[0]).'}' : '+' ).')|(\.[0-9]'.( $format != '' && $aux[1] != '' && is_numeric($aux[1]) ? '{1,'.intval($aux[1]).'}' : '+' ).'))$/i';

			$valor = Validate::Convert2Float($valor);
			$minimo = Validate::Convert2Float($minimo);
			$maximo = Validate::Convert2Float($maximo);

			if ( $requerido || 
				( !$requerido && $valor != "" ) 
			) {
				if ( 
					$valor == "" || 
					!preg_match( $float_regexp, $valor ) ||
					!is_numeric( $valor )
				) {	# Chequeamos que $value sea un entero no nulo
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				} elseif ( # $minimo y $maximo pasados por parametro
					$minimo != "" && 
					$maximo != "" && 
					(
						(
							!is_numeric( $minimo ) ||
							!is_numeric( $maximo ) ||
							!preg_match( $float_regexp, $minimo ) ||							# $minimo no nulo y entero
							!preg_match( $float_regexp, $maximo ) ||							# $maximo no nulo y entero
							floatval($minimo) >= floatval($maximo)								# $minimo menor a $maximo
						) || 
						( 
							(
								( floatval($valor) < floatval($minimo) && $igual ) || 				# $valor menor o igual a $minimo ya que $igual=1
								( floatval($valor) <= floatval($minimo) && !$igual )				# $valor menor a $minimo ya que $igual=0
							) || 
							(
								( floatval($valor) > floatval($maximo) && $igual ) ||				# $valor mayor o igual a $maximo ya que $igual=1
								( floatval($valor) >= floatval($maximo) && !$igual )				# $valor mayor a $maximo ya que $igual=0
							)
						)
					)
				) { # Chequeamos si los $minimo y $maximo son enteros no nulos y que $valor este entre ellos
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;					
				} elseif ( # Solo $minimo pasado por parametro
					$minimo != "" &&
					$maximo == "" &&
					(
						!is_numeric( $minimo ) ||
						!preg_match( $float_regexp, $minimo ) ||
						( floatval($valor) < floatval($minimo) && $igual ) ||
						( floatval($valor) <= floatval($minimo) && !$igual )
					)
				) { # Chequeamos que $minimo sea entero no nulo y $valor sea mayor $minimo
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				} elseif ( # Solo $maximo pasado por parametro
					$minimo == "" &&
					$maximo != "" &&					
					(
						!is_numeric( $maximo ) ||
						!preg_match( $float_regexp, $maximo ) ||
						( floatval($valor) > floatval($maximo) && $igual ) ||
						( floatval($valor) >= floatval($maximo) && !$igual )
					)
				) { # Chequeamos que $maximo sea entero no nulo y $valor sea menor $maximo
					if( $this ){
						if( is_array( $this->errVault ) ) ( $varName ? $this->errVault[ $varName ] = ($altText != '' ? $altText : $this->altText) : array_push( $this->errVault, ($altText != '' ? $altText : $this->altText) ) );
					}
					return false;
				}			
				else return true;
			} else return true;
		} # END Float

		/**
		* pass
		* Validacion de Contraseña.
		* @author Sebastián Gomila <sgomila@virtualdata.com.ar>
		* @param string $pass Contraseña a validar.
		* @param string $format Formato de la contraseña a validar.
		* @param string $black_list Listado de palabras no permitidas.
		* @return booleano.
		*/
		function Pass( $pass, $format = '', $black_list = '' ){
		/*
		Modificacion: 2008-07-16 21:10.
		Responsable: Manuel Dominguez.
		Descripcion: se elimino validacion por $pass_crypted (in_array) y se agrego el array de idioma
		*/
			global $_SESSION;

			$black_list = split( ',', $black_list );
			$format = split( ',', $format );
			$error = "";
			if( in_array( $pass, $black_list ) ){
				$error .= ( $_SESSION['lang']['error']['formato_secuencia'] ? '<li>'.get_language_value( CFG_language, 'error,formato_secuencia' ).'</li>' : "<li>La secuencia ingresada existe en las palabras reservadas o en sus anteriores claves.</li>" );
			} else {
				foreach( $format as $clave ){
					$pc = substr( $clave, 0, 1 );
					$sc = substr( $clave, 1, 1 );
					if ( $pc == "a" ){ // Al menos una letra en minusculas
						$min_char =  substr( $clave, 1 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= ".*[a-z]";
							}
							$preg_match = "/^".$preg_match."[a-z]*/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_letra_minuscula'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_letra_minuscula' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." letra/s en minúsculas.</li>" );
						}
					}
					if ( $pc == "A" ){ // Al menos una letra en mayusculas
						$min_char =  substr( $clave, 1 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= ".*[A-Z]";
							}
							$preg_match = "/^".$preg_match."[A-Z]*/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_letra_mayuscula'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_letra_mayuscula' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." letra/s en mayúsculas.</li>" );
						}
					}
					if ( $pc == "#" ){ // Al menos un numero
						$min_char =  substr( $clave, 1 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= ".*[0-9]";
							}
							$preg_match = "/^".$preg_match."[0-9]*/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_numero'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_numero' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." número/s.</li>" );
						}
					}
					if ( $pc == "^" && $sc == "a" ){ // Inicio con al menos una letra en minusculas
						$min_char =  substr( $clave, 2 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= "[a-z]";
							}
							$preg_match = "/^".$preg_match."[a-z]*.*/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_letra_minuscula_inicio'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_letra_minuscula_inicio' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." letra/s en minúsculas en el inicio de la secuencia.</li>" );
						}
					}
					if ( $pc == "^" && $sc == "A" ){ // Inicio con al menos una letra en mayusculas
						$min_char =  substr( $clave, 2 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= "[A-Z]";
							}
							$preg_match = "/^".$preg_match."[A-Z]*.*/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_letra_mayuscula_inicio'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_letra_mayuscula_inicio' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." letra/s en mayúsculas en el inicio de la secuencia.</li>" );
						}
					}
					if ( $pc == "^" && $sc == "#" ){ // Inicio con al menos un numero
						$min_char =  substr( $clave, 2 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= "[0-9]";
							}
							$preg_match = "/^".$preg_match."[0-9]*.*/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_numero_inicio'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_numero_inicio' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." número/s en el inicio de la secuencia.</li>" );
						}
					}
					if ( $pc == "$" && $sc == "a" ){ // Fin con al menos una letra en minusculas
						$min_char =  substr( $clave, 2 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= "[a-z]";
							}
							$preg_match = "/^.*".$preg_match."[a-z]*$/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_letra_minuscula_fin'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_letra_minuscula_fin' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." letra/s en minúsculas en el fin de la secuencia.</li>" );
						}
					}
					if ( $pc == "$" && $sc == "A" ){ // Fin con al menos una letra en mayusculas
						$min_char =  substr( $clave, 2 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= "[A-Z]";
							}
							$preg_match = "/^.*".$preg_match."[A-Z]*$/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_letra_mayuscula_fin'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_letra_mayuscula_fin' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." letra/s en mayúsculas en el fin de la secuencia.</li>" );
						}
					}
					if ( $pc == "$" && $sc == "#" ){ // Fin con al menos un numero
						$min_char =  substr( $clave, 2 );
						if ( validate::Integer($min_char,1) ){
							$preg_match = "";
							for ($x = 1; $x <= $min_char; $x++) {
								$preg_match .= "[A-Z]";
							}
							$preg_match = "/^.*".$preg_match."[A-Z]*$/";
						}
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_numero_fin'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_numero_fin' ), $min_char ).'</li>' : "<li>Debe ingresar al menos ".$min_char." número/s en el fin de la secuencia.</li>" );
						}
					}
					if ( $pc == "-" ){ // Cantidad minima de caracteres generales
						$min =  substr( $clave, 1 );
						if ( validate::Integer($min,1) ){
							if ( !validate::Text($pass, "", "", $min) ) {
								$error .= ( $_SESSION['lang']['error']['formato_minimo'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_minimo' ), $min ).'</li>' : "<li>Debe ingresar al menos ".$min." caractere/s.</li>" );
							}
						}
					}
					if ( $pc == "+" ){ // Cantidad maxima de caracteres generales
						$max = substr( $clave, 1 );
						if ( validate::Integer($max,1) ){
							if ( !validate::Text($pass, "", "", 1, $max) ) {
								$error .= ( $_SESSION['lang']['error']['formato_maximo'] ? '<li>'.sprintf( get_language_value( CFG_language, 'error,formato_maximo' ), $max ).'</li>' : "<li>Debe ingresar como máximo ".$max." caractere/s.</li>" );
							}
						}
					}
					if ( $pc == "s" && $sc == "a" ){ // Secuencias de letras
						$preg_match = "/^.*(ab|bc|cd|de|ef|fg|gh|hi|ij|jk|kl|lm|mn|nñ|ño|op|pq|qr|rs|st|tu|uv|vw|wx|xy|yz|zy|yx|xw|wv|vu|ut|ys|sr|rq|qp|po|oñ|ñn|nm|ml|lk|kj|ji|ih|hg|gf|fe|ed|dc|cb|ba).*$/";
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_no_secuencia_letra'] ? '<li>'.get_language_value( CFG_language, 'error,formato_no_secuencia_letra' ).'</li>' : "<li>No Debe ingresar secuencias de letras.</li>" );
						}
					}
					if ( $pc == "s" && $sc == "#" ){ // Secuencias de numeros
						$preg_match = "/^.*(01|12|23|34|45|56|67|78|90|09|98|87|76|65|54|43|32|21|10).*$/";
						if ( !preg_match($preg_match,$pass) ) {
							$error .= ( $_SESSION['lang']['error']['formato_no_secuencia_numero'] ? '<li>'.get_language_value( CFG_language, 'error,formato_no_secuencia_numero' ).'</li>' : "<li>No Debe ingresar secuencias de números.</li>" );
						}
					}
				}
			}
			if ($error != "") return $error;
			else return true;
		} #END pass

		/**
		* get_errVault
		* Funcion de retorno de errores del Vault.
		*  
		* @return los errores contenidos en el Vault.
		*/
		function get_errVault() {
			$ret = "";
			foreach ( $this->errVault as $array ) {
			    $key = array_keys( $array );
			    $error = array_values( $array );
			    $ret .= ( $key[0] != "" ? $key[0].": " : "").$error[0]."<br>";
			}
			return $ret;
		} # END function get_errVault
		
		/**
		* get_errCount
		* Funcion de retorno de la cantidad errores del Vault.
		*  
		* @return la cantidad de los errores contenidos en el Vault.
		*/
		function get_errCount() {
			return count( $this->errVault );
		} # END function get_errCount

		/**
		* get_errGeneral
		* Funcion de retorno de error si al menos hay un error en el Vault.
		*  
		* @return el error General si al menos hay un error en el Vault.
		*/
		function get_errGeneral() {
			return ( count( $this->errVault ) > 0 ? $this->genText : '' );
		} # END function get_errGeneral
		
		/**
		* get_errors
		* Funcion de retorno de errores segun el tipo indicado.
		*  
		* @param string $type es el tipo de error que devuelve la funcion. Tipos: vault, count, general.
		* @return los errores en el tipo indicado.
		*/
		function get_errors( $type = 'vault' ) {
			switch ( strtolower($type) ) {
				case 'vault': $ret = $this->get_errVault(); break;
				case 'count': $ret = $this->get_errCount(); break;
				case 'general': $ret = $this->get_errGeneral(); break;
			}
			return $ret;
		} # END function get_errors
	}
?>