<?php
if ($Link->getData()):
	$search = $Link->getLocal()[0];
    extract($Link->getData());
else:
    header('Location: ' . HOME . DIRECTORY_SEPARATOR . '404');
endif;

?>
<!--HOME CONTENT-->
<div class="site-container">

    <article class="page_article">

        <div class="carr_content">
            <!--CABEÇALHO GERAL-->
            <header>
                <hgroup>
					<div style="padding:10px;">
						<h1>Revisão de dados pessoais</h1>				
					</div>					
                </hgroup>
            </header>
			
			<!--CONTEUDO-->
            <div class="htmlchars">
				<div class="main-revisao">
					<section class="revisao_contact">						
						<form name="FormConferir" action="<?=HOME?>/revisao_compra" method="post">
						
						<div class="revisao_left">
							<h3 class="line_title"><span>Endereço de entrega:</span></h3>
							
							<label>
								<span>CPF:</span>
								<input type="text" title="Informe seu CPF" name="cpf_usuario" value="<?= $cpf_usuario; ?>" required />
							</label>
							
							<label>
								<span>Endereço:</span>
								<input type="text" title="Informe seu endereço" name="endereco_usuario" value="<?= $endereco_usuario; ?>" required />
							</label>
							
							<label class="label_small">
								<span class="field">Estado UF:</span>
								<select class="j_loadstate" name="estado_usuario">
									<option value="" selected> Selecione o estado </option>
									<?php
									$readState = new Read;
									$readState->ExeRead("app_estados", "ORDER BY estado_nome ASC");
									foreach ($readState->getResult() as $estado):
										extract($estado);
										echo "<option value=\"{$estado_id}\" ";
										if (isset($estado_usuario) && $estado_usuario == $estado_id): echo 'selected';
										endif;
										echo "> {$estado_uf} / {$estado_nome} </option>";
									endforeach;
									?>                        
								</select>
							</label>

							<label class="label_small">
								<span class="field">Cidade:</span>
								<select class="j_loadcity" name="cidade_usuario">
									<?php if (!isset($cidade_usuario)): ?>
										<option value="" selected disabled> Selecione antes um estado </option>
										<?php
									else:
										$City = new Read;
										$City->ExeRead("app_cidades", "WHERE estado_id = :uf ORDER BY cidade_nome ASC", "uf={$estado_id}");
										if ($City->getRowCount()):
											foreach ($City->getResult() as $cidade):
												extract($cidade);
												echo "<option value=\"{$cidade_id}\" ";
												if (isset($cidade_usuario) && $cidade_usuario == $cidade_id):
													echo "selected";
												endif;
												echo "> {$cidade_nome} </option>";
											endforeach;
										endif;
									endif;
									?>
								</select>
							</label>
							
							<label>
								<span>Código Postal:</span>
								<input type="text" title="Informe seu cep" name="cep_usuario" value="<?= $cep_usuario; ?>" required />
							</label>
						</div>
						
						<div class="revisao_right">
						<h3 class="line_title"><span>Contato:</span></h3>
							<label>
								<span>nome:</span>
								<input type="text" title="Informe seu nome" name="nome_usuario" value="<?= $nome_usuario; ?>" required />
							</label>
							
							<label>
								<span>Sobrenome:</span>
								<input type="text" title="Informe seu sobrenome" name="ultimo_usuario" value="<?= $ultimo_usuario; ?>"required />
							</label>
							
							<label>
								<span>e-mail:</span>
								<input type="text" title="Informe seu e-mail" name="email_usuario" value="<?= $email_usuario; ?>" disabled />
							</label>
							
							<label>
								<span>Telefone:</span>
								<input type="text" title="Informe seu telefone" name="telefone_usuario" value="<?= $telefone_usuario; ?>" required />
							</label>
							<input type="hidden" title="Informe seu e-mail" name="email_usuario" value="<?= $email_usuario; ?>" />
							<input type="submit" value="Confirmar" name="SendFormConferir" class="btn">  
							
						</div>
						</form>
					</section>
				</div>
				<div class="clear"></div>
            </div>
            <!--Comentários aqui-->
        </div><!--art content-->

        <div class="clear"></div>
    </article>

    <div class="clear"></div>
</div><!--/ site container -->