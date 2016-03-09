<?
	/**
	* Libreria de Banners
	* Rotatión de banners
	*/
	
	class ADV {
		var $source;								# Consulta SQL.
		var $id_type = 'noexpire';					# Tipo de seleccion.
		var $id_zona;								# Valor de la zona a mostrar.
		var $id_idioma;								# Valor de idioma a mostrar.
		var $id_class = 'banner';					# Nombre de la Clase de CSS en formato HTML.
		var $record;								# Registro del banner seleccionado.
		var $tbl_banners = 'adv_banners';			# Tabla de banners.
		var $tbl_zonas = 'adv_zonas';				# Tabla de zonas.
		var $sql_query = '';						# Codigo SQL ejecutado para la paginación.
		var $link = '';								# Conexión la Base de datos para el caso de $source como string SQL.
		var $error = '';							# Descripción de errores.	
		var $file_click = 'inc/adv/click.php';		# Archivo redirector.
		var $file_img = 'inc/file.php';					# Archivo blob print.
		var $field_habilitado = 'habilitado';		# Campo que indica si el banner esta habilitado para ser mostrado o no
		var $field_contador = 'vistas';				# Campo que indica la cantidad de veces que fue visto un banner. Usado para la rotacion
		
		/**
		* ADV
		* Constructor de rotatión.
		* @param integer $id_zona es el id de la zona a mostrar.
		*/
		function ADV (						
			$id_zona,								# Valor de la zona a mostrar.
			$id_type = 'noexpire',					# Tipo de seleccion.
			$id_class = 'banner',					# Valor de la zona a mostrar.
			$file_img = 'inc/file.php',					# Archivo blob print.
			$file_click = 'inc/adv/click.php',		# Archivo redirector.
			$tbl_banners = 'adv_banners',			# Tabla de banners.
			$tbl_zonas = 'adv_zonas'				# Tabla de zonas.
			){
			$this->id_type = strtolower($id_type);
			$this->id_zona = intval($id_zona);
			$this->id_class = $id_class;
			$this->file_img = $file_img;
			$this->file_click = $file_click;

			if( defined( 'CFG_DB_dsn') && CFG_DB_dsn != '' ){ # Chequeo de constantes de conexion a SQL
				$this->link = NewADOConnection( CFG_DB_dsn );
				if( $this->link->ErrorNo() ){ # Error al conectar con la BD
					$this->error = $this->link->ErrorMsg();
					return false;
				} 
			} else { # No estan definidas las constantes de conexion a SQL
				$this->error = 'No estan definidas las constantes de conexion a SQL: CFG_SQL_host, CFG_SQL_user, CFG_SQL_pass, CFG_SQL_db';
				return false;
			}
		} # END function ADV
		
		/**
		* get_banner
		* Seleccion del banner según tipo noexpire, expire.
		*  
		* @return string.
		*/
		function get_banner() {
			switch ( $this->id_type ) {
				case 'noexpire':
					$this->source = $this->query( "
						SELECT * 
						FROM ".$this->tbl_banners."
						WHERE ".$this->field_habilitado." = 1".
						( $this->id_zona != '0' ? " AND id_zona = '".$this->id_zona."' " : '' ).
						( $this->id_idioma != '' ? " AND id_idioma = '".$this->id_idioma."' " : '' )."
						ORDER BY ".$this->field_contador
					);
					if ( $this->source ){ # Hay un banner
						$this->record = $this->source->FetchObject( false );
						if ( $this->banner_rotation() ) {
							$this->print_banner();							
						} else {
							return false;
						}
					}					
					break;
				case 'expire':
					/*
						---> PENDIENTE <---
					*/
					break;
			}
		} # END get_banner	
		
		/**
		* banner_rotation
		* Hace la rotacion actualizando el campo $this->field_contador.
		*  
		* @return string.
		*/
		function banner_rotation(){
			$r = $this->query(
				"UPDATE ".$this->tbl_banners." 
					SET ".$this->field_contador." = ".$this->field_contador." + 1 
					WHERE id = '".$this->record->id."' "
			);
			if( $this->error ) { # Error ejecutar el query.
				return false;
			}
			return true;
		} # END banner_rotation

		/**
		* print_banner
		* Imprime el banner.
		*  
		* @return string.
		*/
		function print_banner() {
			if ( strstr( $this->record->imagen_mime, "image" ) != NULL ){
				if( $this->record->url != '' ){
					?><a href="<?=$this->file_click?>?id=<?=$this->record->id?>" target="<?=$this->record->target?>"><?
				}
				?><img 
		border="0" src="<?=$this->file_img?>?id=<?=$this->record->id?>&section=<?=$this->tbl_banners?>&name=imagen" 
		alt="<?=$this->record->alttext?>"><?
				if( $this->url != '' ){
					?></a><?
				}
			} elseif ( strstr( $this->record->imagen_mime, "flash" ) != NULL ) {
?>
		<object type="application/x-shockwave-flash" data="<?=$this->file_img?>?id=<?=$this->record->id?>&section=<?=$this->tbl_banners?>&name=imagen&link=<?=$this->file_click?>?id=<?=$this->record->id?>" 
		width="<?=$this->record->imagen_ancho?>" height="<?=$this->record->imagen_alto?>">
			<param name="movie" 
			value="<?=$this->file_img?>?id=<?=$this->record->id?>&section=<?=$this->tbl_banners?>&name=imagen&link=<?=$this->file_click?>?id=<?=$this->record->id?>"/>
			<param name="wmode" value="transparent">
		</object>
<?
			}
		} # END print_banner
		
		function query( $query ) {
			$result = $this->link->Execute( $query );
			$this->sql_query = $query;
			if( $this->link->ErrorNo() ){
				$this->error = $this->link->ErrorMsg();
				return false;
			} else {
				return $result;
			}
		}
	}
?>
