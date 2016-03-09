<?
	/**
	* noCache
	* Evita que la página generada sea cacheada para asegurar que siempre se obtengan contenidos actualizados.
	*/
	function noCache(){
		header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
	} # END function noCache
		
	if(!defined('allowCache') || allowCache==false) noCache();
?>