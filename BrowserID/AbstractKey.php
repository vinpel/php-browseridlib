<?php
namespace BrowserID;
/**
 * Abstract key
 *
 * A base class for all keys.
 *
 * @abstract
 * @package     BrowserID
 * @subpackage  Algs
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
abstract class AbstractKey {

    /**
     * Algorithm
     *
     * @access protected
     * @var string
     */
    protected $algorithm;

    /**
     * Keysize
     *
     * @access protected
     * @var int
     */
    protected $keysize;

    /**
     * Algorithm
     *
     * Gets the algorithm identifier as used in the web tokens header.
     *
     * @access public
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm . $this->keysize;
    }
}
?>
