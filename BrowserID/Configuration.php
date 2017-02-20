<?php
namespace BrowserID;

use Symfony\Component\Yaml\Yaml;

/**
* Configuration
*
* LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @package    BrowserID
* @subpackage Configuration
* @author     Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
* @copyright  Alien-Scripts.de Benjamin Krämer
* @license    http://www.opensource.org/licenses/mit-license.html  MIT License
*/

/**
* Configuration
*
* An abstraction which contains various pre-set deployment
* environments and adjusts runtime configuration appropriate for
* the current environmnet (specified via Configuration::getInstance()->setEnvironment(...))
*
* The class can only be used through the singleton Configuration::getInstance()
*
* @package     BrowserID
* @subpackage  Configuration
* @author      Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
* @version     1.0.0
*/
class Configuration {

  /**#@+
  * @access public
  * @see Crypt_RSA::encrypt()
  * @see Crypt_RSA::decrypt()
  */
  /**
  * Use {@link http://en.wikipedia.org/wiki/Optimal_Asymmetric_Encryption_Padding Optimal Asymmetric Encryption Padding}
  * (OAEP) for encryption / decryption.
  *
  * Uses sha1 by default.
  *
  * @see Crypt_RSA::setHash()
  * @see Crypt_RSA::setMGFHash()
  */
  const CRYPT_RSA_ENCRYPTION_OAEP=1;
  /**
  * Use PKCS#1 padding.
  *
  * Although CRYPT_RSA_ENCRYPTION_OAEP offers more security, including PKCS#1 padding is necessary for purposes of backwards
  * compatability with protocols (like SSH-1) written before OAEP's introduction.
  */
  const CRYPT_RSA_ENCRYPTION_PKCS1=2;
  /**#@-*/
  /**#@+
  * @access public
  * @see Crypt_RSA::sign()
  * @see Crypt_RSA::verify()
  * @see Crypt_RSA::setHash()
  */
  /**
  * Use the Probabilistic Signature Scheme for signing
  *
  * Uses sha1 by default.
  *
  * @see Crypt_RSA::setSaltLength()
  * @see Crypt_RSA::setMGFHash()
  */
  const CRYPT_RSA_SIGNATURE_PSS=1;
  /**
  * Use the PKCS#1 scheme by default.
  *
  * Although CRYPT_RSA_SIGNATURE_PSS offers more security, including PKCS#1 signing is necessary for purposes of backwards
  * compatability with protocols (like SSH-2) written before PSS's introduction.
  */
  const CRYPT_RSA_SIGNATURE_PKCS1=2;
  /**#@-*/
  /**#@+
  * @access private
  * @see Crypt_RSA::createKey()
  */
  /**
  * ASN1 Integer
  */
  const CRYPT_RSA_ASN1_INTEGER=2;
  /**
  * ASN1 Bit String
  */
  const CRYPT_RSA_ASN1_BITSTRING=3;
  /**
  * ASN1 Sequence (with the constucted bit set)
  */
  const CRYPT_RSA_ASN1_SEQUENCE=48;
  /**#@-*/
  /**#@+
  * @access private
  * @see Crypt_RSA::Crypt_RSA()
  */
  /**
  * To use the pure-PHP implementation
  */
  const CRYPT_RSA_MODE_INTERNAL= 1;
  /**
  * To use the OpenSSL library
  *
  * (if enabled; otherwise, the internal implementation will be used)
  */
  const CRYPT_RSA_MODE_OPENSSL= 2;
  /**#@-*/
  /**#@+
  * @access public
  * @see Crypt_RSA::createKey()
  * @see Crypt_RSA::setPrivateKeyFormat()
  */
  /**
  * PKCS#1 formatted private key
  *
  * Used by OpenSSH
  */
  const CRYPT_RSA_PRIVATE_FORMAT_PKCS1= 0;
  /**
  * PuTTY formatted private key
  */
  const CRYPT_RSA_PRIVATE_FORMAT_PUTTY=1;
  /**
  * XML formatted private key
  */
  const CRYPT_RSA_PRIVATE_FORMAT_XML= 2;
  /**#@-*/
  /**#@+
  * @access public
  * @see Crypt_RSA::createKey()
  * @see Crypt_RSA::setPublicKeyFormat()
  */
  /**
  * Raw public key
  *
  * An array containing two Math_BigInteger objects.
  *
  * The exponent can be indexed with any of the following:
  *
  * 0, e, exponent, publicExponent
  *
  * The modulus can be indexed with any of the following:
  *
  * 1, n, modulo, modulus
  */
  const CRYPT_RSA_PUBLIC_FORMAT_RAW= 3;
  /**
  * PKCS#1 formatted public key (raw)
  *
  * Used by File/X509.php
  */
  const CRYPT_RSA_PUBLIC_FORMAT_PKCS1_RAW= 4;
  /**
  * XML formatted public key
  */
  const CRYPT_RSA_PUBLIC_FORMAT_XML= 5;
  /**
  * OpenSSH formatted public key
  *
  * Place in $HOME/.ssh/authorized_keys
  */
  const CRYPT_RSA_PUBLIC_FORMAT_OPENSSH= 6;
  /**
  * PKCS#1 formatted public key (encapsulated)
  *
  * Used by PHP's openssl_public_encrypt() and openssl's rsautl (when -pubin is set)
  */
  const CRYPT_RSA_PUBLIC_FORMAT_PKCS1= 7;
  /**#@-*/



  /**
  * The configuration selected through the environment
  *
  * @access private
  * @var array   The current configuration
  */
  private $g_config = NULL;

  /**
  * Singleton
  *
  * @access private
  * @static
  * @var Configuration The only instance of this class
  */
  private static $instance = NULL;


  /**
  * Disallow construction
  *
  * @access private
  */
  private function __construct() {

  }

  /**
  * Disallow cloning
  *
  * @access private
  */
  private function __clone() {

  }

  /**
  * Initialize the singleton instance
  *
  * @access private
  */
  private function __initInstance() {
    // production is the configuration that runs on the
    // public service (browserid.org)
    $this->g_config = Yaml::parse(file_get_contents(dirname(__DIR__).'/config/config.yml'));
    $this->g_config['url'] = $this->g_config['scheme'] . '://' . $this->g_config['hostname'] . $this->getPortForURL();
    $this->g_config['root']=dirname(dirname(__FILE__));
  }

  public function setBasePath($path){
    $this->g_config['base_path']=$path;
  }
  /**
  * Extract port from URL
  *
  * Return the port extension if the port is not the standard for the scheme
  *
  * @access private
  * @return string Port extension for URL
  */
  private function getPortForURL() {
    if ($this->g_config['scheme'] === 'https' && $this->g_config['port'] === '443') return '';
    if ($this->g_config['scheme'] === 'http' && $this->g_config['port'] === '80') return '';
    return ':' . $this->g_config['port'];
  }

  /**
  * Get singleton
  *
  * Returns an instance of the configuration singleton
  *
  * @access public
  * @static
  * @return Configuration The singleton
  */
  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new self;
      self::$instance->__initInstance();
    }
    return self::$instance;
  }

  /**
  * Getter
  *
  * Fetch a configuration parameter for the current environment
  *
  * @access public
  * @param string    $val    The configuration param to retrieve
  * @return string The value corresponding to $val
  */
  public function get($val) {
    if (!isset( $this->g_config[$val])){
      print "##".$val."\n";
      print $this->generateCallTrace();
      exit;
    }
    return $this->g_config[$val];
  }
  /**
  * Beautiful error
  * @return string error message to display
  */
  function generateCallTrace()
  {
    $e = new \Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++)
    {
      $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t" . implode("\n\t", $result)."\n";
  }

}
?>
