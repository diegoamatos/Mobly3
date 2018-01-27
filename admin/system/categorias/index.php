<div class="content cat_list">

    <section>

        <h1>Categorias:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Você tentou editar uma categoria que não existe no sistema!", WS_INFOR);
        endif;

        $delCat = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delCat):
            require ('_models/AdminCategoria.class.php');
            $deletar = new AdminCategoria;
            $deletar->ExeDelete($delCat);
            
            WSErro($deletar->getError()[0], $deletar->getError()[1]);
        endif;


        $readSes = new Read;
        $readSes->ExeRead("mb_categorias", "WHERE categoria_pai IS NULL ORDER BY categoria_titulo ASC");
        if (!$readSes->getResult()):

        else:
            foreach ($readSes->getResult() as $ses):
                extract($ses);

                $readProdutos = new Read;
                $readProdutos->ExeRead("mb_produtos", "WHERE produto_cat_pai = :pai", "pai={$categoria_id}");

                $readCats = new Read;
                $readCats->ExeRead("mb_categorias", "WHERE categoria_pai = :pai", "pai={$categoria_id}");

                $countSesProdutos = $readProdutos->getRowCount();
                $countSesCats = $readCats->getRowCount();
                ?>
                <section>

                    <header>
                        <h1><?= $categoria_titulo; ?>  <span>( <?= $countSesProdutos; ?> produtos ) ( <?= $countSesCats; ?> Categorias )</span></h1>
                        <p class="tagline"><?= $categoria_conteudo; ?></p>

                        <ul class="info produto_actions">
                            <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($categoria_cadastro)); ?>Hs</li>
                            <li><a class="act_view" target="_blank" href="../categoria/<?= $categoria_nome; ?>" title="Ver no site">Ver no site</a></li>
                            <li><a class="act_edit" href="painel.php?exe=categorias/update&catid=<?= $categoria_id; ?>" title="Editar">Editar</a></li>
                            <li><a class="act_delete" href="painel.php?exe=categorias/index&delete=<?= $categoria_id; ?>" title="Excluir">Deletar</a></li>
                        </ul>
                    </header>

                    <h2>Sub categorias:</h2>

                    <?php
                    $readSub = new Read;
                    $readSub->ExeRead("mb_categorias", "WHERE categoria_pai = :subpai", "subpai={$categoria_id}");
                    if (!$readSub->getResult()):

                    else:
                        $a = 0;
                        foreach ($readSub->getResult() as $sub):
                            $a++;

                            $readCatProdutos = new Read;
                            $readCatProdutos->ExeRead("mb_produtos", "WHERE produto_categoria = :categoriaid", "categoriaid={$sub['categoria_id']}");
                            ?>
                            <article<?php if ($a % 3 == 0) echo ' class="right"'; ?>>
                                <h1><a target="_blank" href="../categoria/<?= $sub['categoria_nome']; ?>" title="Ver Categoria"><?= $sub['categoria_titulo']; ?></a>  ( <?= $readCatProdutos->getRowCount(); ?> produtos )</h1>

                                <ul class="info produto_actions">
                                    <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($sub['categoria_cadastro'])); ?>Hs</li>
                                    <li><a class="act_view" target="_blank" href="../categoria/<?= $sub['categoria_nome']; ?>" title="Ver no site">Ver no site</a></li>
                                    <li><a class="act_edit" href="painel.php?exe=categorias/update&catid=<?= $sub['categoria_id']; ?>" title="Editar">Editar</a></li>
                                    <li><a class="act_delete" href="painel.php?exe=categorias/index&delete=<?= $sub['categoria_id']; ?>" title="Excluir">Deletar</a></li>
                                </ul>
                            </article>
                            <?php
                        endforeach;
                    endif;
                    ?>

                </section>
                <?php
            endforeach;
        endif;
        ?>

        <div class="clear"></div>
    </section>

    <div class="clear"></div>
</div> <!-- content home -->