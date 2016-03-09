<?
	include_once('../inc/conf.inc.php');
	$title = 'Grupos';
	$tabla = CFG_groupsTable;
	# Mensajes
	$msgDelete	= '¿Esta seguro que desea eliminar este Grupo?\nSi lo elimina, se eliminará también todos los usuarios que dependan del mismo y sus respectivos hostoriales.';
	$msgUnique	= 'El nombre de grupo ingresado ya existe, ingrese otro.';
    #
	$campos = 'nombre, nota, skip_maintenance';
	registrar( $campos.",action,id,id_seccion,sec,cur_page,nombre_grupo" );
	$id = intval($id);
	$skip_maintenance = intval($skip_maintenance);

	if ( in($action, 'Alta', 'Modificar') ) {
		$record = gen_record( $campos );
		$error = '';
		if ( !validate::Text($nombre) ) {
			$action = 'frm'.$action;
			$error=1;
		} 
	}

	if ( $action == 'Alta' ){
		$rs = $db->getrow( "SELECT * FROM ".CFG_groupsTable." WHERE nombre = '$nombre'" );
		if( $rs ){
			$error = '<li>'.$msgUnique.'</li>';
			$action = 'frm'.$action;
		} else {
			$record['owner_id'] = $USER->id;
			$record['owner_ip'] = $_SERVER['REMOTE_ADDR'];
			$record['owner_date'] = date( "Y-m-d H:i:s" );
			$ok = $db->Execute( get_sql( 'INSERT', CFG_groupsTable, $record, '' ) );
			if( !ok ){
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			} else {
				$error = '';
				$action = '';
			}
		}
	}
	if ( $action == 'Modificar' ){
		$rs = $db->getrow( "SELECT * FROM ".CFG_groupsTable." WHERE nombre = '$nombre' AND id <> '$id'" );
		if( $rs ){
			$error = '<li>'.$msgUnique.'</li>';
			$action = 'frm'.$action;
		} else {
			$record['last_user_id'] = $USER->id;
			$record['last_user_ip'] = $_SERVER['REMOTE_ADDR'];
			$record['last_user_date'] = date( "Y-m-d H:i:s" );	
			$ok = $db->Execute( get_sql( 'UPDATE', CFG_groupsTable, $record, "id = '$id'" ) );
			if( !ok ){
				$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
				$action = 'frm'.$action;
			} else {
				$action = '';
			}
		}
	}
	if ( $action == 'Baja' ){
		$ok = $db->execute( "DELETE FROM ".CFG_privilegesTable." WHERE id_grupo = '$id'" );
		$ok = $db->execute( "DELETE FROM ".CFG_groupsPrivilegesTable." WHERE id_grupo = '$id'" );
		$ok = $db->execute( "DELETE FROM ".CFG_groupsTable." WHERE id = '$id'" );
		if( !$ok ){
			$error = '<li>Error al borrar el grupo.</li>';
		} else {
			$error = '';
		}
		$action = '';
	}
	if ( $action == 'permisos' ){
		$db->execute( "DELETE FROM ".CFG_privilegesTable." WHERE id_grupo = '$id'" );	
		if( is_array( $sec ) ){
			unset($record);
			$record['id_grupo'] = $id;
			foreach( $sec as $id_seccion ){
				$record['id_seccion'] = $id_seccion;
				$ok = $db->Execute( get_sql( 'INSERT', CFG_privilegesTable, $record, '' ) );
			}
		}
		$action = '';
	}
	if ( $action == 'acciones' ){
		$db->execute( "DELETE FROM ".CFG_groupsPrivilegesTable." WHERE id_grupo = '$id'" );
		if( is_array( $valor ) ){
			unset($record);
			$record['id_grupo'] = $id;
			foreach( $valor as $id_accion => $value ){
				$record['id_accion'] = $id_accion;
				$record['valor'] = $value;
				$ok = $db->Execute( get_sql( 'INSERT', CFG_groupsPrivilegesTable, $record, '' ) );
			}
		}
		$action = '';
	}
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css">
	<script type="text/javascript" src="<?=CFG_jsPath?>download.php"></script>
</head>

<body>
<?
	if ( in($action,'','buscar') ) {
		$filtros = "";
		$orden = ( $orden ? $orden : 'nombre');
		$pager = new pager(
			"SELECT *
			FROM ".CFG_groupsTable."
			WHERE 1=1 
				AND nombre LIKE '%$nombre_list%'".(
				$USER->id_grupo != 1 ? " AND id <> 1" : '' 
			)." ORDER BY ".( $orden ? $orden : 'nombre'),
			$cur_page,
			20,
			25,
			'nombre_list',
			''
		);
?>
<script type="text/javascript" language="javascript">
<!--
	var item_abierto = '';
	function abrir_item( n_item ){
		cerrar_item( item_abierto );
		eval( 'boton=document.getElementById("boton_'+n_item+'")' );
		eval( 'lista=document.getElementById("lista_'+n_item+'")' );
		boton.style.display = 'none';
		lista.style.display = 'block';
		item_abierto = n_item;
	}
	function cerrar_item( n_item ){
		if( n_item != '' ){
			eval( 'boton=document.getElementById("boton_'+n_item+'")' );
			eval( 'lista=document.getElementById("lista_'+n_item+'")' );
			lista.style.display = 'none';
			boton.style.display = 'block';
			item_abierto = '';
			document.getElementById( 'body' ).style.display = 'none';
			document.getElementById( 'body' ).style.display = 'block';
		}
	}
-->
</script>
<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="buscar" />
<table width="640" border="0" align="center" cellpadding="3" cellspacing="0">
	<tr> 
		<th class="titulosizquierda" align="center" colspan="4">Grupos</th>
	</tr>
<?
		if ( $error != '' ){
?>
		<tr> 
			<td colspan="4" class="cuadro1izquierda"><span class="error"><?=$error?></span></td>
		</tr>
<?
		}
?>
	<tr>
		<td class="cuadro1izquierda" colspan="4">
			<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
				<tr>
					<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAlta"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAlta">Agregar Grupo</a></td>
				</tr>
				<tr>
				  <td><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                    <tr>
                      <td width="80">Nombre:</td>
                      <td><input name="nombre_list" type="text" id="nombre_list" value="<?=$nombre_list?>" 
									style="width:95% " /></td>
                      <td width="100"><input name="submit" type="submit" value="Buscar" /></td>
                    </tr>
                  </table></td>
			  </tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="sombra" align="right" colspan="4">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="text">
				<tr>
					<td>&nbsp;Registros: <?=$pager->get_first_pos()?> al <?=$pager->get_last_pos()?> de <?=$pager->get_total_records()?></td>
					<td align="right">
<?
		if( $pager->get_total_pages() > 0 ) {
?>
						P&aacute;g<?=$pager->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$pager->get_navigator(); ?>&nbsp;
<?
		}
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr align="center">
		<td width="140" align="left" class="titulosizquierda" <?=( strstr($orden,"nombre") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=nombre ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=nombre DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Nombre</td>
		<td width="210" align="center" class="titulos">Lista de Permisos</td>
		<td width="200" align="left" class="titulos" <?=( strstr($orden,"nota") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=nota%20ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=nota%20DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Nota</td>
		<td width="90" align="center" class="titulos">&nbsp;</td>
	</tr>
<?
		if( $pager->num_rows() < 1 ){
?>
	<tr>
		<td colspan="4" align="center" class="cuadro1"><span class="error">No hay registros coincidentes</span></td>
	</tr>
<?
		} else {
			$n = -1;
			while( $grupo = $pager->fetch_object() ){
				$n++;
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
	<tr>
		<td class="<?=$class?>izquierda" valign="top"><a name="id<?=$grupo->ID?>"></a><?=$grupo->nombre?></td>
		<td class="<?=$class?>" valign="top">
<?
				$rs = $db->execute( "SELECT DISTINCT s.nombre,s.id, s.id_padre, s.posicion
					FROM ".CFG_privilegesTable." p
					LEFT JOIN ".CFG_sectionsTable." s ON s.id = p.id_seccion
					WHERE p.id_grupo = $grupo->id /*AND S.ID_PADRE IS NOT NULL*/
					ORDER BY s.posicion,s.nombre" );
				$lista_items='';
				if( $rs->RecordCount() > 0 ){
					$lista_items .= "Este nodo posee los siguientes items:";
					$lista_items .= '<table width="90%">';
					while ( $permiso = $rs->FetchNextObject(false) ) {
						$lista_items .= '
							<tr class="'.( $class == 'cuadro1' ? 'cuadro2' : 'cuadro1' ).'">
								<td>&bull;&nbsp;'.navigation_route( $permiso->id )/*$permiso->nombre*/.'</td>
							</tr>
							';
					}
					$lista_items .= "</table>";
				}
				if( $lista_items == '' ){
?>
					Este nodo no posee items
<?
				} else {
?>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr id="boton_<?=$n?>">
					<td class="titulos" style="cursor:pointer" 
						onClick="document.getElementById('lista_<?=$n?>').style.display = document.getElementById('lista_<?=$n?>').style.display == 'block' ? 'none' : 'block' ">Ver Items</td>
				</tr>
				<tr id="lista_<?=$n?>" style="display: none">
					<td class="<?= $class == 'cuadro1' ? 'cuadro2' : 'cuadro1' ?>"><?=$lista_items?></td>
				</tr>
			</table>
<?
				}
?>
		</td>
		<td class="<?=$class?>" valign="top"><?=$grupo->nota==''?'&nbsp;':nl2br($grupo->nota)?></td>
		<td class="<?=$class?>" align="center" valign="top">&nbsp;<a 
			href="<?=$_SERVER['PHP_SELF']?>?id=<?=$grupo->id?>&nombre_grupo=<?=$grupo->nombre?>&action=frmPermisos"><img
			src="<?=CFG_virtualPath?>sys_images/permisos.gif" alt="Asignar Permisos" border="0" /></a>&nbsp;<a 
			href="<?=$_SERVER['PHP_SELF']?>?id=<?=$grupo->id?>&nombre_grupo=<?=$grupo->nombre?>&action=frmAcciones"><img
			src="<?=CFG_virtualPath?>sys_images/acciones.gif" alt="Asignar Acciones" border="0" /></a>&nbsp;<a 
			href="<?=$_SERVER['PHP_SELF']?>?id=<?=$grupo->id?>&action=frmModificar"><img 
			src="<?= CFG_virtualPath ?>sys_images/list.gif" alt="Modificar Registro" border="0" /></a>&nbsp;<a 
			href="<?=$_SERVER['PHP_SELF']?>?id=<?=$grupo->id?>&action=frmBaja"><img 
			src="<?= CFG_virtualPath ?>sys_images/delete.gif" alt="Eliminar Registro" border="0" /></a>&nbsp;</td>
	</tr>
<?
			}
		}
?>
	<tr>
		<td colspan="4" class="sombra">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="text">
				<tr>
					<td>&nbsp;Registros: <?=$pager->get_first_pos()?> al <?=$pager->get_last_pos()?> de <?=$pager->get_total_records()?></td>
					<td align="right">
<?
		if( $pager->get_total_pages() > 0 ) {
?>
						P&aacute;g<?=$pager->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$pager->get_navigator(); ?>&nbsp;
<?
		}
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?
	}

	if( in( $action, 'frmAlta', 'frmModificar', 'frmBaja' ) ){
		$rs = $db->selectlimit( "SELECT * FROM ".CFG_groupsTable." WHERE id = '$id'", 1 );
		if( $rs ){
			$grupo = $rs->fetchObject(false);
		}
		if( $action == 'frmBaja' ){
			$usuarios = $db->GetOne( "SELECT COUNT(*) FROM ".CFG_usersTable." WHERE id_grupo = '".$id."'" );
		}
		Form::validate();
?>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return validate(this)">
<input type="hidden" name="id" value="<?=$id?>" />
<table width="640" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<th colspan="2" class="titulosizquierda" align="center"><?=$action=='frmModificar'?'Modificar':($action=='frmBaja'?'Borrar':'Agregar')?> Grupo</th>
	</tr>
<?
		if ( $error != '' ){
?>
		<tr> 
			<td colspan="2" class="cuadro1izquierda"><span class="error"><?=$error?></span></td>
		</tr>
<?
		}
?>
	<tr>
		<td width="150" class="cuadro1izquierda"><?=$error?'<font color="#FF0000">*&nbsp;&nbsp;&nbsp;</font>':''?> Nombre</td>
		<td class="cuadro1">
<?
		if( $action == 'frmBaja' ){
			echo $grupo->nombre;
		} else {
?>
			<input style="width: 95%" validar="str:Debe ingresar un nombre de grupo." type="text" name="nombre" value="<?=printif($nombre,$grupo->nombre)?>">
<?
		}
?>
		</td>
	</tr>
	<tr style="display: <?=( $USER->id_grupo == 1 ? 'block' : 'none' )?>">
	    <td class="cuadro1izquierda">Saltea Mantenimiento</td>
	    <td class="cuadro1">
<?
		if( $action != 'frmBaja' && $USER->id_grupo == 1 ){
?>
			<input type="checkbox" name="skip_maintenance" value="1" <?=$grupo->skip_maintenance?'checked':''?> />
<?
		} else {
			echo $grupo->skip_maintenance ? 'Si' : 'No';
		}
?>
		</td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Nota</td>
		<td class="cuadro1">
<?
		if( $action == 'frmBaja' ){
			echo printif(nl2br($grupo->nota));
		} else {
?>
			<textarea style="width: 95%" name="nota" rows="3" wrap="VIRTUAL"><?=printif($nota,$grupo->nota)?></textarea><?
		}
?>
		</td>
	</tr>
<?
		if ( $action == 'frmModificar' ){
			if ( $grupo->owner_id != 0 ){
?>
	<tr>
		<td class="cuadro1izquierda">Creación:</td>
		<td class="cuadro1"><?="[".fecha::iso2normal( substr( $grupo->owner_date, 0, 10 ) )." ".substr( $grupo->owner_date, 11, 8). "] [".$grupo->owner_ip."] ".$db->getone( "SELECT concat(nombre,' ','[',usuario,']') FROM ".CFG_usersTable." WHERE id = '".$grupo->owner_id."'" )?></td>
	</tr>
<?
			}
			if ( $grupo->last_user_id != 0 ){
?>
	<tr>
		<td class="cuadro1izquierda">Ultima modificación:</td>
		<td class="cuadro1"><?="[".fecha::iso2normal( substr( $grupo->last_user_date, 0, 10 ) )." ".substr( $grupo->last_user_date, 11, 8). "] [".$grupo->last_user_ip."] ".$db->getone( "SELECT concat(nombre,' ','[',usuario,']') FROM ".CFG_usersTable." WHERE id = '".$grupo->last_user_id."'" )?></td>
	</tr>
<?	
			}
		}
		
		if( $action == 'frmBaja' && $usuarios ){
?>
	<tr>
		<td class="cuadro1izquierda">&nbsp;</td>
		<td class="cuadro1"><span class="error">El grupo no sera borrado ya que hay <?=$usuarios?> 
		usuario<?=( $usuarios > 1 ? 's' : '' )?> asignados al mismo. Borre los usuarios o reasignelos a otro grupo 
		y ejecute nuevamente esta acci&oacute;n.</span><br>
		<a href="usuarios.php">Ir a usuarios</a></td>
	</tr>
<?		
		}
?>
	<tr>
		<td class="cuadro2izquierda">&nbsp;<input type="hidden" name="action" value="<?=$action=='frmModificar'?'Modificar':($action=='frmBaja'?'Baja':'Alta')?>"></td>
		<td class="cuadro2"><?
		if( in( $action, 'frmAlta', 'frmModificar' ) || ( $action == 'frmBaja' && !$usuarios ) ){
		?><input type="submit" value="Aceptar">&nbsp;<?
		}
		?><input type="button" value="Cancelar" name="Button" onClick="javascript:location = '<?=$_SERVER['PHP_SELF']?>'"></td>
	</tr>
</table>
</form>
<?
	}

	if( $action =='frmPermisos' ){
?>
<script language="javascript" type="text/javascript">
<!--
	function sel( obj ){
		var elems = document.getElementsByName('sec[]');
		var str = '';
		var sep = '';
		for( i = 0 ; i < elems.length ; i++ ){
			elem = elems[i];
			if( elem.padre == obj.id ){
				elem.checked = obj.checked;
				//elem.click();
				//sel( elem );
			}
		}
	}
-->
</script>
<?
	function show_sections( $id_padre = 0, $pos = 0, $last = false, $class = 'text' ){
		global $id;
		global $USER;
		$id_padre = intval( $id_padre );
		$db = newADOConnection( CFG_DB_dsn );
		if( $USER->id_grupo == 1 ) {
			$regs = $db->execute( "SELECT * 
				FROM ".CFG_sectionsTable." 
				WHERE id_padre = $id_padre ".($id_padre==0?" OR id_padre IS NULL OR id_padre = 0":'')."
				ORDER BY posicion, nombre" );
		} else {
			$regs = $db->execute( "SELECT * 
				FROM ".CFG_privilegesTable." p 
				JOIN ".CFG_sectionsTable." s ON s.id = p.id_seccion
				WHERE p.id_grupo = $USER->id_grupo AND(s.id_padre = $id_padre ".($id_padre==0?" OR s.id_padre IS NULL OR s.id_padre = 0":'').")
				ORDER BY s.posicion, s.nombre" );
		}
		if( $id_padre == 0 ){
?>
<table border="0" cellpadding="0" cellspacing="0" class="<?=$class?>">
    <tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" class="<?=$class?>">
				<tr>
					<td><input name="sec[]" id="<?=$id_padre?>" padre="-1" type="checkbox" onClick="sel(this)" value="<?=$id_padre?>" class="checkbox" /><img src="../sys_images/map_folder.gif" border="0" /></td>
					<td>&nbsp;<label for="<?=$id_padre?>">Home</label></td>
				</tr>
			</table>
		</td>
    </tr>
<?
		}
		if( $regs->recordCount() > 0 ){
			$cant = 0;
			while( $reg = $regs->fetchNextObject(false) ){
				$cant++;
?>
    <tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" class="<?=$class?>">
				<tr>
					<td nowrap>
<?
				for( $i = 0 ; $i < $pos ; $i++ ) {
?>
						<img src="../sys_images/px.gif" width="19" border="0" />
<?
				}
?>					    						
						<img src="../sys_images/px.gif" width="19" border="0" />
						<input type="checkbox" name="sec[]" id="<?=$reg->id?>" onClick="sel(this)" padre="<?=$reg->id_padre?>" value="<?=$reg->id?>" 
							class="checkbox" <? $rs = $db->getrow( "SELECT * FROM ".CFG_privilegesTable." WHERE id_grupo = ".$id." AND id_seccion = ".$reg->id );
								if( $rs ) echo 'checked'; ?> />
						<img src="../sys_images/map_folder.gif" border="0" />
					</td>
					<td>&nbsp;<label for="<?=$reg->id?>"><?=$reg->nombre?></label></td>
				</tr>
			</table>
		</td>
	</tr>
<?
				show_sections( $reg->id, $pos+1, $last, $class );
			}
		}
		if( $id_padre == 0 ){
?>
</table>
<?
		}
}
?>
<form name="frm_permisos" method="post" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="action" value="permisos">
<input type="hidden" name="id" value="<?=$id?>">
<?
	$secciones = $db->execute( "SELECT * 
		FROM ".CFG_sectionsTable." 
		WHERE (id_padre = 0 OR id_padre IS NULL) AND nombre <> '-separador-' 
		ORDER BY nombre" );
?>
<table align="center" width="640" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th class="titulosizquierda">Permisos para el grupo: <?=$nombre_grupo?></th>
	</tr>
<?
	if( $secciones->RecordCount() < 1 ) {
?>
	<tr>
		<td align="center" class="cuadro1izquierda">No hay nig&uacute;na secci&oacute;n disponible.</td>
	</tr>
<?
	} else {
?>
	<tr>
		<td class="cuadro1izquierda">
<?
			show_sections();
?>
		</td>
	</tr>
<?
	}
?>
	<tr>
		<td align="center" valign="top" class="cuadro2izquierda"><input type="submit" value="Aceptar">&nbsp;&nbsp;
			<input name="button" type="button" onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
	</tr>
	<tr>
		<td valign="top" class="sombra">&nbsp;</td>
	</tr>
</table>
</form>
<?
}
if( $action == 'frmAcciones' ){
?>
<form name="frm_permisos" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" value="acciones">
	<input type="hidden" name="id" value="<?=$id?>">
	<?
	$acciones = $db->execute( "SELECT * 
		FROM ".CFG_actionsTable." 
		WHERE 1
		ORDER BY accion" );
?>
	<table align="center" width="640" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<th colspan="2" class="titulosizquierda">Acciones para el grupo: 
			<?=$nombre_grupo?></th>
		</tr>
		<?
	if( $acciones->RecordCount() < 1 ) {
?>
		<tr>
			<td colspan="2" align="center" class="cuadro1izquierda">No hay nig&uacute;na acci&oacute;n disponible.</td>
		</tr>
		<?
	} else {
		while( $reg = $acciones->fetchNextObject(false) ){
			$selected = $db->getOne( "SELECT valor 
				FROM ".CFG_groupsPrivilegesTable." 
				WHERE id_accion = '".$reg->id."'
					AND id_grupo = '$id'" );
?>
		<tr>
			<td width="150" class="cuadro1izquierda"><strong><?=$reg->accion?></strong></td>
		    <td width="490" class="cuadro1"><?
				$tmp = split( ',', $reg->valores );
			?><select name="valor[<?=$reg->id?>]">
				<option value=""> </option><?
				foreach( $tmp as $valor ){
					?><option <?=$valor==$selected?'selected':''?>><?=$valor?></option><?
				}
				?></select></td>
		</tr>
		<tr>
			<td colspan="2" class="cuadro1izquierda"><em><?=printif( $reg->descripcion )?></em></td>
		</tr>
		<?
		}
	}
?>
		<tr>
			<td align="center" valign="top" class="cuadro2izquierda">&nbsp;</td>
			<td valign="top" class="cuadro2"><input type="submit" value="Aceptar">&nbsp;<input 
			type="button" onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
		</tr>
		<tr>
			<td colspan="2" valign="top" class="sombra">&nbsp;</td>
		</tr>
	</table>
</form>
<?
}
?>
</body>
</html>