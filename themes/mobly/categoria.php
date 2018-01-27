<?php
if ($Link->getData()):
    extract($Link->getData());
else:
    header('Location: ' . HOME . DIRECTORY_SEPARATOR . '404');
endif;
?>
<!--HOME CONTENT-->
<div class="site-container">
    
    <section class="page_categorias">
        <header class="cat_header">
            <h2><?= $categoria_titulo; ?></h2>
            <p class="tagline"><?= $categoria_conteudo; ?></p>
        </header>

        <?php
        $getPage = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1);
        $Pager = new Pager(HOME . '/categoria/' . $categoria_nome . '/');
        $Pager->ExePager($getPage, 12);

        $readCat = new Read;
        $readCat->ExeRead("mb_produtos", "WHERE produto_status = 1 AND (produto_categoria = :cat OR produto_cat_pai = :cat) ORDER BY produto_data DESC LIMIT :limit OFFSET :offset", "cat={$categoria_id}&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if (!$readCat->getResult()):
            $Pager->ReturnPage();
            WSErro("Desculpe, a categoria {$categoria_titulo} ainda não tem produtos cadastrados, favor volte mais tarde!", WS_INFOR);
        else:
            $cc = 0;
            $View = new View;
            $tpl_cat = $View->Load('article_m');
            foreach ($readCat->getResult() as $cat):
                $cc++;
                $class = ($cc % 3 == 0 ? ' class="right"' : null);
                echo "<span{$class}>";
                $cat['produto_titulo'] = Check::Words($cat['produto_titulo'], 9);
                $cat['produto_conteudo'] = Check::Words($cat['produto_conteudo'], 20);
                $cat['datetime'] = date('Y-m-d', strtotime($cat['produto_data']));
                $cat['pubdate'] = date('d/m/Y H:i', strtotime($cat['produto_data']));
                $View->Show($cat, $tpl_cat);
                echo "</span>";
            endforeach;
        endif;

        echo '<nav class="paginator">';
        echo '<h2>Mais resultados para NOME DA CATEGORIA</h2>';

        $Pager->ExePaginator("mb_produtos", "WHERE produto_status = 1 AND (produto_categoria = :cat OR produto_cat_pai = :cat)", "cat={$categoria_id}");
        echo $Pager->getPaginator();

        echo '</nav>';
        ?>
    </section>
    <div class="clear"></div>
    
</div><!--/ site container -->