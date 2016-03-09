<?
	define( 'authorize', false );
    include_once "inc/conf.inc.php";

	function create_menu( $id_padre = 0 ){
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
		if( $menues->RecordCount() > 0 ){ 
			$ret = '
				var menu'.$id_padre.'	= new WebFXMenu;
				menu'.$id_padre.'.left	= 47;
				menu'.$id_padre.'.top	= 86;
				menu'.$id_padre.'.width	= 200;
			';
			while( $menu = $menues->FetchNextObject(false) ){
				$sub_ret = create_menu( $menu->id );
				$ret .= '//inicio--menu'.$menu->id.'|'.$sub_ret.'//fin--menu'.$menu->ID;
				$ret .= '
					menu'.$id_padre.'.add(
						new WebFXMenuItem(
							\''.( $menu->img_mime ? '<img align="absmiddle" src="inc/file.php?section='.CFG_sectionsTable.'&name=img&id='.$menu->id.'" border="0">&nbsp;' : '' ).'<span valign="absmidddle">'.($menu->nombre == '' ? 'error - Sin Nombre' : $menu->nombre).'</span>'.'\',//nombre
							"javascript: Ir(\''.$menu->vinculo.'\')",//href
							\''.$menu->nombre.'\',//tooltip
							'.($sub_ret!=''?'menu'.$menu->id:'null').'//objeto hijo
						)
					);
				';
				if( $menu->id_padre == 0 || $menu->id_padre == NULL ){
					$ret .= '
					menuBar.add(
						new WebFXMenuButton(
							\''.( $menu->img_mime ? '<img align="absmiddle" src="inc/file.php?section='.CFG_sectionsTable.'&name=img&id='.$menu->id.'" border="0" > ' : '' ).'<span valign="absmidddle">'.($menu->nombre == '' ? 'error - Sin Nombre' : $menu->nombre).'</span>'.'\',//nombre
							'.($menu->vinculo != "" ? "'javascript: Ir(\"".$menu->vinculo."\")'" : 'null').',//href
							\''.$menu->nombre.'\',//tooltip
							'.($sub_ret!=''?'menu'.$menu->id:'null').'//objeto hijo
						)
					);';
				}
			}
		}
		return $ret;		
	}
?>
<link rel="stylesheet" type="text/css" href="styles/xmenu.css">
<script type="text/javascript" src="javascript/cssexpr.js"></script>
<script language="JavaScript1.4" type="text/javascript" src="javascript/xmenu.php"></script>
<script language="JavaScript1.4" type="text/javascript">
	function Ir( pagina ){
		document.getElementById('main').style.display = 'block';
		open( pagina, 'main', '' );
	}

	webfxMenuImagePath				= "sys_images/xmenu/";
	webfxMenuUseHover				= true;
	webfxMenuShowTime				= 100;
	webfxMenuHideTime				= 200;	
	// define the default values
	//Border
	webfxMenuDefaultBorderLeft		= 2;
	webfxMenuDefaultBorderRight		= 2;
	webfxMenuDefaultBorderTop		= 2;
	webfxMenuDefaultBorderBottom	= 2;
	//Padding
	webfxMenuDefaultPaddingLeft		= 1;
	webfxMenuDefaultPaddingRight	= 1;
	webfxMenuDefaultPaddingTop		= 1;
	webfxMenuDefaultPaddingBottom	= 1;
	//Shadow
	webfxMenuDefaultShadowLeft		= 0;
	webfxMenuDefaultShadowRight		= 0;
	webfxMenuDefaultShadowTop		= 0;
	webfxMenuDefaultShadowBottom	= 0;
	// Separador
	var menu_sep 					= new WebFXMenuSeparator;
	//Carga de menues
	var menu;
	var menuBar = new WebFXMenuBar;	

<?=create_menu()?>
//Fin carga de menues
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
                <td width="100%" align="left"><? include ("sys/cabecera.inc.php") ?></td>
        </tr>
        <tr>
                <td width="100%" align="left"><table class="menusuperior"
                        width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                        <td align="left"><script type="text/javascript"
                                                language="JavaScript">document.write( menuBar );</script></td>
                                        <td align="right"><table border="0" cellpadding="0"
                                                cellspacing="0">
                                                        <tr>
                                                                <td class="text">Bienvenido/a <?=$USER->nombre?></td>
                                                                <td></td>
                                                        </tr>
                                                        <tr>
                                                                <td align="right" colspan="2"><a
                                                                    target="_top" href="login.php?action=logout" title="Salir">Salir<img
                                                                    border="0" src="sys_images/exit.gif" hspace="8"
                                                                    align="absmiddle"></a></td>
                                                        </tr>
                                                </table>
                                        </td>
                                </tr>
                        </table>
                </td>
        </tr>
        <tr>
                <td height="5" class="sombra"></td>
        </tr>
</table>
