/**
 * mysql.sql
 *
 * Description
 * This file contains the local configurations of the application
 * 
 * copyright (c) 2008-2009 OPENTIA s.l. (http://www.opentia.com)
 *
 * This file is part of GESTAS (http://gestas.opentia.org)
 * 
 * GESTAS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

--
-- Dumping data for table `accountBook`
--

LOCK TABLES `accountBook` WRITE;
/*!40000 ALTER TABLE `accountBook` DISABLE KEYS */;
/*!40000 ALTER TABLE `accountBook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `acl`
--

LOCK TABLES `acl` WRITE;
/*!40000 ALTER TABLE `acl` DISABLE KEYS */;
INSERT INTO `acl` VALUES (1,1,0),(2,1,1),(3,1,2),(4,2,0),(5,2,1),(6,2,2),(7,3,0),(8,3,1),(9,3,2),(10,4,0),(11,4,1),(12,4,2),(13,5,0),(14,5,1),(15,5,2),(16,6,0),(17,6,1),(18,6,2),(19,7,0),(20,7,1),(21,7,2),(22,8,0),(23,8,1),(24,8,2),(25,9,0),(26,9,1),(27,9,2),(28,10,0),(29,10,1),(30,10,2),(31,11,0),(32,11,1),(33,11,2),(34,12,0),(35,12,1),(36,12,2);
/*!40000 ALTER TABLE `acl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `aclMemberAssoc`
--

LOCK TABLES `aclMemberAssoc` WRITE;
/*!40000 ALTER TABLE `aclMemberAssoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `aclMemberAssoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `aclObject`
--

LOCK TABLES `aclObject` WRITE;
/*!40000 ALTER TABLE `aclObject` DISABLE KEYS */;
/*!40000 ALTER TABLE `aclObject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `aclUser`
--

LOCK TABLES `aclUser` WRITE;
/*!40000 ALTER TABLE `aclUser` DISABLE KEYS */;
INSERT INTO `aclUser` VALUES (1,1),(1,2),(1,3),(1,7),(1,8),(1,9),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36);
INSERT INTO `aclUser` VALUES (2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,28),(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36);
/*!40000 ALTER TABLE `aclUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `aclUserType`
--

LOCK TABLES `aclUserType` WRITE;
/*!40000 ALTER TABLE `aclUserType` DISABLE KEYS */;
INSERT INTO `aclUserType` VALUES
/* Anonymous. */
(1,1,0),(1,2,0),(1,3,0),(1,7,0),(1,8,0),(1,9,0),(1,28,0),(1,29,0),(1,30,0),(1,31,0),(1,32,0),(1,33,0),(1,34,0),(1,35,0),(1,36,0),

/* User. */
(2,4,0),(2,5,0),(2,6,0),(2,7,0),(2,8,0),(2,9,0),(2,25,0),(2,26,0),(2,27,0),(2,28,0),(2,29,0),(2,30,0),(2,34,0),(2,35,0),(2,36,0),

/* Member. */
(3,4,0),(3,5,0),(3,6,0),(3,7,0),(3,8,0),(3,9,0),(3,22,0),(3,23,0),(3,24,0),(3,25,0),(3,26,0),(3,27,0),(3,28,0),(3,29,0),(3,30,0),(3,34,0),(3,35,0),(3,36,0),

/* Application admin. */
(4,4,0),(4,5,0),(4,6,0),(4,7,0),(4,8,0),(4,9,0),(4,10,0),(4,11,0),(4,12,0),(4,13,0),(4,14,0),(4,15,0),(4,16,0),(4,17,0),(4,18,0),(4,19,0),(4,20,0),(4,21,0),(4,22,0),(4,23,0),(4,24,0),(4,25,0),(4,26,0),(4,27,0),(4,28,0),(4,29,0),(4,30,0),(4,31,0),(4,32,0),(4,33,0),(4,34,0),(4,35,0),(4,36,0),

/* Association member. */
(5,4,0),(5,5,0),(5,6,0),(5,7,0),(5,8,0),(5,9,0),(5,22,0),(5,23,0),(5,24,0),(5,25,0),(5,26,0),(5,27,0),(5,28,0),(5,29,0),(5,30,0),(5,34,0),(5,35,0),(5,36,0),
(6,4,0),(6,5,0),(6,6,0),(6,7,0),(6,8,0),(6,9,0),(6,22,0),(6,23,0),(6,24,0),(6,25,0),(6,26,0),(6,27,0),(6,28,0),(6,29,0),(6,30,0),(6,34,0),(6,35,0),(6,36,0),
(7,4,0),(7,5,0),(7,6,0),(7,7,0),(7,8,0),(7,9,0),(7,22,0),(7,23,0),(7,24,0),(7,25,0),(7,26,0),(7,27,0),(7,28,0),(7,29,0),(7,30,0),(7,34,0),(7,35,0),(7,36,0),
(8,4,0),(8,5,0),(8,6,0),(8,7,0),(8,8,0),(8,9,0),(8,22,0),(8,23,0),(8,24,0),(8,25,0),(8,26,0),(8,27,0),(8,28,0),(8,29,0),(8,30,0),(8,34,0),(8,35,0),(8,36,0),
(9,4,0),(9,5,0),(9,6,0),(9,7,0),(9,8,0),(9,9,0),(9,22,0),(9,23,0),(9,24,0),(9,25,0),(9,26,0),(9,27,0),(9,28,0),(9,29,0),(9,30,0),(9,34,0),(9,35,0),(9,36,0),
(10,4,0),(10,5,0),(10,6,0),(10,7,0),(10,8,0),(10,9,0),(10,22,0),(10,23,0),(10,24,0),(10,25,0),(10,26,0),(10,27,0),(10,28,0),(10,29,0),(10,30,0),(10,34,0),(10,35,0),(10,36,0),
(11,4,0),(11,5,0),(11,6,0),(11,7,0),(11,8,0),(11,9,0),(11,22,0),(11,23,0),(11,24,0),(11,25,0),(11,26,0),(11,27,0),(11,28,0),(11,29,0),(11,30,0),(11,34,0),(11,35,0),(11,36,0),
(12,4,0),(12,5,0),(12,6,0),(12,7,0),(12,8,0),(12,9,0),(12,22,0),(12,23,0),(12,24,0),(12,25,0),(12,26,0),(12,27,0),(12,28,0),(12,29,0),(12,30,0),(12,34,0),(12,35,0),(12,36,0),
(13,4,0),(13,5,0),(13,6,0),(13,7,0),(13,8,0),(13,9,0),(13,22,0),(13,23,0),(13,24,0),(13,25,0),(13,26,0),(13,27,0),(13,28,0),(13,29,0),(13,30,0),(13,34,0),(13,35,0),(13,36,0),
(14,4,0),(14,5,0),(14,6,0),(14,7,0),(14,8,0),(14,9,0),(14,22,0),(14,23,0),(14,24,0),(14,25,0),(14,26,0),(14,27,0),(14,28,0),(14,29,0),(14,30,0),(14,34,0),(14,35,0),(14,36,0),
(15,4,0),(15,5,0),(15,6,0),(15,7,0),(15,8,0),(15,9,0),(15,22,0),(15,23,0),(15,24,0),(15,25,0),(15,26,0),(15,27,0),(15,28,0),(15,29,0),(15,30,0),(15,34,0),(15,35,0),(15,36,0),
(16,4,0),(16,5,0),(16,6,0),(16,7,0),(16,8,0),(16,9,0),(16,22,0),(16,23,0),(16,24,0),(16,25,0),(16,26,0),(16,27,0),(16,28,0),(16,29,0),(16,30,0),(16,34,0),(16,35,0),(16,36,0),
(17,4,0),(17,5,0),(17,6,0),(17,7,0),(17,8,0),(17,9,0),(17,22,0),(17,23,0),(17,24,0),(17,25,0),(17,26,0),(17,27,0),(17,28,0),(17,29,0),(17,30,0),(17,34,0),(17,35,0),(17,36,0),
(18,4,0),(18,5,0),(18,6,0),(18,7,0),(18,8,0),(18,9,0),(18,22,0),(18,23,0),(18,24,0),(18,25,0),(18,26,0),(18,27,0),(18,28,0),(18,29,0),(18,30,0),(18,34,0),(18,35,0),(18,36,0),
(19,4,0),(19,5,0),(19,6,0),(19,7,0),(19,8,0),(19,9,0),(19,22,0),(19,23,0),(19,24,0),(19,25,0),(19,26,0),(19,27,0),(19,28,0),(19,29,0),(19,30,0),(19,34,0),(19,35,0),(19,36,0),
(20,4,0),(20,5,0),(20,6,0),(20,7,0),(20,8,0),(20,9,0),(20,22,0),(20,23,0),(20,24,0),(20,25,0),(20,26,0),(20,27,0),(20,28,0),(20,29,0),(20,30,0),(20,34,0),(20,35,0),(20,36,0),

/* Association admin. */
(21,4,0),(21,5,0),(21,6,0),(21,7,0),(21,8,0),(21,9,0),(21,10,1),(21,11,1),(21,12,1),(21,13,1),(21,14,1),(21,15,1),(21,16,1),(21,17,1),(21,18,1),(21,19,1),(21,20,1),(21,21,1),(21,22,0),(21,23,0),(21,24,0),(21,25,0),(21,26,0),(21,27,0),(21,28,0),(21,29,0),(21,30,0),(21,31,1),(21,32,1),(21,33,1),(21,34,0),(21,35,0),(21,36,0),
(22,4,0),(22,5,0),(22,6,0),(22,7,0),(22,8,0),(22,9,0),(22,10,2),(22,11,2),(22,12,2),(22,13,2),(22,14,2),(22,15,2),(22,16,2),(22,17,2),(22,18,2),(22,19,2),(22,20,2),(22,21,2),(22,22,0),(22,23,0),(22,24,0),(22,25,0),(22,26,0),(22,27,0),(22,28,0),(22,29,0),(22,30,0),(22,31,2),(22,32,2),(22,33,2),(22,34,0),(22,35,0),(22,36,0),
(23,4,0),(23,5,0),(23,6,0),(23,7,0),(23,8,0),(23,9,0),(23,10,3),(23,11,3),(23,12,3),(23,13,3),(23,14,3),(23,15,3),(23,16,3),(23,17,3),(23,18,3),(23,19,3),(23,20,3),(23,21,3),(23,22,0),(23,23,0),(23,24,0),(23,25,0),(23,26,0),(23,27,0),(23,28,0),(23,29,0),(23,30,0),(23,31,3),(23,32,3),(23,33,3),(23,34,0),(23,35,0),(23,36,0),
(24,4,0),(24,5,0),(24,6,0),(24,7,0),(24,8,0),(24,9,0),(24,10,4),(24,11,4),(24,12,4),(24,13,4),(24,14,4),(24,15,4),(24,16,4),(24,17,4),(24,18,4),(24,19,4),(24,20,4),(24,21,4),(24,22,0),(24,23,0),(24,24,0),(24,25,0),(24,26,0),(24,27,0),(24,28,0),(24,29,0),(24,30,0),(24,31,4),(24,32,4),(24,33,4),(24,34,0),(24,35,0),(24,36,0),
(25,4,0),(25,5,0),(25,6,0),(25,7,0),(25,8,0),(25,9,0),(25,10,5),(25,11,5),(25,12,5),(25,13,5),(25,14,5),(25,15,5),(25,16,5),(25,17,5),(25,18,5),(25,19,5),(25,20,5),(25,21,5),(25,22,0),(25,23,0),(25,24,0),(25,25,0),(25,26,0),(25,27,0),(25,28,0),(25,29,0),(25,30,0),(25,31,5),(25,32,5),(25,33,5),(25,34,0),(25,35,0),(25,36,0),
(26,4,0),(26,5,0),(26,6,0),(26,7,0),(26,8,0),(26,9,0),(26,10,6),(26,11,6),(26,12,6),(26,13,6),(26,14,6),(26,15,6),(26,16,6),(26,17,6),(26,18,6),(26,19,6),(26,20,6),(26,21,6),(26,22,0),(26,23,0),(26,24,0),(26,25,0),(26,26,0),(26,27,0),(26,28,0),(26,29,0),(26,30,0),(26,31,6),(26,32,6),(26,33,6),(26,34,0),(26,35,0),(26,36,0),
(27,4,0),(27,5,0),(27,6,0),(27,7,0),(27,8,0),(27,9,0),(27,10,7),(27,11,7),(27,12,7),(27,13,7),(27,14,7),(27,15,7),(27,16,7),(27,17,7),(27,18,7),(27,19,7),(27,20,7),(27,21,7),(27,22,0),(27,23,0),(27,24,0),(27,25,0),(27,26,0),(27,27,0),(27,28,0),(27,29,0),(27,30,0),(27,31,7),(27,32,7),(27,33,7),(27,34,0),(27,35,0),(27,36,0),
(28,4,0),(28,5,0),(28,6,0),(28,7,0),(28,8,0),(28,9,0),(28,10,8),(28,11,8),(28,12,8),(28,13,8),(28,14,8),(28,15,8),(28,16,8),(28,17,8),(28,18,8),(28,19,8),(28,20,8),(28,21,8),(28,22,0),(28,23,0),(28,24,0),(28,25,0),(28,26,0),(28,27,0),(28,28,0),(28,29,0),(28,30,0),(28,31,8),(28,32,8),(28,33,8),(28,34,0),(28,35,0),(28,36,0),
(29,4,0),(29,5,0),(29,6,0),(29,7,0),(29,8,0),(29,9,0),(29,10,9),(29,11,9),(29,12,9),(29,13,9),(29,14,9),(29,15,9),(29,16,9),(29,17,9),(29,18,9),(29,19,9),(29,20,9),(29,21,9),(29,22,0),(29,23,0),(29,24,0),(29,25,0),(29,26,0),(29,27,0),(29,28,0),(29,29,0),(29,30,0),(29,31,9),(29,32,9),(29,33,9),(29,34,0),(29,35,0),(29,36,0),
(30,4,0),(30,5,0),(30,6,0),(30,7,0),(30,8,0),(30,9,0),(30,10,10),(30,11,10),(30,12,10),(30,13,10),(30,14,10),(30,15,10),(30,16,10),(30,17,10),(30,18,10),(30,19,10),(30,20,10),(30,21,10),(30,22,0),(30,23,0),(30,24,0),(30,25,0),(30,26,0),(30,27,0),(30,28,0),(30,29,0),(30,30,0),(30,31,10),(30,32,10),(30,33,10),(30,34,0),(30,35,0),(30,36,0),
(31,4,0),(31,5,0),(31,6,0),(31,7,0),(31,8,0),(31,9,0),(31,10,11),(31,11,11),(31,12,11),(31,13,11),(31,14,11),(31,15,11),(31,16,11),(31,17,11),(31,18,11),(31,19,11),(31,20,11),(31,21,11),(31,22,0),(31,23,0),(31,24,0),(31,25,0),(31,26,0),(31,27,0),(31,28,0),(31,29,0),(31,30,0),(31,31,11),(31,32,11),(31,33,11),(31,34,0),(31,35,0),(31,36,0),
(32,4,0),(32,5,0),(32,6,0),(32,7,0),(32,8,0),(32,9,0),(32,10,12),(32,11,12),(32,12,12),(32,13,12),(32,14,12),(32,15,12),(32,16,12),(32,17,12),(32,18,12),(32,19,12),(32,20,12),(32,21,12),(32,22,0),(32,23,0),(32,24,0),(32,25,0),(32,26,0),(32,27,0),(32,28,0),(32,29,0),(32,30,0),(32,31,12),(32,32,12),(32,33,12),(32,34,0),(32,35,0),(32,36,0),
(33,4,0),(33,5,0),(33,6,0),(33,7,0),(33,8,0),(33,9,0),(33,10,13),(33,11,13),(33,12,13),(33,13,13),(33,14,13),(33,15,13),(33,16,13),(33,17,13),(33,18,13),(33,19,13),(33,20,13),(33,21,13),(33,22,0),(33,23,0),(33,24,0),(33,25,0),(33,26,0),(33,27,0),(33,28,0),(33,29,0),(33,30,0),(33,31,13),(33,32,13),(33,33,13),(33,34,0),(33,35,0),(33,36,0),
(34,4,0),(34,5,0),(34,6,0),(34,7,0),(34,8,0),(34,9,0),(34,10,14),(34,11,14),(34,12,14),(34,13,14),(34,14,14),(34,15,14),(34,16,14),(34,17,14),(34,18,14),(34,19,14),(34,20,14),(34,21,14),(34,22,0),(34,23,0),(34,24,0),(34,25,0),(34,26,0),(34,27,0),(34,28,0),(34,29,0),(34,30,0),(34,31,14),(34,32,14),(34,33,14),(34,34,0),(34,35,0),(34,36,0),
(35,4,0),(35,5,0),(35,6,0),(35,7,0),(35,8,0),(35,9,0),(35,10,15),(35,11,15),(35,12,15),(35,13,15),(35,14,15),(35,15,15),(35,16,15),(35,17,15),(35,18,15),(35,19,15),(35,20,15),(35,21,15),(35,22,0),(35,23,0),(35,24,0),(35,25,0),(35,26,0),(35,27,0),(35,28,0),(35,29,0),(35,30,0),(35,31,15),(35,32,15),(35,33,15),(35,34,0),(35,35,0),(35,36,0),
(36,4,0),(36,5,0),(36,6,0),(36,7,0),(36,8,0),(36,9,0),(36,10,16),(36,11,16),(36,12,16),(36,13,16),(36,14,16),(36,15,16),(36,16,16),(36,17,16),(36,18,16),(36,19,16),(36,20,16),(36,21,16),(36,22,0),(36,23,0),(36,24,0),(36,25,0),(36,26,0),(36,27,0),(36,28,0),(36,29,0),(36,30,0),(36,31,16),(36,32,16),(36,33,16),(36,34,0),(36,35,0),(36,36,0);
/*!40000 ALTER TABLE `aclUserType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `appUser`
--

LOCK TABLES `appUser` WRITE;
/*!40000 ALTER TABLE `appUser` DISABLE KEYS */;
INSERT INTO `appUser` VALUES 	
	(1,'anonymous',''),
	(2,'opentia','3d0496416907b827b7909486ac17a008'),
	(3,'palvarez','d41d8cd98f00b204e9800998ecf8427e'),
	(4,'fgarrido','d41d8cd98f00b204e9800998ecf8427e'),
	(5,'abarrio','d41d8cd98f00b204e9800998ecf8427e'),
	(6,'sortiz','d41d8cd98f00b204e9800998ecf8427e'),
	(7,'jperez','d41d8cd98f00b204e9800998ecf8427e'),
	(8,'esolis','d41d8cd98f00b204e9800998ecf8427e'),
	(9,'palvandoz','d41d8cd98f00b204e9800998ecf8427e'),
	(10,'lperabad','d41d8cd98f00b204e9800998ecf8427e'),
	(11,'alopez','d41d8cd98f00b204e9800998ecf8427e'),
	(12,'amasporcuna',MD5('a51tnf62')),
	(13,'mujeresporcuna',MD5('m87rns31')),
	(14,'bascena',MD5('b82tuw65')),
	(15,'zaida',MD5('z73uiy21')),
	(16,'valenzuela',MD5('v59nxc72')),
	(17,'toxiriana',MD5('t39xfe33')),
	(18,'mujereslopera',MD5('m98tin87')),
	(19,'admin',MD5('v13v6v72')),
	(20,'baloncesto',MD5('b78qwe43')),
	(21,'miaque',MD5('porcuna')),
	(22,'astacipor',MD5('a25ert87')),
	(23,'presidente',MD5('2061911'))
;
/*!40000 ALTER TABLE `appUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `association`
--

LOCK TABLES `association` WRITE;
/*!40000 ALTER TABLE `association` DISABLE KEYS */;
INSERT INTO `association` VALUES (1,'','LinuxCordoba',2000,''),(2,'','LinuxMalaga',2000,''),(3,'','GCubo',2000,''),(4,'','Hispalinux',2000,''),(5,'','Arroyo Hondo',2000,''),(6,'','Miaque',2000,''),(7,'','AMPA Colegio Santa Teresa',2000,''),(8,'','ASTACIPOR',2000,''),(9,'','C.B.Porcuna',2000,''),(10,'','Alharilla',2000,''),(11,'','Báscena',2000,''),(12,'','Zaida',2000,''),(13,'','El Despertar Femenino',2000,''),(14,'','Amuva',2000,''),(15,'','Toxiriana',2000,''),(16,'','AMUL',2000,'');
/*!40000 ALTER TABLE `association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `board`
--

LOCK TABLES `board` WRITE;
/*!40000 ALTER TABLE `board` DISABLE KEYS */;
/*!40000 ALTER TABLE `board` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `configuration`
--

LOCK TABLES `configuration` WRITE;
/*!40000 ALTER TABLE `configuration` DISABLE KEYS */;
INSERT INTO `configuration` VALUES (1,'TITLE','Gestas'),(1,'AUTHOR','OPENTIA S.L.'),(1,'KEYWORDS','GESTAS Gestion Asociaciones'),(1,'STYLE','general.css'),(1,'DESCRIPTION','El Gestor de Asociaciones Libre'),(1,'SITENAME','GESTAS'),(1,'language','es_ES'),(1,'lang_domain','messages'),(1,'class_base','clases'),(1,'template_base','templates'),(1,'dir_locales','locales'),(1,'def_template','template1.html'),(1,'debug_mode','1'),(2,'INDEX_DIR','member_mgmt.mod'),(3,'INDEX_DIR','association_mgmt.mod'),(4,'INDEX_DIR','action_mgmt.mod'),(5,'INDEX_DIR','object_mgmt.mod');
/*!40000 ALTER TABLE `configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `decision`
--

LOCK TABLES `decision` WRITE;
/*!40000 ALTER TABLE `decision` DISABLE KEYS */;
/*!40000 ALTER TABLE `decision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `email`
--

LOCK TABLES `email` WRITE;
INSERT INTO `email` VALUES ('fgarrido@opentia.net','2','','');

/*!40000 ALTER TABLE `email` DISABLE KEYS */;
/*!40000 ALTER TABLE `email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `file`
--

LOCK TABLES `file` WRITE;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
/*!40000 ALTER TABLE `file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES 
	(1,'Pablo','Álvarez de Sotomayor','Posadillo','','',3),
	(2,'Francisco','Garrido','Sirvent','30954724J','Plaza de la Constitucion Nº 22',4),
	(3,'Alberto','Barrionuevo','García','','',5),
	(4,'Sergio','Tomás','Ortiz','','',6),
	(5,'Juan Antonio','Pérez','Mu&ntilde;oz','50987684L','',7),
	(6,'Emilio','Solís','Negredo','30987684Z','',8),
	(7,'Pedro','Albandoz','Medina','30587684F','',9),
	(8,'Luisa','Perabad','Luque','80517684C','',10),
	(9,'Antonio','López','Sirvent','30981684V','',11)
	;
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `memberAssociation`
--

LOCK TABLES `memberAssociation` WRITE;
/*!40000 ALTER TABLE `memberAssociation` DISABLE KEYS */;
INSERT INTO `memberAssociation` VALUES (1,1,0,1),(3,1,0,1),(1,3,0,1);
/*!40000 ALTER TABLE `memberAssociation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Secciones');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menuEntry`
--

LOCK TABLES `menuEntry` WRITE;
/*!40000 ALTER TABLE `menuEntry` DISABLE KEYS */;
INSERT INTO `menuEntry` VALUES (1,'Solicitar Alta de Socio',3,1),(2,'Listado de peticiones',4,1),(3,'Gestión de socios',7,1),(4,'Modificación de datos',8,1),(5,'Modificación de la contraseña',9,1),(6,'Solicitar alta de asociacion',12,1),(7,'Salir',2,1);
/*!40000 ALTER TABLE `menuEntry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `obj`
--

LOCK TABLES `obj` WRITE;
/*!40000 ALTER TABLE `obj` DISABLE KEYS */;
INSERT INTO `obj` VALUES (1,'User Auth','Autenticación de usuario',2,'1'),(2,'User Unauth','Salida del usuario',2,'2'),(3,'Member request','Petición de Alta',2,'3'),(4,'Member Validation','Validación de Alta',2,'4'),(5,'Request List','Listado de peticiones',2,'5'),(6,'Member List','Listado de socios',2,'6'),(7,'Member Cancel','Baja de socios',2,'7'),(8,'Modify Member','Modificación de datos del socios',2,'8'),(9,'Modify Password','Modificación de contraseña',2,'9'),(10,'Association Selection','Selección de asociación',2,'10'),(11,'New User','Creación de un nuevo usuario',2,'11'),(12, 'New Association', 'Creación de una nueva asociación', 2 ,'12');
/*!40000 ALTER TABLE `obj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `objAction`
--

LOCK TABLES `objAction` WRITE;
/*!40000 ALTER TABLE `objAction` DISABLE KEYS */;
INSERT INTO `objAction` VALUES (1,1,'User','authentication',1),(2,2,'User','unauthentication',1),(3,3,'MemberManagement','signup_request',3),(4,4,'MemberManagement','signup_validate',3),(5,5,'MemberManagement','signup_list',3),(6,6,'MemberManagement','list_members',3),(7,7,'MemberManagement','cancel_member',3),(8,8,'MemberManagement','modify_member',3),(9,9,'User','modify_password',3),(10,10,'AssociationManagement','load_association',4),(11,11,'User','new_user',1),(12,12,'AssociationManagement','new_association',4);
/*!40000 ALTER TABLE `objAction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `operationType`
--

LOCK TABLES `operationType` WRITE;
/*!40000 ALTER TABLE `operationType` DISABLE KEYS */;
/*!40000 ALTER TABLE `operationType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `pluginACL`
--

LOCK TABLES `pluginACL` WRITE;
/*!40000 ALTER TABLE `pluginACL` DISABLE KEYS */;
/*!40000 ALTER TABLE `pluginACL` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `pluginAssociation`
--

LOCK TABLES `pluginAssociation` WRITE;
/*!40000 ALTER TABLE `pluginAssociation` DISABLE KEYS */;
INSERT INTO `pluginAssociation` VALUES (1,1,1),(2,1,0),(3,1,1),(4,1,1),(5,1,1),(1,2,1),(2,2,0),(3,2,1),(4,2,1),(5,2,1),(1,3,1),(2,3,0),(3,3,1),(4,3,1),(5,3,1),(1,4,1),(2,4,0),(3,4,1),(4,4,1),(5,4,1),(1,5,1),(2,5,0),(3,5,1),(4,5,1),(5,5,1),(1,6,1),(2,6,0),(3,6,1),(4,6,1),(5,6,1),(1,7,1),(2,7,0),(3,7,1),(4,7,1),(5,7,1),(1,8,1),(2,8,0),(3,8,1),(4,8,1),(5,8,1),(1,9,1),(2,9,0),(3,9,1),(4,9,1),(5,9,1),(1,10,1),(2,10,0),(3,10,1),(4,10,1),(5,10,1),(1,11,1),(2,11,0),(3,11,1),(4,11,1),(5,11,1),(1,12,1),(2,12,0),(3,12,1),(4,12,1),(5,12,1),(1,13,1),(2,13,0),(3,13,1),(4,13,1),(5,13,1),(1,14,1),(2,14,0),(3,14,1),(4,14,1),(5,14,1),(1,15,1),(2,15,0),(3,15,1),(4,15,1),(5,15,1),(1,16,1),(2,16,0),(3,16,1),(4,16,1),(5,16,1);
/*!40000 ALTER TABLE `pluginAssociation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
INSERT INTO `plugins` VALUES (1,'kernel','Módulo principal del programa','1'),(2,'MemberManagement','member management plugin','1'),(3,'AssociationManagement','association management plugin','1'),(4,'ActionManagement','action management plugin','1'),(5,'ObjectManagement','object management plugin','1');
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `projectMembers`
--

LOCK TABLES `projectMembers` WRITE;
/*!40000 ALTER TABLE `projectMembers` DISABLE KEYS */;
/*!40000 ALTER TABLE `projectMembers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `registrationRequest`
--

LOCK TABLES `registrationRequest` WRITE;
INSERT INTO `registrationRequest` VALUES 
	(1,'Juan Antonio','Pérez','Mu&ntilde;oz','50987684L','','jperez',1),
	(2,'Emilio','Solís','Negredo','30987684Z','','esolis',1),
	(3,'Pedro','Albandoz','Medina','30587684F','','palvandoz',1),
	(4,'Luisa','Perabad','Luque','80517684C','','lperabad',1),
	(5,'Antonio','López','Sirvent','30981684V','','alopez',1)
;
/*!40000 ALTER TABLE `registrationRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `registrationRequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `telephoneContact`
--

LOCK TABLES `telephoneContact` WRITE;
/*!40000 ALTER TABLE `telephoneContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `telephoneContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `telephoneMember`
--

LOCK TABLES `telephoneMember` WRITE;
/*!40000 ALTER TABLE `telephoneMember` DISABLE KEYS */;
/*!40000 ALTER TABLE `telephoneMember` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `telephoneRequest`
--

LOCK TABLES `telephoneRequest` WRITE;
/*!40000 ALTER TABLE `telephoneRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `telephoneRequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `unregistrationRequest`
--

LOCK TABLES `unregistrationRequest` WRITE;
/*!40000 ALTER TABLE `unregistrationRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `unregistrationRequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `userType`
--

LOCK TABLES `userType` WRITE;
/*!40000 ALTER TABLE `userType` DISABLE KEYS */;
INSERT INTO `userType` VALUES (1,'anonymous',NULL),(2,'user',NULL),(3,'member',NULL),(4,'admin',NULL),(5,'member',1),(6,'member',2),(7,'member',3),(8,'member',4),(9,'member',5),(10,'member',6),(11,'member',7),(12,'member',8),(13,'member',9),(14,'member',10),(15,'member',11),(16,'member',12),(17,'member',13),(18,'member',14),(19,'member',15),(20,'member',16),(21,'admin',1),(22,'admin',2),(23,'admin',3),(24,'admin',4),(25,'admin',5),(26,'admin',6),(27,'admin',7),(28,'admin',8),(29,'admin',9),(30,'admin',10),(31,'admin',11),(32,'admin',12),(33,'admin',13),(34,'admin',14),(35,'admin',15),(36,'admin',16);
/*!40000 ALTER TABLE `userType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `userUserType`
--

LOCK TABLES `userUserType` WRITE;
/*!40000 ALTER TABLE `userUserType` DISABLE KEYS */;
INSERT INTO `userUserType` VALUES (1,1),(2,2),(2,4),(3,2),(4,2),(5,2),(6,2),(12,30),(13,33),(14,31),(15,32),(16,34),(17,35),(18,36),(19,25),(20,29),(21,26),(22,28),(23,27);
/*!40000 ALTER TABLE `userUserType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `webAssociation`
--

LOCK TABLES `webAssociation` WRITE;
/*!40000 ALTER TABLE `webAssociation` DISABLE KEYS */;
/*!40000 ALTER TABLE `webAssociation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `webContact`
--

LOCK TABLES `webContact` WRITE;
/*!40000 ALTER TABLE `webContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `webContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `webMember`
--

LOCK TABLES `webMember` WRITE;
/*!40000 ALTER TABLE `webMember` DISABLE KEYS */;
/*!40000 ALTER TABLE `webMember` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `webProject`
--

LOCK TABLES `webProject` WRITE;
/*!40000 ALTER TABLE `webProject` DISABLE KEYS */;
/*!40000 ALTER TABLE `webProject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `webRequest`
--

LOCK TABLES `webRequest` WRITE;
/*!40000 ALTER TABLE `webRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `webRequest` ENABLE KEYS */;
UNLOCK TABLES;
