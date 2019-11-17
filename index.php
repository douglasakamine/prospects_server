<?php

require_once("config.php");
#########################################
#					#
#  DESENVOLVIDO POR DOUGLAS AKAMINE	#
#  DUVIDAS: douglasakamine@gmail.com	#
# 					#
#########################################

//----------PEGAR VARIÁVEIS DA URL---------------------------------------------------------------------------------------------------------

$numero = $_GET['numero'];
$midia = $_GET['midia']; 
$codigo = $_GET['codigo'];

//------------------------------------------------------------------------------------------------------------------------------------------

$result = new Master();
$result->verificaCliente($numero, $midia, $codigo);



?>