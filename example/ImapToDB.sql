/**
 * Author: Andres Escobar
 * Based on code by: Ernest Wojciuk
 * https://www.linkedin.com/in/apescobar
 */


CREATE TABLE `emailtodb_email` (
  `ID` int(11) NOT NULL auto_increment,
  `IDEmail` varchar(255) NOT NULL default '0',
  `EmailFrom` varchar(255) NOT NULL default '',
  `EmailFromP` varchar(255) NOT NULL default '',
  `EmailTo` varchar(255) NOT NULL default '',
  `DateE` datetime NOT NULL default '0000-00-00 00:00:00',
  `DateDb` datetime NOT NULL default '0000-00-00 00:00:00',
  `DateRead` datetime NOT NULL default '0000-00-00 00:00:00',
  `DateRe` datetime NOT NULL default '0000-00-00 00:00:00',
  `Status` tinyint(3) NOT NULL default '0',
  `Type` tinyint(3) NOT NULL default '0',
  `Del` tinyint(3) NOT NULL default '0',
  `Subject` varchar(255) default NULL,
  `Message` text  NOT NULL,
  `Message_html` text  NOT NULL,
  `MsgSize` int(11) NOT NULL default '0',
  `Kind` tinyint(2) NOT NULL default '0',
  `IDre` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `IDEmail` (`IDEmail`),
  KEY `EmailFrom` (`EmailFrom`)
) ENGINE=MyISAM;


CREATE TABLE `emailtodb_attach` (
  `ID` int(11) NOT NULL auto_increment,
  `IDEmail` int(11) NOT NULL default '0',
  `FileNameOrg` varchar(255) NOT NULL default '',
  `Filedir` varchar(255) NOT NULL default '',
  `AttachType` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  KEY `IDEmail` (`IDEmail`)
) ENGINE=MyISAM;
