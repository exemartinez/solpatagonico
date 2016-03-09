<?
	class imagepreview {
		# Propiedades/Parametros del constructor
		var $source; # Consulta SQL u objeto DBI a paginar
		var $record; # Registro del banner seleccionado.
		var $link = ''; # Conexión la Base de datos para el caso de $source como string SQL.
		var $error = ''; # Descripción de errores.
		var $image_file = ''; # Path del archivo que muestra las imagenes
		var $table; # Nombre de la tabla a consultar
		var $image_field; # Nombre del campo de la imagen
		var $aditional_params = ''; # Parametros adicionales de la consulta
		var $order_by = ''; # Ordenamiento de la consulta
		var $caption_field = ''; # Nombrel del campo del epigrafe de la imagen
		var $next_icon = '&gt;'; # Icono para la siguiente imagen
		var $prev_icon = '&lt;'; # Icono para la anterior imagen
		var $separator = " - "; # Icono separador de los numeros
		var $play_icon = 'play'; # Icono del boton play
		var $stop_icon = 'stop'; # Icono del boton stop
		var $autoplay = false; # Booleano para definir si se activa el autoplay o no
		var $show_thumbnails = false; # Booleano si se muestran los thumnails o no
		var $show_numbers = true; # Booleano para mostrar o no los numeros
		var $popup_view	= false; # Booleano para linkear al popup o no de la imagen para ampliar
		var $timeout = 3000; # Tiempo de timeout para el autoplay
		var $image_maxsize = 350; # Tamanio maximo de la imagen Chica
		var $thumbnail_maxsize = 40; # Tamanio maximo de la imagen Thumbnail
		
		function imagepreview (
			$table,
			$image_field,			
			$aditional_params = '',
			$order_by = '',
			$caption_field = '',
			$next_icon = '&gt;',
			$prev_icon = '&lt;',
			$separator = ' - ',
			$play_icon = 'play',
			$stop_icon = 'stop',
			$autoplay = false,
			$show_thumbnails = false,
			$show_numbers = true,
			$popup_view = false,
			$timeout = 3000,
			$image_maxsize = 350,
			$thumbnail_maxsize = 40
		){
			$this->image_file = ( defined( 'CFG_virtualPath' ) ? CFG_virtualPath : '' ).'inc/file.php';;
			$this->table = $table;
			$this->image_field = $image_field;			
			$this->aditional_params = $aditional_params;
			$this->order_by = $order_by;
			$this->caption_field = $caption_field;
			$this->next_icon = $next_icon;
			$this->prev_icon = $prev_icon;			
			$this->separator = $separator;
			$this->play_icon = $play_icon;
			$this->stop_icon = $stop_icon;
			$this->autoplay = $autoplay;
			$this->show_thumbnails = $show_thumbnails;
			$this->show_numbers = $show_numbers;
			$this->popup_view = $popup_view;
			$this->timeout = $timeout;
			$this->image_maxsize = intval( $image_maxsize );
			$this->thumbnail_maxsize = intval( $thumbnail_maxsize );
			
			if( defined( 'CFG_DB_dsn') && CFG_DB_dsn != '' ){ # Chequeo de constantes de conexion a SQL
				$this->link = NewADOConnection( CFG_DB_dsn );
				if( $this->link->ErrorNo() ){ # Error al conectar con la BD
					$this->error = $this->link->ErrorMsg();
					return false;
				} else {
					$this->source = $this->query( "
						SELECT * 
						FROM ".$this->table." 
						WHERE 1=1 ".( $this->aditional_params ? 'AND '.$this->aditional_params : '' )." 
						".( $this->order_by ? 'ORDER BY '.$this->order_by : '' )."
					" );
				}
			} else { # No estan definidas las constantes de conexion a SQL
				$this->error = 'No estan definidas las constantes de conexion a SQL: CFG_SQL_host, CFG_SQL_user, CFG_SQL_pass, CFG_SQL_db';
				return false;
			}
		}
		
		function print_default_css(){
			?><style type="text/css">
			<!--
			.IP_actual_number {
				color: #FF0000;
				font-weight: bold;
			}
			#IP_next_button, #IP_prev_button, #IP_numbers, .IP_numbers, #IP_play_button  {
				color:#003366;
			}
			#IP_next_button, #IP_prev_button, #IP_play_button, #IP_stop_button {
				font-weight: bold;
			}
			#IP_next_button, #IP_prev_button, .IP_actual_number, .IP_numbers, #IP_play_button, .IP_captions  {
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
				text-decoration: none;
			}
			#IP_container{
				text-align:center;				
			}
			#IP_image_buffer{
				<?=( $this->image_maxsize ? 'width: '.$this->image_maxsize.';' : '' );?>
				<?=( $this->image_maxsize ? 'height: '.$this->image_maxsize.';' : '' );?>
			}
			#IP_image_buffer img{
				
			}
			#IP_buttons{
				padding:2px;
				vertical-align:middle;
			}
			#IP_prev{
				float:left;
				vertical-align:middle;
			}
			#IP_numbers{
				float:left;
				text-align:center;
				padding:2px;
				vertical-align:middle;
				overflow: auto;
			}
			#IP_next{
				float:left;
				vertical-align:middle;
			}
			#IP_thumbnails{
				text-align:center;
				clear:both;
			}
			-->
			</style><?
		}
		
		function print_javascript(){
			?><script>
			<!--
				function IP_image_resize( id_image, maxsize ){
					var image = document.getElementById( id_image );
					var width = image.width;
					var height = image.height;
					
					if( width > maxsize || height > maxsize ){
						if( width > height ){							
							image.width = maxsize;
						}
						if( width < height ){							
							image.height = maxsize;
						}
						if( width == height ){
							image.width = maxsize;
							image.height = maxsize;
						}
					}
				}
				
				var IP_total_images = <?=$this->source->RecordCount()?>;
				var IP_actual_image = 1;
				var IP_scrolling = <?=( $this->autoplay ? 'true' : 'false' )?>;
				var IP_numbers = <?=( $this->show_numbers ? 'true' : 'false' )?>;
				var IP_caption = <?=( $this->caption_field != '' ? 'true' : 'false' )?>;
				function IP_image_switch( id ){
					var actual_image = document.getElementById( 'IP_image_' + IP_actual_image );
					if( IP_numbers ){ var actual_number = document.getElementById( 'IP_number_' + IP_actual_image ); }
					if( IP_caption ){ var actual_caption = document.getElementById( 'IP_caption_' + IP_actual_image ); }
					
					if( id < 1 ){
						id = IP_total_images;
					}
					if( id > IP_total_images ){
						id = 1;
					}
					if( id >= 1 && id <= IP_total_images ){			
						var new_image= document.getElementById( 'IP_image_' + id );
						if( IP_numbers ){ var new_number = document.getElementById( 'IP_number_' + id ); } 
						if( IP_caption ){ var new_caption = document.getElementById( 'IP_caption_' + id ); }
						
						actual_image.style.display = 'none';
						if( IP_numbers ){ actual_number.className = 'IP_numbers'; }
						if( IP_caption ){ actual_caption.style.display = 'none'; }
						
						new_image.style.display = 'block';
						if( IP_numbers ){ new_number.className = 'IP_actual_number'; }
						if( IP_caption ){ new_caption.style.display = 'block'; }
						
						IP_actual_image = id;						
						<?=( $this->image_maxsize ? 'IP_image_resize( \'IP_image_\'+id, '.$this->image_maxsize.' );' : '' )?>
					}
				}
				
				function AdaptarVentana() {
					var altoimagen=document.imagen.height;
					var anchoimagen=document.imagen.width;
					if (navigator.appName.indexOf("Microsoft")!=-1) { 
						self.resizeTo (anchoimagen+50,altoimagen+190); 
					} 
					if (navigator.appName=="Netscape") { 
						window.innerWidth=anchoimagen; 
						window.innerHeight=altoimagen; 
					} 
				}
				
				function IP_popup_view( id, section, name, caption ){
					var new_window = window.open( '', 'IP_wdw', 'menubar=no, toolbar=no, statusbar=no' );
					var content = '<html>\n';
					content += '<body>\n';
					content += '<div align="center">\n';
					// IMAGEN
					content += '<img src="<?=$this->image_file?>?id=' + id + '&section=' + section + '&name=' + name;
					content += '" border="0" name="imagen" id="imagen" onLoad="';
					content += 'var altoimagen=document.imagen.height+80;';
					content += 'var anchoimagen=document.imagen.width+60;';
					content += 'if (navigator.appName.indexOf(\'Microsoft\')!=-1){';
					content += '  self.resizeTo (anchoimagen,altoimagen);';
					content += '}';
					content += 'if (navigator.appName==\'Netscape\'){';
					content += '  window.innerWidth=anchoimagen;';
					content += '  window.innerHeight=altoimagen;';
					content += '}">\n';
					// IMAGEN
					if( caption != '' ){
						content += '<br><span style="font-family: tahoma;">' + caption + '</span>\n';
					}
					content += '<br><br><input type="button" value="Cerrar Ventana" onClick="window.close();"></div></body></html>\n';
					
					new_window.name = 'imgs';
					new_window.document.write( content );
					new_window.document.close();
					new_window.focus();
				}
				
				function IP_scroll(){
					if( IP_scrolling === true ){
						IP_image_switch( IP_actual_image + 1 );						
						setTimeout( "IP_scroll()", <?=$this->timeout?> );
					}
				}
				
				function IP_play(){
					IP_scrolling = true;
					document.getElementById( 'IP_play_button' ).innerHTML = '<?=$this->stop_icon?>';
					document.getElementById( 'IP_play_button' ).href = 'javascript:IP_stop();';
					IP_scroll();
				}
				
				function IP_stop(){
					IP_scrolling = false;
					document.getElementById( 'IP_play_button' ).innerHTML = '<?=$this->play_icon?>';
					document.getElementById( 'IP_play_button' ).href = 'javascript:IP_play();';
				}
			//-->
			</script><?
		}
		
		function get_preview(){
			if( $this->source && $this->source->RecordCount() ){
				?><div id="IP_container">
					<div id="IP_image_buffer" align="center"><?
				$i = 1;
				$thumbnail = '';
				while( $this->record = $this->source->FetchNextObject( false ) ){
					?><img 
						src="<?=$this->image_file?>?id=<?=$this->record->id?>&section=<?=$this->table?>&name=<?=$this->image_field?>" 
						border="0" 
						hspace="5" 
						vspace="5" 
						name="IP_image_<?=$i?>" 
						id="IP_image_<?=$i?>" 
						style="display:<?=( $i == 1 ? 'block' : 'none' )?>" 
						<?=( $this->image_maxsize ? 'onLoad="IP_image_resize( \'IP_image_'.$i.'\', '.$this->image_maxsize.' )"' : '' )?>
						<?=( $this->popup_view ? 'onClick="IP_popup_view( '.$this->record->id.', \''.$this->table.'\', \''.$this->image_field.'\', \''.( $this->caption_field ? $this->record->{$this->caption_field} : '' ).'\' )" style="cursor:hand"' : '' )?>
					/><?
					if( $this->caption_field != '' ){
						?><div id="IP_caption_<?=$i?>" style="display:<?=( $i == 1 ? 'block' : 'none' )?>">
							<span class="IP_captions"><?=$this->record->{$this->caption_field}?></span>
						</div><?
					}
					if( $this->show_thumbnails ){
						$thumbnail .= '<a href="javascript:IP_image_switch( '.$i.' )"><img 
							src="'.$this->image_file.'?id='.$this->record->id.'&section='.$this->table.'&name='.$this->image_field.'" 
							border="0" 
							name="IP_thumbnail_'.$i.'" 
							id="IP_thumbnail_'.$i.'" 
							hspace="2" 
							'.( $this->thumbnail_maxsize ? 'onLoad="IP_image_resize( \'IP_thumbnail_'.$i.'\', '.$this->thumbnail_maxsize.' )"' : '' ).' 
						/></a>';
					}
					$i++;
				}
					?></div>
					<div id="IP_buttons"><?
				if( $this->prev_icon != '' ){
						?><a href="javascript:IP_image_switch( IP_actual_image - 1 )" id="IP_prev_button"><?=$this->prev_icon?></a>&nbsp;<?
				}
				if( $this->play_icon != '' && !$this->autoplay ){
					?><a href="javascript:IP_play()" id="IP_play_button"><?=$this->play_icon?></a>&nbsp;<?
				}
				if( $this->stop_icon != '' && $this->autoplay ){
					?><a href="javascript:IP_stop()" id="IP_play_button"><?=$this->stop_icon?></a>&nbsp;<?
				}
				if( $this->show_numbers ){
					$sep = '';
					for( $i = 1; $i <= $this->source->RecordCount(); $i++ ){
						?><?=$sep?><a href="javascript:IP_image_switch( <?=$i?> )" class="<?=( $i == 1 ? 'IP_actual_number' : 'IP_numbers' )?>" id="IP_number_<?=$i?>"><?=$i?></a><?
						$sep = $this->separator;
					}
					?>&nbsp;<?
				}
				if( $this->next_icon != '' ){
						?><a href="javascript:IP_image_switch( IP_actual_image + 1 )" id="IP_next_button"><?=$this->next_icon?></a>&nbsp;<?
				}
					?></div><?
				if( $this->show_thumbnails ){
					?><div id="IP_thumbnails">
						<?=$thumbnail;?>
					</div><?
				}
				?></div><?
				if( $this->autoplay ){
					?><script>
					<!--
						IP_scroll();
					//-->
					</script><?
				}
			}
		}
		
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