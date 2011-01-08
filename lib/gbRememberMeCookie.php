<?php
/*
 * This file is part of the gbRememberMe package.
 * (c) Gabriel Birke <birke@d-scribe.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This is the class for getting and setting the persistent and one-time token from the cookie string.
 *
 * @package    gbRememberMe
 * @subpackage plugin
 * @author     Gabriel Birke <birke@d-scribe.de>
 * @version    SVN: $Id$
 */
class gbRememberMeCookie {
  
  protected $token = "";
  protected $persistentToken = "";
  protected $cookieIsValid = false;
  
  /**
   * @param string $cookieString A string, containing two tokens, 
   *        separated with a "|"
   */
  public function __construct($cookieString)
  {
    $cookieValues = explode('|', $cookieString);
    if(count($cookieValues) != 2) {
      return;
    }
    $this->cookieIsValid = true;
    $this->persistentToken = $cookieValues[0];
    $this->token = $cookieValues[1];
  }
  
  /**
   * @return string
   */
  public function getPersistentToken()
  {
    return $this->persistentToken;
  }
  
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  
  /**
   * @param string $value
   */
  public function setPersistentToken($value)
  {
    $this->persistentToken = $value;
  }
  
  /**
   * @param string $value
   */
  public function setToken($value)
  {
    $this->token = $value;
  }
  
  /**
   * Returns true if the cookie string contained two tokens
   * @return boolean
   */
  public function isValid()
  {
    return $this->cookieIsValid;
  }
  
  /**
   * @return string
   */
  public function __toString()
  {
    return $this->persistentToken.'|'.$this->token;
  }
  
  /**
   * Create a new instance from the cookie request.
   * @return gbRememberMeCookie
   */
  public static function createInstance()
  {
    $cookieName = sfConfig::get('app_gb_remember_me_plugin_cookie_name', 'gbRemember');
    $cookieValue = sfContext::getInstance()->getRequest()->getCookie($cookieName, '');
    return new gbRememberMeCookie($cookieValue);
  }
  
}
