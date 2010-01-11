<?php
/**
 * Description of ModuleGallery
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleGallery {

    const   wideAllMenu = 0;

    protected $MySQL                  = null;
    protected $SiteStructure          = null;
    protected $AttributeOperations    = null;
    protected $InputFilter            = null;
	protected $Session				  = null;

    protected $ItemsList              = array();
    protected $cacheIds               = array();
    protected $cacheCpu               = array();
    protected $cacheParent            = array();

    protected $moduleParams           = array();
	
	protected $languageId           = 0;
    protected $modifiedId           = 0;

    public $typeId                  = 0;
    public $catId                   = 0;
    public $params                  = 0;
    public $catMassive				= null;

    /**
     * Constructor of class ModuleCataloge
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure, AttributeOperations $AttributeOperations, InputFilter $InputFilter) {

        if ($MySQL == null){
            throw new ExceptionExt('MySQL reference not defined');;
        }

        if ($SiteStructure == null){
            throw new ExceptionExt('MySQL reference not defined');
        }

        if ($AttributeOperations == null){
            throw new ExceptionExt('AttributeOperations reference not defined');
        }

        if ($InputFilter == null){
            throw new ExceptionExt('AttributeOperations reference not defined');
        }

        $this->MySQL                = $MySQL;
        $this->SiteStructure        = $SiteStructure;
        $this->AttributeOperations  = $AttributeOperations;
        $this->InputFilter          = $InputFilter;

        $this->setModuleParameters();

    }

    /**
     * Used for passing references to needed classes into this class
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    final public function setClassesHandlers(MySQL $MySQL, SiteStructure $SiteStructure, InputFilter $InputFilter, AttributeOperations $AttributeOperations){

        $this->MySQL                = $MySQL;
        $this->SiteStructure        = $SiteStructure;
        $this->InputFilter          = $InputFilter;
        $this->AttributeOperations  = $AttributeOperations;

        $this->setModuleParameters();

    }

    /**
     * Setup module parameters
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function setModuleParameters(){

		$language	= $this->SiteStructure->getLanguageId();

		if (!$this->isMenuGalleryDislpay() && !$this->isLatestPhotosDisplay() && !$this->isRandomPhotosDisplay()){
            return true;
        }

        $this->MySQL->query("
							SELECT
								gal_types.id AS id,
								gal_types_languages.name AS name,
								gal_types.name_cpu AS name_cpu,
								gal_types.parent_id AS parent_id,
								gal_types_languages.description AS description
							FROM
								gal_types
								Left Join gal_types_languages ON gal_types_languages.types_id = gal_types.id AND gal_types_languages.language_id = '". $language ."'
							WHERE
								`active` = 1");

        while ($row = $this->MySQL->fetchArray()){

            $newObject = new ModuleGalleryItem($row['id'], $row['name'], $row['name_cpu'], $row['parent_id'], $row['description']);

            $this->ItemsList[]                      = $newObject;
            $this->cacheIds[$row['id']]             = $newObject;
            $this->cacheCpu[$row['name_cpu']]       = $newObject;
            $this->cacheParent[$row['parent_id']][] = $newObject;

        }

        $params = array();
        $params = $this->SiteStructure->getModuleParameters();

        if (count($params) == 0){
            return true;
        }

        $last = $params[count($params)-1];

        if (isset($last) && is_numeric($last)){

            array_pop($params);
            $this->catId = $last;

        } else {

            $this->catId = 0;
        }

        foreach($params as $param){
            
            if (isset($this->cacheCpu[$param])){
                $this->moduleParams[] = $param;
            }
            
        }

		$paramsId = '';
        foreach($params as $k => $v){
            $this->MySQL->query("select * from gal_types where `active` = 1 and name_cpu = '". $v ."'");
            $row = $this->MySQL->fetchArray();
            ($row['id'] != '') ? $paramsId[] = $row['id'] : '';
        }

        $this->typeId = $paramsId;
        $this->params = $params;
        
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

                $active = 0;

                if (isset($params[$deep]) && $params[$deep] == $item->cpu){
                    $active = 1;
                }

                if (self::wideAllMenu){
                    $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');
                } else if ($active == 1){
                    $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');
                } else {
                    $sub = '';
                }

                $result[] = array('id'          => $item->id,
                                  'url'         => $url.$item->cpu.'/',
                                  'name'        => $item->name,
                                  'description' => $item->description,
                                  'active'      => $active,
                                  'sub'         => $sub
                );
        }
        return $result;
    }

    /**
     * Return true if Gallery module used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if gallery module used
     */
    public function isGalleryUsed(){

        if ($this->AttributeOperations->getModuleName() == 'gallery'){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if called single item
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if called single item
     */
    public function isSingleDisplay(){

        if ($this->catId != 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return template name for current page
     *
     * @param   nothing
     * @throws  no throws
     * @return  template name
     */
    public function getTemplateName(){

        if ($this->isSingleDisplay()){

            return GALLERY_MODULE_SINGLE_TEMPLATE_NAME;

        } else {

            return GALLERY_MODULE_LIST_TEMPLATE_NAME;
        }
    }

	/**
     * Return true if latest photos list display
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if latest photos list display
     */
    public function isLatestPhotosDisplay() {

        return GALLERY_MODULE_DISPLAY_LATEST_PRODUCTS;

    }

    /**
     * Return true if random photos list display
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if random photos list display
     */
    public function isRandomPhotosDisplay() {

        return GALLERY_MODULE_DISPLAY_RANDOM_PRODUCTS;

    }

	    /**
     * Return template name for latest Photos
     *
     * @param   nothing
     * @throws  no throws
     * @return  template name for latest Photos
     */
    public function getLatestPhotosTemplateName() {

        return GALLERY_MODULE_LATEST_PRODUCTS_TEMPLATE_NAME;

    }

    /**
     * Return template name for random Photos
     *
     * @param   nothing
     * @throws  no throws
     * @return  template name for random Photos
     */
    public function getRandomPhotosTemplateName() {

        return GALLERY_MODULE_RANDOM_PRODUCTS_TEMPLATE_NAME;

    }

    /*
     * Returns product url by type id and product id
     *
     * @param   $typeId type id
     * @param   $productId product id
     * @throws  no throws
     * @return  product url by product id
     */
    public function getSearchBackwardUrl($typeId, $photoId) {

        $relativeUrl = '';

        $parentId    = $this->cacheIds[$typeId]->parent;

        while ($parentId != 0){

            $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
            $parentId    = $this->cacheIds[$parentId]->parent;

        }

        return GALLERY_RELATIVE_URL . '/' . $relativeUrl . $this->cacheIds[$typeId]->cpu . '/' . $photoId;

    }

    /**
     * Returns product picture by product id
     *
     * @param   $productId product id
     * @throws  no throws
     * @return  product picture by product id
     */
    public function getSearchBackwardPictureUrl($photoId) {

        $fileName = GALLERY_MODULE_IMG_FOLDER_WEB . "s-$photoId.jpg";

        if (file_exists($fileName)){

            return $fileName;

        } else {

            return '';

        }
    }

	/**
     * Return list of random Catalogue items
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  list of Catalogue
     */
    public function getRandomPhotos(){

        $result		= array();
        $language	= $this->SiteStructure->getLanguageId();

        if (!GALLERY_MODULE_DISPLAY_RANDOM_PRODUCTS){
            return $result;
        }

        $limit          = 'LIMIT ' . GALLERY_MODULE_RANDOM_PRODUCTS_LIMIT;
        $count          = 0;
        $query          = array();
        $randomNumbers  = array();
        $randomValue    = 0;
        $url            = '';
        $relativeUrl    = '';
        $mainUrl        = $this->SiteStructure->getSiteTranslatedSiteUrl() . GALLERY_RELATIVE_URL . "/";

        $this->MySQL->query("SELECT count(*) as count FROM gal_photos Inner Join gal_link_photos_types ON gal_photos.id = gal_link_photos_types.photo_id");
        $row = $this->MySQL->fetchArray();
        if ($row !== false){
            $count  = $row['count'] - 1;
        }

        if ($count < GALLERY_MODULE_RANDOM_PRODUCTS_LIMIT){
            throw new ExceptionExt('Can not generate random products list because products count less then random products required');
        }

        for ($i=0;$i<GALLERY_MODULE_RANDOM_PRODUCTS_LIMIT;$i++){

            $randomValue = rand(0, $count);
            while (in_array($randomValue, $randomNumbers)){
                $randomValue = rand(0, $count);
            }

            $randomNumbers[] = $randomValue;
            $limit   = "LIMIT $randomValue, 1";

            $query[] = "(SELECT
								gal_photos.id AS photo_id,
								gal_photos_languages.name AS photo_name,
								gal_photos_languages.description AS photo_description,
								gal_photos.article AS photo_article,
								gal_rating.rating AS photo_rating,
								gal_types.id AS type_id,
								gal_types.name_cpu,
								gal_types.parent_id AS type_parent_id
							FROM
								gal_link_photos_types
								LEFT JOIN gal_photos_languages ON gal_photos_languages.photo_id = gal_link_photos_types.photo_id AND gal_photos_languages.language_id = '". $language ."'
								LEFT JOIN gal_photos ON gal_photos.id = gal_link_photos_types.photo_id
								LEFT JOIN gal_rating ON gal_photos.id = gal_rating.photo
								INNER JOIN gal_types ON gal_link_photos_types.types_id = gal_types.id
                            WHERE
                                gal_photos.active = 1
                              $limit )";
        }

        $query = implode(' UNION ', $query);

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $parentId    = $row['type_parent_id'];
            $relativeUrl = '';

            while ($parentId != 0){

                $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
                $parentId    = $this->cacheIds[$parentId]->parent;

            }

            $url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['photo_id'];

			$img = $this->getImagePath($row['photo_id']);

            $result[] = array(	'img'         =>  $img,
                                'url'         =>  $url,
                                'id'          =>  $row['photo_id'],
                                'name'        =>  $row['photo_name'],
                                'description' =>  $row['photo_description'],
                                'article'     =>  $row['photo_article']
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

	/**
     * Return list of latest Catalogue items
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  list of Catalogue
     */
    public function getLatestPhotos(){

        $result		= array();
        $language	= $this->SiteStructure->getLanguageId();

        if (!GALLERY_MODULE_DISPLAY_LATEST_PRODUCTS){
            return $result;
        }

        $where          = '';
        $limit          = 'LIMIT ' . GALLERY_MODULE_LATEST_PRODUCTS_LIMIT;
        $url            = '';
        $relativeUrl    = '';
        $mainUrl        = $this->SiteStructure->getSiteTranslatedSiteUrl() . GALLERY_RELATIVE_URL . "/";

		$this->MySQL->query("SELECT
								gal_photos.id AS photo_id,
								gal_photos_languages.name AS photo_name,
								gal_photos_languages.description AS photo_description,
								gal_photos.active AS photo_active,
								gal_photos.article AS photo_article,
								gal_rating.rating AS photo_rating,
								gal_types.id AS type_id,
								gal_types.name_cpu,
								gal_types.parent_id AS type_parent_id
							FROM
								gal_link_photos_types
								LEFT JOIN gal_photos_languages ON gal_photos_languages.photo_id = gal_link_photos_types.photo_id AND gal_photos_languages.language_id = '". $language ."'
								LEFT JOIN gal_photos ON gal_photos.id = gal_link_photos_types.photo_id
								LEFT JOIN gal_rating ON gal_photos.id = gal_rating.photo
								INNER JOIN gal_types ON gal_link_photos_types.types_id = gal_types.id
                            WHERE
                                gal_photos.active = 1 ". $where ."
							ORDER BY
                                gal_photos.id DESC
                            $limit ");

        while ($row = $this->MySQL->fetchArray()){

            $parentId    = $row['type_parent_id'];
            $relativeUrl = '';

            while ($parentId != 0){

                $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
                $parentId    = $this->cacheIds[$parentId]->parent;

            }

            $url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['photo_id'];
			$img = $this->getImagePath($row['photo_id']);

            $result[] = array(	'img'             =>  $img,
                                'url'               =>  $url,
                                'id'                =>  $row['photo_id'],
                                'active'            =>  $row['photo_active'],
                                'name'              =>	$row['photo_name'],
                                'description'       =>  $row['photo_description'],
                                'article'           =>  $row['photo_article']
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return list of Gallery elements
     * i.e. list of News/Articles/FAQS headers and dates
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @throws  if wrong module used
     * @return  list of Gallery
     */
    public function getList(){

        if ($this->isSingleDisplay()){
            return "";
        }

        $result		= array();
        $startTag	= 0;
        $endTag		= 0;
        $params		= $this->params;
        $limit		= '';
        $where		= '';
        $url        = '';
        $relativeUrl= '';
        $mainUrl    = $this->SiteStructure->getAbsolutePageUrl();
		$language	= $this->SiteStructure->getLanguageId();

        $typeId = $this->typeId;
        if ($typeId != ''){
            $last  = implode(array_slice($typeId, -1, 1));
            $limit = '';
            $where = "AND gal_link_photos_types.types_id = '". $last ."'";
        }

		$this->MySQL->query("SELECT
								gal_photos.id AS photo_id,
								gal_photos_languages.name AS photo_name,
								gal_photos_languages.description AS photo_description,
								gal_photos.article AS photo_article,
								gal_rating.rating AS photo_rating,
								gal_types.id AS type_id,
								gal_types.name_cpu,
								gal_types.parent_id AS type_parent_id
							FROM
								gal_link_photos_types
								LEFT JOIN gal_photos_languages ON gal_photos_languages.photo_id = gal_link_photos_types.photo_id AND gal_photos_languages.language_id = '". $language ."'
								LEFT JOIN gal_photos ON gal_photos.id = gal_link_photos_types.photo_id
								LEFT JOIN gal_rating ON gal_photos.id = gal_rating.photo
								INNER JOIN gal_types ON gal_link_photos_types.types_id = gal_types.id
                            WHERE
                                gal_photos.active = 1 ". $where ."
                             ". $limit ."");//gal_types.name,gal_types.description,
							 
		if (!GALLERY_ISSET_TYPE) {
			$this->MySQL->query("SELECT
									gal_photos.id AS photo_id,
									gal_photos.name AS photo_name,
									gal_photos.description AS photo_description,
									gal_photos.article AS photo_article,
									gal_rating.rating AS photo_rating,
									gal_rating.`type`
								FROM
									gal_photos
									LEFT JOIN gal_rating ON photo_id = gal_rating.photo
								WHERE
									gal_photos.active = 1
								 ". $limit ."");
		}
		

        $i = 0;
		$countRows = $this->MySQL->countRows();
		while ($row = $this->MySQL->fetchArray()){

            $url = '';
			if (GALLERY_ISSET_TYPE) {
				$parentId    = $row['type_parent_id'];
				$relativeUrl = '';

				while ($parentId != 0){

					$relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
					$parentId    = $this->cacheIds[$parentId]->parent;

				}

				$url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['photo_id'];
			}

			$i++;
			if (GALLERY_IMG_COUNT_ROW == $i OR $i == $countRows) {
				if ($i == $countRows) {
					$i = GALLERY_IMG_COUNT_ROW - $countRows;
				} else {
					$i = 0;
				}
			}

			$img = $this->getImagePath($row['photo_id']);
			
            $result[] = array(	'img'				=>	$img,
                                'url'				=>	$url,
                                'name'				=>	$row['photo_name'],
                                'description'		=>	$row['photo_description'],
                                'article'			=>	$row['photo_article'],
                                'rating'			=>	$row['photo_rating'],
								'addClearRows'		=>	$i,
								'countRows'			=>	GALLERY_IMG_COUNT_ROW
            );
			
			
		}
		

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return full info about single Gallery item
     *
     * @param   nothing
     * @throws  inherited from MySQL
     * @throws  if wrong module used
     * @return  one full Gallery item
     */
    public function getSingle(){

        if (!$this->isSingleDisplay()){
            return "";
        }

        $language	= $this->SiteStructure->getLanguageId();

        $result = array();
        $catId  = 0;
        $catId = $this->catId;

        $this->MySQL->query("SELECT
								gal_photos.id AS photo_id,
								gal_photos_languages.name AS photo_name,
								gal_photos_languages.description AS photo_description,
								gal_photos.article AS photo_article,
								gal_rating.rating AS photo_rating,
								gal_types.id AS type_id,
								gal_types.name_cpu,
								gal_types.parent_id AS type_parent_id
							FROM
								gal_link_photos_types
								LEFT JOIN gal_photos_languages ON gal_photos_languages.photo_id = gal_link_photos_types.photo_id AND gal_photos_languages.language_id = '". $language ."'
								Left Join gal_photos ON gal_photos.id = gal_link_photos_types.photo_id
								Left Join gal_rating ON gal_photos.id = gal_rating.photo
								Inner Join gal_types ON gal_link_photos_types.types_id = gal_types.id
                            WHERE
                                gal_photos.active = 1 AND gal_photos.id = " .$catId ."");

        while ($row = $this->MySQL->fetchArray()){

			$img = $this->getImagePath($row['photo_id']);

            $result = array(
                            's_img'				=>	$img,
                            'name'				=>	$row['photo_name'],
                            'description'		=>	$row['photo_description'],
                            'article'			=>	$row['photo_article'],
                            'rating'			=>	$row['photo_rating'],
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return true if menu should display on this page
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if menu should display on this page
     */
    public function isMenuGalleryDislpay(){

        if (GALLERY_DISPLAY_MENU){
			if (GALLERY_DISPLAY_MENU_ALWAYS){
				return true;
			} else {
				if ($this->isGalleryUsed()){
					return true;
				} else {
					return false;
				}			
			}
		} else {
			return false;
		}
		
    }

    /**
     * Return menu for Gallery
     *
     * @param   nothing
     * @throws  inherited from MySQL
     * @throws  if wrong module used
     * @return  menu for Gallery
     */
    public function getGalleryMenu(){

        if (!$this->isMenuGalleryDislpay()){
            return "";
        }

        $deep = 0;
        $parent = 0;
        $result = array();
		$url = '/'.WEB_PORTAL_URL.'/'.GALLERY_RELATIVE_URL.'/';

        $result = $this->menu($this->moduleParams, $deep, $parent, $url);

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

        if (GALLERY_MODULE_USE_ADMIN && $this->Session->isAuthorized()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return admin panel wrapper template name
     * Used to wrap single edited element before save
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin panel wrapper template name
     */
    public function getAdminWrapperTemplateName(){

        return ADMIN_RESPONSE_WRAPPER_TEMPLATE_NAME;

    }

    /**
     * Return admin panel empty template name
     * Used to generate hyper ref for text1 field
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin panel empty template name
     */
    public function getAdminEmptyTemplateName(){

        return ADMIN_EMPTY_TEMPLATE_NAME;

    }

    /**
     * Return admin panel top panel template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin panel top panel template name
     */
    /*public function getAdminTopPanelTemplateName(){

        return GALLERY_ADMIN_TOP_PANEL_TEMPLATE_NAME;

    }*/

    /**
     * Add new product from popup window
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addProductFull(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        if (empty($this->typeId)){
            throw new ExceptionExt("New data can not be added on top catalogue menu");
        }

        $name       = (string)  isset( $newData["photo_name"]     ) ? $newData["photo_name"]        : '';
        $desc       = (string)  isset( $newData["photo_desc"]     ) ? $newData["photo_desc"]        : '';
        $article    = (string)  isset( $newData["photo_article"]  ) ? $newData["photo_article"]     : '';

        $brendId    = (integer) isset( $newData["photo_brend"]    ) ? $newData["photo_brend"]       : 0;
        $active     = (integer) isset( $newData["active"]         ) ? $newData["active"]            : 0;
        $exist      = (integer) isset( $newData["photo_exist"]    ) ? $newData["photo_exist"]       : 0;
        $priceType  = (integer) isset( $newData["price_type"]     ) ? $newData["price_type"]        : 0;

        $typeId     = $this->typeId[count($this->typeId) - 1];

        $this->MySQL->query("INSERT INTO gal_photos (`article`, `active`) VALUES ('$article', $active)");

        $productId = $this->MySQL->insertedId();

        $this->MySQL->query("INSERT INTO gal_photos_languages (`photo_id`, `language_id`, `name`, `description`) VALUES ($productId, {$this->languageId}, '$name', '$desc')");

        $this->MySQL->query("INSERT INTO gal_link_products_types (`photo_id`, `types_id`) VALUES ($productId, $typeId)");

        $this->modifiedId = $productId;

        $tmpUrlFileName = $this->InputFilter->getParameter("file");

        if (!empty($tmpUrlFileName)){

            $tmpFileName = GALLERY_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            if (preg_match('/\.(\w+?)$/i', $tmpUrlFileName, $match)){
                $extention = $match[1];
            }

            if (empty($extention)){
                throw new ExceptionExt("Can not determine extention of uploaded file");
            }

            $newBigFileName     = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "b-$productId.jpg";
            $newSmallFileName   = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "s-$productId.jpg";

            if (!is_file($tmpFileName)){
                throw new ExceptionExt("Temp picture file not exists");
            }

            if (false === ImageScale::scalePicture($tmpFileName, $newBigFileName, $extention, GALLERY_MODULE_BIG_PICTURE_SIZE, GALLERY_MODULE_NOT_USE_CROP_PICTURE, GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES) ){
                throw new ExceptionExt("Can not decrease picture size for big copy");
            }

            if (false === ImageScale::scalePicture($tmpFileName, $newSmallFileName, $extention, GALLERY_MODULE_SMALL_PICTURE_SIZE, GALLERY_MODULE_NOT_USE_CROP_PICTURE, GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES) ){
                throw new ExceptionExt("Can not decrease picture size for small copy");
            }

            if (false === unlink($tmpFileName)){
                throw new ExceptionExt("Can not remove picture temp file ($tmpFileName)");
            }
        }

        return true;

    }
    /**
     * Edit product from popup window
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function editProductFull(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $productId = (integer)$this->InputFilter->getParameter("id");

        if (empty($productId)){
            throw new ExceptionExt("Id not set");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        $name       = (string)  isset( $newData["product_name"]     ) ? $newData["product_name"]        : '';
        $desc       = (string)  isset( $newData["product_desc"]     ) ? $newData["product_desc"]        : '';
        $article    = (string)  isset( $newData["product_article"]  ) ? $newData["product_article"]     : '';
        $price      = (string)  isset( $newData["price"]            ) ? $newData["price"]               : '';

        $brendId    = (integer) isset( $newData["product_brend"]    ) ? $newData["product_brend"]       : 0;
        $active     = (integer) isset( $newData["active"]           ) ? $newData["active"]              : 0;
        $exist      = (integer) isset( $newData["product_exist"]    ) ? $newData["product_exist"]       : 0;
        $priceType  = (integer) isset( $newData["price_type"]       ) ? $newData["price_type"]          : 0;

        $typeId     = $this->typeId[count($this->typeId) - 1];

        $this->MySQL->query("UPDATE gal_photos SET `article` = '$article', `active` = $active WHERE `id` = $productId");

        $this->MySQL->query("UPDATE gal_photos_languages  SET `name` = '$name', `description` = '$desc' WHERE `photo_id` = $productId and `language_id` = {$this->languageId}");


        $this->modifiedId = $productId;

        $tmpUrlFileName = $this->InputFilter->getParameter("file");

        if (!empty($tmpUrlFileName)){

            $tmpFileName = GALLERY_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            if (preg_match('/\.(\w+?)$/i', $tmpUrlFileName, $match)){
                $extention = $match[1];
            }

            if (empty($extention)){
                throw new ExceptionExt("Can not determine extention of uploaded file");
            }

            $newBigFileName     = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "b-$productId.jpg";
            $newSmallFileName   = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "s-$productId.jpg";

            if (!is_file($tmpFileName)){
                throw new ExceptionExt("Temp picture file not exists");
            }

            if (false === ImageScale::scalePicture($tmpFileName, $newBigFileName, $extention, GALLERY_MODULE_BIG_PICTURE_SIZE, GALLERY_MODULE_NOT_USE_CROP_PICTURE, GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES) ){
                throw new ExceptionExt("Can not decrease picture size for big copy");
            }

            if (false === ImageScale::scalePicture($tmpFileName, $newSmallFileName, $extention, GALLERY_MODULE_SMALL_PICTURE_SIZE, GALLERY_MODULE_NOT_USE_CROP_PICTURE, GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES) ){
                throw new ExceptionExt("Can not decrease picture size for small copy");
            }

            if (false === unlink($tmpFileName)){
                throw new ExceptionExt("Can not remove picture temp file ($tmpFileName)");
            }
        }

        return true;

    }

    /**
     * Remove catalogue products
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeProducts(){

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
                $inStatement .= ", $id";
            }
        }

        $this->MySQL->query("DELETE FROM gal_photos WHERE `id` IN ($inStatement)");

        if($this->MySQL->affectedRows()){

        } else {

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
     * @todo    optimize after getList
     */
    public function getAdminLastModifiedData(){

        $productId = $this->modifiedId;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($productId)){
            throw new ExceptionExt("Id not set");
        }

        $result		= array();
        $startTag	= 0;
        $endTag		= 0;
        $params		= $this->params;
        $limit		= 'LIMIT 1';
        $where		= '';
        $url        = '';
        $relativeUrl= '';
        $mainUrl    = $this->SiteStructure->getAbsolutePageUrl();

        $typeId = $this->typeId;
        if ($typeId != ''){
            $last  = implode(array_slice($typeId, -1, 1));
            $limit = '';
            $where = "AND gal_link_products_types.types_id = '". $last ."'";
        }

        $this->MySQL->query("SELECT
								gal_photos.id AS photo_id,
								gal_photos_languages.name AS photo_name,
								gal_photos_languages.description AS photo_description,
								gal_photos.article AS photo_article,
								gal_rating.rating AS photo_rating,
								gal_types.id AS type_id,

								gal_types.name_cpu,

								gal_types.parent_id AS type_parent_id
							FROM
								gal_link_photos_types
								LEFT JOIN gal_photos_languages ON gal_photos_languages.photo_id = photo_id AND gal_photos_languages.language_id = '". $language ."'
								LEFT JOIN gal_photos ON gal_photos.id = gal_link_photos_types.photo_id
								LEFT JOIN gal_rating ON gal_photos.id = gal_rating.photo
								INNER JOIN gal_types ON gal_link_photos_types.types_id = gal_types.id
                            WHERE
                                gal_photos.active = 1 ". $where ."
                             ". $limit ."");

		if (!GALLERY_ISSET_TYPE) {
			$this->MySQL->query("SELECT
									gal_photos.id AS photo_id,
									gal_photos.name AS photo_name,
									gal_photos.description AS photo_description,
									gal_photos.article AS photo_article,
									gal_rating.rating AS photo_rating,
									gal_rating.`type`
								FROM
									gal_photos
									LEFT JOIN gal_rating ON photo_id = gal_rating.photo
								WHERE
									gal_photos.active = 1
								 ". $limit ."");
		}

        $i = 0;
		$countRows = $this->MySQL->countRows();
		while ($row = $this->MySQL->fetchArray()){

            $url = '';
			if (GALLERY_ISSET_TYPE) {
				$parentId    = $row['type_parent_id'];
				$relativeUrl = '';

				while ($parentId != 0){

					$relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
					$parentId    = $this->cacheIds[$parentId]->parent;

				}

				$url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['photo_id'];
			}

			$i++;
			if (GALLERY_IMG_COUNT_ROW == $i OR $i == $countRows) {
				if ($i == $countRows) {
					$i = GALLERY_IMG_COUNT_ROW - $countRows;
				} else {
					$i = 0;
				}
			}

			$img = $this->getImagePath($row['photo_id']);

            $result[] = array(	'img'				=>	$img,
                                'url'				=>	$url,
                                'name'				=>	$row['photo_name'],
                                'description'		=>	$row['photo_description'],
                                'article'			=>	$row['photo_article'],
                                'rating'			=>	$row['photo_rating'],
								'addClearRows'		=>	$i,
								'countRows'			=>	GALLERY_IMG_COUNT_ROW
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return one element by id for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  one element by nafId for admin panel
     * @todo    add limit 1 to all updates (except activity)
     */
    public function saveAdminElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $id             = (integer)$this->InputFilter->getParameter("id");
        $elementName    = (string)$this->InputFilter->getParameter("type");
        $elementValue   = (string)$this->InputFilter->getParameter("value");

        if (empty($id)){
            throw new ExceptionExt("Id not set");
        }

        switch ($elementName){
            case "photo_name":
                $select     = "name";
                $table      = "gal_products_languages";
                $type       = "text";
                break;
            case "photo_desc":
                $select     = "description";
                $table      = "gal_products_languages";
                $type       = "text";
                break;
            case "photo_article":
                $select     = "article";
                $table      = "gal_products";
                $type       = "text";
                break;
            case "photo_exist":
                $select     = "exist";
                $table      = "gal_products";
                $type       = "text";
                break;
            default:
                throw new ExceptionExt("Incorrect field type");
                break;
        }

        switch ($table){
            case "gal_products":

                $query = "UPDATE gal_photos SET `$select` = '$elementValue' WHERE `id` = $id";

                break;
            case "gal_products_languages":

                $query = "UPDATE gal_photos_languages SET `$select`  = '$elementValue' WHERE `photo_id` = $id and `language_id` = {$this->languageId}";

                break;
        }

        $this->MySQL->query($query);

        if(!$this->MySQL->affectedRows()){

            //throw new ExceptionExt("Element not updated");

        }

        return true;

    }

    /**
     * Return one element by id for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  one element by id for admin panel
     */
    public function getAdminLoadElement(){

        $result = array('value'=>'','type'=>'','id'=>'','field'=>'');
        $table  = '';
        $type   = '';

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $id             = (integer)$this->InputFilter->getParameter("id");
        $elementName    = (string)$this->InputFilter->getParameter("type");

        if (empty($id)){
            throw new ExceptionExt("Id not set");
        }

        switch ($elementName){
            case "photo_name":
                $select     = "name";
                $table      = "gal_photos_languages";
                $type       = "text";
                break;
            case "photo_desc":
                $select     = "description";
                $table      = "gal_photos_languages";
                $type       = "text";
                break;
            default:
                throw new ExceptionExt("Incorrect field type");
                break;
        }

        switch ($table){
            case "gal_photos":

                $query = "select `id`, `$select` from gal_photos where `id` = $id";

                break;
            case "gal_photos_languages":

                $query = "select `photo_id` as `id`, `$select` from gal_photos_languages where `photo_id` = $id and `language_id` = {$this->languageId}";

                break;
        }

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row[$select] = htmlspecialchars_decode($row[$select],ENT_QUOTES);

            $result = array('value' => $row[$select],
                            'type'  => $type,
                            'id'    => $row['id'],
                            'field' => $elementName
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Inverse activity of Naf element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function inverseActivityNafElement(){

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
                $inStatement .= ", $id";
            }
        }

        $this->MySQL->query("UPDATE gal_photos SET `active` = NOT `active` WHERE `id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Activity not inversed");

        }

        return true;
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

        $result = array( 'lines' =>  array(), 'title' => 'Редактирование' );

        $productId  = (integer)$this->InputFilter->getParameter("id");

        if (empty($productId)){
            throw new ExceptionExt("Id not set");
        }

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['lines'] = $ADMIN_WINDOW[$page];

		$this->MySQL->query("SELECT
								gal_photos.id AS photo_id,
								gal_photos_languages.name AS photo_name,
								gal_photos_languages.description AS photo_description,
								gal_photos.article AS photo_article,
								gal_photos.active AS photo_active,
								gal_rating.rating AS photo_rating,
								gal_types.id AS type_id,
								gal_types.name_cpu,
								gal_types.parent_id AS type_parent_id
							FROM
								gal_link_photos_types
								LEFT JOIN gal_photos_languages ON gal_photos_languages.photo_id = photo_id AND gal_photos_languages.language_id = '". $language ."'
								Left Join gal_photos ON gal_photos.id = gal_link_photos_types.photo_id
								Left Join gal_rating ON gal_photos.id = gal_rating.photo
								Inner Join gal_types ON gal_link_photos_types.types_id = gal_types.id
                            WHERE
                                gal_photos.id = " .$productId ."");

        while ($row = $this->MySQL->fetchArray()){

            foreach ($result['lines'] as &$field){

                switch ($field['name']){
                    case "photo_name":
                        $field['value'] = $row['photo_name'];
                        break;
                    case "photo_desc":
                        $field['value'] = $row['photo_description'];
                        break;
                    case "photo_article":
                        $field['value'] = $row['photo_article'];
                        break;
                    case "active":
                        $field['value'] = $row['photo_active'];
                        break;
                    case "picture":
                        $img_array = array('.jpg','.jpeg','.gif','.png');
                        $field['value'] = GALLERY_MODULE_IMG_FOLDER_WEB . 's-00.gif';
                        foreach($img_array as $extention){
                            if (file_exists( GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "s-$productId"  . $extention)) {
                                $field['value'] = GALLERY_MODULE_IMG_FOLDER_WEB . "s-$productId"  . $extention;
                                break;
                            }
                        }
                        break;
                    default:
                        throw new ExceptionExt("Incorrect field type: {$field['name']}");
                        break;
                }
            }
        }

        $this->MySQL->freeResult();

        return $result;
    }

    /**
     * Save the uploaded big picture
     *
     * @param   nothing
     * @throws
     * @return  nothing
     */
    public function saveUploadedBigFile(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($_FILES["upload"])){
            throw new ExceptionExt("No file transmitted");
        }

        //1 - if quick load(picture should be saved immediately), 0 - full add/edit(picture should be saved in add/edit procedure)
        $qload = $this->InputFilter->getParameter("qload");

        switch($_FILES["upload"]["type"]){
            case "image/gif":
                $extention = "gif";
                break;
            case "image/jpg":
                $extention = "jpg";
                break;
            case "image/jpeg":
                $extention = "jpeg";
                break;
            case "image/bmp":
                $extention = "bmp";
                break;
            case "image/png":
                $extention = "png";
                break;
            case "image/pjpeg":     //IE 8 bugfix

                if (preg_match('/\.(\w+)$/i', $_FILES["upload"]["name"], $match)){
                    switch ($match[1]){
                        case "gif":
                            $extention = "gif";
                            break;
                        case "jpg":
                            $extention = "jpg";
                            break;
                        case "jpeg":
                            $extention = "jpeg";
                            break;
                        case "png":
                            $extention = "png";
                            break;
                        default:
                            throw new ExceptionExt("Incorrect file type in IE8/pjpeg ({$_FILES["upload"]["name"]})");
                    }
                } else {
                    throw new ExceptionExt("Incorrect file type in IE8/pjpeg ({$_FILES["upload"]["name"]})");
                }
                break;
            default:
                throw new ExceptionExt("Incorrect file type ({$_FILES["upload"]["type"]})");
        }

        if (false === is_uploaded_file($_FILES['upload']['tmp_name'])){
            throw new ExceptionExt("File is not uploaded through HTTP POST");
        }

        if (!is_dir(GALLERY_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM)){
            mkdir(GALLERY_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM);
        }

        $tmpFileName    = tempnam(GALLERY_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM, "cat");
        unlink($tmpFileName);
        $tmpFileName    = $tmpFileName . ".$extention";
        $tmpUrlFileName = GALLERY_MODULE_IMG_FOLDER_TEMP_WEB . basename($tmpFileName);

        $newBigFileName     = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "b-{$this->catId}.jpg";
        $newSmallFileName   = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "s-{$this->catId}.jpg";

        $urlBigFile         = GALLERY_MODULE_IMG_FOLDER_WEB . "b-{$this->catId}.jpg";
        $urlSmallFile       = GALLERY_MODULE_IMG_FOLDER_WEB . "s-{$this->catId}.jpg";

        if (false === move_uploaded_file($_FILES['upload']['tmp_name'], $tmpFileName)) {
            throw new ExceptionExt("Can not move uploaded file");
        }

        if ($qload == 1){   //quick load(picture should be saved immediately)

            $removeFilesMask    = GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM . "[bs]-{$this->catId}.*";
            $removeFilesList    = glob($removeFilesMask, GLOB_NOSORT);

            if (!empty($removeFilesList)){

                foreach ( $removeFilesList as $filename){

                    if (false === unlink($filename)){
                        throw new ExceptionExt("Can not remove picture old file ($filename)");
                    }
                }
            }

            if (false === ImageScale::scalePicture($tmpFileName, $newBigFileName, $extention, GALLERY_MODULE_BIG_PICTURE_SIZE, GALLERY_MODULE_NOT_USE_CROP_PICTURE, GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES) ){
                throw new ExceptionExt("Can not decrease picture size for big copy");
            }

            if (false === ImageScale::scalePicture($tmpFileName, $newSmallFileName, $extention, GALLERY_MODULE_SMALL_PICTURE_SIZE, GALLERY_MODULE_NOT_USE_CROP_PICTURE, GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES) ){
                throw new ExceptionExt("Can not decrease picture size for small copy");
            }

            if (false === unlink($tmpFileName)){
                throw new ExceptionExt("Can not remove picture temp file ($tmpFileName)");
            }

            if ($this->isSingleDisplay()){

                return $urlBigFile;

            } else {

                return $urlSmallFile;

            }

        } else {

            return $tmpUrlFileName;

        }
    }

    /**
     * Add new product for admin users
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function adminAddProduct(){

        if (!$this->isUseAdmin()){
            //$this->setResponseStatus('failed');
            return true;
        }

        $name       = (string) $this->InputFilter->getParameter("name");
        $desc       = (string) $this->InputFilter->getParameter("description");
        $article    = (string) $this->InputFilter->getParameter("article");
        $active     = (integer)$this->InputFilter->getParameter("active");

        $this->MySQL->query("INSERT INTO gal_photos (`article`, `exist`, `active`) values('$article', $exists, $active)");

        if($this->MySQL->affectedRows()){

            //$this->setResponseStatus('ok');

            return true;

        } else {

            //$this->setResponseStatus('failed');

            return true;

        }
    }

    /**
     * Remove product
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function adminRemoveProduct(){

        if (!$this->isUseAdmin()){
            //$this->setResponseStatus('failed');
            return true;
        }

        $productId  = (integer)$this->InputFilter->getParameter("productId");

        $this->MySQL->query("DELETE FROM gal_photos WHERE `id` = $productId");

        if($this->MySQL->affectedRows()){

            //$this->setResponseStatus('ok');

            return true;

        } else {

            //$this->setResponseStatus('failed');

            return true;

        }
    }


	/**
	 * Return path and size image
	 * @param   id row in table
	 * @throws  nothing
	 * @return  image path and size
	 * @todo    @piggy maybe remove param
	 */

	public function getImagePath($id){

		$imgFolder = GALLERY_MODULE_IMG_FOLDER_WEB;
		$inOne     = GALLERY_IMG_IN_ONE_FOLDER;

		$imgArray  = array('.jpg','.jpeg','.gif','.png');
		$smallImg  = $imgFolder . '00-s.gif';
		$bigImg    = $imgFolder . '00-b.gif';
		$img       = array();

		if ($inOne) {
			foreach($imgArray as $k => $v) {
				$tempSmall = $imgFolder . $id . '-s' . $v;
				$tempBig   = $imgFolder . $id . '-b' . $v;
				if ( file_exists($tempSmall) ) {
					$smallImg = $tempSmall;
					if ( file_exists($tempBig) ) {
						$bigImg = $tempBig;
					}
					break;
				}
			}

			$img['small']      = $smallImg;
			$img['small_size'] = getimagesize($smallImg);
			$img['big']        = $bigImg;
			$img['big_size']   = getimagesize($bigImg);
		} else {
			//$mask      = glob($imgFolder . $id .'/'."[0-9]*-[bs].*", GLOB_NOSORT);
			$smallMask = glob($imgFolder . $id .'/'."[0-9]*-[s].*", GLOB_NOSORT);
			$bigMask   = glob($imgFolder . $id .'/'."[0-9]*-[b].*", GLOB_NOSORT);

			$img['small']      = $smallMask[0];
			$img['small_size'] = getimagesize($smallMask[0]);
			$img['big']        = $bigMask[0];
			$img['big_size']   = getimagesize($bigMask[0]);
			$count             = 0;

			natsort($smallMask);
			foreach($smallMask as $k => $v) {
				$smallImg  = $v;
				$smallSize = getimagesize($v);
				$tempBig   = $bigMask[$k];
				if ( file_exists($tempBig) ) {
					$bigImg  = $tempBig;
					$bigSize = getimagesize($tempBig);
				}
				
				$img['all'][$count] = array(
											'small'      => $smallImg,
											'small_size' => $smallSize,
											'big'        => $bigImg,
											'big_size'   => $bigSize
											);
				$count++;
			}
			foreach($imgArray as $k => $v) {
				$tempSmall = $imgFolder . $id . '-s' . $v;
				$tempBig   = $imgFolder . $id . '-b' . $v;
				if ( file_exists($tempSmall) ) {
					$img = $tempSmall;
					if ( file_exists($tempBig) ) {
						$b_img = $tempBig;
					}
					break;
				}
			}
		}
		return $img;
	}
}
?>
