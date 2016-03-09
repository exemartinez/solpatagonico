<?
	function imageresize (&$src, $x, $y) {

		$dst=ImageCreate ($x, $y);
		ImagePaletteCopy ($dst,$src);
		$scX  =(imagesx ($src)-1)/$x;
		$scY  =(imagesy ($src)-1)/$y;
		$scX2 =intval($scX/2);
		$scY2 =intval($scY/2);
		for ($j = 0; $j < ($y); $j++) {
			$sY = intval($j * $scY);
			$y13 = $sY + $scY2;
			for ($i = 0; $i < ($x); $i++) {
				$sX = intval($i * $scX);
				$x34 = $sX + $scX2;
				$c1 = ImageColorsForIndex ($src, ImageColorAt ($src, $sX, $y13));
				$c2 = ImageColorsForIndex ($src, ImageColorAt ($src, $sX, $sY));
				$c3 = ImageColorsForIndex ($src, ImageColorAt ($src, $x34, $y13));
				$c4 = ImageColorsForIndex ($src, ImageColorAt ($src, $x34, $sY));
				$r = ($c1['red']+$c2['red']+$c3['red']+$c4['red'])/4;
				$g = ($c1['green']+$c2['green']+$c3['green']+$c4['green'])/4;
				$b = ($c1['blue']+$c2['blue']+$c3['blue']+$c4['blue'])/4;
				ImageSetPixel ($dst, $i, $j, ImageColorClosest ($dst, $r, $g, $b)); 
			}
		}
		return ($dst);
	} # END function imageresize

	function ImageCopyResampleBicubic (
		&$dst_img, // Puntero a imagen destino
		&$src_img, // Puntero a imagen origien
		$dst_x,	$dst_y, // Cordenada inicial del Destino
		$src_x, $src_y, // Cordenada inicial del Origen
		$dst_w, $dst_h, // Ofset del Destino
		$src_w, $src_h  // Ofset del Origen
	){
		$palsize = ImageColorsTotal ($src_img);
		for ($i = 0; $i < $palsize; $i++) {  // get palette.
			$colors = ImageColorsForIndex ($src_img, $i);
			ImageColorAllocate ($dst_img, $colors['red'], $colors['green'], $colors['blue']);
		}
		
		$scaleX = ($src_w - 1) / $dst_w;
		$scaleY = ($src_h - 1) / $dst_h;
		
		$scaleX2 = (int) ($scaleX / 2);
		$scaleY2 = (int) ($scaleY / 2);
		
		$dstSizeX = imagesx( $dst_img );
		$dstSizeY = imagesy( $dst_img );
		$srcSizeX = imagesx( $src_img );
		$srcSizeY = imagesy( $src_img );
		
		for ($j = 0; $j < ($dst_h - $dst_y); $j++) {
			$sY = (int) ($j * $scaleY) + $src_y;
			$y13 = $sY + $scaleY2;
			
			$dY = $j + $dst_y;
			
			if (($sY > $srcSizeY) or ($dY > $dstSizeY)) break 1;		
		
			for ($i = 0; $i < ($dst_w - $dst_x); $i++) {
				$sX = (int) ($i * $scaleX) + $src_x;
				$x34 = $sX + $scaleX2;
				
				$dX = $i + $dst_x;
				
				if (($sX > $srcSizeX) or ($dX > $dstSizeX)) break 1;
				
				$color1 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $y13));
				$color2 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $sY));
				$color3 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $y13));
				$color4 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $sY));
				
				$red = ($color1['red'] + $color2['red'] + $color3['red'] + $color4['red']) / 4;
				$green = ($color1['green'] + $color2['green'] + $color3['green'] + $color4['green']) / 4;
				$blue = ($color1['blue'] + $color2['blue'] + $color3['blue'] + $color4['blue']) / 4;
				
				ImageSetPixel ($dst_img, $dX, $dY,
				ImageColorClosest ($dst_img, $red, $green, $blue)); 
			}
		}
	} # END function ImageCopyResampleBicubic
	
	# Depende de ImageCopyResampleBicubic la cual se define en el bloque anterior
	function ImageResizeMax ( &$im, $max_x, $max_y) {
		if ($max_x != 0 && $max_y != 0) {
			$x = imagesx($im);
			$y = imagesy($im);
			
			if ($x > $max_x) {
				$y = (int)floor($y * ($max_x / $x));
				$x = $max_x;
			}
			
			if ($y > $max_y) {
				$x = (int)floor($x * ($max_y / $y));
				$y = $max_y;
			}
			
			if (imagesx($im) != $x || imagesy($im) != $y) {
				$tmp = imagecreatetruecolor($x, $y);
				ImageCopyResampleBicubic($tmp, $im, 0, 0, 0, 0, $x, $y, imagesx($im), imagesy($im));
			}
		}
		return $tmp;
	} # END function ImageResizeMax

	function print_image( $campo, $tabla, $id, $link = "", $class = "", $target = "", $img_html = "" ){
		$db = NewADOConnection( CFG_DB_dsn );
		$row = $db->GetRow( "SELECT * FROM ".$tabla." WHERE id = '".$id."'" );

		if ( strstr($row[$campo."_mime"], "image") != NULL ) { /* Es una imagen */
			if ( $link != "" ) {
?>
		<a href="<?=$link?>" <?=( $class != "" ? 'class="'.$class.'"' : "" )?> <?=( $target != "" ? 'target="'.$target.'"' : "" )?>>
<?
			}
?>
		<img src="../inc/file.php?id=<?=$id?>&section=<?=$tabla?>&name=<?=$campo?>" border="0" <?=$img_html?>></a>
<?
			if ( $link != "" ) {
?>
		</a>
<?
			}
		}
		if ( strstr($row[$campo."_mime"], "flash") != NULL ) { /* Es un flash */
?>
		<object type="application/x-shockwave-flash" data="../inc/file.php?id=<?=$id?>&section=<?=$tabla?>&name=<?=$campo?>" 
		width="<?=$row[$campo."_ancho"]?>" height="<?=$row[$campo."_alto"]?>">
			<param name="movie" 
			value="../inc/file.php?id=<?=$id?>&section=<?=$tabla?>&name=<?=$campo?>"/>
		</object>
<?
		}
	}
?>
