<?php
/*
 * User: tfleet
 * Date: 2013-05-11
 * Time: 4:01 PM
 */

namespace TFleet\AuthToken;


use Illuminate\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;

abstract class AbstractAuthTokenProvider implements AuthTokenProviderInterface {

  /**
   * @var \Illuminate\Encryption\Encrypter
   */
  protected $encrypter;

  /**
   * @var \TFleet\AuthToken\HashProvider
   */
  protected $hasher;

  /**
   * @return \TFleet\AuthToken\HashProvider
   */
  public function getHasher()
  {
    return $this->hasher;
  }


  /**
   * @param Encrypter $encrypter
   * @param HashProvider $hasher
   */
  function __construct(Encrypter $encrypter, HashProvider $hasher)
  {
    $this->encrypter = $encrypter;
    $this->hasher = $hasher;
  }

  protected  function generateAuthToken($publicKey = null,$userAgent = null)
  {
    if(empty($publicKey)) {
      $publicKey = $this->hasher->make();
    }

    $privateKey = $this->hasher->makePrivate($publicKey);

    $t = new \DateTime;

    return new AuthToken(null, $publicKey, $privateKey,$userAgent,$t,$t);
  }

  protected function verifyAuthToken(AuthToken $token) {

      return $this->hasher->check($token->getPublicKey(), $token->getPrivateKey());
  }



  /**
   * Returns serialized token.
   *
   * @param AuthToken $token
   * @return string
   */
  public function serializeToken(AuthToken $token)
  {
    $payload = $this->encrypter->encrypt(array(
      'id' => $token->getAuthIdentifier(),
      'key' => $token->getPublicKey(),
      'userAgent' => $token->getUserAgent())
    );

		$payload = str_replace(array('+', '/', '\r', '\n', '='), array('-', '_'), $payload);

    return $payload;
  }

  /**
   * Deserializes token.
   *
   * @param string $payload
   * @return AuthToken|null
   */
  public function deserializeToken($payload)
  {
    try {
      $payload = str_replace(array('-', '_'), array('+', '/'), $payload);
      $data = $this->encrypter->decrypt($payload);
    } catch (DecryptException $e) {
      return null;
    }

    if(empty($data['id']) || empty($data['key']) || empty($data['userAgent']) ) {
      return null;
    }

    $token = $this->generateAuthToken($data['key']);
    $token->setAuthIdentifier($data['id']);
    $token->setUserAgent($data['userAgent']);

    return $token;
  }
}