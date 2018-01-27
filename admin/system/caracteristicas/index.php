<div class="content cat_list">

    <section>

        <h1>Características:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Você tentou editar uma características que não existe no sistema!", WS_INFOR);
        endif;

        $delCaract = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delCaract):
            require ('_models/AdminCaract.class.php');
            $deletar = new AdminCaract;
            $deletar->ExeDelete($delCaract);
            
            WSErro($deletar->getError()[0], $deletar->getError()[1]);
        endif;


        $readSes = new Read;
        $readSes->ExeRead("mb_caracteristicas");
		$quantos = $readSes->getRowCount();
        if (!$readSes->getResult()):?>
		<section>

			<header>
				<h1>Listagem de Características</h1>
				<p class="tagline">Não existem características cadastradas</p>
			</header>
		</section>
        <?php
		else:
		?>
			<section>

				<header>
					<h1>Listagem de Características</h1>
					<p class="tagline">Não existem características cadastradas</p>
				</header>

				<?php
				if ($quantos == 0):
				?>
				<article>
					<h1><strong><?= $sub['caract_nome']; ?></strong></h1>
				</article>
				<?php
				else:
					$a = 0;
					foreach ($readSes->getResult() as $sub):
						$a++;
						?>
						<article<?php if ($a % 3 == 0) echo ' class="right"'; ?>>
							<h1><strong><?= $sub['caract_nome']; ?></strong></h1>

							<ul class="info produto_actions">
								<li><?= Check::Words($sub['caract_valor'],"30"); ?></li>
								<li><a class="act_view" target="_blank" href="../caracteristicas/<?= Check::Name($sub['caract_valor']); ?>" title="Ver no site">Ver no site</a></li>
								<li><a class="act_edit" href="painel.php?exe=caracteristicas/update&caractid=<?= $sub['caract_id']; ?>" title="Editar">Editar</a></li>
								<li><a class="act_delete" href="painel.php?exe=caracteristicas/index&delete=<?= $sub['caract_id']; ?>" title="Excluir">Deletar</a></li>
							</ul>
						</article>
						<?php
					endforeach;
				endif;
				?>

			</section>
			<?php
		endif;
        ?>

        <div class="clear"></div>
    </section>

    <div class="clear"></div>
</div> <!-- content home -->