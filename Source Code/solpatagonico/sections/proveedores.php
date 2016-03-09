<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Proveedores';
	$name		= 'Proveedor';
	$titleForm	= 'Proveedores';
	$msgDelete	= '¿Esta seguro que desea eliminar este Proveedor?';
	$msgUnDelete = '¿Esta seguro que desea restaurar este Proveedor?';
	# Variables de programación
	$tabla		= "proveedor";
	$campos		= "cuit,razonsocial,direccion,telefono,fax,mail,contacto";
	registrar( $campos );
	$id = intval( $id );
	if( !isset( $ocultar_eliminados ) ) $ocultar_eliminados = 1;
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !is_cuit($cuit) ) 
			$error .= "<li>Debe ingresar un CUIT v&aacute;lido.</li>";
		if ( !validate::Text($razonsocial) ) 
			$error .= "<li>Debe ingresar una Razon Social.</li>";
		if ( $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE cuit='".$cuit."'".( $action == 'Modificar' ? " AND id != '".$id."'" : '' ) ) ){
			$error .= "<li>CUIT existente, ingrese otro.</li>";
			unset( $cuit );
		}
		if( $telefono && !preg_match( '/^[0-9]+([-]*[0-9]+)*$/', $telefono ) ) 
			$error .= '<li>Debe ingresar un Telefono v&aacute;lido (n&uacute;meros y/o guiones)';
		if( $fax && !preg_match( '/^[0-9]+([-]*[0-9]+)*$/', $fax ) ) 
			$error .= '<li>Debe ingresar un Fax v&aacute;lido (n&uacute;meros y/o guiones)';
		if ( $mail && !validate::Email($mail) ) 
			$error .= "<li>Debe ingresar un Email v&aacute;lido.</li>";
		if( $error != '' ){
			$action = 'frm'.$action;			
		}
		$record = gen_record( $campos );
	}
	
	if ( $action == 'Agregar' ){
		$record["fecha_alta"] = date( "Y-m-d H:i:s" );

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
		if( $restaurar ){
			$record["fecha_baja"] = '';
		}
		
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
		$ok = $db->execute( "UPDATE ".$tabla." SET fecha_baja=".( $br ? 'NOW()' : "''" )." WHERE id = '".$id."' " );
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
			WHERE 1=1
				AND razonsocial LIKE '".html_entity_decode( $nombre_list )."%'".
				( $cuit_list != '' ? " AND cuit like '".html_entity_decode( $cuit_list )."%' " : '' ).
				( $ocultar_eliminados ? " AND ( fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' )" : '' ).
			" ORDER BY cuit" ,
			$cur_page,
			20,
			25,
			"nombre_list,ocultar_eliminados",
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
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&cuit=<?=$cuit_list?>"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&cuit=<?=$cuit_list?>">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">CUIT:</td>
                                		<td><input name="cuit_list" type="text" id="cuit_list" value="<?=prepare_var( $cuit_list )?>" style="width:100% " /></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" />	</td>
                               		</tr>
                                	<tr>
                                		<td width="120" class="text">Proveedor:</td>
                                		<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" style="width:100% " /></td>
                                		<td width="100">&nbsp;</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Ocultar eliminados: </td>
                                		<td><select name="ocultar_eliminados" id="ocultar_eliminados" style="width:100% ">
											<option value="0">No</option>
											<option value="1" <?=( $ocultar_eliminados ? 'selected' : '' )?>>Si</option>
										</select></td>
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
				<td width="120" class="titulosizquierda">CUIT</td>
				<td class="titulos">Razon Social</td>
				<td width="80" class="titulos">Eliminado</td>
				<td width="120" class="titulos">&nbsp;</td>
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
				<td align="left" class="<?=$class?>izquierda"><?=$reg->cuit?></td>
				<td class="<?=$class?>"><?=$reg->razonsocial?></td>
				<td class="<?=$class?>"><?=( validate::date( substr( $reg->fecha_baja, 0, 10 ) ) ? fecha::iso2normal( substr( $reg->fecha_baja, 0, 10 ) ).' '.substr( $reg->fecha_baja, 10 ) : '&nbsp;' )?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr>
<?
				if( !validate::date( substr( $reg->fecha_baja, 0, 10 ) ) ){
?>
						<td width="20" align="center"><a href="proveedores_precios.php?&proveedor_lst=<?=$reg->id?>"><img src="../sys_images/precios.gif" border="0" alt="Precios"></a></td>
						<td width="20" align="center"><a href="cuentas.php?proveedor_lst=<?=$reg->id?>"><img src="../sys_images/cuentacorriente.gif" border="0" alt="Cuentas"></a></td>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img src="../sys_images/list.gif" border="0" alt="Modificar" /></a></td>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmVer&id=<?=$reg->id?>"><img src="../sys_images/search.gif" border="0" alt="Ver" /></a></td>
						<td width="20" align="center"><a href="javascript: if (confirm('<?=$msgDelete?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?>&br=1'}"><img src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a></td>
<?
				} else {
?>
						<td width="20" align="center"><a href="javascript: if (confirm('<?=$msgUnDelete?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?>&br=0'}"><img src="../sys_images/restaurar.gif" border="0" alt="Restaurar"></a></td>
<?
				}
?>
					</tr>
				</table></td>
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

	if ( in($action,'frmAgregar','frmModificar','frmVer') ) {
		if ( in($action,'frmModificar','frmVer' ) ) {
			$id = intval($id);
			$rs = $db->SelectLimit( "SELECT * FROM ".$tabla." WHERE id = $id", 1 );
			$reg = $rs->FetchObject(false);
		}
		
		Form::validate ();
?>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
	<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th class="titulosizquierda" colspan="2" align="center" style="font-size: 14px; font-weight: bold"><?=$action=='frmAgregar'?'Agregar':($action=='frmModificar'?'Modificar':'Ver')?> <?=$name?></th>
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
			<td width="150" class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>CUIT</td>
		    <td width="490" class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->cuit );
		} else {
?>
			<input name="cuit" type="text" value="<?=prepare_var( printif( $cuit, $reg->cuit ) )?>" 
			style="width:100% " validate="str:Debe ingresar un CUIT" maxlength="11" />
<?
		}
?>
			</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Raz&oacute;n Social </td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->razonsocial );
		} else {
?>
			<input name="razonsocial" type="text" value="<?=prepare_var( printif( $razonsocial, $reg->razonsocial ) )?>" 
			style="width:100% " validate="str:Debe ingresar un Razon Social" maxlength="150" />
<?
		}
?>			
			</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Direcci&oacute;n</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->direccion );
		} else {
?>
			<input name="direccion" type="text" value="<?=prepare_var( printif( $direccion, $reg->direccion ) )?>" 
			style="width:100% " maxlength="120" />
<?
		}
?>
			</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Tel&eacute;fono</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->telefono );
		} else {
?>
			<input name="telefono" type="text" value="<?=prepare_var( printif( $telefono, $reg->telefono ) )?>" 
			style="width:100% " maxlength="20" />
<?
		}
?>
			</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Fax</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->fax );
		} else {
?>
			<input name="fax" type="text" value="<?=prepare_var( printif( $fax, $reg->fax ) )?>" style="width:100% " 
			maxlength="20" />
<?
		}
?>
			</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Email</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->mail );
		} else {
?>
			<input name="mail" type="text" value="<?=prepare_var( printif( $mail, $reg->mail ) )?>" style="width:100% " 
			maxlength="255" />
<?
		}
?>
			</td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Contacto</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->contacto );
		} else {
?>
			<input name="contacto" type="text" value="<?=prepare_var( printif( $contacto, $reg->contacto ) )?>" 
			style="width:100% " maxlength="80" />
<?
		}
?>
			</td>
		</tr><?
		if( validate::date( substr( $reg->fecha_alta, 0, 10 ) ) ){
			?><tr>
        	<td class="cuadro1izquierda">Fecha de alta </td>
        	<td class="cuadro1"><?=printif( fecha::iso2normal( substr( $reg->fecha_alta, 0, 10 ) ).' '.substr( $reg->fecha_alta, 10 ) )?></td>
		</tr><?
		}
		if( validate::date( substr( $reg->fecha_baja, 0, 10 ) ) ){
			?><tr>
        	<td class="cuadro1izquierda">Fecha de baja </td>
        	<td class="cuadro1"><?=printif( fecha::iso2normal( substr( $reg->fecha_baja, 0, 10 ) ).' '.substr( $reg->fecha_baja, 10 ) )?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda">Restaurar registro </td>
        	<td class="cuadro1"><input type="checkbox" name="restaurar" id="restaurar" value="1"></td>
		</tr><?
		}
		?><tr> 
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