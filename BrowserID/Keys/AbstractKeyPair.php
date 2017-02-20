<?php
namespace BrowserID\Keys;

/**
 * Abstract key pair
 *
 * A base class for all key pairs.
 *
 * @abstract
 * @package     BrowserID
 * @subpackage  Algs
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
abstract class AbstractKeyPair extends AbstractKey {

    /**
     * Public key
     *
     * @access protected
     * @var AbstractPublicKey
     */
    protected $publicKey;

    /**
     * Secret key
     *
     * @access protected
     * @var AbstractSecretKey
     */
    protected $secretKey;

    /**
     * Creates public key
     *
     * Creates a public key using the algorithm of the extended class.
     *
     * @abstract
     * @access public
     * @return AbstractPublicKey
     */
    abstract public function createPublicKey();

    /**
     * Creates secret key
     *
     * Creates a secret key using the algorithm of the extended class.
     *
     * @abstract
     * @access public
     * @return AbstractSecretKey
     */
    abstract public function createSecretKey();

    /**
     * Get public key
     *
     * Gets the public key of this key pair.
     *
     * @access public
     * @return AbstractPublicKey
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Get secret key
     *
     * Gets the secret key of this key pair.
     *
     * @access public
     * @return AbstractSecretKey
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }
}
?>
