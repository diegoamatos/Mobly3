<div class="content form_create">

    <article>

        <h1>Atualizar Usuário!</h1>

        <?php
        $ClienteData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $userId = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT);

        if ($ClienteData && $ClienteData['SendPostForm']):
            unset($ClienteData['SendPostForm']);

            require('_models/AdminUser.class.php');
            $cadastra = new AdminUser;
            $cadastra->ExeUpdate($userId, $ClienteData);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $ReadUser = new Read;
            $ReadUser->ExeRead("mb_usuario", "WHERE id_usuario = :userid", "userid={$userId}");
            if (!$ReadUser->getResult()):

            else:
                $ClienteData = $ReadUser->getResult()[0];
                unset($ClienteData['senha_usuario']);
            endif;
        endif;

        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if ($checkCreate && empty($cadastra)):
            WSErro("O usuário <b>{$ClienteData['nome_usuario']}</b> foi cadastrado com sucesso no sistema!", WS_ACCEPT);
        endif;
        ?>

        <form action = "" method = "post" name = "UserCreateForm">
            <label class="label">
                <span class="field">Nome:</span>
                <input
                    type = "text"
                    name = "nome_usuario"
                    value="<?php if (!empty($ClienteData['nome_usuario'])) echo $ClienteData['nome_usuario']; ?>"
                    title = "Informe seu primeiro nome"
                    required
                    />
            </label>

            <label class="label">
                <span class="field">Sobrenome:</span>
                <input
                    type = "text"
                    name = "ultimo_usuario"
                    value="<?php if (!empty($ClienteData['ultimo_usuario'])) echo $ClienteData['ultimo_usuario']; ?>"
                    title = "Informe seu sobrenome"
                    required
                    />
            </label>

            <label class="label">
                <span class="field">E-mail:</span>
                <input
                    type = "email"
                    name = "email_usuario"
                    value="<?php if (!empty($ClienteData['email_usuario'])) echo $ClienteData['email_usuario']; ?>"
                    title = "Informe seu e-mail"
                    required
                    />
            </label>

            <div class="label_line">
                <label class="label_medium">
                    <span class="field">Senha:</span>
                    <input
                        type = "password"
                        name = "senha_usuario"
                        value="<?php if (!empty($ClienteData['senha_usuario'])) echo $ClienteData['senha_usuario']; ?>"
                        title = "Informe sua senha [ de 6 a 12 caracteres! ]"
                        pattern = ".{6,12}"
                        />
                </label>


                <label class="label_medium">
                    <span class="field">Nível:</span>
                    <select name = "nivel_usuario" title = "Selecione o nível de usuário" required >
                        <option value = "">Selecione o Nível</option>
                        <option value = "1" <?php if (isset($ClienteData['nivel_usuario']) && $ClienteData['nivel_usuario'] == 1) echo 'selected="selected"'; ?>>Cliente</option>
                        <option value="2" <?php if (isset($ClienteData['nivel_usuario']) && $ClienteData['nivel_usuario'] == 2) echo 'selected="selected"'; ?>>Vendedor</option>
                        <option value="3" <?php if (isset($ClienteData['nivel_usuario']) && $ClienteData['nivel_usuario'] == 3) echo 'selected="selected"'; ?>>Admin</option>
                    </select>
                </label>
            </div><!-- LABEL LINE -->

            <input type="submit" name="SendPostForm" value="Atualizar Usuário" class="btn blue" />
        </form>

    </article>
    <div class="clear"></div>
</div> <!-- content home -->