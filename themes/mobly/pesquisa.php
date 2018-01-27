<?php
$search = $Link->getLocal()[1];
$count = ($Link->getData()['count'] ? $Link->getData()['count'] : '0');
?>

<!--HOME CONTENT-->
<div class="site-container">

    <section class="page_categorias">
        <header class="cat_header">
            <h2>Pesquisa por: <?= $search; ?></h2>
            <p class="tagline">Sua pesquisa por <?= $search; ?> retornou <?= $count; ?> resultados!</p>
        </header>

        <?php
        $getPage = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1);
        $Pager = new Pager(HOME . '/pesquisa/' . $search . '/');
        $Pager->ExePager($getPage, 12);

        $readArt = new Read;
        $readArt->ExeRead("mb_produtos", "WHERE produto_status = 1 AND (produto_titulo LIKE '%' :link '%' OR produto_conteudo LIKE '%' :link '%') ORDER BY produto_data DESC LIMIT :limit OFFSET :offset", "link={$search}&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$readArt->getResult()):
            $Pager->ReturnPage();
            WSErro("Desculpe, sua pesquisa não retornou resultados. Você pode resumir sua pesquisa, ou tentar outros termos!", WS_INFOR);
        else:
            $cc = 0;
            $View = new View;
            $tpl_art = $View->Load('article_m');
            foreach ($readArt->getResult() as $art):
                $cc++;
                $class = ($cc % 3 == 0 ? ' class="right"' : null);
                echo "<span{$class}>";
                $art['produto_titulo'] = Check::Words($art['produto_titulo'], 9);
                $art['produto_conteudo'] = Check::Words($art['produto_conteudo'], 20);
                $art['datetime'] = date('Y-m-d', strtotime($art['produto_data']));
                $art['pubdate'] = date('d/m/Y H:i', strtotime($art['produto_data']));
                $View->Show($art, $tpl_art);
                echo "</span>";
            endforeach;
        endif;

        echo '<nav class="paginator">';
        echo '<h2>Mais resultados para NOME DA CATEGORIA</h2>';

        $Pager->ExePaginator("mb_produtos", "WHERE produto_status = 1 AND (produto_titulo LIKE '%' :link '%' OR produto_conteudo LIKE '%' :link '%')", "link={$search}");
        echo $Pager->getPaginator();

        echo '</nav>';
        ?>
    </section>
    <div class="clear"></div>

</div><!--/ site container -->