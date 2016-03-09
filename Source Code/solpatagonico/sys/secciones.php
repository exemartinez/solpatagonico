<?
	define ('CFG_section_id','82367c604b45efd429fbafa2bdebd1ed');
	include_once('../inc/conf.inc.php');

	registrar( "action,id,sin_img,id_padre,id,posicion,nombre,vinculo" );
	$id = intval( $id );
	$id_padre = intval( $id_padre );
	$sin_img = intval( $sin_img );
	$tabla		= CFG_sectionsTable;
	$where		= "id = $id";
	$msgDelete	= "¿Seguro desea eliminar este registro? Se eliminaran todas las secciones que dependan de esta.";
	$msgUnique	= "Ya existe una seccion con el mismo nombre en este mismo nivel.";

	if( in( $action, 'Alta', 'Modificar' ) ) {
		$msg_error = '';
		if( !validate::Text($nombre) ) $msg_error .= '<li>Complete el nombre de sección.</li>';
		if( $db->getRow( "SELECT * 
			FROM $tabla 
			WHERE nombre = '$nombre' AND id_padre = $id_padre".(
			 $id != '' ? " AND id != $id" : '' ) ) ) $msg_error .= '<li>'.$msgUnique.'</li>';
		if( $msg_error != '' ){
			$action = 'frm'.$action;
		}
	}

	if( $action == 'Alta' ){
		$record = gen_record("nombre,vinculo,posicion");
		# IMG
		if( is_uploaded_file( $_FILES['img']['tmp_name'] ) ){
			$record["img"] = addslashes( getfile( $_FILES['img']['tmp_name'] ) );
			$record["img_mime"] = $_FILES['img']['type'];
			$record["img_name"] = $_FILES['img']['name'];
		}
		if( $sin_img ){
			$record["img"] = NULL;
			$record["img_mime"] = '';
			$record["img_name"] = '';
		}
		# POSICIONAMIENTO		
		$max_pos = $db->getone( "SELECT COUNT(*) FROM ".CFG_sectionsTable." WHERE id_padre = '$id_padre'" );
		$max_pos++;
		if( $max_pos > $posicion ) {
			$ok = $db->execute("UPDATE ".CFG_sectionsTable." SET posicion = posicion + 1
				WHERE (id_padre = '$id_padre') AND posicion BETWEEN $posicion AND $max_pos" );
		}
		
		# INSERT
		$record['id_padre'] = $id_padre;
		$record['owner_id'] = $USER->id;
		$record['owner_ip'] = $_SERVER['REMOTE_ADDR'];
		$record['owner_date'] = date( "Y-m-d H:i:s" );
		$ok = $db->Execute( get_sql( 'INSERT', $tabla, $record, '' ) );		
		if( !$ok ){
			$action = 'frm'.$action;
			$error .= "<li>".( strstr( strtolower($db->ErrorMsg()), "unique") ? $msgUnique : $db->ErrorMsg()  )."</li>";
		} else {
			$action = '';
		}
	}
	if( $action == 'Modificar' ){
		$record = gen_record( 'nombre,vinculo,id_padre,posicion' );
		# IMG
		if( is_uploaded_file( $_FILES['img']['tmp_name'] ) ){
			$record["img"] = addslashes( getfile( $_FILES['img']['tmp_name'] ) );
			$record["img_mime"] = $_FILES['img']['type'];
			$record["img_name"] = $_FILES['img']['name'];
		}
		if( $sin_img ){
			$record["img"] = NULL;
			$record["img_mime"] = '';
			$record["img_name"] = '';
		}
		# POSICIONAMIENTO
	    $campo = 'id_padre';
		$old_pos = $db->getone("SELECT posicion FROM $tabla WHERE id = $id");
		$old_pos = intval($old_pos);
		$old_padre = $db->getone("SELECT id_padre FROM $tabla WHERE id = $id");
		$old_padre = intval($old_padre);
	    if( $old_pos != $posicion && $old_padre == $id_padre ){
			if( $old_pos > $posicion ) {
				$ok = $db->execute("UPDATE $tabla SET posicion = posicion + 1 
					WHERE ($campo = $id_padre) 
					AND POSICION BETWEEN $posicion AND $old_pos");
			} else {
				$ok = $db->execute("UPDATE $tabla SET posicion = posicion - 1 
					WHERE ($campo = $id_padre) 
					AND posicion BETWEEN $old_pos AND $posicion");
			}
			$ok  = $db->execute("UPDATE $tabla SET posicion = $posicion WHERE id = $id");
	    } elseif( $old_padre != $id_padre ){
			#Actualiza las posiciones en el viejo nivel
			$ok = $db->execute("UPDATE $tabla SET POSICION = POSICION - 1
				WHERE (id_padre = $old_padre) AND posicion > $old_pos");
			$max_pos = $db->getone("SELECT MAX(posicion) FROM $tabla WHERE $campo = ".${$campo} );
			if( $posicion <= $max_pos ){
				#Actualiza las posiciones en el nuevo nivel
				$ok = $db->execute("UPDATE $tabla SET posicion = posicion + 1
					WHERE ($campo = ".${$campo}.") AND posicion >= $posicion");
			}
	    }
		# UPDATE
		$record['last_user_id'] = $USER->id;
		$record['last_user_ip'] = $_SERVER['REMOTE_ADDR'];
		$record['last_user_date'] = date( "Y-m-d H:i:s" );		
		$ok = $db->execute( get_sql( 'UPDATE', $tabla, $record, "id = $id" ) );
		if( $ok ){
			$action = '';
		} else {
			$action = 'frm'.$action;
			$error .= "<li>Error al actualizar el registro.</li>";
		}
	}
	if( $action == 'Baja' ){
		$rs = $db->execute("SELECT * FROM $tabla WHERE id = $id");
		if( !$rs->EOF ){
			$reg = $rs->fetchobject(false);
		}
		$ok = $db->execute("DELETE FROM $tabla WHERE id = $id");
		$ok = $db->execute("UPDATE $tabla SET posicion = posicion - 1 
			WHERE (id_padre = '".$reg->id_padre."') AND posicion > '".$reg->posicion."'");
		$action = '';
	}
	if( $action == 'actPosicion' ){
	    $campo = 'id_padre';
		$old_pos = $db->getone("SELECT posicion FROM ".CFG_sectionsTable." WHERE id = $id");
	    if( $old_pos != $posicion ) {
			if( $old_pos > $posicion ) {
				$ok = $db->execute("UPDATE $tabla SET posicion = posicion + 1 
					WHERE ($campo = $id_padre) AND posicion BETWEEN $posicion AND $old_pos");
			} else {
				$ok = $db->execute("UPDATE $tabla SET posicion = posicion - 1 
					WHERE ($campo = $id_padre) AND posicion BETWEEN $old_pos AND $posicion");
			}
			$ok = $db->execute("UPDATE $tabla SET posicion = $posicion WHERE id = $id");
	    }
	    $action = $prev_action;
	}
	if( $action == 'bajar_secciones' ){
		?><input type="hidden" name="id_padre" id="id_padre" 
			value="<?=$action == 'frmAlta' ? '0' : printif( $reg->id_seccion, $id_padre )?>" /><?php
			echo navigation_route( $id_padre, '', true, 'text', 'id_padre', $tabla );
			$seccion = $db->execute( "SELECT * FROM $tabla WHERE id = '$id_padre'" );
			$seccion = $seccion->fetchObject(false);
			$rs = $db->execute( "SELECT nombre,id FROM $tabla WHERE id_padre = '$id_padre' ORDER BY posicion,nombre" );
			echo $rs->GetMenu2(
				'id_padre',
				$id_padre,
				printif( $seccion->id, '0' ).':- '.( printif( $seccion->nombre, 'Home' ) ).' -',
				false,
				1,
				'style="width:95%" onChange="bajar_secciones(this.value)"'
			);
		exit();
	}
	if( $action == 'bajar_posiciones' ){
		$cant = $db->execute("SELECT * FROM $tabla WHERE id_padre = '$id_padre'");
		if( !$cant->EOF ){
			$cant = $cant->recordcount();
		} else {
			$cant = 0;
		}
		$cant++;
		$posicion = $cant;
		?><select name="posicion" id="posicion" style="width:95% "><?
			for( $i = 1 ; $i <= $cant ; $i++ ) {
				?><option value="<?=$i?>" <?=$i == $posicion ? 'selected' : ''?> ><?=$i?></option><?
			}
		?></select><?
		exit();
	}
?>
<html>
<head>
	<title>Secciones</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css">
	<script src="<?=CFG_jsPath?>download.php"></script>
	<script language="javascript" type="text/javascript">
	<!--
	function act_posicion( id, posic, seccion, sector ) {
		document.frmPosic.posicion.value = posic;
		document.frmPosic.id.value = id;
		document.frmPosic.id_padre.value = seccion;
		document.frmPosic.submit();
	}
	function bajar_secciones( id_padre ) {
	    var td = document.getElementById('td_secciones');
		td.innerHTML = 'Buscando...';
		if( id_padre ){
			download(
				'sys/secciones.php?action=bajar_secciones&id_padre='+id_padre,
				function ( s ){
					document.getElementById('td_secciones').innerHTML = s;
					download(
						'sys/secciones.php?action=bajar_posiciones&id_padre='+id_padre,
						function (s){
							document.getElementById('td_posiciones').innerHTML = s;
						}
					);
				}
			);
		}
	}
	-->
	</script>
</head>

<body id="body">
<form name="frmPosic" action="<?=$_SERVER['PHP_SELF']?>"  method="post">
    <input type="hidden" name="action" id="action" value="actPosicion" />
    <input type="hidden" name="posicion" id="posicion" value="" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="id_padre" id="id_padre" value="" />
    <input type="hidden" name="prev_action" id="prev_action" value="<?=$action?>" />
</form>
<?
	Form::validate ();
	if( $action == '' ){
   		if( !defined( 'CFG_sectionsTable' ) ) die( "La variable de configuracion CFG_sectionsTable no esta disponible!" );
		$pager = new pager(
			"SELECT * 
				FROM ".CFG_sectionsTable."
				WHERE (id_padre = $id_padre) AND nombre <> '-separador-'
				ORDER BY posicion, nombre",
			$_REQUEST['cur_page'],
			20,
			25,
			$_REQUEST,
			''
		);
		if( $id_padre != 0 ) {
			$padre = $db->getone( "SELECT nombre FROM $tabla WHERE id = $id_padre");
		}
?>
<br>
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
	function cerrar_item(n_item){
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
<table width="640" border="0" align="center" cellpadding="3" cellspacing="0">
	<tr> 
		<th class="titulosizquierda" align="center" colspan="4"><?=$id_padre != 0 ? 'Sub secciones de '.$padre : 'Secciones' ?></th>
	</tr>
	<tr>
		<td class="cuadro1izquierda" colspan="4">
			<table border="0" cellpadding="3" cellspacing="0" width="100%" class="text">
				<tr>
					<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAlta&id_padre=<?=$id_padre?>"><img 
			src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAlta&id_padre=<?=$id_padre?>">Agregar Secci&oacute;n</a></td>
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
	<tr>
		<td class="text" colspan="4">[ <?=navigation_route( $id_padre, $_SERVER['PHP_SELF'], false, '', 'id_padre', $tabla );?> ]</td>
	</tr>
	<tr align="center">
		<td width="50" class="titulosizquierda">Posicion</td>
		<td width="180" class="titulos">Nodo</td>
		<td width="320" class="titulos">Lista de Items</td>
		<td class="titulos" width="90">&nbsp;</td>
	</tr>
<?
		if( $pager->num_rows() < 1 ){
?>
	<tr>
		<td colspan="4" align="center" class="cuadro1izquierda"><span class="error">No hay secciones en este nivel</span></td>
	</tr>
			<?
		} else {
			while( $seccion = $pager->fetch_object() ){
				$n++;
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
				$max_len = 150;
				$sub_secciones = $db->getone("SELECT COUNT(*) FROM $tabla WHERE id_padre = $id_padre" );
				?>
	<tr>
		<td class="<?=$class?>izquierda"><select name="posicion" id="posicion" 
			style=" width: 85% " onChange="act_posicion( '<?=$seccion->id?>', this.value, '<?=$seccion->id_padre?>', 'sec' )">
<?
				for( $i = 1 ; $i <= $pager->num_rows() ; $i++ ) {
?>
				<option value="<?=$i?>" <?=$i==$seccion->posicion?'selected':''?> ><?=$i?></option>
<?
				}
?>
			</select></td>
		<td class="<?= $class?>" valign="top">
<?
				if( $seccion->img != '' ) {
?>
		<img src="../inc/file.php?section=<?=CFG_sectionsTable?>&name=img&id=<?=$seccion->id?>" border="0" />&nbsp;
<?
				}
?>
		<?=printif($seccion->nombre)?></td>
		<td class="<?= $class?>" valign="top"><?
				$subsecciones = $db->execute(
					"SELECT * FROM ".CFG_sectionsTable."
					WHERE id_padre = $seccion->id
					ORDER BY posicion, nombre"
				);
							
				$lista_items='';
				if( $subsecciones && $subsecciones->recordcount() > 0 ){
					$lista_items.="Este nodo posee los siguientes items:<br><br>";
					while( $subseccion = $subsecciones->fetchNextObject(false) ){
						$lista_items.="$subseccion->nombre<br>";
					}
				}
							
				if( $lista_items == '' ){
?>
				Este nodo no posee items
<?
				} else {
?>
			<table width="100%" cellpadding="0" border="0" cellspacing="0">
					<tr id="boton_<?=$n?>">
						<td class="<?= $class == 'cuadro1' ? 'cuadro2' : 'cuadro1' ?>izquierda" onClick="abrir_item(<?=$n?>)" 
							style="cursor:pointer">Ver Items ( <?=$subsecciones->recordcount()?> )</td>
					</tr>
					<tr id="lista_<?=$n?>" style="display: none">
						<td class="<?= $class == 'cuadro1' ? 'cuadro2' : 'cuadro1' ?>izquierda"><?=$lista_items?></td>
					</tr>
			</table>
<?
				}
?>
	  </td>
		<td class="<?=$class?>" align="center"><a 
			href="<?=$_SERVER['PHP_SELF']?>?id_padre=<?=$seccion->id?>" title="<?=intval($subsecciones->recordcount())?> sub seccion<?=intval($subsecciones->recordcount())!=1?'es':''?>"><img 
			src="../sys_images/items.gif" alt="Ver SubSecciones" border="0" align="<?=intval($subsecciones->recordcount())?> sub seccion<?=intval($subsecciones->recordcount())!=1?'es':''?>" /></a>&nbsp;<a 
			href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$seccion->id?>&id_padre=<?=$id_padre?>"><img 
			src="../sys_images/list.gif" alt="Modificar Registro" border="0"/></a>&nbsp;<a 
			href="<?=$_SERVER['PHP_SELF']?>?action=frmBaja&id=<?=$seccion->id?>&id_padre=<?=$id_padre?>"><img 
			src="../sys_images/delete.gif" alt="Eliminar Registro" border="0"/></a>
		</td>

	</tr>
<?
			}
		}
?>
	<tr>
		<td class="sombra" colspan="4">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="text">
				<tr>
					<td>&nbsp;Registros: <?=$pager->get_first_pos()?> al <?=$pager->get_last_pos()?> de <?=$pager->get_total_records()?></td>
					<td align="right">
<?
		if( $pager->get_total_pages() > 0 ) {
?>
						P&aacute;g<?=$pager->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$pager->get_navigator();?>&nbsp;
<?
		}
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?
	}

	if( in ($action, 'frmAlta', 'frmModificar') ){
		if( $action == 'frmModificar' ){
			$rs = $db->execute("SELECT * FROM ".CFG_sectionsTable." WHERE id = $id");
			if( !$rs->EOF ){
				$seccion = $rs->fetchobject(false);
			}
		}
		Form::validate();
?>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onSubmit="return validate(this)">
<input type="hidden" name="action" value="<?=($action == 'frmAlta' ? 'Alta' : 'Modificar')?>" />
<input type="hidden" name="id" value="<?=$id?>" />
<input type="hidden" name="id_padre" value="<?=$id_padre?>" />
<table width="640" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr align="center">
		<th colspan="2" class="titulosizquierda"><?=$action=='frmAlta'?'Agregar':'Modificar'?> <?=intval($id_padre)!=0?'sub':''?>secci&oacute;n</th>
	</tr>
<?	
		if( $msg_error != '' ) {
?>
		<tr>
			<td class="cuadro1izquierda" colspan="2"><span class="error"><?=$msg_error?></span></td>
		</tr>
<?
		}
?>
	<tr>
		<td width="150" class="cuadro1izquierda">Nombre:</td>
		<td class="cuadro1"><input type="text" name="nombre" id="nombre" value="<?=$seccion->nombre?>" style="width:95% " validar="str:Debe ingresar un nombre de Seccion." /></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">V&iacute;nculo:</td>
		<td class="cuadro1"><input type="text" name="vinculo" id="vinculo" style="width:95% "
			value="<?=$seccion->vinculo?>" /></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Padre:</td>
		<td class="cuadro1" id="td_secciones">
<?
		if( $action == 'frmModificar' ) {
			$id_padre = intval($seccion->id_padre);
		}
		$rs = $db->execute("SELECT * FROM $tabla WHERE id = '$id_padre'");
		if( !$rs->EOF ){
			$padre = $rs->fetchobject(false);
		}
		echo navigation_route( $id_padre, '', true, 'text', 'id_padre', $tabla );
		$rs = $db->execute( "SELECT nombre,id FROM $tabla WHERE id_padre = '$id_padre' ORDER BY posicion,nombre" );
		echo $rs->GetMenu2(
			'id_padre',
			$id_padre,
			printif( $padre->id, '0' ).':- '.( printif( $padre->nombre, 'Home' ) ).' -',
			false,
			1,
			'style="width:95%" onChange="bajar_secciones(this.value)"'
		);
?>
		</td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Posicion:
<?
			$rs = $db->execute(
				"SELECT * 
				FROM ".CFG_sectionsTable."
				WHERE id_padre = '$id_padre'");
			if( !$rs->EOF ){
				$cant = $rs->RecordCount();
			} else {
				$cant = 0;
			}
			if( $action == 'frmAlta' ) {
				$cant++;
				$posicion = $cant;
			} else {
				$posicion = $seccion->posicion;
			}
?>
		</td>
		<td id="td_posiciones" class="cuadro1"><select name="posicion" id="posicion" style="width:95% ">
<?
			for( $i = 1 ; $i <= $cant ; $i++ ) {
?>
			<option value="<?=$i?>" <?=$i == $posicion ? 'selected' : ''?> ><?=$i?></option><?
			}
?>
		</select></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Descripci&oacute;n:</td>
		<td class="cuadro1"><textarea name="descripcion" id="descripcion" rows="5" 
			style="width: 95%"><?=printif($seccion->descripcion,$descripcion)?></textarea></td>
	</tr>
	<tr>
		<td class="cuadro1izquierda">Icono:</td>
		<td class="cuadro1"><input type="file" name="img" id="img" style="width:95% "></td>
	</tr>
<?
		if( $seccion->img_mime != "" ){
?>
    <tr>
		<td class="cuadro1izquierda">&nbsp;</td>
		<td class="cuadro1"><input type="checkbox" name="sin_img" id="sin_img" value="1" />&nbsp;<label 
			for="sin_img">Borrar icono</label><br><br><img 
			src="../inc/file.php?section=<?=CFG_sectionsTable?>&name=img&id=<?=$seccion->id?>" border="0" /></td>
    </tr>
<?
		}
		if ( $action == 'frmModificar' ){
			if ( $seccion->owner_id != 0 ){
?>
	<tr>
		<td class="cuadro1izquierda">Creación:</td>
		<td class="cuadro1"><?="[".fecha::iso2normal( substr( $seccion->owner_date, 0, 10 ) )." ".substr( $seccion->owner_date, 11, 8). "] [".$seccion->owner_ip."] ".$db->getone( "SELECT concat(nombre,' ','[',usuario,']') FROM ".CFG_usersTable." WHERE id = '".$seccion->owner_id."'" )?></td>
	</tr>
<?
			}
			if ( $seccion->last_user_id != 0 ){
?>
	<tr>
		<td class="cuadro1izquierda">Ultima modificación:</td>
		<td class="cuadro1"><?="[".fecha::iso2normal( substr( $seccion->last_user_date, 0, 10 ) )." ".substr( $seccion->last_user_date, 11, 8). "] [".$seccion->last_user_ip."] ".$db->getone( "SELECT concat(nombre,' ','[',usuario,']') FROM ".CFG_usersTable." WHERE id = '".$seccion->last_user_id."'" )?></td>
	</tr>
<?	
			}
		}
?>

	<tr>
		<td class="cuadro2izquierda">&nbsp;</td>
	  <td class="cuadro2"><input type="submit" value="Aceptar">&nbsp;<input type="button" value="Cancelar" name="Button" onClick="javascript:location = '<?=$_SERVER['PHP_SELF']?>?id_padre=<?=$id_padre?>'"></td>
	</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
<!--
	document.getElementById( 'nombre' ).focus();
-->
</script>
<?
	}

	if( $action=='frmBaja' ){
		$rs = $db->execute("SELECT * FROM ".CFG_sectionsTable." WHERE id = $id");
		if ( !$rs->EOF ){
			$seccion = $rs->fetchobject(false);
		}
?>
<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
<table width="640" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr align="center"> 
		<th class="titulosizquierda" colspan="2">Borrar Seccion</th>
	</tr>
	<tr> 
		<td class="cuadro1izquierda" width="150"> Nombre </td>
		<td class="cuadro1"> <?=$seccion->nombre?></td>
	</tr>
	<tr> 
		<td class="cuadro2izquierda">&nbsp;</td>
		<td class="cuadro2"><input type="hidden" name="id" value="<?=$id?>">
			<input type="hidden" name="action" value="Baja">
			<input type="submit" value="Borrar">
			<input type="button" value="Cancelar" onClick="javascript:location = '<?=$_SERVER['PHP_SELF']?>'"></td>
	</tr>
</table>
</form>
<?
	}
?>
</body>
</html>