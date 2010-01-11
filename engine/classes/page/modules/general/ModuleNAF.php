<?php
/**
 * Module NAF (News/Articles/FAQS).
 * This module used for all News-like modules
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleNAF extends ModuleGeneral {

    protected $nafId                = 0;
    protected $modifiedId           = 0;
    protected $selectedYear         = '';
    protected $selectedMonth        = '';
    protected $selectedDay          = '';
    protected $setDateRange         = 0;
    protected $languageId           = 0;

    protected $singleTemplateName   = '';
    protected $listTemplateName     = '';
    protected $displayCalendar      = 0;
    protected $databaseTableName    = '';
    protected $databaseTableLimit   = 0;
    protected $relativeUrl          = '';
    protected $maxTextFields        = 0;
    protected $listDisplayFields    = 0;
    protected $singleDisplayFields  = 0;
    protected $configModuleName     = '';
    protected $splitText            = 0;
    protected $splitLeftTag         = '';
    protected $splitRightTag        = '';

    protected $isUseAdmin                       = 0;
    protected $adminTemplateName                = '';
    protected $adminAjaxFullDataTemplateName    = '';
    protected $adminAjaxLastModifiedTemplateName= '';
    protected $adminAjaxTextTemplateName        = '';

    protected $databaseTableNameLatest          = '';
    protected $databaseTableLimitLatest         = 0;
    protected $relativeUrlLatest                = 0;
    protected $splitTextLatest                  = 0;
    protected $splitLeftTagLatest               = '';
    protected $splitRightTagLatest              = '';

    /**
     * Used for read config data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function getConfigData(){

        global $NAF_CONFIG;

        $this->moduleName = "naf";

        $this->setImageTypes();

        if (!is_array($NAF_CONFIG)){
            return true;
        }

        if (!count($NAF_CONFIG)){
            return true;
        }

        if (NAF_LATEST_ELEMENTS_TYPE != ""){

            foreach($NAF_CONFIG as $config){

                if ($config['MODULE_NAME'] == NAF_LATEST_ELEMENTS_TYPE){

                    $this->databaseTableNameLatest    = $config['MODULE_DATABASE_TABLE_NAME'];
                    $this->databaseTableLimitLatest   = NAF_LATEST_ELEMENTS_LIMIT;
                    $this->relativeUrlLatest          = $config['MODULE_RELATIVE_URL'];
                    $this->splitTextLatest            = NAF_LATEST_ELEMENTS_SPLIT_TEXT;
                    $this->splitLeftTagLatest         = NAF_LATEST_ELEMENTS_SPLIT_LEFT_TAG;
                    $this->splitRightTagLatest        = NAF_LATEST_ELEMENTS_SPLIT_RIGHT_TAG;

                    break;

                }
            }
        }

        $moduleName     = $this->AttributeOperations->getModuleName();

        if (empty($moduleName)){

            $moduleName = $this->InputFilter->getParameter("module");

            if (empty($moduleName)){

                return true;

            }
        }

        foreach($NAF_CONFIG as $config){

            if ($config['MODULE_NAME'] == $moduleName){

                $this->singleTemplateName               = $config['MODULE_SINGLE_TEMPLATE_NAME'];
                $this->listTemplateName                 = $config['MODULE_LIST_TEMPLATE_NAME'];
                $this->displayCalendar                  = $config['MODULE_DISPLAY_CALENDAR'];
                $this->databaseTableName                = $config['MODULE_DATABASE_TABLE_NAME'];
                $this->databaseTableLimit               = $config['MODULE_LIST_ELEMENTS_LIMIT'];
                $this->relativeUrl                      = $config['MODULE_RELATIVE_URL'];
                $this->maxTextFields                    = $config['MODULE_MAX_COUNT_OF_TEXT_FIELDS'];
                $this->listDisplayFields                = $config['MODULE_LIST_DISPLAY_FIELDS'];
                $this->singleDisplayFields              = $config['MODULE_SINGLE_DISPLAY_FIELDS'];
                $this->configModuleName                 = $config['MODULE_NAME'];
                $this->splitText                        = $config['MODULE_SPLIT_TEXT'];
                $this->splitLeftTag                     = $config['MODULE_SPLIT_LEFT_TAG'];
                $this->splitRightTag                    = $config['MODULE_SPLIT_RIGHT_TAG'];

                $this->isUseAdmin                       = $config['MODULE_USE_ADMIN'];

                return true;
            }
        }
    }

    /**
     * Setup module parameters
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function setModuleParameters(){

        $params = array();
        $params = $this->SiteStructure->getModuleParameters();
        $this->languageId = $this->SiteStructure->getLanguageId();

        if (count($params) == 0){
            return true;
        }

        if (isset($params[0]) && is_numeric($params[0])){
            $this->selectedYear = $params[0];
        }

        if (isset($params[1]) && is_numeric($params[1])){
            $this->selectedMonth = $params[1];
        }

        if (isset($params[2]) && is_numeric($params[2])){
            $this->selectedDay = $params[2];
        }

        if (isset($params[3])){
            $nafId = $params[3];
            if (is_numeric($nafId)){
                $this->nafId = $nafId;
            }
        }
    }

    /**
     * Return array of splitted text
     *
     * @param   $text text for split
     * @param   $useLatestList if 1 - use settings for latest news, not for standart Naf page
     * @throws  no throws
     * @return  array of splitted text
     */
    protected function splitText($text, $useLatestList = 0) {

        $split              = 0;
        $splitLeftTag       = '';
        $splitRightTag      = '';
        $splitText1         = '';
        $splitText2         = '';
        $splitText3         = '';
        $startTag           = 0;
        $endTag             = 0;

        if (empty($useLatestList)){
            $split              = $this->splitText;
            $splitLeftTag       = $this->splitLeftTag;
            $splitRightTag      = $this->splitRightTag;
        } else {

            $split              = $this->splitTextLatest;
            $splitLeftTag       = $this->splitLeftTagLatest;
            $splitRightTag      = $this->splitRightTagLatest;
        }

        if ($split){
            $startTag           = strpos($text, $splitLeftTag, 0);
            $endTag             = strpos($text, $splitRightTag, $startTag);

            if ($startTag===false && $endTag===false){
                $splitText1    = $text;
                $splitText2    = '';
                $splitText3    = '';
            } else {
                $splitText1    = substr($text, 0, $startTag);
                $splitText2    = substr($text, $startTag+1,$endTag-$startTag-1);
                $splitText3    = substr($text, $endTag+1);
            }
        }

        return array(   'Start' => $splitText1,
                        'Link'  => $splitText2,
                        'End'   => $splitText3
                    );
    }

    /**
     * Return true if NAF module used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if NAF module used
     */
    public function isUsed(){

        if ($this->AttributeOperations->getModuleType() == 'naf'){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if called single New/Article/Faqs article
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if called single New/Article/Faqs article
     */
    public function isSingleDisplay(){

        if (!empty($this->nafId)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if latest list used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if latest list used
     */
    public function isLatestListUsed() {

        return NAF_LATEST_ELEMENTS_DISPLAY;

    }

    /**
     * Return true if date range (year/month) is set
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if date range is set
     */
    public function isDateRangeSet(){

        return true;
    }

    /**
     * Return true if calendar should display on this page
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if calendar should display on this page
     */
    public function isCalendarDislpay(){

        if (!empty($this->displayCalendar)){
            return true;
        } else {
            return false;
        }

    }

    /**
     * Return true if any news or years selected, so navigate inside module
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if any news or years selected, so navigate inside module
     */
    public function isModuleInside() {
        if (!empty($this->selectedYear)){
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

            return $this->singleTemplateName;

        } else {

            return $this->listTemplateName;

        }
    }

    /**
     * Return list of NAF elements by date range
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  list of NAFs
	 * @todo @piggy add fields- link and rus month
     */
    public function getListByDateRange(){

        $result             = array();

        if ($this->isSingleDisplay()){
            return "";
        }

        if ($this->isUseAdmin()){
            $active = '';
        } else {
            $active = 'and `active` = 1';
        }

        $pageUrl   = $this->SiteStructure->getAbsolutePageUrl();

        $limit_condition = '';
		$viewRow         = $this->databaseTableLimit;
		$page            = $this->InputFilter->getParameter("page");
        if (!empty($viewRow) && !$page ){
            $limit_condition = "limit {$viewRow}";
        } else if (!empty($viewRow) && $page ){
			$startRow = ($viewRow * $page) + 1;
            $limit_condition = "limit {$startRow}, {$viewRow}";
        }

        $where_condition = '';
        $year  = $this->selectedYear;
        $month = $this->selectedMonth;
        $day   = $this->selectedDay;
        if (!empty($year)){

            if (!empty($month) && !empty($day)){
                $where_condition = "and `date` = '$year-$month-$day'";
            } else if (!empty($month)) {
                $where_condition = "and `date` BETWEEN '$year-$month-01' and '$year-$month-31'";
            } else {
                $where_condition = "and `date` BETWEEN '$year-01-01' and '$year-12-31'";
            }
        }

        $table  = $this->databaseTableName;

        $select = '';
        for ($i=1; $i <= $this->listDisplayFields; $i++){
            $select .= ", `text$i`";
        }

        $query = "select `id`, `date`, `active` $select from $table where `language_id` = {$this->languageId} $active $where_condition order by `date` desc, `id` desc $limit_condition";

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row['text1'] = htmlspecialchars_decode($row['text1'],ENT_QUOTES);

            $year  = substr($row['date'],0,4);
            $month = substr($row['date'],5,2);
            $day   = substr($row['date'],8,2);

            $url   = "$pageUrl$year/$month/$day/{$row['id']}/";

            $count = count($result);

            $img = $this->getImagePath($row['id']);

            $result[$count] = array('Id'                  => $row['id'],
                                    'Url'                 => $url,
                                    'Active'              => $row['active'],
                                    'Date'                => $this->SiteStructure->formateDate($row['date']),
                                    'Img'                 => $img,
                                    'Text1'               => $row['text1']
                                    );

            for ($i=1; $i <= $this->listDisplayFields; $i++){
                $result[$count]["Text$i"] = htmlspecialchars_decode($row["text$i"],ENT_QUOTES);
            }

            $result[$count]['Split']['Text1'] = $this->splitText($row['text1']);

            if (isset($row['text2'])){

                $result[$count]['Split']['Text2'] = $this->splitText($row['text2']);

            }
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return full info about single NAF element
     *
     * @param   nothing
     * @throws  inherited from MySQL
     * @throws  if wrong module used
     * @return  one full NewsArticleFaq element
	 * @todo @piggy add fields- link and rus month
     */
    public function getSingle(){

        $result = array();
        $table  = '';
        $nafId  = $this->nafId;

        if (!$this->isSingleDisplay()){
            return "";
        }

        $table  = $this->databaseTableName;

        $select = '';
        for ($i=1; $i <= $this->singleDisplayFields; $i++){
            $select .= ", `text$i`";
        }

        if ($this->isUseAdmin()){
            $active = '';
        } else {
            $active = 'and `active` = 1';
        }

        $query = "select `id`, `date`, `active`, `link` $select from $table where `id` = $nafId and `language_id` = {$this->languageId} $active";

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row['text1'] = htmlspecialchars_decode($row['text1'],ENT_QUOTES);

            $this->selectedYear  = substr($row['date'],0,4);
            $this->selectedMonth = substr($row['date'],5,2);
            $this->selectedDay   = substr($row['date'],8,2);

            $img  = $this->getImagePath($row['id']);

            $result = array('Id'            => $row['id'],
                            'Active'        => $row['active'],
                            'Date'          => $this->SiteStructure->formateDate($row['date']),
                            'Img'           => $img,
                            'Text1'         => $row['text1'],
            );

            for ($i=1; $i <= $this->singleDisplayFields; $i++){
                $result["Text$i"] = htmlspecialchars_decode($row["text$i"],ENT_QUOTES);
            }
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return list of latest News/Articles/FAQS for any page
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  list of latest News/Articles/FAQS for any page
     */
    public function getLatestList(){

        $result             = array();

        if (!NAF_LATEST_ELEMENTS_DISPLAY){
            return $result;
        }

        $pageUrl   = $this->SiteStructure->getSiteTranslatedSiteUrl() . $this->relativeUrlLatest . "/";

        $limit_condition = '';
        if (!empty($this->databaseTableLimitLatest)){
            $limit_condition = "limit {$this->databaseTableLimitLatest}";
        }

        $where_condition = '';

        $table  = $this->databaseTableNameLatest;

        $select = ', `text1`, `text2`, `text3`';

        $query = "select `id`, `date` $select from $table where `language_id` = {$this->languageId} and `active` = 1 $where_condition order by `date` desc, `id` desc $limit_condition";

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row['text1'] = htmlspecialchars_decode($row['text1'],ENT_QUOTES);
			$row['text2'] = htmlspecialchars_decode($row['text2'],ENT_QUOTES);
			$row['text3'] = htmlspecialchars_decode($row['text3'],ENT_QUOTES);

            $year  = substr($row['date'],0,4);
            $month = substr($row['date'],5,2);
            $day   = substr($row['date'],8,2);

            $url   = "$pageUrl$year/$month/$day/{$row['id']}/";

            $count = count($result);

            $img = $this->getImagePath($row['id']);

            $result[$count] = array('Id'                  => $row['id'],
                                    'Url'                 => $url,
                                    'Date'                => $this->SiteStructure->formateDate($row['date']),
                                    'Img'                 => $img,
                                    'Text1'               => $row['text1'],
                                    'Text2'               => $row['text2'],
                                    'Text3'               => $row['text3']
                                    );

            $result[$count]['Split']['Text1'] = $this->splitText($row['text1'], true);

            if (isset($row['text2'])){

                $result[$count]['Split']['Text2'] = $this->splitText($row['text2'], true);

            }
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return max text fields
     *
     * @param  nothing
     * @throws no throws
     * @return max text fields
     */
    public function getMaxTextFields(){

        return $this->maxTextFields;

    }

    /**
     * Return calendar for NAF
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @throws  if wrong module used
     * @return  calendar for NAF
     */
    public function getCalendar(){

        $result = array();
        $year   = 0;
        $month  = 0;

        if (!$this->isCalendarDislpay()){
            return "";
        }

        $table      = $this->databaseTableName;
        $mainUrl    = $this->SiteStructure->getSiteTranslatedSiteUrl();

        $pageUrl    = $mainUrl . "$this->relativeUrl/";

        $this->MySQL->query("select distinct YEAR(`date`) from $table where `language_id` = {$this->languageId} and `active` = 1 order by `date` desc");

        while ($row = $this->MySQL->fetchArray()){

            $result['Years'][]  = array('Value' => $row['YEAR(`date`)'],
                                        'Url'   => "$pageUrl{$row['YEAR(`date`)']}/"
            );
        }

        if (!isset($result['Years'])){
            $result['Years'][] = array('Value' => '', 'Url' => '');
        }

        $result['Monthes'] = $this->SiteStructure->getMonthesNames();

        if ($this->selectedYear){

            $year = $this->selectedYear;

            $this->MySQL->query("select distinct MONTH(`date`) from $table where `date` BETWEEN '$year-01-01' and '$year-12-31' and `language_id` = {$this->languageId} and `active` = 1 order by `date` desc");

            while ($row = $this->MySQL->fetchArray()){

                $month = sprintf("%02d",$row['MONTH(`date`)']);

                $result['Monthes'][$month]['Url']  = "$pageUrl$year/$month/";
            }
        }

        $result['Selected'] = array('Year' => $this->selectedYear,
                                    'Month'=> $this->selectedMonth,
                                    'Day'  => $this->selectedDay
                                    );

        $this->MySQL->freeResult();

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

        if (!empty($this->isUseAdmin) && $this->Session->isAuthorized()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return admin panel template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin panel template name
     */
    public function getAdminTemplateName(){

        return $this->adminTemplateName;

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
     * Return lastest list template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  lastest list template name
     */
    public function getLatestListTemplateName() {

        return NAF_LATEST_ELEMENTS_TEMPLATE_NAME;

    }

	/**
     * Return page template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  page template name
     */
    public function getPageTemplateName() {

        return NAF_PAGE_TEMPLATE_NAME;

    }

    /**
     * Return one element by nafId for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  one element by nafId for admin panel
     */
    public function getAdminLoadElement(){

        $result = array('Value'=>'','Type'=>'','Id'=>'','Field'=>'');
        $table  = '';
        $type   = '';

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafId          = (integer)$this->InputFilter->getParameter("id");
        $elementType    = (string)$this->InputFilter->getParameter("type");

        if (empty($nafId)){
            throw new ExceptionExt("Naf Id not set");
        }

        $table  = $this->databaseTableName;

        if (preg_match('/^text(\d{1,2})$/', $elementType, $match)){

            if ($match[1] > $this->maxTextFields){
                throw new ExceptionExt("Text field number greater then allowed");
            }

            $select = "text{$match[1]}";
            $type   = "text";

        } else if ($elementType == 'date'){

            $select = "date";
            $type   = "date";

        } else {
            throw new ExceptionExt("Incorrect field type");
        }

        $query = "select `$select`, `date`, `id` from $table where `id` = $nafId and `language_id` = {$this->languageId}";

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row[$select] = htmlspecialchars_decode($row[$select],ENT_QUOTES);

            $result = array('Id'    => $row['id'],
                            'Type'  => $type,
                            'Field' => $elementType,
                            'Value' => $row[$select]
            );

            if ($select == 'text1' && $this->splitText){
                $year  = substr($row['date'],0,4);
                $month = substr($row['date'],5,2);
                $day   = substr($row['date'],8,2);

                $pageUrl = $this->SiteStructure->getAbsolutePageUrl();

                $url   = "$pageUrl$year/$month/$day/$nafId";

                $result['Url'] = $url;
                $result['Split']['Text1'] = $this->splitText($row['text1']);
            }

            if ($select == 'text2' && $this->splitText){
                $year  = substr($row['date'],0,4);
                $month = substr($row['date'],5,2);
                $day   = substr($row['date'],8,2);

                $pageUrl = $this->SiteStructure->getAbsolutePageUrl();

                $url   = "$pageUrl$year/$month/$day/$nafId";

                $result['Url'] = $url;
                $result['Split']['Text2'] = $this->splitText($row['text2']);

            }

            if ($type == 'date'){
                $result['Date'] = $this->SiteStructure->formateDate($row['date']);
            }
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return last modified data by ajax
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  last modified data by ajax
     */
    public function getAdminLastModifiedData(){

        $result     = array();
        $table      = '';
        $nafId      = $this->modifiedId;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($nafId)){
            throw new ExceptionExt("Naf Id not set");
        }

        $pageUrl = $this->SiteStructure->getAbsolutePageUrl();

        $table  = $this->databaseTableName;

        $select = '';
        for ($i=1; $i <= $this->maxTextFields; $i++){
            $select .= ", `text$i`";
        }

        $query = "select `id`, `date`, `active` $select , `link` from $table where `id` = $nafId and `language_id` = {$this->languageId}";

        $this->MySQL->query($query);

        while ($row = $this->MySQL->fetchArray()){

            $row['text1'] = htmlspecialchars_decode($row['text1'],ENT_QUOTES);

            $year  = substr($row['date'],0,4);
            $month = substr($row['date'],5,2);
            $day   = substr($row['date'],8,2);

            $url = "$pageUrl$year/$month/$day/{$row['id']}";
            $img = $this->getImagePath($row['id']);

            $result[0] = array('Id'     => $row['id'],
                               'Url'    => $url,
                               'Active' => $row['active'],
                               'Date'   => $this->SiteStructure->formateDate($row['date']),
                               'Img'    => $img,
                               'Text1'  => $row['text1']
            );

            for ($i=1; $i <= $this->maxTextFields; $i++){
                $result[0]["Text$i"] = htmlspecialchars_decode($row["text$i"],ENT_QUOTES);
            }

            $result[0]['Split']['Text1'] = $this->splitText($row['text1']);

            if (isset($row['text2'])){

                $result[0]['Split']['Text2'] = $this->splitText($row['text2']);

            }
        }

        if (empty($result)){
            throw new ExceptionExt("Result is empty");
        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Add new Naf element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addNafElement(){

        $i      = 0;
        $value  = '';
        $text   = '';
        $values = '';

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        $Typograph = new Typograph();

        for ($i=1;$i<=$this->maxTextFields;$i++){

            if (!empty($newData["text$i"])){
                $value = $newData["text$i"];
                $value = $Typograph->parse($value);
                $text   .= ", `text$i`";
                $values .= ", '$value'";
            }
        }

        if (!empty($newData["active"])){
            $active = 1;
        } else {
            $active = 0;
        }

        $date  = date("Y-m-d");
        if (!empty($newData["date"])){

            if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/i',$newData["date"])){
                $date  = $newData["date"];
            }
        }

        /*********************************************************************************/

        if (!empty($newData["newsType"])){
                $text   .= ", `link`";
                $values .= ", ".$newData["newsType"];
        }

        /*********************************************************************************/

        $table = $this->databaseTableName;

        $this->MySQL->query("INSERT INTO $table (`date` $text, `active`, `language_id`) values('$date' $values, $active, {$this->languageId})");

		if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Element not inserted");

		}

		$nafId = $this->MySQL->insertedId();

        if (!empty($nafId)){
            $this->modifiedId = $nafId;
        } else {
            $this->modifiedId = 0;
        }

		/*********************************************************************************/
		$tmpUrlFileName = $this->InputFilter->getParameter("file");

		if (!empty($tmpUrlFileName)){

			$tmpFileName_s  = NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            $this->saveTemporaryUploadedFile($tmpFileName_s);

        }
		/*********************************************************************************/

        return true;
    }

    /**
     * Edit Naf element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function editNafElement(){

        $i      = 0;
        $value  = '';
        $text   = '';

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafId = (integer)$this->InputFilter->getParameter("id");

        if (empty($nafId)){
            throw new ExceptionExt("Naf Id not set");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        $Typograph = new Typograph();

        for ($i=1;$i<=$this->maxTextFields;$i++){

            if (!empty($newData["text$i"])){
                $value = $newData["text$i"];
                $value = $Typograph->parse($value);
                $text   .= ", `text$i` = '$value'";
            }
        }

        if (!empty($newData["active"])){
            $active = "`active` = 1";
        } else {
            $active = "`active` = 0";
        }

		$link = "";
		if (isset($newData["newsType"])) {
			$link = ", `link` = ". $newData["newsType"];
		}


        $date  = date("Y-m-d");
        if (!empty($newData["date"])){

            if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/i',$newData["date"])){
                $date  = ", `date` = '{$newData["date"]}'";
            }
        }

        $table = $this->databaseTableName;

        $this->MySQL->query("UPDATE $table SET $active $date $text $link where `id` = $nafId");

        if(!$this->MySQL->affectedRows()){

            //throw new ExceptionExt("Element not updated");

        }

        if (!empty($nafId)){
            $this->modifiedId = $nafId;
        } else {
            $this->modifiedId = 0;
        }

		/*********************************************************************************/
		$tmpUrlFileName = $this->InputFilter->getParameter("file");

		if (!empty($tmpUrlFileName)){

            $tmpFileName_s  = NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM . basename($tmpUrlFileName);

            $this->saveTemporaryUploadedFile($tmpFileName_s);

        }
		/*********************************************************************************/

        return true;

    }

    /**
     * Remove Naf element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeNafElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafIds = $this->InputFilter->getParameter("json");

        if (!is_array($nafIds)){
            throw new ExceptionExt("Naf Ids not set");
        }

        /** can be used implode() */
        $inStatement = '';
        foreach ($nafIds as $nafId){
            if (empty($inStatement)){
                $inStatement .= "$nafId";
            } else {
                $inStatement .= ",$nafId";
            }

        }

        $table = $this->databaseTableName;

        $this->MySQL->query("DELETE FROM $table WHERE `id` IN ($inStatement)");

        if($this->MySQL->affectedRows()){
			$removeFilesMask    = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM . $table . "-{". $inStatement . "}" . "-[bs].*";
            $removeFilesList    = glob($removeFilesMask, GLOB_NOSORT | GLOB_BRACE);

            if (!empty($removeFilesList)){

                foreach ( $removeFilesList as $filename){

                    if (false === unlink($filename)){
                        throw new ExceptionExt("Can not remove picture old file ($filename)");
                    }
                }
            }
        } else {

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
    public function inverseActivityNafElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafIds = $this->InputFilter->getParameter("json");

        if (!is_array($nafIds)){
            throw new ExceptionExt("Naf Ids not set");
        }

        /** can be used implode() */
        $inStatement = '';
        foreach ($nafIds as $nafId){
            if (empty($inStatement)){
                $inStatement .= "$nafId";
            } else {
                $inStatement .= ", $nafId";
            }
        }

        $table = $this->databaseTableName;

        $this->MySQL->query("UPDATE $table SET `active` = NOT `active` WHERE `id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Activity not inversed");

        }

        return true;
    }

    /**
     * Return one element by nafId for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  one element by nafId for admin panel
     */
    public function saveAdminElement(){

        $table  = '';
        $type   = '';
        $update = '';

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafId          = (integer)$this->InputFilter->getParameter("id");
        $elementType    = (string)$this->InputFilter->getParameter("type");
        $elementValue   = (string)$this->InputFilter->getParameter("value");

        if (empty($nafId)){
            throw new ExceptionExt("Naf Id not set");
        }

        $table  = $this->databaseTableName;

        if (preg_match('/^text(\d{1,2})$/', $elementType, $match)){

            if ($match[1] > $this->maxTextFields){
                throw new ExceptionExt("Text field number greater then allowed");
            }

            $Typograph = new Typograph();
            $elementValue = $Typograph->parse($elementValue);
            $update = "text{$match[1]}";
            $type   = "text";

        } else if ($elementType == 'date'){

            $update = "date";
            $type   = "date";

        } else {

            throw new ExceptionExt("Incorrect field type");

        }

        $this->MySQL->query("UPDATE $table SET $update='$elementValue' where `id` = $nafId");

        if(!$this->MySQL->affectedRows()){

            //throw new ExceptionExt("Element not updated");

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

        $result = array( 'Lines' =>  array(), 'Title' => 'Редактирование', 'Action' => 'Edit' );

        $nafId  = (integer)$this->InputFilter->getParameter("id");

        if (empty($nafId)){
            throw new ExceptionExt("Naf Id not set");
        }

        $result['Id'] = $nafId;

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['Lines'] = $ADMIN_WINDOW[$page];

        $table  = $this->databaseTableName;

        $this->MySQL->query("select * from $table where `id` = $nafId and `language_id` = {$this->languageId}");

        while ($row = $this->MySQL->fetchArray()){

            foreach ($result['Lines'] as &$field){

                if (empty($row[$field['Name']])){
                    $field['Value'] = '';
                } else {
                    $field['Value'] = $row[$field['Name']];
                }
				if ($field['Name'] == 'picture') {
                    $field['Value'] = '';
                    $field['Img'] = $this->getImagePath($row['id']);

				}
            }
        }

        $this->MySQL->freeResult();

        return $result;
    }
}
?>
