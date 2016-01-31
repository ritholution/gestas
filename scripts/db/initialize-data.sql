--
-- mysql-data.sql
-- 
-- Description
-- Data to insert in the sql database.
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
INSERT INTO `acl` VALUES (1,1,0,0),(2,1,1,0),(3,1,2,0),(4,2,0,0),(5,2,1,0),(6,2,2,0),(7,3,0,0),(8,3,1,0),(9,3,2,0),(10,4,0,1),(11,4,1,1),(12,4,2,1),(13,5,0,1),(14,5,1,1),(15,5,2,1),(16,6,0,1),(17,6,1,1),(18,6,2,1),(19,7,0,1),(20,7,1,1),(21,7,2,1),(22,8,0,0),(23,8,1,0),(24,8,2,0),(25,9,0,0),(26,9,1,0),(27,9,2,0),(28,10,0,0),(29,10,1,0),(30,10,2,0),(31,11,0,1),(32,11,1,1),(33,11,2,1),(34,12,0,0),(35,12,1,0),(36,12,2,0),(37,13,0,0),(38,13,1,0),(39,13,2,0),(40,14,0,0),(41,14,1,0),(42,14,2,0),(43,15,0,0),(44,15,1,0),(45,15,2,0),(46,16,0,0),(47,16,1,0),(48,16,2,0);
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
-- Dumping data for table `aclUser`
--

LOCK TABLES `aclUser` WRITE;
/*!40000 ALTER TABLE `aclUser` DISABLE KEYS */;
INSERT INTO `aclUser` VALUES (1,1),(1,2),(1,3),(1,7),(1,8),(1,9),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,28),(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(2,37),(2,38),(2,39);
/*!40000 ALTER TABLE `aclUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `aclUserType`
--

LOCK TABLES `aclUserType` WRITE;
/*!40000 ALTER TABLE `aclUserType` DISABLE KEYS */;
INSERT INTO `aclUserType` VALUES (1,1),(1,2),(1,3),(1,7),(1,8),(1,9),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,36),
(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,25),(2,26),(2,27),(2,28),(2,29),(2,30),(2,34),(2,35),(2,36),
(3,4),(3,5),(3,6),(3,7),(3,8),(3,9),(3,22),(3,23),(3,24),(3,25),(3,26),(3,27),(3,28),(3,29),(3,30),(3,34),(3,35),(3,36),
(4,4),(4,5),(4,6),(4,7),(4,8),(4,9),(4,10),(4,11),(4,12),(4,13),(4,14),(4,15),(4,16),(4,17),(4,18),(4,19),(4,20),(4,21),(4,22),(4,23),(4,24),(4,25),(4,26),(4,27),(4,28),(4,29),(4,30),(4,31),(4,32),(4,33),(4,34),(4,35),(4,36),(4,43),(4,44),(4,45),(4,46),(4,47),(4,48),
(5,4),(5,5),(5,6),(5,7),(5,8),(5,9),(5,10),(5,11),(5,12),(5,13),(5,14),(5,15),(5,16),(5,17),(5,18),(5,19),(5,20),(5,21),(5,22),(5,23),(5,24),(5,25),(5,26),(5,27),(5,28),(5,29),(5,30),(5,31),(5,32),(5,33),(5,34),(5,35),(5,36),(5,37),(5,38),(5,39),(5,40),(5,41),(5,42),(5,43),(5,44),(5,45),(5,46),(5,47),(5,48);
/*!40000 ALTER TABLE `aclUserType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `appUser`
--

LOCK TABLES `appUser` WRITE;
/*!40000 ALTER TABLE `appUser` DISABLE KEYS */;
INSERT INTO `appUser` VALUES (1,'anonymous',''),(2,'opentia','3d0496416907b827b7909486ac17a008'),(3,'palvarez','d41d8cd98f00b204e9800998ecf8427e'),(4,'fgarrido','d41d8cd98f00b204e9800998ecf8427e'),(5,'abarrio','d41d8cd98f00b204e9800998ecf8427e'),(6,'sortiz','d41d8cd98f00b204e9800998ecf8427e'),(7,'jperez','d41d8cd98f00b204e9800998ecf8427e'),(8,'esolis','d41d8cd98f00b204e9800998ecf8427e'),(9,'palvandoz','d41d8cd98f00b204e9800998ecf8427e'),(10,'lperabad','d41d8cd98f00b204e9800998ecf8427e'),(11,'alopez','d41d8cd98f00b204e9800998ecf8427e'),(12,'amasporcuna','2dbb6c84bee16da9d9494d693a056818'),(13,'mujeresporcuna','f0546b2c217779bd06dbed750d00de4a'),(14,'bascena','dd4ded30f0e3f011c294183593c5991d'),(15,'zaida','4fc1773ea84ab1137acceae5a6aae551'),(16,'valenzuela','0401476c47431ebc7471b3f23b7f7fcb'),(17,'toxiriana','e4d4f6f724a5e454bcac866b31195ccc'),(18,'mujereslopera','b9d9053bca1507af122bb5bcf5d7918e'),(19,'admin','2399962f4e0382954e25caebfd9b0535'),(20,'baloncesto','fcb36ec1c9cdb2c248258d868d0aad14'),(21,'miaque','c78503a6d18d0611ce054abef89d966d'),(22,'astacipor','78c02326a0f048b8a1125d840e1d98ff'),(23,'presidente','f91f9c7c2048a6fb0313259d60d494a4'),(24,'mosca','e569865a2fa013928b58517f69e1dff4');
/*!40000 ALTER TABLE `appUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `association`
--

LOCK TABLES `association` WRITE;
/*!40000 ALTER TABLE `association` DISABLE KEYS */;
INSERT INTO `association` VALUES (1,'','LinuxCordoba',2000,''),(2,'','LinuxMalaga',2000,''),(3,'','GCubo',2000,''),(4,'','Hispalinux',2000,''),(5,'','Arroyo Hondo',2000,''),(6,'','Miaque',2000,''),(7,'','AMPA Colegio Santa Teresa',2000,''),(8,'','ASTACIPOR',2000,''),(9,'','C.B.Porcuna',2000,''),(10,'','Alharilla',2000,''),(11,'','Báscena',2000,''),(12,'','Zaida',2000,''),(13,'','El Despertar Femenino',2000,''),(14,'','Amuva',2000,''),(15,'','Toxiriana',2000,''),(16,'','AMUL',2000,''),(17,'99999999R','La Mosca',1995,'El Carpio');
/*!40000 ALTER TABLE `association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `associationRequest`
--

LOCK TABLES `associationRequest` WRITE;
/*!40000 ALTER TABLE `associationRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `associationRequest` ENABLE KEYS */;
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
INSERT INTO `configuration` VALUES (1,'TITLE','Gestas'),(1,'AUTHOR','OPENTIA S.L.'),(1,'KEYWORDS','GESTAS Gestion Asociaciones'),(1,'STYLE','general.css'),(1,'DESCRIPTION','El Gestor de Asociaciones Libre'),(1,'SITENAME','GESTAS'),(1,'language','es_ES'),(1,'lang_domain','messages'),(1,'class_base','clases'),(1,'template_base','templates'),(1,'dir_locales','locales'),(1,'def_template','template1.html'),(1,'debug_mode','1'),(2,'INDEX_DIR','member_mgmt.mod'),(3,'INDEX_DIR','association_mgmt.mod'),(4,'INDEX_DIR','action_mgmt.mod'),(5,'INDEX_DIR','object_mgmt.mod'),(1,'dir_base','/var/www/taller');
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
/*!40000 ALTER TABLE `email` DISABLE KEYS */;
INSERT INTO `email` VALUES ('fgarrido@opentia.net',2,0,0);
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
INSERT INTO `member` VALUES (1,'Pablo','Álvarez de Sotomayor','Posadillo','','',3),(2,'Francisco','Garrido','Sirvent','30954724J','Plaza de la Constitucion Nº 22',4),(3,'Alberto','Barrionuevo','García','','',5),(4,'Sergio','Tomás','Ortiz','','',6),(5,'Juan Antonio','Pérez','Muñoz','50987684L','',7),(6,'Emilio','Solís','Negredo','30987684Z','',8),(7,'Pedro','Albandoz','Medina','30587684F','',9),(8,'Luisa','Perabad','Luque','80517684C','',10),(9,'Antonio','López','Sirvent','30981684V','',11);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `memberAssociation`
--

LOCK TABLES `memberAssociation` WRITE;
/*!40000 ALTER TABLE `memberAssociation` DISABLE KEYS */;
INSERT INTO `memberAssociation` VALUES (1,1,'\0',''),(1,3,'\0',''),(1,4,'\0',''),(3,1,'\0',''),(3,2,'\0',''),(3,4,'\0','');
/*!40000 ALTER TABLE `memberAssociation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Secciones'),(2,'Gestión de socios'),(3,'Gestión de asociaciones');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menuEntry`
--

LOCK TABLES `menuEntry` WRITE;
/*!40000 ALTER TABLE `menuEntry` DISABLE KEYS */;
INSERT INTO `menuEntry` VALUES (1,'Solicitar Alta de Socio',3,'',1),(2,'Solicitar alta de asociación',12,'',1),(3,'Gestión de socios',15,'2',1),(4,'Gestión de datos de la asociación',14,'',1),(5,'Gestión de asociaciones',15,'3',1),(6,'Modificación de datos personales',8,'',1),(7,'Modificación de la contraseña',9,'',1),(8,'Salir',2,'3',1);
INSERT INTO `menuEntry` VALUES (9,'Secciones',15,'1',2),(10,'Solicitudes de Alta',4,'',2),(11,'Dar de alta socio',16,'',2),(12,'Listado de socios',7,'',2),(13,'Salir',2,'',2);
INSERT INTO `menuEntry` VALUES (14,'Secciones',15,'1',3),(15,'Solicitudes de Alta',13,'',3),(16,'Salir',2,'',3);
/*!40000 ALTER TABLE `menuEntry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `obj`
--

LOCK TABLES `obj` WRITE;
/*!40000 ALTER TABLE `obj` DISABLE KEYS */;
INSERT INTO `obj` VALUES (1,'User Auth','Autenticación de usuario',2,'1'),(2,'User Unauth','Salida del usuario',2,'2'),(3,'Member request','Petición de Alta',2,'3'),(4,'Member Validation','Validación de Alta',2,'4'),(5,'Request List','Listado de peticiones',2,'5'),(6,'Member List','Listado de socios',2,'6'),(7,'Member Cancel','Baja de socios',2,'7'),(8,'Modify Member','Modificación de datos del socios',2,'8'),(9,'Modify Password','Modificación de contraseña',2,'9'),(10,'Association Selection','Selección de asociación',2,'10'),(11,'New User','Creación de un nuevo usuario',2,'11'),(12,'New Association','Creación de una nueva asociación',2,'12'),(13,'Association Validation','Validación de nueva asociación',2,'13'),(14,'Association Modification','Modificación de Asociación',2,'14'),(15,'Change Menu','Cambiar menú del sistema',2,'15'),(16,'Member Registration','Alta de socio',2,'16');
/*!40000 ALTER TABLE `obj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `objAction`
--

LOCK TABLES `objAction` WRITE;
/*!40000 ALTER TABLE `objAction` DISABLE KEYS */;
INSERT INTO `objAction` VALUES (1,1,'User','authentication',1),(2,2,'User','unauthentication',1),(3,3,'MemberManagement','signup_request',2),(4,4,'MemberManagement','signup_validate',2),(5,5,'MemberManagement','signup_list',2),(6,6,'MemberManagement','list_members',2),(7,7,'MemberManagement','cancel_member',2),(8,8,'MemberManagement','modify_member',2),(9,9,'User','modify_password',1),(10,10,'AssociationManagement','load_association',3),(11,11,'User','new_user',1),(12,12,'AssociationManagement','new_association',3),(13,13,'AssociationManagement','association_validate',3),(14,14,'AssociationManagement','modify_association',3),(15,15,'Menu','change_system_menu',1),(16,16,'MemberManagement','member_registration',2);
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
INSERT INTO `pluginAssociation` VALUES (1,1,''),(2,1,'\0'),(3,1,''),(4,1,''),(5,1,''),(1,2,''),(2,2,'\0'),(3,2,''),(4,2,''),(5,2,''),(1,3,''),(2,3,'\0'),(3,3,''),(4,3,''),(5,3,''),(1,4,''),(2,4,'\0'),(3,4,''),(4,4,''),(5,4,''),(1,5,''),(2,5,'\0'),(3,5,''),(4,5,''),(5,5,''),(1,6,''),(2,6,'\0'),(3,6,''),(4,6,''),(5,6,''),(1,7,''),(2,7,'\0'),(3,7,''),(4,7,''),(5,7,''),(1,8,''),(2,8,'\0'),(3,8,''),(4,8,''),(5,8,''),(1,9,''),(2,9,'\0'),(3,9,''),(4,9,''),(5,9,''),(1,10,''),(2,10,'\0'),(3,10,''),(4,10,''),(5,10,''),(1,11,''),(2,11,'\0'),(3,11,''),(4,11,''),(5,11,''),(1,12,''),(2,12,'\0'),(3,12,''),(4,12,''),(5,12,''),(1,13,''),(2,13,'\0'),(3,13,''),(4,13,''),(5,13,''),(1,14,''),(2,14,'\0'),(3,14,''),(4,14,''),(5,14,''),(1,15,''),(2,15,'\0'),(3,15,''),(4,15,''),(5,15,''),(1,16,''),(2,16,'\0'),(3,16,''),(4,16,''),(5,16,'');
/*!40000 ALTER TABLE `pluginAssociation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
INSERT INTO `plugins` VALUES (1,'kernel','Módulo principal del programa',''),(2,'MemberManagement','member management plugin',''),(3,'AssociationManagement','association management plugin',''),(4,'ActionManagement','action management plugin',''),(5,'ObjectManagement','object management plugin','');
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
/*!40000 ALTER TABLE `registrationRequest` DISABLE KEYS */;
INSERT INTO `registrationRequest` VALUES (1,'Juan Antonio','Pérez','Muñoz','50987684L','','jperez',1),(2,'Emilio','Solís','Negredo','30987684Z','','esolis',1),(3,'Pedro','Albandoz','Medina','30587684F','','palvandoz',1),(4,'Luisa','Perabad','Luque','80517684C','','lperabad',1),(5,'Antonio','López','Sirvent','30981684V','','alopez',1);
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
INSERT INTO `userType` VALUES (1,'anonymous',NULL),(2,'user',NULL),(3,'member',NULL),(4,'admin',NULL),(5,'appAdmin',NULL);
/*!40000 ALTER TABLE `userType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `userUserType`
--

LOCK TABLES `userUserType` WRITE;
/*!40000 ALTER TABLE `userUserType` DISABLE KEYS */;
INSERT INTO `userUserType` VALUES (1,1,0),(2,2,0),(2,3,0),(2,4,0),(2,5,0),(3,2,0),(3,3,0),(3,4,0),(3,5,0),(4,2,0),(5,2,0),(5,3,1),(5,3,2),(5,3,4),(5,4,2),(6,2,0),(7,2,0),(8,2,0),(9,2,0),(10,2,0),(11,2,0),(12,2,0),(12,4,10),(13,2,0),(13,4,13),(14,2,0),(14,4,11),(15,2,0),(15,4,12),(16,2,0),(16,4,14),(17,2,0),(17,4,15),(18,2,0),(18,4,16),(19,2,0),(19,4,5),(20,2,0),(20,4,9),(21,2,0),(21,4,5),(22,2,0),(22,4,8),(23,2,0),(23,4,7),(24,2,0),(24,4,17);
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-03-23 12:29:20
