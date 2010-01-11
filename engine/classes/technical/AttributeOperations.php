<?php
/**
 * Description of AttributeOperations
 *
 * @package     Engine
 * @subpackage  Engine
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class AttributeOperations {

    private $MySQL          = null;
    private $SiteStructure  = null;

    private $PageAttributes = array();

    /**
     * Constructor of class AttributeOperations
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure) {

        if ($MySQL == null){
            throw new ExceptionExt('MySQL reference not defined');;
        }

        if ($SiteStructure == null){
            throw new ExceptionExt('SiteStructure reference not defined');;
        }

        $this->MySQL            = $MySQL;
        $this->SiteStructure    = $SiteStructure;

        $this->gatherPageAttributes();

    }

    /**
     * Gather page attributes
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  true if ok
     */
    private function gatherPageAttributes(){

        $pageId = $this->SiteStructure->getCurrentId();

        $this->MySQL->query("select `attribute_name`, `attribute_value` from `structure_attributes` where `structure_id`=$pageId");

        while ($row = $this->MySQL->fetchArray()) {

            if (isset($row['attribute_name'])){
                $this->PageAttributes[$row['attribute_name']] = $row['attribute_value'];
            }
        }

        $this->MySQL->freeResult();

        return true;

    }

    /**
     * Get page attributes
     *
     * @param   nothing
     * @throws  no throws
     * @return  page attributes
     */
    public function getPageAttributes(){

        return $this->PageAttributes;
        
    }

    /**
     * Return true if module used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true - if module used on this page
     */
    public function isModuleUsed(){

        if (isset($this->PageAttributes['module'])){

            return true;

        } else {

            return false;

        }
    }

    /**
     * Get module type
     *
     * @param   nothing
     * @throws  no throws
     * @return  module type - naf/catalogue/etc.
     */
    public function getModuleType(){

        if (isset($this->PageAttributes['module_type'])){

            return $this->PageAttributes['module_type'];

        } else {

            return "";

        }
    }

    /**
     * Return module name
     *
     * @param   nothing
     * @throws  no throws
     * @return  module name
     */
    public function getModuleName(){

        if (isset($this->PageAttributes['module'])){

            return $this->PageAttributes['module'];

        } else {

            return "";

        }
    }
}
?>
