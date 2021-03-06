<?php
/**
 * Contain gallery items
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         ModuleGallery
 * @author      AlexK
 * @version     1.0
 */
class ModuleGalleryItem {

    public  $id     = 0;
    public  $name   = '';
    public  $cpu    = '';
    public  $parent = 0;
    public  $description = '';

    /**
     * Constructor of class ModuleGalleryItem
     */
    public function  __construct($id, $name, $cpu, $parent, $description) {
        $this->id       = $id;
        $this->name     = $name;
        $this->cpu      = $cpu;
        $this->parent   = $parent;
        $this->description = $description;
    }
}
?>
