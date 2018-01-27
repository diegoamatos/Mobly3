<div class="content form_create">

    <article>

        <header>
            <h1>Criar Características:</h1>
        </header>

        <?php
        $caracteristica = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($caracteristica) && $caracteristica['SendPostForm']):
            unset($caracteristica['SendPostForm']);

            require('_models/AdminCaract.class.php');
            $cadastra = new AdminCaract;
            $cadastra->ExeCreate($caracteristica);

            if ($cadastra->getResult()):
                header('Location: painel.php?exe=caracteristicas/update&create=true&caractid=' . $cadastra->getResult());
            else:
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            endif;
        endif;
        ?>


        <form name="CaractForm" action="" method="post" enctype="multipart/form-data">

            <div class="label_line">
				
				<label class="label_small">
					<span class="field">Característica Nome:</span>
					<input type="text" name="caract_nome" value="<?php if (isset($caracteristica['caract_nome'])) echo $caracteristica['caract_nome']; ?>" />
				</label>

            </div><!--/line-->
			
			<label class="label">
                <span class="field">Característica Conteúdo:</span>
                <textarea name="caract_valor" rows="5"><?php if (isset($caracteristica['caract_valor'])) echo htmlspecialchars($caracteristica['caract_valor']); ?></textarea>
            </label>

            <input type="submit" class="btn blue" value="Criar" name="SendPostForm" />

        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->