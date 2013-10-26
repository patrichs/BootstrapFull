This project is intended to deliver full back-end, ajax and database support to VinceG's Bootstrap Theme.

Installation:

1. Download this project
2. Extract to a folder within your www
3. Create a new database and run the SQL commands at the bottom of the page
4. Navigate to /admin/php/configs/dbconfig.php and set your database info
5. Navigate to /admin/register.html and register an account
6. Navigate to /admin/login.html to login
7. All done.

--- Run these queries on your database ---

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_swedish_ci NOT NULL,
  `location` varchar(256) COLLATE utf8_swedish_ci NOT NULL,
  `uploaded` datetime NOT NULL,
  `type` varchar(16) COLLATE utf8_swedish_ci NOT NULL,
  `uploader` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `groups` (
  `groupid` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`groupid`),
  UNIQUE KEY `unique_groupid` (`groupid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `logs` (
  `logsid` int(11) NOT NULL AUTO_INCREMENT,
  `eventid` int(11) NOT NULL,
  `eventtitle` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
  `eventdesc` varchar(1024) COLLATE utf8_swedish_ci NOT NULL,
  `eventdate` datetime NOT NULL,
  PRIMARY KEY (`logsid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `hash` varchar(512) COLLATE utf8_swedish_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_swedish_ci NOT NULL,
  `groupid` int(11) NOT NULL,
  `dateregistered` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;