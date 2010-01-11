<?php
/**
 * Module General
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
abstract class ModuleGeneral {

    protected $MySQL                = null;
    protected $SiteStructure        = null;
    protected $AttributeOperations  = null;
    protected $InputFilter          = null;
    protected $Session              = null;

    protected $moduleName           = '';
    protected $languageId           = 0;

    protected $imageTypes           = array();
    protected $additionalImageTypes = array();

    /**
     * Constructor of class ModuleNAF
     *
     * @param MySQL $MySQL
     * @param SiteStructure $SiteStructure
     * @param AttributeOperations $AttributeOperations
     * @param InputFilter $InputFilter
     * @param Session $Session
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure, AttributeOperations $AttributeOperations, InputFilter $InputFilter, Session $Session) {

        $this->MySQL                = $MySQL;
        $this->SiteStructure        = $SiteStructure;
        $this->AttributeOperations  = $AttributeOperations;
        $this->InputFilter          = $InputFilter;
        $this->Session              = $Session;

        $this->getConfigData();

        $this->setModuleParameters();

    }

    /**
     * Used for passing references to needed classes into this class
     *
     * @param MySQL $MySQL
     * @param SiteStructure $SiteStructure
     * @param AttributeOperations $AttributeOperations
     * @param InputFilter $InputFilter
     * @param Session $Session
     */
    final public function setClassesHandlers(MySQL $MySQL, SiteStructure $SiteStructure, InputFilter $InputFilter, AttributeOperations $AttributeOperations, Session $Session){

        $this->MySQL                = $MySQL;
        $this->SiteStructure        = $SiteStructure;
        $this->InputFilter          = $InputFilter;
        $this->AttributeOperations  = $AttributeOperations;
        $this->Session              = $Session;

        $this->getConfigData();

        $this->setModuleParameters();

    }

    /**
     * Used for read config data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function getConfigData(){

    }

    /**
     * Setup module parameters
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function setModuleParameters(){

    }

    /**
     * Set image types list
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function setImageTypes() {

        if ($this->moduleName === "naf"){

            global $NAF_IMAGE_TYPES, $NAF_ADDITIONAL_IMAGE_TYPES;

            $this->imageTypes           = $NAF_IMAGE_TYPES;
            $this->additionalImageTypes = $NAF_ADDITIONAL_IMAGE_TYPES;

        } else if ($this->moduleName === "catalogue"){

            global $CATALOGUE_IMAGE_TYPES, $CATALOGUE_ADDITIONAL_IMAGE_TYPES;

            $this->imageTypes           = $CATALOGUE_IMAGE_TYPES;
            $this->additionalImageTypes = $CATALOGUE_ADDITIONAL_IMAGE_TYPES;

        } else {

            throw new ExceptionExt("Module name not setup");

        }
    }

	/**
	 * Return path and size image
     *
	 * @param   $id product id
	 * @throws  nothing
	 * @return  image path and size
	 */
	protected function getImagePath($id){

        switch ($this->moduleName){
            case "naf":
                $imgFolder  = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFilder = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = NAF_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = NAF_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $useSingleFilder = 1;
                break;
            case "catalogue":
                $imgFolder  = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFilder = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $useSingleFilder = CATALOGUE_IMG_IN_ONE_FOLDER;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

		$img       = array();
        $size      = array();

        $img['Id'] = $id;

		if ($useSingleFilder) {

            if (!count($this->imageTypes)){
                throw new ExceptionExt("Image types not set");
            }

            foreach($this->imageTypes as $name => $options){
                $fileName = sprintf($options['MaskName'], $id);

                if(!file_exists($fileFilder . $fileName)){

                    $fileName = $options['DefaultName'];

                    if (!file_exists($fileFilder . $fileName)){
                        throw new ExceptionExt("Default file for image type '$name' not exist");
                    }

                }

                $size = getimagesize($fileFilder . $fileName);

                $img[$name] = array('Url'  => $imgFolder . $fileName,
                                    'Size' => $size[3]
                );

            }

            //Additional images
            //$additionalMask = glob(CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM . "$id/" . "*.{jpg,jpeg,gif,png}", GLOB_NOSORT | GLOB_BRACE);

            if (count($this->additionalImageTypes)){

                foreach($this->additionalImageTypes as $name => $options){

                    $fileMask = sprintf($options['MaskName'], '*');

                    $additionalMask = glob($additionalFolderFile . "$id/$fileMask");

                    if (!count($additionalMask)){
                        continue;
                    }

                    $img['AdditionalImagesCount'] = count($additionalMask);

                    foreach($additionalMask as $fileName){

                        $size = getimagesize($fileName);
                        $key = 0;
                        if (preg_match('/(\d+)/i', basename($fileName, ".jpg"), $match)){
                            $key = $match[1];
                        }

                        $img['AdditionalImages'][$name][] = array(  'Url'  => $additionalFolderWeb . "$id/" . basename($fileName) . "?" . time(),
                                                                    'Size' => $size[3],
                                                                    'Id'   => $id,
                                                                    'Key'  => $key
                        );
                    }
                }
            }
		} else {
			$smallMask = glob($imgFolder . $id .'/'."[0-9]*-[s].*", GLOB_NOSORT);
			$bigMask   = glob($imgFolder . $id .'/'."[0-9]*-[b].*", GLOB_NOSORT);

            $sizeSmall = getimagesize($smallImg);
            $sizeBig   = getimagesize($bigImg);

            $img = array('Small' => array('Url'  => $smallImg,
                                          'Size' => $sizeSmall[3]
                                          ),
                         'Big'   => array('Url'  => $bigImg,
                                          'Size' => $sizeBig[3]
                                          )
                        );
			$count                = 0;

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

    /**
     * Save temprary file
     *
     * @param   $tmpFileName temp file name
     * @param   $additionalFile set 1 if uploaded file should be save in additional files
     * @throws  if file not exists
     * @return  nothing
     */
    protected function saveTemporaryUploadedFile($tmpFileName, $additionalFile = 0) {

        switch ($this->moduleName){
            case "naf":
                $imgFolder  = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFilder = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = NAF_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = NAF_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $useSingleFilder = 1;
                break;
            case "catalogue":
                $imgFolder  = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFilder = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $useSingleFilder = CATALOGUE_IMG_IN_ONE_FOLDER;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

        $result    = array('','');

        $imgType = $this->InputFilter->getParameter("imgType");
        $imgType = trim($imgType, "-"); // data will be received with '-' char before ahead
        $imgType = ucfirst($imgType);

        if (empty($imgType)){
            throw new ExceptionExt("Image Type not setup");
        }

        if (!file_exists($tmpFileName)){
            throw new ExceptionExt("Temporary file not exists $tmpFileName");
        }

        if (preg_match("/\.(\w+?)$/i", $tmpFileName, $match)){
            $tmpFileExtention = $match[1];
        } else {
            throw new ExceptionExt("Can not find temporary file extention");
        }

        if ($additionalFile == 0){

            if (!count($this->imageTypes)){
                throw new ExceptionExt("Image types not set");
            }

            foreach($this->imageTypes as $name => $options){

                $fileName = sprintf($options['MaskName'], $this->modifiedId);
                $fileNameFull = $fileFilder . $fileName;

                //remove old file
                if(file_exists($fileNameFull)){
                    unlink($fileNameFull);
                }

                if ($options['UseOriginalSize']){

                    if (false === ImageScale::copyPicture($tmpFileName, $fileNameFull, $tmpFileExtention) ){
                        throw new ExceptionExt("Can not copy picture for picture type $name");
                    }

                } else {

                    if (false === ImageScale::scalePicture($tmpFileName, $fileNameFull, $tmpFileExtention, $options['Size'], $options['DisableCrop']) ){
                        throw new ExceptionExt("Can not decrease picture size for picture type $name");
                    }
                }

                if ($name == $imgType/*$options['UseAsPreview']*/){
                    $size = getimagesize($fileNameFull);
                    $result = array($imgFolder . basename($fileNameFull).'?'.time(), $size[3]);
                }
            }
        } else {

            $i = 1; $searchI = true;
            $additionalFolder = $additionalFolderFile . "{$this->modifiedId}/";

            if (!count($this->additionalImageTypes)){
                throw new ExceptionExt("Image Types not set");
            }

            foreach($this->additionalImageTypes as $name => $options){

                $fileName = sprintf($options['MaskName'], sprintf("%02d",$i));
                $fileNameFull = $additionalFolder . $fileName;

                while ($searchI && file_exists($fileNameFull)){

                    $i++;
                    $fileName = sprintf($options['MaskName'], sprintf("%02d",$i));
                    $fileNameFull = $additionalFolder . $fileName;

                }

                $searchI = false;

                //remove old file
                if(file_exists($fileNameFull)){
                    unlink($fileNameFull);
                }

                if ($options['UseOriginalSize']){

                    if (false === ImageScale::copyPicture($tmpFileName, $fileNameFull, $tmpFileExtention) ){
                        throw new ExceptionExt("Can not copy picture for picture type $name");
                    }

                } else {

                    if (false === ImageScale::scalePicture($tmpFileName, $fileNameFull, $tmpFileExtention, $options['Size'], $options['DisableCrop']) ){
                        throw new ExceptionExt("Can not decrease picture size for picture type $name");
                    }
                }

                if ($name == $imgType/*$options['UseAsPreview']*/){
                    $size = getimagesize($fileNameFull);
                    $result = array($additionalFolderWeb . "{$this->modifiedId}/" . basename($fileNameFull).'?'.time(), $size[3]);
                }
            }
        }

        if (false === unlink($tmpFileName)){
            throw new ExceptionExt("Can not remove picture temp file ($tmpFileName)");
        }

        if (empty($result[0]) && empty($result[1])){
            throw new ExceptionExt("Image Type $imgType not found in config");
        }

        return $result;
    }

    /**
     * Expand uploaded zip archive with additional pictures
     *
     * @param   $archiveName filename of archive
     * @throws  in any errors
     * @return  nothing
     */
    protected function expandZipArchive($archiveName) {

        switch ($this->moduleName){
            case "naf":
                $imgTmpFolder  = NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                break;
            case "catalogue":
                $imgTmpFolder  = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

        $files  = array();
        $result = array();

        if (!file_exists($archiveName)){
            throw new ExceptionExt("Archive file not exists");
        }

        $za = new ZipArchive();

        if ($za->open($archiveName) !== true){
            throw new ExceptionExt("Can not open zip archive file");
        }

        for ($i=0; $i<$za->numFiles; $i++) {

            $fileName = $za->getNameIndex($i);

            //skip folders
            if (preg_match("/\/$/", $fileName)){
                continue;
            }

            //move file to folder root
            if (preg_match("/\.(.+?)$/", $fileName, $match)){

                $newFileName = "{$this->modifiedId}.zip.".strtolower($match[1]);
                $za->renameIndex($i,$newFileName);
                $files = array($newFileName);

            } else {

                $za->close();
                throw new ExceptionExt("Can not determine file extention for index $i");
            }

            $newFileName = $imgTmpFolder . $newFileName;

            $za->extractTo($imgTmpFolder, $files);

            $result[] = $this->saveTemporaryUploadedFile($newFileName, 1);

            $za->deleteIndex($i);

        }

        $za->close();

        return $result;
    }

    /**
     * Return array of images
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of images
     */
    public function getImages() {

        $id = (integer)$this->InputFilter->getParameter("id");

        return $this->getImagePath($id);

    }

    /**
     * Save the uploaded big picture
     *
     * @param   nothing
     * @throws
     * @return  nothing
     */
    public function saveUploadedFile(){

        switch ($this->moduleName){
            case "naf":
                $imgFolder  = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFilder = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = NAF_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = NAF_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $imgTmpFolder           = NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                $imgTmpFolderWeb        = NAF_MODULE_IMG_FOLDER_TEMP_WEB;
                $useSingleFilder = 1;
                break;
            case "catalogue":
                $imgFolder  = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFilder = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $imgTmpFolder           = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                $imgTmpFolderWeb        = CATALOGUE_MODULE_IMG_FOLDER_TEMP_WEB;
                $useSingleFilder = CATALOGUE_IMG_IN_ONE_FOLDER;
                break;
            default:
                throw new ExceptionExt("Module name not setup 1");
                break;
        }

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($_FILES["upload"])){
            throw new ExceptionExt("No file transmitted");
        }

        //1 - if quick load(picture should be saved immediately), 0 - full add/edit(picture should be saved in add/edit procedure)
        $qload = $this->InputFilter->getParameter("qload");

        $this->modifiedId = (integer)$this->InputFilter->getParameter("id");

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

        if (!is_dir($imgTmpFolder)){
            mkdir($imgTmpFolder);
        }

        if (!is_writable($imgTmpFolder)){
            throw new ExceptionExt("Image temp folder is not writable - $imgTmpFolder");
        }

        $tmpFileName    = tempnam($imgTmpFolder, "cat");
        unlink($tmpFileName);
        $tmpFileName    = $tmpFileName . ".$extention";
        $tmpUrlFileName = $imgTmpFolderWeb . basename($tmpFileName);

        if (false === move_uploaded_file($_FILES['upload']['tmp_name'], $tmpFileName)) {
            throw new ExceptionExt("Can not move uploaded file");
        }

        if ($qload == 1){   //quick load(picture should be saved immediately)

            $result = $this->saveTemporaryUploadedFile($tmpFileName);

            return $result;

        } else {

            $preview = '';
            foreach ($this->imageTypes as $type){
                if ($type['UseAsPreview']){
                    $preview = $type['Size'];
                }
            }

            if (empty($preview)){
                throw new ExceptionExt("Can not determine preview type");
            }

            $size = getimagesize($tmpFileName);

            list($preWidth, $preHeight) = explode("x", $preview);

            if ($size[0] > $size[1]){

                $scaleHeight = $preHeight;
                $scaleWidth  = (integer)(($size[0] * $preHeight) / $size[1]);

            } else {

                $scaleWidth  = $preWidth;
                $scaleHeight = (integer)(($size[1] * $preWidth) / $size[0]);

            }

            $line = "width='$scaleWidth' height='$scaleHeight'";

            return array($tmpUrlFileName.'?'.time(),$line);

        }
    }

    /**
     * Save the uploaded big picture
     *
     * @param   nothing
     * @throws
     * @return  nothing
     */
    public function saveAdditionalFile(){

        switch ($this->moduleName){
            case "naf":
                $imgFolder  = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFilder = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = NAF_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = NAF_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $imgTmpFolder           = NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                $imgTmpFolderWeb        = NAF_MODULE_IMG_FOLDER_TEMP_WEB;
                $useSingleFilder = 1;
                break;
            case "catalogue":
                $imgFolder  = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFilder = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $imgTmpFolder           = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                $imgTmpFolderWeb        = CATALOGUE_MODULE_IMG_FOLDER_TEMP_WEB;
                $useSingleFilder = CATALOGUE_IMG_IN_ONE_FOLDER;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($_FILES["upload"])){
            throw new ExceptionExt("No file transmitted");
        }

        //1 - if quick load(picture should be saved immediately), 0 - full add/edit(picture should be saved in add/edit procedure)
        $qload = $this->InputFilter->getParameter("qload");

        $this->modifiedId = (integer)$this->InputFilter->getParameter("id");
        if (empty($this->modifiedId)){
            throw new ExceptionExt("Product Id not set");
        }

        switch($_FILES["upload"]["type"]){
            case "application/zip":
            case "application/x-zip-compressed":
                $extention = "zip";
                break;
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

        if (!is_dir($additionalFolderFile)){
            mkdir($additionalFolderFile);
        }

        if (!is_writable($additionalFolderFile)){
            throw new ExceptionExt("Image temp folder is not writable - $additionalFolderFile");
        }

        $additionalFolder = $additionalFolderFile . "{$this->modifiedId}/";

        if (!is_dir($additionalFolder)){
            mkdir($additionalFolder);
        }

        if (!is_writable($additionalFolder)){
            throw new ExceptionExt("Image temp folder is not writable - $additionalFolder");
        }

        if (!is_dir($imgTmpFolder)){
            mkdir($imgTmpFolder);
        }

        if (!is_writable($imgTmpFolder)){
            throw new ExceptionExt("Image temp folder is not writable - $imgTmpFolder");
        }

        $tmpFileName    = tempnam($imgTmpFolder, "cat");
        unlink($tmpFileName);
        $tmpFileName    = $tmpFileName . ".$extention";

        if ($extention == 'zip'){

            $tmpFileName = $imgTmpFolder . "{$this->modifiedId}.zip";

            if (false === move_uploaded_file($_FILES['upload']['tmp_name'], $tmpFileName)) {
                throw new ExceptionExt("Can not move uploaded zip file");
            }

            $this->expandZipArchive($tmpFileName);

            if (false === unlink($tmpFileName)){
                throw new ExceptionExt("Can not remove temp zip file");
            }

            return array('zip', 'zip');

        } else {

            if (false === move_uploaded_file($_FILES['upload']['tmp_name'], $tmpFileName)) {
                throw new ExceptionExt("Can not move uploaded file");
            }

            $result = $this->saveTemporaryUploadedFile($tmpFileName, 1);

            return $result;

        }
    }

    /**
     * Remove additional file
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function removeAdditionalFile() {

        switch ($this->moduleName){
            case "naf":
                $imgFolder  = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFilder = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = NAF_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = NAF_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $imgTmpFolder           = NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                $imgTmpFolderWeb        = NAF_MODULE_IMG_FOLDER_TEMP_WEB;
                $useSingleFilder = 1;
                break;
            case "catalogue":
                $imgFolder  = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFilder = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $additionalFolderFile   = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM;
                $additionalFolderWeb    = CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_WEB;
                $imgTmpFolder           = CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM;
                $imgTmpFolderWeb        = CATALOGUE_MODULE_IMG_FOLDER_TEMP_WEB;
                $useSingleFilder = CATALOGUE_IMG_IN_ONE_FOLDER;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $id     = (integer)$this->InputFilter->getParameter("id");
        $key    = (integer)$this->InputFilter->getParameter("key");

        $additionalFolder = $additionalFolderFile . "$id/";

        if (!count($this->additionalImageTypes)){
            throw new ExceptionExt("Image types not set");
        }

        foreach($this->additionalImageTypes as $name => $options){

            $fileName = sprintf($options['MaskName'], sprintf("%02d",$key));
            $fileNameFull = $additionalFolder . $fileName;

            if (file_exists($fileNameFull)){
                if (false === unlink($fileNameFull)){
                    throw new ExceptionExt("Can not remove file $fileNameFull");
                }
            }
        }

        $additionalMask = glob($additionalFolder . "*.*", GLOB_NOSORT);

        if (count($additionalMask) == 0){
            if (false === rmdir($additionalFolder)){
                throw new ExceptionExt("Can not remove empty dir $additionalMask");
            }
        }
    }

    /**
     * Used for work with CKEditor page
     *
     * @param   nothing
     * @throws  no throws
     * @return  js script
     */
    public function saveCKEditorPicture() {

        switch ($this->moduleName){
            case "naf":
                $imgFolder              = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFolder             = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolder      = NAF_MODULE_IMG_CKEDITOR_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolderWeb   = NAF_MODULE_IMG_CKEDITOR_FOLDER_WEB;
                break;
            case "catalogue":
                $imgFolder              = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFolder             = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolder      = CATALOGUE_MODULE_IMG_CKEDITOR_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolderWeb   = CATALOGUE_MODULE_IMG_CKEDITOR_FOLDER_WEB;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

        $ckeditor = $this->InputFilter->getParameter("CKEditor");
        $time = time();
        $tmpFileName  = "{$imgCKEditorFolder}{$ckeditor}-{$time}.jpg";

        if (empty($_FILES["upload"])){
            throw new ExceptionExt("No file transmitted");
        }

        if (!is_writable($fileFolder)){
            throw new ExceptionExt("Image folder is not writable - $fileFolder");
        }

        if (!is_dir($imgCKEditorFolder)){
            mkdir($imgCKEditorFolder, 0777, 1);
        }

        if (!is_writable($imgCKEditorFolder)){
            throw new ExceptionExt("Image CKEditor folder is not writable - $imgCKEditorFolder");
        }

        if (false === is_uploaded_file($_FILES['upload']['tmp_name'])){
            throw new ExceptionExt("File is not uploaded through HTTP POST");
        }

        if (false === move_uploaded_file($_FILES['upload']['tmp_name'], $tmpFileName)) {
            throw new ExceptionExt("Can not move uploaded file");
        }

        print "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(1, '". FULL_SITE_URL . '/' . $imgCKEditorFolderWeb . "{$ckeditor}-{$time}.jpg', '');</script>";

    }

    /**
     * Used for work with CKEditor page
     *
     * @param   nothing
     * @throws  no throws
     * @return  js script
     */
    public function browseCKEditorPicture() {

        switch ($this->moduleName){
            case "naf":
                $imgFolder              = NAF_MODULE_IMG_FOLDER_WEB;
                $fileFolder             = NAF_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolder      = NAF_MODULE_IMG_CKEDITOR_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolderWeb   = NAF_MODULE_IMG_CKEDITOR_FOLDER_WEB;
                break;
            case "catalogue":
                $imgFolder              = CATALOGUE_MODULE_IMG_FOLDER_WEB;
                $fileFolder             = CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolder      = CATALOGUE_MODULE_IMG_CKEDITOR_FOLDER_FILE_SYSTEM;
                $imgCKEditorFolderWeb   = CATALOGUE_MODULE_IMG_CKEDITOR_FOLDER_WEB;
                break;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }

        $ckeditor = $this->InputFilter->getParameter("CKEditor");
        $time = time();
        $tmpFileName  = "{$imgCKEditorFolder}{$ckeditor}-{$time}.jpg";

        print "<img src=\"". FULL_SITE_URL . '/' . $imgCKEditorFolderWeb . "{$ckeditor}-{$time}.jpg\" />";

    }

    /**
     * Return quick load parameter - if edit element in page qload = 1, if in admin window qload = 0
     *
     * @param   nothing
     * @throws  no throws
     * @return  quick load parameter - if edit element in page qload = 1, if in admin window qload = 0
     */
    public function isQuickLoad() {

        return $this->InputFilter->getParameter("qload");

    }

    /**
     * Return admin pictures template name
     * Used to return image after edit
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin pictures template name
     */
    public function getAdminPicturesTemplateName(){

        switch ($this->moduleName){
            case "naf":
                return NAF_MODULE_ADMIN_ADDITIONAL_QUICK_TEMPLATE_NAME;
            case "catalogue":
                return CATALOGUE_MODULE_ADMIN_ADDITIONAL_QUICK_TEMPLATE_NAME;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }
    }

    /**
     * Return admin additional pictures template name for full edit
     * Used to return html block with additional pictures in admin window
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin pictures template name
     */
    public function getAdminAdditionalPicturesFullEditTemplateName(){

        switch ($this->moduleName){
            case "naf":
                return NAF_MODULE_ADMIN_ADDITIONAL_FULL_TEMPLATE_NAME;
            case "catalogue":
                return CATALOGUE_MODULE_ADMIN_ADDITIONAL_FULL_TEMPLATE_NAME;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }
    }

    /**
     * Return admin additional pictures template name for quick edit
     * Used to return html block with additional pictures in admin window
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin pictures template name
     */
    public function getAdminAdditionalPicturesQuickEditTemplateName(){

        switch ($this->moduleName){
            case "naf":
                return NAF_MODULE_ADMIN_ADDITIONAL_QUICK_TEMPLATE_NAME;
            case "catalogue":
                return CATALOGUE_MODULE_ADMIN_ADDITIONAL_QUICK_TEMPLATE_NAME;
            default:
                throw new ExceptionExt("Module name not setup");
                break;
        }
    }

    /**
     * Return admin window
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin window
     */
    public function getAdminWindow(){

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

			if ($field['Type'] == 'file') {
                $field['Img']   = $this->getImagePath(0);
			}
        }

        return $result;
    }
}
?>
