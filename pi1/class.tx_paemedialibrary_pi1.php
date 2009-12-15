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
class tx_paemedialibrary_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_paemedialibrary_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_paemedialibrary_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'pae_media_library';	// The extension key.
	var $pi_checkCHash = true;
	
	//custom vars
	var $lConf;
	var $textAreaID=0;
	var $TSSettingsOverridePluginSettings = 0;
	var $L;
	//used to strip line feeds from data
	var $stripCR   = array("\r\n", "\n", "\r");
	
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
		if($piFlexForm != null){
		 foreach ( $piFlexForm['data'] as $sheet => $data )
		  foreach ( $data as $lang => $value )
		   foreach ( $value as $key => $val )
			$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
		}
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
		$this->pi_USER_INT_obj=1; //enable caching
		
		//HTML output
		global $htmlCode; 
		$htmlCode= array();
		
		
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		
		//intercept and output XML playlist if the given page type matches
		if($GLOBALS['TSFE']->type == $extConf['typeNum']){
			//print_r($_REQUEST);
			return $this->writeXML();
		}
		
		//current language displayed in the frontend
		$current_sys_language_uid = $GLOBALS['TSFE']->sys_language_uid;
		
		// $extConf['default_sys_language_uid'] = sys_language_uid for the default language
		
		// records for the default language are stored in the database with sys_language_uid == 0
		// thus we have to correct the current_sys_language_uid value to 0 if it matches the language currently displayed
		if($GLOBALS['TSFE']->sys_language_uid == $extConf['default_sys_language_uid']) $current_sys_language_uid = 0;
				
		
		// PLUGIN INTERNAL SETTINGS
		// Forging this player unique identifier.
		$pluginName = $this->prefixId;
		$contentUID = $this->cObj->data['uid'];	
		$currentPageUID =  $GLOBALS['TSFE']->id;
		$contentID = $this->prefixId.'_'.$contentUID;
		
		//ISO2 code for extension current language (eg: fr, us, es...)
		$langKey = $GLOBALS['TSFE']->tmpl->setup['config.']['language'];
		
		$stylesheetPath = trim($this->conf['cssFile']);
		
		//Define the flash file to be used
		$resPath = t3lib_extMgm::siteRelPath($this->extKey)."res/";
					
		//relative path to the images related to the website root directory
		$imgSiteRelPath = t3lib_extMgm::siteRelPath($this->extKey)."res/img/";
		
		//Javascripts & stylesheets
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '<link rel="stylesheet" href="'.$stylesheetPath.'" type="text/css" media="screen" /><script src="'.$resPath.'swfobject.js"></script>';
		
		//URL parameters 
		$action = 			($this->piVars['action']) ? 	$this->piVars['action']:'default';
		$record_uid = 		($this->piVars['uid']) ? 		intval($this->piVars['uid']):'';
		
		
		//add error message to this variable for error display
		$errorMessage="";
		//Add text to this variable for debug output
		$debug="";
		//stores the flash HTML
		$flashCode = array();
		
		$comma   = array(",");
		
		
		//Managing player settings between Typoscript and Plugin flexfoms settings
		
		$this->TSSettingsOverridePluginSettings = intval($this->conf['TSSettingsOverridePluginSettings']);
		
		$flash_version_req 	= $this->getSetting($this->conf['flash_version_req'], $this->lConf['flash_version_req'], "9.0115.0");
		$width 				= $this->getSetting($this->conf['width'], $this->lConf['width'], "320");
		$height			 	= $this->getSetting($this->conf['height'], $this->lConf['height'], "240");
		$playertype 		= $this->getSetting($this->conf['JWPlayer.']['playertype'], $this->lConf['jw_playertype'], "regular");
		$show_meta	 		= $this->getSetting($this->conf['JWPlayer.']['show_meta'], $this->lConf['jw_show_meta'], "true");
		$controlbar 		= $this->getSetting($this->conf['JWPlayer.']['controlbar'], $this->lConf['jw_controlbar'], "bottom");
		$backcolor 			= $this->getSetting($this->conf['JWPlayer.']['backcolor'], $this->lConf['jw_backcolor'], "");
		$frontcolor 		= $this->getSetting($this->conf['JWPlayer.']['frontcolor'], $this->lConf['jw_frontcolor'], "");
		$lightcolor 		= $this->getSetting($this->conf['JWPlayer.']['lightcolor'], $this->lConf['jw_lightcolor'], "");
		$screencolor 		= $this->getSetting($this->conf['JWPlayer.']['screencolor'], $this->lConf['jw_screencolor'], "");
		$smoothing 			= $this->getSetting($this->conf['JWPlayer.']['smoothing'], $this->lConf['jw_smoothing'], "true");
		$stretching 		= $this->getSetting($this->conf['JWPlayer.']['stretching'], $this->lConf['jw_stretching'], "uniform");
		$icons 				= $this->getSetting($this->conf['JWPlayer.']['icons'], $this->lConf['jw_icons'], "true");
		$logo 				= $this->getSetting($this->conf['JWPlayer.']['logo'], $this->lConf['jw_logo'], "");
		$playlist			= $this->getSetting($this->conf['JWPlayer.']['playlist'], $this->lConf['jw_playlist'], "none");
		$playlistsize		= $this->getSetting($this->conf['JWPlayer.']['playlistsize'], $this->lConf['jw_playlistsize'], "180");
		$skin				= $this->getSetting($this->conf['JWPlayer.']['skin'], $this->lConf['jw_skin'], "");
		$autostart			= $this->getSetting($this->conf['JWPlayer.']['autostart'], $this->lConf['jw_autostart'], "false");
		$bandwidth			= $this->getSetting($this->conf['JWPlayer.']['bandwidth'], $this->lConf['jw_bandwidth'], "5000");
		$bufferlength		= $this->getSetting($this->conf['JWPlayer.']['bufferlength'], $this->lConf['jw_bufferlength'], "1");
		$displayclick		= $this->getSetting($this->conf['JWPlayer.']['displayclick'], $this->lConf['jw_displayclick'], "play");
		$dock				= $this->getSetting($this->conf['JWPlayer.']['dock'], $this->lConf['jw_dock'], "false");
		$item				= $this->getSetting($this->conf['JWPlayer.']['item'], $this->lConf['jw_item'], "0");
		$linktarget			= $this->getSetting($this->conf['JWPlayer.']['linktarget'], $this->lConf['jw_linktarget'], "_blank");
		$mute				= $this->getSetting($this->conf['JWPlayer.']['mute'], $this->lConf['jw_mute'], "false");
		$repeat				= $this->getSetting($this->conf['JWPlayer.']['repeat'], $this->lConf['jw_repeat'], "none");
		$shuffle			= $this->getSetting($this->conf['JWPlayer.']['shuffle'], $this->lConf['jw_shuffle'], "false");
		$volume				= $this->getSetting($this->conf['JWPlayer.']['volume'], $this->lConf['jw_volume'], "90");
		$plugins			= $this->getSetting($this->conf['JWPlayer.']['plugins'], $this->lConf['jw_plugins'], "");
		$flashvars			= $this->getSetting($this->conf['JWPlayer.']['flashvars'], $this->lConf['jw_flashvars'], "");
		$debug				= $this->getSetting($this->conf['JWPlayer.']['debug'], $this->lConf['jw_debug'], "");
		
		
		//a little bit of data pre-processing
		
		if($logo != "") $logo = '/uploads/tx_paemedialibrary/'.$logo;
		
		$playerFile = ($playertype == "regular")?"player.swf":"player-viral.swf";
		$playerPath = $resPath."JWPlayer_4.6/".$playerFile;
		
		//strip line feeds from flashvars so they don't get doubled
		$flashvars = str_replace($this->stripCR, '', $flashvars);
		//now make sure we have proper line feeds because there won't be any in TS values.
		$flashvars = str_replace($comma, ','.chr(10), $flashvars);
		
		
		$media;
		
		if($this->lConf['displayMode'] == "single"){
			if($this->lConf['selectedFile'] != ""){
				//retreiving dam media record
				$queryParts = array(
					'SELECT' => "*",
					'FROM' => "tx_dam",
					'WHERE' => "uid=".$this->lConf['selectedFile'].$this->cObj->enableFields('tx_dam'),
				);
				//echo "SELECT ".$queryParts['SELECT']." FROM ".$queryParts['FROM']." WHERE ".$queryParts['WHERE']." ;<br />";
				
				$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE']
				) or die("ERROR: tx_paemedialibrary->main(req1): " .$GLOBALS['TYPO3_DB']->sql_error());
				
				$media = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
				
				if($GLOBALS['TYPO3_DB']->sql_num_rows($result) > 0){
					if($media['tx_paemedialibrary_file_use_advanced_location'] == 1)
						$media_src=$this->getMediaSrc($media['tx_paemedialibrary_file_advanced_location'], true);
					else $media_src='/'.$media['file_path'].$media['file_name'];
					
					//$htmlCode[] = $media_src;
				}
				else{
					$errorMessage .= $this->pi_getLL('media_offline');
				}
			}
			else{
				$errorMessage .= $this->pi_getLL('no_single_media');
			}
		}
		else if($this->lConf['displayMode'] == "category"){
			
			//extracting the &L parameter from piVars
			parse_str($GLOBALS['TSFE']->linkVars);
			//now the variable named $L has been initialized by parse_str
			$this->L = $L;
				
			//generate regular URL
			$format = 'RSS';
			$subFormat = 'itunes';
				
			
			if($extConf['realurlintegration']){
				//generate url rewrited URL
				$conf = array(
				  'parameter' =>  $currentPageUID.','.$extConf['typeNum'], // Link to current page + page type
				  // Set additional parameters 
				  'additionalParams' => '&'.$this->prefixId.'[format]'.'='.$format.'&'.$this->prefixId.'[subFormat]'.'='.$subFormat.'&'.$this->prefixId.'[categories]'.'='.$this->lConf['selectedCategories'], 
				  'useCacheHash' => $this->pi_checkCHash, // We must add cHash because we use parameters
				  'returnLast' => 'url', // We want link only
				);
				$media_src = urlencode($this->cObj->typoLink('',$conf));
				
			} else {
				//generate regular URL
				$media_src = urlencode("index.php?id=".$currentPageUID.'&type='.$extConf['typeNum'].'&'.$this->prefixId.'[format]'.'='.$format.'&'.$this->prefixId.'[subFormat]'.'='.$subFormat.'&'.$this->prefixId.'[categories]'.'='.$this->lConf['selectedCategories']);
			}
			
		}
		else if($this->lConf['displayMode'] == "records"){
			
			//extracting the &L parameter from piVars
			parse_str($GLOBALS['TSFE']->linkVars);
			//now the variable named $L has been initialized by parse_str
			$this->L = $L;
			
			//generate regular URL
			$format = 'RSS';
			$subFormat = 'itunes';
				
			if($extConf['realurlintegration']){
				//generate url rewrited URL
				$conf = array(
				  'parameter' => $currentPageUID.','.$extConf['typeNum'], // Link to current page + page type
				  // Set additional parameters
				  'additionalParams' => '&'.$this->prefixId.'[format]'.'='.$format.'&'.$this->prefixId.'[subFormat]'.'='.$subFormat.'&'.$this->prefixId.'[records]'.'='.$this->lConf['selectedRecords'], 
				  'useCacheHash' => $this->pi_checkCHash, // We must add cHash because we use parameters
				  'returnLast' => 'url', // We want link only
				);
				$media_src = urlencode($this->cObj->typoLink('playlist',$conf));
			}
			else {
				//generate regular URL
				$media_src = urlencode("index.php?id=".$currentPageUID.'&type='.$extConf['typeNum'].'&'.$this->prefixId.'[format]'.'='.$format.'&'.$this->prefixId.'[subFormat]'.'='.$subFormat.'&'.$this->prefixId.'[records]'.'='.$this->lConf['selectedRecords']);
			}
			
		}
		else if($this->lConf['displayMode'] == "playlist"){
			$media_src = $this->lConf['playlist_url'];
		}
		
		
		
		//store meta information for file properties
		$meta="";		
		
		//meta information preprocessing
		$date = date($this->conf['dateFormat'], $media['date_cr']); //$extConf['dateFormat']
		
		$description = str_replace($this->stripCR, '', $media['description']);
		
		$image = '/uploads/tx_paemedialibrary/'.$media['tx_paemedialibrary_thumbnail_user_defined'];
		
		//defining meta information for single files. For other files it is loaded from XML
		if($this->lConf['displayMode'] == "single"){
			
			if(isset($media['creator']) && $media['creator'] != "")	$meta .= 'author: "'.$media['creator'].'",'.chr(10);
			if(isset($media['date_cr']) && $media['date_cr'] != "")	$meta .= 'date: "'.$date.'",'.chr(10);
			if(isset($media['description']) && $media['description'] != "")	$meta .= 'description: "'.$description.'",'.chr(10);
			if(isset($media['tx_paemedialibrary_duration_seconds']) && $media['tx_paemedialibrary_duration_seconds'] != "")	$meta .= 'duration: "'.$media['tx_paemedialibrary_duration_seconds'].'",'.chr(10);
			if(isset($media['tx_paemedialibrary_thumbnail_user_defined']) && $media['tx_paemedialibrary_thumbnail_user_defined'] != "")	$meta .= 'image: "'.$image.'",'.chr(10);
			if(isset($media['alt_text']) && $media['alt_text'] != "")	$meta .= 'link: "'.$media['alt_text'].'",'.chr(10);
			if(isset($media['tx_paemedialibrary_start_seconds']) && $media['tx_paemedialibrary_start_seconds'] != "")	$meta .= 'start: "'.$media['tx_paemedialibrary_start_seconds'].'",'.chr(10);
			if(isset($media['keywords']) && $media['keywords'] != "")	$meta .= 'tags: "'.$media['keywords'].'",'.chr(10);
			if(isset($media['title']) && $media['title'] != "")	$meta .= 'title: "'.$media['title'].'",'.chr(10);
			if(isset($media['tx_paemedialibrary_type']) && $media['tx_paemedialibrary_type'] != "")	$meta .= 'type: "'.$media['tx_paemedialibrary_type'].'",'.chr(10);

		}
				
		//$media_src = "typo3conf/ext/pae_media_library/rss.xml";
		// Flash code
		$flashCode[] = '
			<div id="'.$contentID.'"></div>
			<script type="text/javascript">
				// <![CDATA[
				
				var flashvars = {
					//file properties'.chr(10).
					$meta.
					'file: "'.$media_src.'",'.chr(10).
					(($this->lConf['streaming_base_url'] != "")?'streamer: "'.$this->lConf['streaming_base_url'].'",':'').chr(10).'
					
					
				  //layout
				  '.$this->getFlashvar('controlbar', $controlbar, "bottom").
				  $this->getFlashvar('width', $width, "280").
				  $this->getFlashvar('height', $height, "400").
				  $this->getFlashvar('icons', $icons, "true").
				  $this->getFlashvar('backcolor', $backcolor, "").
				  $this->getFlashvar('frontcolor', $frontcolor, "").
				  $this->getFlashvar('lightcolor', $lightcolor, "").
				  $this->getFlashvar('screencolor', $screencolor, "").
				  $this->getFlashvar('icons', $icons, "true").
				  $this->getFlashvar('logo', $logo, "").
				  $this->getFlashvar('skin', $skin, "").
				  '
				  
				  //behavior
				  '.$this->getFlashvar('playlist', $playlist, "none").
				  $this->getFlashvar('playlistsize', $playlistsize, "180").
				  $this->getFlashvar('smoothing', $smoothing, "true").
				  $this->getFlashvar('stretching', $stretching, "uniform").
				  $this->getFlashvar('autostart', $autostart, "false").
				  $this->getFlashvar('bandwidth', $bandwidth, "5000").
				  $this->getFlashvar('bufferlength', $bufferlength, "1").
				  $this->getFlashvar('displayclick', $displayclick, "play").
				  $this->getFlashvar('dock', $dock, "false").
				  $this->getFlashvar('item', $item, "0").
				  $this->getFlashvar('linktarget', $linktarget, "_blank").
				  $this->getFlashvar('mute', $mute, "false").
				  $this->getFlashvar('repeat', $repeat, "none").
				  $this->getFlashvar('shuffle', $shuffle, "false").
				  $this->getFlashvar('volume', $volume, "90").
				  $this->getFlashvar('plugins', $plugins, "").
				  $this->getFlashvar('debug', $debug, "").
				  '};
				var params = {
				  allowfullscreen: "true",
				  allowScriptAccess: "always",
				};
				var attributes = {
				  id: "myDynamicContent",
				  name: "myDynamicContent"
				};'.chr(10);
				
										
				$flashCode[] = '//JW player plugins flashvars';
				//dealing with plugin flashvars requires a specific processing because of the dot notation of JW player plugin flashvar names which messes up with javascript dot notation.
				$flashvars = explode(',',$flashvars);
				foreach($flashvars as $index => $value){
					$value = explode(':',$value);
					//strip line feeds 
					$value[0] = trim(str_replace($this->stripCR, '', $value[0]));
					$value[1] = trim(str_replace($this->stripCR, '', $value[1]));
					if($value[0] != "")$flashCode[] = 'flashvars["'.$value[0].'"] = '.$value[1].';';
				}
				
				$flashCode[] = '
				swfobject.embedSWF("'.$playerPath.'", "'.$contentID.'", "'.$width.'", "'.$height.'", "'.$flash_version_req.'","'.$resPath.'expressInstall.swf", flashvars, params, attributes);

			// ]]>
			</script>';
			
			
			
			
		
		
		//use template for displaying file meta information only if a single file is being displayed, and if show_meta setting is enabled
		if($this->lConf['displayMode'] == "single" && $show_meta == "true"){
			// Get the template
			$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
			 
			  // Get the parts out of the template
			$template['total'] = $this->cObj->getSubpart($this->templateCode,'###TEMPLATE_MEDIAPLAYER###');
			  
			  // Fill marker
			$markerArray['###MEDIA_TITLE###'] = $media['title'];
			$markerArray['###PLAYER_SWF###'] = implode(chr(10),$flashCode);
			$markerArray['###MEDIA_DESCRIPTION###'] = nl2br($media['description']);
			$markerArray['###MEDIA_DURATION###'] = $media['tx_paemedialibrary_duration'];
			$markerArray['###MEDIA_AUTHOR###'] = $media['creator'];
			$markerArray['###MEDIA_DATE###'] = $date;
			$markerArray['###MEDIA_KEYWORDS###'] = $media['keywords'];
			
			$markerArray['###MEDIA_DESCRIPTION_LABEL###'] = $this->pi_getLL('media.description');
			$markerArray['###MEDIA_DURATION_LABEL###'] = $this->pi_getLL('media.duration');
			$markerArray['###MEDIA_AUTHOR_LABEL###'] = $this->pi_getLL('media.author');
			$markerArray['###MEDIA_DATE_LABEL###'] = $this->pi_getLL('media.date');
			$markerArray['###MEDIA_KEYWORDS_LABEL###'] = $this->pi_getLL('media.keywords');
		
			// Finalize and create the content by replacing the "content" marker in the template
			if($errorMessage == ""){
				$content = $this->cObj->substituteMarkerArrayCached($template['total'],$markerArray);
			}
			else{
				$errorCode = '<div class="'.$this->prefixId.'_errorMessage">'.$this->pi_getLL('error').": ".$errorMessage.'</div><br /><br />'.chr(10);
				$content = $errorCode.$this->cObj->substituteMarkerArrayCached($template['total'],$markerArray);
			}
		}
		else{
			
			// Finalize and create the content by replacing the "content" marker in the template
			if($errorMessage == ""){
				$content = implode(chr(10),$flashCode);
			}
			else{
				$errorCode = '<div class="'.$this->prefixId.'_errorMessage">'.$this->pi_getLL('error').": ".$errorMessage.'</div><br /><br />'.chr(10);
				$content = $errorCode.implode(chr(10),$flashCode);
			}
			 
		}
		
			
		
		//debug : view current dam record content in page
		//$content .= t3lib_div::view_array($media);
		
		
		return $this->pi_wrapInBaseClass($content);
	}
	
	
	/*
	 * Loads plugin settings according to TS priority
	 * $settingName : String, the name of the setting must be the same for the TS value and the plugin flexform value
	 * $TSSettingsOverridePluginSettings : int, 1 for TS priority, 0 for plugin flexform priority
	 * $defaultValue : whatever is required here
	 */
	
	function getSetting($TSSettingValue, $PiFlexSettingValue, $defaultValue){
			if($this->TSSettingsOverridePluginSettings){
				//Priority given to TS settings
				if(isset($TSSettingValue) && $TSSettingValue != ""){
					//TS setting exists and value is not void, return TS setting
					return $TSSettingValue;
				}
				else{
					//TS setting does not exist or is void, looking at plugin setting for valid value
					if(isset($PiFlexSettingValue) && $PiFlexSettingValue != ""){
						return $PiFlexSettingValue;
					}
					else {
						//no setting is correctly filled. Return default value
						return $defaultValue;
					}
				}
			}
			else{
				//Priority given to plugin flexform settings
				if(isset($PiFlexSettingValue) && $PiFlexSettingValue != ""){
					//Plugin setting exists and value is not void, return Plugin setting
					return $PiFlexSettingValue;
				}
				else{
					//Plugin setting does not exist or is void, looking at TS setting for valid value
					if(isset($TSSettingValue) && $TSSettingValue != ""){
						return $TSSettingValue;
					}
					else {
						//no setting is correctly filled. Return default value
						return $defaultValue;
					}
				}
			}
		}
	
	
	/*
	 * Prevents outputing flashvar when value matches JW player default value.
	 * This optimizes the output
	 */
	function getFlashvar($flashvarName, $value, $doNotOutputIfMatchesThisValue){
		return ($value != $doNotOutputIfMatchesThisValue)?($flashvarName.': "'.$value.'",').chr(10):'';
	}
	
	function getMediaSrc($xml){
		
		//extracting the content uids from the page TSFE dataStructure elements list
		$DS = t3lib_div::xml2array($xml);
		$contentElements = array();
		
		//$lookupStrings=array("http://", "https://", "rtmp://");
				
		// Traverse the entire array based on the language...
		// and assign each field option to $contentElements array...
		if(isset($DS['data']) && is_array($DS['data']))
		{
			$i=0;
			if(is_array($DS['data']['sDEF']['lDEF']['field_medias']['el']))
			{
				if($DS['data']['sDEF']['lDEF']['field_medias']['el'] != null && sizeof($DS['data']['sDEF']['lDEF']['field_medias']['el'])>0){
					 foreach ( $DS['data']['sDEF']['lDEF']['field_medias']['el'] as $element => $data ){
						//print_r($data);
						
						//check if a the location of the file is already an absolute URL
						$field_source = $data['field_remotemedia']['el']['field_source']['vDEF'];
						/*
						$absolute_URL = false;
						for($j=0;$j<sizeof($lookupStrings);$j++){
							if( stristr($field_source,$lookupStrings[$j])){
								//this is an absolute URL
								$absolute_URL = true;
								break;
							}
						}
						if(!$absolute_URL && $preserveRelativeURL) $contentElements[$i]['field_source'] = $field_source;
						else{
							//convert to absolute URL for XML playlists
							$contentElements[$i]['field_source'] = $this->conf['streaming_base_url'].'/'.$field_source;
						}*/
						$contentElements[$i]['field_source'] = $data['field_remotemedia']['el']['field_source']['vDEF'];
						//$contentElements[$i]['field_source'] = $data['field_remotemedia']['el']['field_source']['vDEF'];
						$contentElements[$i++]['field_bitrate'] = $data['field_remotemedia']['el']['field_bitrate']['vDEF'];
					 }
				}
			}
		}
		
		//if($this->lConf['displayMode'] == "single_mediaserver") return $this->lConf['streaming_base_url'].$contentElements[0]['field_source'];
		return $contentElements[0]['field_source'];
		
	}
	
	
	
	
	/**
	 * Creates XML output for Flash.
	 * 
	 * This function creates an XML output wich can be used by a SWF movie.
	 * 
	 * @return		The XML content.
	 */
	function writeXML() {
		
		//print_r($this->piVars);
		
		$format = $this->piVars["format"];
		$subFormat = $this->piVars["subFormat"];
		$categories = $GLOBALS['TYPO3_DB']->cleanIntList(urldecode($this->piVars["categories"]));
		$records = $GLOBALS['TYPO3_DB']->cleanIntList(urldecode($this->piVars["records"]));
		$items = $GLOBALS['TYPO3_DB']->cleanIntList(urldecode($this->piVars["items"]));
		
		//overwrite session data since it has proven sometimes not to be correct
		/*$session->prefixId = $_REQUEST("prefixId");
		$session->pluginPageUID = $_REQUEST("id");
		$session->selectedPids = $_REQUEST("selectedPids");*/
		
		
		//display all records from given DAM categories 
		if($categories != 0){
			//retreive uid of records matching given categories uids
			
			//filter disabled categories according to enable_fields
			$queryParts = array(
				'SELECT' => "*",
				'FROM' => "tx_dam_cat",
				'WHERE' => "uid IN (".$categories.")".$this->cObj->enableFields('tx_dam_cat')
			);
			
			//echo "SELECT ".$queryParts['SELECT']." FROM ".$queryParts['FROM']." WHERE ".$queryParts['WHERE']." ;<br />";
			
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				$queryParts['WHERE']
			) or die("ERROR: tx_paemedialibrary->class.xml_output->writeXML(req1): " .$GLOBALS['TYPO3_DB']->sql_error());
			
			//extract query result
			$category_enabled = "";
			while($category_enabled = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
				$categories_enabled .= $category_enabled['uid'].',';
			}
			//strip last comma
			$categories_enabled = substr($categories_enabled, 0, strlen($categories_enabled)-1);
			
			//echo "categories_enabled=".$categories_enabled."<br /><br />";
			
			if(isset($categories_enabled) && $categories_enabled != ""){
				//extract uid_local values in mm table
				$queryParts = array(
					'SELECT' => "*",
					'FROM' => "tx_dam_mm_cat",
					'WHERE' => "uid_foreign IN (".$categories_enabled.")"
				);
				//echo "SELECT ".$queryParts['SELECT']." FROM ".$queryParts['FROM']." WHERE ".$queryParts['WHERE']." ;<br />";
				
				
				$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE']
				) or die("ERROR: tx_paemedialibrary->class.xml_output->writeXML(req2): " .$GLOBALS['TYPO3_DB']->sql_error());
				
				//extract query result
				$dam_elements_local = "";
				while($element = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
					$dam_elements_local .= $element['uid_local'].',';
				}
				//strip last comma
				$dam_elements_local = substr($dam_elements_local, 0, strlen($dam_elements_local)-1);
				
				//echo "dam_elements_local=".$dam_elements_local."<br /><br />";die();
				
				
				//then extract dam records for medias
				$queryParts = array(
					'SELECT' => "*",
					'FROM' => "tx_dam",
					'WHERE' => "uid IN (".$dam_elements_local.")".$this->cObj->enableFields('tx_dam')." ORDER BY `date_cr` DESC"
				);
				
				$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE']
				) or die("ERROR: tx_paemedialibrary->class.xml_output->writeXML(req3): " .$GLOBALS['TYPO3_DB']->sql_error());
			
			
			}
			/*
			$result = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$queryParts['SELECT'],
			"tx_dam",
			"tx_dam_mm_cat",
			"tx_dam_cat",
			$queryParts['WHERE'],
			$groupBy = '',
			$orderBy = '',
			$limit = ''	 
			);*/ 	
		}
		
		
		//display all records from given DAM records list
		else if($records != 0){
			//retreive uid of records matching given categories uids
			
			//filter disabled categories according to enable_fields
			$queryParts = array(
				'SELECT' => "*",
				'FROM' => "tx_dam",
				'WHERE' => "uid IN (".$records.")".$this->cObj->enableFields('tx_dam'),
			);
			
			//echo "SELECT ".$queryParts['SELECT']." FROM ".$queryParts['FROM']." WHERE ".$queryParts['WHERE']." ;<br />";
			
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				$queryParts['WHERE']
			) or die("ERROR: tx_paemedialibrary->class.xml_output->writeXML(req4): " .$GLOBALS['TYPO3_DB']->sql_error());
		
		}
		//echo "SELECT ".$queryParts['SELECT']." FROM ".$queryParts['FROM']." WHERE ".$queryParts['WHERE']." ;<br />";
		
		
		
		// XML Storage
		$xml =  array();
		
		$xml[] = '<?xml version="1.0" encoding="'.$GLOBALS['TSFE']->tmpl->setup['config.']['metaCharset'].'" ?>'; 
		
		//$xml[] = '<!-- start format[0]='.$format[0].'-->';
		// standalone="yes"
		
		if($format == "RSS"){
			if($subFormat == "itunes"){$xml[] = '<rss version="2.0"'.chr(10).'	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"'.
			chr(10).'	xmlns:jwplayer="http://developer.longtailvideo.com/trac/wiki/FlashFormats">';}
			else{$xml[] = '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:jwplayer="http://developer.longtailvideo.com/trac/wiki/FlashFormats">';}
			$xml[] = '	<channel>';
			/*
			$xml[] = '		<title>'.$this->conf['XMLPlaylistTitle'].'</title>';
			$xml[] = '		<link><![CDATA[ '."http://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"].' ]]></link>';
			$xml[] = '		<description>'.$this->conf['XMLPlaylistDescription'].'</description>';
			*/
			if($result != NULL){
				
				while($media = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
					
					$forceAbsoluteURLsInXMLPlaylists = (bool)$this->conf['forceAbsoluteURLsInXMLPlaylists'];
					
					if($forceAbsoluteURLsInXMLPlaylists){
						if($media['tx_paemedialibrary_file_use_advanced_location'] == 1)
							$media_src=$this->conf['streaming_base_url'].'/'.$this->getMediaSrc($media['tx_paemedialibrary_file_advanced_location']);
						else{
							$media_src="http://".$_SERVER['SERVER_NAME'].'/'.$media['file_path'].$media['file_name'];
						}
					}
					else{
						if($media['tx_paemedialibrary_file_use_advanced_location'] == 1)
							$media_src = $this->getMediaSrc($media['tx_paemedialibrary_file_advanced_location']);
						else{
							$media_src = '/'.$media['file_path'].$media['file_name'];
						}
					}
					
					$description = str_replace($this->stripCR, '', $media['description']);
					
					/*if($media['tx_paemedialibrary_file_use_advanced_location'] == 1)
						$media_src=$this->getMediaSrc($media['tx_paemedialibrary_file_advanced_location'], $forceAbsoluteURLsInXMLPlaylists);
					else if($forceAbsoluteURLsInXMLPlaylists) $media_src="http://".$_SERVER['SERVER_NAME'].'/'.urlencode($media['file_path'].$media['file_name']);
					else $media_src = urlencode($media['file_path'].$media['file_name']);*/
					//t3lib_htmlmail::getMimeType($fileURL);
					//$media['file_mime_type'] = "video";
					//$media['file_mime_subtype'] = "x-flv";
						
					$xml[] = '		<item>';
					
					if($this->conf['singleMediaPlayerPageUID'] != '')$xml[] = '			<guid isPermaLink="true"><![CDATA[ '."http://".$_SERVER['SERVER_NAME'].'/index.php?id='.$this->conf['singleMediaPlayerPageUID'].'&L='.$this->L.'&media='.$media['uid'].' ]]></guid>';
					else $xml[] = '			<guid isPermaLink="false">dam_'.$media['uid'].'</guid>';
					
					if($media['title'] != "")		$xml[] = '			<title>'.$media['title'].'</title>';
					if($media['alt_text'] != "")	$xml[] = '			<link><![CDATA[ '.$media['alt_text'].' ]]></link>';
					if($description != "")			$xml[] = '			<description>'.$description.'</description>';
					if($media_src != "") 			$xml[] = '			<enclosure url="'.$media_src.'" type="'.$media['file_mime_type'].'/'.$media['file_mime_subtype'].'" length="'.$media['file_size'].'"/>';  
					//if($media_src != "") 			$xml[] = '			<enclosure url="'.$media_src.'" type="video" length="'.$media['file_size'].'"/>';  
					if($media['creator'] != "") 	$xml[] = '			<itunes:author>'.$media['creator'].'</itunes:author>';
					//if($media['creator'] != "") 	$xml[] = '			<jwplayer:image>files/bunny.jpg</jwplayer:image>';

					$xml[] = '		</item>';
				}	
				
			}
			$xml[] = '	</channel>';	
			$xml[] = '</rss>'.chr(10);
		}
		//$xml[] = '<!-- end -->';	
		//print_r($this->conf);die();
		// Return XML code
		return implode(chr(10),$xml);
	}
}

/*

<?xml version="1.0" encoding="iso-8859-1" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="field_medias">
                    <el index="el">
                        <section index="1">
                            <itemType index="field_remotemedia">
                                <el>
                                    <field index="field_source">
                                        <value index="vDEF">PAE_CC_250909.flv</value>
                                    </field>
                                    <field index="field_bitrate">
                                        <value index="vDEF">0</value>
                                    </field>
                                </el>
                            </itemType>
                            <itemType index="_TOGGLE">0</itemType>
                        </section>
                    </el>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>
*/



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pae_media_library/pi1/class.tx_paemedialibrary_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pae_media_library/pi1/class.tx_paemedialibrary_pi1.php']);
}

?>