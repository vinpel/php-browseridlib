<?php
namespace BrowserID\Algs;
use BrowserID\Crypt;
use BrowserID\Math;

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
        $this->rsa = new CryptRSA();
        $this->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        if ($key != null)
        {
            $this->rsa->loadKey($key);
            $this->rsa->setPrivateKey($key);
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
        $n = new MathBigInteger($obj["n"]);
        $e = new MathBigInteger($obj["e"]);
        $d = new MathBigInteger($obj["d"]);
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
