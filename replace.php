<?php

class plgAcymailingReplace extends JPlugin
{

	function plgAcymailingReplace(&$subject, $config){
		parent::__construct($subject, $config);

		//This is just to fix a bug with an old version of Joomla where the params where not loaded
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'replace');
			$this->params = new JParameter( $plugin->params );
		}
	}

	 //This function will be triggered on the preview screen and during the send process to replace personal global tags
	 //Any tag which is not user specific should be replaced by this function in order to optimize the process
	 //The last argument $send indicates if the message will be send (set to true) or displayed as a preview (set to false)
	function acymailing_replacetags(&$email,$send = true){
		
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__rereplacer'));
		$db->setQuery($query);
		$row = $db->loadObjectList();
		//You should replace tags in the three following variables:
		foreach($row as $replacer){
			$email->body = str_replace($replacer->search, str_replace('[[comma]]', ',', $replacer->replace), $email->body); //HTML version of the Newsletter
			$email->altbody = str_replace($replacer->search, str_replace('[[comma]]', ',', $replacer->replace), $email->altbody); //text version of the Newsletter
			$email->subject = str_replace($replacer->search, str_replace('[[comma]]', ',', $replacer->replace), $email->subject); //Subject of the Newsletter
		}
			
		
		
	}

	// //This function will be triggered during the send process and on the preview screen to replace personal tags (user specific information)
	// //If you don't want to replace personal tags (specific to the user), then you can delete this function
	// //The last argument $send indicates if the message will be send (set to true) or displayed as a preview (set to false)
	// function acymailing_replaceusertags(&$email,&$user,$send = true){
	// 	//You should replace tags in the three following variables:
	// 	$email->body = str_replace('{tagexampleuser}','my string for'.$user->email,$email->body); //HTML version of the Newsletter
	// 	$email->altbody = str_replace('{tagexampleuser}','my string for'.$user->email,$email->altbody); //text version of the Newsletter
	// 	$email->subject = str_replace('{tagexampleuser}','my string for'.$user->email,$email->subject); //Subject of the Newsletter
	// }

	//This function is triggered when an auto-Newsletter has to be generated.
	//You can replace tags from there but the main purpose is to block or not the Newsletter generation.
	//For example you may want to block the Newsletter if you don't have a new content on your website
	function acymailing_generateautonews(&$email){

		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		//Do you want to generate the Newsletter from the auto-Newsletter?
		$generate = false;
		if(!$generate){
			$return->status = false;
			$return->message = 'The generation is blocked because...';
		}
		return $return;
	}

}//endclass