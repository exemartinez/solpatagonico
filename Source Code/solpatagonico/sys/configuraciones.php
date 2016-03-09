<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Configuraciones';
	$name		= 'Configuración';
	$titleForm	= 'Configuraciones';
	$msgDelete	= '¿Desea borrar esta Configuración?';
	# Variables de programación
	$campos		= "variable, valor, descripcion";
	registrar( $campos.",action,id,cur_page" );
	$id = intval($id);
	$where		= "id = '$id'";
	$record = gen_record( $campos );
	
	if ( in($action, 'Agregar', 'Modificar') ) {
		$error = '';
		if( !validate::Text($variable) ) $error .= "<li>Complete un nombre de Variable.</li>";
		if( !validate::Text($valor) ) $error .= "<li>Complete un valor para la variable.</li>";
		if( strpos( $variable, " " ) ) $error .= "<li>No se permiten caracteres de espacio.</li>";
		if( $error ) $action = 'frm'.$action;
	}
	
	if ( $action == 'Agregar' ){
		$record['owner_id'] = $USER->id;
		$record['owner_ip'] = $_SERVER['REMOTE_ADDR'];
		$record['owner_date'] = date( "Y-m-d H:i:s" );
		$ok = $db->Execute( get_sql( 'INSERT', CFG_configTable, $record, '' ) );
		if( !$ok ){
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		} else {
			$error = '';
			$action = '';
		}
	}
	if ( $action == 'Modificar' ){
		$record['last_user_id'] = $USER->id;
		$record['last_user_ip'] = $_SERVER['REMOTE_ADDR'];
		$record['last_user_date'] = date( "Y-m-d H:i:s" );		
		$ok = $db->Execute( get_sql( 'UPDATE', CFG_configTable, $record, "id = '$id'" ) );
		if( $ok ){
			$action = '';
		} else {
			$error = '<li>['.$db->errorno().']'.$db->ErrorMsg($db->errorno()).'</li>';
			$action = 'frm'.$action;
		}
	}
	if ( $action == 'Borrar' ){
		$ok = $db->execute( "DELETE FROM ".CFG_configTable." WHERE id = '$id'" );
		if( !$ok ){
			$error = '<li>Error al borrar el registro.</li>';
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
		$filtros = "&nombre_list=".$nombre_list;
		$orden = ( $orden ? $orden : "variable" );
		$paginas = new pager("
			SELECT * 
			FROM ".CFG_configTable." 
			WHERE variable LIKE '%$nombre_list%' 
			ORDER BY ".( $orden ? $orden : 'variable' ),
			$cur_page,
			20,
			25,
			'nombre_list',
			''
		);	
?>
<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<input type="hidden" name="action" id="action" value="buscar" />
<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<th colspan="4" class="titulosizquierda"><?=$title?></th>
	</tr>
	<tr> 
		<td colspan="4" class="cuadro1izquierda">
			<table border="0" cellpadding="3" cellspacing="0" width="100%" class="text">
				<tr>
					<td><a href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar"><img src="../sys_images/add.gif" 
						border="0" align="absmiddle" /></a> <a 
						href="<?=$_SERVER['PHP_SELF']?>?action=frmAgregar">Agregar <?=$name?></a></td>
				</tr>
				<tr> 
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="80">Variable:</td>
								<td><input name="nombre_list" type="text" id="nombre_list" value="<?=$nombre_list?>" 
									style="width:95% " /></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>		
	<tr>
		<td colspan="4" class="sombra">
			<table width=100% border=0 cellpadding="0" cellspacing="0" class="text">
				<tr>
					<td align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
					<td align="right">
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
	<tr align="center"> 
		<td width="160" align="left" class="titulosizquierda" <?=( strstr($orden,"variable") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=variable ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=variable DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Variable</td>
		<td width="350" align="left" class="titulos" <?=( strstr($orden,"descripcion") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=descripcion ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=descripcion DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Descripción</td>
		<td width="100" align="left" class="titulos" <?=( strstr($orden,"valor") ? 'style="font-weight: bold"' : "" )?>><a href="<?=$_SERVER['PHP_SELF']?>?orden=valor ASC<?=$filtros?>"><img src="../sys_images/flechita_arriba.gif" border="0" align="absmiddle"></a><a href="<?=$_SERVER['PHP_SELF']?>?orden=valor DESC<?=$filtros?>"><img src="../sys_images/flechita_abajo.gif" width="8" height="4" border="0" align="absmiddle"></a> Valor</td>
		<td width="30" class="titulos">&nbsp;</td>
	</tr>
<?
		if ($paginas->num_rows () < 1) {
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
		<td class="<?=$class?>izquierda" align="left"><?=$reg->variable?></td>
		<td class="<?=$class?>"><?=printif(nl2br($reg->descripcion))?></td>
		<td class="<?=$class?>" align="left"><?=printif($reg->valor)?></td>	
		<td align="center" class="<?=$class?>"><a 
			href="<?=$_SERVER['PHP_SELF']?>?action=frmModificar&id=<?=$reg->id?>"><img src="../sys_images/list.gif" alt="Modificar Registro" border="0" /></a> 			
		</td>
	</tr>
<?
			}
		}
?>
	<tr>
		<td class="sombra" colspan="4">
			<table width=100% border=0 cellpadding="0" cellspacing="0" class="text">
				<tr>
					<td align="left">&nbsp;Registros: <?=$paginas->get_first_pos()?> al <?=$paginas->get_last_pos()?> de <?=$paginas->get_total_records()?></td>
					<td align="right">
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
		if( $action == 'frmModificar' ){
			$rs = $db->selectlimit( "SELECT * FROM ".CFG_configTable." WHERE id = $id", 1 );
			if( $rs ){
				$reg = $rs->fetchObject(false);
			}
		}
	
		Form::validate ();
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" onSubmit="return validate(this)" enctype="multipart/form-data">
<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
	<tr>
		<th class="titulosizquierda" colspan="2" align="center"><?=$action=='frmAgregar'?'Agregar':'Modificar'?> <?=$name?></th>
	</tr>		
<?
		if ( $error != '' ){
?>
		<tr> 
			<td colspan="5" class="cuadro1izquierda"><span class="error"><?=$error?></span></td>
		</tr>
<?
		}
?>
	<tr> 
		<td width="150" class="cuadro1izquierda"><?=$error?'<font color="#FF0000">*&nbsp;&nbsp;&nbsp;</font>':''?> Variable</td>
		<td class="cuadro1" width="490"><input name="variable" type="text" value="<?=printif ($reg->variable, $variable)?>" style="width:95% " validar="str:Debe ingrear una variable." /></td>
	</tr>
	<tr> 
		<td class="cuadro1izquierda"><?=$error?'<font color="#FF0000">*&nbsp;&nbsp;&nbsp;</font>':''?> Valor</td>
		<td class="cuadro1"><input name="valor" type="text" value="<?=printif ($reg->valor, $valor)?>" style="width:95% " validar="str:Debe ingrear un valor." /></td>
	</tr>		
	<tr>
	    <td class="cuadro1izquierda">Descripcion</td>
	    <td class="cuadro1"><textarea name="descripcion" rows="5" style="width:95%"><?=printif($reg->descripcion,$descripcion)?></textarea></td>
	</tr>
<?
		if( $reg->owner_id != 0 ){
?>
  <tr>
    <td class="cuadro1izquierda">Creaci&oacute;n</td>
    <td class="cuadro1">[
        <?=fecha::iso2normal(
			substr( $reg->owner_date, 0, 10 ) )." ".substr( $reg->owner_date, 11, 8)?>
        ] [
        <?=printif($reg->owner_ip)?>
        ]
        <?=$db->getone( "SELECT concat(nombre,' ','[',usuario,']') 
			FROM ".CFG_usersTable." WHERE id = '".$reg->owner_id."'" ) ?>
    </td>
  </tr>
<?
		}
		if( $reg->last_user_id != 0 ){
?>
  <tr>
    <td class="cuadro1izquierda">Ultima modificaci&oacute;n</td>
    <td class="cuadro1">[
        <?=fecha::iso2normal(
			substr( $reg->last_user_date, 0, 10 ) )." ".substr( $reg->last_user_date, 11, 8)?>
        ] [
        <?=printif($reg->last_user_ip)?>
        ]
        <?=$db->getone( "SELECT concat(nombre,' ','[',usuario,']') 
			FROM ".CFG_usersTable." WHERE id = '".$reg->last_user_id."'" ) ?>
    </td>
<?
		}
?>	
	<tr> 
		<td class="cuadro2izquierda"><input type="hidden" name="action" id="action" style="width:95% " value="<?=$action=='frmAgregar'?'Agregar':'Modificar'?>" />
		<input type="hidden" name="id" id="id" style="width:95% " value="<?=$reg->id?>" /></td>
	    <td class="cuadro2"><input name="aceptar" type="submit" class="button" value="Aceptar"/>
		<input name="button" type="button" class="button" onClick="location = '<?=$_SERVER['PHP_SELF']?>'" value="Cancelar" /></td>
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
<br>
</body>
</html>