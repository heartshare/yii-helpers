<?php
/**
*
* Version 1.0.1
* 
* Author: Roberto Serra - obi.serra@gmail.com
* 
* DateHandler - converts date from/to SQL database
*/

class DateHandler extends CActiveRecordBehavior {

    public $dateColumns = array();

    public $dbFormat = 'Y-m-d';
    public $outFormat = 'd-m-Y';

    private $mesi = array(1=>'Gennaio',2=>'Febbraio',3=>'Marzo',4=>'Aprile',5=>'Maggio',6=>'Giugno',7=>'Luglio',8=>'Agosto',9=>'Settembre', 10=>'Ottobre', 11=>'Novembre', 12=>'Dicembre');
    private $month = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September', 10=>'October', 11=>'November', 12=>'December');
    private $time;

    /**
    * Convert from $dateFormat to ISO 9075 dates before saving
    */
    public function beforeSave($event) {
        foreach( $this->dateColumns as $date ) {
            $_dt = $this->Owner->{$date};
            if($this->convertFromIta($_dt)){
                $_dt = $this->convertToCustom($this->dbFormat);
            }
            $this->Owner->{$date} = $_dt;

        }
        return parent::beforeSave($event);
    }
    /**
    * Converts ISO 9075 dates to $dateFormat after read from database
    */
    public function afterFind($event){
        foreach( $this->dateColumns as $date ){
            $_dt = $this->Owner->{$date};
            if($this->convertFromDb($_dt)){
                $_dt = $this->convertToCustom($this->outFormat);
            }
            $this->Owner->{$date} = $_dt;
        }
        return parent::afterFind($event);
    }

    public function convertFromIta($date){

        $pattern = "/([0-9]{2})-([0-9]{2})-([0-9]{4})([\s0-9:]{0,9})/";
        $patternH = "/([0-9]{2}):([0-9]{2}):([0-9]{2})/";
        if(preg_match($pattern,$date, $regs)){
            if(!preg_match($patternH, $regs[4],$regsH)){
                $regsH[0] = '00:00:00';
                $regsH[1] = '00';
                $regsH[2] = '00';
                $regsH[3] = '00';
            } 
            $this->time = mktime($regsH[1],$regsH[2],$regsH[3],$regs[2],$regs[1],$regs[3]);
            return true;
        } else {
            return false;
        }
    }

    public function convertFromDb($date){
        $pattern = "/([0-9]{4})-([0-9]{2})-([0-9]{2})([\s0-9:]{0,9})/";
        $patternH = "/([0-9]{2}):([0-9]{2}):([0-9]{2})/";
        if(preg_match($pattern,$date, $regs)){
            if(!preg_match($patternH, $regs[4],$regsH)){
                $regsH[0] = '00:00:00';
                $regsH[1] = '00';
                $regsH[2] = '00';
                $regsH[3] = '00';
            } 
            $this->time = mktime($regsH[1],$regsH[2],$regsH[3],$regs[2],$regs[3],$regs[1]);
            return true;
        } else {
            return false;
        }
    }

    public function convertToCustom($format){
        return date($format,$this->time);
    }
    public function convertToMeseStr($lang){
        $m = $this->convertToCustom('m');
        if($lang === 'it'){
            $res = $this->mesi[intval($m)];
        } else {
            $res = $this->month[intval($m)];
        }
        return $res;
    }
}