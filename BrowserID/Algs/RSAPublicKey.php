<?php
namespace BrowserID\Algs;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;
use BrowserID\Keys\AbstractPublicKey;
use BrowserID\Configuration;
/**
* RSA public key
*
* A public key using the RSA algorithm.
*
* @package     Algs
* @subpackage  RS
* @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
* @version     1.0.0
*/
class RSAPublicKey extends AbstractPublicKey {

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
  * @param string $key Public key in PKCS#1 or raw format
  * @param type $keysize
  */
  public function __construct($key = null, $keysize = null)
  {
    $this->rsa = new RSA();
    // @deprecated : https://github.com/phpseclib/phpseclib/pull/902
    //$this->rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
    if ($key != null)
    {
      $this->rsa->setPublicKey($key);
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
    $array = array(
      "n" => $n,
      "e" => $e
    );
    $this->rsa->setPublicKey($array, Configuration::CRYPT_RSA_PUBLIC_FORMAT_RAW);
    $this->keysize = RSAKeyPair::_getKeySizeFromRSAKeySize(strlen($n->toBits()));
    $this->rsa->setHash(RSAKeyPair::$KEYSIZES[$this->keysize]["hashAlg"]);
    return $this;
  }

  /**
  * @see AbstractKeyInstance::serializeToObject($obj)
  */
  protected function serializeToObject(&$obj){
    $key = $this->rsa->getPublicKey(Configuration::CRYPT_RSA_PUBLIC_FORMAT_RAW);
    $obj["n"] = $key["n"]->toString();
    $obj["e"] = $key["e"]->toString();
  }

  /**
  * @see AbstractPublicKey::verify($message, $signature)
  */
  public function verify($message, $signature)
  {

    return $this->rsa->verify($message, $signature);
  }
}
