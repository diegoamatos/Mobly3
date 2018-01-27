<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Produto:</h1>
        </header>

        <?php
        $produto = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $produtoid = filter_input(INPUT_GET, 'produtoid', FILTER_VALIDATE_INT);

        if (isset($produto) && $produto['SendProdutoForm']):
            $produto['produto_status'] = ($produto['SendProdutoForm'] == 'Atualizar' ? '0' : '1' );
            $produto['produto_capa'] = ( $_FILES['produto_capa']['tmp_name'] ? $_FILES['produto_capa'] : 'null' );
            unset($produto['SendProdutoForm']);

            require('_models/AdminProduto.class.php');
            $cadastra = new AdminProduto;
            $cadastra->ExeUpdate($produtoid, $produto);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);

            if (!empty($_FILES['galeria_capas']['tmp_name'])):
                $sendGallery = new AdminProduto;
                $sendGallery->gbSend($_FILES['galeria_capas'], $produtoid);
            endif;

        else:
            $read = new Read;
            $read->ExeRead("mb_produtos", "WHERE produto_id = :id", "id={$produtoid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=produtos/index&empty=true');
            else:
                $produto = $read->getResult()[0];
                $produto['produto_data'] = date('d/m/Y H:i:s', strtotime($produto['produto_data']));
				$produto['produto_caract'] = json_decode($produto['produto_caract'], true);
            endif;
        endif;

        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if ($checkCreate && empty($cadastra)):
            WSErro("O produto <b>{$produto['produto_titulo']}</b> foi cadastrado com sucesso no sistema!", WS_ACCEPT);
        endif;
		
		
		
		
		
		
		//{"cor":1,"garantia":3,"altura":6,"largura":7,"profundidade":8,"peso":9,"descricao-do-tamanho":4,"especificacoes-tecnicas":5}
//Array ( [cor] => 1 [garantia] => 3 [altura] => 6 [largura] => 7 [profundidade] => 8 [peso] => 9 [descricao-do-tamanho] => 4 [especificacoes-tecnicas] => 5 )
						//(DISTINCT caract_nome) lista apenas os que nao são iguais
						
						?>


        <form name="ProdutoForm" action="" method="post" enctype="multipart/form-data">

            <label class="label">
                <span class="field">Imagem Destaque:</span>
                <input type="file" name="produto_capa" />
            </label>

            <label class="label">
                <span class="field">Nome do Produto:</span>
                <input type="text" name="produto_titulo" value="<?php if (isset($produto['produto_titulo'])) echo $produto['produto_titulo']; ?>" />
            </label>

            <label class="label">
                <span class="field">Descrição:</span>
                <textarea class="js_editor" name="produto_conteudo" rows="10"><?php if (isset($produto['produto_conteudo'])) echo htmlspecialchars($produto['produto_conteudo']); ?></textarea>
            </label>

            <div class="label_line">

                <label class="label_small">
                    <span class="field">Preço:</span>
                    <input type="text" name="produto_preco" value="<?php
                    if (isset($produto['produto_preco'])): echo $produto['produto_preco'];
                    else: echo $produto['produto_preco'];
                    endif;
                    ?>" />
                </label>
				
				<input type="hidden" class="formDate center" name="produto_data" value="<?php
                    if (isset($produto['produto_data'])): echo $produto['produto_data'];
                    else: echo date('d/m/Y H:i:s');
                    endif;
                    ?>" />

                <label class="label_small">
                    <span class="field">Categoria:</span>
                    <select name="produto_categoria">
                        <option value=""> Selecione a categoria: </option>                        
                        <?php
                        $readSes = new Read;
                        $readSes->ExeRead("mb_categorias", "WHERE categoria_pai IS NULL ORDER BY categoria_titulo ASC");
                        if ($readSes->getRowCount() >= 1):
                            foreach ($readSes->getResult() as $ses):
                                echo "<option disabled=\"disabled\" value=\"\"> {$ses['categoria_titulo']} </option>";
                                $readCat = new Read;
                                $readCat->ExeRead("mb_categorias", "WHERE categoria_pai = :pai ORDER BY categoria_titulo ASC", "pai={$ses['categoria_id']}");

                                if ($readCat->getRowCount() >= 1):
                                    foreach ($readCat->getResult() as $cat):
                                        echo "<option ";

                                        if ($produto['produto_categoria'] == $cat['categoria_id']):
                                            echo "selected=\"selected\" ";
                                        endif;

                                        echo "value=\"{$cat['categoria_id']}\"> &raquo;&raquo; {$cat['categoria_titulo']} </option>";
                                    endforeach;
                                endif;

                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>

            </div><!--/line-->
			
			<?php
				$readCaract = new Read;
				$readCaract->FullRead("SELECT DISTINCT caract_nome, caract_valor, caract_id, COUNT(*) AS quantidade FROM mb_caracteristicas GROUP BY caract_nome ORDER BY quantidade");
				//print_r($readCaract->getResult());
				if ($readCaract->getRowCount() >= 1):
					$conta_coluna = 1;
					$conta_loop = 1;
					foreach ($readCaract->getResult() as $aut):
						if ($conta_coluna == 1) {
						   echo "<div class=\"label_line\">";
						}
						?>
						<label class="label_small">
							<span class="field"><?=$aut['caract_nome']?>:</span>
							<select name="produto_caract[<?=$aut['caract_nome']?>]">
							<option value=""> Selecione <?=$aut['caract_nome']?>: </option>
							<?php
								$readCaract2 = new Read;
                                $readCaract2->ExeRead("mb_caracteristicas", "WHERE caract_nome = :nome ORDER BY caract_nome ASC", "nome={$aut['caract_nome']}");

                                if ($readCaract2->getRowCount() >= 1):								
                                    foreach ($readCaract2->getResult() as $Caract2):
									//$produto['produto_caract'] = json_decode($produto['produto_caract'], true);
									echo "<option ";
                                        if ($produto['produto_caract'][Check::Name($Caract2['caract_nome'])] == $Caract2['caract_id']):
                                            echo "selected=\"selected\" ";
                                        endif;
                                        echo "value=\"{$Caract2['caract_id']}\">{$Caract2['caract_valor']}</option>";
                                    endforeach;
                                endif;?>                   
							</select>
						</label>
						<?php
						$conta_coluna++;
						if ($conta_coluna == 4) {
						   echo "</div>";
						   $conta_coluna = 1;
						}
						
						if ($conta_loop == $readCaract->getRowCount()) {
							echo "</div>";
						}
						$conta_loop++;
					endforeach;
				endif;
				
			?>

            <div class="label gbform" id="gbfoco">

                <label class="label">             
                    <span class="field">Enviar Galeria:</span>
                    <input type="file" multiple name="galeria_capas[]" />
                </label>

                <?php
                $delGb = filter_input(INPUT_GET, 'gbdel', FILTER_VALIDATE_INT);
                if ($delGb):
                    require_once('_models/AdminProduto.class.php');
                    $DelGallery = new AdminProduto;
                    $DelGallery->gbRemove($delGb);

                    WSErro($DelGallery->getError()[0], $DelGallery->getError()[1]);

                endif;
                ?>

                <ul class="gallery">
                    <?php
                    $gbi = 0;
                    $Gallery = new Read;
                    $Gallery->ExeRead("mb_produtos_galerias", "WHERE produto_id = :produto", "produto={$produtoid}");
                    if ($Gallery->getResult()):
                        foreach ($Gallery->getResult() as $gb):
                            $gbi++;
                            ?>
                            <li<?php if ($gbi % 5 == 0) echo ' class="right"'; ?>>
                                <div class="img thumb_small">
                                    <?= Check::Image('../uploads/' . $gb['galeria_imagem'], $gbi, 146, 100); ?>
                                </div>
                                <a href="painel.php?exe=produtos/update&produtoid=<?= $produtoid; ?>&gbdel=<?= $gb['galeria_id']; ?>#gbfoco" class="del">Deletar</a>
                            </li>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </ul>                
            </div>

			<input type="hidden" name="produto_usuario" value="<?= $_SESSION['userlogin']['id_usuario']; ?>" />
            <input type="submit" class="btn blue" value="Atualizar" name="SendProdutoForm" />
            <input type="submit" class="btn green" value="Atualizar & Publicar" name="SendProdutoForm" />

        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->