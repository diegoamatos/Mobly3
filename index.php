<?php
ob_start();
require('./_app/Config.inc.php');
$Session = new Session;
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">

        <!--[if lt IE 9]>
            <script src="../../_cdn/html5.js"></script>
         <![endif]-->   

        <?php
        $Link = new Link;
        $Link->getTags();
        ?>

        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/reset.css">
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/style.css">
        <link rel="stylesheet" href="<?= HOME; ?>/_cdn/shadowbox/shadowbox.css">
        <link href='http://fonts.googleapis.com/css?family=Baumans' rel='stylesheet' type='text/css'>
		<link rel="icon" href="<?= INCLUDE_PATH; ?>/images/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>/images/favicon.ico" type="image/x-icon" />
    </head>
    <body>

        <?php
        require(REQUIRE_PATH . '/inc/header.inc.php');

        if (!require($Link->getPatch())):
            WSErro('Erro ao incluir arquivo de navegação!', WS_ERROR, true);
        endif;

        require(REQUIRE_PATH . '/inc/footer.inc.php');
        ?>

    </body>
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
    <script src="<?= HOME ?>/_cdn/jquery.js"></script>
    <script src="<?= HOME ?>/_cdn/jcycle.js"></script>
    <script src="<?= HOME ?>/_cdn/jmask.js"></script>
    <script src="<?= HOME ?>/_cdn/shadowbox/shadowbox.js"></script>
    <script src="<?= HOME ?>/_cdn/_plugins.conf.js"></script>
    <script src="<?= HOME ?>/_cdn/_scripts.conf.js"></script>
    <script src="<?= HOME ?>/_cdn/combo.js"></script>
	<script src="<?= INCLUDE_PATH; ?>/jsc/admin.js"></script>

</html>
<?php
ob_end_flush();
