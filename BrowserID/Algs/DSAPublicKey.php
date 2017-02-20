<?php
namespace BrowserID\Algs;
use BrowserID\AbstractPublicKey;
use phpseclib\Math\BigInteger;
use BrowserID\Utils;
use BrowserID\Crypt\CryptDSA;
/**
 * DSA public key
 *
 * A public key using the DSA algorithm.
 *
 * @package     Algs
 * @subpackage  DS
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
class DSAPublicKey extends AbstractPublicKey {

    /**
     * Public key
     *
     * @access private
     * @var BigInteger
     */
    private $key_y;

    /**
     * Prime p
     *
     * @access private
     * @var BigInteger
     */
    private $key_p;

    /**
     * Prime q
     *
     * @access private
     * @var BigInteger
     */
    private $key_q;

    /**
     * Group g
     *
     * @access private
     * @var BigInteger
     */
    private $key_g;

    /**
     * Constructor
     *
     * @access public
     * @param BigInteger $key Public key as number
     * @param type $keysize
     */
    public function __construct($key = null, $keysize = null)
    {
        $this->key_y = $key;
        if ($keysize != null)
        {
            $key_values = DSAKeyPair::$KEYSIZES[$keysize];
            $this->key_p = $key_values["p"];
            $this->key_q = $key_values["q"];
            $this->key_g = $key_values["g"];
            $this->keysize = $keysize;
        }
    }

    /**
     * @see AbstractKeyInstance::deserializeFromObject($obj)
     */
    protected function deserializeFromObject($obj)
    {
        $this->key_p = new BigInteger($obj["p"], 16);
        $this->key_q = new BigInteger($obj["q"], 16);
        $this->key_g = new BigInteger($obj["g"], 16);
        $this->key_y = new BigInteger($obj["y"], 16);
        $this->keysize = DSAKeyPair::_getKeySizeFromRSAKeySize(strlen($this->key_y->toBits()));
        $this->key_values = DSAKeyPair::$KEYSIZES[$this->keysize];
        return $this;
    }

    /**
     * @see AbstractKeyInstance::serializeToObject($obj)
     */
    protected function serializeToObject(&$obj){
        $obj["p"] = $this->key_p->toHex();
        $obj["q"] = $this->key_q->toHex();
        $obj["g"] = $this->key_g->toHex();
        $obj["y"] = $this->key_y->toHex();
    }

    /**
     * @see AbstractPublicKey::verify($message, $signature)
     */
    public function verify($message, $signature)
    {
        $params = DSAKeyPair::$KEYSIZES[$this->keysize];
        $hash_alg = $params["hashAlg"];
        $hexlength = $params["q_bitlength"] / 4;

        // we pre-pad with 0s because encoding may have gotten rid of some
        $signature = Utils::hex_lpad(bin2hex($signature), $hexlength * 2);

        // now this should only happen if the signature was longer
        if (strlen($signature) != ($hexlength * 2)) {
            throw new \Exception("problem with r/s combo: " . sizeof($signature) . "/" . $hexlength . " - " . $signature);
        }

        $r = new BigInteger(substr($signature, 0, $hexlength), 16);
        $s = new BigInteger(substr($signature, $hexlength, $hexlength), 16);


        // check rangeconstraints
        if (($r->compare(DSAKeyPair::$zero) < 0) || ($r->compare($this->key_q) > 0)) {
            throw new \Exception("problem with r: " . $r->toString());
        }

        if (($s->compare(DSAKeyPair::$zero) < 0) || ($s->compare($this->key_q) > 0)) {
            throw new \Exception("problem with s: " . $r->toString());
        }

        return CryptDSA::verify($message, $hash_alg, $r, $s, $this->key_p, $this->key_q, $this->key_g, $this->key_y);
    }
}
