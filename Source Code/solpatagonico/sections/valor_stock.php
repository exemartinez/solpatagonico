<?
	include_once '../inc/conf.inc.php';
	# Variables de diseño
	$title		= 'Valor de Stock';
?>
<html>
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="../styles/estilos.css" type="text/css" />
	<SCRIPT LANGUAGE="JavaScript" SRC="<?=CFG_jsPath?>CalendarPopup.js"></SCRIPT>
</head>

<body>
	<form name="fom1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="action" id="action" value="buscar" />
		<table align="center" width="640" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<th colspan="5" class="titulosizquierda" style="font-size: 14px; font-weight: bold"><?=$title?></th>
			</tr>
			<tr> 
				<td colspan="5" class="cuadro1izquierda"><table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">						
					<tr> 
						<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class=text>
							<tr>
								<td width="120" class="text">C&oacute;digo:</td>								
								<td><input type="text" name="codigo" id="codigo" value="<?=$codigo?>" style="width: 100%"></td>
								<td width="100"><input name="submit" type="submit" value="Buscar" /></td>
							</tr>
						</table></td>
					</tr>
				</table></td>
			</tr><?
	if( $codigo ){
		$rs = $db->Execute( "SELECT * FROM producto WHERE codigo='".$codigo."'" );
		if( $rs->RecordCount() ){
			$reg = $rs->FetchObject( false );
			?><tr>
				<td colspan="5" class="cuadro2izquierda">Producto: [<?=$reg->codigo?>] - <?=$reg->nombre?></td>
			</tr><?
		}
	}
			?><tr>
				<td colspan="5" class="sombra">&nbsp;</td>
			</tr><?
		$rs = $db->Execute( "
			SELECT *
			FROM producto 
			WHERE 1=1 
				AND cantidad_real > 0
			".( $codigo ? " AND codigo = '".$codigo."'" : '' ) 
		);
		if( !$rs->RecordCount() ){
			?><tr>
				<td colspan="5" align="center" class="error">Sin registros coincidentes o el c&oacute;digo 
				ingresado no es v&aacute;lido.</td>
			</tr><?
		} else {
			?><tr>
				<td width="90" class="titulosizquierda" align="center">Código</td>
				<td width="250" class="titulos">Descripción</td>
				<td width="100" class="titulos" align="center">Cantidad Real</td>
				<td width="100" class="titulos" align="center">PPP</td>
				<td width="100" class="titulos" align="center">Valor</td>
			</tr><?		
			$total = 0;
			$total_ppp = 0;
			$total_cantidad_real = 0;
			while( $reg = $rs->FetchNextObject( false ) ){
				$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
				$total = $total + ( $reg->cantidad_real * $reg->ppp );
				$total_cantidad_real = $total_cantidad_real + $reg->cantidad_real;
				$total_ppp = $total_ppp + $reg->ppp;
			?><tr>
				<td class="<?=$class?>izquierda"><?=$reg->codigo?></td>
				<td class="<?=$class?>"><?=printif( $reg->nombre)?> : <?=printif( $reg->descripcion )?></td>
				<td class="<?=$class?>" align="right"><?=intval( $reg->cantidad_real )?></td>
				<td class="<?=$class?>" align="right">$ <?=doubleval( $reg->ppp )?></td>
				<td class="<?=$class?>" align="right">$ <?=doubleval( $reg->cantidad_real * $reg->ppp )?></td>
			</tr><?
			}
			$class = $class == 'cuadro1' ? 'cuadro2' : 'cuadro1';
			?>
			<tr style="font-weight: bold">
				<td class="<?=$class?>izquierda" colspan="2">Total Valor Stock</td>
				<td class="<?=$class?>" align="right"><?=intval( $total_cantidad_real )?></td>
				<td class="<?=$class?>" align="right">&nbsp;</td>
				<td class="<?=$class?>" align="right">$ <?=doubleval( $total )?></td>
			</tr>
			<tr>
					<td colspan="5" class="sombra">&nbsp;</td>
			</tr><?
		}
		?></table>
</form>
</body>
</html>