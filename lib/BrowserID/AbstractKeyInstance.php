<?php
namespace BrowserID;

/**
 * Abstract key instance
 *
 * A base class for all instanciated keys.
 *
 * @abstract
 * @package     BrowserID
 * @subpackage  Algs
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
abstract class AbstractKeyInstance extends AbstractKey {

    /**
     * Deserialize from object
     *
     * Deserialize parameters from the parameter object depending on the algorithmic specific implementation.
     *
     * @abstract
     * @access protected
     * @params array $obj Array of algorithmic specific parameters
     * @return AbstractKeyInstance
     */
    abstract protected function deserializeFromObject($obj);

    /**
     * Serialize to object
     *
     * Serializes parameters of the instance depending on the algorithmic specific
     * implementation into the parameter object.
     *
     * @abstract
     * @access protected
     * @params array $obj Array of algorithmic specific parameters
     */
    abstract protected function serializeToObject(&$obj);

    /**
     * Flatten key
     *
     * Extracts the parameters of the key.
     *
     * @access public
     * @return array
     */
    public function toSimpleObject()
    {
        $obj = array("algorithm" => $this->algorithm);
        $this->serializeToObject($obj);
        return $obj;
    }

    /**
     * Serialize key
     *
     * Serializes the key.
     *
     * @access public
     * @return string
     */
    public function serialize()
    {
        return json_encode($this->toSimpleObject());
    }
}
?>
