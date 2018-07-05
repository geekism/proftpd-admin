#
# Table structure for table `groups`
#

CREATE TABLE `groups` (
  `groupname` varchar(32) NOT NULL default '',
  `gid` smallint(6) unsigned NOT NULL auto_increment,
  `members` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`gid`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='ProFTPd group table';

#
# Table structure for table `users`
#

CREATE TABLE `users` (
  `id` smallint(2) unsigned NOT NULL auto_increment,
  `userid` varchar(32) NOT NULL default '',
  `uid` smallint(6) unsigned default NULL,
  `gid` smallint(6) unsigned default NULL,
  `passwd` varchar(265) NOT NULL default '',
  `homedir` varchar(255) NOT NULL default '',
  `comment` varchar(255) NOT NULL default '',
  `disabled` smallint(2) unsigned NOT NULL default '0',
  `shell` varchar(32) NOT NULL default '/sbin/nologin',
  `email` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(5) NOT NULL default '',
  `company` varchar(255) NOT NULL default '',
  `bytes_in_used` bigint(20) unsigned NOT NULL default '0',
  `bytes_out_used` bigint(20) unsigned NOT NULL default '0',
  `files_in_used` bigint(20) unsigned NOT NULL default '0',
  `files_out_used` bigint(20) unsigned NOT NULL default '0',
  `login_count` int(11) unsigned NOT NULL default '0',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='ProFTPd user table';

CREATE TABLE `members` (
  `id` char(23) NOT NULL,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `email` varchar(65) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `mod_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `loginAttempts` (
  `IP` varchar(20) NOT NULL,
  `Attempts` int(11) NOT NULL,
  `LastLogin` datetime NOT NULL,
  `Username` varchar(65) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
