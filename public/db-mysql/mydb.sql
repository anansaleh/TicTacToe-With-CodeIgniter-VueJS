-- GameTicTacToe
--
-- Table structure for table `gameType`
--

DROP TABLE IF EXISTS `gameType`;
CREATE TABLE `gameType` (
  `gameType_id` int(11) NOT NULL AUTO_INCREMENT,
  `gameType_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`gameType_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `gameType` VALUES (1,'Singleplayer'),(2,'Multiplayer');

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `player_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) COLLATE utf8_unicode_ci  NOT NULL,
  `player_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`player_id`),
  UNIQUE (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `players` VALUES (1,'pc@local','PC');

--
-- Table structure for table `games`
--
DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `gameType_id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `winner_id` int(11) NOT NULL  DEFAULT 0,
  `status` tinyint(1) NOT NULL   DEFAULT 0,
  `level` tinyint(1) NULL   DEFAULT 0,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_ended` datetime DEFAULT NULL,
  PRIMARY KEY (`game_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `State`
--

DROP TABLE IF EXISTS `state`;

CREATE TABLE `state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `board` json NOT NULL,
  `player_turn` int(11) NOT NULL,
  `turn`  varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `winn_line` json  NULL,
  `oMovesCount` int(11)  DEFAULT 0,
  `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_modified` DATETIME  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


