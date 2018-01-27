<?php

/**
 * AdminCategoria.class [ MODEL ADMIN ]
 * Responável por gerenciar as categorias do sistema no admin!
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class AdminCategoria {

    private $Data;
    private $CatId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'mb_categorias';

    /**
     * <b>Cadastrar Categoria:</b> Envelope titulo, descrição, data e sessão em um array atribuitivo e execute esse método
     * para cadastrar a categoria. Case seja uma sessão, envie o categoria_pai como STRING null.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar uma categoria, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();
            $this->setName();
            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar Categoria:</b> Envelope os dados em uma array atribuitivo e informe o id de uma
     * categoria para atualiza-la!
     * @param INT $CategoriaId = Id da categoria
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($CategoriaId, array $Data) {
        $this->CatId = (int) $CategoriaId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar a categoria {$this->Data['categoria_titulo']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->setName();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta categoria:</b> Informe o ID de uma categoria para remove-la do sistema. Esse método verifica
     * o tipo de categoria e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $CategoriaId = Id da categoria
     */
    public function ExeDelete($CategoriaId) {
        $this->CatId = (int) $CategoriaId;

        $read = new Read;
        $read->ExeRead(self::Entity, "WHERE categoria_id = :delid", "delid={$this->CatId}");

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover uma categoria que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$categoria_pai && !$this->checkCats()):
                $this->Result = false;
                $this->Error = ["A <b>seção {$categoria_titulo}</b> possui categorias cadastradas. Para deletar, antes altere ou remova as categorias filhas!", WS_ALERT];
            elseif ($categoria_pai && !$this->checkPosts()):
                $this->Result = false;
                $this->Error = ["A <b>categoria {$categoria_titulo}</b> possui artigos cadastrados. Para deletar, antes altere ou remova todos os produtos desta categoria!", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE categoria_id = :deletaid", "deletaid={$this->CatId}");

                $tipo = ( empty($categoria_pai) ? 'seção' : 'categoria' );
                $this->Result = true;
                $this->Error = ["A <b>{$tipo} {$categoria_titulo}</b> foi removida com sucesso do sistema!", WS_ACCEPT];
            endif;
        endif;
    }

    /**
     * <b>Verificar Cadastro:</b> Retorna TRUE se o cadastro ou update for efetuado ou FALSE se não. Para verificar
     * erros execute um getError();
     * @return BOOL $Var = True or False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com a mensagem e o tipo de erro!
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError() {
        return $this->Error;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Valida e cria os dados para realizar o cadastro
    private function setData() {
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
        $this->Data['categoria_nome'] = Check::Name($this->Data['categoria_titulo']);
        $this->Data['categoria_cadastro'] = Check::Data($this->Data['categoria_cadastro']);
        $this->Data['categoria_pai'] = ($this->Data['categoria_pai'] == 'null' ? null : $this->Data['categoria_pai']);
    }

    //Verifica o NOME da categoria. Se existir adiciona um pós-fix +1
    private function setName() {
        $Where = (!empty($this->CatId) ? "categoria_id != {$this->CatId} AND" : '' );

        $readNome = new Read;
        $readNome->ExeRead(self::Entity, "WHERE {$Where} categoria_titulo = :t", "t={$this->Data['categoria_titulo']}");
        if ($readNome->getResult()):
            $this->Data['categoria_nome'] = $this->Data['categoria_nome'] . '-' . $readNome->getRowCount();
        endif;
    }

    //Verifica categorias da seção
    private function checkCats() {
        $readSes = new Read;
        $readSes->ExeRead(self::Entity, "WHERE categoria_pai = :pai", "pai={$this->CatId}");
        if ($readSes->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica artigos da categoria
    private function checkPosts() {
        $readProdutos = new Read;
        $readProdutos->ExeRead("mb_produtos", "WHERE produto_categoria = :categoria", "categoria={$this->CatId}");
        if ($readProdutos->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra a categoria no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> A categoria {$this->Data['categoria_titulo']} foi cadastrada no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza Categoria
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE categoria_id = :catid", "catid={$this->CatId}");
        if ($Update->getResult()):
            $tipo = ( empty($this->Data['categoria_pai']) ? 'seção' : 'categoria' );
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> A {$tipo} {$this->Data['categoria_titulo']} foi atualizada no sistema!", WS_ACCEPT];
        endif;
    }

}
