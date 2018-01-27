<?php

/**
 * AdminCaract.class [ MODEL ADMIN ]
 * Responável por gerenciar as características do sistema no admin!
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class AdminCaract {

    private $Data;
    private $CaractId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'mb_caracteristicas';

    /**
     * <b>Criar Característica:</b> Envelope nome, conteúdo e sessão em um array atribuitivo e execute esse método
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ['<b>Erro ao criar:</b> Para criar uma característica, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();
            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar Característica:</b> Envelope os dados em uma array atribuitivo e informe o id de uma
     * característica para atualiza-la!
     * @param INT $CaractId = Id da característica
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($CaractId, array $Data) {
        $this->CaractId = (int) $CaractId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar a característica {$this->Data['caract_nome']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta Característica:</b> Informe o ID de uma característica para remove-la do sistema. Esse método verifica
     * o tipo de característica e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $CaractId = Id da característica
     */
    public function ExeDelete($CaractId) {
        $this->CaractId = (int) $CaractId;

        $read = new Read;
        $read->ExeRead(self::Entity, "WHERE caract_id = :delid", "delid={$this->CaractId}");

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover uma característica que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$this->checkProdutos()):
                $this->Result = false;
                $this->Error = ["A <b>característica {$caract_nome}</b> estão sendo usado por algum produto. Para deletar, antes altere ou remova todas as característica destes produtos!", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE caract_id = :deletaid", "deletaid={$this->CaractId}");

                $this->Result = true;
                $this->Error = ["A <b>característica {$caract_nome}</b> foi removida com sucesso do sistema!", WS_ACCEPT];
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
        $this->Data['caract_nome'] = ucfirst($this->Data['caract_nome']);
    }


    //Verifica produtos que utiliza a característica
    private function checkProdutos() {
        $readProdutos = new Read;
        $readProdutos->ExeRead("mb_produtos", "WHERE produto_caract = :caract", "caract={$this->CaractId}");
        if ($readProdutos->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra a característica no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> A característica {$this->Data['caract_nome']} foi cadastrada no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza Característica
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE caract_id = :caractid", "caractid={$this->CaractId}");
        if ($Update->getResult()):
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> A característica {$this->Data['caract_nome']} foi atualizada no sistema!", WS_ACCEPT];
        endif;
    }

}
