<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
// you add pi_flexform to be renderd when your plugin is shown
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';                   // new!


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:pae_media_library/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

// NOTE: Be sure to change sampleflex to the correct directory name of your extension!                    // new!
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds_pi1.xml');             // new!



if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_paemedialibrary_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_paemedialibrary_pi1_wizicon.php';
}


t3lib_div::loadTCA('tt_content');

/*
//pi2 DISABLED

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';
// you add pi_flexform to be renderd when your plugin is shown
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';                   // new!


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:pae_media_library/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

// NOTE: Be sure to change sampleflex to the correct directory name of your extension!                    // new!
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds_pi2.xml');             // new!


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_paemedialibrary_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_paemedialibrary_pi2_wizicon.php';
}
*/

$tempColumns = array (
	'tx_paemedialibrary_file_use_advanced_location' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_file_use_advanced_location',		
		'config' => array (
			'type' => 'check',
		)
	),
	
	
	//make media type adjustable for manual creation of undefined media
	'media_type' => array(
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.media_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:dam/locallang_db.xml:media_type.text', '1'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.image', '2'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.audio', '3'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.video', '4'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.dataset', '9'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.interactive', '5'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.software', '11'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.model', '8'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.font', '7'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.collection', '10'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.service', '6'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.application', '12'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.undefined', '0'),
				),

				'form_type' => '', //'user',
				'userFunc' => '', //'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->tx_dam_mediaType',
				'noTableWrapping' => TRUE,
				'readOnly' => false,
			)
		),
	/*
	'tx_paemedialibrary_custommediatype' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_custommediatype',
		'config' => array(
			'type' => 'select',
			'items' => array(
				array('', '0'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_mediatype_image', '2'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_mediatype_audio', '3'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_mediatype_video', '4'),
			),
			'size' => 1,
			'maxitems' => 1,
		)
	),*/
	
	'tx_paemedialibrary_file_advanced_location' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_file_advanced_location',		
		'config' => array (
			'type' => 'flex',
			'ds' => array (
				'default' => 'FILE:EXT:pae_media_library/flexform_tx_dam_tx_paemedialibrary_file_advanced_location.xml',
			),
		)
	),
	'tx_paemedialibrary_thumbnail_user_defined' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_thumbnail_user_defined',		
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',	
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
			'uploadfolder' => 'uploads/tx_paemedialibrary',
			'show_thumbs' => 1,	
			'size' => 1,	
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
	'tx_paemedialibrary_duration' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_duration',		
		'config' => array (
			'type' => 'input',	
			'size' => '6',
		)
	),
	'tx_paemedialibrary_duration_seconds' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_duration_seconds',		
		'config' => array (
			'type'     => 'input',
			'size'     => '5',
			'max'      => '5',
			'eval'     => 'int',
			'checkbox' => '0',
			'range'    => array (
				'upper' => '100000',
				'lower' => '10'
			),
			'default' => 0
		)
	),
	'tx_paemedialibrary_start_seconds' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_start_seconds',		
		'config' => array (
			'type'     => 'input',
			'size'     => '5',
			'max'      => '5',
			'eval'     => 'int',
			'checkbox' => '0',
			'range'    => array (
				'upper' => '100000',
				'lower' => '10'
			),
			'default' => 0
		)
	),
	'tx_paemedialibrary_archived' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_archived',		
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_paemedialibrary_archive_date' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_archive_date',		
		'config' => array (
			'type'     => 'input',
			'size'     => '12',
			'max'      => '20',
			'eval'     => 'datetime',
			'checkbox' => '0',
			'default'  => '0'
		)
	),
	'tx_paemedialibrary_type' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type',		
		'config' => array (
			'type' => 'select',
			'items' => array (
				array('', ''),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type.I.0', 'video'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type.I.1', 'sound'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type.I.2', 'image'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type.I.3', 'youtube'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type.I.4', 'http'),
				array('LLL:EXT:pae_media_library/locallang_db.xml:tx_dam.tx_paemedialibrary_type.I.5', 'rtmp'),
			),
			'size' => 1,	
			'maxitems' => 1,
		)
	),
);


t3lib_div::loadTCA('tx_dam');
t3lib_extMgm::addTCAcolumns('tx_dam',$tempColumns,1);

$tx_dam_file_use_advanced_location = 'tx_paemedialibrary_file_use_advanced_location;;;;1-1-1, ';
$tx_dam_custommediatype = ''; //tx_paemedialibrary_custommediatype, 
$tx_dam_file_advanced_location = 'tx_paemedialibrary_file_advanced_location, tx_paemedialibrary_type, ';
$tx_dam_thumbnail_user_defined = 'tx_paemedialibrary_thumbnail_user_defined, ';
$tx_dam_duration ='tx_paemedialibrary_duration, tx_paemedialibrary_duration_seconds, tx_paemedialibrary_start_seconds, ';
$tx_dam_archived ='tx_paemedialibrary_archived, ';
$tx_dam_archive_date ='tx_paemedialibrary_archive_date';

//todo : remove
//t3lib_extMgm::addToAllTCAtypes('tx_dam','tx_paemedialibrary_file_use_advanced_location;;;;1-1-1, tx_paemedialibrary_file_advanced_location, tx_paemedialibrary_type, tx_paemedialibrary_thumbnail_user_defined, tx_paemedialibrary_duration, tx_paemedialibrary_duration_seconds, tx_paemedialibrary_start_seconds, tx_paemedialibrary_archived, tx_paemedialibrary_archive_date');
//t3lib_extMgm::addToAllTCAtypes('tx_dam', '--div--;LLL:EXT:pae_media_library/locallang_db.xml:tx_dam_tab.div_media_library, tx_damdemo_info;;;;1-1-1, tx_damdemo_customcategory');

//ADD Custom DAM tab
//$mediaLibraryTab = '--div--;LLL:EXT:pae_media_library/locallang_db.xml:tx_dam_tab.div_media_library,';
t3lib_extMgm::addToAllTCAtypes('tx_dam', '--div--;LLL:EXT:pae_media_library/locallang_db.xml:tx_dam_tab.div_media_library,');

//ADD custom info only to relevant file types. This includes "undefined" for manually created records.
/* undefined */
$TCA['tx_dam']['types'][0]['showitem'] .= $tx_dam_file_use_advanced_location.$tx_dam_custommediatype.$tx_dam_file_advanced_location.$tx_dam_thumbnail_user_defined.$tx_dam_duration.$tx_dam_archived.$tx_dam_archive_date;
/* image */
$TCA['tx_dam']['types'][2]['showitem'] .= $tx_dam_archived.$tx_dam_archive_date;
/* audio */
$TCA['tx_dam']['types'][3]['showitem'] .= $tx_dam_file_use_advanced_location.$tx_dam_custommediatype.$tx_dam_file_advanced_location.$tx_dam_thumbnail_user_defined.$tx_dam_duration.$tx_dam_archived.$tx_dam_archive_date;
/* video */
$TCA['tx_dam']['types'][4]['showitem'] .= $tx_dam_file_use_advanced_location.$tx_dam_custommediatype.$tx_dam_file_advanced_location.$tx_dam_thumbnail_user_defined.$tx_dam_duration.$tx_dam_archived.$tx_dam_archive_date;

// add fields to index preset fields and batch processing form
$TCA['tx_dam']['txdamInterface']['index_fieldList'] .= ',tx_paemedialibrary_archived,tx_paemedialibrary_archive_date';

$tempColumns = array (
	'tx_paemedialibrary_thumbnail' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:pae_media_library/locallang_db.xml:tx_dam_cat.tx_paemedialibrary_thumbnail',		
		'config' => array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',	
			'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
			'uploadfolder' => 'uploads/tx_paemedialibrary',
			'show_thumbs' => 1,	
			'size' => 1,	
			'minitems' => 0,
			'maxitems' => 1,
		)
	),
);


t3lib_div::loadTCA('tx_dam_cat');
t3lib_extMgm::addTCAcolumns('tx_dam_cat',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_dam_cat','tx_paemedialibrary_thumbnail;;;;1-1-1');
?>