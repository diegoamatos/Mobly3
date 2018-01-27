<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Categoria:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $catid = filter_input(INPUT_GET, 'catid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminCategoria.class.php');
            $cadastra = new AdminCategoria;
            $cadastra->ExeUpdate($catid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $read = new Read;
            $read->ExeRead("mb_categorias", "WHERE categoria_id = :id", "id={$catid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=categorias/index&empty=true');
            else:
                $data = $read->getResult()[0];
            endif;
        endif;
        
        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if($checkCreate && empty($cadastra)):
            $tipo = ( empty($data['categoria_pai']) ? 'seção' : 'categoria');
            WSErro("A {$tipo} <b>{$data['categoria_titulo']}</b> foi cadastrada com sucesso no sistema! Continue atualizando a mesma!", WS_ACCEPT);
        endif;
        
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">


            <label class="label">
                <span class="field">Titulo:</span>
                <input type="text" name="categoria_titulo" value="<?php if (isset($data)) echo $data['categoria_titulo']; ?>" />
            </label>

            <label class="label">
                <span class="field">Conteúdo:</span>
                <textarea name="categoria_conteudo" rows="5"><?php if (isset($data)) echo $data['categoria_conteudo']; ?></textarea>
            </label>

            <div class="label_line">

                <label class="label_small">
                    <span class="field">Data:</span>
                    <input type="text" class="formDate center" name="categoria_cadastro" value="<?= date('d/m/Y H:i:s'); ?>" />
                </label>

                <label class="label_small left">
                    <span class="field">Seção:</span>
                    <select name="categoria_pai">
                        <option value="null"> Selecione a Seção: </option>
                        <?php
                        $readSes = new Read;
                        $readSes->ExeRead("mb_categorias", "WHERE categoria_pai IS NULL ORDER BY categoria_titulo ASC");
                        if (!$readSes->getResult()):
                            echo '<option disabled="disabled" value="null"> Cadastre antes uma seção! </option>';
                        else:
                            foreach ($readSes->getResult() as $ses):
                                echo "<option value=\"{$ses['categoria_id']}\" ";

                                if ($ses['categoria_id'] == $data['categoria_pai']):
                                    echo ' selected="selected" ';
                                endif;

                                echo "> {$ses['categoria_titulo']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>
            </div>

            <div class="gbform"></div>

            <input type="submit" class="btn blue" value="Atualizar Categoria" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->