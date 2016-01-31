--
-- mysql.sql
-- 
-- Description
-- Database schema.
-- 
-- copyright (c) 2008-2009 OPENTIA s.l. (http://www.opentia.com)
-- 
-- This file is part of GESTAS (http://gestas.opentia.org)
-- 
-- GESTAS is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as
-- published by the Free Software Foundation, either version 3 of the
-- License, or (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
-- 
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.
--

-- MySQL dump 10.11
--
-- Host: hercules93.opentia.net    Database: gestas_taller
-- ------------------------------------------------------
-- Server version	5.0.51a-24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `acl` (
  `idACL` int(11) NOT NULL auto_increment,
  `idObj` int(11) NOT NULL REFERENCES `object`(`idObject`),
  `permType` int(11) NOT NULL,
  `idEnv` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idACL`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclMemberAssoc`
--

DROP TABLE IF EXISTS `aclMemberAssoc`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclMemberAssoc` (
  `idMember` int(11) NOT NULL REFERENCES `member`(`idMember`),
  `idACL` int(11) NOT NULL REFERENCES `acl`(`idACL`),
  `idAssociation` int(11) NOT NULL REFERENCES `association`(`idAssociation`),
  PRIMARY KEY  (`idMember`,`idACL`,`idAssociation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclUser`
--

DROP TABLE IF EXISTS `aclUser`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclUser` (
  `idUser` int(11) NOT NULL REFERENCES `appUser`(`idUser`),
  `idACL` int(11) NOT NULL REFERENCES `acl`(`idACL`),
  PRIMARY KEY  (`idUser`,`idACL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `aclUserType`
--

DROP TABLE IF EXISTS `aclUserType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `aclUserType` (
  `idType` int(11) NOT NULL REFERENCES `userType`(`idType`),
  `idACL` int(11) NOT NULL REFERENCES `acl`(`idACL`),
  PRIMARY KEY  (`idType`,`idACL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `associationRequest`
--

DROP TABLE IF EXISTS `associationRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `associationRequest` (
  `id` int(11) NOT NULL auto_increment,
  `nif` varchar(10) NOT NULL,
  `assocName` varchar(100) NOT NULL,
  `fundationYear` int(11) default NULL,
  `headquarters` varchar(100) default NULL,
  `idUser` int(11) NOT NULL REFERENCES `appUser`(`idUser`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `board`
--

DROP TABLE IF EXISTS `board`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `board` (
  `idMember` int(11) NOT NULL REFERENCES `member`(`idMember`),
  `dateBegin` date NOT NULL,
  `dateEnd` date default NULL,
  `position` varchar(50) NOT NULL,
  PRIMARY KEY  (`idMember`,`dateBegin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `configuration` (
  `idPlugin` int(11) NOT NULL REFERENCES `plugins`(`idPlugin`),
  `confAttrib` varchar(50) NOT NULL,
  `confValue` varchar(150) default NULL,
  PRIMARY KEY  (`idPlugin`,`confAttrib`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `idObject` int(11) NOT NULL REFERENCES `obj`(`idObject`),
  PRIMARY KEY  (`idContent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `email` (
  `address` varchar(50) NOT NULL,
  `idMember` int(11) default NULL REFERENCES `member`(`idMember`),
  `idContact` int(11) default NULL REFERENCES `contact`(`idContact`),
  `idPetition` int(11) default NULL REFERENCES `registrationRequest`(`idPetition`),
  PRIMARY KEY  (`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `idObject` int(11) NOT NULL REFERENCES `obj`(`idObject`),
  PRIMARY KEY  (`idFile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `member` (
  `idMember` int(11) NOT NULL auto_increment,
  `memberName` varchar(30) NOT NULL,
  `firstSurname` varchar(30) NOT NULL,
  `lastSurname` varchar(30) default NULL,
  `dni` varchar(10) default '',
  `address` varchar(50) default NULL,
  `idUser` int(11) default NULL REFERENCES `appUser`(`idUser`),
  PRIMARY KEY  (`idMember`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `memberAssociation`
--

DROP TABLE IF EXISTS `memberAssociation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `memberAssociation` (
  `idMember` int(11) NOT NULL REFERENCES `member`(`idMember`),
  `idAssociation` int(11) NOT NULL REFERENCES `association`(`idAssociation`),
  `isFounder` bit(1) NOT NULL,
  `isActive` bit(1) NOT NULL,
  PRIMARY KEY  (`idMember`,`idAssociation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
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
  `idAction` int(11) default NULL REFERENCES `objAction`(`idAction`),
  `params`   varchar(200) default NULL,
  `idMenu` int(11) default NULL REFERENCES `menu`(`idMenu`),
  PRIMARY KEY  (`idEntry`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `objAction`
--

DROP TABLE IF EXISTS `objAction`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `objAction` (
  `idAction` int(11) NOT NULL auto_increment,
  `idObject` int(11) NOT NULL REFERENCES `obj`(`idObject`),
  `classAction` varchar(50) NOT NULL,
  `methodAction` varchar(50) NOT NULL,
  `idPlugin` int(11) NOT NULL REFERENCES `plugins`(`idPlugin`),
  PRIMARY KEY  (`idAction`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pluginACL`
--

DROP TABLE IF EXISTS `pluginACL`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pluginACL` (
  `idPlugin` int(11) NOT NULL REFERENCES `plugins`(`idPlugin`),
  `idACL` int(11) NOT NULL REFERENCES `acl`(`idACL`),
  PRIMARY KEY  (`idPlugin`,`idACL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pluginAssociation`
--

DROP TABLE IF EXISTS `pluginAssociation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pluginAssociation` (
  `idPlugin` int(11) NOT NULL REFERENCES `plugin`(`idPlugin`),
  `idAssociation` int(11) NOT NULL REFERENCES `association`(`idAssociation`),
  `active` bit(1) NOT NULL default '\0',
  PRIMARY KEY  (`idPlugin`,`idAssociation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `projectMembers`
--

DROP TABLE IF EXISTS `projectMembers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projectMembers` (
  `idProject` int(11) NOT NULL REFERENCES `project`(`idProject`),
  `idMember` int(11) NOT NULL REFERENCES `member`(`idMember`),
  PRIMARY KEY  (`idProject`,`idMember`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `idAssociation` int(11) REFERENCES `association`(`idAssociation`),
  PRIMARY KEY  (`idPetition`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `contact`(`idContact`),
  PRIMARY KEY  (`phoneNumber`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `member`(`idMember`),
  PRIMARY KEY  (`phoneNumber`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `registrationRequest`(`idPetition`),
  PRIMARY KEY  (`phoneNumber`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `unregistrationRequest`
--

DROP TABLE IF EXISTS `unregistrationRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `unregistrationRequest` (
  `idPetition` int(11) NOT NULL auto_increment,
  `idMember` int(11) default NULL REFERENCES `member`(`idMember`),
  `reason` varchar(100) default NULL,
  PRIMARY KEY  (`idPetition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `idAssociation` int(11) default NULL REFERENCES `association`(`idAssociation`),
  PRIMARY KEY  (`idType`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `userUserType`
--

DROP TABLE IF EXISTS `userUserType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `userUserType` (
  `idUser` int(11) NOT NULL REFERENCES `appUser`(`idUser`),
  `idType` int(11) NOT NULL REFERENCES `userType`(`idType`),
  `idAssociation` int(11) NOT NULL REFERENCES `association`(`idAssociation`),
  PRIMARY KEY  (`idUser`,`idType`,`idAssociation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `association`(`idAssociation`),
  PRIMARY KEY  (`url`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `contact`(`idContact`),
  PRIMARY KEY  (`url`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `member`(`idMember`),
  PRIMARY KEY  (`url`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `project`(`idProject`),
  PRIMARY KEY  (`url`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` int(11) NOT NULL REFERENCES `registrationRequest`(`idPetition`),
  PRIMARY KEY  (`url`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
