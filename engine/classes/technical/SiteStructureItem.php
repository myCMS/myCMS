<?php
/**
 * Contain menu's items
 * Used in SiteStructure class
 *
 * @package     Engine
 * @subpackage  SiteStructure
 * @see         SiteStructure
 * @author      AlexK
 * @version     1.0
 */
class SiteStructureItem {

    public $id          = 0;
    public $parent      = 0;
    public $order       = 0;
    public $cpu         = 0;
    public $name        = 0;
    public $module      = 0;
    public $redirect    = 0;
    public $sitemap     = 0;
    public $hidden      = 0;
    public $blocks      = array();
    public $externalTemplate = '';

    /**
     * Constructor of class MenuItem
     * Data taken from Structure table
     *
     * @param   $id       current Id
     * @param   $parent   parent Id
     * @param   $cpu      cpu name
     * @param   $name     name
     * @throws  no throws
     * @return  nothing
     */
    public function  __construct($id, $parent, $cpu, $name, $order) {
        $this->id       = $id;
        $this->parent   = $parent;
        $this->order    = $order;
        $this->cpu      = $cpu;
        $this->name     = $name;
    }
}
?>