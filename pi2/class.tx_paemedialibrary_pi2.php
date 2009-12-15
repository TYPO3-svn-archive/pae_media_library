<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alban Cousinie <extensions@mind2machine.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Single media display' for the 'pae_media_library' extension.
 *
 * @author	Alban Cousinie <extensions@mind2machine.com>
 * @package	TYPO3
 * @subpackage	tx_paemedialibrary
 */
class tx_paemedialibrary_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_paemedialibrary_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_paemedialibrary_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'pae_media_library';	// The extension key.
	var $pi_checkCHash = true;
	
	//custom vars
	var $lConf;
	var $session;
	var $textAreaID=0;
	
	
	/**
	 * initializes the flexform and all config options 
	 */
	function init(){
	 $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
	 $this->lConf = array(); // Setup our storage array...
	 // Assign the flexform data to a local variable for easier access
	 $piFlexForm = $this->cObj->data['pi_flexform'];
	 // Traverse the entire array based on the language...
	 // and assign each configuration option to $this->lConf array...
	 foreach ( $piFlexForm['data'] as $sheet => $data )
	  foreach ( $data as $lang => $value )
	   foreach ( $value as $key => $val )
		$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
	}
	
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		
		$this->pi_initPIflexform(); // get the flexform data of the plugin
		$this->init(); //parse the flexform data and put it in $this->lConf array
		$this->pi_USER_INT_obj=1; //disable caching
		
		//HTML output
		global $htmlCode; 
		$htmlCode= array();
		
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);
		
		//initializing session
		$this->session = $GLOBALS["TSFE"]->fe_user->getKey('ses',$this->sessionName);
		
		$this->session['prefixId']=$this->prefixId;
		
		//STARTING POINTS
		//Retreiving startingpoint
	 	$startingPoints = $this->lConf['pages'];
				
		//Retreiving recursivity
		$recursive = $this->lConf['recursive'];
		if(!isset($this->lConf['recursive']) | $this->lConf['recursive']=="")$recursive = 0;
		
		//establishing list of all pids to be looked for records according to recursivity settings
		$this->session['selectedPids'] = "";
		$startingPointsArray = explode(",",$startingPoints);
		foreach($startingPointsArray as $index => $root)
		 	$this->session['selectedPids'] .= (($index == 0)?"":",").$this->pi_getPidList($this->lConf['pages'],$this->lConf['recursive']);
		
		//$htmlCode[] .= "session=".$this->session['selectedPids'];
		 
		//current language displayed in the frontend
		$this->session['current_sys_language_uid'] = $GLOBALS['TSFE']->sys_language_uid;
		
		//sys_language_uid for the default language
		$this->session['default_sys_language_uid'] = $extConf['default_sys_language_uid'];
		
		// records for the default language are stored in the database with sys_language_uid == 0
		// thus we have to correct the current_sys_language_uid value to 0 if it matches the language currently displayed
		if($this->session['current_sys_language_uid'] == $this->session['default_sys_language_uid']) $this->session['current_sys_language_uid'] = 0;
				
		//extracting the &L parameter from piVars as well
		parse_str($GLOBALS['TSFE']->linkVars);
		$this->session['L'] = $L; //now the variable named $L has been initialized by parse_str 
		
		// PLUGIN INTERNAL SETTINGS
		// Forging this player unique identifier.
		$pluginName = $this->prefixId;
		$contentUID = $this->cObj->data['uid'];	
		$currentPageUID =  $GLOBALS['TSFE']->id;
		
		//ISO2 code for extension current language (eg: fr, us, es...)
		$langKey = $GLOBALS['TSFE']->tmpl->setup['config.']['language'];
		
		
		$cssStylesheet = trim($this->conf['cssStylesheet']);
		
		
		$stylesheetPath = ($cssStylesheet != "")?$cssStylesheet : t3lib_extMgm::siteRelPath($this->extKey).'res/'.$this->extKey.'.css';
		
		//Javascripts & stylesheets
		$GLOBALS['TSFE']->additionalHeaderData[$prefixId ] ='
		<link rel="stylesheet" href="'.$stylesheetPath.'" type="text/css" media="screen" />
		<script src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/swfobject.js"></script>
		';
		
		//URL parameters 
		$action = 			($this->piVars['action']) ? 	$this->piVars['action']:'default';
		$record_uid = 		($this->piVars['uid']) ? 		intval($this->piVars['uid']):'';
		
		//Define the flash file to be used
		$swfPath = t3lib_extMgm::siteRelPath($this->extKey)."res/player.swf";
					
		//relative path to the images related to the website root directory
		$imgSiteRelPath = t3lib_extMgm::siteRelPath($this->extKey)."res/img/";
		
		
		$flashCode = array();
		
		$contentID = $prefixId.'_'.$contentUID;
		// Flash code
		$flashCode[] = '
			<div id="'.$contentID.'"></div>
			<script type="text/javascript">
				// <![CDATA[
				
				var flashvars = {
				  name1: "hello",
				  name2: "world",
				  name3: "foobar"
				};
				var params = {
				  menu: "false"
				};
				var attributes = {
				  id: "myDynamicContent",
				  name: "myDynamicContent"
				};
				
				swfobject.embedSWF("'.$swfPath .'", "'.$contentID.'", "300", "120", "9.0.0","expressInstall.swf", flashvars, params, attributes);

			// ]]>
			</script>';
			
			
		
		
		$htmlOutput = $htmlCode;
		$htmlOutput = array_merge($htmlOutput,$flashCode);
		
		$GLOBALS["TSFE"]->fe_user->setKey('ses',$this->sessionName,$this->session);
		$GLOBALS["TSFE"]->storeSessionData();

		return $this->pi_wrapInBaseClass(implode(chr(10),$htmlOutput));
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pae_media_library/pi2/class.tx_paemedialibrary_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pae_media_library/pi2/class.tx_paemedialibrary_pi2.php']);
}

?>