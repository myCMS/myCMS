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
class Content {

    private $MySQL          = null;
    private $SiteStructure  = null;

    /**
     * Constructor of class Content
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure) {

        $this->MySQL        = $MySQL;
        $this->SiteStructure= $SiteStructure;

    }

    /**
     * Used as optimization - instead several querys used one, when class created
     *
     * @param   nothing
     * @throws  from MySQL class
     * @return  nothing
     */
    public function getPageInfo(){

        $result = array();

        $pageId = $this->SiteStructure->getCurrentId();

        $langId = $this->SiteStructure->getLanguageId();

        if (empty($langId)){
            $query = "select `name`, `text`, `title`, `page_keywords`, `page_description` from `structure` where `id`=$pageId";
        } else {
            $query = "select `name`, `text`, `title`, `page_keywords`, `page_description` from `structure_languages` where `structure_id`=$pageId and `language_id`=$langId";
        }

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()) {
            $result      = array('Name'             => $row['name'],
                                 'Text'             => $row['text'],
                                 'Title'            => $row['title'],
                                 'Keywords'         => $row['page_keywords'],
                                 'Description'      => $row['page_description']
                                );
        }

        $this->MySQL->freeResult();

        return $result;

    }
}
?>
