<?php
/**
 * Used to manage admin panel element
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class AdminPanel {

    private $SiteStructure  = null;
    private $Session        = null;

    /**
     * Constructor of class AdminPanel
     */
    public function  __construct(SiteStructure $SiteStructure, Session $Session) {

        $this->SiteStructure    = $SiteStructure;
        $this->Session          = $Session;

    }

    /**
     * Return true if user is admin
     *
     * @param   nothing
     * @throws  nothing
     * @return  true if user is admin
     */
    public function isAdmin(){

        return $this->Session->isAdmin();

    }

    /**
     * Return top panel template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  top panel template name
     */
    public function getAdminPanelTemplateName(){

        return ADMIN_TOP_PANEL_TEMPLATE_NAME;

    }

    /**
     * Return add/edit window template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  add/edit window template name
     */
    public function getAdminWindowTemplateName(){

        return ADMIN_WINDOW_TEMPLATE_NAME;

    }

    /**
     * Return wrapper for each element of Naf/Catalogue/etc. item for admin
     *
     * @param   nothing
     * @throws  no throws
     * @return  wrapper for each element of Naf/Catalogue/etc. item for admin
     */
    public function getAdminElementWrapperTemplateName(){

        return ADMIN_ELEMENT_WRAPPER_TEMPLATE_NAME;

    }

    /**
     * Return top panel menu for admin
     *
     * @param   nothing
     * @throws  nothing
     * @return  top panel menu for admin
     */
    public function getAdminPanelTopMenu(){

        global $ADMIN_TOP_MENU;

        $result = array();

        if (!is_array($ADMIN_TOP_MENU)){
            return "";
        }

        $mainUrl = $this->SiteStructure->getSiteTranslatedSiteUrl();
        $currentUrl = $this->SiteStructure->getAbsolutePageUrl();

        foreach ($ADMIN_TOP_MENU as $name => $relativeLink) {

            $url = $mainUrl . "$relativeLink/";

            if ($url == $currentUrl) {
                $active = 1;
            } else {
                $active = 0;
            }

            $result[] = array(  'Url'       =>  $url,
                                'Name'      =>  $name,
                                'Active'    =>  $active
                              );
        }

        return $result;

    }
}
?>
