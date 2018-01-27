<?php

/**
 * Carrinho [ MODEL ]
 * Classe de apoio para o modelo CARRINHO.
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class Carrinho {

    private $produto;
    public function Carrinho(){}
    
	//Adiciona um produto
    public function addProduto(Produto $m){
        $this->produto[] = $m;
		$_SESSION["produto"][$m->getId()] = $this->produto;
		//Cria array para manipular os dados no banco
		$ArrCreate = ['carrinho_cod' => $m->getId(), 'carrinho_nome' => $m->getNome(), 'carrinho_preco' => $m->getPreco(), 'carrinho_quantidade' => 1, 'carrinho_sessao' => $_SESSION['useronline']['online_session']];
		
		//Verifica se sessao existe se não existir cria uma sessão do array.
		if(empty($_SESSION["carrinho"][$m->getId()])){
			$_SESSION["carrinho"][$m->getId()] = $ArrCreate;
		}
		
		//Verifica o banco na tabela carrinho
		$ReadCarrinho = new Read;
		$ReadCarrinho->ExeRead("mb_carrinho", "WHERE carrinho_cod = :carid AND carrinho_sessao = :carsessao", "carid={$m->getId()}&carsessao={$_SESSION['useronline']['online_session']}");
		if (!$ReadCarrinho->getResult()){
			
			//Se não existe o produto no carrinho, cria um!
			$CriarCarr = new Create;
			$CriarCarr->ExeCreate("mb_carrinho", $ArrCreate);
			if ($CriarCarr->getResult()):
				$retorno = true;//$CriarCarr->getResult();
			endif;
		}else{
			
			//Se existe o produto no banco atualiza!
			$updateQuant = $_SESSION["carrinho"][$m->getId()]["carrinho_quantidade"];
			$ArrUpdate = ['carrinho_quantidade' => $_SESSION["carrinho"][$m->getId()]["carrinho_quantidade"] + 1];
			$Update = new Update();
			$Update->ExeUpdate("mb_carrinho", $ArrUpdate, "WHERE carrinho_cod = :carid", "carid={$m->getId()}");
			
			unset($_SESSION["carrinho"][$m->getId()]["carrinho_quantidade"]);
			
			$_SESSION["carrinho"][$m->getId()]["carrinho_quantidade"] = $updateQuant + 1;
			$retorno = true;
		}
		return $retorno;
    }
    
	// Recupera um produto pelo id
    public function getProduto($idProduto){
        foreach($this->produto as $pro){
            if($pro->getId() == $idProduto){
                return $pro;
            }
        }
    }
    
	// Remove um produto pelo id
    public function alterarProduto($codigo, $quantidade = NULL){
		if(($quantidade != NULL) && ($quantidade > 0)){
			$updateQuant = $_SESSION["carrinho"][$codigo]["carrinho_quantidade"];
			if($updateQuant > $quantidade){
				$ArrUpdate = ['carrinho_quantidade' => $quantidade];
				$Update = new Update();
				$Update->ExeUpdate("mb_carrinho", $ArrUpdate, "WHERE carrinho_cod = :carid", "carid={$codigo}");
				
				unset($_SESSION["carrinho"][$codigo]["carrinho_quantidade"]);
				
				$_SESSION["carrinho"][$codigo]["carrinho_quantidade"] = $quantidade;
				
			}else{
				$ArrUpdate = ['carrinho_quantidade' => $quantidade];
				$Update = new Update();
				$Update->ExeUpdate("mb_carrinho", $ArrUpdate, "WHERE carrinho_cod = :carid", "carid={$codigo}");
				
				unset($_SESSION["carrinho"][$codigo]["carrinho_quantidade"]);
				
				$_SESSION["carrinho"][$codigo]["carrinho_quantidade"] = $quantidade;
			}
			
		}else{
			for($i=0;$i < count($this->produto);$i++){
				echo $this->produto[$i]->getId();
					if($this->produto[$i]->getId() == $codigo){
						//unset($this->produto[$i]);
						//unset($_SESSION["carrinho"][$codigo]);
						//unset($_SESSION["produto"][$codigo]);
						//$deleta = new Delete;
						//$deleta->ExeDelete("mb_carrinho", "WHERE carrinho_sessao = :car_cod", "car_cod={$_SESSION['useronline']['online_session']}");
					}
				}
		}
    }
    
	// soma_carrinho
    public function somaCarrinho(){
		$soma_carrinho = 0;
		if(isset($_SESSION["carrinho"])){
			if(count($_SESSION["carrinho"]) > 0){
				foreach($_SESSION["carrinho"] as $key => $soma){
					$soma_carrinho += ($soma["carrinho_preco"]*$soma["carrinho_quantidade"]);
				}
			}
		}
		return $soma_carrinho;
    }
	
	// Remove um produto pelo codigo
    public function removeProduto($codigo){
		if(isset($_SESSION["carrinho"]["{$codigo}"])){
			$_SESSION["carrinho"]["{$codigo}"]["carrinho_sessao"];
			$this->produto = $_SESSION["produto"][$codigo];
			
			for($i=0;$i < count($this->produto);$i++){
			if($this->produto[$i]->getId() == $codigo){
			
				$Deleta = new Delete;
				$Deleta->ExeDelete("mb_carrinho", "WHERE carrinho_cod = :car_cod AND carrinho_sessao = :car_sess", "car_cod={$codigo}&car_sess={$_SESSION['useronline']['online_session']}");
				if ($Deleta->getResult()):
					unset($this->produto[$i]);
					unset($_SESSION["carrinho"][$codigo]);
					unset($_SESSION["produto"][$codigo]);
					if(count($this->produto) == 1){
						unset($_SESSION["carrinho"]);
					}
				endif;
			}
		}
		}
    }
	
	// lista todos os produtos
    public function listar(){
        foreach($this->produto as $pro){
            echo "<b>Código:</b> {$pro->getId()}<br/>
                  <b>Nome:</b> {$pro->getNome()}<br/>
                  <b>Descrição:</b> {$pro->getDescricao()}<br/>
                  --------------------<br/>";
        }
    }
}
