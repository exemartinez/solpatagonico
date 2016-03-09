<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Tipos de transporte';
	$name		= 'Tipo de transporte';
	$titleForm	= 'Tipos de transporte';
	$msgDelete	= '¿Esta seguro que desea eliminar este Tipo de transporte?';
	# Variables de programación
	$tabla		= "tipo_transporte";
	$tabla_users= "sys_users_tipo_transporte";
	$campos		= "nombre";
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Text($nombre) ) 
			$error .= "<li>Debe ingresar un Tipo de transporte.</li>";
		if ( $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE nombre='".$nombre."' ".( $action == 'Modificar' ? " AND id!='".$id."'" : "" ) ) ){
			$error .= "<li>Tipo existente, ingrese otro.</li>";
			unset( $nombre );
		}
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		$ok = $db->Execute( get_sql( 'INSERT', $tabla, $record, '' ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$id_tipo = mysql_insert_id();
			foreach( explode( ',', $conductores ) as $id_usuario ){
				$ok = $db->Execute( "INSERT INTO ".$tabla_users." VALUES ( '', '".$id_usuario."', '".$id_tipo."' )" );
			}
			$error = '';
			$action = '';
		}		
	}
	
	if ( $action == 'Modificar' ){
		$ok = $db->Execute( get_sql( 'UPDATE', $tabla, $record, "id = $id" ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$ok = $db->execute( "DELETE FROM ".$tabla_users." WHERE id_tipo = '".$id."' " );
			foreach( explode( ',', $conductores ) as $id_usuario ){
				$ok = $db->Execute( "INSERT INTO ".$tabla_users." VALUES ( '', '".$id_usuario."', '".$id."' )" );
			}
			$error = '';
			$action = '';
		}
	}
	
	if ( $action == 'Borrar' ){
		(int) $id;
		(int) $id_tipo;
		if( $db->GetOne("SELECT COUNT(*) FROM transporte WHERE id_tipo = '".$id."' ") ){
			if( $tipo_asignacion == 'viejo' && $id_tipo ){
				$db->Execute("UPDATE transporte SET id_tipo = '".$id_tipo."' WHERE id_tipo = '".$id."' ");
			} elseif( $tipo_asignacion == 'nuevo' && $nombre != '' ){
				if ( $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE nombre='".$nombre."' " ) ){
					$action = 'frm'.$action;
					$msg_error = true;
				} else {
					$ok = $db->Execute( get_sql( 'INSERT', $tabla, array( 'nombre' => $nombre ), '' ) );
					$id_tipo = mysql_insert_id();
					$db->Execute("UPDATE transporte SET id_tipo = '".$id_tipo."' WHERE id_tipo = '".$id."' ");
				}
			} else {
				$action = 'frm'.$action;
			}
		}
		$ok = $db->execute( "DELETE FROM ".$tabla." WHERE id = '".$id."' " );
		$ok = $db->execute( "DELETE FROM ".$tabla_users." WHERE id_tipo = '".$id."' " );
		if( !$ok ){
			$error = '<li>Error al borrar el usuario.</li>';
		} else {
			$error = '';
		}
		$action = '';
	}
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
	<script>
		function mover_elemento( origen, destino ){
			var cont = 0;
			for( cont = 0; eval( 'cont <= origen.length-1' ); cont++ ){
				if( eval( 'origen.options[cont].selected' ) == true ){
					eval( 'destino.options[destino.length] = new Option( origen.options[cont].text, origen.options[cont].value );' );
					eval( 'origen.options[cont] = null;' );
					cont--;
				}
			}
		}
		
		function enviar(){
			var cont;
			if( eval( 'document.form1.o_conductores[0]' ) ){
				sep = '';
				for (cont2 = 0; eval('cont2<=document.form1.o_conductores.length-1') ; cont2++) {
					eval("document.form1.conductores.value+=sep+document.form1.o_conductores.options[cont2].value;");
					sep = ',';
				}
			} else {
				alert( 'Debe seleccionar al menos un Conductor.' );
				return false;
			}
			return validate( document.form1 );
		}	
	</script>
</head>

<body>
<?
	if ( in($action,'','buscar') ) {
		$paginas = new pager(
			"SELECT * 
			FROM ".$tabla." 
			WHERE 1=1 AND 
				nombre LIKE '".html_entity_decode( $nombre_list )."%' 
			ORDER BY nombre" ,
			$cur_page,
			20,
			25,
			"nombre_list",
			''
		);	
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="2" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td class="cuadro1izquierda" colspan="2">
					<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
						<tr>
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">Tipo:</td>
                                		<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" style="width:100% " /></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
                               		</tr>
                                	</table></td>
						</tr>
					</table>
				</td>
			</tr>	
			<tr>
				<td class="sombra" colspan="2">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class=text>
						<tr>
							<td class="text" align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
							<td align="right" class="text">
<?
		if( $paginas->get_total_pages() > 0 ) {
?>
								P&aacute;g<?=$paginas->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$paginas->get_navigator(); ?>&nbsp;
<?
		}
?>
							</td>
						</tr>		
					</table>
				</td>
			</tr>		
			<tr>
				<td width="580" class="titulosizquierda">Tipo</td>
				<td width="60" class="titulos">&nbsp;</td>
			</tr>
<?
		if( $paginas->num_rows () < 1) {
?>
			<tr> 
				<td colspan="2" align="center" class="cuadro1izquierda"><span class="error">No se encuentran registros coincidentes</span></td>
			</tr>
<?
		} else {
			while( $reg = $paginas->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
			<tr>
				<td align="left" class="<?=$class?>izquierda"><?=$reg->nombre?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img 
				src="../sys_images/list.gif" border="0" alt="Modificar" /></a></td>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmBorrar&id=<?=$reg->id?>"><img 
				src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a></td>
					</tr>
				</table></td>
			</tr>
<?
			}
		}
?>
		<tr>
		    <td class="sombra" colspan="2">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class=text>
					<tr>
						<td class="text" align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
						<td align="right" class="text">
<?
		if( $paginas->get_total_pages() > 0 ) {
?>
							P&aacute;g<?=$paginas->get_total_pages()>1?'s':''?>&nbsp;&nbsp;<?=$paginas->get_navigator(); ?>&nbsp;
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
	} // Fin de Accion

	if ( in($action,'frmAgregar','frmModificar') ) {
		if ( $action == 'frmModificar' ) {
			$id = intval($id);
			$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE id = $id", 1 );
			$reg = $rs->FetchObject(false);
		}
		
		Form::validate ();
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" enctype="multipart/form-data" onSubmit="return enviar()">
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" colspan="2" align="center" style="font-size: 14px; font-weight: bold"><?=$action=='frmAgregar'?'Agregar':'Modificar'?> <?=$name?></th>
		</tr>
<?
		if ( $error ) {
?>
		<tr height="30">
			<td class="cuadro1izquierda" colspan="2"><span class="error"><?=$error?></span></td>
		</tr>
<?
		}	
?>				
		<tr> 
			<td width="150" class="cuadro1izquierda">*Nombre</td>
		    <td width="490" class="cuadro1"><input name="nombre" type="text" value="<?=prepare_var( printif( $nombre, $reg->nombre ) )?>" 
			style="width:100% " validate="str:Debe ingresar un Tipo de transporte" maxlength="60" /></td>
		</tr>
		<tr>
        	<td valign="top" class="cuadro1izquierda">*Conductores</td>
        	<td class="cuadro1"><table width="100%"  border="0" cellspacing="2" cellpadding="0" class="text">
            	<tr>
            		<td width="50%"><strong>Disponibles</strong></td>
            		<td>&nbsp;</td>
            		<td width="50%"><strong>Otorgadas</strong></td>
            		</tr>
            	<tr>
            		<td><select name="d_conductores" id="d_conductores" size="5" multiple onDblClick="mover_elemento( this, document.form1.o_conductores )" style="width:100% "><?
		$db->debug = true;
		if( $action == 'frmModificar' ){
			$aux_conductores = "";
			$sep = '';
			$rs = $db->Execute( "
				SELECT * 
				FROM  ".$tabla_users." 
				WHERE 1=1 AND id_tipo='".$id."' 
			" );
			while( $cond = $rs->FetchNextObject(false) ){
				$aux_conductores .= $sep.$cond->id_usuario;
				$sep = ',';
			}
		}
		$rs = $db->Execute( "
			SELECT * 
			FROM sys_users
			WHERE 1=1 ".( $conductores != "" ? " AND id NOT IN (".$conductores.")" : ( $aux_conductores != "" ? " AND id NOT IN (".$aux_conductores.")" : "" ) )."
			ORDER BY nombre
		" );
		while ( $cond = $rs->FetchNextObject(false) ){
					?><option value="<?=$cond->id?>"><?=$cond->nombre?></option><?		
		}
					?></select></td>
            		<td><input type="button" value=">>" onClick="mover_elemento( document.form1.d_conductores, document.form1.o_conductores )"><br>
					<input type="button" value="<<" onClick="mover_elemento( document.form1.o_conductores, document.form1.d_conductores )"></td>
            		<td><select name="o_conductores" id="o_conductores" size="5" multiple onDblClick="mover_elemento( this, document.form1.d_conductores )" style="width:100% "><?
		if ( $action == 'frmModificar' && ( $conductores != "" || $aux_conductores != '' )  ){
			$rs = $db->Execute( "
				SELECT * 
				FROM sys_users 
				WHERE 1=1 ".( $conductores != "" ? " AND id IN (".$conductores.")" : ( $aux_conductores != "" ? " AND id IN (".$aux_conductores.")" : "" ) )."
				ORDER BY nombre
			" );			
			while ( $cond = $rs->FetchNextObject(false) ){
					?><option value="<?=$cond->id?>"><?=$cond->nombre?></option><?		
			}
		}
					?></select></td>
            	</tr>
            </table></td>
		</tr>				
		<tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
				<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->id?>" />
				<input type="hidden" name="conductores" id="conductores" value="<?=$conductores?>" />
			</td>
		    <td class="cuadro2"><input name="aceptar" type="submit"  value="Aceptar"/>
           	<input name="button" type="button"  onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
		</tr>
		<tr>
			<td colspan="2" class="sombra">&nbsp;</td>
		</tr>
	</table>
	</form>
	<br>
<?
	}
	
	if( $action == 'frmBorrar' ){
		(int) $id;
		if( $db->GetOne("SELECT COUNT(*) FROM transporte WHERE id_tipo = '".$id."'") ){
			$error = "Este tipo de transporte tiene transportes asignados, por favor seleccione un nuevo tipo de transporte para reasignar los transportes.";
		}
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1">
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" colspan="2" align="center" style="font-size: 14px; font-weight: bold">¿Desea borrar el tipo de transporte "<?=$db->GetOne("SELECT nombre FROM ".$tabla." WHERE id = '".$id."'")?>"?</th>
		</tr>
<?
		if ( $error ) {
?>
		<tr height="30">
			<td class="cuadro1izquierda" colspan="2"><span class="error"><?=$error?></span></td>
		</tr>
		<tr>
			<td class="titulosizquierda" colspan="2" align="center">* Tipos de Transporte</td>
		</tr>
		<tr> 
			<td width="150" class="cuadro1izquierda">Existente</td>
		    <td width="490" class="cuadro1"><input type="radio" name="tipo_asignacion" value="viejo" <?=$tipo_asignacion!='nuevo'?' checked="checked"':''?> />&nbsp;<?
		$rs = $db->Execute( "
			SELECT nombre, id 
			FROM ".$tabla." 
			WHERE id != '".$id."'
			ORDER BY nombre
		" );
		echo $rs->GetMenu2(
			'id_tipo',
			printif ( $id_tipo, $reg->id_tipo ),
			": -- Seleccione un Tipo de Transporte -- ",
			false,
			1,
			'id="id_tipo" style="width: 80%"'
		);
			?></td>
		</tr>
		<tr>
			<td class="cuadro1izquierda">Nuevo</td>
			<td class="cuadro1"><input type="radio" name="tipo_asignacion" value="nuevo" <?=$tipo_asignacion=='nuevo'?' checked="checked"':''?> />&nbsp;<input type="text" name="nombre" value="<?=prepare_var( $nombre )?>" maxlength="20" /></td>
		</tr>			
<?
		}	
?>				
		<tr> 
			<td class="cuadro2izquierda" colspan="2" align="center"><input type="hidden" name="action" id="action" 
			style="width:95% " value="Borrar" /><input type="hidden" name="id" id="id" style="width:95% " 
			value="<?=$id?>" /><input name="aceptar" type="submit"  value="Aceptar"/>&nbsp;<input 
			name="button" type="button"  onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
		</tr>
		<tr>
			<td colspan="2" class="sombra">&nbsp;</td>
		</tr>
	</table>
	</form>
	<br>
<?
	}
?>
</body>
</html>