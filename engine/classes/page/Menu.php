<?php
/**
 * Wrapper for SiteStructure class and used it for return all structure menus
 *
 * @package     Engine
 * @subpackage  Page
 * @see         SiteStructure
 * @author      AlexK
 * @version     1.0
 */
class Menu {

    private $SiteStructure  = null;

    /**
     * Constructor of class Menu
     */
    public function  __construct(SiteStructure $SiteStructure) {

        if ($SiteStructure == null){
            throw new ExceptionExt("SiteSructure reference not defined");
        }

        $this->SiteStructure = $SiteStructure;
    }

    /**
     * Returns current page Id
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of current page
     */
    public function getCurrentId(){

        return $this->SiteStructure->getCurrentId();
    }

    /**
     * Returns parent page Id
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of parent page
     */
    public function getParentId(){

        return $this->SiteStructure->getParentId();
    }

    /**
     * Returns parent page Id
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of parent page
     */
    public function getParentIdOnTopLevel(){

        return $this->SiteStructure->getParentIdOnTopLevel();
    }

    /**
     * Returns true if current page is main
     *
     * @param   nothing
     * @throws  nothing
     * @return  true - if main page, false - if any other page
     */
    public function isMainPage(){

        return $this->SiteStructure->isMainPage();
    }

    /**
     * Get top level menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of top level menu
     */
    public function getTopLevelMenu(){

        return $this->SiteStructure->getTopLevelMenu();
    }

    /**
     * Get sub menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of sub menu
     */
    public function getSubMenu(){

        return $this->SiteStructure->getSubMenu();
    }

    /**
     * Get sub2 menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of sub menu
     */
    public function getSub2Menu(){

        return $this->SiteStructure->getSub2Menu();
    }
}
?>
