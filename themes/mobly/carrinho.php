<?php
$produto_id = (!empty($produto_id) ? $produto_id : null);
$produto_categoria = (!empty($produto_categoria) ? $produto_categoria : null);
$veri = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$Carrinho = new Carrinho();

if(isset($veri)){
	$CarResult = $Carrinho->alterarProduto($veri['cod'],$veri['quant']);
}

if(isset($Link->Url[2])){
	$CarResult = $Carrinho->removeProduto($Link->Url[2]);
}
//unset($_SESSION["carrinho"]);
//unset($_SESSION["produto"]);
?>
<!--HOME CONTENT-->
<div class="site-container">

    <article class="page_article">

        <div class="carr_content">
            <!--CABEÇALHO GERAL-->
            <header>
                <hgroup>
					<div style="padding:10px;">
					<h1>Meu Carrinho</h1>
						<div style="float: left;">
							<div class="btn btn-success direita"><strong>TOTAL:</strong> <?=@number_format($Carrinho->somaCarrinho(),2,",",".");?></div>
						</div>
						<?php 
						//unset($_SESSION["carrinho"]);
						//unset($_SESSION["produto"]);
							if(isset($_SESSION["carrinho"])){?>
							<div style="float: right;"><a class="btn btn-success direita" href="<?= HOME ?>/conferir" title="Ir para o pagamento">Ir para o pagamento</a></div>
						<?php }else{?>
							<div style="float: right;"><a class="btn btn-success direita" href="<?= HOME ?>/" title="Ir para compras">Ir para compras</a></div>
						<?php }?>						
					</div>
					<div style="clear: both;"></div>					
					
			
					
					<ul class="ultable">
						<li class="t_title">
							<span class="ue">Imagem</span>
							<span class="un">Produto</span>
							<span class="ui">Qua</span>
							<span class="ul">Preço</span>
							<span class="ur">SubTotal</span>
							<span class="ed center">Excluir</span>
						</li>
					<?php
						$relacionados = array();
						$ReadCarrinho2 = new Read;
						$ReadProdutoCarr = new Read;
						$ReadCaract3 = new Read;
						$ReadCarrinho2->ExeRead("mb_carrinho", "WHERE carrinho_sessao = :carsessao", "carsessao={$_SESSION['useronline']['online_session']}");
						if ($ReadCarrinho2->getRowCount() >= 1){
							$relacionados["count"] = $ReadCarrinho2->getRowCount();
							foreach ($ReadCarrinho2->getResult() as $Carrinho2):
								extract($Carrinho2);
								$ReadProdutoCarr->ExeRead("mb_produtos", "WHERE produto_codigo = :pcod", "pcod={$Carrinho2['carrinho_cod']}");
								if ($ReadProdutoCarr->getRowCount() >= 1):
									extract($ReadProdutoCarr->getResult()[0]);
									$produtoJson = json_decode($produto_caract, true);
									$ReadCaract3->ExeRead("mb_caracteristicas", "WHERE caract_id = :id ORDER BY caract_nome ASC", "id={$produtoJson['cor']}");
									if($ReadCaract3->getRowCount() >= 1){
										$Caract3 = $ReadCaract3->getResult();
										$Cor = $Caract3[0]['caract_valor'];
									}else{
										$Cor = 'Não definido';
									}
									$relacionados["categoria"][] = $produto_categoria;
									$relacionados["produto"][] = $produto_id;
								else:
									
								endif;
					?>
						<li>
							<span class="ue center"><?= Check::Image('uploads' . DIRECTORY_SEPARATOR . $produto_capa, $produto_titulo, 120); ?></span>
							<span class="un">
								<a href="<?=HOME?>/produto/<?= $produto_nome; ?>">
									<strong><?= $produto_titulo; ?></strong>
								</a>
								<span style="display:block; clear:both">
									<strong>Cod: </strong><?= $produto_codigo; ?>
								</span>
								<span style="display:block; clear:both">
									<strong>Cor: </strong><?=$Cor; ?>
								</span>
							</span>
							<span class="ui">
							<form method="post" name="menuForm<?= $produto_codigo; ?>" action="<?=HOME?>/carrinho/<?=$produto_id?>" onchange="document.forms['menuForm<?= $produto_codigo; ?>'].submit();">
							<input type="hidden" name="cod" value="<?= $produto_codigo; ?>">
								<select name="quant" id="quant">
									<?php
										for($quant = 1; $quant < 20; $quant++){
											echo "<option ";

											if ($carrinho_quantidade == $quant):
												echo "selected=\"selected\" ";
											endif;

											echo "value=\"{$quant}\">{$quant}</option>";
										}
									?>
								</select>
							</form>
							</span>
							<span class="ul"><?= number_format($produto_preco, 2, ',', '.'); ?></span>
							<span class="ur"><?= number_format($produto_preco*$carrinho_quantidade,2,",","."); ?></span>
							<span class="ed center">
								<a href="<?=HOME?>/carrinho/delete/<?= $produto_codigo; ?>" onclick="return confirm('Tem certeza que deseja deletar este produto?')" title="Deletar" class="action user_dele">Deletar</a>
							</span>
						</li>
					<?//$Carrinho2['carrinho_cod']
							endforeach;
						}else{?>
							<li>
								<span class="ufull center"><strong>Opps! </strong>Seu carrinho está vazio.</span>
							<li>
						<?php
						}
					?>						
					</ul>
		
		
		
		
                </hgroup>
            </header>

            <!--RELACIONADOS-->
            <?php
			//print_r($relacionados);
			if(isset($relacionados["count"])){
				$RelCat = implode(',', $relacionados["categoria"]);
				//"SELETC * From tb_chamadas WHERE produto_status = 1 AND produto_categoria in ($RelCat)"
				//"WHERE produto_status = 1 AND produto_id != :id AND produto_categoria in :cat ORDER BY rand() LIMIT 2", "id={$produto_id}&cat=({$RelCat})";
			}
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
        <?php //require(REQUIRE_PATH . '/inc/sidebar.inc.php'); ?>

        <div class="clear"></div>
    </article>

    <div class="clear"></div>
</div><!--/ site container -->