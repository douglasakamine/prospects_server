<?php 

class Sql extends PDO {

	private $conn;

	public function __construct(){
	
	$this->conn = new PDO("mysql:dbname=test;host=localhost", "root", "");	
	
	}

	private function setParams($statement, $parameters = array()){   // funcao de setar multiplos parametros, chamado pelo metodo query
		foreach ($parameters as $key => $value){
			$this->setParam($statement, $key, $value);
		}
	}

	private function setParam($statement, $key, $value){  // funcao de setar apenas 1 parametro, chamado pelo metodo query
		$statement->bindParam($key, $value);
	}

	public function query($rawQuery, $params = array()){ // metodo query recebe o comando do banco, prepara e executa
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);
		$stmt->execute();
		return $stmt;
	}
	public function select($rawQuery, $params = array()){

		$stmt = $this->query($rawQuery, $params);
		return $stmt->FetchAll(PDO::FETCH_ASSOC);
	}
	

}

 ?>