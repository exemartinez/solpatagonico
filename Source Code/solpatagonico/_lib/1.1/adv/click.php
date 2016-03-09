<?
	include_once '../conf.inc.php';

    // Select del Link
    $r = $db->execute("SELECT * FROM adv_banners WHERE id = '$id'");
    $row = $r->fetchObject( false );
    $link = $row->url;
    if(strtolower(substr($link,0,7)) != "http://") $link = "http://".$link;
    // Impresion del Link
    header("Location: ".$link);
?>