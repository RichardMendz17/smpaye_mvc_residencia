-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: smpaye_mvc
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumno_curso_detalles`
--

DROP TABLE IF EXISTS `alumno_curso_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumno_curso_detalles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `alumno_id` int NOT NULL,
  `curso_detalle_id` int NOT NULL,
  `fecha_inscripcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `calificacion` int DEFAULT NULL,
  `estatus` enum('inscrito','aprobado','reprobado','retirado') DEFAULT 'inscrito',
  `referencia` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumno_id` (`alumno_id`,`curso_detalle_id`),
  KEY `curso_detalle_id` (`curso_detalle_id`),
  CONSTRAINT `alumno_curso_detalles_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`),
  CONSTRAINT `alumno_curso_detalles_ibfk_2` FOREIGN KEY (`curso_detalle_id`) REFERENCES `cursos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumno_curso_detalles`
--

LOCK TABLES `alumno_curso_detalles` WRITE;
/*!40000 ALTER TABLE `alumno_curso_detalles` DISABLE KEYS */;
/*!40000 ALTER TABLE `alumno_curso_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `id` int NOT NULL,
  `nombre_Alumno` varchar(30) DEFAULT NULL,
  `apellido_Paterno` varchar(20) DEFAULT NULL,
  `apellido_Materno` varchar(20) DEFAULT NULL,
  `comentarios` varchar(255) DEFAULT NULL,
  `id_Carrera` int DEFAULT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `correo_institucional` varchar(60) DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_Carrera_idx` (`id_Carrera`),
  CONSTRAINT `fk_Carrera_Id` FOREIGN KEY (`id_Carrera`) REFERENCES `carreras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (21120077,'Ricardo Adolfo','Mendoza','Escobedo','',1,'2361080962','21120077@ajalpan.tecnm.mx','Masculino'),(21120078,'Ruben Andres','Mendoza','Escobedo','sin comentarios',1,'2361080962','21120078@ajalpan.tecnm.mx','Masculino'),(21120079,'Juan Carlos','Barbosa','Martinez','',1,'2','21120079@ajalpan.tecnm.mx','Masculino');
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignacion_roles`
--

DROP TABLE IF EXISTS `asignacion_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_personal` int DEFAULT NULL,
  `id_rol` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_personal_idx` (`id_personal`),
  KEY `fk_id_rol_idx` (`id_rol`),
  CONSTRAINT `fk_id_personal` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id`),
  CONSTRAINT `fk_id_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_roles`
--

LOCK TABLES `asignacion_roles` WRITE;
/*!40000 ALTER TABLE `asignacion_roles` DISABLE KEYS */;
INSERT INTO `asignacion_roles` VALUES (3,21120077,2),(5,21120077,3),(7,21120078,13),(16,21120079,5),(17,21120079,2),(18,21120078,2),(22,21120077,0);
/*!40000 ALTER TABLE `asignacion_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_Aula` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` VALUES (1,'C1'),(2,'C2 ACTUALIZADO');
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora_eventos`
--

DROP TABLE IF EXISTS `bitacora_eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitacora_eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_Usuario` int DEFAULT NULL,
  `tipo_Operacion` varchar(255) DEFAULT NULL,
  `fecha_operacion_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_Usuario_idx` (`id_Usuario`),
  CONSTRAINT `fk_Usuario` FOREIGN KEY (`id_Usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora_eventos`
--

LOCK TABLES `bitacora_eventos` WRITE;
/*!40000 ALTER TABLE `bitacora_eventos` DISABLE KEYS */;
INSERT INTO `bitacora_eventos` VALUES (1,3,'Creación del registro con el id 1, perteneciente a la tabla tipos_curso','2025-10-31 23:37:54'),(2,3,'Creación del registro con el id 21120078, perteneciente a la tabla alumnos','2025-11-01 17:33:29'),(3,3,'Creación del registro con el id 21120078, perteneciente a la tabla personal','2025-11-01 17:36:35'),(4,3,'Creación del registro con el id 21120079, perteneciente a la tabla personal','2025-11-01 17:37:37'),(5,3,'Actualización del registro con el id 21120079, perteneciente a la tabla personal','2025-11-01 17:41:28'),(6,3,'Actualización del registro con el id 21120077, perteneciente a la tabla personal','2025-11-01 17:47:32'),(7,3,'Actualización del registro con el id 21120078, perteneciente a la tabla personal','2025-11-01 17:47:45'),(8,3,'Actualización del registro con el id 21120078, perteneciente a la tabla alumnos','2025-11-01 17:47:55'),(9,3,'Creación del registro con el id 1, perteneciente a la tabla periodos','2025-11-01 17:55:50'),(10,3,'Eliminación del registro con el id 1, perteneciente a la tabla periodos','2025-11-01 17:55:54'),(11,3,'Creación del registro con el id 2, perteneciente a la tabla periodos','2025-11-01 17:56:00'),(12,3,'Creación del registro con el id 2, perteneciente a la tabla periodos','2025-11-01 17:56:04'),(13,3,'Creación del registro con el id 2, perteneciente a la tabla periodos','2025-11-01 17:56:07'),(14,3,'Creación del registro con el id 2, perteneciente a la tabla periodos','2025-11-01 17:56:35'),(15,3,'Creación del registro con el id 1, perteneciente a la tabla aulas','2025-11-01 23:17:37'),(16,3,'Creación del registro con el id 2, perteneciente a la tabla aulas','2025-11-01 23:17:41'),(17,3,'Actualización del registro con el id 2, perteneciente a la tabla aulas','2025-11-01 23:17:47'),(18,3,'Eliminación del registro con el id 4, perteneciente a la tabla usuarios','2025-11-03 19:26:51'),(19,3,'Eliminación del registro con el id 5, perteneciente a la tabla usuarios','2025-11-03 19:29:27'),(20,3,'Actualización del registro con el id 21120079, perteneciente a la tabla personal','2025-11-19 20:17:49'),(21,3,'Actualización del registro con el id 21120079, perteneciente a la tabla personal','2025-11-19 20:17:53'),(22,3,'Actualización del registro con el id 21120078, perteneciente a la tabla alumnos','2025-11-19 20:24:25'),(23,3,'Actualización del registro con el id 21120077, perteneciente a la tabla alumnos','2025-11-19 20:26:15'),(24,3,'Actualización del registro con el id 21120078, perteneciente a la tabla alumnos','2025-11-19 20:26:22'),(25,3,'Actualización del registro con el id 21120078, perteneciente a la tabla personal','2025-11-19 20:40:49'),(26,3,'Actualización del registro con el id 21120078, perteneciente a la tabla alumnos','2025-11-19 20:41:06'),(27,3,'Actualización del registro con el id 21120078, perteneciente a la tabla alumnos','2025-11-19 20:46:43'),(28,3,'Creación del registro con el id 21120079, perteneciente a la tabla alumnos','2025-11-19 21:36:57'),(29,3,'Eliminación del registro con el id 6, perteneciente a la tabla usuarios','2025-11-19 22:07:30'),(30,3,'Eliminación del registro con el id 7, perteneciente a la tabla usuarios','2025-11-23 18:14:54'),(31,3,'Creación del registro con el id , perteneciente a la tabla asignacion_rol','2025-11-24 23:17:49'),(32,3,'Eliminación del registro con el id 2, perteneciente a la tabla asignacion_roles','2025-11-26 21:41:05'),(33,3,'Eliminación del registro con el id 6, perteneciente a la tabla asignacion_roles','2025-11-26 21:41:19'),(34,3,'Eliminación del registro con el id 8, perteneciente a la tabla usuarios','2025-11-26 21:57:38'),(35,3,'Eliminación del registro con el id 9, perteneciente a la tabla usuarios','2025-11-26 21:57:46'),(36,3,'Creación del registro con el id , perteneciente a la tabla asignacion_rol','2025-11-26 23:27:13'),(37,3,'Eliminación del registro con el id 19, perteneciente a la tabla asignacion_roles','2025-11-26 23:27:21'),(38,3,'Creación del registro con el id , perteneciente a la tabla asignacion_rol','2025-11-26 23:27:34'),(39,3,'Eliminación del registro con el id 20, perteneciente a la tabla asignacion_roles','2025-11-26 23:27:38'),(40,3,'Creación del registro con el id , perteneciente a la tabla asignacion_rol','2025-11-26 23:28:04'),(41,3,'Eliminación del registro con el id 21, perteneciente a la tabla asignacion_roles','2025-11-26 23:29:03'),(42,3,'Creación del registro con el id 20, perteneciente a la tabla cursos','2025-12-05 00:42:15'),(43,3,'Creación del registro con el id 20, perteneciente a la tabla cursos','2025-12-05 00:46:27'),(44,3,'Creación del registro con el id 1, perteneciente a la tabla cursos','2025-12-05 18:32:55'),(45,3,'Creación del registro con el id 1, perteneciente a la tabla cursos','2025-12-05 23:33:13'),(46,3,'Creación del registro con el id 65, perteneciente a la tabla cursos','2025-12-06 00:03:01'),(47,3,'Creación del registro con el id 66, perteneciente a la tabla cursos','2025-12-09 01:19:53'),(48,3,'Creación del registro con el id 67, perteneciente a la tabla cursos','2025-12-09 01:20:51'),(49,3,'Creación del registro con el id 68, perteneciente a la tabla cursos','2025-12-09 01:21:13'),(50,3,'Eliminación del registro con el id 68, perteneciente a la tabla curso','2025-12-09 18:59:34'),(51,3,'Creación del registro con el id 3, perteneciente a la tabla tipos_curso','2025-12-10 00:07:39'),(52,3,'Creación del registro con el id 69, perteneciente a la tabla cursos','2025-12-10 19:23:04'),(53,3,'Creación del registro con el id 70, perteneciente a la tabla cursos','2025-12-10 19:24:42'),(54,3,'Creación del registro con el id 71, perteneciente a la tabla cursos','2025-12-10 19:32:52'),(55,3,'Eliminación del registro con el id 71, perteneciente a la tabla curso','2025-12-10 19:39:03'),(56,3,'Eliminación del registro con el id 70, perteneciente a la tabla curso','2025-12-10 19:39:08'),(57,3,'Eliminación del registro con el id 69, perteneciente a la tabla curso','2025-12-10 19:39:12'),(58,3,'Eliminación del registro con el id 67, perteneciente a la tabla curso','2025-12-10 19:40:49'),(59,3,'Creación del registro con el id 72, perteneciente a la tabla cursos','2025-12-10 19:41:10');
/*!40000 ALTER TABLE `bitacora_eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carreras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_Carrera` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carreras`
--

LOCK TABLES `carreras` WRITE;
/*!40000 ALTER TABLE `carreras` DISABLE KEYS */;
INSERT INTO `carreras` VALUES (1,'Ingeniaría en Sistemas Computacionales');
/*!40000 ALTER TABLE `carreras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curso_requisitos`
--

DROP TABLE IF EXISTS `curso_requisitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curso_requisitos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_curso` int DEFAULT NULL,
  `minimo_aprobados` int DEFAULT NULL,
  `curso_excluido` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_curso_requerido` (`id_curso`),
  KEY `fk_id_curso_excluido_requerido` (`curso_excluido`),
  CONSTRAINT `fk_id_curso_excluido_requerido` FOREIGN KEY (`curso_excluido`) REFERENCES `cursos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_id_curso_requerido` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curso_requisitos`
--

LOCK TABLES `curso_requisitos` WRITE;
/*!40000 ALTER TABLE `curso_requisitos` DISABLE KEYS */;
/*!40000 ALTER TABLE `curso_requisitos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_curso_id` int DEFAULT NULL,
  `url` varchar(32) DEFAULT NULL,
  `periodo_id` int DEFAULT NULL,
  `aula_id` int DEFAULT NULL,
  `encargado_id` int DEFAULT NULL,
  `inscripcion_alumno` enum('Permitido','No permitido') DEFAULT NULL,
  `limite_alumnos` int DEFAULT NULL,
  `estado` enum('Creado','Abierto','Cerrado','Suspendido') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_periodo_id_idx` (`periodo_id`) /*!80000 INVISIBLE */,
  KEY `fk_aula_id_idx` (`aula_id`) /*!80000 INVISIBLE */,
  KEY `fk_tipo_curso_Id_idx` (`tipo_curso_id`),
  KEY `fk_encargado_Id_idx` (`encargado_id`),
  CONSTRAINT `fk_aula_Id` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`),
  CONSTRAINT `fk_encargado_Id` FOREIGN KEY (`encargado_id`) REFERENCES `personal` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_periodo_Id` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`id`),
  CONSTRAINT `fk_tipo_curso_Id` FOREIGN KEY (`tipo_curso_id`) REFERENCES `tipos_curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (72,1,'430bca6025df7b52c3e4de238a5c5f72',2,2,21120077,'Permitido',NULL,'Creado');
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios_clase`
--

DROP TABLE IF EXISTS `horarios_clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios_clase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clase_id` int DEFAULT NULL,
  `dia_semana` varchar(10) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_curso_idx` (`clase_id`),
  CONSTRAINT `fk_id_curso` FOREIGN KEY (`clase_id`) REFERENCES `cursos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios_clase`
--

LOCK TABLES `horarios_clase` WRITE;
/*!40000 ALTER TABLE `horarios_clase` DISABLE KEYS */;
/*!40000 ALTER TABLE `horarios_clase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_modulo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (1,'Créditos Complementarios'),(2,'Residencia Profesional'),(3,'Coordinación de Lenguas Extranjeras'),(4,'Titulaciones'),(5,'Caja'),(6,'Actividades Extraescolares');
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodos`
--

DROP TABLE IF EXISTS `periodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meses_Periodo` varchar(25) DEFAULT NULL,
  `year` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodos`
--

LOCK TABLES `periodos` WRITE;
/*!40000 ALTER TABLE `periodos` DISABLE KEYS */;
INSERT INTO `periodos` VALUES (2,'Enero - Junio2222',2025);
/*!40000 ALTER TABLE `periodos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal`
--

DROP TABLE IF EXISTS `personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal` (
  `id` int NOT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `apellido_Paterno` varchar(20) DEFAULT NULL,
  `apellido_Materno` varchar(20) DEFAULT NULL,
  `genero` varchar(9) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal`
--

LOCK TABLES `personal` WRITE;
/*!40000 ALTER TABLE `personal` DISABLE KEYS */;
INSERT INTO `personal` VALUES (21120077,'Ricardo Adolfo','Mendoza','Escobedo','Masculino'),(21120078,'Ruben Andres','Mendoza','Escobedo','Masculino'),(21120079,'Ruben Andres 2','Mendoza','Escobedo','Masculino');
/*!40000 ALTER TABLE `personal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol` varchar(45) DEFAULT NULL,
  `modulo_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rol_modulo_id_idx` (`modulo_id`),
  CONSTRAINT `fk_rol_modulo_id` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (0,'Administrador',NULL),(1,'Alumno',NULL),(2,'Jefe de Carrera',NULL),(3,'Coordinador de Actividades Extraescolares',NULL),(4,'Instructor de Actividades Extraescolares',NULL),(5,'Coordinador de Créditos Complementarios',NULL),(6,'Supervisor de Créditos Complementarios',NULL),(7,'Coordinador de Residencias Profesionales',NULL),(8,'Asesor interno de Residencia Profesional',NULL),(9,'Asesor externo de Residencia Profesional',NULL),(10,'Coordinador de Lenguas Extranjeras',NULL),(11,'Docente de Lenguas Extranjeras',NULL),(12,'Responsable de Caja',NULL),(13,'Operador de Caja',NULL),(14,'Coordinador de titulaciones',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_curso`
--

DROP TABLE IF EXISTS `tipos_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_curso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(45) DEFAULT NULL,
  `modulo_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tipo_curso_modulo_id_idx` (`modulo_id`),
  CONSTRAINT `fk_tipo_curso_modulo_id` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_curso`
--

LOCK TABLES `tipos_curso` WRITE;
/*!40000 ALTER TABLE `tipos_curso` DISABLE KEYS */;
INSERT INTO `tipos_curso` VALUES (1,'Boxeo',6),(2,'Feria Cientifica',1),(3,'Boxeo 2',6);
/*!40000 ALTER TABLE `tipos_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_usuario`
--

DROP TABLE IF EXISTS `tipos_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(15) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_usuario`
--

LOCK TABLES `tipos_usuario` WRITE;
/*!40000 ALTER TABLE `tipos_usuario` DISABLE KEYS */;
INSERT INTO `tipos_usuario` VALUES (1,'Alumno','Usuarios correspondientes ala tabla de aluimnos'),(2,'Personal','Usuarios correspondientes a la tabla de personal');
/*!40000 ALTER TABLE `tipos_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `persona_id` int DEFAULT NULL,
  `rol` int DEFAULT NULL,
  `tipo_usuario` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rol_id_idx` (`rol`),
  KEY `fk_tipo_usuario_id_idx` (`tipo_usuario`),
  CONSTRAINT `fk_rol_id` FOREIGN KEY (`rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tipo_usuario_id` FOREIGN KEY (`tipo_usuario`) REFERENCES `tipos_usuario` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,'admin@correo.com','$2y$10$L/5fwI.hIW5PhMc4Glm.6ejha7WCcunv2tTiT5pErRRWCGzTUGXUe',21120077,0,2),(10,'21120079@ajalpan.tecnm.mx','$2y$10$ldp53bLI62hr2xAxCxoWdeb1oSGfPYbvfcMadcXc59QNmcPIPgNa2',21120079,1,1),(11,'21120079@staff.ajalpan.tecnm.mx','$2y$10$mtXtfVAw8O92i.bL5iG9L.BmRLO4DqJOiUjzDdMRrUd4Z13cax8n6',21120079,NULL,2);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-10 21:44:34
