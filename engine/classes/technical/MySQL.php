<?php
/**
 * Used for MySQL database work
 *
 * @package     Engine
 * @subpackage  Engine
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class MySQL {

    private $resource   = 0;

    /**
     * Constructor of class MySQL
     *
     * Make connection to MySQL server
     */
    public function  __construct() {

        if (MYSQL_USE_PERSISTENT_CONNECT){

            $resource = mysql_pconnect(MYSQL_SERVER_NAME.":".MYSQL_SERVER_PORT, MYSQL_SERVER_LOGIN, MYSQL_SERVER_PASSWORD);

        } else {

            $resource = mysql_connect(MYSQL_SERVER_NAME.":".MYSQL_SERVER_PORT, MYSQL_SERVER_LOGIN, MYSQL_SERVER_PASSWORD);

        }

            if ($resource === false) {
                throw new ExceptionExt("Can not connect to MySQL server.<br>".
                                       " Server: '".MYSQL_SERVER_NAME.":".MYSQL_SERVER_PORT."' ".
                                       "Login: ".MYSQL_SERVER_LOGIN." Password: ".MYSQL_SERVER_PASSWORD."<br>".
                                       "Error No: ".mysql_errno().": ".mysql_error()
                                      );
            }

        $result = mysql_select_db(MYSQL_DATABASE_NAME, $resource);
        
            if ($result === false) {
                throw new ExceptionExt("Can not select MySQL database<br>Error No: ".mysql_errno().": ".mysql_error());
            }
            
        //$result = mysql_query("SET CHARACTER SET ".MYSQL_SERVER_ENCODING);
        $result = mysql_set_charset(MYSQL_SERVER_ENCODING, $resource);

            if ($result === false) {
                throw new ExceptionExt("Can not set encoding<br>Error No: ".mysql_errno().": ".mysql_error());
            }

        $this->resource =   $resource;
    }

    /**
     * Destructor of class MySQL
     *
     * Close connection to MySQL server
     */
    public function  __destruct() {
        mysql_close($this->resource);
    }

    /**
     * Wrapper for mysql_query function
     *
     * @param   query string
     * @throws  if mysql_query returns false
     * @return  true if ok
     */
    public function query($queryString){

        $this->Resource = mysql_query($queryString, $this->resource);

        if ($this->Resource === false){
            throw new ExceptionExt("<br>Error in query <pre>$queryString</pre>MySQL error No ".mysql_errno().": ".mysql_error());
        }

        return true;
    }

    /**
     * Wrapper for mysql_fetch_array function
     *
     * @param   nothing
     * @throws  no throws
     * @return  next row
     * @todo    we can use mysql_fetch_object
     */
    public function fetchArray(){
        
        return mysql_fetch_array($this->Resource);

    }

    /**
     * Wrapper for mysql_num_rows function
     *
     * @param   nothing
     * @throws  no throws
     * @return  affected rows count
     */
    public function countRows(){

        return mysql_num_rows($this->Resource);

    }

    /**
     * Wrapper for mysql_affected_rows function
     *
     * @param   nothing
     * @throws  no throws
     * @return  affected rows count
     */
    public function affectedRows(){

        return mysql_affected_rows();

    }

    /**
     * Wrapper for mysql_insert_id function
     *
     * @param   nothing
     * @throws  no throws
     * @return  last inserted id after last INSERT with not affected AUTO_INCREMEN field
     */
    public function insertedId(){

        $id = mysql_insert_id();

        if (empty($id)){
            throw new ExceptionExt("Inserted id not defined");
        }

        return $id;

    }

    /**
     * Wrapper for mysql_free_result function
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function freeResult(){

        mysql_free_result($this->Resource);

    }

    /**
     * Wrapper for mysql_query function
     *
     * @param   query string
     * @throws  if mysql_query returns false
     * @return  true if ok
     */
    public function queryByResource($queryString){

        $Resource = mysql_query($queryString);

        if ($Resource === false){
            throw new ExceptionExt("<br>Error in query <pre>$queryString</pre>MySQL error No ".mysql_errno().": ".mysql_error());
        }

        return $Resource;
    }

    /**
     * Wrapper for mysql_fetch_array function
     *
     * @param   nothing
     * @throws  no throws
     * @return  next row
     * @todo    we can use mysql_fetch_object
     */
    public function fetchArrayByResource($Resource){

        if (!is_resource($Resource)){
            throw new ExceptionExt("<br>Missed Resource Id <pre></pre>MySQL error No ".mysql_errno().": ".mysql_error());
        }

        return mysql_fetch_array($Resource);

    }

    /**
     * Wrapper for mysql_num_rows function
     *
     * @param   nothing
     * @throws  no throws
     * @return  next row
     */
    public function countRowsByResource($Resource){

        if (!is_resource($Resource)){
            throw new ExceptionExt("<br>Missed Resource Id <pre></pre>MySQL error No ".mysql_errno().": ".mysql_error());
        }

        return mysql_num_rows($Resource);

    }

    /**
     * Wrapper for mysql_free_result function
     *
     * @param   nothing
     * @throws  no throws
     * @return  nothing
     */
    public function freeResultByResource($Resource){

        if (!is_resource($Resource)){
            throw new ExceptionExt("<br>Missed Resource Id <pre></pre>MySQL error No ".mysql_errno().": ".mysql_error());
        }

        mysql_free_result($Resource);

    }
}
?>
