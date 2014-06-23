<?php
namespace System\Lib\Kamele;

/**
 * Crypto library
 * 
 * Incorporates some hashing functions and other stuff done the RIGHT way, whereas right is defined 
 * by these articles and publications:
 * How to hash passwords safely:                            http://csrc.nist.gov/publications/nistpubs/800-132/nist-sp800-132.pdf
 * Number of iterations PBKDF2:                             https://www.owasp.org/index.php/Password_Storage_Cheat_Sheet#Work_Factor
 * Applying pepper:                                         https://lucb1e.com/?p=post&id=118
 * Number of iterations (approx for default value):         https://defuse.ca/php-pbkdf2.htm
 * 
 * If you think this class is not safe or one of these publications or articles is wrong, 
 * please e-mail me (Rick Lubbers, me@ricklubbers.nl) and we can work something out.
 * 
 * WARNING: 
 * This library is not to be used before checking the various variables at the start of the class
 * 
 * DISCLAIMER:
 * The authors and contributors of Kamele Framework are not in any way responsible for possible damage 
 * caused by this code. This point is covered in Kamele's main license, but I do want to stress it here once more.
 * 
 * @package     Kamele
 * @subpackage  Kamele Libraries
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.3
 */
final static class Crypto {
    
    /******************* VARIABLES TO REVIEW ARE FOUND BELOW THIS LINE *******************/
    
    /**
     * Change so hashing with PBKDF2 takes a suitable amount of time, which is a significant amount of time on your PRODUCTION server under light load
     * The default is set quite high. (Why I chose this default value: https://www.owasp.org/index.php/Password_Storage_Cheat_Sheet#Work_Factor)
     * 
     * @access  public
     * @static
     * @var     int     Number of iterations to use for PBKDF2
     public static $iterations      = 1000000;
    
    /** 
     * Do not change this value unless the sha256 algorithm has been proved insecure!
     * 
     * @access  public
     * @static
     * @var     string      Hashing algorithm to use
     */
    public static $algorithm        = 'sha256'
    
    /**
     * Apply pepper
     * 
     * @access  public
     * @static
     * @var     boolean     Whether or not to apply pepper
     */
    public static $applypepper      = true;
    
    /**
     * String of the pepper to use
     * 
     * IMPORTANT: This should be changed to a sufficiently long, random value!
     * The current value is about as useful as this: http://xkcd.com/221/
     * 
     * @access  public
     * @static
     * @var     string      String of random tokens to use for pepper
     */
    public static $pepper           = 'fjin6iFadguGqiImAKg2w7ecGiADpUgh0T8DB1aKsM5JLvcAd643go85WXiMX5XV';
    
    /**
     * Default hashlength to return
     * 
     * @access  public
     * @static
     * @var     int         The value in bytes of the hash
     */
    public static $length           = 128;
    
    /******************* VARIABLES TO REVIEW ARE FOUND ABOVE THIS LINE *******************/
    
    
    
    /**
     * Returns a PBKDF2 hashed value of $input, salted with $salt
     * 
     * @access      public
     * @param       string      $input          The input for the hashing function
     * @param       string      $salt           Salt to apply on the hashing function
     * @param       int         $length         Optional, length of the hash produced
     * @param       int         $iterations     Optional, number of iterations to apply
     * @param       boolean     $applypepper    Optional, whether or not pepper should be applied
     * @return      string|null
     * @static
     */
    public static hash_pbkdf2_safe($input, $salt, $length = null, $iterations = null, $algorithm = null, $applypepper = null) {
        if ($length === null || is_nan($length)) {
            $length = self::$length;
        }
        if ($iterations === null || is_nan($iterations)) {
            $iterations = self::$iterations;
        }
        if ($algorithm === null || !in_array($algorithm, hash_algos())) {
            $algorithm = self::$algorithm;
        }
        if ($applypepper !== false && $applypepper !== true) {
            $applypepper = self::$applypepper;
        }
        if ($applypepper === true) { 
            $pepper = self::$pepper;
        }
        else {
            $pepper = '';
        }
        if (function_exists('hash_pbkdf2')) {
            return hash_pbkdf2($algorithm, $input, $salt.$pepper, $iterations, $length, false);
        }
        else {
            return self::hash_pbkdf2($algorithm, $input, $salt.$pepper, $iterations, $length, false);
        }
    }
    
    /**
     * Internal replacement for the hash_pbkdf2 function builtin in PHP 5.5 and higher
     * 
     * @access      privae
     * @param       string      $a              The algorithm to use
     * @param       string      $password       The string to hash
     * @param       string      $salt           The salt to use
     * @param       int         $rounds         Number of iterations
     * @param       int         $key_length     Length of the key in bytes
     * @param       boolean     $raw_output     Should the output be in hexits (false) or raw in 8 bits string characters (true)
     */
    private static function hash_pbkdf2($a = 'sha256', $password, $salt, $rounds = 5000, $key_length = 32, $raw_output = false) { 
        $dk = '';
        for ($block=1; $block<=$key_length; $block++) { 
            $ib = $h = hash_hmac($a, $salt . pack('N', $block), $password, true); 
            for ($i=1; $i<$rounds; $i++) { 
                $ib ^= ($h = hash_hmac($a, $h, $password, true)); 
            } 
            $dk .= $ib;
        } 
        $key = substr($dk, 0, $key_length);
        return $raw_output ? $key : base64_encode($key);
    }
    
    /**
     * Generate a random number
     * 
     * @access      public
     * @static
     * @param       int         $min        The minimum output
     * @param       int         $max        The maximum output
     * @return      int
     */
    public static function rand_number($min, $max) {
        $range = $max - $min;
        if ($range == 0) {
            return $min;
        }
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes, $s)));
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);
        return $min + $rnd;
    }
    
    /**
     * Generate random bytes
     * 
     * @access      public
     * @static
     * @param       int         $length     Number of bytes to generate
     * @return      string|null
     */
    public static function rand_string($length) {
        $secure = false;
        $output = bin2hex(openssl_random_pseudo_bytes($length, $secure));
        if ($secure == false) {
            throw new \Exception('Random string generated is not cryptographically secure!');
            return null;
        }
        return $output;
    }
}