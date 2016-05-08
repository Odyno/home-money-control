<?php

/**
 * Created by PhpStorm.
 * User: astaniscia
 * Date: 18/10/15
 * Time: 17:39
 */



if ( ! defined( 'ABSPATH' ) ) exit;


class HMC_UTILS
{

    static function check_array_value($key, $array, $raise_error = false)
    {
        if ( array_key_exists( $key, $array) && !empty( $array[$key] )) {
            return $array[$key];
        }
        if ($raise_error) {
            throw new Exception("Field " . $key . " not valid");
        }
        return null;
    }



    public static function UUIDv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }


    public static function checkUser(){
        $current_user_id = get_current_user_id();

        if ( empty( $current_user_id ) ) {
            return new WP_Error( 'json_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
        }

    }

}