<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Criar Características:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $caractid = filter_input(INPUT_GET, 'caractid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminCaract.class.php');
            $cadastra = new AdminCaract;
            $cadastra->ExeUpdate($caractid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $read = new Read;
            $read->ExeRead("mb_caracteristicas", "WHERE caract_id = :id", "id={$caractid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=caracteristicas/index&empty=true');
            else:
                $data = $read->getResult()[0];
            endif;
        endif;
        
        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if($checkCreate && empty($cadastra)):
            WSErro("A características <b>{$data['caract_nome']}</b> foi cadastrada com sucesso no sistema! Continue atualizando a mesma!", WS_ACCEPT);
        endif;
        
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">


            <div class="label_line">
				
				<label class="label_small">
					<span class="field">Característica Nome:</span>
					<input type="text" name="caract_nome" value="<?php if (isset($data)) echo $data['caract_nome']; ?>" />
				</label>
			</div>
			
            <label class="label">
                <span class="field">Característica Conteúdo:</span>
                <textarea name="caract_valor" rows="5"><?php if (isset($data)) echo $data['caract_valor']; ?></textarea>
            </label>

            <input type="submit" class="btn blue" value="Atualizar Característica" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->