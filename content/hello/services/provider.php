<?php

/**
* @copyright Copyright (C) 2012 Jean-Luc TRYOEN. All rights reserved.
* @license GNU/GPL
*
* Version 1.0
*
* @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* @link        https://www.jltryoen.fr
*/

use JLTRY\Plugin\Content\Hello\Extension\Hello;
use Joomla\CMS\Extension\PluginInterface;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\Log\Log;




return new class() implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.3.0
     */
     public function register(Container $container)
        {
            $container->set(
                PluginInterface::class,
                function (Container $container) {   
                    $config = (array) PluginHelper::getPlugin('content', 'hello');
                    $subject = $container->get(DispatcherInterface::class);
                    $app = Factory::getApplication();
                    $plugin = new Hello($subject, $config);
                    $plugin->setApplication($app);
                    return $plugin;
                }
            );
    }
};