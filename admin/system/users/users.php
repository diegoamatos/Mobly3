<div class="content form_create">

    <article>

        <h1>Usuários: <a href="painel.php?exe=users/create" title="Cadastrar Novo" class="user_cad">Cadastrar Usuário</a></h1>

        <?php
        $delete = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delete):
            require('_models/AdminUser.class.php');
            $delUser = new AdminUser;
            $delUser->ExeDelete($delete);
            WSErro($delUser->getError()[0], $delUser->getError()[1]);
        endif;
        ?>

        <ul class="ultable">
            <li class="t_title">
                <span class="ui center">Res:</span>
                <span class="un">Nome:</span>
                <span class="ue">E-mail:</span>
                <span class="ur center">Registro:</span>
                <span class="ua center">Atualização:</span>
                <span class="ul center">Nível:</span>
                <span class="ed center">-</span>
            </li>

            <?php
            $read = new Read;
            $read->ExeRead("mb_usuario", "ORDER BY nivel_usuario DESC, nome_usuario ASC");
            if ($read->getResult()):
                foreach ($read->getResult() as $user):
                    extract($user);
                    $atualizacao_usuario = ($atualizacao_usuario ? date('d/m/Y H:i', strtotime($atualizacao_usuario)) . ' hs' : '-');
                    $nivel = ['', 'Cliente', 'Vendedor', 'Admin'];
                    ?>            
                    <li>
                        <span class="ui center"><?= $id_usuario ?></span>
                        <span class="un"><?= $nome_usuario . ' ' . $ultimo_usuario; ?></span>
                        <span class="ue"><?= $email_usuario; ?></span>
                        <span class="ur center"><?= date('d/m/Y', strtotime($registrado_usuario)); ?></span>
                        <span class="ua center"><?= $atualizacao_usuario; ?></span>
                        <span class="ul center"><?= $nivel[$nivel_usuario]; ?></span>
                        <span class="ed center">
                            <a href="painel.php?exe=users/update&userid=<?= $id_usuario; ?>" title="Editar" class="action user_edit">Editar</a>
                            <a href="painel.php?exe=users/users&delete=<?= $id_usuario; ?>" onclick="return confirm('Tem certeza que deseja deletar este usuário?')" title="Deletar" class="action user_dele">Deletar</a>
                        </span>
                    </li>
                    <?php
                endforeach;
            endif;
            ?>

        </ul>


    </article>

    <div class="clear"></div>
</div> <!-- content home -->