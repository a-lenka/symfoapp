-- MySQL dump 10.13  Distrib 8.0.15, for Linux (x86_64)
--
-- Host: symfoapp    Database: symfoapp
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
USE sql7295193;
drop table IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `migration_versions` (
    `version` VARCHAR(14)CHARACTER SET UTF8MB4 COLLATE UTF8MB4_UNICODE_CI NOT NULL,
    `executed_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`version`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8MB4 COLLATE = UTF8MB4_UNICODE_CI;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
insert into `migration_versions` VALUES
(
    '20190410172012','2019-05-02 13:47:06'
),(
    '20190426100156','2019-05-02 13:47:07'
),(
    '20190427042248','2019-05-02 13:47:07'
),(
    '20190502132302','2019-05-02 13:47:07'
),(
    '20190517123310','2019-05-17 12:37:32'
),(
    '20190525054239','2019-05-25 05:46:09'
),(
    '20190602042050','2019-06-02 04:25:27'
);

/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

drop table IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `task` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255)CHARACTER SET UTF8MB4 COLLATE UTF8MB4_UNICODE_CI NOT NULL,
    `date_deadline` DATETIME NOT NULL,
    `state` VARCHAR(50)CHARACTER SET UTF8MB4 COLLATE UTF8MB4_UNICODE_CI NOT NULL,
    `owner_id` INT(11) NOT NULL,
    `icon` VARCHAR(50)CHARACTER SET UTF8MB4 COLLATE UTF8MB4_UNICODE_CI NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_527EDB257E3C61F9` (`owner_id`),
    CONSTRAINT `FK_527EDB257E3C61F9` FOREIGN KEY (`owner_id`)
        REFERENCES `user` (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=549 DEFAULT CHARSET=UTF8MB4 COLLATE = UTF8MB4_UNICODE_CI;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
insert into `task` VALUES (
    512,
    'Clean up the desktop',
    '2019-06-11 00:00:00',
    'In progress',
    215,
    'recycle_5cf884b5e49f1.png'
),(
    513,
    'Check the calendar',
    '2019-06-07 05:12:53',
    'In progress',
    215,
    'calendar_5cf884b5f2afc.png'
),(
514,
    'Make personal calls',
    '2019-07-06 05:12:54',
    'Done',
    215,
    'phone_5cf884b5f3fac.png'
),(
    515,
    'Prioritize your tasks',
    '2019-06-01 05:12:54',
    'In progress',
    215,
    'barchart_5cf884b600733.png'
),(
    516,
    'Prioritize your emails',
    '2019-07-31 05:12:54',
    'In progress',
    215,
    'pin_5cf884b6010f1.png'
),(
    517,
    'Make a small donation',
    '2019-07-31 05:12:54',
    'In progress',
    215,
    'support_5cf884b6018aa.png'
),(
    518,
    'Do one or two small marketing actions',
    '2019-09-30 05:12:54',
    'In progress',
    215,
    'trends_5cf884b601cae.png'
    ),(
    519,
    'Send personal emails (when necessary)',
    '2019-06-05 12:00:00',
    'In progress',
    215,
    'email_5cf884b6032ba.png'
),(
    520,
    'Prepare goals for the next day',
    '2019-06-10 00:00:00',
    'Done',
    215,
    'check_5cf884b603742.png'
),(
    521,
    'Train my gut sense',
    '2019-06-10 00:00:00',
    'In progress',
    215,
    'bulb_5cf884b606cb9.png'
),(
    522,
    'Review all your ideas',
    '2019-06-07 00:00:00',
    'In progress',
    215,
    'lightbulb_5cf884b6073c0.png'
),(
    523,
    'Read the news',
    '2019-06-09 05:12:54',
    'Done',
    215,
    'news_5cf884b60a932.png'
),(
    524,
    'Make bed',
    '2019-06-06 00:12:54',
    'In progress',
    216,
    'bed_5cf884b60b0c1.png'
),(
    525,
    'Open the curtains and welcome the day',
    '2019-06-06 00:12:54',
    'In progress',
    216,
    'colorwheel_5cf884b60cefd.png'
),(
    526,
    'Take care of plants',
    '2019-06-06 08:12:54',
    'In progress',
    216,
    'flower_5cf884b60d5b7.png'
),(
    527,
    'Clean the room',
    '2019-06-05 05:12:54',
    'In progress',
    216,
    'room_5cf884b60db0a.png'
),(
    528,
    'Appreciate something in your home',
    '2019-05-30 05:12:54',
    'In progress',
    216,
    'home_5cf884b60e07f.png'
),(
    529,
    'Go shopping',
    '2019-06-06 06:12:54',
    'Done',
    216,
    'cart_5cf884b60e59a.png'
),(
    530,
    'Meditate',
    '2019-06-07 05:12:54',
    'In progress',
    216,
    'rainbow_5cf884b60eb9f.png'
),(
    531,
    'Do an act of kindness',
    '2019-06-09 05:12:54',
    'In progress',
    216,
    'heart_5cf884b60f3ff.png'
),(
    532,
    'Read good news',
    '2019-06-10 05:12:54',
    'In progress',
    216,
    'focus_5cf884b60fafa.png'
),(
    533,
    'Spend time on a hobby',
    '2019-06-06 00:12:54',
    'In progress',
    216,
    'brush-pencil_5cf884b61003f.png'
),(
    534,
    'Do creative work',
    '2019-06-14 05:12:54',
    'In progress',
    216,
    'art_5cf884b61046a.png'
),(
    535,
    'Keep a dream journal',
    '2019-06-20 05:12:54',
    'In progress',
    216,
    'magicwand_5cf884b610912.png'
),(
    536,
    'Review your previous days spending',
    '2019-06-06 05:12:54',
    'In progress',
    216,
    'clipboard_5cf884b610d0a.png'
),(
537,
    'Keep your finances on track',
    '2019-06-06 00:00:00',
    'In progress',
    216,
    'money_5cf884b611116.png'
),(
    538,
    'Read the book',
    '2019-06-06 03:12:54',
    'Done',
    217,
    'bookshelf_5cf884b6114d3.png'
),(
    539,
    'Take in a TED talk',
    '2019-06-06 10:12:54',
    'In progress',
    217,
    'video_5cf884b61191f.png'
),(
    540,
    'Listen to a podcast',
    '2019-05-30 05:12:54',
    'Done',
    217,
    'headphones_5cf884b611d5b.png'
),(
    541,
    'Read the article',
    '2019-06-06 10:12:54',
    'In progress',
    217,
    'browser_5cf884b6121a1.png'
),(
    542,
    'Write a blog post',
    '2019-06-09 05:12:54',
    'In progress',
    217,
    'pencil_5cf884b612642.png'
),(
    543,
    'Listen to music',
    '2019-06-09 05:12:54',
    'In progress',
    217,
    'radio_5cf884b612ab0.png'
),(
    544,
    'Write down 10 new ideas',
    '2019-06-13 05:12:54',
    'Done',
    217,
    'genius_5cf884b612f54.png'
),(
    545,
    'Get some exercises',
    '2020-01-01 00:00:00',
    'In progress',
    217,
    'star_5cf884b614c77.png'
),(
    546,
    'Win the million',
    '2019-07-27 00:00:00',
    'Done',
    217,
    'trophy_5cf884b615299.png'
),(
    547,
    'Take your dog for a walk',
    '2019-06-13 05:12:54',
    'Done',
    217,
    'medal_5cf884b6159ab.png'
),(
    548,
    'Write the article',
    '2019-09-06 05:12:54',
    'In progress',
    217,
    'compose_5cf884b615ee6.png'
);

/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_8d93d649e7927c74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
(
    214,
    'root@mail.ru',
    '[\"ROLE_ROOT\"]',
    '$argon2i$v=19$m=1024,t=2,p=2$RmpiTmpXZ0hTN1RDWGV1Zw$41wbq/fTcRDC4TTV03AXDr816N4FQNTl9WtgBZD/qZU',
    'root_5cf884b5545d7.png',
    'indigo lighten-2',
    'Root'
),(
215,
    'manager@mail.ru',
    '[]',
    '$argon2i$v=19$m=1024,t=2,p=2$ZndKSXd5ZEI1akJtalI2dg$HVzs1e+viALDqnkDi5x/sR1nxbfKwsEYY+JhT2IRCgw',
    'manager_5cf884b598d99.png',
    'black',
    'Manager'
),(
    216,
    'housewife@mail.ru',
    '[]',
    '$argon2i$v=19$m=1024,t=2,p=2$Y01ydUVhV2gzUlZxT3hZMA$jfBF90TKmw1r1O8rm6BZafJ+XlaCqbZHiWdmSgkM7Ok',
    'housewife_5cf884b5999fe.jpeg',
    'purple lighten-2',
    'Housewife'
),(
    217,
    'student@mail.ru',
    '[]',
    '$argon2i$v=19$m=1024,t=2,p=2$V0ZPYUNCbjUvcjYxTE5QQQ$PZ3+nL+Xrb5N/NkZ1ToT2DFFnL6JPNqBmWh/YQA7Hxw',
    'student_5cf884b59d785.jpeg',
    'purple lighten-2',
    'Student'
),(
    218,
    'new_user@mail.ru',
    '[]',
    '$argon2i$v=19$m=1024,t=2,p=2$UWdvZkpRR1dxeERQT01nSQ$vlWM7jzIgt8MKmko7FLGu1wrvgUR3U8m6PsvU6ZXxIM',
    'vektornyy-dom_5cf928c6d07ad.png',
    'red lighten-2',
    'Guest'
);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-11 15:09:33
