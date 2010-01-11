<?php
/**
 * Pride Library: Typograph Factory
 * 
 * @copyright   (c) 2008 emuravjev.ru, <mail@emuravjev.ru>
 * @author      Артур Русаков <arthur@pridedesign.ru>
 * @version     1.7
 */

/**
 * Pride_Typograph
 *
 * @category    Pride
 * @package 	Pride_Typograph
 */
class PrideTypograph
{
	/**
	 * Фабричный метод
	 *
	 * @param 	string $handler имя адаптера работы с правилами
	 * @param 	array $options массив опций для выбранного адаптера
	 * @throws 	Pride_Typograph_Exception
	 * @return 	Pride_Typograph_RuleHandler_Abstract
	 */
	public static function factory($handler, array $options)
    {
    	$handlerName = (string) $handler;
    	$handlerName = ucfirst(strtolower($handlerName));
    	
    	if ('' === $handlerName) {
    		throw new ExceptionExt('Incorrect rule handler name');
    	}
    	
    	if (!count($options)) {
    		throw new ExceptionExt('Options is empty');
    	}
    	
    	$handlerFile = TYPOGRAPH_LIBRARY_ROOT."Typograph/RuleHandler/$handlerName.php";
    	$handlerClass = "Pride_Typograph_RuleHandler_$handlerName";
    	
    	require_once $handlerFile;
    	
    	if (!class_exists($handlerClass, false)) {
    		throw new ExceptionExt('Class not exists');
    	}
    	
    	$ruleHandler = new $handlerClass($options);
    	
    	if (!$ruleHandler instanceof Pride_Typograph_RuleHandler_Abstract) {
    		throw new ExceptionExt('Handler class must be extend Pride_Typograph_RuleHandler_Abstract');
        }
    	
        return $ruleHandler;
    }
}