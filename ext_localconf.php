<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

//adding alternative page type for XML output
$pae_media_library_parameters = unserialize($_EXTCONF);
t3lib_extMgm::addTypoScriptConstants('extension.pae_media_library.typeNum = '.$pae_media_library_parameters['typeNum']);
  
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_paemedialibrary_pi1.php', '_pi1', 'list_type', 1);


//t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_paemedialibrary_pi2.php', '_pi2', 'list_type', 1);


$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pae_media_library']);
$realurlintegration	= $extConf['realurlintegration'];

if($realurlintegration) {
	
	
		$custom_postVarSets = array(
				// pae_medialibrary playlists
				'pae_playlists' => array(
					array(
						'GETvar' => 'tx_paemedialibrary_pi1[format]',
						),
					array(
						'GETvar' => 'tx_paemedialibrary_pi1[categories]',
						)
				)		
	      );
		
		if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT'])){
			$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT'] = array_merge($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT'], $custom_postVarSets);
		}
		else{
			$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT'] = $custom_postVarSets;
		}
		
		
		$custom_fileName = array(
				// pae_medialibrary playlists
				'playlist.xml' => array(
					'keyValues' => array(
						'type' => $extConf['typeNum'],
					)
				)	
	      );
		if(isset($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fileName']['index'])){
			$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fileName']['index'] = array_merge($TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fileName']['index'], $custom_fileName);
		}
		else{
			$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fileName']['index'] = $custom_fileName;
		}
	}
?>