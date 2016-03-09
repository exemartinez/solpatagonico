<?
	define( 'authorize', false );
	include_once 'inc/conf.inc.php';
?>
<html>
<head>
	<title><?=CFG_site?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="styles/estilos.css" rel="stylesheet" type="text/css">
</head>

<body scroll="no">
<?
	if( $_GET['menu'] == '1' ){
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height:100% ">
		<tr> 
			<td id="menu" valign="top"><? include('menu.php'); ?></td>
		</tr>
<? 
		if( CFG_maintenance == 1 ){
?>
		<tr>
			<td class="error" align="center"><b>Sistema en Mantenimiento</b></td>
		</tr>
<?
		}
?>
		<tr> 
			<td height="100%" a><iframe id="main" name="main" src="sys/principal.php" frameborder="0" 
				style="width: 100%; height: 100%;"></iframe></td>
		</tr>
	</table>
<?
	} else {
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height:100% ">
		<tr> 
			<td id="menu" valign="top" colspan="2"><? include('sys/cabecera.inc.php'); ?></td>
		</tr>
		<tr> 
			<td width="200" valign="top" class="sombra">
				<table border="0" cellpadding="0" cellspacing="0" widht="100%" style="height: 100%">
					<tr>
						<td class="sombra"></td>
					</tr>
					<tr>
						<td height="100%" valign="top"><div id="principal" style="position:absolute;width:200;height:100%;overflow:auto;"><? include( 'menuvertical.php' ); ?></div></td>
					</tr>
				</table>
			</td>
			<td height="100%">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="height: 100%">
					<tr>
						<td class="sombra" align="center"><?=CFG_maintenance!=1?'&nbsp;':'<span class="error"><b>Sistema en Mantenimiento</b></span>'?></td>
					</tr>
					<tr>
						<td height="100%"><iframe id="main" name="main" src="sys/principal.php" frameborder="0" 
					style="width: 100%; height: 100%;"></iframe></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?
	}
?>
</body>
</html>