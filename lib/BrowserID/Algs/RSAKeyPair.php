<?php
namespace BrowserID\Algs;
use BrowserID\Crypt;
use BrowserID\Math;
use BrowserID\Crypt\CryptRSA;
/**
 * RSA-SHA Hashing Interface
 *
 * Offers methods for signing and verifiying data using RSA-SHA
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    Algs
 * @subpackage RS
 * @author     Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @copyright  Alien-Scripts.de Benjamin Krämer
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * RSA key pair
 *
 * A pair of RSA keys.
 *
 * @package     Algs
 * @subpackage  RS
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
class RSAKeyPair extends \BrowserID\AbstractKeyPair {

    /**
     * Allowed keysizes
     *
     * @access public
     * @static
     * @var array
     */
    public static $KEYSIZES = array(
        64 => array(
            "rsaKeySize" => 512,
            "hashAlg" => "sha1" // sha256 is not working, encoding error
        ),
        128 => array(
            "rsaKeySize" => 1024,
            "hashAlg" => "sha256"
        ),
        256 => array(
            "rsaKeySize" => 2048,
            "hashAlg" => "sha256"
        )
    );

    /**
     * RSA instance
     *
     * @access private
     * @var CryptRSA
     */
    private $rsa;

    /**
     * Get keysize
     *
     * Gets the keysize depending on the bit count of the rsa key.
     *
     * @access public
     * @statis
     * @param int $bits Amount of bits
     * @return int Keysize
     */
    public static function _getKeySizeFromRSAKeySize($bits) {
        foreach(RSAKeyPair::$KEYSIZES as $keysize => $entry) {
            // we tolerate one bit off from the keysize
            if (abs($entry["rsaKeySize"]-$bits) <= 1)
                return $keysize;
        }

        throw new \Exception("bad key");
    }

    /**
     * @see AbstractKeyPair::createPublicKey();
     */
    public function createPublicKey()
    {
        return new RSAPublicKey();
    }

    /**
     * @see AbstractKeyPair::createSecretKey();
     */
    public function createSecretKey()
    {
        return new RSASecretKey();
    }

    /**
     * Generate keypair
     *
     * Generates a keypair for a given keysize in bits
     *
     * @abstract
     * @access public
     * @static
     * @param int $keysize Keysize in bits
     * @return RSAKeyPair Returning an instance of the key pair
     */
    public static function generate($keysize) {
        if (!isset(self::$KEYSIZES[$keysize]))
            throw new NoSuchAlgorithmException("keysize not supported");

        $instance = new RSAKeyPair();
        $instance->rsa = new CryptRSA();

        $instance->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $instance->rsa->setHash(self::$KEYSIZES[$keysize]["hashAlg"]);
        $keys = $instance->rsa->createKey(self::$KEYSIZES[$keysize]["rsaKeySize"]);

        $details = openssl_get_publickey($keys["publickey"]);
        $t=openssl_pkey_get_details($details);
        foreach ($t['rsa'] as $key=>$val)
            $public[$key]=bin2hex($val);

        $details = openssl_pkey_get_private($keys["privatekey"]);
        $t=openssl_pkey_get_details($details);

        foreach ($t['rsa'] as $key=>$val)
            $private[$key]=bin2hex($val);
         $instance->publicKey =json_encode($public);
          $instance->secretKey =json_encode($private);
            //don't remember why i short cut end of this function...
        //return array($instance->secretKey,$instance->publicKey);



        $instance->keysize = $keysize;

        $instance->publicKey = new RSAPublicKey($keys["publickey"], $keysize);

        $instance->secretKey = new RSASecretKey($keys["privatekey"], $keysize);

        $instance->algorithm = $instance->publicKey->algorithm = $instance->secretKey->algorithm = "RS";
        return $instance;
    }
}


?>
