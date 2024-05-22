CREATE TABLE IF NOT EXISTS `Operation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL DEFAULT '0',
  `cost` int NOT NULL DEFAULT (0),
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
);

INSERT INTO `Operation` (`id`, `type`, `cost`) VALUES
	(1, 'Addition', 20),
	(2, 'Subtraction', 20),
	(3, 'Multiplication', 30),
	(4, 'Division', 20),
	(5, 'Square Root', 20),
	(6, 'Random String', 50);

CREATE TABLE IF NOT EXISTS `Record` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `user_balance` int DEFAULT NULL,
  `operation_response` json DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `operation_id` (`operation_id`)
);

INSERT INTO `Record` (`id`, `operation_id`, `user_id`, `amount`, `user_balance`, `operation_response`, `date`) VALUES
	(29, 5, 4, 100, 100, '{}', '2024-05-20 12:15:13'),
	(33, 1, 4, 100, 100, '{}', '2024-05-20 12:34:02'),
	(38, 1, 4, -20, 80, '{}', '2024-05-20 12:34:02'),
	(39, 1, 4, 50, 130, '{}', '2024-05-20 12:56:46'),
	(40, 5, 4, 60, 160, '{}', '2024-05-20 13:49:51'),
	(41, 1, 1, 100, 100, '{}', '2024-05-20 14:59:17'),
	(42, 2, 1, 100, 100, '{}', '2024-05-20 14:59:25'),
	(43, 1, 4, 40, 170, '{}', '2024-05-20 15:00:12'),
	(44, 1, 1, -20, 80, '{"data": 105}', '2024-05-21 00:10:27'),
	(45, 1, 1, -20, 60, '{"data": 105}', '2024-05-21 00:11:26'),
	(52, 1, 1, -20, 40, '{"data": 105}', '2024-05-21 00:22:18'),
	(61, 1, 1, -20, 20, '{"data": false}', '2024-05-21 05:37:04'),
	(65, 1, NULL, 100, 100, '{}', '2024-05-21 08:47:03'),
	(66, 1, NULL, 100, 100, '{}', '2024-05-21 08:47:22'),
	(67, 1, 1, 100, 120, '{}', '2024-05-21 08:49:00'),
	(68, 1, 1, -20, 100, '{"data": 105}', '2024-05-21 18:37:59'),
	(69, 1, 1, 200, 300, '{}', '2024-05-21 18:44:56');

CREATE TABLE IF NOT EXISTS `Users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `password` (`password`)
);

INSERT INTO `Users` (`id`, `username`, `password`, `status`) VALUES
	(1, 'admin', 'admin', 1),
	(2, 'test', 'test', 0),
	(3, 'carlos', 'carlos', 0),
	(4, 'pepe', 'pepe', 1),
	(5, 'pablo', 'pablo', 0),
	(6, 'pam', 'pam', 0),
	(7, 'cus', 'cus', 0),
	(8, 'pame', 'pame', 0),
	(9, 'papax', 'papax', 0),
	(10, 'toto', 'toto', 0),
	(11, 'cc', 'cc', 0),
	(12, 'cax', 'cax', 1);
