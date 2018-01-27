<div class="content home">

    <aside>
        <h1 class="boxtitle">Estatísticas de Acesso:</h1>

        <article class="sitecontent boxaside">
            <h1 class="boxsubtitle">Conteúdo:</h1>

            <?php
            //OBJETO READ
            $read = new Read;

            //VISITAS DO SITE
            $read->FullRead("SELECT SUM(siteviews_views) AS views FROM mb_siteviews");
            $Views = $read->getResult()[0]['views'];

            //USUÁRIOS
            $read->FullRead("SELECT SUM(siteviews_users) AS users FROM mb_siteviews");
            $Users = $read->getResult()[0]['users'];

            //MÉDIA DE PAGEVIEWS
            $read->FullRead("SELECT SUM(siteviews_pages) AS pages FROM mb_siteviews");
            $ResPages = $read->getResult()[0]['pages'];
            $Pages = substr($ResPages / $Users, 0, 5);

            //PRODUTOS
            $read->ExeRead("mb_produtos");
            $Produtos = $read->getRowCount();

            //CATEGORIAS
            $read->ExeRead("mb_categorias");
            $Categorias = $read->getRowCount();
            ?>

            <ul>
                <li class="view"><span><?= $Views; ?></span> visitas</li>
                <li class="user"><span><?= $Users; ?></span> usuários</li>
                <li class="page"><span><?= $Pages; ?></span> pageviews</li>
                <li class="line"></li>
                <li class="post"><span><?= $Produtos; ?></span> Produtos</li>
                <li class="emp"><span><?= $Categorias; ?></span> Categorias</li>
            </ul>
            <div class="clear"></div>
        </article>

        <article class="useragent boxaside">
            <h1 class="boxsubtitle">Navegador:</h1>

            <?php
            //LE O TOTAL DE VISITAS DOS NAVEGADORES
            $read->FullRead("SELECT SUM(agent_views) AS TotalViews FROM mb_siteviews_agent");
            $TotalViews = $read->getResult()[0]['TotalViews'];

            $read->ExeRead("mb_siteviews_agent", "ORDER BY agent_views DESC LIMIT 3");
            if (!$read->getResult()):
                WSErro("Oppsss, Ainda não existem estatísticas de navegadores!", WS_INFOR);
            else:
                echo "<ul>";
                foreach ($read->getResult() as $nav):
                    extract($nav);

                    //REALIZA PORCENTAGEM DE VISITAS POR NAVEGADOR!
                    $percent = substr(( $agent_views / $TotalViews ) * 100, 0, 5);
                    ?>
                    <li>
                        <p><strong><?= $agent_name; ?>:</strong> <?= $percent; ?>%</p>
                        <span style="width: <?= $percent; ?>%"></span>
                        <p><?= $agent_views; ?> visitas</p>
                    </li>
                    <?php
                endforeach;
                echo "</ul>";
            endif;
            ?>

            <div class="clear"></div>
        </article>
    </aside>

    <section class="content_statistics">

        <h1 class="boxtitle">Publicações:</h1>

        <section>
            <h1 class="boxsubtitle">Artigos Recentes:</h1>

            <?php
            $read->ExeRead("mb_produtos", "ORDER BY produto_data DESC LIMIT 3");
            if ($read->getResult()):
                foreach ($read->getResult() as $re):
                    extract($re);
                    ?>
                    <article>

                        <div class="img thumb_small">
                            <?= Check::Image('../uploads/' . $produto_capa, $produto_titulo, 120, 70); ?>
                        </div>

                        <h1><a target="_blank" href="../produto/<?= $produto_nome; ?>" title="Ver Produto"><?= Check::Words($produto_titulo, 10) ?></a></h1>
                        <ul class="info produto_actions">
                            <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($produto_data)); ?>Hs</li>
                            <li><a class="act_view" target="_blank" href="../produto/<?= $produto_nome; ?>" title="Ver no site">Ver no site</a></li>
                            <li><a class="act_edit" href="painel.php?exe=produtos/update&produtoid=<?= $produto_id; ?>" title="Editar">Editar</a></li>

                            <?php if (!$produto_status): ?>
                                <li><a class="act_inative" href="painel.php?exe=produtos/index&produto=<?= $post_id; ?>&action=active" title="Ativar">Ativar</a></li>
                            <?php else: ?>
                                <li><a class="act_ative" href="painel.php?exe=produtos/index&produto=<?= $produto_id; ?>&action=inative" title="Inativar">Inativar</a></li>
                            <?php endif; ?>

                            <li><a class="act_delete" href="painel.php?exe=produtos/index&produto=<?= $produto_id; ?>&action=delete" title="Excluir">Deletar</a></li>
                        </ul>

                    </article>
                    <?php
                endforeach;
            endif;
            ?>
        </section>          


        <section>
            <h1 class="boxsubtitle">Artigos Mais Vistos:</h1>

            <?php
            $read->ExeRead("mb_produtos", "ORDER BY produto_visualizacoes DESC LIMIT 3");
            if ($read->getResult()):
                foreach ($read->getResult() as $re):
                    extract($re);
                    ?>
                    <article>

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
            endif;
            ?>
        </section>                           

    </section> <!-- Estatísticas -->

    <div class="clear"></div>
</div> <!-- content home -->