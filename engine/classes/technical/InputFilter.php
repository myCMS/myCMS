<?php
/**
 * Used for filtering input parameters
 *
 * @package     Engine
 * @subpackage  Engine
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 *
 */
class InputFilter {
    private $Parameters = array();
    private $pageUrl    = '';

    /**
     * Constructor of class InputFilter
     *
     * filtering input parameters ($_GET/$_POST) through
     * htmlentities function, will be extended in future
     */
    public function  __construct() {
        if (isset($_GET)){
            foreach ($_GET as $key => $value){
                if ($key == 'json') {
                    $json = json_decode($value, true);
                    if (!is_array($json)){
                        continue;
                    }
                    if (!count($json)){
                        continue;
                    }
                    foreach($json as &$jsonValue){
                        $jsonValue  = htmlspecialchars($jsonValue,ENT_QUOTES,'UTF-8');
                    }
                    $this->Parameters[$key] = $json;
                    continue;
                } elseif ($key == 'file'){
                    $i = strpos($value, '?');
                    $newValue = substr($value, 0, $i);
                    $newValue = htmlspecialchars($newValue,ENT_QUOTES,'UTF-8');
                    $this->Parameters[$key] = $newValue;
                    continue;
                }
                //$newValue = htmlentities($value,ENT_QUOTES,'UTF-8');
                $newValue = htmlspecialchars($value,ENT_QUOTES,'UTF-8');
                $this->Parameters[$key] = $newValue;
            }
        }

        if (isset($_POST)){
            foreach ($_POST as $key => $value){
                if ($key == 'json') {
                    $json = json_decode($value, true);
                    if (!is_array($json)){
                        continue;
                    }
                    if (!count($json)){
                        continue;
                    }
                    foreach($json as &$jsonValue){
                        $jsonValue  = htmlspecialchars($jsonValue,ENT_QUOTES,'UTF-8');
                    }
                    $this->Parameters[$key] = $json;
                    continue;
                } elseif ($key == 'file'){
                    $i = strpos($value, '?');
                    $newValue = substr($value, 0, $i);
                    $newValue = htmlspecialchars($newValue,ENT_QUOTES,'UTF-8');
                    $this->Parameters[$key] = $newValue;
                    continue;
                }
                //$newValue = htmlentities($value,ENT_QUOTES,'UTF-8');
                $newValue = htmlspecialchars($value,ENT_QUOTES,'UTF-8');
                $this->Parameters[$key] = $newValue;
            }
        }

        $this->takePageUrl();
    }

    /**
     * Parse page url
     *
     * @param   nothing
     * @throws  if $_SERVER['REQUEST_URI'] not defined
     * @return  nothing
     */
    private function takePageUrl(){

        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new ExceptionExt('Varialbe REQUEST_URI not defined');
        }

        $fullUrl = htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');

        $pos = strpos($fullUrl,'?');

        if ($pos){
            $fullUrl = substr($fullUrl, 0, $pos);
        }

        $pos = strpos($fullUrl,INDEX_FILE_NAME);

        if ($pos){
            $fullUrl = substr($fullUrl, 0, $pos);
        }

        $this->pageUrl = $fullUrl;

        return true;
    }

    /**
     * Return page url from $_SERVER['REQUEST_URI']
     *
     * @param   nothing
     * @throws  no throws
     * @return  page url
     */
    public function getPageUrl(){

        return $this->pageUrl;

    }

    /**
     * Override of parent's assign method
     * @param   $ParameterName    parameter ingested through $_GET/$_POST
     * @throws  no throws
     * @return  parameter value
     */
    public function getParameter($ParameterName){
        if (isset($this->Parameters[$ParameterName])) {
            return $this->Parameters[$ParameterName];
        } else {
            return 0;
        }
    }

    /**
     * Return remote ip address
     *
     * @param   nothing
     * @throws  no throws
     * @return  remote ip address
     */
    public function getRemoteIpAddress(){

        return htmlentities($_SERVER['REMOTE_ADDR'],ENT_QUOTES,'UTF-8');

    }

    /**
     * Get $_POST/$_GET parameters, sent from feedback page and start from 'mail-'
     *
     * @param   nothing
     * @throws  no throws
     * @return  array of mail parameters
     */
    public function getFeedbackEmailParameters() {
        
        $result = array();
        
        foreach($this->Parameters as $key => $value){
            if (preg_match("/^mail\-(.+)$/i", $key, $match)){
                $result[$match[1]] = $value;
            }
        }

        return $result;
    }
}
?>