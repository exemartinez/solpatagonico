<?
    define( 'authorize', false );
    include_once("inc/conf.inc.php");

	function create_menu( $id_padre = 0, $pos = 0, $last = false ){
		global $USER;
		$id_padre = intval( $id_padre );
		$db = NewADOConnection( CFG_DB_dsn );
		$menues = $db->execute(
			"SELECT s.*
			FROM ".CFG_sectionsTable." s
			JOIN ".CFG_privilegesTable." p ON p.id_seccion = s.id
			WHERE p.id_grupo = '".$USER->id_grupo."'
				AND s.id_padre = ".$id_padre."
			ORDER BY s.posicion, s.nombre"
		);

		if( $id_padre == 0 ){
?>
<table border="0" cellpadding="0" cellspacing="0" class=text>
    <tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="sys_images/map_folder.gif" border="0" /></td><td>&nbsp;<a href="sys/principal.php" class="text" target="main">Home</a></td>
				</tr>
			</table>
		</td>
    </tr>
<?
		}
		
		if( $menues->RecordCount() > 0 ){ 
			$cant = 0;
			while( $menu = $menues->FetchNextObject() ){
				$cant++;
?>
    <tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" class=text>
				<tr>
					<td nowrap><?
				for( $i = 0 ; $i < $pos ; $i++ ) {
					?><img src="sys_images/px.gif" width="19" border="0" /><?
				}
					?><img src="sys_images/px.gif" width="19" border="0" />
					<img src="sys_images/map_folder.gif" border="0" /></td>
					<td>&nbsp;<? if ($menu->VINCULO != "") { ?><a href="<?=$menu->VINCULO?>" target="main" 
					class="text"><?=$menu->NOMBRE?></a><? } else echo $menu->NOMBRE?></td>
				</tr>
			</table>
		</td>
    </tr>
<?
			create_menu( $menu->ID, $pos+1, $last );
			}
		}
    if( $id_padre == 0 ){
?>
</table>
<?
    }
}

create_menu();
?>
<table width="100%" border="0">
    <tr height="30">
	<td><a target="_top" href="login.php?action=logout" title="Salir"><img border="0" src="sys_images/exit.gif" hspace="8" align="absmiddle">Salir</a></td>
    </tr>
</table>
