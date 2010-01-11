<?php

/**
 * Module Feedback
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleFeedback {

    protected   $SiteStructure          = null;
    protected   $InputFilter            = null;
    protected   $Smarty                 = null;

    protected   $moduleName             = '';
    protected   $mailToName             = '';
    protected   $mailToEmail            = '';
    protected   $mailFromName           = '';
    protected   $mailFromEmail          = '';
    protected   $mailSubject            = '';
    protected   $mailReplyTo            = '';
    protected   $mailCC                 = '';
    protected   $mailBCC                = '';
    protected   $coverTemplate          = '';
    protected   $templateNames          = array();

    protected   $sendStatus             = 0;

    /**
     * Constructor of class Feedback
     */
    public function  __construct(SiteStructure $SiteStructure, InputFilter $InputFilter, Smarty $Smarty) {
        $this->SiteStructure        = $SiteStructure;
        $this->InputFilter          = $InputFilter;
        $this->Smarty               = $Smarty;

        $this->getConfigData();

    }

    /**
     * Used for passing references to needed classes into this class
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    final public function setClassesHandlers(SiteStructure $SiteStructure, InputFilter $InputFilter, Smarty $Smarty){

        $this->SiteStructure        = $SiteStructure;
        $this->InputFilter          = $InputFilter;
        $this->Smarty               = $Smarty;
		
		$this->getConfigData();

        $this->getConfigEmail();

    }

    /**
     * Used for read config data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function getConfigData() {
	
        global $FEEDBACK_CONFIG;

        $language = strtoupper($this->SiteStructure->getLanguageName());

        $blocks = $this->SiteStructure->getPageBlocks();

        foreach ($blocks as $block){

            if (preg_match("/feedback:(.+)/i", $block, $match)){
			
                foreach($FEEDBACK_CONFIG as $config){                
                    
                    if ($config['MODULE_NAME'] === $match[1]){
					
                        $this->templateNames[$match[1]] = $config["MODULE_TEMPLATE_NAME_$language"];

                    }
                }
            }
        }
    }

    /**
     * Used for read config data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function getConfigEmail() {

        global $FEEDBACK_CONFIG;

        $language = strtoupper($this->SiteStructure->getLanguageName());

        $name = $this->InputFilter->getParameter("module_type");

        foreach($FEEDBACK_CONFIG as $config){

            if ($config['MODULE_NAME'] === $name){

                if (isset($config["MODULE_TEMPLATE_NAME_$language"])){

                    $this->moduleName       = $config["MODULE_NAME"];

                    $this->mailToName       = $config["MODULE_MAIL_TO_NAME"];
                    $this->mailToEmail      = $config["MODULE_MAIL_TO_EMAIL"];
                    $this->mailFromName     = $config["MODULE_MAIL_FROM_NAME"];
                    $this->mailFromEmail    = $config["MODULE_MAIL_FROM_EMAIL"];
                    $this->mailSubject      = $config["MODULE_MAIL_SUBJECT"];
                    $this->mailReplyTo      = $config["MODULE_MAIL_REPLY_TO"];
                    $this->mailCC           = $config["MODULE_MAIL_CC"];
                    $this->mailBCC          = $config["MODULE_MAIL_BCC"];
                    $this->coverTemplate    = $config["MODULE_COVER_TEMPLATE_NAME"];

                    return true;
                }
            }
        }
    }

    /**
     * Send email
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    protected function sendFeedback(){

        if ( $this->InputFilter->getParameter("action") !== "sendfeedback" ){

            return true;
            
        }

        $this->getConfigEmail();

        $name       = $this->InputFilter->getParameter("name");
        $comments   = $this->InputFilter->getParameter("message");
        $email      = $this->InputFilter->getParameter("email");

        $type       = $this->InputFilter->getParameter("module_type");

        $message = "You have received feedback from $name (email $email) with comments $comments";

        if ( mail($this->mailToEmail, "Feedback from Mart($type)", $message) ){
            $this->sendStatus = 1;
        } else {
            $this->sendStatus = 2;
        }
    }

    /**
     * Return true if email was send
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if email was send
     */
    public function isEmailFeedbackSend(){

        if ( $this->InputFilter->getParameter("action") === "sendfeedback" ){
            $this->sendFeedback();
        }

        if (!empty($this->sendStatus)){
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Return true if email was send successfully
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if email was send successfully
     */
    public function isEmailSendSuccessfully(){

        if ($this->sendStatus == 1){
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Return template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  template name
     */
    public function getTemplateName(){

        return $this->templateNames;

    }
}
?>
