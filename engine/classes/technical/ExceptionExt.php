<?php
/**
 * Extention of standart Exception class
 *
 * @package     Engine
 * @subpackage  Engine
 * @link        http://www.php.net/manual/ru/language.exceptions.php
 * @author      AlexK
 * @version     1.0
 */
class ExceptionExt extends Exception{

    /**
     * Constructor of class ExceptionExt
     */
    public function  __construct($message, $code = 0) {

        parent::__construct($message, $code);

    }

    /**
     * Override parent method __toString()
     * @return  formated string with exception information
     */
    public function __toString() {
        if (DISPLAY_PHP_ERRORS){

            return  "<br><br>Exception in <small>'{$this->file}'</small> at line <small>{$this->line}</small>, message: <b>{$this->message}</b> (code:{$this->code})".
                    "<br><br>Stack trace:<br><pre>{$this->getTraceAsString()}</pre>";
            
        } else {

            return "";
            
        }
    }
}
?>
