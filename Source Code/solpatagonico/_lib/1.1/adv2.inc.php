<?
	class ADV {
		# Variables
		var $link;
		var $record;		
		var $source;
		var $sql_query = '';
		var $error = '';
		var $tbl_banners = 'adv_banners';
		var $tbl_zones = 'adv_zonas';
		var $tbl_clicks = 'adv_clicks';
		var $tbl_views = 'adv_views';
		var $aditional_params = '';
		var $file_click = 'inc/adv2/click.php';
		var $file_img = 'inc/file.php';
		var $field_contador = 'vistas';
		var $loaded = false;
		
		# Constructor
		function ADV( 
			$id_zone, # Zona a mostrar.
			$id_banner = 0, # Banner a mostrar.
			$file_click = 'inc/adv2/click.php', # Archivo de direccion al click.
			$file_img = 'inc/file.php', # Archivo que muestra la imagen.
			$tbl_banners = 'adv_banners', # Tabla de banners.
			$tbl_zones = 'adv_zonas', # Tabla de zonas.
			$tbl_clicks = 'adv_clicks', # Tabla de clicks.
			$tbl_views = 'adv_views', # Tabla de impresiones
			$aditional_params = '' # Parametros adicionales.
		){
			$this->id_zone = intval( $id_zone );
			$this->id_banner = intval( $id_banner );
			$this->file_click = $file_click;
			$this->file_img = $file_img;
			$this->tbl_banners = $tbl_banners;
			$this->tbl_zones = $tbl_zones;
			$this->tbl_clicks = $tbl_clicks;
			$this->tbl_views = $tbl_views;
			$this->aditional_params = $aditional_params;
			
			if( !$id_zone && !$id_banner ){ # Sin zona ni banner
				$this->error = 'No esta definido ni una zona ni un banner.';
				return false;
			} else {  # Zona / Banner definido.
				if( defined( 'CFG_DB_dsn') && CFG_DB_dsn != '' ){ # Chequeo de constantes de conexion a SQL
					$this->link = NewADOConnection( CFG_DB_dsn );
					if( $this->link->ErrorNo() ){ # Error al conectar con la BD
						$this->error = $this->link->ErrorMsg();
						return false;
					} else {
						if( $id_zone ){ # Zona definida.
							$this->load_zone( $id_zone ); # Cargamos un banner de la zona.
						} elseif( $id_banner ){ # Banner definido.
							$this->load_banner( $id_banner ); # Cargamos Banner.
						}
					}
				} else { # No estan definidas las constantes de conexion a SQL
					$this->error = 'No estan definidas las constantes de conexion a SQL: CFG_SQL_host, CFG_SQL_user, CFG_SQL_pass, CFG_SQL_db';
					return false;
				}				
			}
		}
		
		# Carga el banner de la zona definida
		function load_zone( $id = '' ){
			if( $id != '' ){
				$this->id_zone = $id;
			}
			$r = $this->query( "
				SELECT * 
				FROM ".$this->tbl_banners."
				WHERE habilitado ".
				( $this->id_zone != '0' ? "AND id_zona = '".$this->id_zone."' " : '' ).
				( $this->aditional_params ? $this->aditional_params." " : "" )." 
				AND ( expiracion='sin' OR ( expiracion='fecha' AND NOW() BETWEEN exp_inicio AND exp_fin )".
					( $this->tbl_clicks ? " OR ( expiracion='clicks' AND id IN ( 
						SELECT a.id FROM (
							SELECT b.* 
							FROM ".$this->tbl_banners." b 
							LEFT JOIN ".$this->tbl_clicks." c ON b.id = c.id_banner 
							WHERE 1=1 AND 
								b.expiracion='clicks' 
							GROUP BY c.id_banner 
							HAVING COUNT(c.id) <= b.exp_clicks 
						) a 
					) )" : "" ).
					( $this->tbl_views ? " OR ( expiracion='views' AND id IN (
						SELECT a.id FROM ( 
							SELECT b.* 
							FROM ".$this->tbl_banners." b 
							LEFT JOIN ".$this->tbl_views." v ON b.id = v.id_banner 
							WHERE 1=1 AND 
								b.expiracion='views' 
							GROUP BY v.id_banner 
							HAVING COUNT(v.id) <= b.exp_views 
						) a 
					) )" : "" 
				)." )
				ORDER BY ".$this->field_contador
			);
			if ( !$r->RecordCount() || $this->error ){
				$this->loaded = false;
			} else {
				$this->loaded = true;
			}
			return $this->loaded;
		}
		
		# Carga el banners segun $id_banner.
		function load_banner( 
			$id_banner # Banner a mostrar.
		){
			$r = $this->query( "
				SELECT *
				FROM ".$this->tbl_banners." 
				WHERE id='".$id_banner."'".(
					$this->aditional_params ? " ".$this->aditional_params : "" 
				)."
			" );
			if( !$r->RecordCount() || $this->error ){ # Error o Id incorrecto.
				$this->loaded = false;
			} else {
				$this->loaded = true;
				$this->record = $r->FetchObject( false );
			}
			return $this->loaded;
		}
		
		# Carga el siguiente banner
		function fetch_banner(){
			return $this->source->FetchNextObject( false );
		}
		
		# Setea el click al banner cargado.
		function set_click(){
			if( $this->tbl_clicks ){
				$r = $this->query( "
					INSERT INTO ".$this->tbl_clicks." 
					VALUES ( 
						'',
						'".$this->record->id."',
						NOW(),
						'".session_id()."',
						'".$_SERVER['REMOTE_ADDR']."',
						'".$_SERVER['HTTP_USER_AGENT']."',
						'".$_SERVER['HTTP_REFERER']."'
					)
				" );
				if( $this->error ){
					return false;
				} else {
					return true;
				}
			}
			return true;
		}
		
		# Setea la impresion al banner cargado.
		function set_view(){
			if( $this->tbl_views ){
				$r = $this->query( "
					INSERT INTO ".$this->tbl_views." 
					VALUES ( 
						'',
						'".$this->record->id."',
						NOW(),
						'".session_id()."',
						'".$_SERVER['REMOTE_ADDR']."',
						'".$_SERVER['HTTP_USER_AGENT']."',
						'".$_SERVER['HTTP_REFERER']."'
					)
				" );
				if( $this->error ){
					return false;
				} else {
					return true;
				}
			}
			return true;
		}
		
		# Obtiene los clicks del banner cargado.
		function get_clicks(){
			if( $this->tbl_clicks ){
				$r = $this->query( "
					SELECT COUNT(*) clicks
					FROM ".$this->tbl_clicks." 
					WHERE id_banner='".$this->record->id."'
				" );
				if( !$r->RecordCount() || $this->error ){
					return 0;
				} else {
					$reg = $r->FetchObject( false );
					return $reg->clicks;
				}
			}
			return 0;
		}
		
		# Obtiene las impresiones del banner cargado.
		function get_views(){
			if( $this->tbl_clicks ){
				$r = $this->query( "
					SELECT COUNT(*) views
					FROM ".$this->tbl_views." 
					WHERE id_banner='".$this->record->id."'
				" );
				if( !$r->RecordCount() || $this->error ){
					return 0;
				} else {
					$reg = $r->FetchObject( false );
					return $reg->views;
				}
			}
			return 0;
		}
		
		# Obtiene el banner segun la rotacion, la zona y los parametros adicionales.
		function get(){
			if( $this->loaded ){
				$this->record = $this->fetch_banner();
				if ( $this->rotation() && $this->set_view() ) {
					return $this->print_banner();							
				} else {
					return false;
				}
			} else return false;
		}
		
		# Genera la rotacion de los banners.
		function rotation(){
			$r = $this->query(
				"UPDATE ".$this->tbl_banners." 
					SET ".$this->field_contador." = ".$this->field_contador." + 1 
					WHERE id = '".$this->record->id."' "
			);
			if( $this->error ) { # Error ejecutar el query.
				return false;
			}
			return true;
		}
		
		# Imprime el header de redireccion del click.
		function url(){
			$link = $this->record->url;
			if( $this->set_click() ){
				# Agregamos http:// si no lo tiene.
				if( strtolower( substr( $link, 0, 7 ) ) != "http://" ) $link = "http://".$link;
				# Redireccionamos.
				header( "Location: ".$link );
			} else {
				return false;
			}
		}
		
		# Imprime el banners segun el tipo de banner.
		function print_banner(){
			switch( $this->record->tipo ){
				case 'image':
					return $this->print_image(); # Imprime banner de imangen.
					break;
				case 'flash':
					return $this->print_flash(); # Imprime banner en flash.
					break;
				case 'html':
					return $this->print_html(); # Imprime banner en html.
					break;
			}
		}
		
		# Imprime el banners en formato Image
		function print_image(){
			if( $this->record->banner != '' ){
				if( $this->record->url != '' ){
					?><a href="<?=$this->file_click?>?id=<?=$this->record->id?>" 
					target="<?=$this->record->target?>" ><?
				}
				?><img border="0" 
					src="<?=$this->file_img?>?id=<?=$this->record->id?>&section=<?=$this->tbl_banners?>&name=banner" 
					alt="<?=$this->record->alt?>" 
					<?=( $this->record->mapa != '' ? 'usemap="#MapADV"' : '' )?> ><?
				if( $this->record->mapa != '' ){
					?><map name="MapADV"><?=$this->record->mapa?></map><?
				}
				if( $this->record->url != '' ){
					?></a><?
				}
			} else {
				return false;
			}
		}
		
		# Imprime el banners en formato Flash
		function print_flash(){
			?><object type="application/x-shockwave-flash" 
				data="<?=$this->file_img?>?id=<?=$this->record->id?>&section=<?=$this->tbl_banners?>&name=banner" 
				width="<?=$this->record->ancho?>" 
				height="<?=$this->record->alto?>">
				<param name="movie" 
					value="<?=$this->file_img?>?id=<?=$this->record->id?>&section=<?=$this->tbl_banners?>&name=banner" />
				<param name="wmode" 
					value="transparent" />
			</object><?
		}
		
		# Imprime el banners en formato HTML
		function print_html(){
			if( $this->record->url != '' ){
				?><a href="<?=$this->file_click?>?id=<?=$this->record->id?>" 
				target="<?=$this->record->target?>" 
				title="<?=$this->record->alt?>" ><?
			}
			?><?=$this->record->html;?><?
			if( $this->url != '' ){
				?></a><?
			}
		}
		
		# Ejecucion del query.
		function query( 
			$query # Query a ejecturar.
		) {
			$this->source = $this->link->Execute( $query );
			$this->sql_query = $query;
			if( $this->link->ErrorNo() ){
				$this->error = $this->link->ErrorMsg();
				return false;
			} else {
				return $this->source;
			}
		}
	}
?>