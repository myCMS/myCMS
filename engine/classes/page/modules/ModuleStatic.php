<?php
/**
 * Used for define content
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleStatic {

    private $MySQL          = null;
    private $SiteStructure  = null;
    private $InputFilter    = null;
    private $Session        = null;
    private $languageId     = 0;
    private $modifiedId     = 0;

    /**
     * Constructor of class Content
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure, InputFilter $InputFilter, Session $Session) {

        $this->MySQL        = $MySQL;
        $this->SiteStructure= $SiteStructure;
        $this->InputFilter  = $InputFilter;
        $this->Session      = $Session;

        $this->languageId   = $this->SiteStructure->getLanguageId();

    }

    /**
     * Return static template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  static template name
     */
    public function getTemplateName() {
        return STATIC_PAGES_MODULE_TEMPLATE_NAME;
    }

    /**
     * Return test page
     *
     * @param   nothing
     * @throws  no throws
     * @return  test page
     */
    public function getInfo() {

        $result = array();

        $result = $this->SiteStructure->getAllPagesList();

        return $result;

    }

    /**
     * Return true if admin panel is used
     *
     * @param  nothing
     * @throws no throws
     * @return true if admin panel is used
     */
    public function isUseAdmin(){

        if ($this->Session->isAuthorized()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Save map
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function saveMap() {

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $pageId = $this->InputFilter->getParameter("id");
        if (empty($pageId)){
            throw new ExceptionExt("Page Id not setup");
        }

        $nextPageId = $this->InputFilter->getParameter("nextId", 0);

        $parentId = $this->InputFilter->getParameter("parent", 0);
        /*if (empty($parentId)){
            throw new ExceptionExt("Parent Id not setup");
        }*/
        
        $cacheIds = array();
        $cacheIds = $this->SiteStructure->getPageCacheIds();

        $nextPageOrder = 1;
        if (!empty($nextPageId) && isset($cacheIds[$nextPageId])){
            $nextPageOrder = $cacheIds[$nextPageId]->order;
        }

        $this->MySQL->query("UPDATE `structure` SET `parent` = $parentId, `order` = $nextPageOrder WHERE `id` = $pageId LIMIT 1");

        if (!empty($nextPageId)){

            $this->MySQL->query("UPDATE `structure` SET `order` = `order` + 1 WHERE `parent` = $parentId AND `order` >= $nextPageOrder AND `id` <> $pageId");

        }
    }

    /**
     * Add new element
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function addElement() {

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        //$Typograph = new Typograph();

        $parent = 0;
        if (!empty($newData["id"])){
            $parent = (integer)$newData["id"];
        }

        $active = 0;
        if (!empty($newData["Active"])){
            $active = 1;
        }

        $order = 0;
        if (!empty($newData["Order"])){
            $order = (integer)$newData["Order"];
        }

        $cpu = '';
        if (!empty($newData["Cpu"])){
            $cpu = $newData["Cpu"];
        }

        $name       = $newData["Name"];
        $text       = $newData["Text"];
        $title      = $newData["Title"];
        $desc       = $newData["Description"];
        $keywords   = $newData["Keywords"];

        $this->MySQL->query("INSERT INTO `structure` (`cpu_name`, `parent`, `active`, `order`) VALUES('$cpu', $parent, $active, $order)");

		if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Element not inserted");

		}

		$id = $this->MySQL->insertedId();

        if (!empty($id)){
            $this->modifiedId = $id;
        } else {
            $this->modifiedId = 0;
        }

        $this->MySQL->query("INSERT INTO `structure_languages` (`structure_id`, `language_id`, `name`, `text`, `title`, `page_description`, `page_keywords`) VALUES($id, {$this->languageId}, '$name', '$text', '$title', '$desc', '$keywords')");

		if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Element not inserted");

		}

        return true;

    }

    /**
     * Edit element
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function editElement() {

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $id = (integer)$this->InputFilter->getParameter("id");

        if (empty($id)){
            throw new ExceptionExt("Id not set");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        //$Typograph = new Typograph();

        $parent = 0;

        $active = 0;
        if (!empty($newData["Active"])){
            $active = 1;
        }

        $order = 0;
        if (!empty($newData["Order"])){
            $order = (integer)$newData["Order"];
        }

        $cpu = '';
        if (!empty($newData["Cpu"])){
            $cpu = $newData["Cpu"];
        }

        $name       = $newData["Name"];
        $text       = $newData["Text"];
        $title      = $newData["Title"];
        $desc       = $newData["Description"];
        $keywords   = $newData["Keywords"];

        $this->MySQL->query("UPDATE `structure` SET `cpu_name` = '$cpu', `parent` = $parent, `active` = $active, `order` = $order WHERE `id` = $id LIMIT 1");

        $this->modifiedId = $id;

        $this->MySQL->query("UPDATE `structure_languages` SET `name` = '$name', `text` = '$text', `title` = '$title', `page_description` = '$desc', `page_keywords` = '$keywords' WHERE `structure_id` = $id AND `language_id` = {$this->languageId} LIMIT 1");

        return true;

    }

    /**
     * Remove element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $ids = $this->InputFilter->getParameter("json");

        if (!is_array($ids)){
            throw new ExceptionExt("Ids not set");
        }

        /** can be used implode() */
        $inStatement = '';
        foreach ($ids as $id){
            if (empty($inStatement)){
                $inStatement .= "$id";
            } else {
                $inStatement .= ",$id";
            }

        }

        $this->MySQL->query("DELETE FROM `structure` WHERE `id` IN ($inStatement)");
        $this->MySQL->query("DELETE FROM `structure_languages` WHERE `structure_id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Elements not deleted");

        }

        return true;
    }

    /**
     * Inverse activity of Naf element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function inverseActivityElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $ids = $this->InputFilter->getParameter("json");

        if (!is_array($ids)){
            throw new ExceptionExt("Naf Ids not set");
        }

        /** can be used implode() */
        $inStatement = '';
        foreach ($ids as $id){
            if (empty($inStatement)){
                $inStatement .= "$id";
            } else {
                $inStatement .= ", $id";
            }
        }

        $this->MySQL->query("UPDATE `structure` SET `active` = NOT `active` WHERE `id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Activity not inversed");

        }

        return true;
    }

    /**
     * Remove element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeOneElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $id = $this->InputFilter->getParameter("id");

        $this->MySQL->query("DELETE FROM `structure` WHERE `id` = $id LIMIT 1");
        $this->MySQL->query("DELETE FROM `structure_languages` WHERE `structure_id` = $id LIMIT 1");

        if(!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Elements not deleted");
        }
        return true;
    }

    /**
     * Return last modified data by ajax
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  last modified data by ajax
     */
    public function getAdminLastModifiedData(){

        $result = array();
        $id     = $this->modifiedId;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($id)){
            throw new ExceptionExt("Id not set");
        }

        $result = $this->SiteStructure->getAllPagesList($id);
        
        return $result;

    }

    /**
     * Return admin window
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin window
     */
    public function getAdminWindowAdd(){

        global $ADMIN_WINDOW;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $parentTypeId = $this->InputFilter->getParameter("id", 0);

        $result = array( 'Lines' =>  array(), 'Title' => 'Добавление', 'Action' => 'Add' );

        $result['Id'] = $parentTypeId;

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['Lines'] = $ADMIN_WINDOW[$page];

        foreach ($result['Lines'] as &$field){
            $field['Value'] = '';
        }

        $result['Lines'][] = array(
            'Name'          => 'id',
			'Type'          => 'hiddenDataBox',
			'Description'   => $parentTypeId,
			'Value'         => $parentTypeId);

        return $result;
    }

    /**
     * Return admin window block
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function getAdminWindowEdit(){

        global $ADMIN_WINDOW;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $parentTypeId = $this->InputFilter->getParameter("id", 0);

        $result = array( 'Lines' =>  array(), 'Title' => 'Редактирование', 'Action' => 'Edit' );

        $pageId  = (integer)$this->InputFilter->getParameter("id");

        if (empty($pageId)){
            throw new ExceptionExt("Id not set");
        }

        $this->modifiedId = $pageId;

        $result['Id'] = $pageId;

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['Lines'] = $ADMIN_WINDOW[$page];

        $useLatestList = 0;
        $useRandomList = 0;
        $randomValue   = 0;
        $useLastModifiedData = 1;

        $this->MySQL->query("SELECT s.`id`, s.`cpu_name` as `cpu`, l.`name`, l.`text`, l.`title`, l.`page_description`, l.`page_keywords`, s.`active` FROM `structure` s, `structure_languages` l WHERE s.`id` = l.`structure_id` AND s.`id` = $pageId LIMIT 1");

        while ($row = $this->MySQL->fetchArray()){

            foreach ($result['Lines'] as &$field){

                switch ($field['Name']){
                    case "Name":
                        $field['Value'] = $row['name'];
                        break;
                    case "Text":
                        $field['Value'] = $row['text'];
                        break;
                    case "Title":
                        $field['Value'] = $row['title'];
                        break;
                    case "Description":
                        $field['Value'] = $row['page_description'];
                        break;
                    case "Keywords":
                        $field['Value'] = $row['page_keywords'];
                        break;
                    case "Cpu":
                        $field['Value'] = $row['cpu'];
                        break;
                    case "Active":
                        $field['Value'] = $row['active'];
                        break;
                    default:
                        throw new ExceptionExt("Incorrect field type: {$field['Name']}");
                        break;
                }
            }
        }

        $this->MySQL->freeResult();

        $result['Lines'][] = array(
            'Name'          => 'id',
			'Type'          => 'hiddenDataBox',
			'Description'   => $parentTypeId,
			'Value'         => $parentTypeId);

        return $result;
    }
}
?>
