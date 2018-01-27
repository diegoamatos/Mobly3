<div class="content list_content">

    <section>

        <h1>Produtos:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou editar um produto que não existe no sistema!", WS_INFOR);
        endif;


        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminProduto.class.php');

            $produtoAction = filter_input(INPUT_GET, 'produto', FILTER_VALIDATE_INT);
            $produtoUpdate = new AdminProduto;

            switch ($action):
                case 'active':
                    $produtoUpdate->ExeStatus($produtoAction, '1');
                    WSErro("O status do produto foi atualizado para <b>ativo</b>. Produto publicado!", WS_ACCEPT);
                    break;

                case 'inative':
                    $produtoUpdate->ExeStatus($produtoAction, '0');
                    WSErro("O status do produto foi atualizado para <b>inativo</b>. Produto agora é um rascunho!", WS_ALERT);
                    break;

                case 'delete':
                    $produtoUpdate->ExeDelete($produtoAction);
                    WSErro($produtoUpdate->getError()[0], $produtoUpdate->getError()[1]);
                    break;

                default :
                    WSErro("Ação não foi identifica pelo sistema, favor utilize os botões!", WS_ALERT);
            endswitch;
        endif;


        $produtoi = 0;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=produtos/index&page=');
        $Pager->ExePager($getPage, 10);

        $readProdutos = new Read;
        $readProdutos->ExeRead("mb_produtos", "ORDER BY produto_status ASC, produto_data DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if ($readProdutos->getResult()):
            foreach ($readProdutos->getResult() as $produto):
                $produtoi++;
                extract($produto);
                $status = (!$produto_status ? 'style="background: #fffed8"' : '');
                ?>
                <article<?php if ($produtoi % 2 == 0) echo ' class="right"'; ?> <?= $status; ?>>

                    <div class="img thumb_small">
                        <?= Check::Image('../uploads/' . $produto_capa, $produto_titulo, 120, 70); ?>
                    </div>

                    <h1><a target="_blank" href="../produto/<?= $produto_nome; ?>" title="Ver Produto"><?= Check::Words($produto_titulo, 10) ?></a></h1>
                    <ul class="info produto_actions">
                        <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($produto_data)); ?>Hs</li>
                        <li><a class="act_view" target="_blank" href="../produto/<?= $produto_nome; ?>" title="Ver no site">Ver no site</a></li>
                        <li><a class="act_edit" href="painel.php?exe=produtos/update&produtoid=<?= $produto_id; ?>" title="Editar">Editar</a></li>

                        <?php if (!$produto_status): ?>
                            <li><a class="act_inative" href="painel.php?exe=produtos/index&produto=<?= $produto_id; ?>&action=active" title="Ativar">Ativar</a></li>
                        <?php else: ?>
                            <li><a class="act_ative" href="painel.php?exe=produtos/index&produto=<?= $produto_id; ?>&action=inative" title="Inativar">Inativar</a></li>
                        <?php endif; ?>

                        <li><a class="act_delete" href="painel.php?exe=produtos/index&produto=<?= $produto_id; ?>&action=delete" title="Excluir">Deletar</a></li>
                    </ul>

                </article>
                <?php
            endforeach;

        else:
            $Pager->ReturnPage();
            WSErro("Desculpe, ainda não existem produtos cadastrados!", WS_INFOR);
        endif;
        ?>

        <div class="clear"></div>
    </section>

    <?php
    $Pager->ExePaginator("mb_produtos");
    echo $Pager->getPaginator();
    ?>

    <div class="clear"></div>
</div> <!-- content home -->