<?php
namespace BrowserID\Algs;

use phpseclib\Crypt\RSA;
use BrowserID\Keys\AbstractSecretKey;
use phpseclib\Math\BigInteger;
/**
 * RSA secret key
 *
 * A secret key using the RSA algorithm.
 *
 * @package     Algs
 * @subpackage  RS
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
class RSASecretKey extends AbstractSecretKey {

    /**
     * RSA instance
     *
     * @access private
     * @var CryptRSA
     */
    private $rsa;

    /**
     * Constructor
     *
     * @access public
     * @param string $key Secret key in PKCS#1 or raw format
     * @param type $keysize
     */
    public function __construct($key = null, $keysize = null)
    {
        $this->rsa = new RSA();
        $this->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        if ($key != null)
        {

            $this->rsa->loadKey($key);
            \Codeception\Util\Debug::debug('set private key');
            $this->rsa->setPrivateKey($key);
            \Codeception\Util\Debug::debug(  var_dump(          $this->rsa->getPrivateKey($key)));
            if ($keysize != null)
            {
                $this->rsa->setHash(RSAKeyPair::$KEYSIZES[$keysize]["hashAlg"]);
                $this->keysize = $keysize;
            }
        }

    }

    /**
     * @see AbstractKeyInstance::deserializeFromObject($obj)
     */
    protected function deserializeFromObject($obj)
    {
        $n = new BigInteger($obj["n"]);
        $e = new BigInteger($obj["e"]);
        $d = new BigInteger($obj["d"]);
        $array = array(
            "n" => $n,
            "e" => $e,
            "d" => $d
        );
        $this->rsa->loadKey($array, CRYPT_RSA_PUBLIC_FORMAT_RAW);
        $this->rsa->setPrivateKey($array, CRYPT_RSA_PUBLIC_FORMAT_RAW);
        $this->keysize = RSAKeyPair::_getKeySizeFromRSAKeySize(strlen($n->toBits()));
        $this->rsa->setHash(RSAKeyPair::$KEYSIZES[$this->keysize]["hashAlg"]);
        return $this;
    }

    /**
     * @see AbstractKeyInstance::serializeToObject($obj)
     */
    protected function serializeToObject(&$obj){
      \Codeception\Util\Debug::debug($this->rsa->getPrivateKey());
        $key = $this->rsa->getPrivateKey();
        $obj["n"] = $key["n"]->toString();
        $obj["e"] = $key["e"]->toString();
        $obj["d"] = $key["d"]->toString();
    }

    /**
     * @see AbstractSecretKey::sign($message)
     */
    public function sign($message)
    {
        return $this->rsa->sign($message);
    }
}
