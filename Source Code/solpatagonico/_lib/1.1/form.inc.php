<?
	/**
	* Libreria de Formularios
	* Funciones relacionadas con tratamiento de formularios.
	*  
	*/
	class Form {	

		/**
		* validate
		* Evita que la página genedada sea cacheada para asegurar que siempre se obtengan contenidos actualizados.
		*  
		* @param string $inLine booleano, para imprimir o no el codigo si esta en debug o no.
		* @param string $form_name es el nombre del formulario.
		*/
		function validate( $inLine=FALSE, $form_name = '' ){
			if(!$GLOBALS['formValidationPrinted'] and !$inLine){
				echo '<script type="text/javascript" src="'.CFG_jsPath.'formValidation.php"></script>';
				$GLOBALS['formValidationPrinted']=TRUE;
			}elseif(!$GLOBALS['formValidationPrinted'] and $inLine){
				?>
					<script>
						<?
							if(!CFG_debug and file_exists(CFG_libPath."javascript/formValidation_c.js")){
								include_once(CFG_libPath."javascript/formValidation_c.js");
							}else{
								include_once(CFG_libPath."javascript/formValidation.js");
							}
						?>
					</script>					
					<?
						if( $form_name != '' ){
					?>
							<script for="form" event="onsubmit">
								return validate(this);
							</script>
					<?
						}
					?>
				<?
				$GLOBALS['formValidationPrinted']=TRUE;
			}
		} # END function validate
	}
?>