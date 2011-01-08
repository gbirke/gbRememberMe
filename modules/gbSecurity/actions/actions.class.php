<?php
/*
 * This file is part of the gbRememberMe package.
 * (c) Gabriel Birke <birke@d-scribe.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * gbSecurity actions.
 *
 * @package    gbRememberMePlugin
 * @subpackage actions
 * @author     Gabriel Birke <birke@d-scribe.de>
 */
class gbSecurityActions extends sfActions
{
  public function executeWarning(sfWebRequest $request)
  {
    $cookie = gbRememberMeCookie::createInstance();
    $persistentToken = $cookie->getPersistentToken();
    $tokenTable = gbRememberMeTokenTable::getInstance();
    $token = gbRememberMeTokenTable::getInstance()->createQuery('t')
      ->leftJoin('t.User u')
      ->where('t.persistent_token = ?', $persistentToken)
      ->fetchOne();
    if(!$token || !$token->User) {
      $this->deleted = 0;
      return;
    }
    // Delete all tokens and the cookie
    $this->deleteTokensForUser($token->User->getId());
    sfContext::getInstance()->getResponse()->setCookie($cookieName, '', time() - $expiration_age);
  }
  
  public function executeOverview($value='')
  {
    $this->logins = gbRememberMeTokenTable::getInstance()->createQuery()
      ->delete('gbRememberMeToken t')
      ->where('user_id = ?', $this->getUser()->getGuardUser()->getId())
      ->execute();
  }
  
  public function executeRemovetokens()
  {
    $this->deleteTokensForUser($this->getUser()->getGuardUser()->getId());
    $this->redirect('gbSecurity/overview');
  }
  
  protected function deleteTokensForUser($user_id)
  {
    $this->deleted = gbRememberMeTokenTable::getInstance()->createQuery()
      ->delete('gbRememberMeToken t')
      ->where('user_id = ?', $user_id)
      ->execute();
  }
  
}