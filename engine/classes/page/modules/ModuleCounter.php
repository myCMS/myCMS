<?php
/**
 * Counter module
 *
 * @category    Engine
 * @package     Engine
 * @subpackage  Modules
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ModuleCounter {

    private $MySQL          = null;
    private $InputFilter    = null;

    /**
     * Constructor of class ModuleCounter
     */
    public function  __construct(MySQL $MySQL, InputFilter $InputFilter) {

        $this->MySQL        = $MySQL;
        $this->InputFilter  = $InputFilter;

        $ip = $this->InputFilter->getRemoteIpAddress();

        /*** insert new ip or update hits and hits_today if ip exists ***/
        $query  = "INSERT INTO counter (`ip_address`, `date`, `hits`, `hits_today`, `hosts`) VALUES (INET_ATON('$ip'), NOW(), 1, 1, 1)";
        $query .= "ON DUPLICATE KEY UPDATE `hits`=`hits`+1, `hits_today`=`hits_today`+1;";

        $this->MySQL->query($query);

        /*** update hosts if date of inserted ip is older than 1 day ***/
        $this->MySQL->query("select * from counter where `date` BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) and NOW() and `ip_address`=INET_ATON('$ip')");

        if ( !$this->MySQL->countRows() ){

            $this->MySQL->query("UPDATE counter set `date`=NOW(), `hosts`=`hosts`+1, `hits_today`=1 where `ip_address`=INET_ATON('$ip')");
        }
    }

    /**
     * Return all counters
     *
     * @param   nothing
     * @throws  taken from MySQL
     * @return  all counters
     */
    public function getCounter(){

        $result = array();

        $this->MySQL->query("select SUM(`hits_today`) as hits_today, COUNT(`hosts`) as hosts_today from counter where `date` BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) and NOW()");

        while ($row = $this->MySQL->fetchArray()){

            $result['hits_today']   = $row['hits_today'];
            $result['hosts_today']  = $row['hosts_today'];

        }

        $this->MySQL->query("select SUM(`hits`) as hits, SUM(`hosts`) as hosts from counter");

        while ($row = $this->MySQL->fetchArray()){

            $result['hits_all']     = $row['hits'];
            $result['hosts_all']    = $row['hosts'];

        }

        return $result;

    }
}
?>
