<?php

/**
 * Check.class [ HELPER ]
 * Classe responável por manipular e validade dados do sistema!
 * 
 * @copyright (c) 2018, Diego Matos diegomatos.com
 */
class Check {

    private static $Data;
    private static $Format;

    /**
     * <b>Verifica E-mail:</b> Executa validação de formato de e-mail. Se for um email válido retorna true, ou retorna false.
     * @param STRING $Email = Uma conta de e-mail
     * @return BOOL = True para um email válido, ou false
     */
    public static function Email($Email) {
        self::$Data = (string) $Email;
        self::$Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

        if (preg_match(self::$Format, self::$Data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * <b>Tranforma URL:</b> Tranforma uma string no formato de URL amigável e retorna o a string convertida!
     * @param STRING $Name = Uma string qualquer
     * @return STRING = $Data = Uma URL amigável válida
     */
    public static function Name($Name) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        self::$Data = strtr(utf8_decode($Name), utf8_decode(self::$Format['a']), self::$Format['b']);
        self::$Data = strip_tags(trim(self::$Data));
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('-----', '----', '---', '--'), '-', self::$Data);

        return strtolower(utf8_encode(self::$Data));
    }

    /**
     * <b>Tranforma Data:</b> Transforma uma data no formato DD/MM/YY em uma data no formato TIMESTAMP!
     * @param STRING $Name = Data em (d/m/Y) ou (d/m/Y H:i:s)
     * @return STRING = $Data = Data no formato timestamp!
     */
    public static function Data($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (empty(self::$Format[1])):
            self::$Format[1] = date('H:i:s');
        endif;

        self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0] . ' ' . self::$Format[1];
        return self::$Data;
    }

    /**
     * <b>Limita os Palavras:</b> Limita a quantidade de palavras a serem exibidas em uma string!
     * @param STRING $String = Uma string qualquer
     * @return INT = $Limite = String limitada pelo $Limite
     */
    public static function Words($String, $Limite, $Pointer = null) {
        self::$Data = strip_tags(trim($String));
        self::$Format = (int) $Limite;

        $ArrWords = explode(' ', self::$Data);
        $NumWords = count($ArrWords);
        $NewWords = implode(' ', array_slice($ArrWords, 0, self::$Format));

        $Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer );
        $Result = ( self::$Format < $NumWords ? $NewWords . $Pointer : self::$Data );
        return $Result;
    }
	
	/**
     * <b>Array Para String:</b> Converte um array em uma string!
     * @param ARRAY $array = Um array qualquer
     */
	public static function ArrayString($array) {
		$conta = count($array);
        if ($conta >= 1):
			$acumula = '';
			$soma = 1;
			foreach ($array as $key => $proCar):
				$acumula .= '"'.self::Name($key).'":'.$proCar;
				if($conta == $soma){
					$acumula .= '';
				}else{
					$acumula .= ',';
				}
				$soma++;
			endforeach;
			return '{' . $acumula . '}';
		endif;
    }
	
    /**
     * <b>Obter categoria:</b> Informe o name (url) de uma categoria para obter o ID da mesma.
     * @param STRING $category_name = URL da categoria
     * @return INT $categoria_id = id da categoria informada
     */
    public static function CatByName($CategoryName) {
        $read = new Read;
        $read->ExeRead('mb_categorias', "WHERE categoria_nome = :nome", "nome={$CategoryName}");
        if ($read->getRowCount()):
            return $read->getResult()[0]['categoria_id'];
        else:
            echo "A categoria {$CategoryName} não foi encontrada!";
            die;
        endif;
    }

    /**
     * <b>Usuários Online:</b> Ao executar este HELPER, ele automaticamente deleta os usuários expirados. Logo depois
     * executa um READ para obter quantos usuários estão realmente online no momento!
     * @return INT = Qtd de usuários online
     */
    public static function UserOnline() {
        $now = date('Y-m-d H:i:s');
        $deleteUserOnline = new Delete;
        $deleteUserOnline->ExeDelete('mb_siteviews_online', "WHERE online_endview < :now", "now={$now}");

        $readUserOnline = new Read;
        $readUserOnline->ExeRead('mb_siteviews_online');
        return $readUserOnline->getRowCount();
    }
	
	/**
     * <b>Obter Cidade:</b> Informe o id de uma cidade para obter os dados da mesma.
     * @return INT $CidadeId = id da cidade informada
     */
    public static function CidadeByName($CidadeId) {
        $read = new Read;
        $read->ExeRead('app_cidades', "WHERE cidade_id = :cidId", "cidId={$CidadeId}");
        if ($read->getRowCount()):
            return $read->getResult()[0];
        else:
            echo "O ID {$CidadeId} da cidade não foi encontrado!";
            die;
        endif;
    }
	
	/**
     * <b>Obter Estado:</b> Informe o id do estado para obter os dados do mesmo.
     * @return INT $EstadoId = id do estado informado
     */
    public static function EstadoByName($EstadoId) {
        $read = new Read;
        $read->ExeRead('app_estados', "WHERE estado_id = :estId", "estId={$EstadoId}");
        if ($read->getRowCount()):
            return $read->getResult()[0];
        else:
            echo "O ID {$EstadoId} do estado não foi encontrado!";
            die;
        endif;
    }

    /**
     * <b>Imagem Upload:</b> Ao executar este HELPER, ele automaticamente verifica a existencia da imagem na pasta
     * uploads. Se existir retorna a imagem redimensionada!
     * @return HTML = imagem redimencionada!
     */
    public static function Image($ImageUrl, $ImageDesc, $ImageW = null, $ImageH = null) {

        self::$Data = $ImageUrl;

        if (file_exists(self::$Data) && !is_dir(self::$Data)):
            $patch = HOME;
            $imagem = self::$Data;
            return "<img src=\"{$patch}/tim.php?src={$patch}/{$imagem}&w={$ImageW}&h={$ImageH}\" alt=\"{$ImageDesc}\" title=\"{$ImageDesc}\"/>";
        else:
            return false;
        endif;
    }

}
