--
-- Table structure for table `accountBook`
--

DROP TABLE IF EXISTS `accountBook`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `accountBook` (
  `idOperation` int(11) NOT NULL auto_increment,
  `operationDesc` varchar(100) NOT NULL,
  `operationAmount` int(11) NOT NULL,
  `odOperationType` int(11) default NULL,
  PRIMARY KEY  (`idOperation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `acl` (
  `idACL` int(11) NOT NULL auto_increment,
  `idObj` int(11) NOT NULL,
  `permType` int(11) NOT NULL,
  PRIMARY KEY  (`idACL`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclMemberAssoc`
--

DROP TABLE IF EXISTS `aclMemberAssoc`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclMemberAssoc` (
  `idMember` int(11) NOT NULL default '0',
  `idACL` int(11) NOT NULL default '0',
  `idAssociation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idMember`,`idACL`,`idAssociation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclObject`
--

DROP TABLE IF EXISTS `aclObject`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclObject` (
  `idPermission` int(11) NOT NULL default '0',
  `idObject` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idPermission`,`idObject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclUser`
--

DROP TABLE IF EXISTS `aclUser`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclUser` (
  `idUser` int(11) NOT NULL default '0',
  `idACL` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idUser`,`idACL`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclUserType`
--

DROP TABLE IF EXISTS `aclUserType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclUserType` (
  `idType` int(11) NOT NULL default '0',
  `idACL` int(11) NOT NULL default '0',
  `idAssociation` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idType`,`idACL`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclMemberAssoc`
--

--
-- Table structure for table `appUser`
--

DROP TABLE IF EXISTS `appUser`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `appUser` (
  `idUser` int(11) NOT NULL auto_increment,
  `login` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY  (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `association`
--

DROP TABLE IF EXISTS `association`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `association` (
  `idAssociation` int(11) NOT NULL auto_increment,
  `nif` varchar(10) NOT NULL,
  `assocName` varchar(100) NOT NULL,
  `fundationYear` int(11) default NULL,
  `headquarters` varchar(100) default NULL,
  PRIMARY KEY  (`idAssociation`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `associationRequest`
--

DROP TABLE IF EXISTS `associationRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `associationRequest` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`nif` varchar( 10 ) NOT NULL ,
`assocName` varchar( 100 ) NOT NULL ,
`fundationYear` int( 11 ) default NULL ,
`headquarters` varchar( 100 ) default NULL ,
`idUser` int(11) NOT NULL,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM AUTO_INCREMENT =3 DEFAULT CHARSET = utf8;


--
-- Table structure for table `board`
--

DROP TABLE IF EXISTS `board`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `board` (
  `idMember` int(11) NOT NULL,
  `dateBegin` date NOT NULL,
  `dateEnd` date default NULL,
  `position` varchar(50) NOT NULL,
  PRIMARY KEY  (`idMember`,`dateBegin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `configuration` (
  `idPlugin` int(11) NOT NULL,
  `confAttrib` varchar(50) NOT NULL,
  `confValue` varchar(150) default NULL,
  PRIMARY KEY  (`idPlugin`,`confAttrib`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `contact` (
  `idContact` int(11) NOT NULL auto_increment,
  `contactName` varchar(20) default NULL,
  `firstSurname` varchar(20) default NULL,
  `lastSurname` varchar(20) default NULL,
  `address` varchar(20) default NULL,
  PRIMARY KEY  (`idContact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `content` (
  `idContent` int(11) NOT NULL auto_increment,
  `content` blob NOT NULL,
  `contentType` int(11) NOT NULL,
  `idObject` int(11) NOT NULL,
  PRIMARY KEY  (`idContent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `decision`
--

DROP TABLE IF EXISTS `decision`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `decision` (
  `idDecision` int(11) NOT NULL auto_increment,
  `decision_date` date NOT NULL,
  `decision_maked` varchar(100) NOT NULL,
  `votesInFavor` int(11) NOT NULL,
  `votesAgainst` int(11) NOT NULL,
  `abstentions` int(11) NOT NULL,
  PRIMARY KEY  (`idDecision`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `email` (
  `address` varchar(50) NOT NULL,
  `idMember` int(11) default NULL,
  `idContact` int(11) default NULL,
  `idPetition` int(11) default NULL,
  PRIMARY KEY  (`address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `file` (
  `idFile` int(11) NOT NULL auto_increment,
  `pathFile` varchar(100) NOT NULL,
  `idObject` int(11) NOT NULL,
  PRIMARY KEY  (`idFile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `logs` (
  `idLog` int(11) NOT NULL auto_increment,
  `dateEntry` date NOT NULL,
  `logText` varchar(100) default NULL,
  PRIMARY KEY  (`idLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `member` (
  `idMember` int(11) NOT NULL AUTO_INCREMENT,
  `memberName` varchar(30) NOT NULL,
  `firstSurname` varchar(30) NOT NULL,
  `lastSurname` varchar(30) default NULL,
  `dni` varchar(10) default '',
  `address` varchar(50) default NULL,
  `idUser` int(11) UNIQUE REFERENCES appUser.idUser,
  PRIMARY KEY  (`idMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `memberAssociation`
--

DROP TABLE IF EXISTS `memberAssociation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `memberAssociation` (
  `idMember` int(11) NOT NULL default '0',
  `idAssociation` int(11) NOT NULL default '0',
  `isFounder` bit(1) NOT NULL,
  `isActive` bit(1) NOT NULL,
  PRIMARY KEY  (`idMember`,`idAssociation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `menuEntry`
--

DROP TABLE IF EXISTS `menuEntry`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `menuEntry` (
  `idEntry` int(11) NOT NULL auto_increment,
  `entryName` varchar(40) NOT NULL,
  `idAction` int(11) default NULL,
  `idMenu` int(11) default NULL,
  PRIMARY KEY  (`idEntry`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `obj`
--

DROP TABLE IF EXISTS `obj`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `obj` (
  `idObject` int(11) NOT NULL,
  `objectName` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `objectType` int(11) NOT NULL,
  `objectValue` varchar(50) default NULL,
  PRIMARY KEY  (`idObject`),
  UNIQUE KEY `objectName` (`objectName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `objAction`
--

DROP TABLE IF EXISTS `objAction`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `objAction` (
  `idObjAction` int(11) NOT NULL auto_increment,
  `idObject` int(11) NOT NULL,
  `classAction` varchar(50) NOT NULL,
  `methodAction` varchar(50) NOT NULL,
  `idPlugin` int(11) NOT NULL,
  PRIMARY KEY  (`idObjAction`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `operationType`
--

DROP TABLE IF EXISTS `operationType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `operationType` (
  `idOperationType` int(11) NOT NULL auto_increment,
  `operationType` varchar(50) default NULL,
  PRIMARY KEY  (`idOperationType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pluginACL`
--

DROP TABLE IF EXISTS `pluginACL`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pluginACL` (
  `idPlugin` int(11) NOT NULL,
  `idACL` int(11) NOT NULL,
  PRIMARY KEY  (`idPlugin`,`idACL`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pluginAssociation`
--

DROP TABLE IF EXISTS `pluginAssociation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pluginAssociation` (
  `idPlugin` int(11) NOT NULL,
  `idAssociation` int(11) NOT NULL,
  `active` bit(1) NOT NULL default '\0',
  PRIMARY KEY  (`idPlugin`,`idAssociation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugins` (
  `idPlugin` int(11) NOT NULL auto_increment,
  `plugName` varchar(30) NOT NULL,
  `description` varchar(200) default NULL,
  `base` bit(1) NOT NULL default '\0',
  PRIMARY KEY  (`idPlugin`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `project` (
  `idProject` int(11) NOT NULL auto_increment,
  `projectName` varchar(50) NOT NULL,
  `dateBegin` date default NULL,
  `dateEnd` date default NULL,
  PRIMARY KEY  (`idProject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `projectMembers`
--

DROP TABLE IF EXISTS `projectMembers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projectMembers` (
  `idProject` int(11) NOT NULL default '0',
  `idMember` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idProject`,`idMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `registrationRequest`
--

DROP TABLE IF EXISTS `registrationRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `registrationRequest` (
  `idPetition` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `firstSurname` varchar(30) NOT NULL,
  `lastSurname` varchar(30) NOT NULL,
  `dni` varchar(10) default NULL,
  `address` varchar(50) default NULL,
  `login` varchar(30) NOT NULL,
  `idAssociation` int(11) default NULL,
  PRIMARY KEY  (`idPetition`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `telephoneContact`
--

DROP TABLE IF EXISTS `telephoneContact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `telephoneContact` (
  `phoneNumber` int(11) NOT NULL,
  `phoneType` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`phoneNumber`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `telephoneMember`
--

DROP TABLE IF EXISTS `telephoneMember`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `telephoneMember` (
  `phoneNumber` int(11) NOT NULL,
  `phoneType` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`phoneNumber`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `telephoneRequest`
--

DROP TABLE IF EXISTS `telephoneRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `telephoneRequest` (
  `phoneNumber` int(11) NOT NULL,
  `phoneType` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`phoneNumber`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `unregistrationRequest`
--

DROP TABLE IF EXISTS `unregistrationRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `unregistrationRequest` (
  `idPetition` int(11) NOT NULL auto_increment,
  `idMember` int(11) default NULL,
  `reason` varchar(100) default NULL,
  PRIMARY KEY  (`idPetition`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `userType`
--

DROP TABLE IF EXISTS `userType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `userType` (
  `idType` int(11) NOT NULL auto_increment,
  `usrType` varchar(50) default NULL,
  `idAssociation` int(11) default NULL,
  PRIMARY KEY  (`idType`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `userUserType`
--

DROP TABLE IF EXISTS `userUserType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `userUserType` (
  `idUser` int(11) NOT NULL default '0',
  `idType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idUser`,`idType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `webAssociation`
--

DROP TABLE IF EXISTS `webAssociation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `webAssociation` (
  `url` varchar(100) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`url`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `webContact`
--

DROP TABLE IF EXISTS `webContact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `webContact` (
  `url` varchar(100) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`url`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `webMember`
--

DROP TABLE IF EXISTS `webMember`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `webMember` (
  `url` varchar(100) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`url`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `webProject`
--

DROP TABLE IF EXISTS `webProject`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `webProject` (
  `url` varchar(100) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`url`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `webRequest`
--

DROP TABLE IF EXISTS `webRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `webRequest` (
  `url` varchar(100) NOT NULL,
  `description` varchar(100) default NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`url`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

source mysql-data.sql
source mysql-configs.sql
