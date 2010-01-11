<?php
/**
 * Module NAF (News/Articles/FAQS).
 * This module used for all News-like modules
 *
 * @see EngineCore.php
 * @author AlexK
 * @version 1.0
 */
class ModuleNAFExt extends ModuleNAF {

    /**
     * Constructor of class ContentExt
     */
    public function  __construct() {
        ;
    }

    /**
     * Return list of NAF list
     * i.e. list of News/Articles/FAQS headers and dates
     *
     * @param   nothing
     * @throws  taken from ModuleNaf
     * @return  text1 and text2 - if glossary, otherwise use standert
     */
    public function getList(){

        if ($this->isSingleDisplay()){
            return "";
        }

        if ($this->AttributeOperations->getModuleName() == 'glossary'){

            $table      = 'naf_glossary';
            $limit      = '30';

            $this->MySQL->query("select `id`, `text1`, `text2`, `date` from $table where `active` = '1' order by `id` desc limit $limit");

            while ($row = $this->MySQL->fetchArray()){

                $result[] = array('text1'               => $row['text1'],
                                  'text2'               => $row['text2'],
                                  'date'                => $row['date'],
                                  'url'                 => "nafId={$row['id']}"
                                 );
            }

            $this->MySQL->freeResult();

            return $result;

        } else {

            return parent::getList();

        }
    }
}
?>
