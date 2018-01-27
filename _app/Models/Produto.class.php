<?php

/**
 * Produto [ MODEL ]
 * Classe de apoio para o modelo PRODUTO.
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class Produto {

    private $id;
    private $nome;
	private $preco;
    private $descricao;
    private $quantidade;
	
    public function Produto($id, $nome, $preco, $descricao=null, $quantidade=1){
        $this->id = $id;
        $this->nome = $nome;
		$this->preco = $preco;
        $this->descricao = $descricao;
        $this->quantidade = $quantidade;
    }
	
    public function getQuantidade() {
        return $this->quantidade;
    }
	
    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }
	
    public function getId() {
        return $this->id;
    }
	
    public function getNome() {
        return $this->nome;
    }
	
	public function getPreco() {
        return $this->preco;
    }
	
    public function getDescricao() {
        return $this->descricao;
    }
	
    public function setId($id) {
        $this->id = $id;
    }
	
    public function setNome($nome) {
        $this->nome = $nome;
    }
	
	public function setPreco($preco) {
        $this->preco = $preco;
    }
	
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

}
