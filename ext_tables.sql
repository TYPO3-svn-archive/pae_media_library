#
# Table structure for table 'tx_dam'
#
CREATE TABLE tx_dam (
	tx_paemedialibrary_type varchar(7) DEFAULT '' NOT NULL
	tx_paemedialibrary_file_use_advanced_location tinyint(3) DEFAULT '0' NOT NULL,
	tx_paemedialibrary_file_advanced_location mediumtext,
	tx_paemedialibrary_thumbnail_user_defined text,
	tx_paemedialibrary_duration tinytext,
	tx_paemedialibrary_duration_seconds int(11) DEFAULT '0' NOT NULL,
	tx_paemedialibrary_start_seconds int(11) DEFAULT '0' NOT NULL,
	tx_paemedialibrary_archived tinyint(3) DEFAULT '0' NOT NULL,
	tx_paemedialibrary_archive_date int(11) DEFAULT '0' NOT NULL
	tx_paemedialibrary_custommediatype mediumtext,
);


#
# Table structure for table 'tx_dam_cat'
#
CREATE TABLE tx_dam_cat (
	tx_paemedialibrary_thumbnail text
);