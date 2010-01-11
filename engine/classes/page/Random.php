<?php
/**
 * Used for access to othen then Structure database tables
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class Random {

    private $MySQL        = null;
    private $SiteStructure  = null;

    /**
     * Constructor of class Content
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure) {

        $this->MySQL         = $MySQL;
        $this->SiteStructure = $SiteStructure;

    }

    /**
     * Return true if random questions used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if random questions used
     */
    public function isUsedRandomQuestions() {

        return GENERATE_RANDOM_QUESTION;

    }

    /**
     * Return true if random picture used
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if random picture used
     */
    public function isUsedRandomPicture() {

        return GENERATE_RANDOM_PICTURE;
        
    }

    /**
     * Return random question block template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  random question block template name
     */
    public function getRandomQuestionTemplateName() {

        return RANDOM_QUESTION_TEMPLATE_NAME;

    }

    /**
     * Return random picture block template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  random picture block template name
     */
    public function getRandomPictureTemplateName() {

        return RANDOM_PICTURE_TEMPLATE_NAME;
        
    }

    /**
     * Return random question
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  random question
     */
    public function getRandomQuestion(){

        if (!GENERATE_RANDOM_QUESTION){
            return "";
        }

        $typeArray = array();
        $result    = array();
        $i         = 1;

        $this->MySQL->query("SELECT
                                DISTINCT `type`
                             FROM
                                random_questions
                            ");
        while ($row = $this->MySQL->fetchArray()){

            $typeArray[] = $row['type'];
        }
        $this->MySQL->freeResult();

        $languageId = $this->SiteStructure->getLanguageId();

        foreach ($typeArray as $value) {
            $this->MySQL->query("SELECT
                                    `text`
                                 FROM
                                    random_questions   
                                 WHERE
                                    `type` = $value
                                    AND
                                    `language_id` = $languageId
                                 ORDER BY
                                    RAND()
                                 LIMIT 1
                                ");
            $row = $this->MySQL->fetchArray();
            $result[$value] = $row['text'];
        }
        $this->MySQL->freeResult();

        /*$this->MySQL->query("select `text` from random_questions where `active` = 1 limit 30");
        while ($row = $this->MySQL->fetchArray()){

            $result[] = $row['text'];
            $i++;

        }

        $this->MySQL->freeResult();

        if ($i == 0){
            return "";
        }

        $rand = rand(0,$i-1);

        return $result[$rand];*/
        return $result;
    }

    /**
     * Return random picture
     * CAn be used glob or scandir
     *
     * @param   nothing
     * @throws  no throws
     * @return  random picture url
     */
    public function getRandomPicture(){

        if (!GENERATE_RANDOM_PICTURE){
            return "";
        }

        if (!is_dir(RANDOM_PICTURE_FOLDER)){
            return "";
        }

        $result = array();
        $i = 0;

        $d = dir(RANDOM_PICTURE_FOLDER);
        while(false !== ($file = $d->read())){

            if ($file == '.' || $file == '..' || $file == '.svn'){
                continue;
            }

            $result[] = $file;

        }
        $d->close();

        $i = count($result);

        if ($i == 0){
            return "";
        }

        $rand = rand(0, $i-1);

        return RANDOM_PICTURE_URL.$result[$rand];

    }
}
?>
