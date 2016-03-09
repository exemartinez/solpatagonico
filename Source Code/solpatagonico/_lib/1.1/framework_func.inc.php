<?
	/**
	* navigation_route
	* Crea el Path de navegacion, para seguimiento, similar a Secciones.
	*  
	* @param integer $id_padre es el id del padre.
	* @param string $link es el valor del link si se necesita enviarlo a otro que no sea el del onClick.
	* @param bool $onclick si esta en false no lo agrega, sino se utiliza para hacer un update de los combos.
	* @param string $class es la clase de CSS utilizada.
	* @param string $var_name es el nombre de la variable para pasar el valor $id_padre.
	* @param string $table es nombre de la tabla de los valores.
	* @return la ruta de navegacion.
	*/
	function navigation_route( $id_padre, $link = '', $onclick = false, $class = 'texto', $var_name = 'id_padre', $table = 'sys_sections', $js_function = 'bajar_secciones', $js_params = '' ){
		$js_parameters = '';
		$sep = '';
		if( $js_params ){
			$aux = explode(',', $js_params);
			foreach( $aux as $variable ){
				$js_parameters .= $sep."'".$variable."'";
				$sep = ',';
			}
		}
		if( intval( $id_padre ) == 0 ){
			$ret = '<a href="'.($link!=''?$link.'?'.$var_name.'=0':'#').'" class="'.$class.'" ';
			$ret .= 'onclick="'.($onclick?$js_function."('0'".($js_parameters!=''?",".$js_parameters:'').")":'').'">Home</a>';
		} else {
			$db = newADOConnection( CFG_DB_dsn );
			$rs = $db->execute("SELECT * FROM $table WHERE id = $id_padre" );
			if( !$rs->EOF ){
				$padre = $rs->fetchObject(false);
			}
			$ret .= navigation_route( $padre->id_padre, $link, $onclick, $class, $var_name, $table, $js_function, $js_params).' <span class="'.$class.'">-></span> ';
			$ret .= '<a href="'.($link!=''?$link.'?'.$var_name.'='.$padre->id:'#').'" class="'.$class.'" ';
			$ret .= 'onclick="'.($onclick?$js_function."('".$padre->id."'".($js_parameters!=''?",".$js_parameters:'').")":'').'">'.printif($padre->nombre,$padre->titulo).'</a>';
			
		}
		return $ret;
	} # END function navigation_rout
	
	/**
	* get_menu_array
	* Obtiene el menu en formato de arreglo, en un string separado por comas.
	*  
	* @param integer $id_seccion es el id de la seccion.
	* @param string $sep es el separador.
	* @param string $table es nombre de la tabla de los valores.
	* @return los valores que obtiene de $table separados por $sep.
	*/
	function get_menu_array( $id_seccion, $sep = '', $table = 'sys_sections' ){
		$id_seccion = intval( $id_seccion );
		if( $id_seccion > 0 ){
			$db = newADOConnection( CFG_DB_dsn );
			$rs = $db->execute("SELECT * FROM $table WHERE id = $id_seccion");
			if( !$rs->EOF ){
				$reg = $rs->fetchObject( false );
			}
			$ret = $sep.$reg->id;
			$ret .= get_menu_array( $reg->id_padre, ',', $table );			
		}
		return $ret;
	} # END function get_menu_array

	/**
	* menu_array
	* Obtiene el menu en formato de arreglo.
	*  
	* @param string $table es nombre de la tabla de los valores.
	* @param string $field es el campo con el cual se compara $id.
	* @param integer $id es el id inicial.
	* @param string $order es el orden de seleccion del query.
	* @return los valores que obtiene de $table en un arreglo.
	*/
	function menu_array( $table = 'sys_sections', $field = 'id_padre', $id = 0, $order = 'posicion,nombre' ){
	    $id = intval( $id );
		$db = newADOConnection( CFG_DB_dsn );
		$db->debug=true;
		$regs = $db->execute("SELECT * FROM $table WHERE $field = $id ORDER BY ".$order );
	    if( $regs->recordCount() ){
			while( $reg = $regs->FetchNextObject() ){
				$ret[] = array( $reg->nombre );
				$tmp = menu_array( $table, $field, $reg->ID, $order );
				if( $tmp !== NULL ){
					$ret[] = $tmp;
				}
			}
	    }
	    return $ret;
	} # END function menu_array
	
	/**
	* create_map
	* Crea el mapa del menu.
	*  
	* @param integer $id_padre es el id del padre.
	* @param string $pos es la posicion de la columna para ubicar a los hijos.
	* @param string $last booleano que ubica si es o no el ultima para cerrar el arbol.
	* @return el arbol de navegacion.
	*/
	function create_map( $id_padre = 0, $pos = 0, $last = false ){
		$id_padre = intval( $id_padre );
		$db = newADOConnection( CFG_DB_dsn );
		$menues = $db->execute( "SELECT * FROM sys_sections 
			WHERE id_padre = $id_padre ".($id_padre==0?" OR id_padre IS NULL ":'')."
			ORDER BY posicion,nombre" );
		if( $id_padre == 0 ){
	?>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="path"><table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="../sys_images/map_folder.gif" border="0" /></td>
					<td class="path">&nbsp;<a class="path" href="<?=$_SERVER['PHP_SELF']?>">Home</a></td>
				</tr>
			</table></td>
		</tr><?
		}
		if( $menues->recordcount() > 0 ){ 
			$cant = 0;
			while( $menu = $menues->fetchNextObject() ){
				$cant++;
	?>
		<tr>
			<td><table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td nowrap><?
				for( $i = 0 ; $i < $pos ; $i++ ) {
					if( $i == $pos-1 && $last ){
						?><img src="../sys_images/px.gif" width="19" border="0" /><?
					} else {
						?><img src="../sys_images/map_i.png" border="0" /><?
					}
				}
				if( $cant == $menues->recordcount() ){
					$last = true;
					?><img src="../sys_images/map_l.png" border="0" /><?
				} else {
					?><img src="../sys_images/map_t.png" border="0" /><?
				}
				?><img src="../sys_images/map_folder.gif" border="0" /></td><td class="path">&nbsp;
				<a class="path" href="<?=$_SERVER['PHP_SELF']?>?id_seccion=<?=$menu->id?>"><?=$menu->nombre?> <?
					?></a></td>
				</tr></table>
			</td>
		</tr><?
				create_map( $menu->id, $pos+1, $last );//$ret[ $menu->id.'-'.$menu->nombre ] = create_map( $menu->id );
			}
		}
		if( $id_padre == 0 ){
		?></table><?
		}
	} # END function create_map

	function get_conf_value( $var ) {
		$db = NewADOConnection( CFG_DB_dsn );
		$rs = $db->execute( "SELECT valor FROM ".CFG_configTable." WHERE variable = '$var'" );
		return $rs->Fields('valor');
	}
	
	function write_log( $code, $usuario = '' ){
	/*
	Modificacion: 2008-07-15 12:31.
	Responsable: Manuel Dominguez.
	Descripcion: Se cambio el cambio tipo_entrada por id_entrada y no se utiliza mas el arreglo de codigos. Se paso el 
	INSERT a AutoExecute.
	*/
		global $USER;
		$db = NewADOConnection( CFG_DB_dsn );
		if( $usuario != '' ){
			$id_usuario = $db->GetOne( "SELECT id FROM ".CFG_usersTable." 
				WHERE ".CFG_authUserField." = '".$usuario."'" );
		} else {
			$id_usuario = $USER->id;
			$usuario = $USER->usuario;
		}
		if( CFG_logging ){
			$record['id_usuario'] = $id_usuario;
			$record['usuario'] = $usuario;
			$record['id_entrada'] = strtoupper( $code );
			$record['fecha'] = time();
			$record['ip'] = $_SERVER['REMOTE_ADDR'];
			$record['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

			$ok = $db->AutoExecute( CFG_logsTable, $record, 'INSERT' );
		}
	}
	
	function get_black_list( $user_id = 0 ){
	/*
	Modificacion: 2008-07-15 12:18.
	Responsable: Manuel Dominguez.
	Descripcion: Faltaba el "false" en el FetchNextObject de la obtencion de las claves.
	*/
		$user_id = intval( $user_id );
		$ret = CFG_passBlackList;
		if( $user_id != 0 && CFG_passBlockUserData ){
			$db = newADOConnection( CFG_DB_dsn );
			$reg = $db->getRow( "SELECT ".CFG_passBlockUserDataFields." FROM ".CFG_usersTable." WHERE id = '$user_id'" );
			foreach( $reg as $value ){
				if( $value != '' ) $ret .= ($ret!=''?',':'').$value;
			}
			$rs = $db->SelectLimit( "SELECT clave FROM ".CFG_passwordsTable." WHERE id_usuario = '$user_id' ORDER BY fecha DESC", CFG_passBlockHistory );
			while( $reg = $rs->FetchNextObject( false ) ){
				$ret .= ($ret!=''?',':'').$reg->clave;
			}
		}
		return $ret;
	}
	
	function set_error( $msg ){
		return "&middot;&nbsp;".$msg."<br />";
	}
	
	function get_language_section( $id_idioma, $id_seccion ){
		global $db;
		
		$ret = $db->GetRow( "
			SELECT * 
			FROM ".CFG_sectionsLangTable." 
			WHERE id_idioma='".$id_idioma."' 
				AND id_seccion='".$id_seccion."'
		" );
		if( $ret ){
			return $ret;
		} else {
			return $db->GetRow( "
				SELECT * 
				FROM ".CFG_sectionsLangTable." 
				WHERE id_idioma = ( SELECT id FROM ".CFG_langTable." WHERE principal ) AND id_seccion='".$id_seccion."'
			" );
		}
	}
	
	function get_language_value( $idioma, $claves ){
		global $db;
		
		# Buscamos en el idioma cargado en sesion
		$tmp = $_SESSION['lang'];
		foreach( split( ',', $claves ) as $clave ){
			$tmp = $tmp[$clave];
		}		
		if( !$tmp ){ # no existe la clave
			# Buscamos en el idioma principal
			include( CFG_languagePath.$db->GetOne( "SELECT id FROM ".CFG_langTable." WHERE principal" ).'.php' );
			$tmp = $LANG;
			foreach( split( ',', $claves ) as $clave ){
				$tmp = $tmp[$clave];				
			}
		}
		return $tmp;
	}
?>