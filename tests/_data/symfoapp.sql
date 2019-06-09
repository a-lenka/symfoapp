-- MySQL dump 10.13  Distrib 8.0.15, for Linux (x86_64)
--
-- Host: symfoapp    Database: symfoapp
-- ------------------------------------------------------
-- Server version	8.0.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
SET character_set_client = utf8mb4;
CREATE TABLE `migration_versions`
(
    `version`     varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `executed_at` datetime                                                     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`version`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions`
    DISABLE KEYS */;
INSERT INTO `migration_versions` (`version`, `executed_at`)
VALUES ('20190410172012', '2019-05-02 13:47:06'),
       ('20190426100156', '2019-05-02 13:47:07'),
       ('20190427042248', '2019-05-02 13:47:07'),
       ('20190502132302', '2019-05-02 13:47:07'),
       ('20190517123310', '2019-05-17 12:37:32');
/*!40000 ALTER TABLE `migration_versions`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
SET character_set_client = utf8mb4;
CREATE TABLE `task`
(
    `id`            int(11)                                                       NOT NULL AUTO_INCREMENT,
    `title`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `date_deadline` datetime                                                      NOT NULL,
    `state`         varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    `owner_id`      int(11)                                                       NOT NULL,
    `icon`          varchar(50) COLLATE utf8mb4_unicode_ci                        NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_527EDB257E3C61F9` (`owner_id`),
    CONSTRAINT `FK_527EDB257E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task`
    DISABLE KEYS */;
INSERT INTO `task` (`id`, `title`, `date_deadline`, `state`, `owner_id`, `icon`)
VALUES (
        1,
        'First Admin task',
        '2000-09-30 20:00:00',
        'In progress',
        2,
        'art_5cdfd36a511c9.png'
    ),(
        2,
        'Second Admin task',
        '2000-10-30 20:00:00',
        'In progress', 2,
        'bookshelf_5cdfd36a5207c.png'
    ),(
        3,
        'First User task',
        '2001-09-30 20:00:00',
        'In progress',
        3,
        'briefcase_5cdfd36a52370.png'
    ),(
        4,
        'Second User task',
        '2001-10-30 20:00:00',
        'In progress',
        3,
        'brightness_5cdfd36a5267e.png'
    ),(
        5,
        'Third User task',
        '2001-10-30 20:00:00',
        'In progress',
        3,
        'brush-pencil_5cdfd36a52944.png'
);
/*!40000 ALTER TABLE `task`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
SET character_set_client = utf8mb4;
CREATE TABLE `user`
(
    `id`       int(11)                                                       NOT NULL AUTO_INCREMENT,
    `email`    varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `roles`    json                                                          NOT NULL,
    `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `avatar`   varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_8d93d649e7927c74` (`email`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user`
    DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `avatar`)
VALUES (
          1,
          'root@mail.ru',
          '[\"ROLE_ROOT\"]',
          '$argon2i$v=19$m=1024,t=2,p=2$TjJITXMvdjdEbTZaWmtJUw$dwBHJeF4Rf+Mie9r/CueQ/+Kq2f81IvShqPDdPtuWtg',
          'root_5cdfd36a2bec1.png'
        ),(
           2,
           'admin@mail.ru',
           '[\"ROLE_ADMIN\"]',
           '$argon2i$v=19$m=1024,t=2,p=2$aTY1ci9nMTBYQ3VEVHlGaw$0WVNfcLxi1JaBegswdq6P3wu6FGkYhWzdkxQKB1oQ8Y',
           'admin_5cdfd36a37ccf.png'
        ),(
           3,
           'user@mail.ru',
           '[]',
           '$argon2i$v=19$m=1024,t=2,p=2$ZEoueWNmek1rcXlselZ3Sg$+SPgxIYJvlQltES6D1ku77+Q9s3B+U2EOsGVD1XbMMQ',
           'user_5cdfd36a389f6.png'
        ),(
           4,
           'anonymous@mail.ru',
           '[]',
           '$argon2i$v=19$m=1024,t=2,p=2$Y2R3ZjExTVA2LklXWkVrRQ$dR5COPn3EU07Ti/gs5/TXm8FAHIw/6nxtyijO7eNMaM',
           'anonymous_5cdfd36a396ca.png'
        ),(
           5,
           'housewife@mail.ru',
           '[]',
           '$argon2i$v=19$m=1024,t=2,p=2$TmpMbnFmWFh4Z2NrL2s0RA$aVBCWP5HITpn9E9E6JBuitT8O4QO569IreU6NBlJmDU',
           'housewife_5cdfd36a3a30a.jpeg'
        ),(
           6,
           'student@mail.ru',
           '[]',
           '$argon2i$v=19$m=1024,t=2,p=2$VkR1ZENWNXoxdTZhQjVPNg$8TW8xlKbofVvKPclO/07+1MMA93RHeVs+VotdM0g5+4',
           'student_5cdfd36a44b30.jpeg'
       );
/*!40000 ALTER TABLE `user`
    ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

-- Dump completed on 2019-05-18 11:42:33
