<?php
namespace BrowserID;
/**
* Abstract secret key
*
* A base class for all secret keys.
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

use Codeception\Util\Debug;
abstract class AbstractSecretKey extends AbstractKeyInstance {
  /**
  * Sign message
  *
  * Generate a signature for the message.
  *
  * @abstract
  * @access public
  * @param string $message The message
  * @return string The signature
  */
  abstract public function sign($message);

  /**
  * Unflatten key
  *
  * Creates an key instance from the parameters.
  *
  * @abstract
  * @access public
  * @static
  * @param array $obj Parameters of the key
  * @return AbstractSecretKey
  */
  public static function fromSimpleObject($obj) {
    $algs = include(__DIR__."/algs.php");
    if (!isset($algs[$obj["algorithm"]])){
      throw new \Exception("no such algorithm: " . $obj["algorithm"]);
    }
    $secretKey =$algs[$obj["algorithm"]]->createSecretKey();
    $secretKey->algorithm = $obj["algorithm"];
    $secretKey->deserializeFromObject($obj);
    return $secretKey;
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
  * @return AbstractSecretKey
  */
  public static function deserialize($str) {
    return AbstractSecretKey::fromSimpleObject(json_decode($str, true));
  }
}
?>
