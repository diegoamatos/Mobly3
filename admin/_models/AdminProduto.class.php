<?php

/**
 * AdminProduto.class [ MODEL ADMIN ]
 * Respnsável por gerenciar os produtos no Admin do sistema!
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class AdminProduto {

    private $Data;
    private $Produto;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados
    const Entity = 'mb_produtos';

    /**
     * <b>Cadastrar o Produto:</b> Envelope os dados do produto em um array atribuitivo e execute esse método
     * para cadastrar o produto. Envia a capa automaticamente!
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao cadastrar: Para criar um produto, favor preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();
            $this->setName();

            if ($this->Data['produto_capa']):
                $uplaod = new Upload;
                $uplaod->Image($this->Data['produto_capa'], $this->Data['produto_nome']);
            endif;

            if (isset($uplaod) && $uplaod->getResult()):
                $this->Data['produto_capa'] = $uplaod->getResult();
                $this->Create();
            else:
                $this->Data['produto_capa'] = null;
                $this->Create();
            endif;
        endif;
    }

    /**
     * <b>Atualizar Produto:</b> Envelope os dados em uma array atribuitivo e informe o id de um 
     * produto para atualiza-lo na tabela!
     * @param INT $ProdutoId = Id do produto
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($ProdutoId, array $Data) {
        $this->Produto = (int) $ProdutoId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Error = ["Para atualizar este produto, preencha todos os campos ( Capa não precisa ser enviada! )", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();
            $this->setName();

            if (is_array($this->Data['produto_capa'])):
                $readCapa = new Read;
                $readCapa->ExeRead(self::Entity, "WHERE produto_id = :produto", "produto={$this->Produto}");
                $capa = '../uploads/' . $readCapa->getResult()[0]['produto_capa'];
                if (file_exists($capa) && !is_dir($capa)):
                    unlink($capa);
                endif;

                $uploadCapa = new Upload;
                $uploadCapa->Image($this->Data['produto_capa'], $this->Data['produto_nome']);
            endif;

            if (isset($uploadCapa) && $uploadCapa->getResult()):
                $this->Data['produto_capa'] = $uploadCapa->getResult();
                $this->Update();
            else:
                unset($this->Data['produto_capa']);
                $this->Update();
            endif;
        endif;
    }

    /**
     * <b>Deleta Produto:</b> Informe o ID do produto a ser removido para que esse método realize uma checagem de
     * pastas e galerias excluinto todos os dados nessesários!
     * @param INT $ProdutoId = Id do produto
     */
    public function ExeDelete($ProdutoId) {
        $this->Produto = (int) $ProdutoId;

        $ReadProduto = new Read;
        $ReadProduto->ExeRead(self::Entity, "WHERE produto_id = :produto", "produto={$this->Produto}");

        if (!$ReadProduto->getResult()):
            $this->Error = ["O produto que você tentou deletar não existe no sistema!", WS_ERROR];
            $this->Result = false;
        else:
            $ProdutoDelete = $ReadProduto->getResult()[0];
            if (file_exists('../uploads/' . $ProdutoDelete['produto_capa']) && !is_dir('../uploads/' . $ProdutoDelete['produto_capa'])):
                unlink('../uploads/' . $ProdutoDelete['produto_capa']);
            endif;

            $readGaleria = new Read;
            $readGaleria->ExeRead("mb_produtos_galerias", "WHERE produto_id = :id", "id={$this->Produto}");
            if ($readGaleria->getResult()):
                foreach ($readGaleria->getResult() as $gbdel):
                    if (file_exists('../uploads/' . $gbdel['galeria_imagem']) && !is_dir('../uploads/' . $gbdel['galeria_imagem'])):
                        unlink('../uploads/' . $gbdel['galeria_imagem']);
                    endif;
                endforeach;
            endif;

            $deleta = new Delete;
            $deleta->ExeDelete("mb_produtos_galerias", "WHERE produto_id = :gbproduto", "gbproduto={$this->Produto}");
            $deleta->ExeDelete(self::Entity, "WHERE produto_id = :produtoid", "produtoid={$this->Produto}");

            $this->Error = ["O produto <b>{$ProdutoDelete['produto_titulo']}</b> foi removido com sucesso do sistema!", WS_ACCEPT];
            $this->Result = true;

        endif;
    }

    /**
     * <b>Ativa/Inativa Produto:</b> Informe o ID do produto e o status e um status sendo 1 para ativo e 0 para
     * rascunho. Esse méto ativa e inativa os produtos!
     * @param INT $ProdutoId = Id do produto
     * @param STRING $ProdutoStatus = 1 para ativo, 0 para inativo
     */
    public function ExeStatus($ProdutoId, $ProdutoStatus) {
        $this->Produto = (int) $ProdutoId;
        $this->Data['produto_status'] = (string) $ProdutoStatus;
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE produto_id = :id", "id={$this->Produto}");
    }

    /**
     * <b>Enviar Galeria:</b> Envelope um $_FILES de um input multiple e envie junto a um produtoID para executar
     * o upload e o cadastro de galerias do artigo!
     * @param ARRAY $Files = Envie um $_FILES multiple
     * @param INT $ProdutoId = Informe o ID do produto
     */
    public function gbSend(array $Images, $ProdutoId) {
        $this->Produto = (int) $ProdutoId;
        $this->Data = $Images;

        $ImageNome = new Read;
        $ImageNome->ExeRead(self::Entity, "WHERE produto_id = :id", "id={$this->Produto}");

        if (!$ImageNome->getResult()):
            $this->Error = ["Erro ao enviar galeria. O índice {$this->Produto} não foi encontrado no banco!", WS_ERROR];
            $this->Result = false;
        else:
            $ImageNome = $ImageNome->getResult()[0]['produto_nome'];

            $gbFiles = array();
            $gbCount = count($this->Data['tmp_name']);
            $gbKeys = array_keys($this->Data);

            for ($gb = 0; $gb < $gbCount; $gb++):
                foreach ($gbKeys as $Keys):
                    $gbFiles[$gb][$Keys] = $this->Data[$Keys][$gb];
                endforeach;
            endfor;

            $gbSend = new Upload;
            $i = 0;
            $u = 0;

            foreach ($gbFiles as $gbUpload):
                $i++;
                $ImgNome = "{$ImageNome}-gb-{$this->Produto}-" . (substr(md5(time() + $i), 0, 5));
                $gbSend->Image($gbUpload, $ImgNome);

                if ($gbSend->getResult()):
                    $gbImage = $gbSend->getResult();
                    $gbCreate = ['produto_id' => $this->Produto, "galeria_imagem" => $gbImage, "galeria_data" => date('Y-m-d H:i:s')];
                    $insertGb = new Create;
                    $insertGb->ExeCreate("mb_produtos_galerias", $gbCreate);
                    $u++;
                endif;

            endforeach;

            if ($u > 1):
                $this->Error = ["Galeria Atualizada: Foram enviadas {$u} imagens para galeria deste produto!", WS_ACCEPT];
                $this->Result = true;
            endif;
        endif;
    }

    /**
     * <b>Deletar Imagem da galeria:</b> Informe apenas o id da imagem na galeria para que esse método leia e remova
     * a imagem da pasta e delete o registro do banco!
     * @param INT $GbImageId = Id da imagem da galleria
     */
    public function gbRemove($GbImageId) {
        $this->Produto = (int) $GbImageId;
        $readGb = new Read;
        $readGb->ExeRead("mb_produtos_galerias", "WHERE galeria_id = :gb", "gb={$this->Produto}");
        if ($readGb->getResult()):

            $Imagem = '../uploads/' . $readGb->getResult()[0]['galeria_imagem'];

            if (file_exists($Imagem) && !is_dir($Imagem)):
                unlink($Imagem);
            endif;

            $Deleta = new Delete;
            $Deleta->ExeDelete("mb_produtos_galerias", "WHERE galeria_id = :id", "id={$this->Produto}");
            if ($Deleta->getResult()):
                $this->Error = ["A imagem foi removida com sucesso da galeria!", WS_ACCEPT];
                $this->Result = true;
            endif;

        endif;
    }

    /**
     * <b>Verificar Cadastro:</b> Retorna ID do registro se o cadastro for efetuado ou FALSE se não.
     * Para verificar erros execute um getError();
     * @return BOOL $Var = InsertID or False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com uma mensagem e o tipo de erro.
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
        $Capa = $this->Data['produto_capa'];
        $Conteudo = $this->Data['produto_conteudo'];
        unset($this->Data['produto_capa'], $this->Data['produto_conteudo']);
		
		$this->Data['produto_caract'] = Check::ArrayString($this->Data['produto_caract']);
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        $this->Data['produto_nome'] = Check::Name($this->Data['produto_titulo']);
        $this->Data['produto_data'] = Check::Data($this->Data['produto_data']);

        $this->Data['produto_tipo'] = 'produto';
        $this->Data['produto_capa'] = $Capa;
        $this->Data['produto_conteudo'] = $Conteudo;
        $this->Data['produto_cat_pai'] = $this->getCatParent();
    }

    //Obtem o ID da categoria PAI
    private function getCatParent() {
        $rCat = new Read;
        $rCat->ExeRead("mb_categorias", "WHERE categoria_id = :id", "id={$this->Data['produto_categoria']}");
        if ($rCat->getResult()):
            return $rCat->getResult()[0]['categoria_pai'];
        else:
            return null;
        endif;
    }

    //Verifica o NOME produto. Se existir adiciona um pós-fix -Count
    private function setName() {
        $Where = (isset($this->Produto) ? "produto_id != {$this->Produto} AND" : '');
        $readNome = new Read;
        $readNome->ExeRead(self::Entity, "WHERE {$Where} produto_titulo = :t", "t={$this->Data['produto_titulo']}");
        if ($readNome->getResult()):
            $this->Data['produto_nome'] = $this->Data['produto_nome'] . '-' . $readNome->getRowCount();
        endif;
    }

    //Cadastra o produto no banco!
    private function Create() {
        $cadastra = new Create;
        $cadastra->ExeCreate(self::Entity, $this->Data);
        if ($cadastra->getResult()):
            $this->Error = ["O produto {$this->Data['produto_titulo']} foi cadastrado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = $cadastra->getResult();
        endif;
    }

    //Atualiza o produto no banco!
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE produto_id = :id", "id={$this->Produto}");
        if ($Update->getResult()):
            $this->Error = ["O produto <b>{$this->Data['produto_titulo']}</b> foi atualizado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

}
