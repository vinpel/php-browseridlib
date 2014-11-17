<?php
namespace BrowserID\Algs;
use BrowserID\Crypt;
use BrowserID\Math;

/**
 * DSA secret key
 *
 * A secret key using the DSA algorithm.
 *
 * @package     Algs
 * @subpackage  DS
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
class DSASecretKey extends AbstractSecretKey {

    /**
     * Secret key
     *
     * @access private
     * @var MathBigInteger
     */
    private $key_x;

    /**
     * Prime p
     *
     * @access private
     * @var MathBigInteger
     */
    private $key_p;

    /**
     * Prime q
     *
     * @access private
     * @var MathBigInteger
     */
    private $key_q;

    /**
     * Group g
     *
     * @access private
     * @var MathBigInteger
     */
    private $key_g;

    /**
     * Constructor
     *
     * @access public
     * @param MathBigInteger $key Secret key as number
     * @param type $keysize
     */
    public function __construct($key = null, $keysize = null)
    {
        $this->key_x = $key;
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
        $this->key_p = new MathBigInteger($obj["p"], 16);
        $this->key_q = new MathBigInteger($obj["q"], 16);
        $this->key_g = new MathBigInteger($obj["g"], 16);
        $this->key_x = new MathBigInteger($obj["x"], 16);
        $this->keysize = DSAKeyPair::_getKeySizeFromRSAKeySize(strlen($this->key_p->toBits()));
        return $this;
    }

    /**
     * @see AbstractKeyInstance::serializeToObject($obj)
     */
    protected function serializeToObject(&$obj){
        $obj["p"] = $this->key_p->toHex();
        $obj["q"] = $this->key_q->toHex();
        $obj["g"] = $this->key_g->toHex();
        $obj["x"] = $this->key_x->toHex();
    }

    /**
     * @see AbstractSecretKey::sign($message)
     */
    public function sign($message)
    {
        $params = DSAKeyPair::$KEYSIZES[$this->keysize];
        $hash_alg = $params["hashAlg"];
        $hexlength = $params["q_bitlength"] / 4;

        $keys = Crypt_DSA::sign($message, $hash_alg, $this->key_p, $this->key_q, $this->key_g, $this->key_x);
        $signature = Utils::hex_lpad($keys["r"]->toHex(), $hexlength) . Utils::hex_lpad($keys["s"]->toHex(), $hexlength);
        return pack("H*" , $signature);
    }
}
