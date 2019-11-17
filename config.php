<?php 

spl_autoload_register(function($class_name){ // esse metodo carrega automatico o arquivo que contem a classe chamada

	$filename = "class" . DIRECTORY_SEPARATOR . $class_name . ".php";

	if(file_exists($filename)){
		require_once($filename);
	}
});


 ?>