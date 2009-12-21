<?php

########################################################################
# Extension Manager/Repository config file for ext: "pae_media_library"
#
# Auto generated 21-12-2009 12:33
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
	'version' => '0.8.3',
	'constraints' => array(
		'depends' => array(
			'dam' => '1.1',
			'typo3' => '4.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:41:{s:9:"ChangeLog";s:4:"e45f";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"9a96";s:12:"ext_icon.gif";s:4:"ed05";s:17:"ext_localconf.php";s:4:"d267";s:14:"ext_tables.php";s:4:"5091";s:14:"ext_tables.sql";s:4:"91dc";s:28:"ext_typoscript_constants.txt";s:4:"4694";s:24:"ext_typoscript_setup.txt";s:4:"78e1";s:19:"flexform_ds_pi1.xml";s:4:"ed8e";s:19:"flexform_ds_pi2.xml";s:4:"1a3c";s:61:"flexform_tx_dam_tx_paemedialibrary_file_advanced_location.xml";s:4:"ed28";s:47:"flexform_tx_paemedialibrary_media_media_src.xml";s:4:"d41d";s:13:"locallang.xml";s:4:"b172";s:16:"locallang_db.xml";s:4:"51cf";s:7:"rss.xml";s:4:"edb3";s:7:"tca.php";s:4:"d41d";s:14:"doc/manual.sxw";s:4:"775f";s:19:"doc/wizard_form.dat";s:4:"01e9";s:20:"doc/wizard_form.html";s:4:"dac2";s:22:"res/expressInstall.swf";s:4:"7b65";s:30:"res/medialibrary_template.html";s:4:"8542";s:25:"res/pae_media_library.css";s:4:"cc6c";s:26:"res/pae_media_library.html";s:4:"e262";s:16:"res/swfobject.js";s:4:"892a";s:12:"res/test.swf";s:4:"3eb7";s:33:"res/JWPlayer_4.6/player-viral.swf";s:4:"35bd";s:27:"res/JWPlayer_4.6/player.swf";s:4:"0b1b";s:23:"res/JWPlayer_4.6/yt.swf";s:4:"8108";s:14:"pi2/ce_wiz.gif";s:4:"241d";s:36:"pi2/class.tx_paemedialibrary_pi2.php";s:4:"c158";s:44:"pi2/class.tx_paemedialibrary_pi2_wizicon.php";s:4:"33de";s:13:"pi2/clear.gif";s:4:"cc11";s:17:"pi2/locallang.xml";s:4:"ef4e";s:14:"pi1/ce_wiz.gif";s:4:"a1f2";s:36:"pi1/class.tx_paemedialibrary_pi1.php";s:4:"6807";s:44:"pi1/class.tx_paemedialibrary_pi1_wizicon.php";s:4:"252f";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"a93c";s:24:"pi1/static/editorcfg.txt";s:4:"d41d";s:20:"pi1/static/setup.txt";s:4:"d41d";}',
);

?>