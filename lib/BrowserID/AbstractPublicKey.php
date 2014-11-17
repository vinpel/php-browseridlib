<?php
namespace BrowserID;
/**
 * Abstract public key
 *
 * A base class for all public keys.
 *
 * @abstract
 * @package     BrowserID
 * @subpackage  Algs
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
 /**
  * Include Algorithms
  */
 require_once("./lib/BrowserID/algs.php");

 
abstract class AbstractPublicKey extends AbstractKeyInstance {
    /**
     * Verify message
     *
     * Verifies a message using a signature.
     *
     * @abstract
     * @access public
     * @param string $message The message to be verified
     * @param string $signature The signature to be validated
     * @return boolean
     */
    abstract public function verify($message, $signature);

    /**
     * Unflatten key
     *
     * Creates an key instance from the parameters.
     *
     * @abstract
     * @access public
     * @static
     * @param array $obj Parameters of the key
     * @return AbstractPublicKey
     */
    public static function fromSimpleObject($obj) {
        if (!isset($GLOBALS["ALGS"][$obj["algorithm"]]))
          throw new NotImplementedException("no such algorithm: " . $obj["algorithm"]);

        $publicKey = $GLOBALS["ALGS"][$obj["algorithm"]]->createPublicKey();
        $publicKey->algorithm = $obj["algorithm"];
        $publicKey->deserializeFromObject($obj);
        return $publicKey;
    }

    /**
     * Deserialize key
     *
     * Deserializes the key.
     *
     * @abstract
     * @access public
     * @static
     * @param string $str Serialized parmeters of the key
     * @return AbstractPublicKey
     */
    public static function deserialize($str) {
        return AbstractPublicKey::fromSimpleObject(json_decode($str, true));
    }
}
