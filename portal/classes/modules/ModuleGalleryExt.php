<?php
/**
 * Module Gallery.
 *
 * @see EngineCore.php
 * @author AlexK
 * @version 1.0
 */
class ModuleGalleryExt extends ModuleGallery {

    /**
     * Constructor of class ContentExt
     */
    public function  __construct() {
        ;
    }

    /**
     * Return list of Gallery list
     *
     * @param   nothing
     * @throws  taken from ModuleGallery
     */
    public function getList(){

        if ($this->isSingleDisplay()){
            return "";
        }
        return parent::getList();

    }
}
?>
