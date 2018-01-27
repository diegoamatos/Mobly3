<footer class="main-footer" id="contato">
    <section class="container">                
        <nav>
            <h3 class="line_title"><span>Categorias:</span></h3>
            <ul>
			<?php
				$MenuCatFoo = new Read;
				$MenuCatFoo->ExeRead("mb_categorias", "WHERE categoria_pai = : cat ORDER BY categoria_cadastro DESC LIMIT :limit OFFSET :offset", "cat=NULL&limit=4&offset=0");
				if (!$MenuCatFoo->getResult()):
					WSErro('Desculpe, ainda não existem categorias cadastradas. Favor volte mais tarde!', WS_INFOR);
				else:
					foreach ($MenuCatFoo->getResult() as $MenuLinkFoo):
						echo "<li><a href=\"".HOME."/categoria/".$MenuLinkFoo['categoria_nome']."\">".Check::Words($MenuLinkFoo['categoria_titulo'], 12)."</a></li>";
					endforeach;
				endif;
			?>
            </ul>
        </nav>

        <section>
            <h3 class="line_title"><span>Um resumo:</span></h3>
            <p>A Mobly nasceu em 2011 e, hoje, é referência em móveis e decoração em todo o país e foi idealizada a partir de empreendores que, enquanto estudavam no exterior, se interessaram por modelos de negócios digitais. Ao voltar para o Brasil, viram uma oportunidade de vender móveis e artigos decorativos para deixar os lares dos brasileiros ainda mais com o jeito deles. Com isso, a Mobly recebeu investimentos do grupo Rocket Internet e também de outros investidores que acreditaram no negócio.</p>
        </section>

        <section class="footer_contact">
            <h3 class="line_title"><span>Contato:</span></h3>
            
            <?php
            $Contato = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if($Contato && $Contato['SendFormContato']):
                unset($Contato['SendFormContato']);
            
                $Contato['Assunto'] = 'Mensagem via Site!';
                $Contato['DestinoNome'] = 'Diego Matos - Mobly';
                $Contato['DestinoEmail'] = 'diego.a.matos@gmail.com';
                
                $SendMail = new Email;
                $SendMail->Enviar($Contato);
                
                if($SendMail->getError()):
                    WSErro($SendMail->getError()[0], $SendMail->getError()[1]);
                endif;
                
            endif;
            ?>
            
            <form name="FormContato" action="#contato" method="post">
                <label>
                    <span>nome:</span>
                    <input type="text" title="Informe seu nome" name="RemetenteNome" required />
                </label>

                <label>
                    <span>e-mail:</span>
                    <input type="email" title="Informe seu e-mail" name="RemetenteEmail" required />
                </label>

                <label>
                    <span>mensagem:</span>
                    <textarea title="Envie sua mensagem" name="Mensagem" required rows="3"></textarea>
                </label>

                <input type="submit" value="Enviar" name="SendFormContato" class="btn">                        
            </form>
        </section>
        <div class="clear"></div>
    </section><!-- /ontainer -->

    <div class="footer_logo">Loja Mobly - Loja de Móveis e Artigos de Decoração</div><!-- footer logo -->
</footer>