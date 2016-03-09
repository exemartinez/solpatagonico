<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Sucursales';
	$name		= 'Sucursal';
	$titleForm	= 'Sucursales';
	$msgDelete	= '¿Esta seguro que desea eliminar esta Sucursal?';
	$msgUnDelete = '¿Esta seguro que desea restaurar esta Sucursal?';
	# Variables de programación
	$tabla		= "sucursal";
	$campos		= "id_zona,id_cliente,nombre,direccion,cp,telefono,fax,mail,contacto";
	registrar( $campos );
	$id = intval( $id );
	if( !isset( $ocultar_eliminados ) ) $ocultar_eliminados = 1;
	$where		= "id = '$id'";
	
	if ( in($action,'Agregar','Modificar') ) {
		if ( !validate::Integer($id_zona) ) 
			$error .= "<li>Debe seleccionar una Zona.</li>";
		if ( !validate::Integer($id_cliente) ) 
			$error .= "<li>Debe seleccionar un Cliente.</li>";			
		if ( !validate::Text($nombre) ) 
			$error .= "<li>Debe ingresar un Nombre.</li>";
		if( 
			$id_cliente 
			&& $nombre 
			&& $db->GetOne( "SELECT COUNT(*) FROM ".$tabla." WHERE id_cliente='".$id_cliente."' AND nombre='".$nombre."'".( $action == 'Modificar' ? " AND id !='".$id."'" : '' ) ) 
		) $error .= "<li>El nombre de la sucursal para el cliente seleccionado ya existe, por favor ingrese otro.</li>";
		if( $telefono && !preg_match( '/^[0-9]+([-]*[0-9]+)*$/', $telefono ) ) 
			$error .= '<li>Debe ingresar un Telefono v&aacute;lido (n&uacute;meros y/o guiones)';
		if( $fax && !preg_match( '/^[0-9]+([-]*[0-9]+)*$/', $fax ) ) 
			$error .= '<li>Debe ingresar un Fax v&aacute;lido (n&uacute;meros y/o guiones)';
		if ( $email && !validate::Email($email) ) 
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
			"SELECT s.id, s.nombre, s.fecha_baja, c.razonsocial cliente, z.nombre zona 
			FROM ".$tabla." s 
			LEfT JOIN cliente c ON s.id_cliente = c.id 
			LEfT JOIN zona z ON s.id_zona = z.id 
			WHERE 1=1 AND ( c.fecha_baja is NULL OR c.fecha_baja = '0000-00-00 00:00:00' ) 
				AND s.nombre LIKE '".html_entity_decode( $nombre_list )."%'".(
				$ocultar_eliminados ? " AND ( s.fecha_baja is NULL OR s.fecha_baja = '0000-00-00 00:00:00' )" : '' 
			).(
				$cliente_lst ? " AND s.id_cliente='".$cliente_lst."'" : "" 
			).(
				$zona_lst ? " AND s.id_zona='".$zona_lst."'" : "" 
			)." ORDER BY s.nombre" ,
			$cur_page,
			20,
			25,
			"nombre_list,ocultar_eliminados,zona_lst,cliente_lst",
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
							<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&id_cliente=<?=$cliente_lst?>"><img src="../sys_images/add.gif" border="0" align="absmiddle" /></a> <a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar&id_cliente=<?=$cliente_lst?>">Agregar <?=$name?></a></td>
							<td>&nbsp;</td>
						</tr>
						<tr> 
							<td colspan="2">
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
                                	<tr>
                                		<td width="120" class="text">Sucusal:</td>
                                		<td><input name="nombre_list" type="text" id="nombre_list" value="<?=prepare_var( $nombre_list )?>" style="width:100% " /></td>
                                		<td width="100"><input name="submit" type="submit" value="Buscar" />	</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Cliente:</td>
                                		<td><?
									$rs = $db->Execute( "
										SELECT CONCAT( cuit, ' - ', razonsocial ) nombre, id 
										FROM cliente 
										WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' 
										ORDER BY cuit
									" );
									echo $rs->GetMenu2(
										'cliente_lst',
										$cliente_lst,
										": -- Seleccione un cliente -- ",
										false,
										1,
										'id="cliente_lst" style="width: 100%"'
									);
										?></td>
                                		<td>&nbsp;</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Zona:</td>
                                		<td><?
									$rs = $db->Execute( "
										SELECT nombre, id 
										FROM zona 
										ORDER BY nombre
									" );
									echo $rs->GetMenu2(
										'zona_lst',
										$zona_lst,
										": -- Seleccione una zona -- ",
										false,
										1,
										'id="zona_lst" style="width: 100%"'
									);
										?></td>
                                		<td>&nbsp;</td>
                               		</tr>
                                	<tr>
                                		<td class="text">Ocultar eliminados:</td>
                                		<td><select name="ocultar_eliminados" id="ocultar_eliminados" style="width:100% ">
                                        	<option value="0">No</option>
                                        	<option value="1" <?=( $ocultar_eliminados == 1 ? 'selected' : '' )?>>Si</option>
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
				<td width="120" class="titulosizquierda">Zona</td>
				<td width="380" class="titulos">Raz&oacute;n Social &gt; Sucursal</td>
				<td width="80" class="titulos">Eliminado</td>
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
				<td class="<?=$class?>izquierda"><?=$reg->zona?></td>
				<td class="<?=$class?>"><?=$reg->cliente.' > '.$reg->nombre?></td>
				<td class="<?=$class?>"><?=( validate::date( substr( $reg->fecha_baja, 0, 10 ) ) ? fecha::iso2normal( substr( $reg->fecha_baja, 0, 10 ) ).' '.substr( $reg->fecha_baja, 10 ) : '&nbsp;' )?></td>
				<td align="center" class="<?=$class?>"><table border="0" align="left">
					<tr>
<?
				if( !validate::date( substr( $reg->fecha_baja, 0, 10 ) ) ){
?>					
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img src="../sys_images/list.gif" border="0" alt="Modificar" /></a></td>
						<td width="20" align="center"><a href="<?=$_SERVER['PHP_SELF']?>?action=frmVer&id=<?=$reg->id?>"><img src="../sys_images/search.gif" border="0" alt="Ver" /></a></td>
<?
					if( 
						!$db->GetOne( "
							SELECT COUNT(*) 
							FROM pedido 
							WHERE id_sucursal = '".$reg->id."' AND id_estado_pedido NOT IN ( 6, 7 ) 
						" )			
					){
?>
						<td width="20" align="center"><a href="javascript: if (confirm('<?=$msgDelete?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?>&br=1'}"><img src="../sys_images/delete.gif" border="0" alt="Eliminar" /></a></td>						
<?
					}
				} else {
?>
						<td width="20" align="center"><a href="javascript: if (confirm('<?=$msgUnDelete?>')) { location = '<?=$_SERVER['PHP_SELF']?>?action=Borrar&id=<?=$reg->id?>&br=0'}"><img src="../sys_images/restaurar.gif" alt="Restaurar" border="0"></a></td>
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
        	<td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Cliente</td>
        	<td class="cuadro1"><?
		if( $action == 'frmVer' ){
			echo printif( $db->GetOne( "SELECT CONCAT( cuit, ' - ', razonsocial ) FROM cliente WHERE id = '".$reg->id_cliente."'" ) );
		} else {
			$rs = $db->Execute( "
				SELECT CONCAT( cuit, ' - ', razonsocial ) nombre, id 
				FROM cliente 
				WHERE fecha_baja is NULL OR fecha_baja = '0000-00-00 00:00:00' 
				ORDER BY cuit
			" );
			echo $rs->GetMenu2(
				'id_cliente',
				printif ( $id_cliente, $reg->id_cliente ),
				": -- Seleccione un cliente -- ",
				false,
				1,
				'id="id_cliente" style="width: 100%"'
			);
		}
			?></td>
		</tr>
		<tr>
        	<td class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Nombre Sucursal</td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->nombre );
		} else {
?>
			<input name="nombre" type="text" value="<?=prepare_var( printif( $nombre, $reg->nombre ) )?>" style="width:100% " 
			validate="str:Debe ingresar un Nombre" maxlength="20" />
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
        	<td class="cuadro1izquierda">C&oacute;digo postal </td>
        	<td class="cuadro1">
<?
		if( $action == 'frmVer' ){
			echo printif( $reg->cp );
		} else {
?>
			<input name="cp" type="text" value="<?=prepare_var( printif( $cp, $reg->cp ) )?>" style="width:100% " 
		 	maxlength="10" />
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
			echo printif( $reg->email );
		} else {
?>
			<input name="email" type="text" value="<?=prepare_var( printif( $email, $reg->email ) )?>" style="width:100% " 
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
		</tr>
		<tr> 
			<td width="150" class="cuadro1izquierda"><?=($action!='frmVer'?'*':'')?>Zona</td>
		    <td width="490" class="cuadro1"><?
		if( $action == 'frmVer' ){
			echo printif( $db->GetOne( "SELECT nombre FROM zona WHERE id='".$reg->id_zona."'" ) );
		} else {
			$rs = $db->Execute( "
				SELECT nombre, id 
				FROM zona 
				ORDER BY nombre
			" );
			echo $rs->GetMenu2(
				'id_zona',
				printif ( $id_zona, $reg->id_zona ),
				": -- Seleccione una zona -- ",
				false,
				1,
				'id="id_zona" style="width: 100%"'
			);
		}
			?></td>
		</tr>
<?
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