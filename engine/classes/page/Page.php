<?php
/**
 * Creates all modules and Menu/Content/Random/StaticContent classes
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class Page {

    private   $Smarty               = null;
    private   $MySQL                = null;
    private   $InputFilter          = null;
    private   $SiteStructure        = null;
    private   $Session              = null;
    private   $AttributeOperations  = null;

    private   $PageBuilder          = null;
    private   $PageBuilderExt       = null;
    private   $Menu                 = null;
    private   $Content              = null;
    private   $Random               = null;
    private   $StaticContent        = null;
    private   $AdminPanel           = null;

    private   $ModuleNAF            = null;
    private   $ModuleNAFExt         = null;
    private   $ModuleCatalogue      = null;
    private   $ModuleCatalogueExt   = null;
	private   $ModuleGallery        = null;
	private   $ModuleGalleryExt     = null;

    private   $ModuleFeedback       = null;
    private   $ModuleVote           = null;
    private   $ModuleCounter        = null;
    private   $ModuleSearch         = null;
    private   $ModuleFiles          = null;
    private   $ModuleStatic         = null;

    private   $ModulePlasticine     = null;

    /**
     * Constructor of class Page
     */
    public function  __construct(SmartyExt $Smarty, MySQL $MySQL, InputFilter $InputFilter, SiteStructure $SiteStructure, Session $Session, AttributeOperations $AttributeOperations) {

        $this->Smarty               = $Smarty;
        $this->MySQL                = $MySQL;
        $this->InputFilter          = $InputFilter;
        $this->SiteStructure        = $SiteStructure;
        $this->Session              = $Session;
        $this->AttributeOperations  = $AttributeOperations;

        $this->Menu                 = new Menu($this->SiteStructure);
        $this->Content              = new Content($this->MySQL, $this->SiteStructure);
        $this->Random               = new Random($this->MySQL, $this->SiteStructure);
        $this->StaticContent        = new StaticContent();
        $this->AdminPanel           = new AdminPanel($this->SiteStructure, $this->Session);

        $this->ConstructModules();

        $this->PageBuilder          = new PageBuilder($this->Smarty, $this->MySQL, $this->InputFilter, $this->SiteStructure, $this->Session, $this->AttributeOperations, $this->Menu, $this->Content, $this->Random, $this->StaticContent, $this->AdminPanel, $this->ModuleNAF, $this->ModuleCatalogue, $this->ModuleGallery, $this->ModuleFeedback, $this->ModuleVote, $this->ModuleCounter, $this->ModuleSearch, $this->ModuleFiles, $this->ModuleStatic);

    }

    /**
     * Enable other spec modules
     *
     * @param   nothing
     * @throws  if any error in modules
     * @return  nothing
     */
    private function ConstructModules(){

        $blocks = $this->SiteStructure->getPageBlocks();

        /*** Naf Module ***/
        if (NAF_MODULE_ENABLE){

            if ($this->AttributeOperations->getModuleType() == "naf" || NAF_LATEST_ELEMENTS_DISPLAY){

                $this->ModuleNAF = new ModuleNAF($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter, $this->Session);

            }
        }

        /*** Catalogue Module ***/
        if (CATALOGUE_MODULE_ENABLE){

            if ($this->AttributeOperations->getModuleName() == "catalogue" || $this->AttributeOperations->getModuleName() == "invcatalogue" || CATALOGUE_DISPLAY_MENU_ALWAYS || CATALOGUE_MODULE_DISPLAY_LATEST_PRODUCTS || CATALOGUE_MODULE_DISPLAY_RANDOM_PRODUCTS){

                $this->ModuleCatalogue = new ModuleCatalogue($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter, $this->Session);

            }
        }

        /*** Gallery Module ***/
        if (GALLERY_MODULE_ENABLE){

            if ($this->AttributeOperations->getModuleName() == "gallery" || GALLERY_DISPLAY_MENU_ALWAYS){

                $this->ModuleGallery = new ModuleGallery($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter);

            }
        }

        /*** Feedback Module ***/
        if (FEEDBACK_MODULE_ENABLE){

            foreach($blocks as $block){

                if (preg_match("/feedback/i", $block)){

                    $this->ModuleFeedback = new ModuleFeedback($this->SiteStructure, $this->InputFilter, $this->Smarty);

                    break;
                }
            }
        }

        /*** Vote Module ***/
        if (VOTE_MODULE_ENABLE){

            if ($this->AttributeOperations->getModuleName() == "vote"){

                $this->ModuleVote = new ModuleVote($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->Session);

            } else {

                foreach($blocks as $block){

                    if (preg_match("/vote/i", $block)){

                        $this->ModuleVote = new ModuleVote($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->Session);

                        break;
                    }
                }
            }
        }

        /*** Counter Module ***/
        if (USE_COUNTER){

            $this->ModuleCounter = new ModuleCounter($this->MySQL, $this->InputFilter);

        }

        /*** Search Module ***/
        if (SEARCH_ENABLE){

            if ($this->AttributeOperations->getModuleName() == "search"){

                $this->ModuleNAF        = new ModuleNAF($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter, $this->Session);
                $this->ModuleCatalogue  = new ModuleCatalogue($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter, $this->Session);
                //$this->ModuleGallery    = new ModuleGallery($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter);

                $this->ModuleSearch     = new ModuleSearch($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->ModuleNAF, $this->ModuleCatalogue, $this->ModuleGallery);

            }
        }

        /*** Files Module ***/
        if (FILES_MODULE_ENABLE){

            if ($this->AttributeOperations->getModuleName() == "files"){
                $this->ModuleFiles          = new ModuleFiles($this->Session, $this->SiteStructure, 1);
            } else {
                $this->ModuleFiles          = new ModuleFiles($this->Session, $this->SiteStructure, 0);
            }
        }

        /*** Static Module ***/
        if (defined("STATIC_MODULE_ENABLE") && STATIC_MODULE_ENABLE){
            if ($this->AttributeOperations->getModuleName() == "static"){
                $this->ModuleStatic = new ModuleStatic($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->Session);
            }
        }
    }

    /**
     * Set independent module handler
     *
     * @param   $Module new module reference
     * @throws  if Module is not an object
     * @return  nothing
     */
    public function setModuleHandler($Module) {

        if (!is_object($Module)){
            throw new ExceptionExt('Module Plasticine is not an object');
        }

        $this->ModulePlasticine = $Module;

        $this->ModulePlasticine->setClassesHandlers($this->Smarty, $this->MySQL, $this->InputFilter, $this->SiteStructure, $this->Session, $this->AttributeOperations);

        if ($this->PageBuilderExt == null){

            $this->PageBuilder->setModuleHandler($this->ModulePlasticine);

        } else {

            $this->PageBuilderExt->setModuleHandler($this->ModulePlasticine);

        }

    }

    /**
     * Declare new, extended version of PageBuilder class
     *
     * @param   $PageBuilderExt reference to extended content class
     * @throws  if Page Builder Extention is not a child of PageBuilder class or link incorrect
     * @return  nothing
     */
    public function setExtendedPageHandler(&$PageBuilderExt){

        if ($PageBuilderExt == null){
            throw new ExceptionExt('Extended Page handler is null');
        }

        if (!is_object($PageBuilderExt)){
            throw new ExceptionExt('Extended Page handler not is object');
        }

        if (get_parent_class($PageBuilderExt) != 'PageBuilder'){
            throw new ExceptionExt('Extended Page handler points to class, not extends Content');
        }

        $this->PageBuilderExt = $PageBuilderExt;

        $this->PageBuilderExt->setClassesHandlers($this->Smarty, $this->MySQL, $this->InputFilter, $this->SiteStructure, $this->Session, $this->AttributeOperations, $this->Menu, $this->Content, $this->Random, $this->StaticContent, $this->AdminPanel, $this->ModuleNAF, $this->ModuleCatalogue, $this->ModuleGallery, $this->ModuleFeedback, $this->ModuleVote, $this->ModuleCounter, $this->ModuleSearch, $this->ModuleFiles, $this->ModuleStatic);

    }

    /**
     * Declare new, extended version of ModuleNAF class
     *
     * @param   $ModuleNAFExt reference to extended content class
     * @throws  if Module Naf Extention is not a child of ModuleNAF class or link incorrect
     * @return  nothing
     */
    public function setExtendedModuleNAFHandler(&$ModuleNAFExt){

        if ($ModuleNAFExt == null){
            throw new ExceptionExt('Extended Module NAF handler is null');
        }

        if (!is_object($ModuleNAFExt)){
            throw new ExceptionExt('Extended Module NAF handler not is object');
        }

        if (get_parent_class($ModuleNAFExt) != 'ModuleNAF'){
            throw new ExceptionExt('Extended Module NAF handler points to class, not extends Content');
        }

        $this->ModuleNAFExt = $ModuleNAFExt;

        $this->ModuleNAFExt->setClassesHandlers($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->AttributeOperations, $this->Session);


        if ($this->PageBuilderExt == null){

            $this->PageBuilder->setModuleNAFClassHandler($this->ModuleNAFExt);

        } else {

            $this->PageBuilderExt->setModuleNAFClassHandler($this->ModuleNAFExt);

        }
    }

	/**
     * Declare new, extended version of ModuleCatalogue class
     *
     * @param   $ModuleCatalogueExt reference to extended content class
     * @throws  if Module Catalogue Extention is not a child of PageBuilder class or link incorrect
     * @return  nothing
     */
    public function setExtendedModuleCatalogueHandler(&$ModuleCatalogueExt){

        if ($ModuleCatalogueExt == null){
            throw new ExceptionExt('Extended Module Catalogue handler is null');
        }

        if (!is_object($ModuleCatalogueExt)){
            throw new ExceptionExt('Extended Module Catalogue handler not is object');
        }

        if (get_parent_class($ModuleCatalogueExt) != 'ModuleCatalogue'){
            throw new ExceptionExt('Extended Module Catalogue handler points to class, not extends Content');
        }

        $this->ModuleCatalogueExt = $ModuleCatalogueExt;

		$this->ModuleCatalogueExt->setClassesHandlers($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->AttributeOperations, $this->Session);

        if ($this->PageBuilderExt == null){

            $this->PageBuilder->setModuleCatalogueClassHandler($this->ModuleCatalogueExt);

        } else {

            $this->PageBuilderExt->setModuleCatalogueClassHandler($this->ModuleCatalogueExt);

        }
    }

	/**
     * Declare new, extended version of ModuleGallery class
     *
     * @param   $ModuleGalleryExt reference to extended content class
     * @throws  if Module Gallery Extention is not a child of PageBuilder class or link incorrect
     * @return  nothing
     */
    public function setExtendedModuleGalleryHandler(&$ModuleGalleryExt){

        if ($ModuleGalleryExt == null){
            throw new ExceptionExt('Extended Module Gallery handler is null');
        }

        if (!is_object($ModuleGalleryExt)){
            throw new ExceptionExt('Extended Module Gallery handler not is object');
        }

        if (get_parent_class($ModuleGalleryExt) != 'ModuleGallery'){
            throw new ExceptionExt('Extended Module Gallery handler points to class, not extends Content');
        }

        $this->ModuleGalleryExt = $ModuleGalleryExt;

        $this->ModuleGalleryExt->setClassesHandlers($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->AttributeOperations);


        if ($this->PageBuilderExt == null){

            $this->PageBuilder->setModuleGalleryClassHandler($this->ModuleGalleryExt);

        } else {

            $this->PageBuilderExt->setModuleGalleryClassHandler($this->ModuleGalleryExt);

        }
    }


    public function setExtendedModuleFeedbackHandler(&$ModuleFeedbackExt){

		if ($this->ModuleFeedback == null) {
			return true;  //if module feedback is null - it not used
		}

        if ($ModuleFeedbackExt == null){
            throw new ExceptionExt('Extended Module Feedback handler is null');
        }

        if (!is_object($ModuleFeedbackExt)){
            throw new ExceptionExt('Extended Module Feedback handler not is object');
        }

        if (get_parent_class($ModuleFeedbackExt) != 'ModuleFeedback'){
            throw new ExceptionExt('Extended Module Feedback handler points to class, not extends Content');
        }

        $this->ModuleFeedbackExt = $ModuleFeedbackExt;

        $this->ModuleFeedbackExt->setClassesHandlers($this->SiteStructure, $this->InputFilter, $this->Smarty);

        if ($this->PageBuilderExt == null){

            $this->PageBuilder->setModuleFeedbackClassHandler($this->ModuleFeedbackExt);

        } else {

            $this->PageBuilderExt->setModuleFeedbackClassHandler($this->ModuleFeedbackExt);

        }
    }

    /**
     * Used for render page
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function RenderPage(){

        if ($this->PageBuilderExt == null){

            $this->PageBuilder->PerformAjax();
            $this->PageBuilder->DefineTemplateParameters();
            $this->PageBuilder->DisplayTemplate();

        } else {

            $this->PageBuilderExt->PerformAjax();
            $this->PageBuilderExt->DefineTemplateParameters();
            $this->PageBuilderExt->DisplayTemplate();

        }
    }

    /**
     * Used for work with CKEditor page
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function saveCKEditorPicture(){

        if ($this->ModuleNAFExt !== null){

            $this->ModuleNAFExt->saveCKEditorPicture();

        } else if ($this->ModuleNAF !== null) {

            $this->ModuleNAF->saveCKEditorPicture();

        } else if ($this->ModuleCatalogueExt !== null) {

            $this->ModuleCatalogueExt->saveCKEditorPicture();

        } else if ($this->ModuleCatalogue !== null) {

            $this->ModuleCatalogue->saveCKEditorPicture();

        }
    }

    /**
     * Used for work with CKEditor page
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function browseCKEditorPicture(){

        if ($this->ModuleNAFExt !== null){

            $this->ModuleNAFExt->browseCKEditorPicture();

        } else if ($this->ModuleNAF !== null) {

            $this->ModuleNAF->browseCKEditorPicture();

        } else if ($this->ModuleCatalogueExt !== null) {

            $this->ModuleCatalogueExt->browseCKEditorPicture();

        } else if ($this->ModuleCatalogue !== null) {

            $this->ModuleCatalogue->browseCKEditorPicture();

        }
    }
}
?>
