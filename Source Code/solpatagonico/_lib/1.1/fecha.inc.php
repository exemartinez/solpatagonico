<?
	/**
	* Librer�a de Fechas
	*  
	*/
	class Fecha {

		# Menor d�a posible 		= 13/12/1901
		# Menor Timestamp posible	= -2147483647
		# Formato Gregoriano 		= MM-DD-AAAA

		/**
		* Format
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $modo es el tipo de formato, opcional.
		* @param string $tipo es el tipo de fecha ingresado en el parametro $fecha, opcional.
		* @return fecha en el formato ingresado.
		*/
		function Format( $fecha, $modo = 'texto', $tipo = 'normal' ) {
			switch( strtolower( $tipo ) ){
				case 'normal': $fecha_iso = Fecha::Normal2ISO( $fecha ); break;
				case 'iso': $fecha_iso = $fecha; break;
				case 'timestamp': $fecha_iso = Fecha::Timestamp2ISO( $fecha ); break;
			}
			if( Fecha::validate( $fecha_iso ) ){
				list( $a , $m , $d ) = split( '-|/' , $fecha_iso , 3 );
				if( $modo == 'texto' ){
					return Fecha::nameDay( Fecha::numberDay($fecha_iso) )." ".$d.", de ".Fecha::nameMonth( $m )." de ".$a;
				} elseif( strtolower( $tipo ) == 'timestamp' ){
					return date( $modo, $fecha );
				}
			}
			return false;
		} # END Format

		/**
		* ISO2Normal
		* Conversi�n de fecha ISO a Normal.
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $sep es el separador a utilizar, opcional.
		* @return fecha en formato Normal.
		*/
		function ISO2Normal( $fecha , $sep = '/' ) {
			if( Fecha::validate( $fecha ) ){
				list( $a , $m , $d ) = split( '-|/' , $fecha , 3 );
				return $d.$sep.$m.$sep.$a;
			}
			return false;
		} # END ISO2Normal

		/**
		* Normal2ISO
		* Conversi�n de fecha Normal a ISO.
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $sep es el separador a utilizar, opcional.
		* @return fecha en formato ISO.
		*/
		function Normal2ISO( $fecha , $sep = '-' ) {
			if ( Fecha::validate( $fecha, 'normal' ) ) {
				list( $d , $m , $a ) = split( '-|/' , $fecha , 3 );
				return $a.$sep.$m.$sep.$d;
			}
			return false;
		} # END Normal2ISO

		/**
		* ISO2Timestamp
		* Conversi�n de fecha ISO a Timestamp.
		*  
		* @param date $fecha es la fecha a formatear.
		* @return fecha en formato Timestamp.
		*/
		function ISO2Timestamp( $fecha ) {
			if( Fecha::validate( $fecha ) ){
				list( $a , $m , $d ) = split( '-|/' , $fecha , 3 );
				return mktime(0,0,0,$m,$d,$a);
			}
			return false;
		} # END ISO2Timestamp

		/**
		* Timestamp2ISO
		* Conversi�n de Timestamp a fecha ISO.
		*  
		* @param date $fecha es la fecha a formatear.
		* @return fecha en formato ISO.
		*/
		function Timestamp2ISO( $timestamp , $sep = '-' ) {
			$timestamp = intval($timestamp);
			if( is_integer( $timestamp ) ){
				return date( "Y".$sep."m".$sep."d" , $timestamp );
			}
			return false;
		} # END Timestamp2ISO

		/**
		* Norma2Timestamp
		* Conversi�n de fecha Normal a Timestamp.
		*  
		* @param date $fecha es la fecha a formatear.
		* @return fecha en formato Tiemstamp.
		*/
		function Normal2Timestamp( $fecha ) {
			if( Fecha::validate( $fecha, 'normal' ) ){
				list( $d , $m , $a ) = split( '-|/' , $fecha , 3 );
				return mktime(0,0,0,$m,$d,$a);
			}
			return false;
		} # END Normal2Timestamp

		/**
		* Timestamp2Normal
		* Conversi�n de Timestamp a fecha Normal.
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $sep es el separador a utilizar, opcional.
		* @return fecha en formato Normal.
		*/
		function Timestamp2Normal( $timestamp , $sep = '/' ) {
			if ( $timestamp != "" ) {
				return date( "d".$sep."m".$sep."Y" , $timestamp );
			} else return false;
		} # END Timestamp2Normal

		/**
		* Timestamp2NormalyHora
		* Conversi�n de Timestamp a fecha Normal con hora.
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $sep es el separador a utilizar en la fecha, opcional.
		* @param string $sep2 es el separador a utilizar en la hora, opcional.
		* @return fecha en formato Normal con hora.
		*/
		function Timestamp2NormalyHora( $timestamp , $sep = '/', $sep2 = ':' ) {
			if ( $timestamp != "" ) {
				return date( "d".$sep."m".$sep."Y"." H".$sep2."i" , $timestamp );
			} else return false;
		} # END Timestamp2NormalyHora

		/**
		* validate
		* Validacion de fecha.
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $tipo es el de fecha ingresada en el parametro $fecha, opcional.
		* @return booleano.
		*/
		function validate( $fecha, $tipo = 'iso' ) {
			if( $fecha != '' ) {
				switch( strtolower($tipo) ){
					case 'iso': list( $a , $m , $d ) = split( '-|/' , $fecha ); break;
					case 'normal': list( $d , $m , $a ) = split( '-|/' , $fecha );  break;
				}
				return checkdate( intval($m) , intval($d) , intval($a) );
			}
			return false;
		} # END validate

		/**
		* today
		* Genera la fecha actual seg�n el tipo.
		*  
		* @param date $fecha es la fecha a formatear.
		* @param string $sep es el separador a utilizar, opcional.
		* @return fecha actual en formato ingresado.
		*/
		function today( $tipo = 'normal', $sep = '/' ){
			switch( strtolower($tipo) ){
				case 'normal': return Fecha::Timestamp2Normal(time(), $sep);
				case 'iso': return Fecha::Timestamp2ISO(time(), $sep);
				case 'timestamp': return time();
			}
		} # END today

		/**
		* numberDay
		* Obtiene el numero del d�a seg�n la fecha.
		*  
		* @param date $fecha es la fecha a formatear.
		* @return el entero que identifica el d�a (0 a 6).
		*/
		function numberDay( $fecha ) {
			$d = getdate( Fecha::ISO2Timestamp($fecha) );
			return $d["wday"];
		} # END numberDay

		/**
		* nameDay
		* Obtiene el nombre del d�a seg�n el numero ingresado, se combina con la funcion numberDay.
		*  
		* @param integer $d es d�a a buscar.
		* @return el nombre del d�a.
		*/
		function nameDay( $d ) {
			if( $d < 0 || $d > 6 ) return false;
			$d = intval($d);
			switch($d){
				case 0: $d�a = 'Domingo';break;
				case 1: $d�a = 'Lunes';break;
				case 2: $d�a = 'Martes';break;
				case 3: $d�a = 'Mi�rcoles';break;
				case 4: $d�a = 'Jueves';break;
				case 5: $d�a = 'Viernes';break;
				case 6: $d�a = 'S�bado';break;
			}
			return $d�a;
		} # END nameDay

		/**
		* nameMonth
		* Obtiene el nombre del m seg�n el numero ingresado.
		*  
		* @param integer $m es mes a buscar.
		* @return el nombre del mes.
		*/
		function nameMonth( $m ) {
			if( $m < 1 || $m > 12 ) return false;
			$m = intval($m);
			switch($m){
				case 1: $mes = 'Enero';break;
				case 2: $mes = 'Febrero';break;
				case 3: $mes = 'Marzo';break;
				case 4: $mes = 'Abril';break;
				case 5: $mes = 'Mayo';break;
				case 6: $mes = 'Junio';break;
				case 7: $mes = 'Julio';break;
				case 8: $mes = 'Agosto';break;
				case 9: $mes = 'Septiembre';break;
				case 10: $mes = 'Octubre';break;
				case 11: $mes = 'Noviembre';break;
				case 12: $mes = 'Diciembre';break;
			}
			return $mes;
		} # END nameMonth
		
		/**
		* days_in_month
		* Obtiene la cantidad de d�as del mes seg�n los datos ingresados
		*  
		* @param integer $month es mes a buscar.
		* @param integer $year es a�o a buscar.
		* @return la cantidad de d�as del mes.
		*/		
		function days_in_month( $month, $year ) { 
		    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
		} # END days_in_month

		/**
		* differenceDate
		* Obtiene la diferencia en d�as de las fechas ingresadas.
		*  
		* @param date $fecha1 es la fecha inicial.
		* @param date $fecha2 es la fecha final.
		* @param string $returnType es el formato de retorno d=d�as, m=meses, y=a�os, h=horas, i=minutos, s=seg�ndos, opcional.
		* @return la diferencia entre las fechas seg�n el tipo de retorno.
		*/
		function differenceDate( $fecha1, $fecha2, $returnType = 'd') {
			if ( Fecha::validate( $fecha1 ) && Fecha::validate( $fecha2 ) ) {
				$s = strtotime($fecha1) - strtotime($fecha2);
				$y = date("Y",strtotime($fecha1)) - date("Y",strtotime($fecha2));
				$m = date("m",strtotime($fecha1)) - date("m",strtotime($fecha2)) + $y * 12 ;
				$m = date("d",strtotime($fecha2)) > date("d",strtotime($fecha1)) ? $m - 1 : $m;
				$d = intval($s/86400);
				$h = intval($s/3600);
				$i = intval($s/60);
				$returnType = strtolower($returnType);
				return $$returnType;
			}
			return false;
		} # END differenceDate

		/**
		* addDate
		* Obtiene la fecha modificada seg�n los parametros, puede sumar o restar d�as, meses o a�os.
		*  
		* @param date $date es la fecha a modificar.
		* @param integer $days es la cantidad de d�as a sumar.
		* @param integer $months es la cantidad de meses a sumar, opcional.
		* @param integer $years es la cantidad de a�os a sumar, opcional.
		* @return la fecha modificada, sumando o restando d�as, meses o a�os.
		*/
		function addDate( $date, $days, $months = 0, $years = 0) {
			if ( Fecha::validate ( $date )  ) {
				list( $a , $m , $d ) = split( '-|/' , $date , 3 );
				return date( "Y-m-d", mktime( 0, 0, 0, $m + $months, $d + $days, $a + $years ) );
			}
			return false;
		} # END addDate
	}
?>