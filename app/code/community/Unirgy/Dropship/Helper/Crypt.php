<?php

class Unirgy_Dropship_Helper_Crypt extends Mage_Core_Helper_Abstract {
    
    /* Encrypt
     *
     * @param string OR array $stringArray
     *
     * @return string $s
     */
    function encrypt($stringArray, $key = "default_key") {
        $s = strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), serialize($stringArray), MCRYPT_MODE_CBC, md5(md5($key)))), '+/=', '-_,');
        return $s;
    }

    /* Decrypt
     *
     * @param string OR array $stringArray
     *
     * @return string $s
     */    
    function decrypt($stringArray, $key = "default_key") {
        $s = unserialize(rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(strtr($stringArray, '-_,', '+/=')), MCRYPT_MODE_CBC, md5(md5($key))), "\0"));
        return $s;
    }
    
}