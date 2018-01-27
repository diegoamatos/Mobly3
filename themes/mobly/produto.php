<?php
if ($Link->getData()):
    extract($Link->getData());
else:
    header('Location: ' . HOME . DIRECTORY_SEPARATOR . '404');
endif;
?>
<!--HOME CONTENT-->
<div class="site-container">

    <article class="page_article">

        <div class="art_content">
            <!--CABEÇALHO GERAL-->
            <header>
                <hgroup>
					<?php
						$ProdutoCat = new Read;
						$ProdutoCat->ExeRead("mb_categorias", "WHERE categoria_id = :categid", "categid={$produto_categoria}");
						if (!$ProdutoCat->getResult()):
							WSErro('Desculpe, ainda não existem categorias cadastradas. Favor volte mais tarde!', WS_INFOR);
						else:
							$ProdutoLink = $ProdutoCat->getResult();
							echo "<p style=\"padding:10px;\"><strong> Categoria: " . Check::Words($ProdutoLink[0]['categoria_titulo'], 40) . "</strong></p>";
						endif;
					?>
					<h1><?= $produto_titulo; ?></h1>
					<div style="padding:10px;">
						<div style="float: left; padding: 8px;" class="btn-success">R$ <?= number_format($produto_preco, 2, ',', '.'); ?></div>
						<div style="float: right;"><a onclick="return confirm('Tem certeza que deseja adcionar ao carrinho?')" class="btn btn-success direita" href="<?= HOME ?>/cart/<?= $produto_id; ?>" title="Adcionar ao Carrinho">Adcionar ao Carrinho</a></div>
					</div>
					<div style="clear: both;"></div>
					<div class="img capa">
                        <?= Check::Image('uploads' . DIRECTORY_SEPARATOR . $produto_capa, $produto_titulo, 578); ?>
                    </div>
					<div style="padding:10px;">
						<table border="0">
                        <?php
						$produtoJson = json_decode($produto_caract, true);
						$conta_coluna = 1;
						$conta_loop = 1;
						if(count($produtoJson) > 0){
							foreach ($produtoJson as $key => $json):
							
								$jsonCaract2 = new Read;
                                $jsonCaract2->ExeRead("mb_caracteristicas", "WHERE caract_id = :id ORDER BY caract_nome ASC", "id={$json}");
								
								if ($conta_coluna == 1) {
								   echo "<tr>";
								}
								
								if ($jsonCaract2->getRowCount() >= 1){							
                                    foreach ($jsonCaract2->getResult() as $Caract2):
									echo "<td><strong>" . $Caract2['caract_nome'] . ": </strong><div>{$Caract2['caract_valor']}</div></td>";
                                    endforeach;
                                }
								
								$conta_coluna++;
								if ($conta_coluna == 4) {
								   echo "<tr>";
								   $conta_coluna = 1;
								}
								
								if ($conta_loop == count($produtoJson)) {
									echo "<tr>";
								}
								$conta_loop++;
							endforeach;
						}
						
						?>
						</table>
                    </div>
                </hgroup>
            </header>

            <!--CONTEUDO-->
            <div class="htmlchars">
				<strong>Descrição: </strong><br>
                <?= $produto_conteudo; ?>

                <!--GALERIA-->
                <?php
                $readGb = new Read;
                $readGb->ExeRead("mb_produtos_galerias", "WHERE produto_id = :produtoid ORDER BY galeria_data DESC", "produtoid={$produto_id}");
                if ($readGb->getResult()):
                    ?>
                    <section class="gallery">
                        <hgroup>
                            <h3>
                                GALERIA:
                                <p class="tagline">Veja fotos em <mark><?= $produto_titulo; ?></mark></p>
                            </h3>
                        </hgroup>

                        <ul>
                            <?php
                            $gb = 0;
                            foreach ($readGb->getResult() as $gallery):
                                $gb++;
                                extract($gallery);
                                ?>
                                <li>
                                    <div class="img">
                                        <a href="<?= HOME; ?>/uploads/<?= $galeria_imagem; ?>" rel="shadowbox[<?= $produto_id; ?>]" title="Imagem <?= $gb; ?> do post <?= $produto_titulo; ?>">
                                            <?= Check::Image('uploads' . DIRECTORY_SEPARATOR . $galeria_imagem, "Imagem {$gb} do post {$produto_titulo}", 120, 80); ?>
                                        </a>
                                    </div>
                                </li>
                                <?php
                            endforeach;
                            ?>
                        </ul>
                        <div class="clear"></div>
                    </section>
                <?php endif; ?>
            </div>

            <!--RELACIONADOS-->
            <?php
            $readMore = new Read;
            $readMore->ExeRead("mb_produtos", "WHERE produto_status = 1 AND produto_id != :id AND produto_categoria = :cat ORDER BY rand() LIMIT 2", "id={$produto_id}&cat={$produto_categoria}");
            if ($readMore->getResult()):
                $View = new View;
                $tpl_m = $View->Load('article_m');
                ?>
                <footer>
                    <nav>
                        <h3>Veja também:</h3>
                        <?php
						//unset($_SESSION["carrinho"]);
						//unset($_SESSION["produto"]);
                        foreach ($readMore->getResult() as $more):
                            $more['datetime'] = date('Y-m-d', strtotime($more['produto_data']));
                            $more['pubdate'] = date('d/m/Y H:i', strtotime($more['produto_data']));
                            $more['produto_conteudo'] = Check::Words($more['produto_conteudo'], 20);

                            $View->Show($more, $tpl_m);
                        endforeach;
                        ?>
                    </nav>
                    <div class="clear"></div>
                </footer>
                <?php
            endif;
            ?>
            <!--Comentários aqui-->
        </div><!--art content-->

        <!--SIDEBAR-->
        <?php require(REQUIRE_PATH . '/inc/sidebar.inc.php'); ?>

        <div class="clear"></div>
    </article>

    <div class="clear"></div>
</div><!--/ site container -->