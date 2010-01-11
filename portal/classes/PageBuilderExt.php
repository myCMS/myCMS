<?php
/**
 * User defined class
 *
 * @see Content.php
 * @author Alex
 * @version 1.0
 */
class PageBuilderExt extends PageBuilder {

    /**
     * Constructor of class ContentExt
     */
    public function  __construct() {
        ;
    }

    /**
     * override of DefineTemplateParameters function
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function DefineTemplateParameters(){
        parent::DefineTemplateParameters();
        $this->Smarty->assign('additional_var',  'value1');
        $this->Smarty->assign('additional_var2', 'value2');
    }
}
?>
