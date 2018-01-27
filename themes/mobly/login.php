<?php
	$login = new Login(1);
	
	if ($login->CheckLogin()):
		header('Location: ' . HOME . '/dasboard');
	endif;

	$dataLogin = filter_input_array(INPUT_POST, FILTER_DEFAULT);
	if (!empty($dataLogin['SendFormLogin'])):

		$login->ExeLogin($dataLogin);
		if (!$login->getResult()):
			WSErro($login->getError()[0], $login->getError()[1]);
		else:
			header('Location: ' . HOME . '/dasboard');
		endif;

	endif;

	$get = filter_input(INPUT_GET, 'exe', FILTER_DEFAULT);
	if (!empty($get)):
		if ($get == 'restrito'):
			WSErro('<b>Oppsss:</b> Acesso negado. Favor efetue login para acessar o painel!', WS_ALERT);
		elseif ($get == 'logoff'):
			WSErro('<b>Sucesso ao deslogar:</b> Sua sessão foi finalizada. Volte sempre!', WS_ACCEPT);
		endif;
	endif;
?>
<!--HOME CONTENT-->
<div class="site-container">

    <article class="page_article">

        <div class="carr_content" style="width: 570px; padding: 10px;">
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
						<form name="FormLogin" action="<?=HOME?>/login" method="post">
						
						<div class="revisao_left">
							<h3 class="line_title"><span>Login:</span></h3>
							<label>
								<span>E-mail:</span>
								<input type="email" title="Informe seu e-mail" name="user" value="<?php if (!empty($ClienteData['email_usuario'])) echo $ClienteData['email_usuario']; ?>" required />
							</label>
							
							<label>
								<span>Senha:</span>
								<input type="password" title="Informe sua senha" name="pass" required />
							</label>
							
							<input type="submit" value="Logar" name="SendFormLogin" class="btn">
							
						</div>
						</form>
					</section>
						
				</div>
				<div class="clear"></div>
            </div>
            <!--Comentários aqui-->
        </div><!--art content-->
		<?php require(REQUIRE_PATH . '/inc/sidebar.inc.php'); ?>
        <div class="clear"></div>
    </article>

    <div class="clear"></div>
</div><!--/ site container -->