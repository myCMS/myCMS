<?php
/**
 * Used for define static content
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class StaticContent {

    /**
     * Constructor of class StaticContent
     */
    public function  __construct() {

    }

    /**
     * Return current year
     *
     * @param   nothing
     * @throws  no throws
     * @return  current year
     */
    public function getCopyright(){
        
        $result = date("Y");

        if ($result == COPYRIGHT_START_YEAR){

            return $result;
            
        } else {

            return COPYRIGHT_START_YEAR."&ndash;".$result;
        }
    }

    /**
     * Return list of russian month names
     *
     * @param   nothing
     * @throws  no throws
     * @return  list of russian month names
     */
    public function getAllMonthesRu(){

        return array("01" => "Январь",
                     "02" => "Февраль",
                     "03" => "Март",
                     "04" => "Апрель",
                     "05" => "Май",
                     "06" => "Июнь",
                     "07" => "Июль",
                     "08" => "Август",
                     "09" => "Сентябрь",
                     "10" => "Октябрь",
                     "11" => "Ноябрь",
                     "12" => "Декабрь"
                    );
    }

    /**
     * Return list of english month names
     *
     * @param   nothing
     * @throws  no throws
     * @return  list of english month names
     */
    public function getAllMonthesEn(){

        return array("01" => "Jan",
                     "02" => "Feb",
                     "03" => "Mar",
                     "04" => "Apr",
                     "05" => "May",
                     "06" => "Jun",
                     "07" => "Jul",
                     "08" => "Aug",
                     "09" => "Sep",
                     "10" => "Okt",
                     "11" => "Nov",
                     "12" => "Dec"
                    );
    }
}
?>
