<?php
/*
 * This file is part of the gbRememberMe package.
 * (c) Gabriel Birke <birke@d-scribe.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class extends the sfGuardSecurityUser class with its own sign in logic.
 *
 * @package    gbRememberMe
 * @subpackage plugin
 * @author     Gabriel Birke <birke@d-scribe.de>
 * @version    SVN: $Id$
 */
class gbSecurityUser extends sfGuardSecurityUser {
  
  /**
   * Signs in the user on the application.
   *
   * @param sfGuardUser $user The sfGuardUser 
   * @param boolean $remember Whether or not to remember the user
   * @param Doctrine_Connection $con A Doctrine_Connection object
   */
  public function signIn($user, $remember = false, $con = null)
  {
    parent::signIn($user, false, $con);
    if ($remember)
    {
      $cookieName = sfConfig::get('app_gb_remember_me_plugin_cookie_name', 'gbRemember');
      $expiration_age = sfConfig::get('app_gb_remember_me_plugin_token_expiration_age', 30 * 24 * 3600);
      $cookie = gbRememberMeCookie::createInstance();
      // Modify existing token or create new token?
      if ($cookie->isValid()) {
        $tokenRecords = gbRememberMeTokenTable::getInstance()
          ->findByPersistentToken($cookie->getPersistentToken());
        if($tokenRecords) {
          $tokenRecord = $tokenRecords->getFirst();
        }
      }
      // Cookie was invalid or no token was found in DB
      if (!$tokenRecord) {
        $tokenRecord = $this->getNewTokenRecord($user);
        $cookie->setPersistentToken($tokenRecord->getPersistentToken());
      }
      
      $newToken = $this->generateRandomKey();
      $cookie->setToken($newToken);
      $expire = time() + $expiration_age;
      
      $tokenRecord->setToken($newToken);
      $tokenRecord->setExpires($expire); 
      $tokenRecord->save();
      sfContext::getInstance()->getResponse()->setCookie($cookieName, (string) $cookie, $expire, '/', null, false, true);
    }
  }
  
  /**
   * Signs out the user and deletes the remember me cookies and related DB record.
   */
  public function signOut()
  {
    parent::signOut();
    $cookieName = sfConfig::get('app_gb_remember_me_plugin_cookie_name', 'gbRemember');
    $expiration_age = sfConfig::get('app_gb_remember_me_plugin_token_expiration_age', 30 * 24 * 3600);
    $cookie = gbRememberMeCookie::createInstance();
    if ($cookie->isValid()) {
      gbRememberMeTokenTable::getInstance()
        ->findByPersistentToken($cookie->getPersistentToken())
        ->delete();
    }
    sfContext::getInstance()->getResponse()->setCookie($cookieName, '', time() - $expiration_age);
  }
  
  protected function getNewTokenRecord($user)
  {
    $tokenRecord = new gbRememberMeToken();
    $persistentToken = $this->generateRandomKey();
    $tokenRecord->setPersistentToken($persistentToken);
    $tokenRecord->setUser($user);
    return $tokenRecord;
  }
  
}