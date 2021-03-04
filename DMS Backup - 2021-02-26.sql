CREATE DATABASE  IF NOT EXISTS `doctor_attendance_mgmt` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `doctor_attendance_mgmt`;
-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 192.168.3.166    Database: doctor_attendance_mgmt
-- ------------------------------------------------------
-- Server version	5.7.28

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
-- Table structure for table `employee_attendance`
--

DROP TABLE IF EXISTS `employee_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_attendance` (
  `PK_employee_attendance` int(11) NOT NULL AUTO_INCREMENT,
  `FK_employee` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `sched_start` datetime NOT NULL,
  `sched_end` datetime NOT NULL,
  `FK_employee_update` int(11) NOT NULL DEFAULT '0',
  `isDelete` tinyint(1) DEFAULT '0',
  `deleteDate` datetime DEFAULT NULL,
  `FK_employee_delete` int(11) DEFAULT NULL,
  PRIMARY KEY (`PK_employee_attendance`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attendance`
--

LOCK TABLES `employee_attendance` WRITE;
/*!40000 ALTER TABLE `employee_attendance` DISABLE KEYS */;
INSERT INTO `employee_attendance` VALUES (1,1,'2020-02-02','2020-02-02 10:00:00',NULL,'2020-02-02 08:00:00','2020-02-02 13:00:00',2,0,NULL,NULL),(2,1,'2021-02-07','2021-02-07 08:00:00',NULL,'2021-02-07 02:00:00','2021-02-07 00:00:00',0,0,NULL,NULL),(3,2,'2021-02-06','2021-02-06 07:00:00','2021-02-06 17:00:00','2021-02-06 08:00:00','2021-02-06 00:00:00',0,0,NULL,NULL),(6,1,'2021-02-21','2021-02-21 08:16:55',NULL,'2021-02-21 02:00:00','2021-02-21 00:00:00',0,1,NULL,NULL),(7,1,'2021-02-21','2021-02-21 08:16:55',NULL,'2021-02-21 02:00:00','2021-02-21 00:00:00',0,0,NULL,NULL),(8,2,'2021-02-22','2021-02-22 06:54:56','2021-02-22 21:58:05','2021-02-22 00:00:00','2021-02-22 00:00:00',0,0,NULL,NULL),(9,1,'2021-02-22','2021-02-22 11:58:41','2021-02-22 00:13:17','2021-02-22 00:00:00','2021-02-22 00:00:00',0,1,'2021-02-22 13:56:55',5),(10,1,'2021-02-22','2021-02-22 11:58:41','2021-02-22 00:13:17','2021-02-22 00:00:00','2021-02-22 00:00:00',0,0,NULL,NULL),(11,5,'2021-02-22','2021-02-22 07:58:54',NULL,'2021-02-22 00:00:00','2021-02-22 00:00:00',0,0,NULL,NULL),(12,6,'2021-02-22','2021-02-22 09:46:36','2021-02-22 19:14:54','2021-02-22 00:00:00','2021-02-22 00:00:00',0,0,NULL,NULL),(13,7,'2021-02-22','2021-02-22 08:46:07','2021-02-22 17:05:05','2021-02-22 00:00:00','2021-02-22 00:00:00',0,0,NULL,NULL),(14,8,'2021-02-22','2021-02-22 08:49:43','2021-02-22 23:00:51','2021-02-22 00:00:00','2021-02-22 00:00:00',0,0,NULL,NULL),(15,2,'2021-02-23','2021-02-23 06:58:32','2021-02-23 18:18:19','2021-02-23 00:00:00','2021-02-23 00:00:00',0,0,NULL,NULL),(16,8,'2021-02-23','2021-02-23 08:58:11','2021-02-23 19:39:15','2021-02-23 00:00:00','2021-02-23 00:00:00',0,0,NULL,NULL),(17,9,'2021-02-23','2021-02-23 09:39:07','2021-02-23 19:05:05','2021-02-23 00:00:00','2021-02-23 00:00:00',0,0,NULL,NULL),(18,6,'2021-02-23','2021-02-23 10:05:05','2021-02-23 19:05:15','2021-02-23 00:00:00','2021-02-23 00:00:00',0,0,NULL,NULL),(19,1,'2021-02-23','2021-02-23 12:04:33','2021-02-23 00:19:09','2021-02-23 00:00:00','2021-02-23 00:00:00',0,0,NULL,NULL),(20,2,'2021-02-24','2021-02-24 06:56:04','2021-02-24 21:00:47','2021-02-24 00:00:00','2021-02-24 00:00:00',0,0,NULL,NULL),(21,5,'2021-02-24','2021-02-24 07:56:12','2021-02-24 18:38:58','2021-02-24 00:00:00','2021-02-24 00:00:00',0,0,NULL,NULL),(22,7,'2021-02-24','2021-02-24 08:42:57','2021-02-24 17:14:18','2021-02-24 00:00:00','2021-02-24 00:00:00',0,0,NULL,NULL),(23,8,'2021-02-24','2021-02-24 08:56:05','2021-02-24 22:10:46','2021-02-24 00:00:00','2021-02-24 00:00:00',0,0,NULL,NULL),(24,9,'2021-02-24','2021-02-24 09:45:04','2021-02-24 21:00:58','2021-02-24 00:00:00','2021-02-24 00:00:00',0,0,NULL,NULL),(25,2,'2021-02-25','2021-02-25 07:50:15','2021-02-25 17:01:11','2021-02-25 00:00:00','2021-02-25 00:00:00',0,0,NULL,NULL),(26,7,'2021-02-25','2021-02-25 07:55:35','2021-02-25 17:00:48','2021-02-25 00:00:00','2021-02-25 00:00:00',0,0,NULL,NULL),(27,1,'2021-02-25','2021-02-25 12:34:20','2021-02-25 23:50:27','2021-02-25 00:00:00','2021-02-25 00:00:00',0,0,NULL,NULL),(28,2,'2021-02-26','2021-02-26 06:50:45','2021-02-26 16:24:41','2021-02-26 07:00:00','2021-02-26 13:00:00',0,0,NULL,NULL),(29,5,'2021-02-26','2021-02-26 07:58:43','2021-02-26 18:26:52','2021-02-26 00:00:00','2021-02-26 00:00:00',0,0,NULL,NULL),(30,1,'2021-02-26','2021-02-26 08:03:06',NULL,'2021-02-26 08:00:00','2021-02-26 17:00:00',0,0,NULL,NULL),(31,7,'2021-02-26','2021-02-26 08:50:14','2021-02-26 17:07:22','2021-02-26 00:00:00','2021-02-26 00:00:00',0,0,NULL,NULL),(32,9,'2021-02-26','2021-02-26 09:45:37','2021-02-26 19:01:42','2021-02-26 00:00:00','2021-02-26 00:00:00',0,0,NULL,NULL),(33,6,'2021-02-26','2021-02-26 11:41:10',NULL,'2021-02-26 00:00:00','2021-02-26 00:00:00',0,0,NULL,NULL);
/*!40000 ALTER TABLE `employee_attendance` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER attendance_audit_trail
AFTER UPDATE ON employee_attendance FOR EACH ROW
BEGIN
	SET @update_type = 0;
    SET @new_log = NULL;
    SET @old_log = NULL;
    
	IF IFNULL(NEW.time_out, '-') != IFNULL(OLD.time_out, '-') THEN
		SET @update_type = 2;
		SET @new_log = NEW.time_out;
		SET @old_log = OLD.time_out;
		
		INSERT INTO employee_attendance_audit (FK_employee_attendance, FK_employee_doctor, FK_employee_user, update_type, attendance_date, log_old, log_new)
		VALUES (NEW.PK_employee_attendance, NEW.FK_employee, NEW.FK_employee_update, @update_type, NEW.attendance_date, @old_log, @new_log);
	END IF;
	
	IF IFNULL(NEW.time_in, '-') != IFNULL(OLD.time_in, '-') THEN
		SET @update_type = 1;
		SET @new_log = NEW.time_in;
		SET @old_log = OLD.time_in;
		
		INSERT INTO employee_attendance_audit (FK_employee_attendance, FK_employee_doctor, FK_employee_user, update_type, attendance_date, log_old, log_new)
		VALUES (NEW.PK_employee_attendance, NEW.FK_employee, NEW.FK_employee_update, @update_type, NEW.attendance_date, @old_log, @new_log);
	END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `employee_attendance_audit`
--

DROP TABLE IF EXISTS `employee_attendance_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_attendance_audit` (
  `PK_employee_attendance_audit` int(11) NOT NULL AUTO_INCREMENT,
  `FK_employee_attendance` int(11) NOT NULL,
  `FK_employee_doctor` int(11) NOT NULL,
  `FK_employee_user` int(11) NOT NULL,
  `update_type` enum('1','2') NOT NULL,
  `attendance_date` date NOT NULL,
  `log_old` datetime DEFAULT NULL,
  `log_new` datetime DEFAULT NULL,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_employee_attendance_audit`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attendance_audit`
--

LOCK TABLES `employee_attendance_audit` WRITE;
/*!40000 ALTER TABLE `employee_attendance_audit` DISABLE KEYS */;
INSERT INTO `employee_attendance_audit` VALUES (1,4,1,0,'1','2021-02-21','2021-02-21 08:16:55','2021-02-21 08:00:00','2021-02-21 21:28:04'),(2,3,2,0,'2','2021-02-06','2021-02-06 17:36:14','2021-02-06 17:00:00','2021-02-21 21:28:04'),(3,3,2,0,'1','2021-02-06','2021-02-06 07:59:11','2021-02-06 07:00:00','2021-02-21 21:28:04'),(4,2,1,0,'1','2021-02-07','2021-02-07 08:10:30','2021-02-07 08:00:00','2021-02-21 22:10:50'),(5,4,1,5,'2','2021-02-21',NULL,'2021-02-21 15:00:00','2021-02-21 22:15:29'),(6,4,1,5,'2','2021-02-21','2021-02-21 15:00:00',NULL,'2021-02-21 22:41:43'),(7,9,1,0,'2','2021-02-22',NULL,'2021-02-22 00:13:17','2021-02-22 13:56:50'),(8,10,1,0,'2','2021-02-22',NULL,'2021-02-22 00:13:17','2021-02-22 13:58:07'),(9,13,7,0,'2','2021-02-22',NULL,'2021-02-22 17:05:05','2021-02-22 17:06:08'),(10,12,6,0,'2','2021-02-22',NULL,'2021-02-22 19:14:54','2021-02-22 19:16:07'),(11,8,2,0,'2','2021-02-22',NULL,'2021-02-22 21:58:05','2021-02-22 22:01:25'),(12,14,8,0,'2','2021-02-22',NULL,'2021-02-22 23:00:51','2021-02-22 23:03:41'),(13,19,1,0,'2','2021-02-23',NULL,'2021-02-23 00:19:09','2021-02-23 12:06:11'),(14,15,2,0,'2','2021-02-23',NULL,'2021-02-23 18:18:19','2021-02-23 18:20:09'),(15,17,9,0,'2','2021-02-23',NULL,'2021-02-23 19:05:05','2021-02-23 19:06:15'),(16,18,6,0,'2','2021-02-23',NULL,'2021-02-23 19:05:15','2021-02-23 19:06:15'),(17,16,8,0,'2','2021-02-23',NULL,'2021-02-23 19:39:15','2021-02-23 19:40:08'),(18,22,7,0,'2','2021-02-24',NULL,'2021-02-24 17:14:18','2021-02-24 17:16:15'),(19,21,5,0,'2','2021-02-24',NULL,'2021-02-24 18:38:58','2021-02-24 18:40:17'),(20,20,2,0,'2','2021-02-24',NULL,'2021-02-24 21:00:47','2021-02-24 21:02:17'),(21,24,9,0,'2','2021-02-24',NULL,'2021-02-24 21:00:58','2021-02-24 21:02:17'),(22,23,8,0,'2','2021-02-24',NULL,'2021-02-24 22:10:46','2021-02-24 22:12:42'),(23,25,2,0,'2','2021-02-25',NULL,'2021-02-25 17:01:11','2021-02-25 17:02:16'),(24,26,7,0,'2','2021-02-25',NULL,'2021-02-25 17:00:48','2021-02-25 17:02:17'),(25,27,1,0,'2','2021-02-25',NULL,'2021-02-25 23:50:27','2021-02-25 23:51:26'),(26,28,2,0,'2','2021-02-26',NULL,'2021-02-26 16:24:41','2021-02-26 16:26:49'),(27,31,7,0,'2','2021-02-26',NULL,'2021-02-26 17:07:22','2021-02-26 17:11:32'),(28,29,5,0,'2','2021-02-26',NULL,'2021-02-26 18:26:52','2021-02-26 18:28:10'),(29,32,9,0,'2','2021-02-26',NULL,'2021-02-26 19:01:42','2021-02-26 19:02:57');
/*!40000 ALTER TABLE `employee_attendance_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_clinic_sched`
--

DROP TABLE IF EXISTS `employee_clinic_sched`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_clinic_sched` (
  `PK_employee_clinic_sched` int(11) NOT NULL AUTO_INCREMENT,
  `FK_employee` int(11) NOT NULL,
  `sched_day` enum('SUN','MON','TUE','WED','THU','FRI','SAT') NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  PRIMARY KEY (`PK_employee_clinic_sched`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_clinic_sched`
--

LOCK TABLES `employee_clinic_sched` WRITE;
/*!40000 ALTER TABLE `employee_clinic_sched` DISABLE KEYS */;
INSERT INTO `employee_clinic_sched` VALUES (1,1,'SUN','1970-01-01 02:00:00','1970-01-01 00:00:00'),(2,1,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(3,1,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(4,1,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(5,1,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(6,1,'FRI','1970-01-01 08:00:00','1970-01-01 17:00:00'),(7,1,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(8,2,'SUN','1970-01-01 08:00:00','1970-01-01 00:00:00'),(9,2,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(10,2,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(11,2,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(12,2,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(13,2,'FRI','1970-01-01 07:00:00','1970-01-01 13:00:00'),(14,2,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(15,3,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(16,3,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(17,3,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(18,3,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(19,3,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(20,3,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(21,3,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(22,4,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(23,4,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(24,4,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(25,4,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(26,4,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(27,4,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(28,4,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(29,5,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(30,5,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(31,5,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(32,5,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(33,5,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(34,5,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(35,5,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(36,6,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(37,6,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(38,6,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(39,6,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(40,6,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(41,6,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(42,6,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(43,7,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(44,7,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(45,7,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(46,7,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(47,7,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(48,7,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(49,7,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(50,8,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(51,8,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(52,8,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(53,8,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(54,8,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(55,8,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(56,8,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00'),(57,9,'SUN','1970-01-01 00:00:00','1970-01-01 00:00:00'),(58,9,'MON','1970-01-01 00:00:00','1970-01-01 00:00:00'),(59,9,'TUE','1970-01-01 00:00:00','1970-01-01 00:00:00'),(60,9,'WED','1970-01-01 00:00:00','1970-01-01 00:00:00'),(61,9,'THU','1970-01-01 00:00:00','1970-01-01 00:00:00'),(62,9,'FRI','1970-01-01 00:00:00','1970-01-01 00:00:00'),(63,9,'SAT','1970-01-01 00:00:00','1970-01-01 00:00:00');
/*!40000 ALTER TABLE `employee_clinic_sched` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `PK_employee` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) NOT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) NOT NULL,
  `birthDate` date DEFAULT NULL,
  `gender` varchar(5) DEFAULT NULL,
  `mobileNo` varchar(15) DEFAULT NULL,
  `AddressLine1` varchar(100) DEFAULT NULL,
  `AddressLine2` varchar(30) DEFAULT NULL,
  `AddressLine3` varchar(30) DEFAULT NULL,
  `employeeNo` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `FK_mscDepartment` int(11) NOT NULL,
  `isDeleted` tinyint(1) DEFAULT '0',
  `clinic` varchar(20) DEFAULT NULL,
  `fingerScanId` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`PK_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Yancy','Suarez','Suarez','1998-05-21','','','','','','0000-1751','',1,0,'215','000001751'),(2,'Renzo','GROMERO','Pangyarihan',NULL,'M','','','','','0000-1514','',1,0,'214','000001514'),(3,'asdf','asdf','asdf',NULL,'','','','','','123123','',1,1,'123',NULL),(4,'Rex','Robles','\'\'adsf',NULL,'F','123123','asdf','asdf','123123','adsf','ylsuarez@ollh.ph',5,1,'123',NULL),(5,'LIDA','SINGSON','VILLANUEVA',NULL,'F','','','','','0000-0750','',1,0,'208','000000750'),(6,'JOHN BENEDICT','','RAMOS',NULL,'M','','','','','0000-1717','',1,0,'200','000001717'),(7,'MARK ANGELO','','ALCANTARA',NULL,'M','','','','','0000-1580','',1,0,'101','000001580'),(8,'JEMUEL','','OSORIO',NULL,'M','','','','','0000-0507','',1,0,'401','000000507'),(9,'CHRYSLER','','NADURATA',NULL,'','','','','','0000-0447','',1,0,'205','000000447');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trgr_insert_employee_clinic_sched
AFTER INSERT
ON employees FOR EACH ROW
BEGIN
	INSERT INTO employee_clinic_sched (FK_employee, sched_day, time_start, time_end)
    VALUES (NEW.PK_employee, 'SUN', '1970-01-01 00:00:00', '1970-01-01 00:00:00'), 
    (NEW.PK_employee, 'MON', '1970-01-01 00:00:00', '1970-01-01 00:00:00'), 
    (NEW.PK_employee, 'TUE', '1970-01-01 00:00:00', '1970-01-01 00:00:00'), 
    (NEW.PK_employee, 'WED', '1970-01-01 00:00:00', '1970-01-01 00:00:00'), 
    (NEW.PK_employee, 'THU', '1970-01-01 00:00:00', '1970-01-01 00:00:00'), 
    (NEW.PK_employee, 'FRI', '1970-01-01 00:00:00', '1970-01-01 00:00:00'), 
    (NEW.PK_employee, 'SAT', '1970-01-01 00:00:00', '1970-01-01 00:00:00');

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `mscdepartment`
--

DROP TABLE IF EXISTS `mscdepartment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mscdepartment` (
  `PK_mscdepartment` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`PK_mscdepartment`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mscdepartment`
--

LOCK TABLES `mscdepartment` WRITE;
/*!40000 ALTER TABLE `mscdepartment` DISABLE KEYS */;
INSERT INTO `mscdepartment` VALUES (1,'Pedia','Pediatrics'),(5,'Department 123ers','Department 123er');
/*!40000 ALTER TABLE `mscdepartment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_features`
--

DROP TABLE IF EXISTS `system_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_features` (
  `PK_system_features` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`PK_system_features`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_features`
--

LOCK TABLES `system_features` WRITE;
/*!40000 ALTER TABLE `system_features` DISABLE KEYS */;
INSERT INTO `system_features` VALUES ('GEN_HOSP_PASS','Generation of Hospital Pass','1'),('MAIL_HOSP_PASS','Emailing of Hospital Pass','0');
/*!40000 ALTER TABLE `system_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_mail_serv`
--

DROP TABLE IF EXISTS `system_mail_serv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_mail_serv` (
  `pk_system_mail_serv` int(11) NOT NULL AUTO_INCREMENT,
  `email_addr` varchar(100) NOT NULL,
  `email_pwd` varchar(100) NOT NULL,
  `serv_host` varchar(100) NOT NULL,
  `serv_port` varchar(10) NOT NULL,
  PRIMARY KEY (`pk_system_mail_serv`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_mail_serv`
--

LOCK TABLES `system_mail_serv` WRITE;
/*!40000 ALTER TABLE `system_mail_serv` DISABLE KEYS */;
INSERT INTO `system_mail_serv` VALUES (2,'ollhservices@gmail.com','OLLH@Manil@70','smtp.gmail.com','587');
/*!40000 ALTER TABLE `system_mail_serv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useracc`
--

DROP TABLE IF EXISTS `useracc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useracc` (
  `PK_userAcc` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pwd` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwd_salt` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FK_userMstr` int(11) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `FK_userType` int(11) NOT NULL,
  PRIMARY KEY (`PK_userAcc`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useracc`
--

LOCK TABLES `useracc` WRITE;
/*!40000 ALTER TABLE `useracc` DISABLE KEYS */;
INSERT INTO `useracc` VALUES (1,'admin','5fd113aa8e671e186be92fa7d0eb5917c35332ee','062519c537b474682bcb3073f706dc99dffd47dc','2020-06-06 09:55:53','2020-06-06 09:55:53',1,1,1),(5,'1751','5ecb853e8a98f0432fcd1e4d09aef4f15b40c477','f3a48d0d49c9cf9a3b02ae4ef365818c9b62abca','2020-08-24 06:18:52','2020-08-24 06:18:52',5,1,1);
/*!40000 ALTER TABLE `useracc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usermstr`
--

DROP TABLE IF EXISTS `usermstr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usermstr` (
  `PK_userMstr` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_userMstr`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usermstr`
--

LOCK TABLES `usermstr` WRITE;
/*!40000 ALTER TABLE `usermstr` DISABLE KEYS */;
INSERT INTO `usermstr` VALUES (1,'System','Administrator','ylsuarez@ollh.ph','2020-08-23 08:15:38','2020-08-23 08:15:38'),(5,'Yancy','Suarez','ylsuarez@ollh.ph','2020-08-24 06:18:52','2020-08-24 06:18:52');
/*!40000 ALTER TABLE `usermstr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usertype`
--

DROP TABLE IF EXISTS `usertype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertype` (
  `PK_userType` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`PK_userType`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usertype`
--

LOCK TABLES `usertype` WRITE;
/*!40000 ALTER TABLE `usertype` DISABLE KEYS */;
INSERT INTO `usertype` VALUES (1,'Administrator'),(2,'MDO'),(3,'Information');
/*!40000 ALTER TABLE `usertype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'doctor_attendance_mgmt'
--

--
-- Dumping routines for database 'doctor_attendance_mgmt'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-02-26 20:53:14
