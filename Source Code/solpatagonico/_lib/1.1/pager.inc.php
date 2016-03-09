<?
	/**
	* Pager library 1.0
	* Última edición 23/02/2006
	*  
	*/
	class pager {
		# Propiedades/Parametros del constructor
		var $source; # Consulta SQL u objeto DBI a paginar
		var $actual_page; # Página actual
		var $max_pages = 10; # Cantidad máxima de páginas, 0 (cero) para ilimitadas
		var $min_records = 10; # Cantidad mínima de registros por página
		var $additional_params = ''; # Parametros adicionales para el GET
		var $class = "paginar_paginas"; # Estilo para aplicar a los vinculos
		var $previous = "&lt;"; # Puntero a la página anterior
		var $next = "&gt;"; # Puntero a la página siguiente
		var $separator = " - "; # Separador de páginas
		var $first = ""; # Puntero a la primer página
		var $last = ""; # Puntero a la última página
		var $previous_block = "&lt;&lt;"; # Puntero al bloque anterior
		var $next_block = "&gt;&gt;"; # Puntero al bloque siguiente
		var $obj_class = ""; # Estilo para aplicar a la caja de texto de get_text_navigator
		var $button_class = ""; # Estilo para aplicar al la caja de texto de get_text_navigator
		
		# Propiedades dinamicas
		var $title_first_button = "Primer página"; # Título para el vinculo del primer boton
		var $title_prev_block = "Bloque anterior"; # Título para el vinculo del primer boton
		var $title_prev_button = "Página anterior"; # Título para el vinculo del primer boton
		var $title_next_button = "Página siguiente"; # Título para el vinculo del primer boton
		var $title_next_block = "Bloque siguiente"; # Título para el vinculo del primer boton
		var $title_last_button = "Última página"; # Título para el vinculo del primer boton
		
		var $alt_previous = ""; # Alternativa al puntero de pagina anterior sin vinculo
		var $alt_next = ""; # Alternativa al puntero de pagina siguiente sin vinculo
		var $alt_first = ""; # Alternativa al puntero de primer pagina sin vinculo
		var $alt_last = ""; # Alternativa al puntero de ultima pagina sin vinculo
		var $alt_previous_block = ""; # Alternativa al puntero de bloque anterior sin vinculo
		var $alt_next_block = ""; # Alternativa al puntero de bloque siguiente sin vinculo
		
		var $pointer_default_link = ''; # Valor a ingresar si un puntero no tiene link
		var $rewrite_rule = ''; # Array con valores de reescritura. EJ: array( "sep_field" => '/', "sep_value" = '-' )
		
		# Propiedades privadas		
		var $HTML_pages = ''; # Código HTML con las paginas
		var $total_records = 0; # Cantidad total de registros que cumplen con la consulta del source
		var $total_pages = 0; # Cantidad total de paginas
		var $block_size = 10; # Cantidad de paginas a mostrar simultaneamente para el navegador con desplazamiento
		var $sql_query = ''; # Codigo SQL ejecutado para la paginación
		var $link = ''; # Conexión la Base de datos para el caso de $source como string SQL
		var $error = ''; # Descripción de errores 
		var $source_mode = ''; # Modo de operacion de la clase: en modo dbi o en modo sql_string
		var $block_init = 0; # Primer pagina a incluir en el bloque a mostrar
		var $block_end = 0; # Ultima pagina a incluir en el bloque

		/**
		* Class constructor
		* Comentario
		*  
		* @params $source Consulta SQL u objeto DBI a paginar
		* @params $actual_page Pagina actual de visualizacion
		* @params $max_pages Cantidad máxima de paginas, 0 (cero) para ilimitadas
		* @params $min_records Cantidad mínima de registros por página. Se respetara este parametro mientras no haya limite de paginas o no se exceda dicho limite
		* @params $additional_params Parametros adicionales para los vinculos de paginación
		* @params $class Estilos para aplicar a los vinculos de paginación
		* @params $previous Puntero a la página anterior. Soporta código HTML para insertar imágenes
		* @params $next Puntero a la página siguiente. Soporta código HTML para insertar imágenes
		* @params $separator Separador de páginas. Soporta código HTML para insertar imágenes
		* @params $first Puntero a la primer página. Soporta código HTML para insertar imágenes.
		* @params $last Puntero a la última página. Soporta código HTML para insertar imágenes.
		* @return boolean.
		*/
		function pager (
			$source, # Objeto DBI a paginar
			$actual_page, # Página actual
			$max_pages = 10, # (Opcional) Cantidad máxima de páginas, 0 (cero) para ilimitadas
			$min_records = 10, # (Opcional) Cantidad mínima de registros por página
			$additional_params = '', # (Opcional) Parametros adicionales para el GET
			$class = "pager", # (Opcional) Estilo para aplicar a los vinculos
			$previous = "&lt;", # (Opcional) Puntero a la página anterior
			$next = "&gt;", # (Opcional) Puntero a la página siguiente
			$separator = " - ", # (Opcional) Separador de páginas
			$first = "", # (Opcional) Puntero a la primer página
			$last = "", # (Opcional) Puntero a la última página
			$previous_block = "&lt;&lt;", # (Opcional) Puntero al bloque anterior
			$next_block = "&gt;&gt;", # (opcional) Puntero al bloque siguiente
			$actual_page_class = '' #Estilo a aplicar a la pagina actual, si esta vacio, aplica el $class y lo hace bold
		) {
			$this->source = $source;
			
			$this->actual_page = intval($actual_page);
			$this->max_pages = ( ($max_pages === '' || $max_pages === NULL || $max_pages < 0) ? 10 : $max_pages );
			$this->min_records = ( ($min_records == '' || $min_records < 0) ? 10 : $min_records );
			$this->additional_params = $additional_params;
			$this->class = $class;
			$this->previous = $previous;
			$this->next = $next;
			$this->separator = $separator;
			$this->first = $first;
			$this->last = $last;
			$this->previous_block = $previous_block;
			$this->next_block = $next_block;
			$this->actual_page_class = $actual_page_class;
			
			# Validacion de parametro de entrada $source
			if( is_a( $this->source, 'DBI' ) ){ #$source es de tipo DBI
			    $this->source_mode = 'dbi';
			    $this->total_records = $this->source->num_rows();
			    /* Modificado el 15 de Setiembre de 2005 por Fernando Sanz.*/
			    //$this->total_records = $this->source->num_rows() <= 0 ? 1 : $this->source->num_rows();
			/*
HACER TRATAMIENTO CON OBJECTO ADOdb DIRECTAMENTE!!!!!!!!!!!
			} elseif( is_a( $this->source, 'ADOdb' ) ){
				$this->source_mode = 'adodb';
			*/
			} elseif( defined( 'CFG_DB_dsn') && CFG_DB_dsn != '' ){ #$source es un string para ejecutar sobre ADOdb
				$this->link = NewADOConnection( CFG_DB_dsn );
				if( $this->link->ErrorNo() ){ # Error al conectar con la BD
					$this->error = $this->link->ErrorMsg();
					return false;
				} else { # Conexion exitosa
					$this->source_mode = 'adodb_string';
					$this->sql_query = $this->source;
					$this->source = $this->link->execute( $this->sql_query );
					if( $this->source ){
						$this->total_records = $this->source->RecordCount();
					} else {
						$this->total_records = 0;
					}
				}
			} elseif( is_string( $this->source ) && $this->source != '' ){ #$source es un srting SQL
			    if( 
					CFG_DB_host != ''
					&& CFG_DB_user != ''
					//&& CFG_SQL_pass != ''
					&& CFG_DB_db != ''
			    ){ # Chequeo de constantes de conexion a SQL
					$this->link = mysql_connect( CFG_SQL_host, CFG_SQL_user, CFG_SQL_pass );
					if( mysql_errno() ) { # Error al conectar con el host SQL
						$this->error = "MySQL error ".mysql_errno().": ".mysql_error();
						return false;
					} else { # Conexion con el host SQL establecida
						mysql_select_db( CFG_SQL_db, $this->link );
						if( mysql_errno() ) { # Error al seleccionar DB
							$this->error = "MySQL error ".mysql_errno().": ".mysql_error();
							return false;
						} else { # Seleccion de DB exitosa
							$this->source_mode = 'sql_string';
							$this->sql_query = $this->source;
							$this->source = mysql_query( $this->sql_query, $this->link );
							$this->total_records = mysql_num_rows( $this->source );
						}
					}
			    } else { # No estan definidas las constantes de conexion a SQL
					$this->error = 'No estan definidas las constantes de conexion a SQL: CFG_DB_host, CFG_DB_user, CFG_DB_db';
					return false;
			    }
			} else { #$source no es de un tipo reconodido
			    $this->error = 'El tipo de parámetro de $source no es aceptado';
			    return false;
			}
			# Validacion de parametro de entrada $max_pages
			if( $this->max_pages < 0 ){ # No se pueden mostrar páginas negativas
			    $this->error = 'Error en cantidad máxima de páginas';
			    return false;
			}
			# Validacion de parametro de entrada $min_records
			if( $this->min_records < 1 ){ # No se pueden mostrar menos de 1 producto por página
			    $this->error = 'Error en cantidad de registros minimos';
			    return false;
			}
			# Comienzo del armado del objeto pager
			$this->total_pages = ceil( $this->total_records/$this->min_records );
			if ( $this->max_pages > 0 ) { # Hay limite de páginas
			    while ( $this->total_pages > $this->max_pages ) {
					$this->total_pages = ceil( $this->total_records/++$this->min_records );
			    }
			} # END limite de páginas
			/*# Ajuste de $actual_page a páginas válidas
			if( $this->actual_page < 0 ) {
			    $this->actual_page = 0;
			} elseif( $this->actual_page >= $this->total_pages ) {
			    $this->actual_page = $this->total_pages-1;
			}*/
			# Reconstruccion del $source
			switch( $this->source_mode ){
			    case 'dbi':
					$this->source->select(
						$this->source->tabla,
						$this->source->values,
						$this->source->where,
						$this->source->order,
						intval( $this->actual_page * $this->min_records ).",".$this->min_records
					);
					$this->sql_query = $this->source->sql;
					break;
				case 'adodb_string':
					$this->source = $this->link->SelectLimit( $this->sql_query, $this->min_records, intval( $this->actual_page * $this->min_records ) );
					$this->sql_query = $this->source->sql;
					break;
			    case 'sql_string':
					$this->sql_query = $this->sql_query." LIMIT ".intval( $this->actual_page * $this->min_records ).",".$this->min_records;
					$this->source = mysql_query( $this->sql_query, $this->link );
					break;
			}
			# Bajar array de additional_params a string
    			$amp = '&amp;';
			if( is_array( $this->additional_params ) ) {
			    $aux = '';
			    foreach ( $this->additional_params as $key => $value ) {
					if( $key != 'cur_page' ) {
						$aux .= $amp.$key."=".$value;
					}
			    }
			    $this->additional_params = $aux;
			} else {
			    $this->additional_params = $this->globals_parser( $this->additional_params );
			}
			$this->additional_params = ( substr( $this->additional_params, 0, 1 ) == '&' ? '' : $amp ).$this->additional_params;
			return true;
		} # END function pager
		
		
		/**
		* fetch_object
		* Avanza sobre los registros de la pagina actual.
		*  
		* @return un objeto query.
		*/		
		function fetch_object(){
		    $ret = false;
		    switch( $this->source_mode ){
			case 'dbi':
			    $ret = $this->source->fetch_object();
			    break;
			case 'adodb_string':
				$ret = $this->source->FetchNextObject(false);
				break;
			case 'sql_string':
			    $ret = mysql_fetch_object( $this->source );
			    break;
		    }
		    return $ret;
		}
		
		/**
		* get_sql
		* Devuelve el Query SQL ejecutado.
		*  
		* @return el sql del query.
		*/		
		function get_sql(){
		    return $this->sql_query;
		}
		
		/**
		* print_sql
		* Imprime el Query ejecutado
		*  
		* @return el SQL del query
		*/
		function print_sql(){
			$ret = '<hr>';
			$ret .= $this->get_sql();
			$ret .= '<hr>';
			echo $ret;
		}

		/**
		* get_db
		* Devuelve el objeto pager con los datos correspondientes a la página actual.
		*  
		* @return el objecto DB.
		*/		
		function get_db () {
			return $this->source;
		}
		
		/**
		* get_page
		* Devuelve la pagina actual.
		*  
		* @return un numero.
		*/		
		function get_page () {
			$ret = $this->actual_page;
			if( $this->total_pages ) $ret++;
			return $ret;
		}
		
		/**
		* get_total_pages
		* Devuelve la cantidad total de paginas.
		*  
		* @return un numero.
		*/		
		function get_total_pages () {
			return intval( $this->total_pages );
		}

		/**
		* get_total_records
		* Devuelve la cantidad total de registros.
		*  
		* @return un numero.
		*/		
		function get_total_records () {
			return intval( $this->total_records );
		}
		
		/**
		* get_page_records
		* Devuelve la cantidad de registros en la pagina actual.
		*  
		* @return (int) Cantidad de registros en la pagina actual.
		*/		
		function get_page_records () {
		    switch( $this->source_mode ){
			case 'dbi': 
			    $num_rows = $this->source->num_rows(); break;
			case 'adodb_string':
				$num_rows = $this->source->RecordCount();break;
			case 'sql_string':
			    $num_rows = mysql_num_rows( $this->source ); break;
		    }
			return intval( $num_rows );
		}
		
		/**
		* num_rows
		* Alias de get_page_records.
		*  
		* @return (int) Cantidad de registros en la pagina actual.
		*/
		function num_rows(){
			return $this->get_page_records();
		}
		
		/**
		* get_first_pos
		* Devuelve la posición del primer registro.
		*  
		* @return un numero.
		*/		
		function get_first_pos () {
			if( $this->total_records > 0 ) {
				$ret = $this->actual_page * $this->min_records + 1;
			} else {
				$ret = 0;
			}
			return intval( $ret );
		}
		
		/**
		* get_last_pos
		* Devuelve la posición del último registro.
		*  
		* @return un numero.
		*/		
		function get_last_pos () {
			$first = ( $this->actual_page < 0 ? 0 : $this->actual_page ) * $this->min_records; 
			return intval( $first + $this->get_page_records() );
		}
		
		/**
		* print_navigator
		* Imprime el navegador de paginas.
		*  
		* @param string $type tipo de navegador a imprimir.
		* @return el html del navegador.
		*/
		function print_navigator( $type = '' ){
		    echo $this->get_navigator( $type );
		}
		
		/**
		* get_navigator
		* Devuelve el navegador de paginas.
		*  
		* @param string $type tipo de navegador a devolver.
		* @return el html del navegador.
		*/		
		function get_navigator( $type = ''  ){
			$script_rewrite = '
			<script language="javascript" type="text/javascript">
			<!--
				function pager_rewrite( page ){
					var url = "'.$this->get_page_link('#CUR_PAGE#').'";
					url = url.replace( /#CUR_PAGE#/, page );
					window.location = url;
				}
			-->
			</script>
			';
		    switch( $type ){
				case 'combo':
					$ret = $this->get_combo_navigator(); break;
				case 'input':
					$ret = $script_rewrite.$this->get_input_navigator(); break;
				case 'desplazamiento':
					$ret = $this->get_short_navigator(); break;
				case 'desp':
					$ret = $this->get_short_navigator(); break;
				case 'short':
					$ret = $this->get_short_navigator(); break;
				default:
					$ret = $this->get_text_navigator(); break;
		    }
		    return $ret;
		}
		
		/**
		* get_combo_navigator
		* Escribe el navegador de páginas con un combo.
		*  
		* @param string $obs_class estilo para la caja de texto.
		* @return el html del navegador.
		*/		
		function get_combo_navigator (
			$obj_class = '' # Estilo para la caja de texto
		) {
			$this->obj_class = $obj_class != '' ? $obj_class : $this->obj_class;

			if( $this->total_records > 0 ) {
				$ret = $this->class ? '<span class="'.$this->class.'">' : '';
				$ret .= $this->first_button();
				$ret .= $this->prev_button();
				$ret .= " ".$this->separator." ";
				$ret .= $this->next_button();
				$ret .= $this->last_button();
				$ret .= '&nbsp;';
				$ret .= '<select id="pager_page" class="'.$this->obj_class.'" onchange="location=this.value">';
				for ( $i = 0 ; $i < $this->total_pages ; $i++ ) {
					$ret .= '<option value="'.$this->get_page_link( $i ).'" ';
					$ret .= $i == $this->actual_page ? 'selected' : '';
					$ret .= " >";
					$ret .= ($i+1);
					$ret .= '</option>';
				}
				$ret .= '</select>';
				$ret .= '&nbsp;/&nbsp;';
				$ret .= $this->get_total_pages();
			} else {
				$ret = '';
			}
			return $ret;
		}
		
		/**
		* get_input_navigator
		* Escribe el navegador de páginas con caja de texto (input).
		*  		
		* @param string $obs_class estilo para la caja de texto.
		* @param string $button_class estilo para el boton de navegacion.
		* @return el html del navegador.
		*/		
		function get_input_navigator (
			$obj_class = '', # Estilo para la caja de texto
			$button_class = '' # Estilo para el boton de navegacion
		) {
			$this->obj_class = $obj_class != '' ? $obj_class : $this->obj_class;
			$this->button_class = $button_class != '' ? $button_class : $this->button_class;

			if( $this->total_records > 0 ) {
				$ret = $this->class ? '<span class="'.$this->class.'">' : '';
				$ret .= $this->first_button();
				$ret .= $this->prev_button();
				$ret .= " ".$this->separator." ";
				$ret .= $this->next_button();
				$ret .= $this->last_button();
				$ret .= '&nbsp;';
				$ret .= '<input type="text" id="pager_page_input" value="'.($this->actual_page+1).'" class="'.$this->obj_class.'" style="width: 40px" />';
				$ret .= '&nbsp;/&nbsp;';
				$ret .= $this->get_total_pages();
				$ret .= '&nbsp;';
				$ret .= '<input type="button" value="Ir" style="'.$this->button_class.'" onclick="pager_rewrite( parseInt( parseInt( document.getElementById( \'pager_page_input\' ).value ) - 1 ) )" />';
				$ret .= $this->class ? '</span>' : '';
			} else {
				$ret = '';
			}
			return $ret;
		}

		/**
		* get_short_navigator
		* Escribe un navegador tradicional con desplazamiento
		*  		
		* @param string $cant_paginas cantidad maxima de paginas en el navegador.
		* @return el html del navegador.
		*/				
		function get_short_navigator(
		    $cant_paginas = ''
		){
			$this->block_size = ( intval( $cant_paginas ) > 0 ? intval( $cant_paginas ) : $this->block_size );
			if( $this->total_records > 0 ) {
				# Impresión de las páginas
				$ret = $this->class ? '<span class="'.$this->class.'">' : '';
				$ret .= $this->first_button();
				$ret .= $this->prev_block();
				$ret .= $this->prev_button();
				$sep = " ".$this->separator." ";
				# Calculo de bloque
				if( $this->block_size % 2 == 0 ){
					$this->block_init = $this->actual_page - ( ( $this->block_size/2 )-1 );
					$this->block_end = $this->actual_page + ($this->block_size/2)+1;
				} else {
					$this->block_init = $this->actual_page - floor( $this->block_size/2 );
					$this->block_end = $this->actual_page + floor( $this->block_size/2)+1;
				}
				#Correccion de limites
				if( $this->block_init < 0 ){
					$this->block_init = 0; $this->block_end = $this->block_init + $this->block_size;
				}
				if( $this->block_end > $this->total_pages ){
					$this->block_end = $this->total_pages; $this->block_init = $this->block_end - $this->block_size;
				}
				if( $this->block_init < 0 || $this->block_end > $this->total_pages ){
					$this->block_init = 0; $this->block_end = $this->total_pages;
				}
				# Recorrido de bloque
				$ret .= $this->get_pages_list( $this->block_init, $this->block_end, $sep );
				$ret .= $sep;
				$ret .= $this->next_button();
				$ret .= $this->next_block();
				$ret .= $this->last_button();
				$ret .= $this->class ? '</span>' : '';
			} else {
				$ret = '';
			}
			return $ret;
		}

		/**
		* 
		* Escribe el navegador de páginas estandar
		*  
		* @return el html del navegador.
		*/		
		function get_text_navigator(){
			if( $this->total_records > 0 ) {
				# Impresión de las páginas
				$ret = $this->class ? '<span class="'.$this->class.'">' : '';
				$ret .= $this->first_button();
				$ret .= $this->prev_button();
				$sep = ' '.$this->separator.' ';
				$ret .= $this->get_pages_list( 0, $this->total_pages, $sep );
				$ret .= $sep;
				$ret .= $this->next_button();
				$ret .= $this->last_button();
				$ret .= $this->class ? '</span>' : '';
			} else {
				$ret = '';
			}
			return $ret;
		}
		
		/**
		* get_pages_list
		* Private: Devuelve el listado de paginas correspondientes
		*  
		* @return el html de las paginas.
		*/		
		function get_pages_list( $inicio, $fin, $sep = '' ){
		    $inicio = intval($inicio);
		    $fin = intval($fin);
		    $sep = $sep != '' ? $sep : $this->separator;
		    $ret = '';
		    for( $i = $inicio ; $i < $fin ; $i++ ){
				$ret .= $sep;
				$ret .= $i != $this->actual_page 
					? '<a href="'.$this->get_page_link( $i ).'" class="'.$this->class.'">' 
					: '';
				$ret .= $i == $this->actual_page 
					? ( $this->actual_page_class != '' 
						? ( $this->class 
								? '</span>' 
								: ''
							).'<span class="'.$this->actual_page_class.'">' 
						: '<strong>' ) 
					: '';
				$ret .= ( $i + 1 );
				$ret .= $i == $this->actual_page 
					? ( $this->actual_page_class != '' 
						? '</span>'.( $this->class 
								? '<span class="'.$this->class.'">' 
								: ''
							)
						: '</strong>' ) 
					: '';
				$ret .= $i != $this->actual_page ? '</a>' : '';
		    }
		    return $ret;
		}

		/**
		* first_button
		* Imprime el boton de navegacion a la primer pagina
		*  
		* @return el html del boton.
		*/		
		function first_button() {
			if( $this->first != '' ){
				$vinculo = $this->actual_page > 0;
				if( !$vinculo && $this->pointer_default_link ){
					$href = $this->pointer_default_link;
					$vinculo = true;
				} else {
					$href = $this->get_page_link( 0 );//$_SERVER['PHP_SELF'].'?cur_page=0'.$this->additional_params;
				}
				//$href = $_SERVER['PHP_SELF'].'?cur_page=0'.$this->additional_params;
				$ret = $vinculo 
					? '<a href="'.$href.'" class="'.$this->class.'" title="'.$this->title_first_button.'" >' 
					: '';
				$ret .= !$vinculo && $this->alt_first != '' > 0 
					? $this->alt_first 
					: $this->first;
				$ret .= $vinculo ? '</a>' : '';
				$ret .= " ";
			}
			return $ret;
		}
		
		/**
		* prev_block
		* Imprime el boton de navegacion del bloque anterior
		*  
		* @return el html del boton.
		*/		
		function prev_block(){
			$mostrar = ( $this->actual_page >= ( $this->block_size / 2 ) ) && ( $this->block_size < $this->total_pages );
			$cur_page = ($this->actual_page-$this->block_size) >= 0 
				? ($this->actual_page-$this->block_size) 
				: 0;
			if( !$mostrar && $this->pointer_default_link ){
				$href = $this->pointer_default_link;
				$mostrar = true;
			} else {
				$href = $this->get_page_link( $cur_page );//$_SERVER['PHP_SELF'].'?cur_page='.$cur_page.$this->additional_params;
			}
			$ret = $mostrar 
				? '<a href="'.$href.'" title="'.$this->title_prev_block.'" >' 
				: '';
			$ret .= !$mostrar && $this->alt_previous_block != '' 
				? $this->alt_previous_block 
				: $this->previous_block;
			$ret .= $mostrar 
				? '</a>' 
				: '';
			$ret .= ' ';
		    return $ret;
		}

		/**
		* prev_button
		* Imprime el boton de navegacion de la pagina anterior
		*  
		* @return el html del boton.
		*/				
		function prev_button() {
			if( $this->previous != '' ){ # Posicionador de pagina anterior
				$vinculo = $this->actual_page > 0;
				if( !$vinculo && $this->pointer_default_link ){
					$href = $this->pointer_default_link;
					$vinculo = true;
				} else {
					$href = $this->get_page_link( ($this->actual_page-1) );//$_SERVER['PHP_SELF'].'?cur_page='.($this->actual_page-1).$this->additional_params;
				}
				$ret = $vinculo
					? '<a href="'.$href.'" class="'.$this->class.'" title="'.$this->title_prev_button.'" >' 
					: '';
				$ret .= $this->actual_page <= 0 && $this->alt_previous != '' 
					?  $this->alt_previous 
					: $this->previous;
				$ret .= $vinculo 
					? '</a>' 
					: '';
			}
			return $ret;
		}
		
		/**
		* get_page_link
		* Imprime el vinculo segun reescritura o no 
		*  
		* @return el link reescrito correctamente
		*/
		function get_page_link( $page ){
			if( !is_array( $this->rewrite_rule ) ){
				$ret = $_SERVER['PHP_SELF'].'?cur_page='.$page.$this->additional_params;
			} else {
				$query_string = html_entity_decode( 'cur_page='.$page.$this->additional_params );
				$q = split( '&', $query_string );
				if( $this->rewrite_rule['url_prefix'] != '' ){
					$ret .= $this->rewrite_rule['url_prefix'].$this->rewrite_rule['sep_field'];
				}
				foreach( $q as $valores ){
					$v = split( '=', $valores );
					if( $v[ 1 ] != '' ){
						$ret .= $sep.$v[ 0 ].$this->rewrite_rule[ 'sep_value' ].$v[ 1 ];
						$sep = $this->rewrite_rule['sep_field'];
					}
				}
			}
			return $ret;
		}
		
		/**
		* next_button
		* Imprime el boton de navegacion de la siguiente pagina
		*  
		* @return el html del boton.
		*/						
		function next_button() {
			if( $this->next != '' ){ # Posicionador de pagina siguiente
				$vinculo = $this->actual_page < $this->total_pages - 1;
				if( !$vinculo && $this->pointer_default_link ){
					$href = $this->pointer_default_link;
					$vinculo = true;
				} else {
					$href = $this->get_page_link( ($this->actual_page+1) );//$_SERVER['PHP_SELF'].'?cur_page='.($this->actual_page+1).$this->additional_params;
				}
				$ret = $vinculo
					? '<a href="'.$href.'" class="'.$this->class.'" title="'.$this->title_next_button.'" >'
					: '';
				$ret .= !$vinculo && $this->alt_next != '' 
					? $this->alt_next 
					: $this->next;
				$ret .= $vinculo 
					? '</a>' 
					: '';
			}
			return $ret;
		}
		
		/**
		* next_block
		* Imprime el boton de navegacion del bloque siguiente
		*  
		* @return el html del boton.
		*/					
		function next_block(){
			$mostrar = ( ( $this->actual_page + ( $this->block_size / 2 ) ) < ( $this->total_pages - 1 ) ) && ( $this->block_size < $this->total_pages );
			$cur_page = ( $this->actual_page + $this->block_size ) > $this->total_pages - 1
				? $this->total_pages - 1 
				: ( $this->actual_page + $this->block_size );
			if( !$mostrar && $this->pointer_default_link ){
				$href = $this->pointer_default_link;
				$mostrar = true;
			} else {
				$href = $this->get_page_link( $cur_page );//$_SERVER['PHP_SELF'].'?cur_page='.$cur_page.$this->additional_params;
			}
			$ret = ' ';
			$ret .= $mostrar 
				? '<a href="'.$href.'" title="'.$this->title_next_block.'" >' : '';
			$ret .= !$mostrar && $this->alt_next_block != '' 
				? $this->alt_next_block
				: $this->next_block;
			$ret .= $mostrar 
				? '</a>' 
				: '';
			return $ret;
		}

		/**
		* last_button
		* Imprime el boton de navegacion a la ultima pagina
		*  
		* @return el html del boton.
		*/		
		function last_button() {
			if( $this->last != '' ){ # Posicionador de ultima pagina
				$ret = " ";
				$vinculo = $this->actual_page < $this->total_pages - 1;
				if( !$vinculo && $this->pointer_default_link ){
					$href = $this->pointer_default_link;
					$vinculo = true;
				} else {
					$href = $this->get_page_link( ($this->total_pages-1) );//$_SERVER['PHP_SELF'].'?cur_page='.($this->total_pages-1).$this->additional_params;
				}
				$ret .= $vinculo 
					? '<a href="'.$href.'" class="'.$this->class.'" title="'.$this->title_last_button.'" >' 
					: '';
				$ret .= !$vinculo && $this->alt_last != '' 
					? $this->alt_last 
					: $this->last;
				$ret .= $vinculo 
					? '</a>' 
					: '';
			}
			return $ret;
		}
		
		/**
		* globals_parser
		* Lee la lista de parametros adicionales y levanta sus valores de las globales si es necesario
		*  
		* @return un string con el armado de las variables adicionales.
		*/		
		function globals_parser ( $valores ) {
			preg_match_all(
				"/
				(.+?) 				# Nombre del campo
				(?(?==)
					=
					((?(?=\\s*')
						\\s*'.*?'|
						.*?
					))
				)
				(?(?=\\s*\\\\{0},)
					\\s*\\\\{0},|		# Coma separadora de tuplas
					\\s*$			# Final de la cadena
				)
				/sx",
				$valores,
				$pares
			);
			$campos = $pares[1];
			$valores = $pares[2];
			
			$add_get = '';
			
			for( $i = 0 ; $i < sizeof($campos) ; $i++) {
				$valor = '';
				$campo = trim( $campos[$i] );
				if( trim($valores[$i]) != '' ) {
					$valor = str_replace( '\,', ',', trim( $valores[$i] ) );
				} else {
					if( isset( $GLOBALS[$campo] ) ) {
						if( is_array( $GLOBALS[$campo] ) ) {
							$array_campo = $campo;
							foreach( $GLOBALS[$campo] as $array_valor ) {
								$array_valores[] = "'$array_valor'";
							}
						} elseif( is_string( $campo ) ) {
							$valor = $GLOBALS[$campo];
						} else {
							$valor = $GLOBALS[$campo];
						}
					}
				}
				if( $campo != '' && $valor != '' ) {
					$add_get .= "&amp;$campo=$valor";
				}
			}
			return $add_get;
		} # END function globals_params()
		
	} # END class pager
?>