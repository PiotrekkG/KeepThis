CREATE TABLE `users` (
	`userId` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`username` VARCHAR(64) NOT NULL,
	`password` VARCHAR(80) NOT NULL,
	`email` VARCHAR(100),
	`activatedEmail` BOOLEAN,
	`premiumActiveTill` DATE,
	`joinTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`lang` ENUM('en','fr','de','es','pl'),
	`deleted` BOOLEAN NOT NULL DEFAULT 0
) ENGINE = InnoDB;

CREATE TABLE `bookmarkKeys` (
	`bookmarkId` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`bookmarkKey` VARCHAR(34) NOT NULL,
	`bookmarkKeyGeneratedTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`refUserId` INT UNSIGNED REFERENCES `users`(`userId`),
	`showForm` BOOLEAN NOT NULL DEFAULT 1,
	`allowUnlogged` BOOLEAN NOT NULL DEFAULT 1,
	`actionAfterAdd` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
	`templateId` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
	`templateText` VARCHAR(64) DEFAULT NULL,
	`lastUsedDate` DATE,
	`lastUsedDateCounter` SMALLINT UNSIGNED DEFAULT 0,
	`totalUsedCounter` INT UNSIGNED DEFAULT 0,
	`active` BOOLEAN NOT NULL DEFAULT 1,
	`createdTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`deletedTime` BOOLEAN DEFAULT NULL
) ENGINE = InnoDB;

CREATE TABLE `linkList` (
	`posId` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`refUserId` INT UNSIGNED REFERENCES `users`(`userId`),
	`refBookmarkId` VARCHAR(34) REFERENCES `bookmarkKeys`(`bookmarkId`),
	`url` VARCHAR(1024) NOT NULL,
	`pageTitle` VARCHAR(1024) NOT NULL,
	`description` VARCHAR(1024),
	`rate` TINYINT(1) UNSIGNED,
	`favorite` BOOLEAN,
	`clickCounterId` SMALLINT UNSIGNED DEFAULT 0,
	`clickCounterKey` SMALLINT UNSIGNED DEFAULT 0,
	`linkKey` VARCHAR(34),
	`addTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;