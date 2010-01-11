<?php
/**
 * Main class of Mart's engine
 * used singleton design pattern
 *
 * @package     Engine
 * @subpackage  Engine
 * @link        http://ru.wikipedia.org/wiki/%D0%A4%D0%B0%D1%81%D0%B0%D0%B4_(%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD_%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
 * @link        http://ru.wikipedia.org/wiki/%D0%9E%D0%B4%D0%B8%D0%BD%D0%BE%D1%87%D0%BA%D0%B0_(%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD_%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
 * @link        http://ru.wikipedia.org/wiki/%D0%A8%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD_%D0%B4%D0%B5%D0%BB%D0%B5%D0%B3%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F
 * @link        http://ru.wikipedia.org/wiki/Composite
 * @author      AlexK
 * @version     1.0
 */
class EngineCore {
    private static $instance    = null;
    private $Smarty             = null;
    private $InputFilter        = null;
    private $MySQL              = null;
    private $SiteStructure      = null;
    private $Session            = null;
    private $AttributeOperations= null;

    private $Page               = null;

    /**
     * Constructor of class EngineCore
     */
    public function  __construct() {

        try {

            $this->InputFilter          = new InputFilter();
            $this->MySQL                = new MySQL();
            $this->Smarty               = new SmartyExt($this->InputFilter);
            $this->Session              = new Session($this->MySQL, $this->InputFilter, $this->Smarty);
            $this->SiteStructure        = new SiteStructure($this->MySQL, $this->InputFilter, $this->Session);
            $this->AttributeOperations  = new AttributeOperations($this->MySQL, $this->SiteStructure);

            $this->Page                 = new Page($this->Smarty, $this->MySQL, $this->InputFilter, $this->SiteStructure, $this->Session, $this->AttributeOperations);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }
    }

    /**
     * Static function, used for creation instance of EngineCore class.
     *
     * @param   nothing
     * @throws
     * @return  reference to class instance
     */
    public static function singleton(){
        if (!isset(self::$instance)){
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Set independent module handler
     *
     * @param   $Module new module reference
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function setModuleHandler(&$Module) {

        $this->Page->setModuleHandler($Module);

        return true;

    }

    /**
     * Declare new, extended version of PageBuilder class
     *
     * @param   $PageBuilderExt reference to extended PageBuilder class
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function setExtendedPageHandler(&$PageBuilderExt){

        $this->Page->setExtendedPageHandler($PageBuilderExt);

        return true;
    }

    /**
     * Declare new, extended version of ModuleNAF class
     *
     * @param   $ModuleNAFExt reference to extended ModuleNAF class
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function setExtendedModuleNAFHandler(&$ModuleNAFExt){

        $this->Page->setExtendedModuleNAFHandler($ModuleNAFExt);

        return true;
    }

	/**
     * Declare new, extended version of ModuleCatalogue class
     *
     * @param   $ModuleCatalogueExt reference to extended ModuleCatalogue class
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function setExtendedModuleCatalogueHandler(&$ModuleCatalogueExt){

        $this->Page->setExtendedModuleCatalogueHandler($ModuleCatalogueExt);

        return true;
    }

	/**
     * Declare new, extended version of ModuleFeedback class
     *
     * @param   $ModuleCatalogueExt reference to extended ModuleFeedback class
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function setExtendedModuleFeedbackHandler(&$ModuleFeedbackExt){

        $this->Page->setExtendedModuleFeedbackHandler($ModuleFeedbackExt);

        return true;
    }

    /**
     * Used for render page
     *
     * @param   nothing
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function RenderPage(){

        $this->Page->RenderPage();

        return true;
    }

    /**
     * Used for work with CKEditor page
     *
     * @param   nothing
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function saveCKEditorPicture(){

        $this->Page->saveCKEditorPicture();

        return true;
    }

    /**
     * Used for work with CKEditor page
     *
     * @param   nothing
     * @throws  taken from Page class
     * @return  true if ok
     */
    public function browseCKEditorPicture(){

        $this->Page->browseCKEditorPicture();

        return true;
    }
}
?>
