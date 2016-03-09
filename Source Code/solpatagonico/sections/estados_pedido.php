<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Estados de pedido';
	$name		= 'Estado de pedido';
	$titleForm	= 'Estados de pedido';
	$msgDelete	= '¿Esta seguro que desea eliminar este Estado de pedido?';
	# Variables de programación
	$tabla		= "estado_pedido";
	$campos		= "nombre";
	registrar( $campos );
	$id = intval( $id );
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Text($nombre) ) 
			$error .= "<li>Debe ingresar un Estado de pedido.</li>";
		if ( $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE nombre='".$nombre."' ".( $action == 'Modificar' ? " AND id!='".$id."'" : '' ) ) ){
			$error .= "<li>Estado existente, ingrese otro.</li>";
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
			$error = '';
			$action = '';
		}
	}
	
	if ( $action == 'Borrar' ){
		$ok = $db->execute( "DELETE FROM ".$tabla." WHERE id = '".$id."' " );
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
			"SELECT * 
			FROM ".$tabla." 
			WHERE 1=1 AND 
				nombre LIKE '".html_entity_decode( $nombre_list )."%' 
			ORDER BY id" ,
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
                                		<td width="120" class="text">Estado:</td>
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
				<td width="580" class="titulosizquierda">Estado</td>
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
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img src="../sys_images/list.gif" border="0" alt="Modificar" /></a></td>
						<td width="20" align="center"><a href="javascript: if (confirm('<?=( $reg->borrado ? $msgUnDelete : $msgDelete )?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?><?=( $reg->borrado ? '&br=0' : '&br=1')?>'}"><img src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a></td>
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
			<td width="150" class="cuadro1izquierda">*Nombre</td>
		    <td width="490" class="cuadro1"><input name="nombre" type="text" value="<?=prepare_var( printif( $nombre, $reg->nombre ) )?>" style="width:100% " validate="str:Debe ingresar un Estado de pedido" maxlength="20" /></td>
		</tr>				
		<tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
				<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->id?>" />
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