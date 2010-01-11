<?php
/**
 * Description of ModuleCatalogue
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleCatalogue extends ModuleGeneral {

    const   wideAllMenu = 0;

    private static $menuSelectedLevel = array();

    protected $ItemsList            = array();
    protected $cacheIds             = array();
    protected $cacheCpu             = array();
    protected $cacheParent          = array();

    protected $moduleParams         = array();
    protected $nestiModuleParams    = array();

    protected $languageId           = 0;
    protected $modifiedId           = 0;

    protected $typeId               = 0;
    protected $productId            = 0;
    protected $brandsList           = array();

    /**
     * Setup module parameters
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function setModuleParameters(){

        $this->languageId   = $this->SiteStructure->getLanguageId();
        $this->moduleName   = "catalogue";

        $this->setImageTypes();

        if (!$this->isMenuCatalogueDislpay() && !$this->isLatestProductsDisplay() && !$this->isRandomProductsDisplay()){
            return true;
        }

        $this->MySQL->query("SELECT
								cat_types.id AS id,
								cat_types_languages.name AS name,
								cat_types.name_cpu AS name_cpu,
								cat_types.parent_id AS parent_id,
								cat_types_languages.description AS description
							FROM
								cat_types, cat_types_languages
							WHERE
                                cat_types_languages.type_id = cat_types.id AND cat_types_languages.language_id = {$this->languageId} AND
								cat_types.active = 1");

        while ($row = $this->MySQL->fetchArray()){

            $newObject = new ModuleCatalogueItem($row['id'], $row['name'], $row['name_cpu'], $row['parent_id'], $row['description']);

            $this->ItemsList[]                      = $newObject;
            $this->cacheIds[$row['id']]             = $newObject;
            $this->cacheCpu[$row['name_cpu']]       = $newObject;
            $this->cacheParent[$row['parent_id']][] = $newObject;

        }

        $params = array();
        $params = $this->SiteStructure->getModuleParameters();

        if (CATALOGUE_USE_PRODUCTS_NESTING_MODE){
            if (count($params) == 0){
                foreach($this->cacheIds as $item){
                    $this->nestiModuleParams[] = $item->id;
                }
            }
        } //nestiModuleParams

        if (count($params) == 0){
            return true;
        }

        $lastParam = $params[count($params)-1];

        if (isset($lastParam) && is_numeric($lastParam)){

            array_pop($params);
            $this->productId = $lastParam;

        }

        foreach($params as $param){

            if (isset($this->cacheCpu[$param])){
                $this->moduleParams[] = $param;
            }

        }

        $countParams = count($this->moduleParams);
        if ($countParams){
            $this->typeId = $this->cacheCpu[$this->moduleParams[$countParams-1]]->id;
        }

        if (CATALOGUE_USE_PRODUCTS_NESTING_MODE){

            $this->nestiModuleParams[] = $this->typeId;

            foreach($this->cacheIds as $item){

                $parentId    = $item->parent;

                while ($parentId != 0){

                    if ($this->typeId == $parentId){
                        $this->nestiModuleParams[] = $item->id;
                    }

                    $parentId    = $this->cacheIds[$parentId]->parent;

                }
            }
        }

        return true;

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

        $lastType = '';
        if (!empty($this->typeId)){
            $lastType = $this->cacheIds[$this->typeId]->cpu;
        }

        $result = array();

        foreach ($this->cacheParent[$parent] as $item){

                $active = 0;
                $onActive = 0;

                if (CATALOGUE_USE_EXPAND_MENU_MODE || $this->isUsedInvisible()){

                    //if current level selected via url
                    if (isset($params[$deep])){

                        if ($params[$deep] == $item->cpu){

                            $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');

                            if ($this->typeId == $item->id){
                                $active = 1;
                            } else {
                                $onActive = 1;
                            }
                        } else {
                            $sub = '';
                        }
                    } else {

                        //if item have childs
                        if (isset($this->cacheParent[$item->id])){

                            //if child already selected
                            if (isset($this->menuSelectedLevel[$deep])){
                                $sub = '';
                            } else {
                                $onActive = 1;
                                $params[$deep] = $item->cpu;
                                $this->moduleParams[$deep] = $item->cpu;
                                $this->typeId = $item->id;
                                $this->menuSelectedLevel[$deep] = $item->id;
                                $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');
                            }
                        } else {
                            $active = 1;
                            $params[$deep] = $item->cpu;
                            $this->moduleParams[$deep] = $item->cpu;
                            $this->typeId = $item->id;
                            $sub = '';
                        }
                    }
                } else { //CATALOGUE_USE_EXPAND_MENU_MODE

                    if (isset($params[$deep]) && $params[$deep] == $item->cpu){
                        $onActive = 1;
                    }

                    if (!empty($lastType) && $lastType == $item->cpu){
                        $onActive = 0;
                        $active   = 1;
                    }

                    if (self::wideAllMenu){
                        $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');
                    } else if ($onActive == 1 || $active == 1){
                        $sub = $this->menu($params, $deep + 1, $item->id, $url.$item->cpu.'/');
                    } else {
                        $sub = '';
                    }
                }

                $result[] = array('Id'          => $item->id,
                                  'Url'         => $url.$item->cpu.'/',
                                  'Name'        => $item->name,
                                  'Description' => $item->description,
                                  'Deep'        => $deep+1,
                                  'Selected'    => $active,
                                  'OnActive'    => $onActive,
                                  'Sub'         => $sub
                );
        }
        return $result;
    }

    /**
     * Save Brands list
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    protected function getBrandsList() {

        $this->MySQL->query("select `id`, `name` from cat_brand");

        while ($row = $this->MySQL->fetchArray()){

            $this->brandsList[$row['id']] = $row['name'];

        }
    }

    /**
     * Return main select statement for execute
     *
     * @param   $latestList 1 if sql should be generated for latest list, 0 if not latest list
     * @param   $randomList 1 if sql should be generated for random list, 0 if not latest list
     * @param   $randomNumber random number for random list limit
     * @param   $lastModifiedData 1 if sql should be generated for last modified throught admin function
     * @throws  no throws
     * @return  main select statement for execute
     */
    protected function getMainSelectStatement($latestList = 0, $randomList = 0, $randomNumber = 0, $lastModifiedData = 0) {

        $active = '';
        $where  = '';
        $limit  = '';
        $order  = '';

        if ($this->isUseAdmin() && $latestList == 0 && $randomList == 0){
            $active = '';
        } else {
            $active = 'and cat_products.active = 1 ';
        }

        if (CATALOGUE_USE_PRODUCTS_NESTING_MODE){
            $str = implode(',', $this->nestiModuleParams);
            $where = "and cat_link_products_types.type_id in ($str)";
            $limit = '';
        } else {
            if (!empty($this->typeId)){
                $limit = '';
                $where = "and cat_link_products_types.type_id = '{$this->typeId}'";
            } else {
                $limit = 'LIMIT '.CATALOGUE_MODULE_PRODUCTS_LIST_LIMIT;
            }
        }

        if ($this->productId != 0){
            $where .= "and cat_products.id = {$this->productId}";
        }

        if ($latestList == 1){
            $where = '';
            $order = 'ORDER BY cat_products.id DESC';
            $limit = 'LIMIT ' . CATALOGUE_MODULE_LATEST_PRODUCTS_LIMIT;
        }

        if ($randomList == 1){
            $where = '';
            $order = '';
            $limit = "LIMIT $randomNumber, 1";
        }

        if ($lastModifiedData == 1){
            $where = "and cat_products.id = {$this->modifiedId}";
            $order = '';
            $limit = "LIMIT 1";
        }

        $select = <<<SQL
            SELECT
                cat_products.id AS product_id,
                cat_products_languages.name AS product_name,
                cat_products_languages.description AS product_description,
                cat_products_languages.description2 AS product_description2,
                cat_products.article AS product_article,
                cat_products.exist AS product_exist,
                cat_products.active AS product_active,
                cat_products.price AS product_price,

                cat_brand.id AS brand_id,
                cat_brand.name AS brand_name,
                cat_brand.description AS brand_description,

                cat_types.id AS type_id,
                cat_types.parent_id AS type_parent_id
            FROM
                cat_products, cat_products_languages, cat_brand, cat_link_products_types, cat_types
            WHERE
                cat_products.id = cat_products_languages.product_id and cat_products_languages.language_id = {$this->languageId}
                and cat_brand.id = cat_products.brand_id
                and cat_products.id = cat_link_products_types.product_id
                and cat_types.id = cat_link_products_types.type_id
                $active $where $order
            $limit
SQL;

        return $select;
    }

    /**
     * Return true if Catalogue module used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if catalogue module used
     */
    public function isUsed(){

        if ($this->AttributeOperations->getModuleName() == 'catalogue' || $this->AttributeOperations->getModuleName() == 'invcatalogue'){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if Catalogue module used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if catalogue module used
     */
    public function isUsedInvisible(){

        if ($this->AttributeOperations->getModuleName() == 'invcatalogue'){
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

        if ($this->productId != 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if latest products list display
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if latest products list display
     */
    public function isLatestProductsDisplay() {

        return CATALOGUE_MODULE_DISPLAY_LATEST_PRODUCTS;

    }

    /**
     * Return true if random products list display
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if random products list display
     */
    public function isRandomProductsDisplay() {

        return CATALOGUE_MODULE_DISPLAY_RANDOM_PRODUCTS;

    }

    /**
     * Return true if any news or years selected, so navigate inside module
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if any news or years selected, so navigate inside module
     */
    public function isModuleInside() {
        if (count($this->moduleParams)){
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Return true if any news or years selected, so navigate inside module
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if any news or years selected, so navigate inside module
     */
    public function isDisplayAddButton() {

        if (CATALOGUE_USE_PRODUCTS_NESTING_MODE){

            if (isset($this->cacheParent[$this->typeId])){
                return 0;
            }
        }

        if ($this->isModuleInside()){
            return 1;
        } else {
            return 0;
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

            return CATALOGUE_MODULE_SINGLE_TEMPLATE_NAME;

        } else {

            if ($this->isUsedInvisible()){

                return CATALOGUE_MODULE_INVISIBLE_TEMPLATE_NAME;

            } else {

                return CATALOGUE_MODULE_LIST_TEMPLATE_NAME;

            }
        }
    }

    /**
     * Return menu template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  menu template name
     */
    public function getMenuTemplateName(){

        return CATALOGUE_MODULE_MENU_TEMPLATE_NAME;

    }

    /**
     * Return template name for latest products
     *
     * @param   nothing
     * @throws  no throws
     * @return  template name for latest products
     */
    public function getLatestProductsTemplateName() {

        return CATALOGUE_MODULE_LATEST_PRODUCTS_TEMPLATE_NAME;

    }

    /**
     * Return template name for random products
     *
     * @param   nothing
     * @throws  no throws
     * @return  template name for random products
     */
    public function getRandomProductsTemplateName() {

        return CATALOGUE_MODULE_RANDOM_PRODUCTS_TEMPLATE_NAME;

    }

    /**
     * Returns product url by type id and product id
     *
     * @param   $typeId type id
     * @param   $productId product id
     * @throws  no throws
     * @return  product url by product id
     */
    public function getSearchBackwardUrl($typeId, $productId) {

        $relativeUrl = '';

        $parentId    = $this->cacheIds[$typeId]->parent;

        while ($parentId != 0){

            $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
            $parentId    = $this->cacheIds[$parentId]->parent;

        }

        return CATALOGUE_RELATIVE_URL . '/' . $relativeUrl . $this->cacheIds[$typeId]->cpu . '/' . $productId;

    }

    /**
     * Returns product picture by product id
     *
     * @param   $productId product id
     * @throws  no throws
     * @return  product picture by product id
     */
    public function getSearchBackwardPictureUrl($productId) {

        $fileName = CATALOGUE_MODULE_IMG_FOLDER_WEB . "$productId-s.jpg";

        if (file_exists($fileName)){

            return $fileName;

        } else {

            return '';

        }
    }

    /**
     * Return default currency name
     *
     * @param   nothing
     * @throws  no throws
     * @return  default currency name
     */
    public function getCurrency() {

        return CATALOGUE_MODULE_DEFAULT_CURRENCY_NAME;

    }

    /**
     * Return currency line
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  currency line
     */
    public function getCurrencyLine() {

        $result = '';

        $this->MySQL->query("select `currency`, `rate` from cat_price");

        while ($row = $this->MySQL->fetchArray()){

            if (!empty($result)){
                $result .= ", ";
            }

            $result .= sprintf("%s: %s", $row['currency'], $row['rate']);

        }

        $this->MySQL->freeResult();

        return $result;
    }

    /**
     * Return list of Catalogue items
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  list of Catalogue
     */
    public function getList(){

        if ($this->isSingleDisplay()){
            return "";
        }

        $result		= array();
        $where		= '';
        $url        = '';
        $relativeUrl= '';
        $mainUrl    = $this->SiteStructure->getSiteTranslatedSiteUrl() . CATALOGUE_RELATIVE_URL . "/";

        $this->MySQL->query($this->getMainSelectStatement());

        while ($row = $this->MySQL->fetchArray()){

            $parentId    = $row['type_parent_id'];
            $relativeUrl = '';

            while ($parentId != 0){

                $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
                $parentId    = $this->cacheIds[$parentId]->parent;

            }

            $url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['product_id'] . '/';
			$img = $this->getImagePath($row['product_id']);

            $result[] = array(  'Id'                =>  $row['product_id'],
                                'Url'               =>  $url,
                                'Active'            =>  $row['product_active'],
                                'Exist'             =>  $row['product_exist'],
                                'Name'              =>  $row['product_name'],
                                'Description'       =>  $row['product_description'],
                                'Description2'      =>  $row['product_description2'],
                                'Article'           =>  $row['product_article'],
                                'BrandName'         =>  $row['brand_name'],
                                'BrandDescription'  =>  $row['brand_description'],
                                'Price'             =>  $row['product_price'],
                                'Currency'          =>  $this->getCurrency(),
                                'Img'               =>  $img,
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return full info about single Catalogue item
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  one full Catalogue item
     */
    public function getSingle(){

        $result = array();

        if (!$this->isSingleDisplay()){
            return "";
        }

        if ($this->isUseAdmin()){
            $active = '';
        } else {
            $active = ' AND cat_products.active = 1 ';
        }

        $this->MySQL->query($this->getMainSelectStatement());

        while ($row = $this->MySQL->fetchArray()){

			$img = $this->getImagePath($row['product_id']);

            $result = array(
                            'Id'                =>  $row['product_id'],
                            'Active'			=>	$row['product_active'],
                            'Exist'				=>	$row['product_exist'],
                            'Name'				=>	$row['product_name'],
                            'Description'		=>	$row['product_description'],
                            'Description2'  	=>	$row['product_description2'],
                            'Article'			=>	$row['product_article'],
                            'BrandName'         =>	$row['brand_name'],
                            'BrandDescription'	=>	$row['brand_description'],
                            'Price'     		=>	$row['product_price'],
                            'Currency'          =>  $this->getCurrency(),
                            'Img'				=>	$img,
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
    public function getLatestProducts(){

        $result		= array();

        if (!$this->isLatestProductsDisplay()){
            return $result;
        }

        $useLatestList  = 1;
        $url            = '';
        $relativeUrl    = '';
        $mainUrl        = $this->SiteStructure->getSiteTranslatedSiteUrl() . CATALOGUE_RELATIVE_URL . "/";

        $this->MySQL->query($this->getMainSelectStatement($useLatestList));

        while ($row = $this->MySQL->fetchArray()){

            $parentId    = $row['type_parent_id'];
            $relativeUrl = '';

            while ($parentId != 0){

                $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
                $parentId    = $this->cacheIds[$parentId]->parent;

            }

            $url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['product_id'] . '/';
			$img = $this->getImagePath($row['product_id']);

            $result[] = array(	'Id'                =>  $row['product_id'],
                                'Url'               =>  $url,
                                'Active'            =>  $row['product_active'],
                                'Exist'             =>  $row['product_exist'],
                                'Name'              =>	$row['product_name'],
                                'Description'       =>  $row['product_description'],
                                'Description2'      =>  $row['product_description2'],
                                'Article'           =>  $row['product_article'],
                                'BrandName'         =>  $row['brand_name'],
                                'BrandDescription'  =>  $row['brand_description'],
                                'Price'             =>  $row['product_price'],
                                'Currency'          =>  $this->getCurrency(),
                                'Img'               =>  $img
            );
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return list of random Catalogue items
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  list of Catalogue
     */
    public function getRandomProducts(){

        $result		= array();

        if (!$this->isRandomProductsDisplay()){
            return $result;
        }

        $limit          = 'LIMIT ' . CATALOGUE_MODULE_RANDOM_PRODUCTS_LIMIT;
        $count          = 0;
        $query          = array();
        $randomNumbers  = array();
        $randomValue    = 0;
        $url            = '';
        $relativeUrl    = '';
        $mainUrl        = $this->SiteStructure->getSiteTranslatedSiteUrl() . CATALOGUE_RELATIVE_URL . "/";

        $this->MySQL->query("SELECT count(*) as count FROM cat_products Inner Join cat_link_products_types ON cat_products.id = cat_link_products_types.product_id");
        $row = $this->MySQL->fetchArray();
        if ($row !== false){
            $count  = $row['count'] - 1;
        }

        for ($i=0;$i<$count;$i++){

            $randomValue = rand(0, $count);
            while (in_array($randomValue, $randomNumbers)){
                $randomValue = rand(0, $count);
            }

            $randomNumbers[] = $randomValue;

            $useLatestList = 0;
            $useRandomList = 1;

            $query[] = '(' . $this->getMainSelectStatement($useLatestList, $useRandomList, $randomValue) . ')';

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

            $url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['product_id'] . '/';
			$img = $this->getImagePath($row['product_id']);

            $result[] = array(	'Id'                =>  $row['product_id'],
                                'Url'               =>  $url,
                                'Active'            =>  $row['product_active'],
                                'Exist'             =>  $row['product_exist'],
                                'Name'              =>	$row['product_name'],
                                'Description'       =>  $row['product_description'],
                                'Description2'      =>  $row['product_description2'],
                                'Article'           =>  $row['product_article'],
                                'BrandName'         =>  $row['brand_name'],
                                'BrandDescription'  =>  $row['brand_description'],
                                'Price'             =>  $row['product_price'],
                                'Currency'          =>  $this->getCurrency(),
                                'Img'               =>  $img
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
    public function isMenuCatalogueDislpay(){

        if (CATALOGUE_DISPLAY_MENU){
			if (CATALOGUE_DISPLAY_MENU_ALWAYS){
				return true;
			} else {
				if ($this->isUsed()){
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
     * Return menu for Catalogue
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  menu for Catalogue
     */
    public function getCatalogueMenu(){

        if (!$this->isMenuCatalogueDislpay()){
            return "";
        }

        $deep = 0;
        $parent = 0;
        $result = array();
		$url = $this->SiteStructure->getSiteTranslatedSiteUrl() .CATALOGUE_RELATIVE_URL.'/';

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

        if (CATALOGUE_MODULE_USE_ADMIN && $this->Session->isAuthorized()){
            return 1;
        } else {
            return 0;
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
     * Add new product from popup window
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addProductFull(){

        if (CATALOGUE_USE_EXPAND_MENU_MODE){
            $this->getCatalogueMenu(); //need for shift menu to latest level
        }

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

        if (CATALOGUE_USE_PRODUCTS_NESTING_MODE){

            if (isset($this->cacheParent[$this->typeId])){
                throw new ExceptionExt("New data can not be added on cat type with childs on nesting mode");
            }
        }

        $name       = (string)  isset( $newData["ProductName"]          ) ? $newData["ProductName"]         : '';
        $desc       = (string)  isset( $newData["ProductDescription"]   ) ? $newData["ProductDescription"]  : '';
        $article    = (string)  isset( $newData["ProductArticle"]       ) ? $newData["ProductArticle"]      : '';
        $price      = (string)  isset( $newData["ProductPrice"]         ) ? $newData["ProductPrice"]        : '';

        $brandId    = (integer) isset( $newData["BrandName"]            ) ? $newData["BrandName"]           : 0;
        $active     = (integer) isset( $newData["Active"]               ) ? $newData["Active"]              : 0;
        $exist      = (integer) isset( $newData["ProductExist"]         ) ? $newData["ProductExist"]        : 0;

        $this->MySQL->query("INSERT INTO cat_products (`article`, `exist`, `brand_id`, `active`, `price`) VALUES ('$article', $exist, $brandId, $active, '$price')");

        $productId = $this->MySQL->insertedId();

        $this->MySQL->query("INSERT INTO cat_products_languages (`product_id`, `language_id`, `name`, `description`) VALUES ($productId, {$this->languageId}, '$name', '$desc')");

        $this->MySQL->query("INSERT INTO cat_link_products_types (`product_id`, `type_id`) VALUES ($productId, {$this->typeId})");

        $this->modifiedId = $productId;

        $tmpUrlFileName = $this->InputFilter->getParameter("file");

        if (!empty($tmpUrlFileName)){

            $tmpFileName = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            $this->saveTemporaryUploadedFile($tmpFileName);

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

        $name       = (string)  isset( $newData["ProductName"]          ) ? $newData["ProductName"]         : '';
        $desc       = (string)  isset( $newData["ProductDescription"]   ) ? $newData["ProductDescription"]  : '';
        $article    = (string)  isset( $newData["ProductArticle"]       ) ? $newData["ProductArticle"]      : '';
        $price      = (string)  isset( $newData["ProductPrice"]         ) ? $newData["ProductPrice"]        : '';

        $brandId    = (integer) isset( $newData["BrandName"]            ) ? $newData["BrandName"]           : 0;
        $active     = (integer) isset( $newData["Active"]               ) ? $newData["Active"]              : 0;
        $exist      = (integer) isset( $newData["ProductExist"]         ) ? $newData["ProductExist"]        : 0;

        $this->MySQL->query("UPDATE cat_products SET `article` = '$article', `exist` = $exist, `brand_id` = $brandId, `active` = $active, `price` = '$price' WHERE `id` = $productId");

        $this->MySQL->query("UPDATE cat_products_languages  SET `name` = '$name', `description` = '$desc' WHERE `product_id` = $productId and `language_id` = {$this->languageId}");

        $this->modifiedId = $productId;

        $tmpUrlFileName = $this->InputFilter->getParameter("file");

        if (!empty($tmpUrlFileName)){

            $tmpFileName = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            $this->saveTemporaryUploadedFile($tmpFileName);

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

        $this->MySQL->query("DELETE FROM cat_products WHERE `id` IN ($inStatement)");

        $this->MySQL->query("DELETE FROM cat_products_languages WHERE `product_id` IN ($inStatement)");

        $this->MySQL->query("DELETE FROM cat_link_products_types WHERE `product_id` IN ($inStatement)");

        if($this->MySQL->affectedRows() === -1){

            throw new ExceptionExt("Elements not deleted");

        }

        $fileFilder = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;

        foreach ($ids as $id){
            if (!count($this->imageTypes)){
                throw new ExceptionExt("Image types not set");
            }

            foreach($this->imageTypes as $name => $options){

                $fileName = sprintf($options['MaskName'], $id);
                $fileNameFull = $fileFilder . $fileName;

                if(file_exists($fileNameFull)){
                    unlink($fileNameFull);
                }
            }

            $additionalFolder = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM . "$id/";

            if (is_dir($additionalFolder)){

                if (!count($this->additionalImageTypes)){
                    throw new ExceptionExt("Additional image types not set");
                }

                foreach($this->additionalImageTypes as $name => $options){

                    $fileName = sprintf($options['MaskName'], '*');

                    $additionalMask = glob($additionalFolder . "*.*", GLOB_NOSORT);

                    if (count($additionalMask)){
                        foreach($additionalMask as $file){
                            if (false === unlink($file)){
                                throw new ExceptionExt("Can not remove file $file");
                            }
                        }
                    }
                }

                if (false === rmdir($additionalFolder)){
                    throw new ExceptionExt("Can not remove empty dir $additionalFolder");
                }
            }
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

        $result		= array();
        $productId  = $this->modifiedId;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($productId)){
            throw new ExceptionExt("Id not set");
        }

        $url        = '';
        $relativeUrl= '';
        $mainUrl    = $this->SiteStructure->getSiteTranslatedSiteUrl() . CATALOGUE_RELATIVE_URL . "/";

        $useLatestList = 0;
        $useRandomList = 0;
        $randomValue   = 0;
        $useLastModifiedData = 1;

        $this->MySQL->query($this->getMainSelectStatement($useLatestList, $useRandomList, $randomValue, $useLastModifiedData));

        while ($row = $this->MySQL->fetchArray()){

            $parentId    = $row['type_parent_id'];
            $relativeUrl = '';

            while ($parentId != 0){

                $relativeUrl = $this->cacheIds[$parentId]->cpu . '/' . $relativeUrl;
                $parentId    = $this->cacheIds[$parentId]->parent;

            }

            $url = $mainUrl . $relativeUrl . $this->cacheIds[$row['type_id']]->cpu . '/' . $row['product_id'] . '/';
			$img = $this->getImagePath($row['product_id']);

            $result[] = array(  'Id'				=>	$row['product_id'],
                                'Url'				=>	$url,
                                'Active'			=>	$row['product_active'],
                                'Exist'				=>	$row['product_exist'],
                                'Name'				=>	$row['product_name'],
                                'Description'		=>	$row['product_description'],
                                'Description2'		=>	$row['product_description2'],
                                'Article'			=>	$row['product_article'],
                                'BrandName'         =>	$row['brand_name'],
                                'BrandDescription'	=>	$row['brand_description'],
                                'Price'             =>	$row['product_price'],
                                'Currency'          =>  $this->getCurrency(),
                                'Img'				=>	$img,
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
            case "ProductName":
                $select     = "name";
                $table      = "cat_products_languages";
                $type       = "text";
                break;
            case "ProductDescription":
                $select     = "description";
                $table      = "cat_products_languages";
                $type       = "text";
                break;
            case "ProductArticle":
                $select     = "article";
                $table      = "cat_products";
                $type       = "text";
                break;
            case "ProductPrice":
                $select     = "price";
                $table      = "cat_products";
                $type       = "text";
                break;
            case "ProductExist":
                $select     = "exist";
                $table      = "cat_products";
                $type       = "text";
                break;
            case "BrandName":
                $select     = "brand_id";
                $table      = "cat_products";
                $type       = "text";
                break;
            case "BrandDescription":
                $select     = "brand_id";
                $table      = "cat_products";
                $type       = "text";
                break;
            default:
                throw new ExceptionExt("Incorrect field type");
                break;
        }

        switch ($table){
            case "cat_products":

                $query = "UPDATE cat_products SET `$select` = '$elementValue' WHERE `id` = $id limit 1";

                break;
            case "cat_products_languages":

                $query = "UPDATE cat_products_languages SET `$select`  = '$elementValue' WHERE `product_id` = $id and `language_id` = {$this->languageId} limit 1";

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

        $result = array('Value'=>'','Type'=>'','Id'=>'','Field'=>'');
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
            case "ProductName":
                $select     = "name";
                $table      = "cat_products_languages";
                $type       = "textline";
                break;
            case "ProductDescription":
                $select     = "description";
                $table      = "cat_products_languages";
                $type       = "text";
                break;
            case "ProductArticle":
                $select     = "article";
                $table      = "cat_products";
                $type       = "textline";
                break;
            case "ProductPrice":
                $select     = "price";
                $table      = "cat_products";
                $type       = "textline";
                break;
            case "ProductExist":
                $select     = "exist";
                $table      = "cat_products";
                $type       = "radio";
                break;
            case "BrandName":
                $select     = "name";
                $table      = "cat_brand";
                $type       = "select";
                break;
            case "BrandDescription":
                $select     = "description";
                $table      = "cat_brand";
                $type       = "select";
                break;
            default:
                throw new ExceptionExt("Incorrect field type");
                break;
        }

        switch ($table){
            case "cat_products":
                $query = "select `id`, `$select` from cat_products where `id` = $id";
                break;
            case "cat_products_languages":
                $query = "select `product_id` as `id`, `$select` from cat_products_languages where `product_id` = $id and `language_id` = {$this->languageId}";
                break;
            case "cat_brand":
                $query = "select b.`id`, b.`$select` from cat_brand b, cat_products p where p.`brand_id` = b.`id` and p.`id` = $id";
                break;
        }

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row[$select] = htmlspecialchars_decode($row[$select],ENT_QUOTES);

            $result = array('Value' => $row[$select],
                            'Type'  => $type,
                            'Id'    => $row['id'],
                            'Field' => $elementName
            );

            if ($elementName == 'BrandName' || $elementName == 'BrandDescription'){
                $this->getBrandsList();
                foreach($this->brandsList as $id => $name){
                    $result['Options'][$id] = $name;
                }
            }
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

        $this->MySQL->query("UPDATE cat_products SET `active` = NOT `active` WHERE `id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Activity not inversed");

        }

        return true;
    }

    /**
     * Add new product from popup window
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addMenuElementFull(){

        /*if (CATALOGUE_USE_EXPAND_MENU_MODE){
            $this->getCatalogueMenu(); //need for shift menu to latest level
        }*/

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        /*if (empty($this->typeId)){
            throw new ExceptionExt("New data can not be added on top catalogue menu");
        }

        if (CATALOGUE_USE_PRODUCTS_NESTING_MODE){

            if (isset($this->cacheParent[$this->typeId])){
                throw new ExceptionExt("New data can not be added on cat type with childs on nesting mode");
            }
        }*/

        $name       = (string)  isset( $newData["Name"]         ) ? $newData["Name"]        : '';
        $desc       = (string)  isset( $newData["Description"]  ) ? $newData["Description"] : '';
        $cpu        = (string)  isset( $newData["Cpu"]          ) ? $newData["Cpu"]         : '';

        $active     = (integer) isset( $newData["Active"]       ) ? $newData["Active"]      : 0;
        $parentId   = 0;

        $this->MySQL->query("INSERT INTO cat_types (`name_cpu`, `parent_id`, `active`) VALUES ('$cpu', $parentId, $active)");

        $typeId = $this->MySQL->insertedId();

        $this->MySQL->query("INSERT INTO cat_types_languages (`type_id`, `language_id`, `name`, `description`) VALUES ($typeId, {$this->languageId}, '$name', '$desc')");

        $this->modifiedId = $typeId;

        /*$tmpUrlFileName = $this->InputFilter->getParameter("file");

        if (!empty($tmpUrlFileName)){

            $tmpFileName = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            $this->saveTemporaryUploadedFile($tmpFileName);

        }*/

        return true;

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

        $result = array( 'Lines' =>  array(), 'Title' => 'Добавление', 'Action' => 'Add' );

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['Lines'] = $ADMIN_WINDOW[$page];

        foreach ($result['Lines'] as &$field){
            $field['Value'] = '';

            if ($field['Type'] == 'file'){
                $field['Img']   = $this->getImagePath(0);
            } else if ($field['Type'] == 'select' AND $field['Name'] == 'BrandName') {
                $this->getBrandsList();
                foreach ($this->brandsList as $brandId => $brandName){
                    $field['Options'][$brandId] = $brandName;
                }
            }
        }

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

        $result = array( 'Lines' =>  array(), 'Title' => 'Редактирование', 'Action' => 'Edit' );

        $productId  = (integer)$this->InputFilter->getParameter("id");

        if (empty($productId)){
            throw new ExceptionExt("Id not set");
        }

        $this->modifiedId = $productId;

        $result['Id'] = $productId;

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['Lines'] = $ADMIN_WINDOW[$page];

        $useLatestList = 0;
        $useRandomList = 0;
        $randomValue   = 0;
        $useLastModifiedData = 1;

        $this->MySQL->query($this->getMainSelectStatement($useLatestList, $useRandomList, $randomValue, $useLastModifiedData));

        while ($row = $this->MySQL->fetchArray()){

            foreach ($result['Lines'] as &$field){

                switch ($field['Name']){
                    case "ProductName":
                        $field['Value'] = $row['product_name'];
                        break;
                    case "ProductDescription":
                        $field['Value'] = $row['product_description'];
                        break;
                    case "ProductArticle":
                        $field['Value'] = $row['product_article'];
                        break;
                    case "ProductPrice":
                        $field['Value'] = $row['product_price'];
                        break;
                    case "ProductExist":
                        $field['Value'] = $row['product_exist'];
                        break;
                    case "BrandName":
                        $field['Value'] = $row['brand_id'];
                        $this->getBrandsList();
                        foreach ($this->brandsList as $brandId => $brandName){
                            $field['Options'][$brandId] = $brandName;
                        }
                        break;
                    case "ProductBrandId":
                        $field['Value'] = $row['brand_id'];
                        break;
                    case "Active":
                        $field['Value'] = $row['product_active'];
                        break;
                    case "Picture":
                        $field['Value'] = '';
                        $field['Img'] = $this->getImagePath($this->modifiedId);
                    case "MultiPicture":
                        $field['Value'] = '';
                        break;
                    default:
                        throw new ExceptionExt("Incorrect field type: {$field['Name']}");
                        break;
                }
            }
        }

        $this->MySQL->freeResult();

        return $result;
    }
}
?>