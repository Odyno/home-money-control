<?php
/**
 * Created by PhpStorm.
 * User: staniscia
 * Date: 26/05/14
 * Time: 14.06
 */


    $lines = file("MHF_COUNTS.DF");

    foreach ($lines as $line) {

        $keydata = explode(";",$line);
        print_r($keydata);
    }
