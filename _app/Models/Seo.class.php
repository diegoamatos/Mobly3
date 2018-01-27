<?php

/**
 * Seo [ MODEL ]
 * Classe de apoio para o modelo LINK. Pode ser utilizada para gerar SSEO para as páginas do sistema!
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class Seo {

    private $File;
    private $Link;
    private $Data;
    private $Tags;

    /* DADOS POVOADOS */
    private $seoTags;
    private $seoData;
	private $Carrinho;
	private $Produto;

    function __construct($File, $Link) {
        $this->File = strip_tags(trim($File));
        $this->Link = strip_tags(trim($Link));
    }

    /**
     * <b>Obter MetaTags:</b> Execute este método informando os valores de navegação para que o mesmo obtenha
     * todas as metas como title, description, og, itemgroup, etc.
     * 
     * <b>Deve ser usada com um ECHO dentro da tag HEAD!</b>
     * @return HTML TAGS =  Retorna todas as tags HEAD
     */
    public function getTags() {
        $this->checkData();
        return $this->seoTags;
    }

    /**
     * <b>Obter Dados:</b> Este será automaticamente povoado com valores de uma tabela single para arquivos
     * como categoria, produto, etc. Basta usar um extract para obter as variáveis da tabela!
     * 
     * @return ARRAY = Dados da tabela
     */
    public function getData() {
        $this->checkData();
        return $this->seoData;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Verifica o resultset povoando os atributos
    private function checkData() {
        if (!$this->seoData):
            $this->getSeo();
        endif;
    }

    //Identifica o arquivo e monta o SEO de acordo
    private function getSeo() {
        $ReadSeo = new Read;

        switch ($this->File):
            //SEO:: PRODUTO
            case 'produto':
                $Admin = (isset($_SESSION['userlogin']['nivel_usuario']) && $_SESSION['userlogin']['nivel_usuario'] == 3 ? true : false);
                $Check = ($Admin ? '' : 'produto_status = 1 AND');

                $ReadSeo->ExeRead("mb_produtos", "WHERE {$Check} produto_nome = :link", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    $extract = extract($ReadSeo->getResult()[0]);
                    $this->seoData = $ReadSeo->getResult()[0];
                    $this->Data = [$produto_titulo . ' - ' . SITENAME, $produto_conteudo, HOME . "/produto/{$produto_nome}", HOME . "/uploads/{$produto_capa}"];

                    //produto:: conta visualizações do produto
                    $ArrUpdate = ['produto_visualizacoes' => $produto_visualizacoes + 1];
                    $Update = new Update();
                    $Update->ExeUpdate("mb_produtos", $ArrUpdate, "WHERE produto_id = :produtoid", "produtoid={$produto_id}");
                endif;
                break;

            //SEO:: CATEGORIA
            case 'categoria':
                $ReadSeo->ExeRead("mb_categorias", "WHERE categoria_nome = :link", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($ReadSeo->getResult()[0]);
                    $this->seoData = $ReadSeo->getResult()[0];
                    $this->Data = [$categoria_titulo . ' - ' . SITENAME, $categoria_conteudo, HOME . "/categoria/{$categoria_nome}", INCLUDE_PATH . '/images/site.png'];

                    //category:: conta views da categoria
                    $ArrUpdate = ['categoria_visualizacoes' => $categoria_visualizacoes + 1];
                    $Update = new Update();
                    $Update->ExeUpdate("mb_categorias", $ArrUpdate, "WHERE categoria_id = :catid", "catid={$categoria_id}");
                endif;
                break;
				
			//SEO:: CARRINHO
            case 'categorias':
				$this->Data = [' Seu carrinho Mobly | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
                break;

            //SEO:: PESQUISA
            case 'pesquisa':
                $ReadSeo->ExeRead("mb_produtos", "WHERE produto_status = 1 AND (produto_titulo LIKE '%' :link '%' OR produto_conteudo LIKE '%' :link '%')", "link={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    $this->seoData['count'] = $ReadSeo->getRowCount();
                    $this->Data = ["Pesquisa por: {$this->Link}" . ' - ' . SITENAME, "Sua pesquisa por {$this->Link} retornou {$this->seoData['count']} resultados!", HOME . "/pesquisa/{$this->Link}", INCLUDE_PATH . '/images/site.png'];
                endif;
                break;

            //SEO:: LISTA PRODUTOSS
            case 'produtos':
                $Name = ucwords(str_replace("-", " ", $this->Link));
                $this->seoData = ["produto_link" => $this->Link, "produto_cat" => $Name];
                $this->Data = ["Produtos {$this->Link}" . SITENAME, "Loja de Móveis e Artigos de Decoração {$this->Link}.", HOME . '/produtos/' . $this->Link, INCLUDE_PATH . '/images/site.png'];
                break;
				
			//SEO:: CART
            case 'cart':
                $Admin = (isset($_SESSION['userlogin']['nivel_usuario']) && $_SESSION['userlogin']['nivel_usuario'] == 3 ? true : false);
                $Check = ($Admin ? '' : 'produto_status = 1 AND');

                $ReadSeo->ExeRead("mb_produtos", "WHERE {$Check} produto_id = :pid", "pid={$this->Link}");
                if (!$ReadSeo->getResult()):
                    $this->seoData = null;
                    $this->seoTags = null;
                else:
                    extract($ReadSeo->getResult()[0]);					
						//Carrinho:: add produto no carrinho
						$this->Carrinho = new Carrinho();
						$this->Produto = new Produto($produto_codigo,$produto_titulo,$produto_preco);
						$CarResult = $this->Carrinho->addProduto($this->Produto);
						if($CarResult){
							header("HTTP/1.1 301 Moved Permanently");
							header("Location: ". HOME . DIRECTORY_SEPARATOR ."carrinho");
						}else{
							$this->seoData = null;
							$this->seoTags = null;
						}
                endif;
                break;
				
			//SEO:: DASBOARD	
			case 'dasboard':
				if(isset($_SESSION["userlogin"]["id_usuario"])){
					$ReadSeo->ExeRead("mb_usuario", "WHERE id_usuario = :idUser", "idUser={$_SESSION["userlogin"]["id_usuario"]}");
					if (!$ReadSeo->getResult()):
						$this->seoData = null;
						$this->seoTags = null;
					else:
						$this->seoData = $ReadSeo->getResult()[0];
						$this->Data = [' Loja de Móveis e Artigos de Decoração | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
						
					endif;
				}else{
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ". HOME . DIRECTORY_SEPARATOR ."login");
				}
                break;
				
			//SEO:: LOGIN
			case 'login':
				if(isset($_SESSION["userlogin"]["id_usuario"])){
					$this->Data = [' Loja de Móveis e Artigos de Decoração | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
				}else{
					$this->Data = [' Loja de Móveis e Artigos de Decoração | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
				}
                break;
				
			//SEO:: CONFERIR
            case 'conferir':
				if(isset($_SESSION["userlogin"]["id_usuario"])){
					$ReadSeo->ExeRead("mb_usuario", "WHERE id_usuario = :idUser", "idUser={$_SESSION["userlogin"]["id_usuario"]}");
					if (!$ReadSeo->getResult()):
						$this->seoData = null;
						$this->seoTags = null;
					else:
						$this->seoData = $ReadSeo->getResult()[0];
						$this->Data = [' Loja de Móveis e Artigos de Decoração | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
						
					endif;
				}else{
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ". HOME . DIRECTORY_SEPARATOR ."login");
				}
                break;
			
			//SEO:: REVISÂO DE COMPRA
            case 'revisao_compra':
				$this->Data = [' Loja de Móveis e Artigos de Decoração | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
                break;
				
            //SEO:: CARRINHO
            case 'carrinho':
				$this->Data = [' Seu carrinho Mobly | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
                break;

            //SEO:: INDEX
            case 'index':
                $this->Data = [' Loja de Móveis e Artigos de Decoração | ' . SITENAME, SITEDESC, HOME, INCLUDE_PATH . '/images/site.png'];
                break;

            //SEO:: 404
            default :
                $this->Data = [SITENAME . ' - 404 Oppsss, Nada encontrado!', SITEDESC, HOME . '/404', INCLUDE_PATH . '/images/site.png'];

        endswitch;

        if ($this->Data):
            $this->setTags();
        endif;
    }

    //Monta e limpa as tags para alimentar as tags
    private function setTags() {
        $this->Tags['Title'] = $this->Data[0];
        $this->Tags['Content'] = Check::Words(html_entity_decode($this->Data[1]), 25);
        $this->Tags['Link'] = $this->Data[2];
        $this->Tags['Image'] = $this->Data[3];

        $this->Tags = array_map('strip_tags', $this->Tags);
        $this->Tags = array_map('trim', $this->Tags);

        $this->Data = null;

        //NORMAL PAGE
        $this->seoTags = '<title>' . $this->Tags['Title'] . '</title> ' . "\n";
        $this->seoTags .= '<meta name="description" content="' . $this->Tags['Content'] . '"/>' . "\n";
        $this->seoTags .= '<meta name="robots" content="index, follow" />' . "\n";
        $this->seoTags .= '<link rel="canonical" href="' . $this->Tags['Link'] . '">' . "\n";
        $this->seoTags .= "\n";

        //FACEBOOK
        $this->seoTags .= '<meta property="og:site_name" content="' . SITENAME . '" />' . "\n";
        $this->seoTags .= '<meta property="og:locale" content="pt_BR" />' . "\n";
        $this->seoTags .= '<meta property="og:title" content="' . $this->Tags['Title'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:description" content="' . $this->Tags['Content'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:image" content="' . $this->Tags['Image'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:url" content="' . $this->Tags['Link'] . '" />' . "\n";
        $this->seoTags .= '<meta property="og:type" content="article" />' . "\n";
        $this->seoTags .= "\n";

        //ITEM GROUP (TWITTER)
        $this->seoTags .= '<meta itemprop="name" content="' . $this->Tags['Title'] . '">' . "\n";
        $this->seoTags .= '<meta itemprop="description" content="' . $this->Tags['Content'] . '">' . "\n";
        $this->seoTags .= '<meta itemprop="url" content="' . $this->Tags['Link'] . '">' . "\n";

        $this->Tags = null;
    }

}
