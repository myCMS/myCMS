<?php

require_once(TYPOGRAPH_LIBRARY_ROOT.'PrideTypograph.php');

/**
 * Typograph class
 *
  * @category	Typograph
 * @package 	Engine
 * @subpackage  Typograph
 * @link        http://emuravjev.ru/works/tg/
 * @version     1.0
 */
class Typograph extends PrideTypograph
{
	/**
	 * Type of adapter of rules for typograph
	 * 
	 * Allowed types - 'xml' and 'array'
	 * 
	 * @var 	string
	 */
	private $handler = 'array';
	
	/**
	 * Parameters of selected adapter
	 *
	 * @var 	string
	 */
	private $handlerOptions = array();

	/**
	 * Typograph link
	 *
	 * @var 	object
	 */
    private $Typograph  = null;
	
	/**
	 * Constractor of Typograph class
     *
	 * @return 	void
	 */
	public function __construct()
	{
        //ini_set('pcre.backtrack_limit',90000000000);
        //ini_set('pcre.recursion_limit',90000000000);

        ini_set("mbstring.language","Russian");
        ini_set("mbstring.internal_encoding","UTF-8");
        ini_set("mbstring.http_input","auto");
        ini_set("mbstring.http_output","UTF-8");

		/**
		 * Файлы с правилами как в формате XML, так и в виде массивов,
		 * можно найти в директории 'resources', которая включена в дистрибутив
		 */
		switch ($this->handler) {
			/**
			 * Пути к файлам в формате XML, которые идут в поставке с дистрибутивом.
			 */
			case 'xml':
				$this->handlerOptions = array( 	'xml_regex_file' 	=>  TYPOGRAPH_RESOURCES_ROOT.'Xml/RulesRegex.xml',
												'xml_replace_file' 	=>  TYPOGRAPH_RESOURCES_ROOT.'Xml/RulesReplace.xml',
												'xml_clean_file' 	=>  TYPOGRAPH_RESOURCES_ROOT.'Xml/CleanHtml.xml' );
				break;
			/**
			 * Можно передавать путь к файлам, поставляемым в комплекте с дистрибутивом,
			 * или же передавать переменную с этими массивами (на случай, если правила хранятся
			 * не в файлах, а в базе данных)
			 */
			case 'array':
				$this->handlerOptions = array( 	'array_regex_array' 	=> require TYPOGRAPH_RESOURCES_ROOT.'Php/RulesRegex.php',
												'array_replace_array' 	=> require TYPOGRAPH_RESOURCES_ROOT.'Php/RulesReplace.php',
												'array_clean_array' 	=> require TYPOGRAPH_RESOURCES_ROOT.'Php/CleanHtml.php', );
				break;
			/**
			 * Иные типы хранения данных нами, увы, не поддерживаются.
			 * Но это только пока :)
			 */
			default:
				throw new ExceptionExt("Incorrect handler type - '$this->handler'.");
				break;
		}
        
        $this->Typograph = parent::factory($this->handler, $this->handlerOptions);

        return true;

	}

    /**
     * Typoraph received text
     *
     * @param   $text text for parse
     * @throws  if text is too large
     * @return  typographed text
     */
    public function parse($text){

        $text = htmlspecialchars_decode($text, ENT_QUOTES);

        $typoText = $this->Typograph->parse($text);
        
        if (empty($typoText)){
            throw new ExceptionExt("Typograph overload - text too large");
        }

        return htmlspecialchars($typoText, ENT_QUOTES);

    }	
}
?>