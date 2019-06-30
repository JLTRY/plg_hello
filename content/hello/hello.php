<?php
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

jimport( 'joomla.plugin.plugin' );
define('PF_REGEX_HELLO_PATTERN', "#{hello(.*?)}#s");
/**
* Hello Content Plugin
*
*/
class plgContentHello extends JPlugin
{

	/**
	* Constructor
	*
	* @param object $subject The object to observe
	* @param object $params The object that holds the plugin parameters
	*/
	function __construct( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	* Example prepare content method in Joomla 1.5
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	* @param int The 'page' number
	*/
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		return $this->OnPrepareRow($article);			
	}

 	/**
	* Example prepare content method in Joomla 1.6/1.7/2.5
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	*/   
	function onContentPrepare($context, &$row, &$params, $page = 0){
		return $this->OnPrepareRow($row);
	}

		
	
    function onPrepareRow(&$row) 
	{
		//Escape fast
        if (!$this->params->get('enabled', 1)) {
            return true;
        }
 		if ( strpos( $row->text, '{hello' ) === false ) {
            return true;
		}		
		preg_match_all(PF_REGEX_HELLO_PATTERN, $row->text, $matches);
		// Number of plugins
        $count = count($matches[0]);		
        // plugin only processes if there are any instances of the plugin in the text
        if ($count) {
			
			$document = JFactory::getDocument();
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
					$row->text	= preg_replace('#{hello.*}#', $p_content, $row->text);				
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
		return $content;			
	}
}
