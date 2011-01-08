<?php

/*
 * This file is part of the gbRememberMe package.
 * (c) Gabriel Birke <birke@d-scribe.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Clear tokens from the database
 *
 * @package    gbRememberMe
 * @subpackage task
 * @author     Gabriel Birke <birke@d-scribe.de>
 * @version    SVN: $Id$
 */
class gbRememberMeClearTokensTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('all', null, sfCommandOption::PARAMETER_NONE, 'Delete all tokens.'),
    ));

    $this->namespace = 'remember-me';
    $this->name = 'clear';
    $this->briefDescription = 'Delete "remember me" tokens from the database.';

    $this->detailedDescription = <<<EOF
The [remember_me:clear|INFO] task deletes tokens from the database.

By default it deletes the expired tokens. Use the [--all|INFO] option to delete 
all tokens.

EOF;
  }

  /**
   * Executes the task.
   *
   * @param array $arguments An array of arguments
   * @param array $options An array of options
   * @throws sfException
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $q = Doctrine_Query::create()->delete('gbRememberMeToken t');
    
    if(empty($options['all'])) {
      $q->where('t.expires < NOW()');
    }
    $count = $q->execute();

    $this->logSection('remember-me', sprintf('%d token(s) deleted. ', $count));
  }
}