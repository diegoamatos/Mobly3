<?php

/**
 * User.class [ MODEL ]
 * Respnsável por gerenciar os usuários no sistema!
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class User {

    private $Data;
    private $User;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados
    const Entity = 'mb_usuario';

    /**
     * <b>Cadastrar Usuário:</b> Envelope os dados de um usuário em um array atribuitivo e execute esse método
     * para cadastrar o mesmo no sistema. Validações serão feitas!
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;
        $this->checkData();

        if ($this->Result):
            $this->Create();
        endif;
    }
	
	public function ExeCreatePedido(array $Data) {
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Existem campos em branco. Favor preencha todos os campos!", WS_ALERT];
            $this->Result = false;
		else:
			$this->Result = true;
		endif;

        if ($this->Result):
            $this->CreatePedido();
        endif;
    }

    /**
     * <b>Atualizar Usuário:</b> Envelope os dados em uma array atribuitivo e informe o id de um
     * usuário para atualiza-lo no sistema!
     * @param INT $UserId = Id do usuário
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($UserId, array $Data) {
        $this->User = (int) $UserId;
        $this->Data = $Data;

        if (!$this->Data['senha_usuario']):
            unset($this->Data['senha_usuario']);
        endif;

        $this->checkData();

        if ($this->Result):
            $this->Update();
        endif;
    }

    /**
     * <b>Remover Usuário:</b> Informe o ID do usuário que deseja remover. Este método não permite deletar
     * o próprio perfil ou ainda remover todos os ADMIN'S do sistema!
     * @param INT $UserId = Id do usuário
     */
    public function ExeDelete($UserId) {
        $this->User = (int) $UserId;

        $readUser = new Read;
        $readUser->ExeRead(self::Entity, "WHERE id_usuario = :id", "id={$this->User}");

        if (!$readUser->getResult()):
            $this->Error = ['Oppsss, você tentou remover um usuário que não existe no sistema!', WS_ERROR];
            $this->Result = false;
        elseif ($this->User == $_SESSION['userlogin']['id_usuario']):
            $this->Error = ['Oppsss, você tentou remover seu usuário. Essa ação não é permitida!!!', WS_INFOR];
            $this->Result = false;
        else:
            if ($readUser->getResult()[0]['nivel_usuario'] == 3):

                $readAdmin = $readUser;
                $readAdmin->ExeRead(self::Entity, "WHERE id_usuario != :id AND nivel_usuario = :lv", "id={$this->User}&lv=3");

                if (!$readAdmin->getRowCount()):
                    $this->Error = ['Oppsss, você está tentando remover o único ADMIN do sistema. Para remover cadastre outro antes!!!', WS_ERROR];
                    $this->Result = false;
                else:
                    $this->Delete();
                endif;

            else:
                $this->Delete();
            endif;

        endif;
    }

    /**
     * <b>Verificar Cadastro:</b> Retorna TRUE se o cadastro ou update for efetuado ou FALSE se não.
     * Para verificar erros execute um getError();
     * @return BOOL $Var = True or False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com um erro e um tipo.
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

    //Verifica os dados digitados no formulário
    private function checkData() {
        if (in_array('', $this->Data)):
            $this->Error = ["Existem campos em branco. Favor preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        elseif (!Check::Email($this->Data['email_usuario'])):
            $this->Error = ["O e-email informado não parece ter um formato válido!", WS_ALERT];
            $this->Result = false;
        elseif (isset($this->Data['senha_usuario']) && (strlen($this->Data['senha_usuario']) < 6 || strlen($this->Data['senha_usuario']) > 12)):
            $this->Error = ["A senha deve ter entre 6 e 12 caracteres!", WS_INFOR];
            $this->Result = false;
        else:
            $this->checkEmail();
        endif;
    }

    //Verifica usuário pelo e-mail, Impede cadastro duplicado!
    private function checkEmail() {
        $Where = ( isset($this->User) ? "id_usuario != {$this->User} AND" : '');

        $readUser = new Read;
        $readUser->ExeRead(self::Entity, "WHERE {$Where} email_usuario = :email", "email={$this->Data['email_usuario']}");

        if ($readUser->getRowCount()):
            $this->Error = ["O e-email informado foi cadastrado no sistema por outro usuário! Informe outro e-mail!", WS_ERROR];
            $this->Result = false;
        else:
            $this->Result = true;
        endif;
    }

	//Cadasrtra Usuário!
    private function CreatePedido() {
        $Create = new Create;

        $Create->ExeCreate("mb_pedidos", $this->Data);

        if ($Create->getResult()):
            $this->Result = true;
        endif;
    }
	
    //Cadasrtra Usuário!
    private function Create() {
        $Create = new Create;
        $this->Data['registrado_usuario'] = date('Y-m-d H:i:s');
        $this->Data['senha_usuario'] = md5($this->Data['senha_usuario']);

        $Create->ExeCreate(self::Entity, $this->Data);

        if ($Create->getResult()):
            $this->Error = ["O usuário <b>{$this->Data['nome_usuario']}</b> foi cadastrado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = $Create->getResult();
        endif;
    }

    //Atualiza Usuário!
    private function Update() {
        $Update = new Update;
        if (isset($this->Data['senha_usuario'])):
            $this->Data['senha_usuario'] = md5($this->Data['senha_usuario']);
        endif;

        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE id_usuario = :id", "id={$this->User}");
        if ($Update->getResult()):
            $this->Error = ["O usuário <b>{$this->Data['nome_usuario']}</b> foi atualizado com sucesso!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

    //Remove Usuário
    private function Delete() {
        $Delete = new Delete;
        $Delete->ExeDelete(self::Entity, "WHERE id_usuario = :id", "id={$this->User}");
        if ($Delete->getResult()):
            $this->Error = ["Usuário removido com sucesso do sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

}
