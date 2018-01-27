<?php
$View = (!empty($View) ? $View : $View = new View);
$produto_id = (!empty($produto_id) ? $produto_id : null);

$Side = new Read;
$tpl_p = $View->Load('article_p');
?>

<aside class="main-sidebar">
    <article class="ads">
        <header>
            <h1>Anúncio Patrocinado:</h1>
            <!--300x250-->
			<a href="http://www.diegomatos.com" title="Diego Matos - Profissional em TI">
				<img src="<?= INCLUDE_PATH; ?>/_tmp/banner_large.png" title="Diego Matos - Profissional em TI" alt="Diego Matos - Profissional em TI" />
			</a>
        </header>
    </article>

    <section class="widget art-list last-publish">
        <h2 class="line_title"><span class="oliva">Novidades:</span></h2>
        <?php
        $Side->ExeRead("mb_produtos", "WHERE produto_status = 1 AND produto_id != :side ORDER BY produto_data DESC LIMIT 3", "side={$produto_id}");
        if ($Side->getResult()):
            foreach ($Side->getResult() as $last):
                $last['datetime'] = date('Y-m-d', strtotime($last['produto_data']));
                $last['pubdate'] = date('d/m/Y H:i', strtotime($last['produto_data']));
                $View->Show($last, $tpl_p);
            endforeach;
        endif;
        ?>
    </section>

    <section class="widget art-list most-view">
        <h2 class="line_title"><span class="vermelho">Destaques:</span></h2>
        <?php
        $Side->ExeRead("mb_produtos", "WHERE produto_status = 1 AND produto_id != :side ORDER BY produto_ultima_visual DESC LIMIT 3", "side={$produto_id}");
        if ($Side->getResult()):
            foreach ($Side->getResult() as $most):
                $most['datetime'] = date('Y-m-d', strtotime($most['produto_data']));
                $most['pubdate'] = date('d/m/Y H:i', strtotime($most['produto_data']));
                $View->Show($most, $tpl_p);
            endforeach;
        endif;
        ?>
    </section>
</aside>