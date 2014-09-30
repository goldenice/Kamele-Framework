<?php
namespace System\Lib\Kamele;

/**
 * Compatibility library
 * 
 * Implementations of native PHP 5.5 functions for PHP 5.3 and up
 * The core functions mentioned here are implemented: http://nl1.php.net/manual/en/migration55.new-functions.php
 * 
 * Contains parts of https://github.com/ircmaxell/password_compat
 * 
 * @package     Kamele
 * @subpackage  Kamele Libraries
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.4
 * @static
 */
class Compatibility {
    
    /**
     * Returns values from single column of the $array, identified by $column_key
     * 
     * @param   array       $array          The main array with data
     * @param   mixed       $column_key     Column to return
     * @param   mixed       $index_key      Index key to use (optionally)
     * @return  array
     */
    public static function array_column($array, $column_key, $index_key = null) {
        $return = array();
        foreach ($array as $key=>$value) {
            if (isset($value[$column_key]) == true) {
                if ($index_key == null) {
                    $return[$key] = $value[$column_key];
                }
                else {
                    $return[$value[$index_key]] = $value[$column_key];
                }
            }
        }
        return $return;
    }
    
    /**
     * Get boolean value of variable
     * 
     * @param   mixed       $var    The variable to get the boolean value for
     * @return  boolean
     */
    public static function boolvar($var) {
        if ($var == true) {
        	return true;
        }
        return false;
    }
    
    /**
     * Returns last error message for JSON encode or JSON decode
     * 
     * Copypasted from php.net, by Anonymous.
     * 
     * @return  string
     */
    public static function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    } 
    
    /**
     * Fix the constants for password functions
     * 
     * @access  private
     * @internal
     * @return  void
     */
    private static function fixPasswordConstants() {
        if (!defined('PASSWORD_DEFAULT')) {
            define('PASSWORD_BCRYPT', 1);
            define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
        }
    }
    
    /**
     * Count the number of bytes in a string
     *
     * We cannot simply use strlen() for this, because it might be overwritten by the mbstring extension.
     * In this case, strlen() will count the number of *characters* based on the internal encoding. A
     * sequence of bytes might be regarded as a single multibyte character.
     *
     * @access  private
     * @author  Anthony Ferrara, https://github.com/ircmaxell, <me@ircmaxell.com>
     * @internal
     * @param   string      $binary_string      The input string
     * @return  int
     */
    private static function _strlen($binary_string) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($binary_string, '8bit');
        }
        return strlen($binary_string);
    }
    
    /**
     * Get a substring based on byte limits
     *
     * @access  private
     * @author  Anthony Ferrara, https://github.com/ircmaxell, <me@ircmaxell.com>
     * @internal
     * @param string $binary_string The input string
     * @param int    $start
     * @param int    $length
     * @see _strlen()
     * @return string The substring
     */
    private static function _substr($binary_string, $start, $length) {
        if (function_exists('mb_substr')) {
            return mb_substr($binary_string, $start, $length, '8bit');
        }
        return substr($binary_string, $start, $length);
    }
   
    /**
     * Hash the password using the specified algorithm
     *
     * @author  Anthony Ferrara, https://github.com/ircmaxell, <me@ircmaxell.com>
     * @param   string  $password   The password to hash
     * @param   int     $algo       The algorithm to use (Defined by PASSWORD_* constants)
     * @param   array   $options    The options for the algorithm to use
     * @return string|false     The hashed password, or false on error.
     */
    public static function password_hash($password, $algo, array $options = array()) {
        self::fixPasswordConstants();
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_hash to function", E_USER_WARNING);
            return null;
        }
        if (!is_string($password)) {
            trigger_error("password_hash(): Password must be a string", E_USER_WARNING);
            return null;
        }
        if (!is_int($algo)) {
            trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
            return null;
        }
        $resultLength = 0;
        switch ($algo) {
            case PASSWORD_BCRYPT:
                // Note that this is a C constant, but not exposed to PHP, so we don't define it here.
                $cost = 10;
                if (isset($options['cost'])) {
                    $cost = $options['cost'];
                    if ($cost < 4 || $cost > 31) {
                        trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
                        return null;
                    }
                }
                // The length of salt to generate
                $raw_salt_len = 16;
                // The length required in the final serialization
                $required_salt_len = 22;
                $hash_format = sprintf("$2y$%02d$", $cost);
                // The expected length of the final crypt() output
                $resultLength = 60;
                break;
            default:
                trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
                return null;
        }
        $salt_requires_encoding = false;
        if (isset($options['salt'])) {
            switch (gettype($options['salt'])) {
                case 'NULL':
                case 'boolean':
                case 'integer':
                case 'double':
                case 'string':
                    $salt = (string) $options['salt'];
                    break;
                case 'object':
                    if (method_exists($options['salt'], '__tostring')) {
                        $salt = (string) $options['salt'];
                        break;
                    }
                case 'array':
                case 'resource':
                default:
                    trigger_error('password_hash(): Non-string salt parameter supplied', E_USER_WARNING);
                    return null;
            }
            if (PasswordCompat\binary\_strlen($salt) < $required_salt_len) {
                trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", PasswordCompat\binary\_strlen($salt), $required_salt_len), E_USER_WARNING);
                return null;
            } elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
                $salt_requires_encoding = true;
            }
        } else {
            $buffer = '';
            $buffer_valid = false;
            if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
                $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
                if ($buffer) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
                $buffer = openssl_random_pseudo_bytes($raw_salt_len);
                if ($buffer) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid && @is_readable('/dev/urandom')) {
                $f = fopen('/dev/urandom', 'r');
                $read = PasswordCompat\binary\_strlen($buffer);
                while ($read < $raw_salt_len) {
                    $buffer .= fread($f, $raw_salt_len - $read);
                    $read = PasswordCompat\binary\_strlen($buffer);
                }
                fclose($f);
                if ($read >= $raw_salt_len) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid || PasswordCompat\binary\_strlen($buffer) < $raw_salt_len) {
                $bl = PasswordCompat\binary\_strlen($buffer);
                for ($i = 0; $i < $raw_salt_len; $i++) {
                    if ($i < $bl) {
                        $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                    } else {
                        $buffer .= chr(mt_rand(0, 255));
                    }
                }
            }
            $salt = $buffer;
            $salt_requires_encoding = true;
        }
        if ($salt_requires_encoding) {
            // encode string with the Base64 variant used by crypt
            $base64_digits =
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
            $bcrypt64_digits =
                './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            $base64_string = base64_encode($salt);
            $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
        }
        $salt = PasswordCompat\binary\_substr($salt, 0, $required_salt_len);

        $hash = $hash_format . $salt;

        $ret = crypt($password, $hash);

        if (!is_string($ret) || PasswordCompat\binary\_strlen($ret) != $resultLength) {
            return false;
        }

        return $ret;
    }
    
    
    /**
     * Returns information about the given hash
     * 
     * @author  Anthony Ferrara, https://github.com/ircmaxell, <me@ircmaxell.com>
     * @param   string      $hash   The hash to return information about
     * @return  array
     */
    public static function password_get_info($hash) {
        self::fixPasswordConstants();
        $return = array(
            'algo' => 0,
            'algoName' => 'unknown',
            'options' => array(),
        );
        if (self::_substr($hash, 0, 4) == '$2y$' && self::_strlen($hash) == 60) {
            $return['algo'] = PASSWORD_BCRYPT;
            $return['algoName'] = 'bcrypt';
            list($cost) = sscanf($hash, "$2y$%d$");
            $return['options']['cost'] = $cost;
        }
        return $return;
    }
    
    /**
     * Determine if the password hash needs to be rehashed according to the options provided
     *
     * If the answer is true, after validating the password using password_verify, rehash it.
     *
     * @author  Anthony Ferrara, https://github.com/ircmaxell, <me@ircmaxell.com>
     * @param   string      $hash       The hash to test
     * @param   int         $algo       The algorithm used for new password hashes
     * @param   array       $options    The options array passed to password_hash
     * @return  boolean True if the password needs to be rehashed.
     */
    public static function password_needs_rehash($hash, $algo, array $options = array()) {
        self::fixPasswordConstants();
        $info = password_get_info($hash);
        if ($info['algo'] != $algo) {
            return true;
        }
        switch ($algo) {
            case PASSWORD_BCRYPT:
                $cost = isset($options['cost']) ? $options['cost'] : 10;
                if ($cost != $info['options']['cost']) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Verify a password against a hash using a timing attack resistant approach
     *
     * @author  Anthony Ferrara, https://github.com/ircmaxell, <me@ircmaxell.com>
     * @param   string      $password   The password to verify
     * @param   string      $hash       The hash to verify against
     * @return  boolean If the password matches the hash
     */
    public static function password_verify($password, $hash) {
        self::fixPasswordConstants();
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_verify to function", E_USER_WARNING);
            return false;
        }
        $ret = crypt($password, $hash);
        if (!is_string($ret) || PasswordCompat\binary\_strlen($ret) != PasswordCompat\binary\_strlen($hash) || PasswordCompat\binary\_strlen($ret) <= 13) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < PasswordCompat\binary\_strlen($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }

        return $status === 0;
    }
    
    /**
     * Hash a password with PBKDF2
     * 
     * @author  Ryan Chouinard, http://ryanchouinard.com, <rchouinard@gmail.com>
     * @param   string  $algo       The algorithm to use
     * @param   string  $password   Password to hash
     * @param   string  $salt       Salt to use
     * @param   int     $iterations The number of iterations to use
     * @param   int     $length     Length of the string in bytes
     * @param   boolean $raw_output Return raw output
     * @return  string
     */
    public static function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false) {
        // Prep input arguments
        $algo       = (string) isset($algo) ? $algo : null;
        $password   = (string) isset($password) ? $password : null;
        $salt       = (string) isset($salt) ? $salt : null;
        $iterations = (integer) isset($iterations) ? $iterations : null;
        $length     = (integer) $length;
        $raw_output = (boolean) $raw_output;

        // Recreate \hash_pbkdf2() error conditions
        $num_args = func_num_args();
        if ($num_args < 4) {
            trigger_error(sprintf('\%s() expects at least 4 parameters, %d given', __FUNCTION__, $num_args), E_USER_WARNING);
            return null;
        }

        if (!in_array($algo, hash_algos())) {
            trigger_error(sprintf('Unknown hashing algorithm: %s', $algo), E_USER_WARNING);
            return false;
        }

        if ($iterations <= 0) {
            trigger_error(sprintf('Iterations must be a positive integer: %d', $iterations), E_USER_WARNING);
            return false;
        }

        if ($length < 0) {
            trigger_error(sprintf('Length must be greater than or equal to 0: %d', $length), E_USER_WARNING);
            return false;
        }

        $salt_len = strlen($salt);
        if ($salt_len > PHP_INT_MAX - 4) {
            trigger_error(sprintf('Supplied salt is too long, max of PHP_INT_MAX - 4 bytes: %d supplied', $salt_len), E_USER_WARNING);
            return false;
        }

        // Algorithm implementation
        $hash_len = strlen(hash($algo, null, true));
        if ($length == 0) {
            $length = $hash_len;
        }

        $output = '';
        $block_count = ceil($length / $hash_len);
        for ($block = 1; $block <= $block_count; ++$block) {
            $key1 = $key2 = hash_hmac($algo, $salt . pack('N', $block), $password, true);
            for ($iteration = 1; $iteration < $iterations; ++$iteration) {
                $key2 ^= $key1 = hash_hmac($algo, $key1, $password, true);
            }
            $output .= $key2;
        }

        // Output the derived key
        // NOTE: The built-in \hash_pbkdf2() function trims the output to $length,
        // not the raw bytes before encoding as might be expected. I'm not a fan
        // of that decision, but it's emulated here for full compatibility.
        return substr(($raw_output) ? $output : bin2hex($output), 0, $length);
    }
}