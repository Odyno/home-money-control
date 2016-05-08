<?php


if (!defined('ABSPATH')) exit;


if (!class_exists('HMC_Voice_Type')) {

    class VoiceType {
       public  $id=0;
       public $label="";
    };


    class HMC_Voice_Type
    {

        const SOPRAVVIVENZA = 0;
        const SERVIZI_OPTIONAL = 1;
        const HOBBIES_TEMPO_LIBERO = 2;
        const IMPREVISTI_EXTRA = 3;
        const ENTRATE = 4 ;
        const USCITE_FISSE = 5;

        static $types = array(
             'SOPRAVVIVENZA',
             'SERVIZI OPTIONAL',
             'HOBBIES E TEMPO LIBERO',
             'IMPREVISTI EXTRA',
             'ENTRATE',
             'USCITE FISSE');







        static public function check($in)
        {
            if( is_numeric($in)){
                HMC_Voice_Type::toString($in);
                return $in;
            }else{
                return HMC_Voice_Type::fromString($in);
            }

        }

        static public function fromString($in)
        {
            foreach (HMC_Voice_Type::$types as $key => $type) {
                if ( $type == $in || $key == $in)  {
                    return $key;
                }
            }
            throw new Exception("Not Valid Type");
        }

        static public function toString($id)
        {
            if (!isset(HMC_Voice_Type::$types[$id])) throw new Exception("not Found");
            return HMC_Voice_Type::$types[$id];
        }





        public static function get($id)
        {
            if (!isset(HMC_Voice_Type::$types[$id])) throw new Exception("not Found");

            $a= new VoiceType();
            $a->id= $id;
            $a->label =  HMC_Voice_Type::$types[$id];
            return $a;
        }

        public static function getAll()
        {
            $out=array();

            foreach (HMC_Voice_Type::$types as $key => $type) {
                array_push($out,HMC_Voice_Type::get($key));
            }

            return $out;
        }


    }

}