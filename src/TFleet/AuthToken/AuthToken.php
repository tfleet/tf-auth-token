<?php
/*
 * User: tfleet
 */

namespace TFleet\AuthToken;


use Illuminate\Support\Contracts\ArrayableInterface;

class AuthToken implements ArrayableInterface {

  protected $authIdentifier;
  protected $publicKey;
  protected $privateKey;
  protected $userAgent;
  protected $createdAt;
  protected $updatedAt;

  function __construct($authIdentifier, $publicKey, $privateKey,$userAgent,$createdAt,$updatedAt)
  {
    $this->authIdentifier = $authIdentifier;
    $this->publicKey = $publicKey;
    $this->privateKey = $privateKey;
    $this->userAgent = $userAgent;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public function getAuthIdentifier()
  {
    return $this->authIdentifier;
  }

  public function setAuthIdentifier($authIdentifier)
  {
    $this->authIdentifier = $authIdentifier;
  }

  public function getPrivateKey()
  {
    return $this->privateKey;
  }

  public function getPublicKey()
  {
    return $this->publicKey;
  }

  public function setPrivateKey($privateKey)
  {
    $this->privateKey = $privateKey;
  }

  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }

  /**
   * @return mixed
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }

  /**
   * @param mixed $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }

  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray()
  {
    return array(
      'auth_identifier' => $this->authIdentifier,
      'public_key' => $this->publicKey,
      'private_key' => $this->privateKey,
      'user_agent' => $this->userAgent,
      'created_at' => $this->createdAt,
      'updated_at' => $this->updatedAt,
    );
  }


}