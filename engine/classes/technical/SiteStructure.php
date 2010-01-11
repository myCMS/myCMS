<?php
/**
 * Contain full menu list
 *
 * @package     Engine
 * @subpackage  SiteStructure
 * @see         SiteStructureItem
 * @author      AlexK
 * @version     1.0
 */
class SiteStructure {

    private $MySQL                  = null;
    private $InputFilter            = null;
    private $Session                = null;

    private $currentPageUrl         = '';
    private $parentPageUrl          = '';
    private $pageUrlOnTopLevel      = '';
    private $pageUrlOnSecondLevel   = '';
    private $pageUrlOnThirdLevel    = '';
    private $currentPageId          = 0;
    private $parentPageId           = 0;
    private $pageIdOnTopLevel       = 0;
    private $pageIdOnSecondLevel    = 0;
    private $pageIdOnThirdLevel     = 0;
    private $pageNameOnTopLevel     = '';
    private $pageNameOnSecondLevel  = '';
    private $pageNameOnThirdLevel   = '';
    private $portalUrl              = '';
    private $display404             = 0;
    private $menuDeepLevel          = 0;
    private $sitemap                = 0;
    private $externalTemplate       = '';
    private $absolutePageUrl        = '';
    private $selectedLanguageId     = 1;
    private $selectedLanguageName   = "ru";
    private $selectedLanguageUrl    = "";
    private $ItemsList              = array();
    private $cacheIds               = array();
    private $cacheCpu               = array();
    private $cacheParent            = array();
    private $pageParameters         = array();
    private $pageBlocks             = array();
    private $languagesList          = array();

    /**
     * Constructor of class SiteStructure
     */
    public function  __construct(MySQL $MySQL, InputFilter $InputFilter, Session $Session) {

        $this->MySQL                = $MySQL;
        $this->InputFilter          = $InputFilter;
        $this->Session              = $Session;

        $this->gatherSiteStructure();
    }

    /**
     * Return menu for current parent Id
     *
     * @param   $params - all module parameters
     * @param   $deep   - deep of module parameters
     * @param   $parent - parent Id
     * @param   $url    - start url for current branch
     * @throws  no throws
     * @return  menu for current parent Id
     */
    private function menu($params, $deep, $parent, $url){

        if (empty($this->cacheParent[$parent])){
            return '';
        }

        $result = array();

        foreach ($this->cacheParent[$parent] as $item){

                $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');

				if (!$item->hidden) {
					$result[] = array('Id'          => $item->id,
									  'Url'         => $url.$item->cpu.'/',
									  'Name'        => $item->name,
									  'Deep'        => $deep+1,
									  'Sub'         => $sub
					);
				}
        }
        return $result;
    }

    /**
     * Return menu for current parent Id
     *
     * @param   $params - all module parameters
     * @param   $deep   - deep of module parameters
     * @param   $parent - parent Id
     * @param   $url    - start url for current branch
     * @param   $id     - id will be added
     * @throws  no throws
     * @return  menu for current parent Id
     */
    private function menuAdmin($params, $deep, $parent, $url, $id = 0){
        
        $result = array();

        if (!empty($id)){
            if (isset($this->cacheIds[$id])){

                $item = $this->cacheIds[$id];

                $sub = $this->menuAdmin($params, $deep + 1, $item->id, $url.$item->cpu.'/');

                $result[] = array('Id'          => $item->id,
                                  'Url'         => $url.$item->cpu.'/',
                                  'Parent'      => $item->parent,
                                  'Order'       => $item->order,
                                  'Name'        => $item->name,
                                  'Deep'        => $deep,
                                  'Hidden'      => $item->hidden,
                                  'IsModule'    => $item->module,
                                  'Sub'         => $sub
                );
            }

            return $result;
        }

        if (empty($this->cacheParent[$parent])){
            return '';
        }

        foreach ($this->cacheParent[$parent] as $item){

                $sub = $this->menuAdmin($params, $deep + 1, $item->id, $url.$item->cpu.'/');

                $result[] = array('Id'          => $item->id,
                                  'Url'         => $url.$item->cpu.'/',
                                  'Parent'      => $item->parent,
                                  'Order'       => $item->order,
                                  'Name'        => $item->name,
                                  'Deep'        => $deep+1,
                                  'Hidden'      => $item->hidden,
                                  'IsModule'    => $item->module,
                                  'Sub'         => $sub
                );
        }
        return $result;
    }

    /**
     * Parse page url and define all page Ids
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function parsePageUrl(){

        $currentPageUrl         = '';
        $parentPageUrl          = '';
        $pageUrlOnTopLevel      = '';
        $pageUrlOnSecondLevel   = '';
        $pageUrlOnThirdLevel    = '';
        $currentPageId          = 0;
        $parentPageId           = 0;
        $pageIdOnTopLevel       = 0;
        $pageIdOnSecondLevel    = 0;
        $pageIdOnThirdLevel     = 0;
        $pageNameOnTopLevel     = '';
        $pageNameOnSecondLevel  = '';
        $pageNameOnThirdLevel   = '';
        $urls                   = array();
        $redirectPage           = 0;

        $fullUrl = $this->InputFilter->getPageUrl();

        if (defined("PORTAL_URL")){

            $this->portalUrl = PORTAL_URL . '/';

            if (strpos(PORTAL_URL, "//") === 0){
                $setServerName = 1;
            } else {
                $setServerName = 0;
                $this->portalUrl = '/' . PORTAL_URL . '/';
            }

            $consts = explode("/",PORTAL_URL);
            $count = count($consts);

            for($i = 0; $i < $count; $i++){
                if (empty($consts[$i])){
                    unset($consts[$i]);
                }
            }

            $urls = explode('/',$fullUrl);
            /*** remove empty value ***/
            array_shift($urls);

            if (empty($urls[count($urls)-1])){
                array_pop($urls);
            }

            $count = count($consts);

            for ($i = 0; $i < $count-$setServerName; $i++){
                array_shift($urls);
            }

        } else {

            $this->portalUrl = '/' . WEB_PORTAL_URL . '/';

            $urls = explode('/',$fullUrl);
            /*** remove empty value ***/
            array_shift($urls);

            if (empty($urls[count($urls)-1])){
                array_pop($urls);
            }

            for ($i = 0; $i < WEB_PORTAL_SKIP_URL; $i++){
                array_shift($urls);
            }
        }

        /*** check logout ***/
        if (isset($urls[count($urls)-1]) && $urls[count($urls)-1] == 'logout'){
            $this->Session->logout();
            array_pop($urls);
            $i = strpos($fullUrl, 'logout');
            $redirect_string = substr($fullUrl, 0, $i);
            header("Location: $redirect_string");
            exit(0);
        }

        /*** detect language ***/
        if ( isset( $urls[0], $this->languagesList[$urls[0]] ) ){

            $this->selectedLanguageId   = $this->languagesList[$urls[0]]['id'];
            $this->selectedLanguageName = $urls[0];
            $this->selectedLanguageUrl  = $urls[0].'/';
            array_shift($urls);

        }

        /*** get structure ***/
        $this->MySQL->query("select s.`id`, s.`cpu_name`, s.`parent`, l.`name`, s.`order` from structure s, structure_languages l where s.`id` = l.`structure_id` and s.`active` = 1 and l.language_id = {$this->selectedLanguageId} order by s.`order`, s.`id`");

        while ($row = $this->MySQL->fetchArray()) {

            $newSiteItem = new SiteStructureItem($row['id'], $row['parent'], $row['cpu_name'], $row['name'], $row['order']);
            $this->ItemsList[]                      = $newSiteItem;
            $this->cacheIds[$row['id']]             = $newSiteItem;
            $this->cacheCpu[$row['cpu_name']]       = $newSiteItem;
            $this->cacheParent[$row['parent']][]    = $newSiteItem;

        }

        $this->MySQL->freeResult();

        /*** get attributes ***/
        $this->MySQL->query("select `structure_id`, `attribute_name`, `attribute_value` from `structure_attributes`");

        while ($row = $this->MySQL->fetchArray()) {

            if (!isset($this->cacheIds[$row['structure_id']])){
                continue;
            }

            $MenuItem = $this->cacheIds[$row['structure_id']];

            switch ($row['attribute_name']){
                case 'module':
                    $MenuItem->module   = 1;
                    break;
                case 'redirect':
                    $MenuItem->redirect = $row['attribute_value'];
                    break;
                case 'sitemap':
                    $MenuItem->sitemap  = 1;
                    break;
                case 'hidden':
                    $MenuItem->hidden   = 1;
                    break;
                case 'block':
                    $MenuItem->blocks[] = $row['attribute_value'];
                    break;
                case 'template':
                    $MenuItem->externalTemplate = $row['attribute_value'];
                    break;
            }
        }

        $this->MySQL->freeResult();

        /*** if main page again ***/
        if (count($urls) == 0){

            foreach($this->languagesList as $name => &$array){

                $url        = $this->portalUrl."$name/";

                if ($array['id'] == 1){
                    $url    = $this->portalUrl;
                }

                $array['url'] = $url;

            }

            return true;
        }

        /*** Level 1 ***/
        foreach ($this->ItemsList as $MenuItem){

            if ($MenuItem->cpu == $urls[0]){

                if ($MenuItem->parent != 0){
                    /*** first url not from top level ***/
                    break;
                }

                $pageUrlOnTopLevel = $MenuItem->cpu;
                $pageIdOnTopLevel  = $MenuItem->id;
                $pageNameOnTopLevel= $MenuItem->name;

                if (isset($urls[1]) && $MenuItem->module != 1){

                    /*** Level 2 ***/
                    foreach ($this->ItemsList as $SubMenuItem){

                        if ($SubMenuItem->cpu == $urls[1]){

                            if ($SubMenuItem->parent != $pageIdOnTopLevel){
                                /*** second url parent not eq selected ***/
                                break 2;
                            }

                            $pageUrlOnSecondLevel = $SubMenuItem->cpu;
                            $pageIdOnSecondLevel  = $SubMenuItem->id;
                            $pageNameOnSecondLevel= $SubMenuItem->name;

                            if (isset($urls[2]) && $SubMenuItem->module != 1){

                                /*** Level 3 ***/
                                foreach($this->ItemsList as $Sub2MenuItem){

                                    if ($Sub2MenuItem->cpu == $urls[2]){

                                        if ($Sub2MenuItem->parent != $pageIdOnSecondLevel){
                                            /*** Third url parent not eq selected ***/
                                            break 3;
                                        }

                                        if (isset($urls[3]) && $Sub2MenuItem->module != 1){
                                            /*** Third url has parameters, but its not module ***/
                                            break 3;
                                        }

                                        $pageUrlOnThirdLevel = $Sub2MenuItem->cpu;
                                        $pageIdOnThirdLevel  = $Sub2MenuItem->id;
                                        $pageNameOnThirdLevel= $Sub2MenuItem->name;

                                        /*** no check 4-th level ***/

                                        /*** module on third level ***/
                                        $currentPageUrl = $pageUrlOnThirdLevel;
                                        $currentPageId  = $pageIdOnThirdLevel;
                                        $parentPageUrl  = $pageUrlOnSecondLevel;
                                        $parentPageId   = $pageIdOnSecondLevel;
                                        $redirectPage   = $Sub2MenuItem->redirect;

                                        array_shift($urls);
                                        array_shift($urls);
                                        array_shift($urls);
                                        $this->menuDeepLevel    = 3;
                                        $this->pageParameters   = $urls;
                                        $this->externalTemplate = $Sub2MenuItem->externalTemplate;
                                        $this->pageBlocks       = $Sub2MenuItem->blocks;
                                        $this->sitemap          = $Sub2MenuItem->sitemap;

                                        break 3;
                                    }
                                }

                            } else {

                                /*** module on second level ***/
                                $currentPageUrl = $pageUrlOnSecondLevel;
                                $currentPageId  = $pageIdOnSecondLevel;
                                $parentPageUrl  = $pageUrlOnTopLevel;
                                $parentPageId   = $pageIdOnTopLevel;
                                $redirectPage   = $SubMenuItem->redirect;

                                array_shift($urls);
                                array_shift($urls);
                                $this->menuDeepLevel    = 2;
                                $this->pageParameters   = $urls;
                                $this->externalTemplate = $SubMenuItem->externalTemplate;
                                $this->pageBlocks       = $SubMenuItem->blocks;
                                $this->sitemap          = $SubMenuItem->sitemap;

                            }

                            break 2;
                        }
                    }

                } else {

                    /*** module on top level ***/
                    $currentPageUrl = $pageUrlOnTopLevel;
                    $currentPageId  = $pageIdOnTopLevel;
                    $parentPageUrl  = '';
                    $parentPageId   = 0;
                    $redirectPage   = $MenuItem->redirect;

                    array_shift($urls);
                    $this->menuDeepLevel    = 1;
                    $this->pageParameters   = $urls;
                    $this->externalTemplate = $MenuItem->externalTemplate;
                    $this->pageBlocks       = $MenuItem->blocks;
                    $this->sitemap          = $MenuItem->sitemap;

                }

                break;
            } //end of if ($MenuItem->cpu == $urls[0]){
        } //end of foreach ($this->ItemsList as $MenuItem){

        /*** display 404 error ***/
        if (empty($currentPageId)){
            header("HTTP/1.0 404 Not Found");
            $this->display404 = 1;
            return true;
        }

        /*** check for redirect ***/
        if ($redirectPage){

            foreach ($this->ItemsList as $MenuItem){

                if ($MenuItem->id == $redirectPage){

                    $currentPageUrl     = $MenuItem->cpu;
                    $currentPageId      = $MenuItem->id;
                    $pageNameOnTopLevel = $MenuItem->name;

                    $pageUrlOnTopLevel  = $currentPageUrl;
                    $pageIdOnTopLevel   = $currentPageId;
                    $parentPageUrl      = '';
                    $parentPageId       = 0;
                    $this->menuDeepLevel= 1;
                    $this->externalTemplate = $MenuItem->externalTemplate;
                    $this->pageBlocks       = $MenuItem->blocks;
                    $this->sitemap          = $MenuItem->sitemap;

                    if ($MenuItem->parent != 0){

                        foreach($this->ItemsList as $SubMenuItem){

                            if ($MenuItem->parent == $SubMenuItem->id){

                                $parentPageUrl          = $SubMenuItem->cpu;
                                $parentPageId           = $SubMenuItem->id;
                                $pageUrlOnSecondLevel   = $pageUrlOnTopLevel;
                                $pageIdOnSecondLevel    = $pageIdOnTopLevel;
                                $pageUrlOnTopLevel      = $SubMenuItem->cpu;
                                $pageIdOnTopLevel       = $SubMenuItem->id;
                                $pageNameOnSecondLevel  = $pageNameOnTopLevel;
                                $pageNameOnTopLevel     = $SubMenuItem->name;
                                $this->externalTemplate = $SubMenuItem->externalTemplate;
                                $this->pageBlocks       = $SubMenuItem->blocks;
                                $this->sitemap          = $SubMenuItem->sitemap;
                                $this->menuDeepLevel    = 2;

                                if ($SubMenuItem->parent != 0){

                                    foreach($this->ItemsList as $Sub2MenuItem){

                                        if ($SubMenuItem->parent == $Sub2MenuItem->id){

                                            $pageUrlOnThirdLevel    = $pageUrlOnSecondLevel;
                                            $pageIdOnThirdLevel     = $pageIdOnSecondLevel;
                                            $pageUrlOnSecondLevel   = $pageUrlOnTopLevel;
                                            $pageIdOnSecondLevel    = $pageIdOnTopLevel;
                                            $pageUrlOnTopLevel      = $Sub2MenuItem->cpu;
                                            $pageIdOnTopLevel       = $Sub2MenuItem->id;
                                            $pageNameOnTopLevel     = $Sub2MenuItem->name;
                                            $pageNameOnThirdLevel   = $pageNameOnSecondLevel;
                                            $pageNameOnSecondLevel  = $pageNameOnTopLevel;
                                            $this->externalTemplate = $Sub2MenuItem->externalTemplate;
                                            $this->pageBlocks       = $Sub2MenuItem->blocks;
                                            $this->sitemap          = $Sub2MenuItem->sitemap;
                                            $this->menuDeepLevel    = 3;

                                            break;
                                        }
                                    }
                                }

                                break;
                            }
                        }
                    }

                    break;
                }
            }
        }

        $this->pageIdOnTopLevel     = $pageIdOnTopLevel;
        $this->pageIdOnSecondLevel  = $pageIdOnSecondLevel;
        $this->pageIdOnThirdLevel   = $pageIdOnThirdLevel;
        $this->currentPageId        = $currentPageId;
        $this->parentPageId         = $parentPageId;

        $this->pageUrlOnTopLevel    = $pageUrlOnTopLevel;
        $this->pageUrlOnSecondLevel = $pageUrlOnSecondLevel;
        $this->pageUrlOnThirdLevel  = $pageUrlOnThirdLevel;
        $this->currentPageUrl       = $currentPageUrl;
        $this->parentPageUrl        = $parentPageUrl;

        $this->pageNameOnTopLevel   = $pageNameOnTopLevel;
        $this->pageNameOnSecondLevel= $pageNameOnSecondLevel;
        $this->pageNameOnThirdLevel = $pageNameOnThirdLevel;

        $currentUrl = $this->pageUrlOnTopLevel.'/';

        if (!empty($this->pageUrlOnThirdLevel)){

            $currentUrl .= $this->pageUrlOnSecondLevel.'/'.$this->pageUrlOnThirdLevel.'/';

        } else if (!empty($this->pageUrlOnSecondLevel)){

            $currentUrl .= $this->pageUrlOnSecondLevel.'/';

        }

        foreach($this->languagesList as $name => &$array){

            $url        = $this->portalUrl."$name/";

            if ($array['id'] == 1){
                $url    = $this->portalUrl;
            }

            $array['url'] = $url . $currentUrl;

        }

        /*** detect languages translations for this page ***/
        $this->MySQL->query("select l.`name` from `structure_languages` s, `languages` l where s.`language_id` = l.`id` and s.`structure_id` = {$this->currentPageId}");

        while ($row = $this->MySQL->fetchArray()) {

            $this->languagesList[$row['name']]['hidden'] = 0;

        }

        $this->MySQL->freeResult();

        $this->absolutePageUrl = $this->portalUrl.$this->selectedLanguageUrl.$currentUrl;

    }

    /**
     * get all site stucture, and store it in Manu class instance
     *
     * @param   nothing
     * @throws  if Menu reference not defined
     * @return  nothing
     */
    private function gatherSiteStructure(){

        if ($this->MySQL == null){

            throw new ExceptionExt('Reference to MySQL class not defined');
        }

        /*** get languages ***/
        $this->MySQL->query("select `id`, `name`, `interpretation` from `languages`");

        while ($row = $this->MySQL->fetchArray()) {
            $this->languagesList[$row['name']] = array( 'id'             => $row['id'],
                                                        'url'            => '',
														'interpretation' => $row['interpretation'],
                                                        'hidden'         => 1
            );
        }

        if (empty($this->languagesList)){
            throw new ExceptionExt('No languages setup on structure_languages table');
        }

        $this->MySQL->freeResult();

        $this->parsePageUrl();

        return true;
    }

    /**
     * Reset Data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function resetData() {
        $this->ItemsList = array();
        $this->cacheIds = array();
        $this->cacheCpu = array();
        $this->cacheParent = array();
        $this->pageParameters = array();
        $this->pageBlocks = array();
        $this->languagesList = array();
    }

    /**
     * Return page cache ids
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function getPageCacheIds() {
        return $this->cacheIds;
    }

    /**
     * Reset Data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function resetSiteStructureData() {
        $this->resetData();
        $this->gatherSiteStructure();
    }

    /**
     * Return all page Ids
     *
     * @param   nothing
     * @throws  no throws
     * @return  all page Ids
     */
    public function getPageIds(){

        return array('currentPageId'        => $this->currentPageId,
                     'parentPageId'         => $this->parentPageId,
                     'pageIdOnTopLevel'     => $this->pageIdOnTopLevel,
                     'pageIdOnSecondLevel'  => $this->pageIdOnSecondLevel,
                     'pageIdOnThirdLevel'   => $this->pageIdOnThirdLevel
                    );

    }

    /**
     * Return all page Names
     *
     * @param   nothing
     * @throws  no throws
     * @return  all page Names
     */
    public function getPageNames(){

        return array('Level1'       => $this->pageNameOnTopLevel,
                     'Level2'       => $this->pageNameOnSecondLevel,
                     'Level3'       => $this->pageNameOnThirdLevel
                    );

    }

    /**
     * Return all page Urls
     *
     * @param   nothing
     * @throws  no throws
     * @return  all page Urls
     */
    public function getPageUrls(){

        return array('Main'       => $this->portalUrl.$this->selectedLanguageUrl,
                     'Base'       => $this->portalUrl,
                     'Current'    => $this->currentPageUrl,
                     'Parent'     => $this->parentPageUrl,
                     'Level1'     => $this->pageUrlOnTopLevel,
                     'Level2'     => $this->pageUrlOnSecondLevel,
                     'Level3'     => $this->pageUrlOnThirdLevel,

                     'absCurrent' => $this->absolutePageUrl,

                     'absLevel1'  => $this->portalUrl.$this->selectedLanguageUrl.$this->pageUrlOnTopLevel.'/',
                     'absLevel2'  => $this->portalUrl.$this->selectedLanguageUrl.$this->pageUrlOnTopLevel.'/'.$this->pageUrlOnSecondLevel.'/',
                     'absLevel3'  => $this->portalUrl.$this->selectedLanguageUrl.$this->pageUrlOnTopLevel.'/'.$this->pageUrlOnSecondLevel.'/'.$this->pageUrlOnThirdLevel.'/'
                    );

    }

    /**
     * Returns current page Id
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of current page
     */
    public function getCurrentId(){

        return $this->currentPageId;

    }

    /**
     * Returns parent page Id
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of parent page
     */
    public function getParentId(){

        return $this->parentPageId;

    }

    /**
     * Returns parent page Id on Level 1 Menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of parent page
     */
    public function getPageIdOnTopLevel(){

        return $this->pageIdOnTopLevel;

    }

    /**
     * Returns parent page Id on Level 2 Menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of parent page
     */
    public function getPageIdOnSecondLevel(){

        return $this->pageIdOnSecondLevel;

    }

    /**
     * Returns parent page Id on Level 3 Menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  Id of parent page
     */
    public function getPageIdOnThirdLevel(){

        return $this->pageIdOnThirdLevel;

    }

    /**
     * Returns main url of site
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of main site
     */
    public function getBaseSiteUrl(){

        return $this->portalUrl;

    }

    /**
     * Returns main url of site
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of main site
     */
    public function getSiteUrl(){

        return $this->portalUrl;

    }

    /**
     * Return main url of site with language
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of main site with language
     */
    public function getSiteTranslatedSiteUrl() {

        return $this->portalUrl.$this->selectedLanguageUrl;

    }

    /**
     * Returns url of current page
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of current page
     */
    public function getCurrentPageUrl(){

        return $this->currentPageUrl;

    }

    /**
     * Returns url of parent page
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of parent page
     */
    public function getParentPageUrl(){

        return $this->parentPageUrl;

    }

    /**
     * Returns url of parent page on Level 1 Menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of parent page
     */
    public function getPageUrlOnTopLevel(){

        return $this->pageUrlOnTopLevel;

    }

    /**
     * Returns url of parent page on Level 2 Menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of parent page
     */
    public function getPageUrlOnSecondLevel(){

        return $this->pageUrlOnSecondLevel;

    }

    /**
     * Returns url of parent page on Level 3 Menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  url of parent page
     */
    public function getPageUrlOnThirdLevel(){

        return $this->pageUrlOnThirdLevel;

    }

    /**
     * Return absolute page url
     *
     * @param   nothing
     * @throws  no throws
     * @return  absolute page url
     */
    public function getAbsolutePageUrl(){

        return $this->absolutePageUrl;

    }

    /**
     * Return page unique id, used for cache control
     *
     * @param   nothing
     * @throws  no throws
     * @return  page unique id, used for cache control
     */
    public function getPageUniqueId(){

        $admin = $this->Session->isAdmin();

        return $this->currentPageId."_".$this->selectedLanguageName."_".$admin;

    }

    /**
     * Return selected language id
     *
     * @param   nothing
     * @throws  no throws
     * @return  selected language id
     */
    public function getLanguageId(){

        return $this->selectedLanguageId;

    }

    /**
     * Return selected language name
     *
     * @param   nothing
     * @throws  no throws
     * @return  selected language name
     */
    public function getLanguageName(){

        return $this->selectedLanguageName;

    }

    /**
     * Return languages list
     *
     * @param   nothing
     * @throws  no throws
     * @return  languages list
     */
    public function getLanguagesList(){

        $result = array();

        foreach ($this->languagesList as $name => $array){

            $selected   = 0;
            $default    = 0;

            if ($this->selectedLanguageId == $array['id']){
                $selected   = 1;
            }

            if ($array['id'] == 1){
                $default    = 1;
            }

            $result[] = array('Id'      => $array['id'],
                              'Name'    => $name,
                              'Selected'=> $selected,
                              'Default' => $default,
                              'Url'     => $array['url'],
                              'Hidden'  => $array['hidden'],
							  'Interpretation' => $array['interpretation']
                             );
        }

        return $result;
    }


    /**
     * Returns true if current page is main
     *
     * @param   nothing
     * @throws  nothing
     * @return  true - if main page, false - if any other page
     */
    public function isMainPage(){

        if (empty($this->currentPageId) && empty($this->display404)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if site map
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if site map
     */
    public function isSiteMap(){

        return $this->sitemap;

    }

    /**
     * Returns true if display 404 error
     *
     * @param   nothing
     * @throws  nothing
     * @return  true - if 404 page, false - if any other page
     */
    public function isDisplay404error(){

        if (!empty($this->display404)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return all page parameters as array
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of page parameters
     */
    public function getModuleParameters(){

        return $this->pageParameters;
    }

    /**
     * Return all page parameters as array
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of page parameters
     */
    public function getPageBlocks(){

        return $this->pageBlocks;
    }

    /**
     * Return main page template name
     *
     * @param   nothing
     * @throws  nothing
     * @return  main page template name
     */
    public function getMainPageTemplateName(){

        global $PAGES_CONFIG;

        return $PAGES_CONFIG[strtoupper($this->selectedLanguageName)]["SMARTY_TEMPLATE_MAIN_PAGE_TEMPLATE_NAME"];
    }

    /**
     * Return page template name
     *
     * @param   nothing
     * @throws  nothing
     * @return  page template name
     */
    public function getPageTemplateName(){

        global $PAGES_CONFIG;

        return $PAGES_CONFIG[strtoupper($this->selectedLanguageName)]["SMARTY_TEMPLATE_PAGE_TEMPLATE_NAME"];
    }

    /**
     * Return site map template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  site map template name
     */
    public function getSiteMapTemplateName(){

        return SITE_MAP_TEMPLATE_NAME;

    }

    /**
     * Return external template path
     *
     * @param   nothing
     * @throws  no throws
     * @return  external template path
     */
    public function getExternalTemplate(){

        return $this->externalTemplate;
    }

    /**
     * Get top level menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of top level menu
	 * @todo @piggy add menu img and id
     */
    public function getTopLevelMenu(){

	    $СurrentList = array();

        foreach($this->ItemsList as $MenuItem){
            if ($MenuItem->parent == 0 && $MenuItem->hidden == 0){

                $deactivated = 0;
                if (!empty($MenuItem->redirect)){
                    $deactivated = 1;
                }

                $СurrentList[$MenuItem->id] = array('Id'        => $MenuItem->id,
													'Name'      => $MenuItem->name,
                                                    'Url'       => $this->portalUrl.$this->selectedLanguageUrl.$MenuItem->cpu.'/',
                                                    'ShortUrl' => $MenuItem->cpu,
                                                    'Deactivated'=>$deactivated);

				if (MENU_TOP_LEVEL_IMG) {
					$img = str_replace('{id}', $MenuItem->id,
						   str_replace('{lang}', $this->selectedLanguageName,
						   MENU_TOP_LEVEL_IMG_PATH));
					if ( is_file($img) ) {
						$imgSize = getimagesize($img);
						$СurrentList[$MenuItem->id] ['img'] = $img;
						$СurrentList[$MenuItem->id] ['size'] = $imgSize[3];
					}
				}//Леша извини, но нужно было быстро. Хотя по мне - и так пойдет :)
            }
        }

        return $СurrentList;
    }

    /**
     * Get sub menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of sub menu
     */
    public function getSubMenu(){

        $CurrentList        = array();
        $pageIdOnTopLevel   = $this->getPageIdOnTopLevel();
        $pageUrlOnTopLevel  = $this->getPageUrlOnTopLevel();

        if ($this->isMainPage()){
            return array();
        }

        foreach($this->ItemsList as $MenuItem){
            if ($MenuItem->parent == $pageIdOnTopLevel && $MenuItem->hidden == 0){

                $deactivated = 0;
                if (!empty($MenuItem->redirect)){
                    $deactivated = 1;
                }

                $CurrentList[$MenuItem->id] = array('Name'      => $MenuItem->name,
                                                    'Url'       => $this->portalUrl.$this->selectedLanguageUrl."$pageUrlOnTopLevel/".$MenuItem->cpu.'/',
                                                    'ShortUrl'  => $MenuItem->cpu,
                                                    'Deactivated'=>$deactivated);
            }
        }

        return $CurrentList;
    }

    /**
     * Get sub2 menu
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of sub menu
     */
    public function getSub2Menu(){

        $CurrentList            = array();
        $pageIdOnSecondLevel    = $this->getPageIdOnSecondLevel();
        $pageUrlOnTopLevel      = $this->getPageUrlOnTopLevel();
        $pageUrlOnSecondLevel   = $this->getPageUrlOnSecondLevel();

        if ($this->isMainPage()){
            return array();
        }

        if ($pageIdOnSecondLevel == 0){
            return array();
        }

        foreach($this->ItemsList as $MenuItem){
            if ($MenuItem->parent == $pageIdOnSecondLevel && $MenuItem->hidden == 0){

                $deactivated = 0;
                if (!empty($MenuItem->redirect)){
                    $deactivated = 1;
                }

                $CurrentList[$MenuItem->id] = array('Name'      => $MenuItem->name,
                                                    'Url'       => $this->portalUrl.$this->selectedLanguageUrl."$pageUrlOnTopLevel/$pageUrlOnSecondLevel/".$MenuItem->cpu.'/',
                                                    'ShortUrl'  => $MenuItem->cpu,
                                                    'Deactivated'=>$deactivated);
            }
        }

        return $CurrentList;
    }

    /**
     * Return site map
     *
     * @param   nothing
     * @throws  no throws
     * @return  site map
     */
    public function getSiteMap(){

        $deep = 0;
        $parent = 0;
        $result = array();
		$url = $this->portalUrl.$this->selectedLanguageUrl;

        $result = $this->menu(array(), $deep, $parent, $url);

        return $result;

    }

    /**
     * Return all pages list
     *
     * @param   $pageId parent Id
     * @throws  no throws
     * @return  all pages list
     */
    public function getAllPagesList($pageId = 0) {

        $deep = 0;
        $parent = $pageId;
        $result = array();
		$url = $this->portalUrl.$this->selectedLanguageUrl;

        $result = $this->menuAdmin(array(), $deep, $parent, $url, $pageId);

        return $result;

    }

    /**
     * Return url by page id
     *
     * @param   $id page id
     * @throws  no throws
     * @return  url by page id
     */
    public function getSearchBackwardUrl($id) {

        $relativeUrl = '';

        $parentId = $this->cacheIds[$id]->parent;

        while ($parentId != 0){

            $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
            $parentId    = $this->cacheIds[$parentId]->parent;

        }

        return $this->getSiteTranslatedSiteUrl() . $relativeUrl . $this->cacheIds[$id]->cpu . '/';

    }

    /**
     * Return page picture by page id
     *
     * @param   $id page id
     * @throws  no throws
     * @return  page picture by page id
     */
    public function getSearchBackwardPictureUrl($id) {

        return '';

    }

    /**
     * Return formatted date
     *
     * @param   string  $date   date in MySQL date format (YYYY-MM-DD) for formatting
     * @throws  nothing
     * @return  formatted date
     */
    public function formateDate($date) {

        $month      = substr($date,5,2);
        $monthNames = $this->getMonthesNames();

        if ($date == '0000-00-00'){
            throw new ExceptionExt("Incorrect date setup ($date)");
        }

        return array(   "Year"          => substr($date,0,4),
                        "Month"         => substr($date,5,2),
                        "Day"           => sprintf("%d", substr($date,8,2)),
                        "MonthName"     => $monthNames[$month]['Name'],
                        "MonthNameP"    => $monthNames[$month]['NameP']
            );
    }

    /**
     * Return monthes names depends from current language
     *
     * @param   nothing
     * @throws  if corrent translation not exists
     * @return  monthes names depends from current language
     */
    public function getMonthesNames() {

        switch($this->selectedLanguageName){
            case "ru":
                return $this->getAllMonthesRu();
                break;
            case "en":
                return $this->getAllMonthesEn();
                break;
            case "ua":
                return $this->getAllMonthesUa();
                break;
            default:
                throw new ExceptionExt("Selected language translation not exists({$this->selectedLanguageName})");
        }
    }

    /**
     * Return list of russian month names
     *
     * @param   nothing
     * @throws  no throws
     * @return  list of russian month names
     */
    private function getAllMonthesRu(){

        return array("01" => array( "Name"    => "Январь",
                                    "NameP"   => "января",
                                    "Value"   => "01",
                                    "Url"     => ""
            ),
                     "02" => array( "Name"    => "Февраль",
                                    "NameP"   => "Февраля",
                                    "Value"   => "02",
                                    "Url"     => ""
            ),
                     "03" => array( "Name"    => "Март",
                                    "NameP"   => "марта",
                                    "Value"   => "03",
                                    "Url"     => ""
            ),
                     "04" => array( "Name"    => "Апрель",
                                    "NameP"   => "апреля",
                                    "Value"   => "04",
                                    "Url"     => ""
            ),
                     "05" => array( "Name"    => "Май",
                                    "NameP"   => "мая",
                                    "Value"   => "05",
                                    "Url"     => ""
            ),
                     "06" => array( "Name"    => "Июнь",
                                    "NameP"   => "июня",
                                    "Value"   => "06",
                                    "Url"     => ""
            ),
                     "07" => array( "Name"    => "Июль",
                                    "NameP"   => "июля",
                                    "Value"   => "07",
                                    "Url"     => ""
            ),
                     "08" => array( "Name"    => "Август",
                                    "NameP"   => "августа",
                                    "Value"   => "08",
                                    "Url"     => ""
            ),
                     "09" => array( "Name"    => "Сентябрь",
                                    "NameP"   => "сентября",
                                    "Value"   => "09",
                                    "Url"     => ""
            ),
                     "10" => array( "Name"   => "Октябрь",
                                    "NameP"  => "октября",
                                    "Value"  => "10",
                                    "Url"    => ""
            ),
                     "11" => array( "Name"   => "Ноябрь",
                                    "NameP"  => "ноября",
                                    "Value"  => "11",
                                    "Url"    => ""
            ),
                     "12" => array( "Name"   => "Декабрь",
                                    "NameP"  => "декабря",
                                    "Value"  => "12",
                                    "Url"    => ""
            )
        );
    }

    /**
     * Return list of english month names
     *
     * @param   nothing
     * @throws  no throws
     * @return  list of russian month names
     */
    private function getAllMonthesEn(){

         return array("01" => array( "Name"   => "January",
                                     "NameP"  => "january",
                                     "Value"  => "01",
                                     "Url"    => ""
             ),
                      "02" => array( "Name"   => "February",
                                     "NameP"  => "february",
                                     "Value"  => "02",
                                     "Url"    => ""
             ),
                      "03" => array( "Name"   => "March",
                                     "NameP"  => "march",
                                     "Value"  => "03",
                                     "Url"    => ""
             ),
                      "04" => array( "Name"   => "April",
                                     "NameP"  => "april",
                                     "Value"  => "04",
                                     "Url"    => ""
             ),
                      "05" => array( "Name"   => "May",
                                     "NameP"  => "may",
                                     "Value"  => "05",
                                     "Url"    => ""
             ),
                      "06" => array( "Name"   => "June",
                                     "NameP"  => "june",
                                     "Value"  => "06",
                                     "Url"    => ""
             ),
                      "07" => array( "Name"   => "July",
                                     "NameP"  => "july",
                                     "Value"  => "07",
                                     "Url"    => ""
             ),
                      "08" => array( "Name"   => "August",
                                     "NameP"  => "august",
                                     "Value"  => "08",
                                     "Url"    => ""
             ),
                      "09" => array( "Name"   => "September",
                                     "NameP"  => "september",
                                     "Value"  => "09",
                                     "Url"    => ""
             ),
                      "10" => array( "Name"   => "October",
                                     "NameP"  => "october",
                                     "Value"  => "10",
                                     "Url"    => ""
             ),
                      "11" => array( "Name"   => "November",
                                     "NameP"  => "november",
                                     "Value"  => "11",
                                     "Url"    => ""
             ),
                      "12" => array( "Name"   => "December",
                                     "NameP"  => "december",
                                     "Value"  => "12",
                                     "Url"    => ""
             )
         );
     }

    /**
     * Return list of ukranian month names
     *
     * @param   nothing
     * @throws  no throws
     * @return  list of russian month names
     */
    private function getAllMonthesUa(){

         return array("01" => array( "Name"   => "Січень",
                                     "NameP"  => "січень",
                                     "Value"  => "01",
                                     "Url"    => ""
             ),
                      "02" => array( "Name"   => "Лютий",
                                     "NameP"  => "лютий",
                                     "Value"  => "02",
                                     "Url"    => ""
             ),
                      "03" => array( "Name"   => "Март",
                                     "NameP"  => "марта",
                                     "Value"  => "03",
                                     "Url"    => ""
             ),
                      "04" => array( "Name"   => "Квітень",
                                     "NameP"  => "квітень",
                                     "Value"  => "04",
                                     "Url"    => ""
             ),
                      "05" => array( "Name"   => "Травень",
                                     "NameP"  => "травень",
                                     "Value"  => "05",
                                     "Url"    => ""
             ),
                      "06" => array( "Name"   => "Червень",
                                     "NameP"  => "червень",
                                     "Value"  => "06",
                                     "Url"    => ""
             ),
                      "07" => array( "Name"   => "Липень",
                                     "NameP"  => "липень",
                                     "Value"  => "07",
                                     "Url"    => ""
             ),
                      "08" => array( "Name"   => "Серпень",
                                     "NameP"  => "серпня",
                                     "Value"  => "08",
                                     "Url"    => ""
             ),
                      "09" => array( "Name"   => "Вересень",
                                     "NameP"  => "вересень",
                                     "Value"  => "09",
                                     "Url"    => ""
             ),
                      "10" => array( "Name"   => "Жовтень",
                                     "NameP"  => "жовтень",
                                     "Value"  => "10",
                                     "Url"    => ""
             ),
                      "11" => array( "Name"   => "Листопад",
                                     "NameP"  => "листопад",
                                     "Value"  => "11",
                                     "Url"    => ""
             ),
                      "12" => array( "Name"   => "Грудень",
                                     "NameP"  => "грудень",
                                     "Value"  => "12",
                                     "Url"    => ""
             )
         );
     }
}
?>