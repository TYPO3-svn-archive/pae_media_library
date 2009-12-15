<?php

########################################################################
# Extension Manager/Repository config file for ext: "pae_media_library"
#
# Auto generated 27-10-2009 16:21
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'PAE Media Library',
	'description' => 'Sorts and plays media files from DAM or streaming server',
	'category' => 'plugin',
	'author' => 'Alban Cousinie',
	'author_email' => 'extensions@mind2machine.com',
	'shy' => '',
	'dependencies' => 'dam',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.8.0',
	'constraints' => array(
		'depends' => array(
			'dam' => '1.1',
			'typo3' => '4.2.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:42:{s:9:"ChangeLog";s:4:"d554";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"a4b5";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"af9b";s:14:"ext_tables.php";s:4:"40a7";s:14:"ext_tables.sql";s:4:"91dc";s:28:"ext_typoscript_constants.txt";s:4:"0d2c";s:24:"ext_typoscript_setup.txt";s:4:"c0e5";s:19:"flexform_ds_pi1.xml";s:4:"90bf";s:19:"flexform_ds_pi2.xml";s:4:"1a3c";s:61:"flexform_tx_dam_tx_paemedialibrary_file_advanced_location.xml";s:4:"ddd5";s:47:"flexform_tx_paemedialibrary_media_media_src.xml";s:4:"d41d";s:33:"icon_tx_paemedialibrary_media.gif";s:4:"d41d";s:37:"icon_tx_paemedialibrary_media_cat.gif";s:4:"d41d";s:13:"locallang.xml";s:4:"b172";s:16:"locallang_db.xml";s:4:"5156";s:7:"tca.php";s:4:"d41d";s:14:"xml_output.php";s:4:"678b";s:19:"doc/wizard_form.dat";s:4:"01e9";s:20:"doc/wizard_form.html";s:4:"dac2";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:36:"pi1/class.tx_paemedialibrary_pi1.php";s:4:"5533";s:44:"pi1/class.tx_paemedialibrary_pi1_wizicon.php";s:4:"252f";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"a93c";s:24:"pi1/static/editorcfg.txt";s:4:"d41d";s:20:"pi1/static/setup.txt";s:4:"d41d";s:14:"pi2/ce_wiz.gif";s:4:"02b6";s:36:"pi2/class.tx_paemedialibrary_pi2.php";s:4:"c158";s:44:"pi2/class.tx_paemedialibrary_pi2_wizicon.php";s:4:"33de";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"ef4e";s:22:"res/expressInstall.swf";s:4:"7b65";s:30:"res/medialibrary_template.html";s:4:"8542";s:25:"res/pae_media_library.css";s:4:"cc6c";s:26:"res/pae_media_library.html";s:4:"e262";s:16:"res/swfobject.js";s:4:"892a";s:12:"res/test.swf";s:4:"3eb7";s:33:"res/JWPlayer_4.6/player-viral.swf";s:4:"35bd";s:27:"res/JWPlayer_4.6/player.swf";s:4:"c608";s:23:"res/JWPlayer_4.6/yt.swf";s:4:"8108";}',
);

?>