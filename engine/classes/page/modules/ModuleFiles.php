<?php
/**
 * Module Files, used for manage files entire site
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      Alex
 * @version     1.0
 */
class ModuleFiles {

    private $Session        = null;
    private $SiteStructure  = null;
    private $enableTemplate = 0;

    /**
     * Constructor of class ModuleSearch
     */
    public function  __construct(Session $Session, SiteStructure $SiteStructure, $enableTemplate = 0) {

        $this->Session        = $Session;
        $this->SiteStructure  = $SiteStructure;

        $this->enableTemplate = $enableTemplate;
       
    }

    /**
     * Return true if user admin
     *
     * @param  nothing
     * @throws no throws
     * @return true if user admin
     */
    public function isUseAdmin(){

        if ($this->Session->isAuthorized()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if module used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if module used
     */
    public function enableTemplate() {

        if ($this->enableTemplate){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return files module template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  files module template name
     */
    public function getTemplateName() {

        return FILES_MODULE_TEMPLATE_NAME;

    }

    /**
     * Return pricelist path
     *
     * @param   nothing
     * @throws  no throws
     * @return  pricelist path
     */
    public function getPricelist() {

        $result = array();

        $files = glob(FILES_FOLDER . FILES_PRICELIST_NAME . ".*");

        if (count($files) == 0){
            return '';
        }

        if (count($files) > 1){
            throw new ExceptionExt("Can not detect price list file - files is pricelist folder is more then one");
        }
        
        $file = $files[0];
        $webFile = FILES_FOLDER_WEB . basename($file);

        if (!is_file($file)){
            throw new ExceptionExt("Error found file $file");
        }

        $stat = stat($file);

        $result = array("Filename"  => $webFile,
                        "Size"      => round($stat['size'] / 1024, 1),
                        "Date"      => $this->SiteStructure->formateDate(date("Y-m-d",$stat['mtime']))
                        );

        return $result;

    }

    /**
     * Save uploaded pricelist
     *
     * @param   nothing
     * @throws  if user is not admin
     * @return  nothing
     */
    public function savePricelist() {

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        if (empty($_FILES["file"])){
            throw new ExceptionExt("No file transmitted");
        }

        if (preg_match("/\.(.+?)$/", $_FILES["file"]["name"], $match)){

            switch($match[1]){
                case "doc":
                case "docx":
                case "xls":
                case "xlsx":
                    $extention = $match[1];
                    break;
                default:
                throw new ExceptionExt("Incorrect file type ({$match[1]})");
            }
        } else {
            throw new ExceptionExt("Can not determine file extention ".$_FILES["file"]["name"]);
        }

        $tmpFileName = FILES_FOLDER . FILES_PRICELIST_NAME . '.' . $extention;

        if (is_dir(dirname(FILES_FOLDER))){
            if (!is_dir(FILES_FOLDER)){
                mkdir(FILES_FOLDER);
            }
        } else {
            throw new ExceptionExt("Parent folder not exist  ".dirname(FILES_FOLDER));
        }

        if (!is_writable(FILES_FOLDER)){
            throw new ExceptionExt("Can not write file to directory ".FILES_FOLDER);
        }
        
        $files = glob(FILES_FOLDER . FILES_PRICELIST_NAME . ".*");

        if (count($files)){

            foreach($files as $file){

                if (is_file($file)){
                    unlink($file);
                }
            }
        }

        if (false === move_uploaded_file($_FILES['file']['tmp_name'], $tmpFileName)) {
            throw new ExceptionExt("Can not move uploaded file");
        }

        return true;

    }

    /**
     * Remove uploaded pricelist
     *
     * @param   nothing
     * @throws  if user is not admin
     * @return  nothing
     */
    public function removePricelist() {
        
        $tmpFileName = FILES_FOLDER . FILES_PRICELIST_NAME;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $files = glob(FILES_FOLDER . FILES_PRICELIST_NAME . ".*");

        if (count($files) == 0){
            return true;
        }

        if (count($files) > 1){
            throw new ExceptionExt("Can not detect price list file - files is pricelist folder is more then one");
        }

        $file = $files[0];

        if (is_file($file)){
            if (false === unlink($file)){
                throw new ExceptionExt("Can not remove pricelist file");
            }
        }

        return true;

    }
}
?>
