<?php

/**
 * PlugingbRememberMeTokenTable
 * 
 * @package    gbRememberMe
 * @subpackage model
 * @author     Gabriel Birke <birke@d-scribe.de>
 * @version    SVN: $Id$
 */
class PlugingbRememberMeTokenTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object PlugingbRememberMeTokenTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PlugingbRememberMeToken');
    }
}