<?php
/**
 * Module Search, used for search entire site
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      Alex
 * @version     1.0
 */
class ModuleSearch {

    private $MySQL          = null;
    private $SiteStructure  = null;
    private $InputFilter    = null;

    private $ModuleNAF      = null;
    private $ModuleCatalogue= null;
    private $ModuleGallery  = null;

    /**
     * Constructor of class ModuleSearch
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure, InputFilter $InputFilter, ModuleNAF $ModuleNAF = null, ModuleCatalogue $ModuleCatalogue = null, ModuleGallery $ModuleGallery = null) {

        $this->MySQL            = $MySQL;
        $this->SiteStructure    = $SiteStructure;
        $this->InputFilter      = $InputFilter;

        $this->ModuleNAF        = $ModuleNAF;
        $this->ModuleCatalogue  = $ModuleCatalogue;
        $this->ModuleGallery    = $ModuleGallery;
       
    }

    /**
     * Return search results ввв
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  search results
     */
    public function getSearchResults() {

        $search = $this->InputFilter->getParameter("search");
        $result = array();

        if (empty($search)) {
            $search = '';
        }

        $languageId = $this->SiteStructure->getLanguageId();
        $mainUrl = $this->SiteStructure->getSiteTranslatedSiteUrl();

        $query  = "select `text1`, `text2`, `text3`, `type`, `id`, `language_id`, `link`, MATCH (text1,text2,text3) AGAINST ('$search' IN BOOLEAN MODE) as v from `search`";
        $query .= "    where MATCH (text1,text2,text3) AGAINST ('$search' IN BOOLEAN MODE) and `language_id` = $languageId order by v desc";

        $this->MySQL->query($query);

        if ($this->MySQL->countRows() == 0){
            $result = array( array('EmptySearchResults'  => '1', 'WordLine' => $search));
        }

        while ($row = $this->MySQL->fetchArray()){

            $link = '';
            $type = '';
            $typeLink = '';
            $picture = '';

            switch ($row['type']){
                case 'news':
                    $link = $mainUrl . 'news/' . str_replace('-', '/', $row['link']) . '/naf' . $row['id'];
                    $type = 'Новости';
                    $typeLink = $mainUrl . 'news/';
                    break;
                case 'articles':
                    $link = $mainUrl . 'articles/' . str_replace('-', '/', $row['link']) . '/naf' . $row['id'];
                    $type = 'Статьи';
                    $typeLink = $mainUrl . 'articles/';
                    break;
                case 'catalogue':
                    $link = $this->ModuleCatalogue->getSearchBackwardUrl($row['link'], $row['id']);
                    $type = 'Каталог';
                    $typeLink = $mainUrl . CATALOGUE_RELATIVE_URL;
                    $picture = $this->ModuleCatalogue->getSearchBackwardPictureUrl($row['id']);
                    break;
                case 'gallery':
                    $link = $this->ModuleGallery->getSearchBackwardUrl($row['link'], $row['id']);
                    $type = 'Галерея';
                    $typeLink = $mainUrl . GALLERY_RELATIVE_URL;
                    $picture = $this->ModuleGallery->getSearchBackwardPictureUrl($row['id']);
                    break;
                case 'static':
                    $link = $this->SiteStructure->getSearchBackwardUrl($row['id']);
                    $type = 'На странице';
                    $typeLink = $mainUrl;
                    $picture = $this->SiteStructure->getSearchBackwardPictureUrl($row['id']);
                    break;
            }

            $searchWords = preg_replace("/ +/", "|", trim($search));

            $searchText = '';

            $searchText = $row['text2'];

            $searchText = strip_tags($searchText);

            $searchText = htmlspecialchars_decode($searchText);

            $array = preg_split("/[\.!?]\s/ui", $searchText);
            $array2 = preg_grep("/(?:$searchWords)/ui", $array);

            $array3 = array();
            $array3 = array_slice($array2, 0, 1);

            $words = explode(" ", trim($search));

            $result[] = array(  'Id'            => $row['id'],
                                'Text1'         => htmlspecialchars_decode($row['text1']),
                                'Text2'         => htmlspecialchars_decode($row['text2']),
                                'Text3'         => htmlspecialchars_decode($row['text3']),
                                'ChunkedArray'  => $array3,
                                'Type'          => $type,
                                'TypeUrl'       => $typeLink,
                                'Url'           => $link,
                                'Picture'       => $picture,
                                'Words'         => $words,
                                'WordLine'      => $search,
                                'EmptySearchResults'  => '0'
            );
        }

        return $result;
    }

    /**
     * Return search module template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  search module template name
     */
    public function getTemplateName() {

        return SEARCH_TEMPLATE_NAME;

    }
}
?>
