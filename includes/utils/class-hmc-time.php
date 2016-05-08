<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Description of class-HMC-time-machine
 *
 * @author astaniscia
 */
class HMC_Time {
    
    static function HUMAN_TO_MYSQL_DATE(HMC_Time $date_str){
        $datea_str=explode ("/",$date_str);
        return date( 'Y-m-d H:i:s',strtotime($datea_str[1]."/".$datea_str[0]."/".$datea_str[2]));
    }

    static function MYSQL_TO_HUMAN_DATE($date_str_mysql){
        return date('d/m/Y',strtotime($date_str_mysql));
    }

    static function MakeFromHumanDate($date_str){
        $datea_str=explode ("/",$date_str);
        $out=new HMC_Time();
        $out->set($datea_str[0],$datea_str[1],$datea_str[2]);
        return $out;
    }


    static function MakeFromDataBase($date_str_mysql){
        $out=new HMC_Time();
        $out->setUnixEpoch(strtotime($date_str_mysql));
        return $out;
    }


    static function MakeFromUnixEpoc($date_str){
        $out=new HMC_Time();
        $out->setUnixEpoch(intval($date_str));
        return $out;
    }


    static function MakeFromECMAScriptISO8601($date_str){
        $pattern = "YYYY-MM-DDTHH:mm:ss.sssZ";
        $out=new HMC_Time();
        $out->setUnixEpoch(intval(date("U",strtotime($date_str))));
        return $out;
    }



    private $datetime;
    
    function __construct() {
        $this->datetime = time();
    }

    public function setUnixEpoch($long) {
        $this->datetime = $long;
    }

    
    public function getDay() {
        return date('d', $this->datetime);
    }

    public function getMonth() {
         return date('m', $this->datetime);
    }

    public function getYear() {
         return date('Y', $this->datetime);
    }
    
    
     public function set($day,$month,$year) {
         if (checkdate (  $month ,  $day ,  $year )){
             $this->datetime = strtotime($month ."/".  $day ."/". $year );
         }else{
             throw  new Exception('Invalid Date Time');
         }
         return $this;
     }

    public function setDay($day) {
        $this->set($day,$this->getMonth(),$this->getYear());
        return $this;
    }

    public function setMonth($month) {
        $this->set($this->getDay(),$month,$this->getYear());
        return $this;
    }

    public function setYear($year) {
        $this->set($this->getDay(),$this->getMonth(),$year);
        return $this;
    }
    
    public function asString() {
        return $this-> __toString();
    }
    
    public function asMySqlString() {

        return date( 'Y-m-d H:i:s',$this->datetime);
    }
    
    public function __toString() {
        return $this->getDay() ."/".$this->getMonth() ."/".$this->getYear();
    }

    public function toArray() {
        #$pattern = "YYYY-MM-DDTHH:mm:ss.sssZ";
        return date('Y-m-d',$this->datetime) . "T" . date('H:i:s',$this->datetime).".000Z";
        #return $this->datetime;

    }



}
