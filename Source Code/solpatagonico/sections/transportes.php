<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Transportes';
	$name		= 'Transporte';
	$titleForm	= 'Transportes';
	$msgDelete	= '¿Esta seguro que desea eliminar este Transporte?';
	# Variables de programación
	$tabla		= "transporte";
	$campos		= "patente,id_tipo,nombre,capacidad";
	registrar( $campos );
	$where		= "patente = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Integer($id_tipo) ) 
			$error .= "<li>Debe seleccione un Tipo.</li>";
		if ( !validate::Text($patente) || !preg_match( '/^[A-Z]{3}[0-9]{3}$/', $patente ) ) 
			$error .= "<li>Debe ingresar una Patente (Formato: AAA000).</li>";
		if ( $action == 'Agregar' && $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE patente='".$patente."' " ) ){
			$error .= "<li>Patente existente, ingrese otra.</li>";
			unset( $patente );
		}
		if ( !validate::Text($nombre) ) 
			$error .= "<li>Debe ingresar un Nombre.</li>";
		if ( !validate::Integer($capacidad,'','','','',0) ) 
			$error .= "<li>Debe ingresar una Capacidad v&aacute;lida.</li>";
		if ( !validate::Text($nombre) ) 
			$error .= "<li>Debe ingresar un Nombre.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		if( is_uploaded_file( $_FILES['imagen']['tmp_name'] ) ){
			$record["imagen"] = addslashes( getfile( $_FILES['imagen']['tmp_name'] ) );
			$record["imagen_mime"] = $_FILES['imagen']['type'];
			$record["imagen_name"] = $_FILES['imagen']['type'];
		}
		$ok = $db->Execute( get_sql( 'INSERT', $tabla, $record, '' ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$error = '';
			$action = '';
		}		
	}
	
	if ( $action == 'Modificar' ){
		if( is_uploaded_file( $_FILES['imagen']['tmp_name'] ) ){
			$record["imagen"] = addslashes( getfile( $_FILES['imagen']['tmp_name'] ) );
			$record["imagen_mime"] = $_FILES['imagen']['type'];
			$record["imagen_name"] = $_FILES['imagen']['type'];
		}
		if( $delete_imagen ){
			$record["imagen"] = '';
			$record["imagen_mime"] = '';
			$record["imagen_name"] = '';
		}
		$ok = $db->Execute( get_sql( 'UPDATE', $tabla, $record, "patente = '$id'" ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$error = '';
			$action = '';
		}
	}
	
	if ( $action == 'Borrar' ){
		$ok = $db->execute( "DELETE FROM ".$tabla." WHERE patente = '".$id."' " );
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
</head>

<body>
<?
	if ( in($action,'','buscar') ) {
		$paginas = new pager(
			"SELECT t.*, p.nombre tipo 
			FROM ".$tabla." t 
			LEFT JOIN tipo_transporte p ON t.id_tipo = p.id 
			WHERE 1=1 AND 
				( t.nombre LIKE '".html_entity_decode( $nombre_list )."%' OR t.patente LIKE '".html_entity_decode( $nombre_list )."%' )".(
				$tipo_lst ? " AND t.id_tipo='".$tipo_lst."'" : "" 
			)."
			ORDER BY t.nombre" ,
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
				<th class="titulosizquierda" colspan="4" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td class="cuadro1izquierda" colspan="4">
					<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
						<tr>
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">Nombre/Patente:</td>
                                		<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" style="width:100% " /></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
                               		</tr>
                                	<tr>
                                		<td class="text">Tipo</td>
                                		<td><?
									$rs = $db->Execute( "
										SELECT nombre, id 
										FROM tipo_transporte
										ORDER BY nombre
									" );
									echo $rs->GetMenu2(
										'tipo_lst',
										$tipo_lst,
										": -- Seleccione un tipo de transporte -- ",
										false,
										1,
										'id="tipo_lst" style="width: 100%"'
									);
										?></td>
                                		<td>&nbsp;</td>
                               		</tr>
                                	</table></td>
						</tr>
					</table>
				</td>
			</tr>	
			<tr>
				<td class="sombra" colspan="4">
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
				<td width="150" class="titulosizquierda">Tipo</td>
				<td width="60" class="titulos">Capacidad</td>
				<td width="370" class="titulos">Nombre / Patente </td>
				<td width="60" class="titulos">&nbsp;</td>
			</tr>
<?
		if( $paginas->num_rows () < 1) {
?>
			<tr> 
				<td colspan="4" align="center" class="cuadro1izquierda"><span class="error">No se encuentran registros coincidentes</span></td>
			</tr>
<?
		} else {
			while( $reg = $paginas->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
			<tr>
				<td align="left" class="<?=$class?>izquierda"><?=$reg->tipo?></td>
				<td class="<?=$class?>"><?=$reg->capacidad?></td>
				<td class="<?=$class?>"><?=$reg->nombre?> / <?=$reg->patente?></td>
				<td align="center" class="<?=$class?>"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->patente?>"><img src="../sys_images/list.gif" border="0" /></a>&nbsp;<a 
					href="javascript: if (confirm('<?=( $reg->borrado ? $msgUnDelete : $msgDelete )?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->patente?><?=( $reg->borrado ? '&br=0' : '&br=1')?>'}"><img src="../sys_images/delete.gif" border="0" /></a>				</td>
			</tr>
<?
			}
		}
?>
		<tr>
		    <td class="sombra" colspan="4">
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
			$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE patente = '$id'", 1 );
			$reg = $rs->FetchObject(false);
		}
		
		Form::validate ();
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
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
			<td width="150" class="cuadro1izquierda">*Tipo</td>
		    <td width="490" class="cuadro1"><?
		$rs = $db->Execute( "
			SELECT nombre, id 
			FROM tipo_transporte
			ORDER BY nombre
		" );
		echo $rs->GetMenu2(
			'id_tipo',
			printif( $id_tipo, $reg->id_tipo ),
			": -- Seleccione un tipo de transporte -- ",
			false,
			1,
			'id="id_tipo" style="width: 100%"'
		);
			?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">*Patente</td>
        	<td class="cuadro1"><input name="patente" type="text" value="<?=prepare_var( printif( $patente, $reg->patente ) )?>" 
			style="width: 100%;" validate="str:Debe ingresar una Patente" maxlength="6" /></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">*Nombre</td>
        	<td class="cuadro1"><input name="nombre" type="text" value="<?=prepare_var( printif( $nombre, $reg->nombre ) )?>" style="width:100% " validate="str:Debe ingresar un Nombre" maxlength="60" /></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Capacidad</td>
        	<td class="cuadro1"><input name="capacidad" type="text" value="<?=prepare_var( printif( $capacidad, $reg->capacidad ) )?>" 
			style="width:100% "/></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Imagen</td>
        	<td class="cuadro1"><input name="imagen" type="file" style="width:100% "/></td>
		</tr><?		
		if( $reg->imagen_mime != '' ){
			?><tr>
			<td class="cuadro1izquierda">&nbsp;</td>
			<td class="cuadro1"><input type="checkbox" name="delete_imagen" id="delete_imagen" value="1"> Borrar Imagen<br>
			<img src="../inc/file.php?id=<?=$reg->patente?>&section=<?=$tabla?>&name=imagen&id_name=patente" border="0"></td>
			</tr><?
		}
		?><tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
				<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->patente?>" />
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
?>
</body>
</html>