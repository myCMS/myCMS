<?php

require_once(ENGINE_CONFIG_ROOT.'MainConfig.php');
require_once(ENGINE_ROOT.'EngineCore.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'MySQL.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'SmartyExt.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'InputFilter.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'SiteStructure.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'SiteStructureItem.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'ExceptionExt.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'Session.php');
require_once(ENGINE_TECHNICAL_CLASSES_FOLDER.'AttributeOperations.php');

require_once(ENGINE_PAGE_CLASSES_FOLDER.'Page.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'PageBuilder.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'Content.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'Menu.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'Random.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'StaticContent.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'AdminPanel.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'Typograph.php');
require_once(ENGINE_PAGE_CLASSES_FOLDER.'ImageScale.php');

require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleGeneral.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'general/ModuleNAF.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'general/ModuleCatalogue.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'general/ModuleCatalogueItem.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'general/ModuleGallery.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'general/ModuleGalleryItem.php');

require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleFeedback.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleVote.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleCounter.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleSearch.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleFiles.php');
require_once(ENGINE_MODULE_CLASSES_FOLDER.'ModuleStatic.php');

/**
 * Front class of Mart's engine
 *
 * Wrapper of EngineCore class of Mart's engine
 *
 * @package     Engine
 * @subpackage  Engine
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 *
 */
class Engine {

    private $EngineCore = null;

    /**
     * Constructor of class Engine
     *
     * Contain creation of EngineCore class
     */
    public function  __construct() {

        try{

            if (DISPLAY_PHP_ERRORS){

                ini_set('display_errors',1);
                error_reporting(E_ALL);

            } else {

                ini_set('display_errors',0);
                error_reporting(0);

            }

            if ( false !== ini_set("session.cookie_lifetime", AUTHENTICATION_SESSION_LIFETIME)){
                //print "ini set";
            } else {
                //print "ini not set";
            }

            if (!extension_loaded("mbstring")){
                throw new ExceptionExt("mbstring extension not setup");
            }

            if (!extension_loaded("gd")){
                throw new ExceptionExt("GD extension not setup");
            }

            if (!extension_loaded("zip")){
                throw new ExceptionExt("zip extension not setup");
            }

            $this->EngineCore = EngineCore::singleton();

        } catch (ExceptionExt $e) {
            print $e;
        }
    }

    /**
     * Set independent module handler
     *
     * @param   $Module new module reference
     * @throws  taken from EngineCore
     * @return  true if ok
     */
    public function setModuleHandler(&$Module) {

        try {

            $this->EngineCore->setModuleHandler($Module);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }

        return true;
    }

    /**
     * Calls setExtendedContentHandler from EngineCore class
     *
     * @param   reference to class extended from Page
     * @throws  taken from EngineCore
     * @return  true if ok
     */
    public function setExtendedPageHandler(&$PageBuilderExt){

        try {

            $this->EngineCore->setExtendedPageHandler($PageBuilderExt);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }

        return true;
    }

    /**
     * Calls setExtendedModuleNAFHandler from EngineCore class
     *
     * @param   reference to class extended from ModuleNAF
     * @throws  taken from EngineCore
     * @return  true if ok
     */
    public function setExtendedModuleNAFHandler(&$ModuleNAFExt){

        try {

            $this->EngineCore->setExtendedModuleNAFHandler($ModuleNAFExt);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }

        return true;
    }

	/**
     * Calls setExtendedModuleCatalogueHandler from EngineCore class
     *
     * @param   reference to class extended from ModuleCatalogue
     * @throws  taken from EngineCore
     * @return  true if ok
     */
    public function setExtendedModuleCatalogueHandler(&$ModuleCatalogueExt){

        try {

            $this->EngineCore->setExtendedModuleCatalogueHandler($ModuleCatalogueExt);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }

        return true;
    }

	/**
     * Calls setExtendedModuleGalleryHandler from EngineCore class
     *
     * @param   reference to class extended from ModuleGallery
     * @throws  taken from EngineCore
     * @return  true if ok
     */
    public function setExtendedModuleGalleryHandler(&$ModuleGalleryExt){

        try {

            $this->EngineCore->setExtendedModuleGalleryHandler($ModuleGalleryExt);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }

        return true;
    }

	/**
     * Calls setExtendedModuleFeedbackHandler from EngineCore class
     *
     * @param   reference to class extended from ModuleFeedback
     * @throws  taken from EngineCore
     * @return  true if ok
     */
    public function setExtendedModuleFeedbackHandler(&$ModuleFeedbackExt){

        try {

            $this->EngineCore->setExtendedModuleFeedbackHandler($ModuleFeedbackExt);

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }

        return true;
    }

    /**
     * Calls RenderPage from EngineCore class
     *
     * @param   nothing
     * @throws  taken from EngineCore
     * @return  nothing
     */
    public function RenderPage(){

        try {

            $this->EngineCore->RenderPage();

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }
    }

    /**
     * Calls saveCKEditorPicture from EngineCore class
     *
     * @param   nothing
     * @throws  taken from EngineCore
     * @return  nothing
     */
    public function saveCKEditorPicture(){

        try {

            $this->EngineCore->saveCKEditorPicture();

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }
    }

    /**
     * Calls browseCKEditorPicture from EngineCore class
     *
     * @param   nothing
     * @throws  taken from EngineCore
     * @return  nothing
     */
    public function browseCKEditorPicture(){

        try {

            $this->EngineCore->browseCKEditorPicture();

        } catch (ExceptionExt $e) {
            print $e;
            exit(0);
        }
    }
}
?>