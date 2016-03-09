<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Cuentas';
	$name		= 'Cuenta';
	$titleForm	= 'Cuentas';
	$msgDelete	= '¿Esta seguro que desea eliminar esta Cuenta?';
	# Variables de programación
	$tabla		= "cuenta";
	$campos		= "cbu,id_tipo,id_proveedor,banco,sucursal";
	registrar( $campos );
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Integer($cbu) || strlen( $cbu ) != 22 ) 
			$error .= "<li>Debe ingresar un CBU v&aacute;lido (Solo n&uacute;meros, 22 digitos).</li>";
		if ( !validate::Integer($id_tipo) ) 
			$error .= "<li>Debe seleccionar un Tipo de cuenta.</li>";
		if ( !validate::Integer($id_proveedor) ) 
			$error .= "<li>Debe seleccionar un Proveedor.</li>";
		if ( $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE nombre='".$nombre."' ".( $action == 'Modificar' ? " AND id!='".$id."'" : ''  ) ) ){
			$error .= "<li>Cuenta existente, ingrese otra.</li>";
			unset( $nombre );
		}
		if ( $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE cbu='".$cbu."' ".( $action == 'Modificar' ? " AND id!='".$id."'" : ''  ) ) ){
			$error .= "<li>CBU existente, ingrese otro.</li>";
			unset( $cbu );
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
		$ok = $db->Execute( get_sql( 'UPDATE', $tabla, $record, "id = '$id'" ) );
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
			"SELECT c.*, CONCAT( p.cuit,' ', p.razonsocial ) proveedor 
			FROM ".$tabla." c 
			LEFT JOIN proveedor p ON c.id_proveedor = p.id 
			WHERE 1=1 AND ( p.fecha_baja is NULL OR p.fecha_baja = '0000-00-00 00:00:00' ) 
				AND c.cbu LIKE '".html_entity_decode( $nombre_list )."%'".(
				$proveedor_lst ? " AND c.id_proveedor='".$proveedor_lst."'" : ""
			)." 
			ORDER BY c.cbu" ,
			$cur_page,
			20,
			25,
			"nombre_list,proveedor_lst",
			''
		);	
?>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th class="titulosizquierda" colspan="3" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td class="cuadro1izquierda" colspan="3">
					<table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
						<tr>
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&id_proveedor=<?=$proveedor_lst?>"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&id_proveedor=<?=$proveedor_lst?>">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">Cuenta:</td>
                                		<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" style="width:100% " /></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
                               		</tr>
                                	<tr>
                                		<td class="text">Proveedor:</td>
                                		<td><?
										$rs = $db->Execute( "
											SELECT CONCAT( cuit, ' - ', razonsocial ) nombre, id 
											FROM proveedor 
											WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' 
											ORDER BY razonsocial
										" );
										echo $rs->GetMenu2(
											'proveedor_lst',
											$proveedor_lst,
											": -- Seleccione un proveedor -- ",
											false,
											1,
											'id="proveedor_lst" style="width: 100%"'
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
				<td class="sombra" colspan="3">
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
				<td width="290" class="titulosizquierda">Cuenta</td>
				<td width="290" class="titulos">Proveedor</td>
				<td width="60" class="titulos">&nbsp;</td>
			</tr>
<?
		if( $paginas->num_rows () < 1) {
?>
			<tr> 
				<td colspan="3" align="center" class="cuadro1izquierda"><span class="error">No se encuentran registros coincidentes</span></td>
			</tr>
<?
		} else {
			while( $reg = $paginas->fetch_object() ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
?>
			<tr>
				<td align="left" class="<?=$class?>izquierda"><?=$reg->cbu?> <?=( $reg->banco ? ' > '.$reg->banco : '' )?> <?=( $reg->sucursal ? ' > '.$reg->sucursal : '' )?></td>
				<td class="<?=$class?>"><?=$reg->proveedor?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img src="../sys_images/list.gif" border="0" alt="Modificar" /></a></td>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmVer&id=<?=$reg->id?>"><img src="../sys_images/search.gif" border="0" alt="Ver" /></a></td>
						<td width="20" align="center"><a 
					href="javascript: if (confirm('<?=( $reg->borrado ? $msgUnDelete : $msgDelete )?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?><?=( $reg->borrado ? '&br=0' : '&br=1')?>'}"><img src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a></td>
					</tr>
				</table></td>
			</tr>
<?
			}
		}
?>
		<tr>
		    <td class="sombra" colspan="3">
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

	if ( in($action,'frmAgregar','frmModificar','frmVer') ) {
		if ( in($action,'frmModificar','frmVer') ) {
			$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE id = '".$id."'", 1 );
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
		</tr><?
		}
		?>		
		<tr>
        	<td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Proveedor</td>
        	<td class="cuadro1"><?
		if( $action == 'frmVer' ){
			echo printif( $db->GetOne( "SELECT CONCAT( cuit, ' - ', razonsocial ) FROM proveedor WHERE id='".$reg->id_proveedor."'" ) );
		} else {
			$rs = $db->Execute( "
				SELECT CONCAT( cuit, ' - ', razonsocial ) nombre, id 
				FROM proveedor 
				WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' 
				ORDER BY razonsocial
			" );
			echo $rs->GetMenu2(
				'id_proveedor',
				printif( $id_proveedor, $reg->id_proveedor ),
				": -- Seleccione un proveedor -- ",
				false,
				1,
				'id="id_proveedor" style="width: 100%"'
			);
		}
			?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Tipo</td>
        	<td class="cuadro1"><?
		if( $action == 'frmVer' ){
			echo printif( $db->GetOne( "SELECT nombre FROM tipo_cuenta WHERE id='".$reg->id_tipo."'" ) );
		} else {
			$rs = $db->Execute( "
				SELECT nombre, id 
				FROM tipo_cuenta 
				ORDER BY nombre
			" );
			echo $rs->GetMenu2(
				'id_tipo',
				printif( $id_tipo, $reg->id_tipo ),
				": -- Seleccione un tipo de cuenta -- ",
				false,
				1,
				'id="id_tipo" style="width: 100%"'
			);
		}
			?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Banco</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->banco );
		} else {
?>
			<input name="banco" type="text" value="<?=prepare_var( printif( $banco, $reg->banco ) )?>" style="width:100% " maxlength="200" />
<?
		}
?>
			</td>
		</tr>
		<tr> 
			<td width="150" class="cuadro1izquierda">Sucursal</td>
		    <td width="490" class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->sucursal );
		} else {
?>
			<input name="sucursal" type="text" value="<?=prepare_var( printif( $sucursal, $reg->sucursal ) )?>" style="width:100% " maxlength="80" />
<?
		}
?>
			</td>
		</tr>				
		<tr>
        	<td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>CBU</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->cbu );
		} else {
?>
			<input name="cbu" type="text" value="<?=prepare_var( printif( $cbu, $reg->cbu ) )?>" style="width:100% " validate="int:Debe ingresar un CBU" maxlength="22" />
<?
		}
?>
			</td>
		</tr>
		<tr> 
			<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
				<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->id?>" />
			</td>
		    <td class="cuadro2">
<?
		if( $action != 'frmVer' ){
?>			
			<input name="aceptar" type="submit"  value="Aceptar"/>
<?
		}
?>
           	<input name="button" type="button"  onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="<?=$action!='frmVer'?'Cancelar':'Volver'?>" /></td>
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