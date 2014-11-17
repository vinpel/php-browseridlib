<?php
namespace BrowserID;
/**
 * Malformed webtoken
 *
 * The webtoken is not well-formed
 *
 * @package     BrowserID
 * @subpackage  WebToken
 * @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @version     1.0.0
 */
class MalformedWebTokenException extends Exception {

    /**
     * @access public
     * @param string    $message    The error message
     * @param int       $code       An error code
     */
    public function __construct($message, $code = 0) {
        parent::__construct("Malformed JSON web token: " . $message, $code);
    }
}
?>
