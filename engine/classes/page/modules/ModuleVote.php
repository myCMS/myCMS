<?php
/**
 * Module Vote
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleVote {

    private $MySQL          = null;
    private $SiteStructure  = null;
    private $InputFilter    = null;
    private $Session        = null;

    private $languageId     = 0;
    private $modifiedId     = 0;
    private $voteIds        = array();
    private $voteId         = 0;
    /**
     * Constructor of class ModuleVote
     */
    public function  __construct(MySQL $MySQL, SiteStructure $SiteStructure, InputFilter $InputFilter, Session $Session) {

        $this->MySQL            = $MySQL;
        $this->SiteStructure    = $SiteStructure;
        $this->InputFilter      = $InputFilter;
        $this->Session          = $Session;

        $this->languageId       = $this->SiteStructure->getLanguageId();

        $blocks = $this->SiteStructure->getPageBlocks();

        foreach ($blocks as $block){

            if (preg_match("/vote:(.+)/i", $block, $match)){

                $this->voteIds[] = $match[1];

            }
        }

        if ($this->InputFilter->getParameter("action") === "make_vote" || $this->InputFilter->getParameter("ajax") === "make_vote"){
            $this->makeVote();
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

        /*$voted = $this->Session->getSessionValue('voted', 0);

        if ($voted == 1){

            return VOTE_MODULE_AJAX_TEMPLATE_NAME;

        } else {*/

            return VOTE_MODULE_TEMPLATE_NAME;

        //}
    }

    /**
     * Return admin page template name
     *
     * @param   nothing
     * @throws  no throws
     * @return  admin page template name
     */
    public function getAdminTemplateName() {
        return VOTE_MODULE_ADMIN_TEMPLATE_NAME;
    }

    /**
     * Return full vote data
     *
     * @param   nothing
     * @throws  no throws
     * @return  full vote data
     */
    public function getVote(){

        $result = array();

        if (!count($this->voteIds)){
            return array();
        }

        $ids = implode(",", $this->voteIds);

        $this->MySQL->query("select q.`id`, l.`name`, q.`date`, q.`active`, q.`finished` from votes_questions q, votes_questions_languages l where q.`id`=l.`vote_id` and l.`language_id` = {$this->languageId} and q.`id` in ($ids) and q.`active` = 1 order by q.`id` desc");

        while ($row = $this->MySQL->fetchArray()){

            $result[]   = array('Id'        => $row['id'],
                                'Name'      => $row['name'],
                                'Date'      => $row['date'],
                                'Active'    => $row['active'],
                                'Finished'  => $row['finished'],
                                'Voted'     => $row['finished'],
                                'Answers'   => array(),
                                'GeneralSum'=> 0
            );

        }

        $this->MySQL->freeResult();

        foreach ($result as &$vote){

            $generalSum = 0;
            $generalCount = 0;
            $currentSum = 0;

            $this->MySQL->query("select a.`id`, l.`text`, a.`answers` from votes_answers a, votes_answers_languages l where a.`id`=l.`answer_id` and l.`language_id` = {$this->languageId} and a.`vote_id` = {$vote['Id']} order by a.`vote_order` asc");

            while ($row = $this->MySQL->fetchArray()){

                $generalSum += $row['answers'];
                $generalCount++;

                $vote['Answers'][] = array('Id'        => $row['id'],
                                           'Name'      => $row['text'],
                                           'Answers'   => $row['answers']
                );
            }

            foreach($vote['Answers'] as &$answer){

                $percents = round(($answer['Answers'] / $generalSum) * 100, 0);

                if ($percents + $currentSum > 100){
                    $percents = 100 - $currentSum;
                }

                $answer['Percents'] = $percents;
                $currentSum += $answer['Percents'];
            }

            $vote['GeneralSum'] = $generalSum;
        }

        if (isset($_COOKIE['vote']) && count($result)){
            $voted = explode(";", $_COOKIE['vote']);

            foreach ($result as &$vote) {
                if (in_array($vote['Id'], $voted) || $this->voteId == $vote['Id']){
                    $vote['Voted'] = 1;
                }
            }
        } else if (!empty($this->voteId)){
            foreach ($result as &$vote) {
                if ($this->voteId == $vote['Id']){
                    $vote['Voted'] = 1;
                }
            }
        }

        return $result;
    }

    /**
     * Make vote
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function makeVote(){

        $voteId = (integer)$this->InputFilter->getParameter("voteId");
        $answerId = (integer)$this->InputFilter->getParameter("answerId");
        $voted = array();

        if (empty($voteId)){
            throw new ExceptionExt("Vote Id not set");
        }

        if (empty($answerId)){
            throw new ExceptionExt("Answer Id not set");
        }

        if (isset($_COOKIE['vote'])){
            $voted = explode(";", $_COOKIE['vote']);

            if (in_array($voteId, $voted)){
                //throw new ExceptionExt("You have already voted this");
                return true;
            }
        }

        $this->MySQL->query("UPDATE votes_answers SET `answers` = `answers` + 1 WHERE `id` = $answerId AND `vote_id` = $voteId LIMIT 1");

        $voted[] = $voteId;

        $value = implode(";", $voted);

        setcookie("vote", $value, time()+60*60*24*30, "/");

        $this->voteId = $voteId;

        return true;
    }

    /**
     * Return true if admin panel is used
     *
     * @param  nothing
     * @throws no throws
     * @return true if admin panel is used
     */
    public function isUseAdmin(){

        if ($this->Session->isAuthorized()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return all votes
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function getAllVotes() {

        $result = array();

        $this->MySQL->query("select q.`id`, l.`name`, q.`date`, q.`active`, q.`finished` from votes_questions q, votes_questions_languages l where q.`id`=l.`vote_id` and l.`language_id` = {$this->languageId} order by q.`id` desc");

        while ($row = $this->MySQL->fetchArray()){

            $result[]   = array('Id'        => $row['id'],
                                'Name'      => $row['name'],
                                'Date'      => $row['date'],
                                'Active'    => $row['active'],
                                'Finished'  => $row['finished'],
                                'Answers'   => array(),
                                'GeneralSum'=> 0
            );

        }

        $this->MySQL->freeResult();

        foreach ($result as &$vote){

            $generalSum = 0;
            $generalCount = 0;
            $currentSum = 0;

            $this->MySQL->query("select a.`id`, l.`text`, a.`answers` from votes_answers a, votes_answers_languages l where a.`id`=l.`answer_id` and l.`language_id` = {$this->languageId} and a.`vote_id` = {$vote['Id']} order by a.`vote_order` asc");

            while ($row = $this->MySQL->fetchArray()){

                $generalSum += $row['answers'];
                $generalCount++;

                $vote['Answers'][] = array('Id'        => $row['id'],
                                           'Name'      => $row['text'],
                                           'Answers'   => $row['answers']
                );
            }

            foreach($vote['Answers'] as &$answer){

                $percents = round(($answer['Answers'] / $generalSum) * 100, 0);

                if ($percents + $currentSum > 100){
                    $percents = 100 - $currentSum;
                }

                $answer['Percents'] = $percents;
                $currentSum += $answer['Percents'];
            }

            $vote['GeneralSum'] = $generalSum;
        }

        return $result;
    }

    /**
     * Return all votes for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  all votes for admin panel
     */
    public function getAdminAllVotes(){

        $result = array();

        if (!$this->isUseAdmin()){
            return $result;
        }

        $this->MySQL->query("select q.`id`, l.`name`, q.`date`, q.`active`, q.`finished` from votes_questions q, votes_questions_languages l where q.`id`=l.`vote_id` and l.`language_id` = {$this->languageId} order by q.`id` desc");

        while ($row = $this->MySQL->fetchArray()){

            $result[]   = array('id'        => $row['id'],
                                'name'      => $row['name'],
                                'date'      => $row['date'],
                                'active'    => $row['active'],
                                'finished'  => $row['finished']
            );

        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Return all vote answers by current vote
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  all vote answers by current vote
     */
    public function getAdminAllVoteAnswers(){

        $result = array();

        if (!$this->isUseAdmin()){
            return $result;
        }

        $voteId = (integer)$this->InputFilter->getParameter("voteId");

        $this->MySQL->query("select a.`id`, l.`text`, a.`answers`, a.`active`, a.`vote_order` from votes_answers a, votes_answers_languages l where a.`id`=l.`answer_id` and a.`vote_id` = $voteId and l.`language_id` = {$this->languageId} order by a.`vote_order`");

        while ($row = $this->MySQL->fetchArray()){

            $result[]   = array('id'            => $row['id'],
                                'text'          => $row['text'],
                                'answers'       => $row['answers'],
                                'active'        => $row['active'],
                                'vote_order'    => $row['vote_order']
            );

        }

        $this->MySQL->freeResult();

        return $result;

    }

    /**
     * Add new one of vote's items for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addAdminVote(){

        if (!$this->isUseAdmin()){
            return true;
        }

        $name       = (string)$this->InputFilter->getParameter("name");
        $date       = (string)$this->InputFilter->getParameter("date");
        $active     = (integer)$this->InputFilter->getParameter("active");
        $finished   = (integer)$this->InputFilter->getParameter("finished");

        $this->MySQL->query("INSERT INTO votes_questions (`date`, `active`, `finished`) VALUES('$date', $active, $finished)");

        $voteId = $this->MySQL->insertedId();

        $this->MySQL->query("INSERT INTO votes_questions_languages (`vote_id`, `name`, `language_id`) VALUES($voteId, '$name', {$this->languageId})");

        return true;

    }

    /**
     * Add new one of vote's answers for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addAdminAnswer(){

        if (!$this->isUseAdmin()){
            return true;
        }

        $voteId     = (integer)$this->InputFilter->getParameter("voteId");
        $text       = (string)$this->InputFilter->getParameter("text");
        $answers    = (string)$this->InputFilter->getParameter("answers");
        $active     = (integer)$this->InputFilter->getParameter("active");
        $voteOrder  = (integer)$this->InputFilter->getParameter("voteOrder");

        $this->MySQL->query("INSERT INTO votes_answers (`vote_id`, `answers`, `active`, `vote_order`) VALUES( $voteId, '$answers', $active, $voteOrder)");

        $answerId = $this->MySQL->insertedId();

        $this->MySQL->query("INSERT INTO votes_answers_languages (`answer_id`, `language_id`, `text`) VALUES( $answerId, {$this->languageId}, '$text')");

        return true;

    }

    /**
     * Add new one of vote's items for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeAdminVote(){

        if (!$this->isUseAdmin()){
            return true;
        }

        $voteId = (integer)$this->InputFilter->getParameter("voteId");

        $this->MySQL->query("DELETE FROM votes_questions where `id` = $voteId");

        return true;

    }

    /**
     * Add new one of vote's answers for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeAdminAnswer(){

        if (!$this->isUseAdmin()){
            return true;
        }

        $voteId = (integer)$this->InputFilter->getParameter("voteId");

        $this->MySQL->query("DELETE FROM votes_answers where `id` = $voteId");

        return true;

    }

    /**
     * Save one of vote's items for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function saveAdminVoteItem(){

        if (!$this->isUseAdmin()){
            return true;
        }

        $voteId     = (integer)$this->InputFilter->getParameter("voteId");
        $itemName   = (string)$this->InputFilter->getParameter("itemName");
        $itemValue  = (string)$this->InputFilter->getParameter("itemValue");

        if (empty($voteId)){
            return true;
        }

        switch($itemName){
            case 'name':
                $update = "`name` = '$itemValue'";
                $table  = 2;
                break;
            case 'date':
                $update = "`date` = '$itemValue'";
                $table  = 1;
                break;
            case 'active':
                $update = "`active` = $itemValue";
                $table  = 1;
                break;
            case 'finished':
                $update = "`finished` = $itemValue";
                $table  = 1;
                break;
            default:
                return true;
        }

        switch ($table){
            case 1:
                $this->MySQL->query("UPDATE votes_questions SET $update where `id` = $voteId");
                break;
            case 2:
                $this->MySQL->query("UPDATE votes_questions_languages SET $update where `vote_id` = $voteId and `language_id` = {$this->languageId}");
                break;
        }

        return true;

    }

    /**
     * Save one of vote's answers for admin panel
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function saveAdminAnswerItem(){

        if (!$this->isUseAdmin()){
            return true;
        }

        $voteId     = (integer)$this->InputFilter->getParameter("voteId");
        $itemName   = (string)$this->InputFilter->getParameter("itemName");
        $itemValue  = (string)$this->InputFilter->getParameter("itemValue");

        if (empty($voteId)){
            return true;
        }

        switch($itemName){
            case 'text':
                $update = "`text` = '$itemValue'";
                $table  = 2;
                break;
            case 'answers':
                $update = "`answers` = '$itemValue'";
                $table  = 1;
                break;
            case 'active':
                $update = "`active` = $itemValue";
                $table  = 1;
                break;
            case 'voteOrder':
                $update = "`vote_order` = $itemValue";
                $table  = 1;
                break;
            default:
                return true;
        }

        switch ($table){
            case 1:
                $this->MySQL->query("UPDATE votes_answers SET $update where `id` = $voteId");
                break;
            case 2:
                $this->MySQL->query("UPDATE votes_answers_languages SET $update where `answer_id` = $voteId and `language_id` = {$this->languageId}");
                break;
        }

        return true;

    }

    /**
     * Add new element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function addElement(){

        $i      = 0;
        $value  = '';
        $text   = '';
        $values = '';
        $active = 0;
        $finished = 0;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $newData = $this->InputFilter->getParameter("json");

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        if (!empty($newData["VoteName"])){
            $voteName = $newData["VoteName"];
        } else {
            throw new ExceptionExt("Vote Name not setup");
        }
        $active   = $newData["Active"];
        $finished = $newData["Finished"];

        $this->MySQL->query("INSERT INTO `votes_questions` (`active`, `finished`, `date`) values($active, $finished, NOW())");

        $id = $this->MySQL->insertedId();

        if (!empty($id)){
            $this->modifiedId = $id;
        } else {
            $this->modifiedId = 0;
            throw new ExceptionExt("Vote not inserted");
        }

        $this->MySQL->query("INSERT INTO `votes_questions_languages` (`vote_id`, `language_id`, `name`) values($id, {$this->languageId}, '$voteName')");

		if(!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Vote language not inserted");
		}

        for ($i=1;$i<=10;$i++){

            if (!empty($newData["Question$i"])){

                $question = $newData["Question$i"];

                if (!empty($newData["Answer$i"])){
                    $answer = $newData["Answer$i"];
                } else {
                    $answer = 0;
                }

                $this->MySQL->query("INSERT INTO `votes_answers` (`vote_id`, `answers`, `active`, `vote_order`) values($id, $answer, 1, $i)");

                $answerId = $this->MySQL->insertedId();

                $this->MySQL->query("INSERT INTO `votes_answers_languages` (`answer_id`, `language_id`, `text`) values($answerId, {$this->languageId}, '$question')");

                if(!$this->MySQL->affectedRows()){
                    throw new ExceptionExt("Vote answer $i not inserted");
                }
            }
        }

        return true;
    }

    /**
     * Edit element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function editElement(){

        $i      = 0;
        $value  = '';
        $text   = '';
        $values = '';
        $active = 0;
        $finished = 0;

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $newData = $this->InputFilter->getParameter("json");
        $id = (integer)$this->InputFilter->getParameter("id");
        $answerIds = array();

        if (!is_array($newData)){
            throw new ExceptionExt("New data not set");
        }

        if (empty($id)){
            throw new ExceptionExt("Id not set");
        }

        $this->modifiedId = $id;

        if (!empty($newData["VoteName"])){
            $voteName = $newData["VoteName"];
        } else {
            throw new ExceptionExt("Vote Name not setup");
        }

        $active   = $newData["Active"];
        $finished = $newData["Finished"];

        $this->MySQL->query("UPDATE `votes_questions` SET `active` = $active, `finished` = $finished WHERE `id` = $id LIMIT 1");

        $this->MySQL->query("UPDATE `votes_questions_languages` SET `name` = '$voteName' WHERE `vote_id` = $id LIMIT 1");

        $this->MySQL->query("SELECT a.`id`, a.`answers` as `Answer`, l.`text` as `Question` FROM `votes_answers` a, `votes_answers_languages` l WHERE a.`id` = l.`answer_id` AND a.`vote_id` = $id AND l.`language_id` = {$this->languageId} ORDER BY a.`vote_order`");

        while ($row = $this->MySQL->fetchArray()){
            $answerIds[] = $row['id'];
        }

        for ($i=1;$i<=10;$i++){

            if (!empty($newData["Question$i"])){

                $question = $newData["Question$i"];
                if (!empty($newData["Answer$i"])){
                    $answer = $newData["Answer$i"];
                } else {
                    $answer = 0;
                }

                if ($i <= count($answerIds)){

                    $answerId = $answerIds[$i-1];

                    $this->MySQL->query("UPDATE `votes_answers` SET `answers` = '$answer' WHERE `id` = $answerId LIMIT 1");

                    $this->MySQL->query("UPDATE `votes_answers_languages` SET `text` = '$question' WHERE `answer_id` = $answerId LIMIT 1");

                } else {

                    $this->MySQL->query("INSERT INTO `votes_answers` (`vote_id`, `answers`, `active`, `vote_order`) values($id, $answer, 1, $i)");

                    $answerId = $this->MySQL->insertedId();

                    $this->MySQL->query("INSERT INTO `votes_answers_languages` (`answer_id`, `language_id`, `text`) values($answerId, {$this->languageId}, '$question')");

                    if(!$this->MySQL->affectedRows()){
                        throw new ExceptionExt("Vote answer $i not inserted");
                    }
                }
            } else {

                if (isset($answerIds[$i-1])){

                    $answerId = $answerIds[$i-1];

                    $this->MySQL->query("DELETE FROM `votes_answers` WHERE `id` = $answerId LIMIT 1");
                }
            }
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
    public function inverseActivityElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafIds = $this->InputFilter->getParameter("json");

        if (!is_array($nafIds)){
            throw new ExceptionExt("Naf Ids not set");
        }

        $inStatement = implode(",", $nafIds);

        $this->MySQL->query("UPDATE `votes_questions` SET `active` = NOT `active` WHERE `id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){

            throw new ExceptionExt("Activity not inversed");

        }

        return true;
    }

    /**
     * Remove Naf element
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  nothing
     */
    public function removeElement(){

        if (!$this->isUseAdmin()){
            throw new ExceptionExt("User is not admin");
        }

        $nafIds = $this->InputFilter->getParameter("json");

        if (!is_array($nafIds)){
            throw new ExceptionExt("Naf Ids not set");
        }

        $inStatement = implode(",", $nafIds);

        $this->MySQL->query("DELETE FROM `votes_questions` WHERE `id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Elements not deleted");
        }

        $this->MySQL->query("DELETE FROM `votes_questions_languages` WHERE `vote_id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Elements not deleted");
        }

        $this->MySQL->query("DELETE FROM `votes_answers` WHERE `vote_id` IN ($inStatement)");

        if(!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Elements not deleted");
        }

        return true;
    }

    /**
     * Return Last modified data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function getAdminLastModifiedData() {

        $result = array();

        $id = $this->modifiedId;

        if (empty($id)){
            throw new ExceptionExt("Last modified id not setup");
        }

        $this->MySQL->query("select q.`id`, l.`name`, q.`date`, q.`active`, q.`finished` from votes_questions q, votes_questions_languages l where q.`id`=l.`vote_id` and l.`language_id` = {$this->languageId} and q.`id` = $id order by q.`id` desc limit 1");

        while ($row = $this->MySQL->fetchArray()){

            $result[]   = array('Id'        => $row['id'],
                                'Name'      => $row['name'],
                                'Date'      => $row['date'],
                                'Active'    => $row['active'],
                                'Finished'  => $row['finished'],
                                'Answers'   => array()
            );

        }

        $this->MySQL->freeResult();

        foreach ($result as &$vote){

            $this->MySQL->query("select a.`id`, l.`text`, a.`answers` from votes_answers a, votes_answers_languages l where a.`id`=l.`answer_id` and l.`language_id` = {$this->languageId} and a.`vote_id` = {$vote['Id']} order by a.`vote_order` asc");

            while ($row = $this->MySQL->fetchArray()){

                $vote['Answers'][] = array('Id'        => $row['id'],
                                           'Name'      => $row['text'],
                                           'Answers'   => $row['answers']
                );
            }
        }

        return $result;
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

        $id  = (integer)$this->InputFilter->getParameter("id");
        $i   = 0;

        if (empty($id)){
            throw new ExceptionExt("Vote Id not set");
        }

        $result['Id'] = $id;

        $page = $this->SiteStructure->getCurrentPageUrl();

        if (empty($ADMIN_WINDOW[$page])){
            return $result;
        }

        $result['Lines'] = $ADMIN_WINDOW[$page];

        $this->MySQL->query("SELECT q.`id`, l.`name` as `VoteName`, q.`active` as `Active`, q.`finished` as `Finished` FROM `votes_questions` q, votes_questions_languages l WHERE q.`id` = l.`vote_id` AND q.`id` = $id AND l.`language_id` = {$this->languageId} LIMIT 1");

        while ($row = $this->MySQL->fetchArray()){

            foreach ($result['Lines'] as &$field){

                if (empty($row[$field['Name']])){
                    $field['Value'] = '';
                } else {
                    $field['Value'] = $row[$field['Name']];
                }
            }
        }

        $this->MySQL->freeResult();

        $this->MySQL->query("SELECT a.`id`, a.`answers` as `Answer`, l.`text` as `Question` FROM `votes_answers` a, `votes_answers_languages` l WHERE a.`id` = l.`answer_id` AND a.`vote_id` = $id AND l.`language_id` = {$this->languageId} ORDER BY a.`vote_order`");

        while ($row = $this->MySQL->fetchArray()){

            $i++;

            foreach ($result['Lines'] as &$field){
                if ($field['Name'] == "Question$i"){
                    $field['Value'] = $row['Question'];
                } else if ($field['Name'] == "Answer$i") {
                    $field['Value'] = $row['Answer'];
                }
            }
        }

        $this->MySQL->freeResult();

        return $result;
    }
}
?>