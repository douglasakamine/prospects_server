<?php 

class Master {

	public function verificaCliente($numero, $midia, $codigo){

		$stmt = new Sql();
		$cliente = $stmt->select("SELECT cliente FROM base_master_teste WHERE telefone = :NUM ORDER BY id", array(":NUM"=>$numero));
		$cliente = $cliente[0]['cliente'];

if($cliente == '')
{                      
	$total_finalizados = $stmt->select("SELECT finalizado FROM clientes"); 
	$arrayResultFinalizados = count($total_finalizados);     //Soma a quantidade de clientes
	
	$sum_finalizados = $stmt->select("SELECT SUM(finalizado) FROM clientes");         //Soma as boleanas. 	
	
	    if($sum_finalizados[0]['SUM(finalizado)'] == $arrayResultFinalizados)   //Se a soma de clientes for igual a soma dos finalizados significa que todos os clientes estao fechados.
          { 	
		  
		if(count($stmt->select("SELECT * FROM backup_teste WHERE telefone='$numero'")) > 0){           
		echo "Numero: $numero Duplicado na Base de Backup Teste";			
   		
		exit();
            }
            else{			
		$stmt->select("INSERT INTO backup_teste (data,hora,telefone,operadora,tipo) VALUES (CURDATE(),CURTIME(),'$numero','$operadora','$midia')");
		echo "Todos os clientes atingiram o limite - Numero: $numero Inserido na Base de Backup Teste<br>";	
		echo "Total de clientes: $sum_finalizado<br>";
		exit();
		}
	  }   
	   

//-----------SCRIPT DE INSERÇÃO NA BASE, QUANDO O LEAD É NOVO! ## CUIDADO AO MEXER AQUI ##----------------------------------------------------
//-----------###### ESTE SCRIPT DEFINE QUAL SERÁ O CLIENTE DA RODADA SEGUINDO A ORDEM DA CADENCIA ########------------------------------------


$query_inserir = $stmt->select("SELECT cliente,callback,cadencia,cadencia_desc FROM clientes WHERE finalizado = '0' AND rodada_finalizada = '0' ORDER BY cadencia desc LIMIT 1");
    
    $cliente_ativo = $query_inserir[0]['cliente'];        
    $callback_result = $query_inserir[0]['callback'];       
    $cadencia = $query_inserir[0]['cadencia'];        
    $cadencia_desc = $query_inserir[0]['cadencia_desc'];     

	$cadencia_desc = $cadencia_desc - 1;    //subtrai o contador cadencia.

$stmt->select("UPDATE clientes SET cadencia_desc = $cadencia_desc WHERE cliente = '$cliente_ativo'");

	if($cadencia_desc <= 0){
		$stmt->select("UPDATE clientes SET rodada_finalizada = 1,cadencia_desc = $cadencia WHERE cliente = '$cliente_ativo'"); 
                               }

$query_verificar_rodada = $stmt->select("SELECT rodada_finalizada FROM clientes WHERE finalizado = '0'");

	$arrayResultRodada = count($query_verificar_rodada);              //Soma a quantidade de clientes	
	$sum_rodada = $stmt->select("SELECT SUM(rodada_finalizada) FROM clientes WHERE finalizado = '0'");         //Soma as boleanas. 	
	$sum_rodadas = $sum_rodada[0]['SUM(rodada_finalizada)'];        // Pega o resultado da soma

		if($arrayResultRodada == $sum_rodadas){
			$stmt->select("UPDATE clientes SET rodada_finalizada = '0' WHERE finalizado = '0'");
						      }

//-------------------SCRIPT DO CLIENTE------------------------------------------------------------------------------------------------------
//---------------#### INSERINDO O LEAD NA DATABASE E ENVIANDO O CALLBACK PARA O CLIENTE ###### ---------------------------------------------
	$operadora = new Portabilidade();
	$operadora = $operadora->consultaOperadora($numero);

	$stmt->select("INSERT INTO base_master_teste (data,hora,telefone,operadora,duplicados,cliente,tipo,codigo) VALUES (CURDATE(),CURTIME(),'$numero','$operadora','NAO','$cliente_ativo','$midia','$codigo')");

        $callback = "$callback_result";           //URL que envia para o Callback
			
	//file_get_contents($callback);             //envia o callback

//---------------#### VERIFICANDO SALDOS E LIMITES DO CLIENTE ###### -----------------------------------------------------------------------



$arrayResultFinalizado_cliente_fetch = $stmt->select("SELECT finalizado,saldo,tarifa FROM clientes WHERE cliente = '$cliente_ativo'");      //Seleciona a boleana finalizado

	$saldo = $arrayResultFinalizado_cliente_fetch[0]['saldo'];  
	$tarifa = $arrayResultFinalizado_cliente_fetch[0]['tarifa'];    
	
		$novo_saldo = $saldo - $tarifa;

$stmt->select("UPDATE clientes SET saldo = '$novo_saldo' WHERE cliente = '$cliente_ativo'");     
    
	$arrayResultLimite = $stmt->select("SELECT limite FROM clientes WHERE cliente = '$cliente_ativo'");

		$limite_total = $arrayResultLimite[0]['limite'];          

		$abrir_limite = fopen("tags/$cliente_ativo", "r+");   //Lê o arquivo que armazena a variável contadora de limite do cliente
		$cont_limite = fgets($abrir_limite);                  //Armazena o resultado do fopen em uma variável
            
		if($cont_limite < $limite_total)             //Enquanto o contador for menor que o limite total insere o numero na base do cliente
                {                    
		$cont_limite++;                             //Soma + 1 ao contador de limite
	        fseek($abrir_limite,0);                     // volta o ponteiro do fopen ao inicio do arquivo (zera o arquivo)
	        fwrite($abrir_limite, $cont_limite);        // Escreve o novo limite no arquivo
	        fclose($abrir_limite);                      // fecha o arquivo
						
			echo "Limite Total de $cliente_ativo: $limite_total<br>";
                        echo "Limite de Contador: $cont_limite<br>";
			echo "Saldo: R$$novo_saldo<br>";
			echo "Tarifa: R$$tarifa<br>";
			echo "URL enviada";		
			exit();			
                }
		else
		{
$stmt->select("UPDATE clientes SET finalizado = 1 WHERE cliente = '$cliente_ativo'");   //Altera a boleana Finalizado para 1, ou seja, Bloqueia o cliente
			
                        echo "Limite Total de $cliente_ativo: $limite_total<br>";
                        echo "Limite de Contador: $cont_limite<br>";
			echo "Saldo: R$$novo_saldo<br>";
			echo "Tarifa: R$$tarifa<br>";
			echo "URL enviada";
			echo "######### Limite Atingido, Cliente: $cliente_ativo Bloqueado #######";
			
			exit();			
							
			}

}

//-------##### SE CASO O LEAD JÁ EXISTIR NA BASE MASTER #####--------------------------------------------------------------------------------

else
{   
	$operadora = new Portabilidade();
	$operadora = $operadora->consultaOperadora($numero);                  
$stmt->select("INSERT INTO base_master_teste (data,hora,telefone,operadora,duplicados,cliente,tipo,codigo) VALUES (CURDATE(),CURTIME(),'$numero','$operadora','SIM','$cliente','$midia','$codigo')");
	
	echo "O Numero ja existe na Base<br>";
	echo "Telefone: $numero<br>";
    echo "Operadora: $operadora<br>";
    echo "Cliente: $cliente<br>";
    exit();
}

}}


 ?>