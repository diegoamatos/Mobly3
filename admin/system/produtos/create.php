<div class="content form_create">

    <article>

        <header>
            <h1>Cadastrar Produtos:</h1>
        </header>

        <?php
        $produto = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($produto) && $produto['SendProdutoForm']):
            $produto['produto_status'] = ($produto['SendProdutoForm'] == 'Cadastrar' ? '0' : '1' );
            $produto['produto_capa'] = ( $_FILES['produto_capa']['tmp_name'] ? $_FILES['produto_capa'] : null );
            unset($produto['SendProdutoForm']);
			for ($i = 0; $i < 10; $i++) {
				$produto['produto_codigo'] = date("BHis")."";
			}
            require('_models/AdminProduto.class.php');
            $cadastra = new AdminProduto;
            $cadastra->ExeCreate($produto);

            if ($cadastra->getResult()):

                if (!empty($_FILES['galeria_capas']['tmp_name'])):
                    $sendGallery = new AdminProduto;
                    $sendGallery->gbSend($_FILES['galeria_capas'], $cadastra->getResult());
                endif;

                header('Location: painel.php?exe=produtos/update&create=true&produtoid=' . $cadastra->getResult());
            else:
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            endif;
        endif;
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
                    <input type="text" class="formPreco center" name="produto_preco" value="<?php
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
				//$decCaract = json_decode($produto['produto_caract']);
				//print_r($decCaract);
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
									echo "<option ";

                                        if ($produto['produto_caract'][$Caract2['caract_nome']] == $Caract2['caract_id']):
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

            <div class="label gbform">
                <label class="label">             
                    <span class="field">Enviar Galeria:</span>
                    <input type="file" multiple name="galeria_capas[]" />
                </label>             
            </div>
			<input type="hidden" name="produto_usuario" value="<?= $_SESSION['userlogin']['id_usuario']; ?>" />
            <input type="submit" class="btn blue" value="Cadastrar" name="SendProdutoForm" />
            <input type="submit" class="btn green" value="Cadastrar & Publicar" name="SendProdutoForm" />

        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->