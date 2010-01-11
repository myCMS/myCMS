<?php
/**
 * Controller, displayed all content
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class PageBuilder {

    protected $Smarty               = null;
    protected $MySQL                = null;
    protected $InputFilter          = null;
    protected $SiteStructure        = null;
    protected $Session              = null;
    protected $AttributeOperations  = null;

    protected $Menu                 = null;
    protected $Content              = null;
    protected $Random               = null;
    protected $StaticContent        = null;
    protected $AdminPanel           = null;

    protected $ModuleNAF            = null;
    protected $ModuleCatalogue      = null;
	protected $ModuleGallery 		= null;

    protected $ModuleFeedback       = null;
    protected $ModuleVote           = null;
    protected $ModuleCounter        = null;
    protected $ModuleSearch         = null;
    protected $ModuleFiles          = null;
    protected $ModuleStatic         = null;
    protected $ModulePlasticine     = null;

    /**
     * Constructor of class PageBuilder
     */
    public function  __construct(SmartyExt $Smarty, MySQL $MySQL, InputFilter $InputFilter, SiteStructure $SiteStructure, Session $Session, AttributeOperations $AttributeOperations, Menu $Menu, Content $Content, Random $Random, StaticContent $StaticContent, AdminPanel $AdminPanel, ModuleNAF $ModuleNAF = null, ModuleCatalogue $ModuleCatalogue = null, ModuleGallery $ModuleGallery = null, ModuleFeedback $ModuleFeedback = null, ModuleVote $ModuleVote = null, ModuleCounter $ModuleCounter = null, ModuleSearch $ModuleSearch = null, ModuleFiles $ModuleFiles = null, ModuleStatic $ModuleStatic = null) {

        $this->Smarty               = $Smarty;
        $this->MySQL                = $MySQL;
        $this->InputFilter          = $InputFilter;
        $this->SiteStructure        = $SiteStructure;
        $this->Session              = $Session;
        $this->AttributeOperations  = $AttributeOperations;

        $this->Menu                 = $Menu;
        $this->Content              = $Content;
        $this->Random               = $Random;
        $this->StaticContent        = $StaticContent;
        $this->AdminPanel           = $AdminPanel;

        $this->ModuleNAF            = $ModuleNAF;
        $this->ModuleCatalogue      = $ModuleCatalogue;
        $this->ModuleGallery        = $ModuleGallery;

        $this->ModuleFeedback       = $ModuleFeedback;
        $this->ModuleVote           = $ModuleVote;
        $this->ModuleCounter        = $ModuleCounter;
        $this->ModuleSearch         = $ModuleSearch;
        $this->ModuleFiles          = $ModuleFiles;
        $this->ModuleStatic         = $ModuleStatic;
    }

    /**
     * Used for passing references to needed classes into PageBuilder class
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    final public function setClassesHandlers(SmartyExt $Smarty, MySQL $MySQL, InputFilter $InputFilter, SiteStructure $SiteStructure, Session $Session, AttributeOperations $AttributeOperations, Menu $Menu, Content $Content, Random $Random, StaticContent $StaticContent, AdminPanel $AdminPanel, ModuleNAF $ModuleNAF = null, ModuleCatalogue $ModuleCatalogue = null, ModuleGallery $ModuleGallery = null, ModuleFeedback $ModuleFeedback = null, ModuleVote $ModuleVote = null, ModuleCounter $ModuleCounter = null, ModuleSearch $ModuleSearch = null, ModuleFiles $ModuleFiles = null, ModuleStatic $ModuleStatic = null){

        $this->Smarty               = $Smarty;
        $this->MySQL                = $MySQL;
        $this->InputFilter          = $InputFilter;
        $this->SiteStructure        = $SiteStructure;
        $this->Session              = $Session;
        $this->AttributeOperations  = $AttributeOperations;

        $this->Menu                 = $Menu;
        $this->Content              = $Content;
        $this->Random               = $Random;
        $this->StaticContent        = $StaticContent;
        $this->AdminPanel           = $AdminPanel;

        $this->ModuleNAF            = $ModuleNAF;
        $this->ModuleCatalogue      = $ModuleCatalogue;
        $this->ModuleGallery        = $ModuleGallery;

        $this->ModuleFeedback       = $ModuleFeedback;
        $this->ModuleVote           = $ModuleVote;
        $this->ModuleCounter        = $ModuleCounter;
        $this->ModuleSearch         = $ModuleSearch;
        $this->ModuleFiles          = $ModuleFiles;
        $this->ModuleStatic         = $ModuleStatic;
    }

    /**
     * Add plasticine module
     *
     * @param   Module Naf Extention reference
     * @throws  no throws
     * @return  nothing
     */
    final public function setModuleHandler($ModulePlasticine){

        $this->ModulePlasticine = $ModulePlasticine;

    }

    /**
     * Replace Module Naf by extended vertion
     *
     * @param   Module Naf Extention reference
     * @throws  no throws
     * @return  nothing
     */
    final public function setModuleNAFClassHandler($ModuleNAFExt){

        $this->ModuleNAF = $ModuleNAFExt;

    }

    /**
     * Replace Module Catalogue by extended version
     *
     * @param   Module Catalogue Extention reference
     * @throws  no throws
     * @return  nothing
     */
    final public function setModuleCatalogueClassHandler($ModuleCatalogueExt){

        $this->ModuleCatalogue = $ModuleCatalogueExt;

    }

    /**
     * Replace Module Gallery by extended version
     *
     * @param   Module Gallery Extention reference
     * @throws  no throws
     * @return  nothing
     */
    final public function setModuleGalleryClassHandler($ModuleGalleryExt){

        $this->ModuleGallery = $ModuleGalleryExt;

    }

	/**
     * Replace Module Feedback by extended version
     *
     * @param   Module Feedback Extention reference
     * @throws  no throws
     * @return  nothing
     */
    final public function setModuleFeedbackClassHandler($ModuleFeedbackExt){

        $this->ModuleFeedback = $ModuleFeedbackExt;

    }

    /**
     * Check if ajax used - run needed modules
     *
     * @param   nothing
     * @throws  taken from new objects
     * @return  nothing
     */
    public function PerformAjax(){

        $action = "".$this->InputFilter->getParameter("ajax")."";

        $response = array("status" => "success");

        if (empty($action)){
            return true;
        }

        try {

            switch($this->AttributeOperations->getModuleType()){
                case "naf":

                    switch ($action){
                        case "add":
                            $this->ModuleNAF->addNafElement();
							$Used['Admin']              =       $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']   =       $this->AdminPanel->getAdminElementWrapperTemplateName();
                            $Naf['Used']['Single']      =       0;
                            $Naf['Content']             =       $this->ModuleNAF->getAdminLastModifiedData();
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Naf',        $Naf);
                            $this->Smarty->fetch( $this->ModuleNAF->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "edit":
                            $this->ModuleNAF->editNafElement();
                            $Used['Admin']                  =   $this->AdminPanel->isAdmin();
							$Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
                            if ($this->ModuleNAF->isSingleDisplay()){
                                $Naf['Used']['Single']      = 1;
                                $Naf['Used']['AddButton']   = 0;
								$a = $this->ModuleNAF->getAdminLastModifiedData();
                                $Naf['Single']              =   $a[0];
							} else {
                                $Naf['Used']['Single']      =   0;
                                $Naf['Content']             =   $this->ModuleNAF->getAdminLastModifiedData();
							}
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Naf',        $Naf);
                            $this->Smarty->fetch( $this->ModuleNAF->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "remove":
                            $this->ModuleNAF->removeNafElement();
                            break;
                        case "activity":
                            $this->ModuleNAF->inverseActivityNafElement();
                            break;
                        case "load":
                            $this->Smarty->assign('Element',    $this->ModuleNAF->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleNAF->getAdminWrapperTemplateName() );
                            break;
                        case "save":
                            $this->ModuleNAF->saveAdminElement();
                            $this->Smarty->assign('Element',    $this->ModuleNAF->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleNAF->getAdminEmptyTemplateName() );
                            break;
                        case "cancel":
                            $this->Smarty->assign('Element',    $this->ModuleNAF->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleNAF->getAdminEmptyTemplateName() );
                            break;
                        case "loadWindowAdd":
                            $this->Smarty->assign('AdminWindow',$this->ModuleNAF->getAdminWindow());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowEdit":
                            $this->Smarty->assign('AdminWindow',$this->ModuleNAF->getAdminWindowEdit());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowDelete":
                            $this->Smarty->assign('AdminWindow',$this->ModuleNAF->getAdminWindow());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
						case "page":
                            $Naf['Content'] = $this->ModuleNAF->getListByDateRange();
                            $this->Smarty->assign('Naf', $Naf);
                            $response["html"] = $this->Smarty->fetch( $this->ModuleNAF->getPageTemplateName() );
                            break;
						case "file":
                            list($newFileName,$size) = $this->ModuleNAF->saveUploadedFile();
                            $response["link"] = $newFileName;
                            $response["size"] = $size;
                            break;
                        case "additionalFileAdd":
                            list($newFileName,$size)= $this->ModuleNAF->saveAdditionalFile();
                            $response["link"] = $newFileName;
                            $response["size"] = $size;
                            break;
                        case "additionalFileRemove":
                            $this->ModuleNAF->removeAdditionalFile();
                            break;
                        case "additionalFileShow":
                            $this->Smarty->assign('Images',     $this->ModuleNAF->getImages());
                            if ($this->ModuleNAF->isQuickLoad()){
                                $response["html"] = $this->Smarty->fetch( $this->ModuleNAF->getAdminAdditionalPicturesQuickEditTemplateName() );
                            } else {
                                $response["html"] = $this->Smarty->fetch( $this->ModuleNAF->getAdminAdditionalPicturesFullEditTemplateName() );
                            }
                            break;
                    }
                    break;
            }

            switch($this->AttributeOperations->getModuleName()){
                case "catalogue":

                    switch ($this->InputFilter->getParameter("ajax").""){
                        case "add":
                            $this->ModuleCatalogue->addProductFull();
							$Used['Admin']                  =   $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
                            $Catalogue['Used']['Single']    =   0;
                            $Catalogue['Content']           =   $this->ModuleCatalogue->getAdminLastModifiedData();
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Catalogue',  $Catalogue);
                            $this->Smarty->fetch( $this->ModuleCatalogue->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "edit":
                            $this->ModuleCatalogue->editProductFull();
							$Used['Admin']                  =   $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
                            if ($this->ModuleCatalogue->isSingleDisplay()){
                                $Catalogue['Used']['Single']    = 1;
                                $Catalogue['Used']['AddButton'] = 0;
								$a = $this->ModuleCatalogue->getAdminLastModifiedData();
                                $Catalogue['Single']            =   $a[0];
							} else {
                                $Catalogue['Used']['Single']    =   0;
                                $Catalogue['Content']           =   $this->ModuleCatalogue->getAdminLastModifiedData();
							}
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Catalogue',  $Catalogue);
                            $this->Smarty->fetch( $this->ModuleCatalogue->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "remove":
                            $this->ModuleCatalogue->removeProducts();
                            break;
                        case "activity":
                            $this->ModuleCatalogue->inverseActivityNafElement();
                            break;
                        case "load":
                            $this->Smarty->assign('Element',    $this->ModuleCatalogue->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminWrapperTemplateName() );
                            break;
                        case "save":
                            $this->ModuleCatalogue->saveAdminElement();
                            $this->Smarty->assign('Element',    $this->ModuleCatalogue->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminEmptyTemplateName() );
                            break;
                        case "cancel":
                            $this->Smarty->assign('Element',    $this->ModuleCatalogue->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminEmptyTemplateName() );
                            break;
                        case "loadWindowAdd":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleCatalogue->getAdminWindowAdd());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowEdit":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleCatalogue->getAdminWindowEdit());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowDelete":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleCatalogue->getAdminWindow());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "file":
                            list($newFileName,$size) = $this->ModuleCatalogue->saveUploadedFile();
                            $response["link"] = $newFileName;
                            $response["size"] = $size;
                            break;
                        case "additionalFileAdd":
                            list($newFileName,$size)= $this->ModuleCatalogue->saveAdditionalFile();
                            $response["link"] = $newFileName;
                            $response["size"] = $size;
                            break;
                        case "additionalFileRemove":
                            $this->ModuleCatalogue->removeAdditionalFile();
                            break;
                        case "additionalFileShow":
                            $this->Smarty->assign('Images',     $this->ModuleCatalogue->getImages());
                            if ($this->ModuleCatalogue->isQuickLoad()){
                                $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminAdditionalPicturesQuickEditTemplateName() );
                            } else {
                                $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminAdditionalPicturesFullEditTemplateName() );
                            }
                            break;
                    }

                    break;

                case "invcatalogue":

                    switch ($this->InputFilter->getParameter("ajax").""){
                        case "add":
                            $this->ModuleCatalogue->addMenuElementFull();
							/*$Used['Admin']                  =   $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
                            $Catalogue['Used']['Single']    =   0;
                            $Catalogue['Content']           =   $this->ModuleCatalogue->getAdminLastModifiedData();
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Catalogue',  $Catalogue);
                            $this->Smarty->fetch( $this->ModuleCatalogue->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');*/
                            break;
                        /*case "edit":
                            $this->ModuleCatalogue->editProductFull();
							$Used['Admin']                  =   $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
                            if ($this->ModuleCatalogue->isSingleDisplay()){
                                $Catalogue['Used']['Single']    = 1;
                                $Catalogue['Used']['AddButton'] = 0;
								$a = $this->ModuleCatalogue->getAdminLastModifiedData();
                                $Catalogue['Single']            =   $a[0];
							} else {
                                $Catalogue['Used']['Single']    =   0;
                                $Catalogue['Content']           =   $this->ModuleCatalogue->getAdminLastModifiedData();
							}
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Catalogue',  $Catalogue);
                            $this->Smarty->fetch( $this->ModuleCatalogue->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "remove":
                            $this->ModuleCatalogue->removeProducts();
                            break;
                        case "activity":
                            $this->ModuleCatalogue->inverseActivityNafElement();
                            break;
                        case "load":
                            $this->Smarty->assign('Element',    $this->ModuleCatalogue->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminWrapperTemplateName() );
                            break;
                        case "save":
                            $this->ModuleCatalogue->saveAdminElement();
                            $this->Smarty->assign('Element',    $this->ModuleCatalogue->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminEmptyTemplateName() );
                            break;
                        case "cancel":
                            $this->Smarty->assign('Element',    $this->ModuleCatalogue->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminEmptyTemplateName() );
                            break;*/
                        case "loadWindowAdd":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleCatalogue->getAdminWindowAdd());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowEdit":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleCatalogue->getAdminWindowEdit());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        /*case "loadWindowDelete":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleCatalogue->getAdminWindow());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "file":
                            list($newFileName,$size) = $this->ModuleCatalogue->saveUploadedFile();
                            $response["link"] = $newFileName;
                            $response["size"] = $size;
                            break;
                        case "additionalFileAdd":
                            list($newFileName,$size)= $this->ModuleCatalogue->saveAdditionalFile();
                            $response["link"] = $newFileName;
                            $response["size"] = $size;
                            break;
                        case "additionalFileRemove":
                            $this->ModuleCatalogue->removeAdditionalFile();
                            break;
                        case "additionalFileShow":
                            $this->Smarty->assign('Images',     $this->ModuleCatalogue->getImages());
                            if ($this->ModuleCatalogue->isQuickLoad()){
                                $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminAdditionalPicturesQuickEditTemplateName() );
                            } else {
                                $response["html"] = $this->Smarty->fetch( $this->ModuleCatalogue->getAdminAdditionalPicturesFullEditTemplateName() );
                            }
                            break;*/
                    }

                    break;
				case "gallery":
                    $this->ModuleGallery        = new ModuleGallery($this->MySQL, $this->SiteStructure, $this->AttributeOperations, $this->InputFilter, $this->Session);
                    $this->AdminPanel           = new AdminPanel($this->SiteStructure, $this->Session);

                    switch ($this->InputFilter->getParameter("ajax").""){
                        case 'addPhoto':
                            $this->ModuleGallery->adminAddProduct();
                            $response = array(
                                                'status'  =>  'success'
                            );
                            break;
                        case 'removePhoto':
                            $this->ModuleGallery->adminRemoveProduct();
                            $response = array(
                                                'status'  =>  'success'
                            );
                            break;
                        case "add":
                            $this->ModuleGallery->addProductFull();
                            $this->Smarty->assign('use_admin',                    1);
                            $this->Smarty->assign('gallery_display_single_block', 0);
                            $this->Smarty->assign('gallery_header_blocks',        $this->ModuleGallery->getAdminLastModifiedData());
                            $this->Smarty->assign('admin_wrapper_template_name',  $this->AdminPanel->getAdminElementWrapperTemplateName());
                            $this->Smarty->fetch( $this->ModuleGallery->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "edit":
                            $this->ModuleGallery->editProductFull();
                            $this->Smarty->assign('admin_wrapper_template_name',        $this->AdminPanel->getAdminElementWrapperTemplateName());
                            if ($this->ModuleGallery->isSingleDisplay()){
								$this->Smarty->assign('use_admin',                      1);
								$this->Smarty->assign('gallery_display_single_block',   1);
								$a = $this->ModuleGallery->getAdminLastModifiedData();
								$this->Smarty->assign('gallery_single_block',           $a[0]);
							} else {
								$this->Smarty->assign('use_admin',                      1);
								$this->Smarty->assign('gallery_display_single_block',   0);
								$this->Smarty->assign('gallery_header_blocks',          $this->ModuleGallery->getAdminLastModifiedData());
							}
                            $this->Smarty->fetch( $this->ModuleGallery->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "remove":
                            $this->ModuleGallery->removeProducts();
                            break;
                        case "activity":
                            $this->ModuleGallery->inverseActivityNafElement();
                            break;
                        case "load":
                            $this->Smarty->assign('Element',    $this->ModuleGallery->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleGallery->getAdminWrapperTemplateName() );
                            break;
                        case "save":
                            $this->ModuleGallery->saveAdminElement();
                            $this->Smarty->assign('Element',    $this->ModuleGallery->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleGallery->getAdminEmptyTemplateName() );
                            break;
                        case "cancel":
                            $this->Smarty->assign('Element',    $this->ModuleGallery->getAdminLoadElement());
                            $response["html"] = $this->Smarty->fetch( $this->ModuleGallery->getAdminEmptyTemplateName() );
                            break;
                        case "loadWindowAdd":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleGallery->getAdminWindow());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowEdit":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleGallery->getAdminWindowEdit());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowDelete":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleGallery->getAdminWindow());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "file":
                            $newFileName = $this->ModuleGallery->saveUploadedFile();
                            $response["link"] = $newFileName;
                            break;
                    }

                    break;

                case "files":
                    switch($this->InputFilter->getParameter("ajax").""){
                        case "uploadPricelist":
                            $this->ModuleFiles->savePricelist();
                            $response["link"] = $this->ModuleFiles->getPricelist();
                            break;
                        case "removePricelist":
                            $this->ModuleFiles->removePricelist();
                            break;
                    }
                    break;

                case "vote":
                    switch($this->InputFilter->getParameter("ajax"."")){
                        case "add":
                            $this->ModuleVote->addElement();
							$Used['Admin']                  =   $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Vote',       $this->ModuleVote->getAdminLastModifiedData());
                            $this->Smarty->fetch( $this->ModuleVote->getAdminTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "edit":
                            $this->ModuleVote->editElement();
							$Used['Admin']                  =   $this->AdminPanel->isAdmin();
                            $Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
							$this->Smarty->assign('Used',       $Used);
							$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Vote',  $this->ModuleVote->getAdminLastModifiedData());
                            $this->Smarty->fetch( $this->ModuleVote->getAdminTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('admin');
                            break;
                        case "activity":
                            $this->ModuleVote->inverseActivityElement();
                            break;
                        case "remove":
                            $this->ModuleVote->removeElement();
                            break;
                        case "loadWindowAdd":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleVote->getAdminWindowAdd());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowEdit":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleVote->getAdminWindowEdit());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                    }
                    break;

                case "static":
                    switch ($this->InputFilter->getParameter("ajax"."")){

                        case "add":
                            $this->ModuleStatic->addElement();
							$Used['Admin']              =       $this->AdminPanel->isAdmin();
                            //$Template['AdminWrapper']   =       $this->AdminPanel->getAdminElementWrapperTemplateName();
                            //$Naf['Used']['Single']      =       0;
                            //$Naf['Content']             =       $this->ModuleStatic->getAdminLastModifiedData();
                            $this->SiteStructure->resetSiteStructureData();
                            $this->Smarty->assign('PageUrl',    $this->SiteStructure->getPageUrls());
							$this->Smarty->assign('Used',       $Used);
							//$this->Smarty->assign('Template',   $Template);
							$this->Smarty->assign('Deep',       1);
                            $this->Smarty->assign('Static',     $this->ModuleStatic->getAdminLastModifiedData());
                            $this->Smarty->fetch( $this->ModuleStatic->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('mapitem');
                            break;
                        case "edit":
                            $this->ModuleStatic->editElement();
                            $Used['Admin']                  =   $this->AdminPanel->isAdmin();
							/*$Template['AdminWrapper']       =   $this->AdminPanel->getAdminElementWrapperTemplateName();
                            if ($this->ModuleStatic->isSingleDisplay()){
                                $Naf['Used']['Single']      = 1;
                                $Naf['Used']['AddButton']   = 0;
								$a = $this->ModuleStatic->getAdminLastModifiedData();
                                $Naf['Single']              =   $a[0];
							} else {
                                $Naf['Used']['Single']      =   0;
                                $Naf['Content']             =   $this->ModuleStatic->getAdminLastModifiedData();
							}*/
                            $this->Smarty->assign('PageUrl',    $this->SiteStructure->getPageUrls());
							$this->Smarty->assign('Used',       $Used);
							//$this->Smarty->assign('Template',   $Template);
                            $this->Smarty->assign('Deep',       1);
                            $this->Smarty->assign('Static',     $this->ModuleStatic->getAdminLastModifiedData());
                            $this->Smarty->fetch( $this->ModuleStatic->getTemplateName() );
                            $response["html"] = $this->Smarty->get_template_vars('mapitem');
                            break;
                        case "remove":
                            $this->ModuleStatic->removeElement();
                            break;
                        case "activity":
                            $this->ModuleStatic->inverseActivityElement();
                            break;
                        case "mapItemRemove":
                            $this->ModuleStatic->removeOneElement();
                            break;
                        case "SaveMap":
                            $this->ModuleStatic->saveMap();
                            break;
                        case "loadWindowAdd":
                        case "mapItemAdd":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleStatic->getAdminWindowAdd());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                        case "loadWindowEdit":
                        case "mapItemEdit":
                            $this->Smarty->assign('AdminWindow',    $this->ModuleStatic->getAdminWindowEdit());
                            $response["html"] = $this->Smarty->fetch( $this->AdminPanel->getAdminWindowTemplateName() );
                            break;
                    }
                    break;
            }

            if ($this->InputFilter->getParameter("ajax")."" == "vote"){

                $this->ModuleVote = new ModuleVote($this->MySQL, $this->SiteStructure, $this->InputFilter, $this->Session);

                switch ($this->InputFilter->getParameter("action").""){
                    case 'getVotesList':
                        $this->Smarty->assign('votes', $this->ModuleVote->getAdminAllVotes());
                        $response["html"] = $this->Smarty->fetch( VOTE_ADMIN_AJAX_VOTES_TEMPLATE_NAME );
                        break;
                    case 'getAnswersList':
                        $this->Smarty->assign('vote_answers', $this->ModuleVote->getAdminAllVoteAnswers());
                        $response["html"] = $this->Smarty->fetch( VOTE_ADMIN_AJAX_ANSWERS_TEMPLATE_NAME );
                        break;
                    case 'addVote':
                        $this->ModuleVote->addAdminVote();
                        break;
                    case 'addAnswer':
                        $this->ModuleVote->addAdminAnswer();
                        break;
                    case 'removeVote':
                        $this->ModuleVote->removeAdminVote();
                        break;
                    case 'removeAnswer':
                        $this->ModuleVote->removeAdminAnswer();
                        break;
                    case 'saveVote':
                        $this->ModuleVote->saveAdminVoteItem();
                        break;
                    case 'saveAnswers':
                        $this->ModuleVote->saveAdminAnswerItem();
                        break;
                    default:
                        $this->ModuleVote->makeVote();
                        $this->Smarty->assign('vote', $this->ModuleVote->getVote());
                        $response["html"] = $this->Smarty->fetch( VOTE_MODULE_AJAX_TEMPLATE_NAME );
                }
			}

            $action = $this->InputFilter->getParameter("ajax")."";

            switch($action){
                case "authorizedForm":
                    $response["html"] = $this->Smarty->fetch( ADMIN_AUTHORIZE_TEMPLATE_NAME );
                    break;
                case "authorizedCheck":
                    $response["check"] = $this->Session->checkUserDetails();
                    break;
                case "registrationCheck":
                    $response["check"] = $this->Session->checkRegistrationDetails();
                    break;
            }

            switch($action){
                case "registration":
                    $this->Session->registration();
                    break;
                case "authentication":
                    $this->Session->authentication();
                    break;
                case "logout":
                    $this->Session->logout();
                    break;
                case "recover":
                    $this->Session->passwordRecover();
                    break;
                case "my":
                    $this->Session->editUserDetails();
                    break;
            }

            print json_encode($response);
            exit(0);

        } catch (ExceptionExt $e) {

            $response["status"] = "failed";
            $response["reason"] = $e->getMessage();
            print json_encode($response);
            exit(0);

        }
    }

    /**
     * Define template parameters
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function DefineTemplateParameters(){

        if (ENABLE_CACHING) {
            if ($this->SiteStructure->isMainPage()){

                if ($this->Smarty->is_cached( $this->SiteStructure->getMainPageTemplateName(),$this->SiteStructure->getPageUniqueId() )){
                    return true;
                }
            } else {

                if (!$this->AttributeOperations->isModuleUsed()){

                    if ($this->Smarty->is_cached( $this->SiteStructure->getPageTemplateName(),    $this->SiteStructure->getPageUniqueId() )){
                        return true;
                    }
                }
            }
        }

        $Block      = array();
        $Naf        = array();
        $Catalogue  = array();
        $Menu       = array();
        $Used       = array();
        $Template   = array();
        $Admin      = array();
        $Display    = array();

        $Block['Copyright'] = $this->StaticContent->getCopyright();

		/*** Module Search block ***/
        if ($this->ModuleSearch != null){

            $Template['Module'] = $this->ModuleSearch->getTemplateName();
            $Used['ModuleInside'] = 0;

            $this->Smarty->assign('Search',                     $this->ModuleSearch->getSearchResults());
            $this->Smarty->assign('module_name',                'search');

        }

		$Menu['Level1'] = $this->Menu->getTopLevelMenu();
		$Menu['Level2'] = $this->Menu->getSubMenu();
		$Menu['Level3'] = $this->Menu->getSub2Menu();

		$PageId   = array(
						'Current'           => $this->SiteStructure->getCurrentId(),
						'Parent'            => $this->SiteStructure->getParentId(),
						'Level1'            => $this->SiteStructure->getPageIdOnTopLevel(),
						'Level2'            => $this->SiteStructure->getPageIdOnSecondLevel(),
						'Level3'            => $this->SiteStructure->getPageIdOnThirdLevel()
			);

		$PageUrl  = $this->SiteStructure->getPageUrls();

		$Used['Facts']      = $this->Random->isUsedRandomQuestions();
		$Used['Image']      = $this->Random->isUsedRandomPicture();
		$Used['Authorized'] = $this->Session->isAuthorized();
        $Used['Admin']      = $this->AdminPanel->isAdmin();

		$Block['Facts']     = $this->Random->getRandomQuestion();
		$Block['Image']     = $this->Random->getRandomPicture();

		$Template['Facts']          = $this->Random->getRandomQuestionTemplateName();
		$Template['Image']          = $this->Random->getRandomPictureTemplateName();
		$Template['Level1']         = SMARTY_TEMPLATE_TOP_LEVEL_MENU_TEMPLATE_NAME;
		$Template['Level2']         = SMARTY_TEMPLATE_SUB_MENU_TEMPLATE_NAME;
		$Template['Level3']         = SMARTY_TEMPLATE_SUB2_MENU_TEMPLATE_NAME;
		$Template['AdminPanelMenu'] = $this->AdminPanel->getAdminPanelTemplateName();
		$Template['AdminWrapper']   = $this->AdminPanel->getAdminElementWrapperTemplateName();

		$Admin['PanelMenu']         = $this->AdminPanel->getAdminPanelTopMenu();

		/*** Site map block ***/
        if ($this->SiteStructure->isSiteMap()){

            $Used['SiteMap']     = 1;
            $Template['SiteMap'] = $this->SiteStructure->getSiteMapTemplateName();
            $Display['SiteMap']  = $this->SiteStructure->getSiteMap();

        } else {

            $Used['SiteMap'] = 0;

        }

		/*** Gallery random products ***/
		if ($this->ModuleGallery != null){
			$Used['Gallery']        = $this->ModuleGallery->isRandomPhotosDisplay();
			$Block['Gallery']       = $this->ModuleGallery->getRandomPhotos();
			$Template['Gallery']    = $this->ModuleGallery->getRandomPhotosTemplateName();
		}

		/*** Naf latest news ***/
        if ($this->ModuleNAF != null){
			$Used['NewsLatest']     = $this->ModuleNAF->isLatestListUsed();
			$Block['NewsLatest']    = $this->ModuleNAF->getLatestList();
			$Template['NewsLatest'] = $this->ModuleNAF->getLatestListTemplateName();
        }

        /*** External template ***/
		if ($this->SiteStructure->getExternalTemplate()) {
			$Template['External']   = $this->SiteStructure->getExternalTemplate();
			$Used['External']       = 1;
		} else {
			$Used['External']       = 0;
		}

        /*** Counter block ***/
        if ($this->ModuleCounter != null){
            $Block['Counter'] = $this->ModuleCounter->getCounter();
        }

        /*** Vote block ***/
        if ($this->ModuleVote != null){
            if ($this->AttributeOperations->getModuleName() == "vote"){
                $this->Smarty->assign('Vote',                 $this->ModuleVote->getAllVotes());
                $this->Smarty->assign('module_template_name', $this->ModuleVote->getAdminTemplateName());
                $this->Smarty->assign('module_name',          'Vote');
                $Template['Module'] = $this->ModuleVote->getAdminTemplateName();
            }
            $Block['Vote']    = $this->ModuleVote->getVote();
            $Template['Vote'] = $this->ModuleVote->getTemplateName();
        }

        /*** Feedback block ***/
        if ($this->ModuleFeedback != null){
            $Template['Feedback'] =                             $this->ModuleFeedback->getTemplateName();
            $Feedback['Send']     =                             $this->ModuleFeedback->isEmailFeedbackSend();
            $Feedback['Status']   =                             $this->ModuleFeedback->isEmailSendSuccessfully();
            $this->Smarty->assign('Feedback',                   $Feedback);
        }

		/*** Catalogue independent blocks ***/
		if ($this->ModuleCatalogue != null){

            /*** Catalogue random products ***/
			$Used['CatalogueRandom']        = $this->ModuleCatalogue->isRandomProductsDisplay();
			$Block['CatalogueRandom']       = $this->ModuleCatalogue->getRandomProducts();
			$Template['CatalogueRandom']    = $this->ModuleCatalogue->getRandomProductsTemplateName();

            /*** Catalogue latest products ***/
            $Used['CatalogueLatest']        = $this->ModuleCatalogue->isLatestProductsDisplay();
            $Block['CatalogueLatest']       = $this->ModuleCatalogue->getLatestProducts();
            $Template['CatalogueLatest']    = $this->ModuleCatalogue->getLatestProductsTemplateName();

            /*** Catalogue menu block ***/
            if ($this->ModuleCatalogue->isMenuCatalogueDislpay()){
                $Catalogue['Used']['Menu']  = 1;
                $Catalogue['Used']['AddButton'] = $this->ModuleCatalogue->isDisplayAddButton();
                $Catalogue['Menu']          = $this->ModuleCatalogue->getCatalogueMenu();
                $Template['CatalogueMenu']  = $this->ModuleCatalogue->getMenuTemplateName();
                $Template['Module']         = $this->ModuleCatalogue->getTemplateName();
                $this->Smarty->assign('module_template_name',       $this->ModuleCatalogue->getTemplateName());
            } else {
                $Catalogue['Used']['Menu']  = 0;
            }
		}

        /*** Files block ***/
        if ($this->ModuleFiles != null){

            if ($this->ModuleFiles->enableTemplate()){

                $Template['Module'] = $this->ModuleFiles->getTemplateName();
                $this->Smarty->assign('Pricelist', $this->ModuleFiles->getPricelist());

            }

            $Block['Pricelist'] = $this->ModuleFiles->getPricelist();

        }

        /*** Static block ***/
        if ($this->ModuleStatic != null){

            $Template['Module'] = $this->ModuleStatic->getTemplateName();
            $this->Smarty->assign('module_template_name', $this->ModuleStatic->getTemplateName());
            $this->Smarty->assign('module_name',          'Static');
            $this->Smarty->assign('Static', $this->ModuleStatic->getInfo());

        }

		/*** Gallery latest products ***/
        if ($this->ModuleGallery != null){

            $this->Smarty->assign('use_gallery_latest_products',  $this->ModuleGallery->isLatestPhotosDisplay());
            $this->Smarty->assign('gallery_latest_products_list', $this->ModuleGallery->getLatestPhotos());
            $this->Smarty->assign('gallery_latest_template_name', $this->ModuleGallery->getLatestPhotosTemplateName());

        }

        /*** Gallery menu block ***/
        if ($this->ModuleGallery != null && $this->ModuleGallery->isMenuGalleryDislpay()){
            $this->Smarty->assign('module_template_name',       $this->ModuleGallery->getTemplateName());

            $this->Smarty->assign('menu_gallery',               1);

            $this->Smarty->assign('gallery_menu',               $this->ModuleGallery->getGalleryMenu());
            $this->Smarty->assign('gallery_active_menu',        $this->ModuleGallery->typeId);

            $this->Smarty->assign('gallery_menu_display',       1);
            /** @todo change this */
            $this->Smarty->assign('menu_gallery_template_name', GALLERY_MODULE_MENU_TEMPLATE_NAME);

            $this->Smarty->assign('selected_type',              $this->InputFilter->getParameter("typeId"));

        } else {
            $this->Smarty->assign('menu_gallery',               0);
        }

        /*** switch between main/other pages ***/
        if ($this->SiteStructure->isMainPage()){

        } else if ($this->SiteStructure->isDisplay404error()){

            $Used['Module']       = 0;
            $Used['404error']     = 1;
            $Template['404error'] = SMARTY_TEMPLATE_404_PAGE;
            $this->Smarty->assign('Content', array('Title'=> 'Page Not Found'));

        } else {

            $this->Smarty->assign('Content', $this->Content->getPageInfo());

            $Used['Module']   = $this->AttributeOperations->isModuleUsed();
            $Used['404error'] = 0;
            $Used['ModuleInside'] = 0;

            /* if module used */
            if ($this->AttributeOperations->isModuleUsed()){

                /* if module is NAF */
                if ($this->ModuleNAF != null && $this->ModuleNAF->isUsed()){

                    $this->Smarty->assign('module_name', 'naf');

                    $Template['Module']         = $this->ModuleNAF->getTemplateName();
                    $Used['ModuleInside']       = $this->ModuleNAF->isModuleInside();
                    $Naf['Used']['Single']      = $this->ModuleNAF->isSingleDisplay();
                    $Naf['Used']['Calendar']    = $this->ModuleNAF->isCalendarDislpay();

                    if ($this->ModuleNAF->isSingleDisplay()){

                        $Naf['Single']  = $this->ModuleNAF->getSingle();

                    } else if ($this->ModuleNAF->isDateRangeSet()) {

                        $Naf['Content'] = $this->ModuleNAF->getListByDateRange();

                    } else {

                    }

                    if ($this->ModuleNAF->isCalendarDislpay()){

                        $Naf['Calendar'] = $this->ModuleNAF->getCalendar();

                    }
                }

                /* if module is Catalogue */
                if ($this->ModuleCatalogue != null && $this->ModuleCatalogue->isUsed()){

                    $this->Smarty->assign('module_name', 'catalogue');
                    $Used['ModuleInside']        = $this->ModuleCatalogue->isModuleInside();
                    $Template['Module']          = $this->ModuleCatalogue->getTemplateName();
                    $Catalogue['Used']['Single'] = $this->ModuleCatalogue->isSingleDisplay();
                    $Catalogue['Used']['Admin']  = $this->ModuleCatalogue->isUseAdmin();
                    $Catalogue['CurrencyLine']   = $this->ModuleCatalogue->getCurrencyLine();

                    if ($this->ModuleCatalogue->isSingleDisplay()){

                        $Catalogue['Single'] = $this->ModuleCatalogue->getSingle();

                    } else {

                        $Catalogue['Content'] =  $this->ModuleCatalogue->getList();

                    }
                }

				/* if module is Gallery */
                if ($this->ModuleGallery != null && $this->ModuleGallery->isGalleryUsed()){

                    $this->Smarty->assign('module_name',                        'gallery');
					$this->Smarty->assign('module_template_name',               $this->ModuleGallery->getTemplateName());

                    if ($this->ModuleGallery->isSingleDisplay()){

                        $this->Smarty->assign('gallery_display_single_block',   1);
                        $this->Smarty->assign('gallery_single_block',           $this->ModuleGallery->getSingle());

                    } else {

                        $this->Smarty->assign('gallery_display_single_block',   0);
                        $this->Smarty->assign('gallery_header_blocks',          $this->ModuleGallery->getList());

                    }
                }

            /* if module not used */
            } else {

                $this->Smarty->assign('module_name',                        '');

            }
        }

        $activationCode = $this->InputFilter->getParameter("activation_code")."";

        if (!empty($activationCode)){
            try{
                $this->Session->activation();
                $this->Smarty->assign("Activation", "Ok");
            } catch (ExceptionExt $e) {
                $this->Smarty->assign("Activation", $e->getMessage());
            }
        }

		$this->Smarty->assign('PageNames',   $this->SiteStructure->getPageNames());
        $this->Smarty->assign('Languages',   $this->SiteStructure->getLanguagesList());
        $this->Smarty->assign('UserDetails', $this->Session->getUserDetails());

		$this->Smarty->assign('Admin',       $Admin);
		$this->Smarty->assign('Menu',        $Menu);
		$this->Smarty->assign('PageId',      $PageId);
		$this->Smarty->assign('PageUrl',     $PageUrl);
		$this->Smarty->assign('Block',       $Block);
		$this->Smarty->assign('Used',        $Used);
		$this->Smarty->assign('Display',     $Display);
		$this->Smarty->assign('Template',    $Template);
		$this->Smarty->assign('Naf',         $Naf);
		$this->Smarty->assign('Catalogue',   $Catalogue);
    }

    /**
     * Display template
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function DisplayTemplate(){

        if ($this->SiteStructure->isMainPage()){

            $this->Smarty->display( $this->SiteStructure->getMainPageTemplateName(),$this->SiteStructure->getPageUniqueId() );

        } else {

            if ($this->AttributeOperations->isModuleUsed()){
                $this->Smarty->clear_cache( $this->SiteStructure->getPageTemplateName(), $this->SiteStructure->getPageUniqueId() );
            }

            $this->Smarty->display( $this->SiteStructure->getPageTemplateName(),    $this->SiteStructure->getPageUniqueId() );

        }
    }
}
?>
