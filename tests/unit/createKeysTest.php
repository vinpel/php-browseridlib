<?php
use BrowserID\Algs\RSAKeyPair;
use BrowserID\Secrets;


class createKeysTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
    * Tests
    */
    public function testMe()
    {
      $name = 'toto';//substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,10);
      $keysize = 256;// "Allowed keysizes: 64, 128, 256!\r\n";

      // Generate keypair:
      \Codeception\Util\Debug::debug("Generate key pair with keysize $keysize $name...");
      $pair = RSAKeyPair::generate($keysize);
      //\Codeception\Util\Debug::debug($pair);
      $this->tester->assertInstanceOf('BrowserID\Algs\RSAKeyPair',$pair,'Keys were generated!');

//      \Codeception\Util\Debug::debug($pair);

      // Write secret key to file:
      \Codeception\Util\Debug::debug("Write Secret Key...");
      $pathSecretKey = Secrets::getPathSecretKey($name);;
      \Codeception\Util\Debug::debug(var_dump($pair->getSecretKey()->serialize()));exit;

      $handle = fopen($pathSecretKey, "w+");
      fwrite($handle, $pair->getSecretKey()->serialize());
      fclose($handle);
      \Codeception\Util\Debug::debug("Secret Key was written to " . $pathSecretKey);

      // Write public key to file:
      echo "Write Public Key...\r\n";
      $pathPublicKey = Secrets::getPathPublicKey($name);
      $public = array("public-key"=>json_decode($pair->getPublicKey()->serialize(), true));
      $token = new WebToken($public);
      $handle = fopen($pathPublicKey, "w+");
      fwrite($handle, $token->serialize($pair->getSecretKey()));
      fclose($handle);
      echo "Public Key was written to " . $pathPublicKey . "\r\n";

    }
}
