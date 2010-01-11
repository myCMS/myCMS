<?php

require_once(SMARTY_LIB_FOLDER.'Smarty.class.php');

/**
 * Extention of Smatry lib class
 *
 * @package     Engine
 * @subpackage  Smarty
 * @see         Smarty.class.php
 * @author      AlexK
 * @version     1.0
 */

class SmartyExt extends Smarty {

    private $InputFilter    = null;

    private $isDebug        = 0;
    private $fd             = 0;
    private $filename       = '';

    /**
     * Constructor of class SmartyExt
     *
     * Contains call of parent Smarty class
     * and setup Smarty dirs/params
     */
    public function __construct(InputFilter $InputFilter)
    {
        $this->Smarty();

        $this->InputFilter  = $InputFilter;

        $this->template_dir = SMARTY_TEMPLATES;
        $this->compile_dir  = SMARTY_TEMPLATES_C;
        $this->config_dir   = SMARTY_CONFIGS;
        $this->cache_dir    = SMARTY_CACHE;

        $this->compile_check = (boolean)!ENABLE_CACHING; //enable/disable compile check - rebuild cache if compile template is changed

        $this->caching      = ENABLE_CACHING;       //enable/disable caching
        $this->debugging    = ENABLE_SMARTY_DEBUG;  //enable/disable debug window for Smarty
        $this->cache_lifetime = SMARTY_CACHE_LIFETIME;  //setup cache filetime

        $this->isDebug = 0;

        if (ENABLE_DEBUG){

            if($this->InputFilter->getParameter("debug")){

                $this->isDebug = 1;
                $this->filename = $this->InputFilter->getParameter("filename");

            }
        }
    }

    /**
     * Override of parent's assign method
     * @param $key      tag name from template
     * @param $value    value will be assigned to $key tag into template
     */
    function assign($key, $value)
    {
        if ($this->isDebug)
        {
            $value = str_replace('|', '||', $value);
            $value = preg_replace('/\n/', '', $value);

            $value = serialize($value);
            $str = "$key|$value\n";
            
            $this->openFile();

            fwrite($this->fd, $str);

            flush();

        }
        else
        {
            parent::assign($key, $value);
        }
    }

    /**
     * Override of parent's assign method
     * @param $templateName template which will be displayed
     */
    function display($templateName, $cache_id = null)
    {
        if ($this->isDebug)
        {
            $this->closeFile();

            print "finished ".$this->filename."<br>";
        }
        else
        {
            parent::display($templateName, $cache_id);
        }
    }

    /**
     * Open test file for write
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function openFile(){

        if (empty($this->fd)){

            $this->fd = fopen(PROJECT_ROOT . "site/testing/tmp_lite/".$this->filename, "w");

            if (empty($this->fd)){
                print "can't open file:".PROJECT_ROOT . "site/testing/tmp_lite/{$this->filename} for write<br>\n";
            }
        }
    }

    /**
     * Close test file for write
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function closeFile(){

        if(!empty($this->fd)){

            fclose($this->fd);
            $this->fd = 0;

        }
    }
}
?>