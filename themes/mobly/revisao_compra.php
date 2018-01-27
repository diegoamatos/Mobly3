<?php
$produto_id = (!empty($produto_id) ? $produto_id : null);
$produto_categoria = (!empty($produto_categoria) ? $produto_categoria : null);
$ConferirData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if(isset($_SESSION["userlogin"]["id_usuario"])){
	$userId = $_SESSION["userlogin"]["id_usuario"];
}

if (isset($ConferirData) && $ConferirData['SendFormConferir']){
	unset($ConferirData['SendFormConferir']);
	$ConferirData['senha_usuario'] = NULL;

	
	
	$conferirUser = new User;
	$conferirUser->ExeUpdate($userId, $ConferirData);

	if ($conferirUser->getResult()):
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: " . HOME . "/revisao_compra");
	else:
		WSErro($conferirUser->getError()[0], $conferirUser->getError()[1]);
	endif;
}

$Carrinho = new Carrinho();

if(isset($veri)){
	$CarResult = $Carrinho->alterarProduto($veri['cod'],$veri['quant']);
}

if(isset($Link->Url[2])){
	$CarResult = $Carrinho->removeProduto($Link->Url[2]);
}

if(isset($Link->getLocal()[1])){
	if($Link->getLocal()[1] == 'finalizar'){
		
		if(isset($_SESSION["carrinho"])){
			
			$pedido_codigo = date("BHis")."";
			$_SESSION["cod_pedido"] = $pedido_codigo;
			foreach($_SESSION["carrinho"] as $key => $final){
			$pedidoUser = new User;
			$criarPedido = ['pedido_codigo' => $pedido_codigo, "pedido_usuario" => $_SESSION["userlogin"]["id_usuario"], "pedido_produto" => $final["carrinho_cod"], "pedido_quantidade" => $final["carrinho_quantidade"], "pedido_pagamento" => "Boleto", "pedido_parcelas" => 1, "pedido_data" => date('Y-m-d H:i:s'), "pedido_status" => "Em andamento"];
				$pedidoUser-> ExeCreatePedido($criarPedido);
				if ($pedidoUser->getResult()){
					$CarResult = $Carrinho->removeProduto($final["carrinho_cod"]);
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ". HOME . DIRECTORY_SEPARATOR . "revisao_compra" . DIRECTORY_SEPARATOR . "finalizado");
				}
			}
		}
	}
}
?>
<!--HOME CONTENT-->
<div class="site-container">

    <article class="page_article">
		<?php
		if(isset($Link->getLocal()[1])){
			if($Link->getLocal()[1] == 'finalizado'){
				$ReadListaPedido = new Read;
				$ReadProdutoCarr = new Read;
				$ReadCaract3 = new Read;
				$ReadListaPedido->ExeRead("mb_pedidos", "WHERE pedido_usuario = :pdID", "pdID={$_SESSION["userlogin"]["id_usuario"]}");
		?>

		<div class="carr_content" style="padding: 10px;">
            <!--CABEÇALHO GERAL-->
            <header>
                <hgroup>
					<div style="padding:10px;">
						
					</div>					
                </hgroup>
            </header>
			
			<!--CONTEUDO-->
            <div class="htmlchars">
				<div class="main-revisao">
					<section class="revisao_contact">
						
						<?php
							if ($ReadListaPedido->getRowCount() >= 1){
						?>
						<div class="">
							<h3 class="line_title"><span>Obrigado por comprar no Mobly</span></h3>
							
							<div class="cart-block mb-3">
								<h5><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Este é o seu número de ordem: </font></font><strong><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"># <?=$_SESSION["cod_pedido"];?></font></font></strong></h5>
								<div class="cart-block-inside">
								<p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Um e-mail com o resumo do seu pedido foi enviado para: </font></font><strong><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?=$_SESSION["userlogin"]["email_usuario"];?></font></font></strong></p>
								<div class="row">

								<div class="col-md-6 col-sm-6 col-12">
								<h5><strong><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">A ordem será enviada para:</font></font></strong></h5>
								<p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?=$_SESSION["userlogin"]["nome_usuario"];?> <?=$_SESSION["userlogin"]["ultimo_usuario"];?></font></font><br><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?=$_SESSION["userlogin"]["endereco_usuario"];?>, <?=$_SESSION["userlogin"]["numero_usuario"];?> </font></font><br><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?=$_SESSION["userlogin"]["cep_usuario"];?> <?=Check::CidadeByName($_SESSION["userlogin"]["cidade_usuario"])["cidade_nome"];?> </font></font><br><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?=Check::EstadoByName($_SESSION["userlogin"]["estado_usuario"])["estado_nome"];?> - Brasil</font></font></p>
								</div>

								</div>
								</div>
							</div>
						</div>
						
						<div class="clear"></div>
						
						<div style="padding:10px;">
							<h3>Lista de Pedidos</h3>				
						</div>				
				
						
						<ul class="ultable">
							<li class="t_title">
								<span class="ue">Imagem</span>
								<span class="un">Produto</span>
								<span class="ui">Qua</span>
								<span class="ul">Preço</span>
								<span class="ur">SubTotal</span>
							</li>
						<?php
								foreach ($ReadListaPedido->getResult() as $ListaDePedidos):
									extract($ListaDePedidos);
									$ReadProdutoCarr->ExeRead("mb_produtos", "WHERE produto_codigo = :pcod", "pcod={$pedido_produto}");
									if ($ReadProdutoCarr->getRowCount() >= 1){
										extract($ReadProdutoCarr->getResult()[0]);
										$produtoJson = json_decode($produto_caract, true);
										$ReadCaract3->ExeRead("mb_caracteristicas", "WHERE caract_id = :id ORDER BY caract_nome ASC", "id={$produtoJson['cor']}");
										if($ReadCaract3->getRowCount() >= 1){
											$Caract3 = $ReadCaract3->getResult();
											$Cor = $Caract3[0]['caract_valor'];
										}else{
											$Cor = 'Não definido';
										}										
									}else{
										echo "FFFFFFFFFFFFF";
									}
								?>
							<li>
								<span class="ue center"><?= Check::Image('uploads' . DIRECTORY_SEPARATOR . $produto_capa, $produto_titulo, 120); ?></span>
								<span class="un">
									<a style="color:#666;" href="<?=HOME?>/produto/<?= $produto_nome; ?>">
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
									<?= $pedido_quantidade;?>
								</span>
								<span class="ul"><?= number_format($produto_preco, 2, ',', '.'); ?></span>
								<span class="ur"><?= number_format($produto_preco*$pedido_quantidade,2,",","."); ?></span>
							</li>
								<?php
								endforeach;
							}
						?>
						</ul>
					</section>
						
				</div>
				<div class="clear"></div>
            </div>
            <!--Comentários aqui-->
        </div><!--art content-->
		
		<?php	
			}
		}else{
		?>
		
		<div class="carr_content">
            <!--CABEÇALHO GERAL-->
            <header>
                <hgroup>
					<div style="padding:10px;">
						<h1>Revisão do Pedido</h1>				
					</div>				
			
					
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
						$ReadCarrinho2 = new Read;
						$ReadProdutoCarr = new Read;
						$ReadCaract3 = new Read;
						$ReadCarrinho2->ExeRead("mb_carrinho", "WHERE carrinho_sessao = :carsessao", "carsessao={$_SESSION['useronline']['online_session']}");
						if ($ReadCarrinho2->getRowCount() >= 1){							
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
		
		
					<div style="padding:10px;">
						<div style="float: left;">
							<div class="btn btn-success direita"><strong>TOTAL:</strong> <?=@number_format($Carrinho->somaCarrinho(),2,",",".");?></div>
						</div>
						<?php 
							if(isset($_SESSION["carrinho"])){?>
							<div style="float: right;"><a class="btn btn-success direita" href="<?= HOME ?>/revisao_compra/finalizar" title="Faça a encomenda">Faça a encomenda</a></div>
						<?php }else{?>
							<div style="float: right;"><a class="btn btn-success direita" href="<?= HOME ?>/produtos" title="Ir para compras">Ir para compras</a></div>
						<?php }?>				
					</div>
					
					<div style="clear: both;"></div>	
					
                </hgroup>
            </header>
			
			<!--CONTEUDO-->
            <div class="htmlchars">
                <!--ENDEREÇO-->
                <?php
				//$_SESSION["userlogin"]
				//[id_usuario] => 1 [nome_usuario] => Diego [ultimo_usuario] => Andrade de Matos [email_usuario] => diego.a.matos@gmail.com [senha_usuario] => e10adc3949ba59abbe56e057f20f883e [registrado_usuario] => 2018-01-20 11:14:04 [atualizacao_usuario] => 2018-01-21 14:19:40 [nivel_usuario] => 3 ) 
                
				if(isset($_SESSION["userlogin"])){
					
				}
				$readGb = new Read;
                $readGb->ExeRead("mb_usuario", "WHERE id_usuario = :userid", "userid={$_SESSION["userlogin"]["id_usuario"]}");
                if ($readGb->getResult()):
				$entrega = $readGb->getResult()[0];
                    ?>
                    <section class="gallery">
                        <hgroup>
                            <h3>
                                <p class="tagline"> <strong>Endereço de Entrega:</strong></p>
                            </h3>
                        </hgroup>

                        
								<div>
									<p><?= $entrega["nome_usuario"]; ?> <?= $entrega["ultimo_usuario"]; ?></p>
								</div>
								<div>
									<p><?= $entrega["endereco_usuario"]; ?>, <?= $entrega["numero_usuario"]; ?></p>
								</div>
								<div>
									<p><?= $entrega["cep_usuario"]; ?> <?= Check::CidadeByName($entrega["cidade_usuario"])["cidade_nome"]; ?></p>
								</div>
								<div>
									<p><?= Check::EstadoByName($entrega["estado_usuario"])["estado_nome"]; ?> - Brasil</p>
								</div>
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
		
		<?php
		}
		?>
        

        <!--SIDEBAR-->
        <?php //require(REQUIRE_PATH . '/inc/sidebar.inc.php'); ?>

        <div class="clear"></div>
    </article>

    <div class="clear"></div>
</div><!--/ site container -->