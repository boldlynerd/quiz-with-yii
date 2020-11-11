CREATE DATABASE `yii2quiz`;

CREATE TABLE `quiz` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `question` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quizId` int unsigned DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `quizId_idx` (`quizId`),
  CONSTRAINT `quizId` FOREIGN KEY (`quizId`) REFERENCES `quiz` (`id`)
);

CREATE TABLE `answer` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `questionId` int unsigned DEFAULT NULL,
  `text` text,
  `correct` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `question_idx` (`questionId`),
  CONSTRAINT `question` FOREIGN KEY (`questionId`) REFERENCES `question` (`id`) ON DELETE CASCADE
);

CREATE TABLE `score` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quizId` int unsigned DEFAULT NULL,
  `score` int unsigned DEFAULT NULL,
  `sessionId` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `dateFinished` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sessionId_UNIQUE` (`sessionId`),
  KEY `quizId_idx` (`quizId`),
  CONSTRAINT `quizToScore` FOREIGN KEY (`quizId`) REFERENCES `quiz` (`id`)
);

INSERT INTO `quiz`
(`id`, `name`)
VALUES
(1, 'Default Quiz');