<?php
/**
 * Used for working with sessions
 *
 * @package     Engine
 * @subpackage  Engine
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class Session {

    private $authorized = 0;
    private $login      = '';
    private $privileges = array();

    private $MySQL       = null;
    private $InputFilter = null;
    private $Smarty      = null;

    /**
     * Constructor of class Session
     *
     * @param   $MySQL
     * @param   $InputFilter
     * @throws  no throws
     * @return  nothing
     */
    public function  __construct(MySQL $MySQL, InputFilter $InputFilter, Smarty $Smarty) {

        $this->MySQL        = $MySQL;
        $this->InputFilter  = $InputFilter;
        $this->Smarty       = $Smarty;

        if (!AUTHENTICATION_USE_ONLY_ANONIMOUS_ACCESS){
            $this->initSession();
        }
    }

    /**
     * Initiat session and check input data
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function initSession(){

        if ($this->InputFilter->getParameter("ajax")."" == "authentication" && defined("AUTHENTICATION_USE_VERSION_2")){

            if ($this->InputFilter->getParameter("remember")."" != "true"){

                $sessionName = session_name();
                unset($_COOKIE["$sessionName"]);
                session_set_cookie_params(0);
            }
        }

        session_start();

        $this->setPrivileges();

        $action = $this->InputFilter->getParameter("action")."";

        switch($action){
            case 'registration':
                $this->registration();
                break;
            case 'authentication':
                $this->authentication();
                break;
            case 'logout':
                session_destroy();
                break;
        }
    }

    /**
     * Gather all user's privileges
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function setPrivileges() {

        if (defined("AUTHENTICATION_USE_VERSION_2") && AUTHENTICATION_USE_VERSION_2 == 1){

            $this->privileges = array(); //@todo cleanup

            if (!$this->getSessionValue('authorized', 0)){
                return true;
            }

            $id = $this->getSessionValue('id', 0);

            if (empty($id)){
                throw new ExceptionExt("User id not defined");
            }

            $this->MySQL->query("select p.`name` from `privileges` p, `user_privilages` u where p.`id` = u.`privilege_id` and u.`user_id` = $id");

            while ($row = $this->MySQL->fetchArray()){

                $this->privileges[] = $row['name'];

            }
        }
    }

    /**
     * Send confirmation email
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function confirmationEmail($email, $login, $activationCode) {

        if (defined("AUTHENTICATION_USE_VERSION_2") && REGISTRATION_SEND_CONFIRMATION_EMAIL){

            $headers = '';
            //$headers  = 'MIME-Version: 1.0' . "\r\n";
            //$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

            $link = REGISTRATION_CONFIRMATION_FULL_SITE_LINK . "?login=$login&activation_code=$activationCode";

            //$this->Smarty->assign('Link',  $link);
            //$this->Smarty->assign('Login', $login);
            //$message = $this->Smarty->fetch( REGISTRATION_CONFIRMATION_COVER_TEMPLATE_NAME );

            $message = "You received email with registration confirmation. Please fallow this $link to activate your account";

            if ( !mail($email, "Activation code", $message, $headers) ){
                throw new ExceptionExt("Registration confirmation email not sent");
            }
        }

        return true;

    }

    /**
     * Generate new password
     *
     * @param   nothing
     * @throws  no throws
     * @return  new password
     */
    private function generatePassword() {
        /*
        48 - 0
        57 - 9
        97 - a
        122 - я
        65 - A
        90 - Z
        */

        $password = '';

        for($i=0;$i<8;$i++){

            switch( rand(1, 3) ){
                case 1:
                    $password .= chr(rand(48,57));
                    break;
                case 2:
                    $password .= chr(rand(97,122));
                    break;
                case 3:
                    $password .= chr(rand(65,90));
                    break;
            }
        }

        return $password;
    }

    /**
     * Update user's IP
     *
     * @param   $id user id
     * @throws  no throws
     * @return  nothing
     */
    private function updateIP($id) {

        if (empty($id)){
            throw new ExceptionExt("User Id is empty");
        }

        $ip = $this->InputFilter->getRemoteIpAddress();

        $this->MySQL->query("update users set `ip` = '$ip' where `id` = $id");

        return true;
    }

    /**
     * Check user's IP
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    private function checkIP() {

        $ip = $this->InputFilter->getRemoteIpAddress();

        $this->MySQL->query("select `id` from users where `ban` = 1 and `ip` = '$ip'");

        if ($this->MySQL->countRows()){
            throw new ExceptionExt("Your IP address is banned.");
        }

        return true;
    }

    /**
     * Activation user's account
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function activation() {

        if (defined("AUTHENTICATION_USE_VERSION_2") && REGISTRATION_SEND_CONFIRMATION_EMAIL){

            $login = $this->InputFilter->getParameter("login")."";
            $activationCode = $this->InputFilter->getParameter("activation_code")."";

            if (empty($login)){
                throw new ExceptionExt("Login is empty");
            }

            if (empty($activationCode)){
                throw new ExceptionExt("Activation code is empty");
            }

            $this->MySQL->query("select `id`, `login`, `name`, `active` from users where `login` = '$login' and `activation_code` = '$activationCode'");

            if (!$this->MySQL->countRows()){
                throw new ExceptionExt("Login or activation code is incorrect");
            }

            $this->MySQL->query("update users set `active` = 1, `activation_code` = '' where `login` = '$login'");

            if (!$this->MySQL->affectedRows()){
                throw new ExceptionExt("User not activated");
            }

        }

        return true;

    }

    /**
     * Edit user details
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function editUserDetails() {

        $email      = $this->InputFilter->getParameter("email");
        $passwd     = $this->InputFilter->getParameter("password");
        $deleteUser = $this->InputFilter->getParameter("deleteMe")."";
        $subscribe  = $this->InputFilter->getParameter("subscribe")."";

        $id = $this->getSessionValue('id', 0);

        $set = '';

        if ($subscribe == "true"){
            $set .= "`subscribe` = 1";
        } else {
            $set .= "`subscribe` = 0";
        }

        if ($deleteUser == "true"){
            $set .= ", `active` = 0";
        }

        if (!empty($email)){
            $set .= ", `login` = '$email'";

            $this->MySQL->query("select `id` from users where `login` = '$email' and `id` != $id");

            if ($this->MySQL->countRows()){
                throw new ExceptionExt("This email already exists");
            }
        }

        if (!empty($passwd)){
            $set .= ", `password` = '".md5($passwd)."'";
        }

        $this->MySQL->query("update users set $set where `id` = $id limit 1");

        if (!$this->MySQL->affectedRows()){
            throw new ExceptionExt("User details not changed");
        }

        if ($deleteUser == "true"){
            session_destroy();
        }

        return true;
    }

    /**
     * Password recover
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function passwordRecover() {

        $email = $this->InputFilter->getParameter("email");

        if (empty($email)){
            throw new ExceptionExt("Email is empty");
        }

        $this->MySQL->query("select `id`, `login` from users where `login` = '$email'");

        if (!$this->MySQL->countRows()){
            throw new ExceptionExt("User with this email not exists");
        }

        $password = $this->generatePassword();

        $this->MySQL->query("update users set `password` = '".md5($password)."' where `login` = '$email' limit 1");

        if (!$this->MySQL->affectedRows()){
            throw new ExceptionExt("User not updated");
        }

        $headers = '';
        $message = "Your new password is $password";

        if ( !mail($email, "Password recovery", $message, $headers) ){
            throw new ExceptionExt("Password recovery email not sent");
        }

        return true;

    }

    /**
     * Register new user
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function registration(){

        $login      = $this->InputFilter->getParameter("login");
        $passwd     = $this->InputFilter->getParameter("password");
        $name       = $this->InputFilter->getParameter("name");
        $subscribe  = $this->InputFilter->getParameter("subscribe")."";

        ($subscribe == 'true')? $subscribe  = 1:$subscribe  = 0;

        if (empty($login) || empty($passwd)){
            throw new ExceptionExt("Login or Password is empty");
        }

        if (defined("AUTHENTICATION_USE_VERSION_2") && AUTHENTICATION_USE_VERSION_2){
            $this->checkIP();
        }

        $this->MySQL->query("select `id`, `login`, `name` from users where `login` = '$login' or `name` = '$name'");

        if ($this->MySQL->countRows()){
            $row = $this->MySQL->fetchArray();
            if ($row['login'] == $login){
                throw new ExceptionExt("User with login $login already exist");
            } else if ($row['name'] == $name){
                throw new ExceptionExt("User with name $name already exist");
            } else {
                throw new ExceptionExt("Error while creating user");
            }
        }

        $passwd = md5($passwd);
        $date   = date("Y-m-d");

        if (defined("AUTHENTICATION_USE_VERSION_2") && REGISTRATION_SEND_CONFIRMATION_EMAIL){
            $active = 0;
            $activationCode = md5(time());
        } else {
            $active = 1;
            $activationCode = '';
        }

        $this->MySQL->query("insert into users (`login`, `password`, `reg_date`, `active`, `name`, `ban`, `ban_date`, `subscribe`, `activation_code`) values ('$login', '$passwd', NOW(), $active, '$name', 0, '0000-00-00', $subscribe, '$activationCode')");

        if (!$this->MySQL->affectedRows()){
            throw new ExceptionExt("User not created");
        }

        $this->confirmationEmail($login, $login, $activationCode);

        return true;
    }

    /**
     * Check authentication for user
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function authentication(){

        $login  = $this->InputFilter->getParameter("login");
        $passwd = $this->InputFilter->getParameter("password");
        $remember = $this->InputFilter->getParameter("remember")."";

        ($remember == "on")? $remember = 1 : $remember = 0;

        if (empty($login) || empty($passwd)){
            $this->setSessionValue('authorized',0);
            throw new ExceptionExt("Login or Password is empty");
        }

        $passwd = md5($passwd);

        if (defined("AUTHENTICATION_USE_VERSION_2") && AUTHENTICATION_USE_VERSION_2 == 1){

            $this->checkIP();

            $this->MySQL->query("select `id`, `login`, `name`, `ban`, `ban_date`, `subscribe` from users where `login` = '$login' and `password` = '$passwd' and `active` = 1");

            if ($this->MySQL->countRows()){
                $row = $this->MySQL->fetchArray();
                if ($row['ban'] == 0){
                    $this->setSessionValue('authorized',    1);
                    $this->setSessionValue('id',            $row['id']);
                    $this->setSessionValue('login',         $row['login']);
                    $this->setSessionValue('name',          $row['name']);
                    $this->setSessionValue('ban',           $row['ban']);
                    $this->setSessionValue('ban_date',      $row['ban_date']);
                    $this->setSessionValue('subscribe',     $row['subscribe']);
                    $this->delSessionValue('privileges');
                    $this->updateIP($row['id']);
                    $this->setPrivileges();
                    $this->setSessionValue('privileges',    $this->getPrivileges());
                    $this->authorized   = 1;
                    $this->login        = $login;
                    return true;
                } else {
                    $this->setSessionValue('authorized',    0);
                    $this->setSessionValue('id',            0);
                    $this->setSessionValue('login',         0);
                    $this->setSessionValue('name',          0);
                    $this->setSessionValue('ban',           0);
                    $this->setSessionValue('ban_date',      0);
                    $this->setSessionValue('privileges',    0);
                    throw new ExceptionExt("User is banned.");
                }
            } else {
                $this->setSessionValue('authorized',    0);
                $this->setSessionValue('id',            0);
                $this->setSessionValue('login',         0);
                $this->setSessionValue('name',          0);
                $this->setSessionValue('ban',           0);
                $this->setSessionValue('ban_date',      0);
                $this->setSessionValue('privileges',    0);
                throw new ExceptionExt("Login or Password is incorrect");
            }

        } else {

            $this->MySQL->query("select `id` from users where `login` = '$login' and `password` = '$passwd' and `active` = 1");

            if ($this->MySQL->countRows()){
                $row = $this->MySQL->fetchArray();
                $this->setSessionValue('authorized',1);
                $this->setSessionValue('id',$row['id']);
                $this->setSessionValue('login',"$login");
                $this->authorized = 1;
                $this->login = $login;
                return true;
            } else {
                $this->setSessionValue('authorized',0);
                throw new ExceptionExt("Login or Password is incorrect");
            }
        }
    }

    /**
     * Return all user's privileges
     *
     * @param   nothing
     * @throws  no throws
     * @return  all user's privileges
     */
    public function getPrivileges() {

        return $this->privileges;

    }

    /**
     * Save privilege for user
     *
     * @param   $userId user id
     * @param   $privilegeName privilege name
     * @throws  if privilege not exists
     * @return  nothing
     */
    public function saveUserPrivilege($userId, $privilegeName) {

        $id = 0;

        $userId = (integer)$userId;

        $this->MySQL->query("select p.`id` as `priv_id`, up.`id` as `priv_set` from `privileges` p left join `user_privilages` up on up.`privilege_id` = p.`id` and up.`user_id` = $userId where `name` = '$privilegeName' limit 1");

        while($row = $this->MySQL->fetchArray()){

            $id = $row['priv_id'];

            if ($row['priv_set'] !== null){
                return true; //privilege already set
            }
        }

        if (empty($id)){
            throw new ExceptionExt("Privilege '$privilegeName' not exists");
        }

        $currentUserId = $this->getSessionValue('id', 0);

        if (empty($currentUserId)){
            throw new ExceptionExt("Current user id not defined");
        }

        $this->MySQL->query("insert into user_privilages (`user_id`, `privilege_id`, `granted_by`, `granted_at`) values($userId, $id, $currentUserId, NOW())");

        if (!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Privilege '$privilegeName' not added");
        }

        return true;
    }

    /**
     * Delete privilege for user
     *
     * @param   $userId user id
     * @param   $privilegeName privilege name
     * @throws  if privilege not exists
     * @return  nothing
     */
    public function deleteUserPrivilege($userId, $privilegeName) {

        $id = 0;

        $userId = (integer)$userId;

        $this->MySQL->query("select p.`id` as `priv_id`, up.`id` as `priv_set` from `privileges` p left join `user_privilages` up on up.`privilege_id` = p.`id` and up.`user_id` = $userId where `name` = '$privilegeName' limit 1");

        while($row = $this->MySQL->fetchArray()){

            $id = $row['priv_id'];

            if ($row['priv_set'] === null){
                return true; //privilege not set
            }
        }

        if (empty($id)){
            throw new ExceptionExt("Privilege '$privilegeName' not exists");
        }

        $this->MySQL->query("delete from user_privilages where `user_id` = $userId and `privilege_id` = $id");

        if (!$this->MySQL->affectedRows()){
            throw new ExceptionExt("Privilege '$privilegeName' not deleted");
        }

        return true;

    }

    /**
     * Makes user logout
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function logout() {
        $this->setSessionValue('authorized', 0);
        session_destroy();
        return true;
    }

    /**
     * Return true if user is authorized
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if user is authorized
     */
    public function isAuthorized(){

        return $this->getSessionValue('authorized', 0);

    }

    /**
     * Return true if user is admin
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if user is admin
     */
    public function isAdmin(){

        return $this->isAuthorized();

    }

    /**
     * Return true if user details are correct
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if user details are correct
     */
    public function checkUserDetails() {

        $login  = $this->InputFilter->getParameter("login");
        $passwd = $this->InputFilter->getParameter("password");

        if (empty($login) || empty($passwd)){
            return false;
        }

        $passwd = md5($passwd);

        $this->MySQL->query("select `id` from users where `login` = '$login' and `password` = '$passwd' and `active` = 1");

        if ($this->MySQL->countRows()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if user details are correct
     *
     * @param   nothing
     * @throws  no throws
     * @return  true if user details are correct
     */
    public function checkRegistrationDetails() {

        $login  = $this->InputFilter->getParameter("login");

        if (empty($login)){
            return false;
        }

        $this->MySQL->query("select `id` from users where `login` = '$login' and `active` = 1");

        if ($this->MySQL->countRows()){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Return all user's details
     *
     * @param   nothing
     * @throws  no throws
     * @return  all user's details
     */
    public function getUserDetails(){

        return array(   'Login'     => $this->getSessionValue('login',      ''),
                        'Name'      => $this->getSessionValue('name',       ''),
                        'Ban'       => $this->getSessionValue('ban',        ''),
                        'BanDate'   => $this->getSessionValue('ban_date',   ''),
                        'Privileges'=> $this->getSessionValue('privileges', ''),
                        'Subscribe' => $this->getSessionValue('subscribe',  '')
                    );
    }

    /**
     * Set session value
     *
     * @param   $name name of variable which will be saved
     * @param   $value value which will be saved
     * @throws  no throws
     * @return  true
     */
    public function setSessionValue($name, $value){

        $_SESSION["$name"] = $value;

        return true;
    }

    /**
     * Get session value
     *
     * @param   $name name of variable
     * @throws  no throws
     * @return  value of variable
     */
    public function getSessionValue($name, $defaultValue = ''){

        if (isset($_SESSION["$name"])){
            return $_SESSION["$name"];
        } else {
            return $defaultValue;
        }
    }

    /**
     * Delete session value
     *
     * @param   $name name of variable
     * @throws  no throws
     * @return  true
     */
    public function delSessionValue($name){

        if (isset($_SESSION["$name"])){
            unset($_SESSION["$name"]);
        }
        return true;
    }
}
?>
