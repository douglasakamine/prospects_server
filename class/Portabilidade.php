<?php  

class Portabilidade {

	private $operadora;

	public function consultaOperadora($numero){
		/*    $url_portabilidade = "http://url_here?numero=$numero";
		return $this->operadora = file_get_contents($url_portabilidade); // Consulta a portabilidade através da URL acima
		if($operadora == "0")
		$operadora = "FIXO";
		if($operadora == "-1")
		$operadora = "INVALIDO";     */
		return $this->operadora = "VIVO";
	}
}


?>
