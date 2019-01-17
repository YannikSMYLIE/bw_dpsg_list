#
# Table structure for table 'tx_bwdpsglist_domain_model_server'
#
CREATE TABLE tx_bwdpsglist_domain_model_server (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	address varchar(255) DEFAULT '' NOT NULL,
	version smallint(3) unsigned DEFAULT '2',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
);

#
# Table structure for table 'tx_bwdpsglist_domain_model_maillist'
#
CREATE TABLE tx_bwdpsglist_domain_model_maillist (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

  server int(11) unsigned DEFAULT '0',
  name varchar(255) DEFAULT '' NOT NULL,
	displayname varchar(255) DEFAULT '' NOT NULL,
  password varchar(255) DEFAULT '' NOT NULL,
	listowner varchar(255) DEFAULT '' NOT NULL,
  senders int(11) unsigned DEFAULT '0',
  receivers int(11) unsigned DEFAULT '0',
	type smallint(3) unsigned DEFAULT '0',
	archive smallint(3) unsigned DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
);

#
# Table structure for table 'tx_bwdpsglist_domain_model_group'
#
CREATE TABLE tx_bwdpsglist_domain_model_group (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	leaders smallint(3) DEFAULT '0' NOT NULL,
	members smallint(3) DEFAULT '0' NOT NULL,
	staff smallint(3) DEFAULT '0' NOT NULL,
	stufen varchar(255) DEFAULT '0' NOT NULL,
	additional_groups int(11) unsigned DEFAULT '0',
	maillist int(11) unsigned DEFAULT '0',
	role varchar(255) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
);

#
# Table structure for table 'tx_bwdpsglist_maillist_receiver_mm'
# Tabelle für die Verknüpfung von AdditionalGroups mit den Groups.
#
CREATE TABLE tx_bwdpsglist_groups_additionalgroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);