<?php
/*
 * This file is part of the gbRememberMe package.
 * (c) Gabriel Birke <birke@d-scribe.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This filter logs in the user when he sends a cookie with tokens that match
 * tokens in the database.
 *
 * @package    gbRememberMe
 * @subpackage plugin
 * @author     Gabriel Birke <birke@d-scribe.de>
 * @version    SVN: $Id$
 */
class gbRememberMeFilter extends sfFilter
{
  /**
   * Executes the filter chain.
   *
   * @param sfFilterChain $filterChain
   */
  public function execute($filterChain)
  {
    $cookieName = sfConfig::get('app_gb_remember_me_plugin_cookie_name', 'gbRemember');
    if (
      $this->isFirstCall()
      &&
      $this->context->getUser()->isAnonymous()
      &&
      $cookieValue = $this->context->getRequest()->getCookie($cookieName)
    )
    {
      $cookie = new gbRememberMeCookie($cookieValue);
      // Ignore invalid cookie values
      if (!$cookie->isValid()) {
        sfContext::getInstance()->getResponse()->setCookie($cookieName, '', time() - 65000);
        $filterChain->execute();
        return;
      }
      $q = Doctrine_Core::getTable('gbRememberMeToken')->createQuery('t')
            ->leftJoin('t.User u')
            ->andWhere('t.persistent_token = ?', $cookie->getPersistentToken())
            ->andWhere('t.expires > NOW()')
      ;
      if ($q->count())
      {
        $token = $q->fetchOne();
        if($token->checkTokenIsValid($cookie->getToken())) {
          $user = $this->context->getUser();
          $user->signIn($token->User, true);
          $user->setAttribute('logged_in_with_remember_me', true);
        }
        else {
          sfContext::getInstance()->getLogger()->notice("Invalid 'remember me' token.");
          if (in_array('gbSecurity', sfConfig::get('sf_enabled_modules', array())))
          {
            $this->redirectToSecurityWarning();
          }
        }
      }
    }
    $filterChain->execute();
  }
  
  protected function redirectToSecurityWarning()
  {
    $this->getContext()
      ->getController()
      ->redirect(array('sf_route' => 'gbSecurity/warning'), null, 302);
    throw new sfStopException();
  }
  
}
