<header class="main-header">
    <div class="container">
        <hgroup>
            <h1>Loja de Móveis e Artigos de Decoração</h1>
            <h2>Bem-vindo à maior loja de móveis e artigos de decoração online do Brasil. A Mobly tem tudo para sua casa.</h2>
        </hgroup>

        <div class="header-banner">
            <!--468x60-->
            <a href="https://www.mobly.com.br/" title="Loja de Móveis e Artigos de Decoração">
                <img style="width:468px; hight:60px" src="<?= INCLUDE_PATH; ?>/_tmp/banner_medium.png" title="Loja de Móveis e Artigos de Decoração" alt="Loja de Móveis e Artigos de Decoração" />
            </a>
        </div><!-- banner -->

        <nav class="main-nav">

            <ul class="top">
                <li><a href="<?= HOME ?>" title="">Home</a></li>
                <li><a href="<?= HOME ?>/categoria" title="">Categorias</a>
                    <ul class="sub">
						<?php
							$MenuCat = new Read;
							$MenuCat->ExeRead("mb_categorias");
							if (!$MenuCat->getResult()):
								WSErro('Desculpe, ainda não existem categorias cadastradas. Favor volte mais tarde!', WS_INFOR);
							else:
								foreach ($MenuCat->getResult() as $MenuLink):
									echo "<li><a href=\"".HOME."/categoria/".$MenuLink['categoria_nome']."\">".Check::Words($MenuLink['categoria_titulo'], 12)."</a></li>";
								endforeach;
							endif;
						?>
                    </ul>                
                </li>
                <li><a href="<?= HOME ?>/carrinho" title="">Carrinho</a></li>
                <li><a href="<?= HOME ?>/contato" title="">Contate-nos</a></li>

                <li class="search">
                    <?php
                    $search = filter_input(INPUT_POST, 's', FILTER_DEFAULT);
                    if (!empty($search)):
                        $search = strip_tags(trim(urlencode($search)));
                        header('Location: ' . HOME . '/pesquisa/' . $search);
                    endif;
                    ?>

                    <form name="search" action="" method="post">
                        <input class="fls" type="text" name="s" />
                        <input class="btn" type="submit" name="sendsearch" value="" />
                    </form>
                </li>

            </ul>
        </nav> <!-- main nav -->

    </div><!-- Container Header -->
</header> <!-- main header -->