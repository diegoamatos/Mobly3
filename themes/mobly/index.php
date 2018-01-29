<?php
$View = new View;
$tpl_g = $View->Load('article_g');
$tpl_m = $View->Load('article_m');
$tpl_p = $View->Load('article_p');
$tpl_empresa = $View->Load('empresa_p');
?>
<!--HOME SLIDER-->
<section class="main-slider">
    <h3>Últimas Atualizações:</h3>
    <div class="container">

        <div class="slidecount">
            <?php
            $cat = Check::CatByName('moveis');
            $produto = new Read;
            $produto->ExeRead("mb_produtos", "WHERE produto_status = 1 AND (produto_cat_pai = :cat OR produto_categoria = :cat) ORDER BY produto_data DESC LIMIT :limit OFFSET :offset", "cat={$cat}&limit=3&offset=0");
            if (!$produto->getResult()):
                WSErro('Desculpe, ainda não existem produtos cadastrados. Favor volte mais tarde!', WS_INFOR);
            else:
                foreach ($produto->getResult() as $slide):
					/*$produtoCat = new Read;
					$produtoCat->ExeRead("mb_categorias", "WHERE categoria_id = :catid", "catid={$slide['produto_categoria']}");
					if ($produtoCat->getResult()):
						$produtoLink = $produtoCat->getResult();					
					endif;*/
                    $slide['produto_titulo'] = Check::Words($slide['produto_titulo'], 12);
                    $slide['produto_conteudo'] = Check::Words($slide['produto_conteudo'], 38);
                    $slide['produto_preco'] = number_format($slide['produto_preco'], 2, ',', '.');
					$slide['url'] = HOME;
					$slide['produto_id'] = $slide['produto_id'];
                    $View->Show($slide, $tpl_g);
                endforeach;
            endif;
            ?>                
        </div>

        <div class="slidenav"></div>   
    </div><!-- Container Slide -->

</section><!-- /slider -->


<!--HOME CONTENT-->
<div class="site-container">
	<section class="last_forcat">

        <h1>Por categoria!</h1>

        <section class="eventos">
            <h2 class="line_title"><span class="roxo">Decoração:</span></h2>

            <?php
            $cat = Check::CatByName('decoracao');
            $produto->setPlaces("cat={$cat}&limit=1&offset=0");
            if (!$produto->getResult()):
                WSErro("Desculpe, não existe um produto destaque para ser exibido agora. Favor, volte depois!", WS_INFOR);
            else:
                $new = $produto->getResult()[0];
                $new['produto_titulo'] = Check::Words($new['produto_titulo'], 9);
                $new['produto_conteudo'] = Check::Words($new['produto_conteudo'], 20);
                $new['produto_preco'] = number_format($new['produto_preco'], 2, ',', '.');
                $new['url'] = HOME;
				$new['produto_id'] = $new['produto_id'];
                $View->Show($new, $tpl_m);
            endif;
            ?>

            <div class="last_news">
                <?php
                $produto->setPlaces("cat={$cat}&limit=3&offset=1");
                if (!$produto->getResult()):
                    WSErro("Desculpe, não temos mais produtos para serem exibidas aqui. Favor, volte depois!", WS_INFOR);
                else:
                    foreach ($produto->getResult() as $news):
                        $news['produto_titulo'] = Check::Words($news['produto_titulo'], 12);
                        $news['produto_preco'] = number_format($news['produto_preco'], 2, ',', '.');
						$news['url'] = HOME;
						$news['produto_id'] = $news['produto_id'];
                        $View->Show($news, $tpl_p);
                    endforeach;
                endif;
                ?>
            </div>
        </section>


        <section class="esportes">
            <h2 class="line_title"><span class="verde">Moveis:</span></h2>

            <?php
            $cat = Check::CatByName('moveis');
            $produto->setPlaces("cat={$cat}&limit=1&offset=0");
            if (!$produto->getResult()):
                WSErro("Desculpe, não existe um produto destaque para ser exibido agora. Favor, volte depois!", WS_INFOR);
            else:
                $new = $produto->getResult()[0];
                $new['produto_titulo'] = Check::Words($new['produto_titulo'], 9);
                $new['produto_conteudo'] = Check::Words($new['produto_conteudo'], 20);
                $new['datetime'] = date('Y-m-d', strtotime($new['produto_data']));
                $new['pubdate'] = date('d/m/Y H:i', strtotime($new['produto_data']));
                $View->Show($new, $tpl_m);
            endif;
            ?>

            <div class="last_news">
                <?php
                $produto->setPlaces("cat={$cat}&limit=3&offset=1");
                if (!$produto->getResult()):
                    WSErro("Desculpe, não temos mais produtos para serem exibidas aqui. Favor, volte depois!", WS_INFOR);
                else:
                    foreach ($produto->getResult() as $news):
                        $news['produto_titulo'] = Check::Words($news['produto_titulo'], 12);
                        $news['datetime'] = date('Y-m-d', strtotime($news['produto_data']));
                        $news['pubdate'] = date('d/m/Y H:i', strtotime($news['produto_data']));
                        $View->Show($news, $tpl_p);
                    endforeach;
                endif;
                ?>
            </div>
        </section>


        <section class="baladas">
            <h2 class="line_title"><span class="azul">Iluminação:</span></h2>

            <?php
            $cat = Check::CatByName('iluminacao');
            $produto->setPlaces("cat={$cat}&limit=1&offset=0");
            if (!$produto->getResult()):
                WSErro("Desculpe, não existe uma notícia destaque para ser exibida agora. Favor, volte depois!", WS_INFOR);
            else:
                $new = $produto->getResult()[0];
                $new['produto_titulo'] = Check::Words($new['produto_titulo'], 9);
                $new['produto_conteudo'] = Check::Words($new['produto_conteudo'], 20);
                $new['datetime'] = date('Y-m-d', strtotime($new['produto_data']));
                $new['pubdate'] = date('d/m/Y H:i', strtotime($new['produto_data']));
                $View->Show($new, $tpl_m);
            endif;
            ?>

            <div class="last_news">
                <?php
                $produto->setPlaces("cat={$cat}&limit=3&offset=1");
                if (!$produto->getResult()):
                    WSErro("Desculpe, não temos mais produtos para serem exibidas aqui. Favor, volte depois!", WS_INFOR);
                else:
                    foreach ($produto->getResult() as $news):
                        $news['produto_titulo'] = Check::Words($news['produto_titulo'], 12);
                        $news['datetime'] = date('Y-m-d', strtotime($news['produto_data']));
                        $news['pubdate'] = date('d/m/Y H:i', strtotime($news['produto_data']));
                        $View->Show($news, $tpl_p);
                    endforeach;
                endif;
                ?>
            </div>
        </section>

    </section><!-- categorias -->
	


    
    <div class="clear"></div>
</div><!--/ site container -->