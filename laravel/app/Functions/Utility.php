<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 上午9:12
 */

namespace App\Functions;

class Utility {

    const RESPONSE_CODE_SUCCESS = 1;
    const RESPONSE_CODE_Error = 0;
    const RESPONSE_CODE_AUTH_ERROR = -99;
    const RESPONSE_CODE_DB_ERROR = -98;

    static function genToken( $len = 32, $md5 = true ) {
        # Seed random number generator
        # Only needed for PHP versions prior to 4.2
        mt_srand( (double)microtime()*1000000 );
        # Array of characters, adjust as desired
        $chars = array(
            'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
            'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
            '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
            'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
            '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
            '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
            'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
        );
        # Array indice friendly number of chars;
        $numChars = count($chars) - 1; $token = '';
        # Create random token at the specified length
        for ( $i=0; $i<$len; $i++ )
            $token .= $chars[ mt_rand(0, $numChars) ];
        # Should token be run through md5?
        if ( $md5 ) {
            # Number of 32 char chunks
            $chunks = ceil( strlen($token) / 32 ); $md5token = '';
            # Run each chunk through md5
            for ( $i=1; $i<=$chunks; $i++ )
                $md5token .= md5( substr($token, $i * 32 - 32, 32) );
            # Trim the token
            $token = substr($md5token, 0, $len);
        } return $token;
    }

    static function response_format($response_code, $data, $response_message)
    {
        $arr = array('response_code' => $response_code, 'data' => $data, 'response_message' => $response_message);
        return json_encode($arr);
    }

    static function isValidateUsername($username) {

        return true;
    }

    static function isValidatePassword($password) {

        return true;
    }


}