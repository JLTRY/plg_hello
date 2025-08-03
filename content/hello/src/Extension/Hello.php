<?php
namespace JLTRY\Plugin\Content\Hello\Extension;
/**
 * 
* @copyright Copyright (C) 2012 Jean-Luc TRYOEN. All rights reserved.
* @license GNU/GPL
*
* Version 1.0
*
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

define('PF_REGEX_HELLO_PATTERN', "#{hello(.*?)}#s");
/**
* Hello Content Plugin
*
*/
class Hello extends CMSPlugin implements SubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
                'onContentPrepare' => 'onContentPrepare'
                ];
    }


    function onContentPrepare(Event $event)
    {
        //Escape fast
        if (!$this->params->get('enabled', 1)) {
            return;
        }
        if (!$this->getApplication()->isClient('site')) {
            return;
        }
        // use this format to get the arguments for both Joomla 4 and Joomla 5
        // In Joomla 4 a generic Event is passed
        // In Joomla 5 a concrete ContentPrepareEvent is passed
        [$context, $row, $params, $page] = array_values($event->getArguments());
         if ( strpos( $row->text, '{hello' ) === false ) {
            return true;
        }
        preg_match_all(PF_REGEX_HELLO_PATTERN, $row->text, $matches);
        // Number of plugins
        $count = count($matches[0]);
        // plugin only processes if there are any instances of the plugin in the text
        if ($count) {

            $document = Factory::getDocument();
            for ($i = 0; $i < $count; $i++)
            {
                $result = array();
                if (@$matches[1][$i]) {
                    $inline_params = $matches[1][$i];
                
                    $pairs = explode(' ', trim($inline_params));
                    foreach ($pairs as $pair) {
                        $pos = strpos($pair, "=");
                        $key = substr($pair, 0, $pos);
                        $value = substr($pair, $pos + 1);
                        $result[$key] = $value;
                    }
                    $p_content = $this->hello($result);
                    $row->text = str_replace("{hello" . $matches[1][$i] . "}", $p_content, $row->text);
                }
                else
                {
                    $p_content = $this->hello($result);
                    $row->text    = preg_replace('#{hello.*}#', $p_content, $row->text);
                }
            }

        }
        else
        {
            $row->text = str_replace("{hello ", "erreur de syntaxe: {hello style=normal|bold|italic}", $row->text);
        }
        return true;
    }



     /**
    * Function to insert hello world
    *
    * Method is called by the onContentPrepare or onPrepareContent
    *
    * @param string The text string to find and replace
    */    
    function hello( $params )
    {
        $content = "Hello World";
        if (isset($params['style']))
        {
            switch($params['style'])
            {
                case 'bold':
                    $content = '<b>' . $content . '</b>';
                    break;
                case 'normal':
                    break;
                case 'italic':
                    $content = '<I>' . $content . '</I>';
                    break;
            }
        }
        //$plugin =& JPluginHelper::getPlugin('content', '');
        return $content;
    }
}
