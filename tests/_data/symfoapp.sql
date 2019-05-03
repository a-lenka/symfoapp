-- MySQL dump 10.13  Distrib 8.0.15, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: symfoapp
-- ------------------------------------------------------
-- Server version	8.0.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    SET character_set_client = utf8mb4 ;

CREATE TABLE `migration_versions`
(
    `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
    `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`version`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions`
    WRITE;

INSERT INTO `migration_versions`
    VALUES
    (
        '20190410172012','2019-05-02 13:47:06'
    ),(
        '20190426100156','2019-05-02 13:47:07'
    ),(
        '20190427042248','2019-05-02 13:47:07'
    ),(
        '20190502132302','2019-05-02 13:47:07'
    );
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    SET character_set_client = utf8mb4 ;

CREATE TABLE `task`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date_deadline` datetime NOT NULL,
    `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `owner_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_527EDB257E3C61F9` (`owner_id`),
    CONSTRAINT `FK_527EDB257E3C61F9`
    FOREIGN KEY (`owner_id`)
        REFERENCES `user` (`id`)
)
ENGINE=InnoDB AUTO_INCREMENT=6
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task`
    WRITE;

INSERT INTO `task`
    VALUES
    (
        1,'First Admin task','2000-09-30 20:00:00','In progress',2
    ),(
        2,'Second Admin task','2000-10-30 20:00:00','In progress',2
    ),(
        3,'First User task','2001-09-30 20:00:00','In progress',3
    ),(
        4,'Second User task','2001-10-30 20:00:00','In progress',3
    ),(
        5,'Third User task','2001-10-30 20:00:00','In progress',3
    );
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
    /*!40101 SET @saved_cs_client     = @@character_set_client */;
    SET character_set_client = utf8mb4 ;

CREATE TABLE `user`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
    `roles` json NOT NULL,
    `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_8d93d649e7927c74` (`email`)
)
ENGINE=InnoDB AUTO_INCREMENT=5
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user`
    WRITE;

INSERT INTO `user`
    VALUES (
        1,'root@mail.ru','[\"ROLE_ROOT\"]','$argon2i$v=19$m=1024,t=2,p=2$V2I5cXBGWkdaTGJteGFBdA$oLGqiCH8t1d5JiMXhgwZ70uTJEanW5eKzuNu2US0RHc','root.png'
    ),(
        2,'admin@mail.ru','[\"ROLE_ADMIN\"]','$argon2i$v=19$m=1024,t=2,p=2$WTQzUFlGbnpRaS9PWjJyZQ$oNbBDbQ688OF+Y1wHIwwd9lcAgxNfBIzhd7M80gTBmI','admin.png'
    ),(
        3,'user@mail.ru','[]','$argon2i$v=19$m=1024,t=2,p=2$aXZQS0VPRkt6UHliOHhVeQ$5xODP96ySKXXqjFH18AEQ/ExQmEHhVkpcae9uKJkO8o','user.png'
    ),(
        4,'anonymous@mail.ru','[]','$argon2i$v=19$m=1024,t=2,p=2$NjNQTUpXLk9iQk9lcEtqOQ$kX44aRUpZT5TvpJ1D4eFyuQq6LhU12tp37YnWOvlw6w','anonymous.png'
    );
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-05-02 18:07:52
