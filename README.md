# wcsb-stream-stats
PHP code for displaying current ShoutCast listeners.

__Intro__

Code for viewing user counts connected to ShoutCast server.

__Setup__
Create a database to hold data from ip2location.com

Create a table to hold the data.
CREATE TABLE `lookup` (`ip_from` INT(10) UNSIGNED ZEROFILL NOT NULL,`ip_to` INT(10) UNSIGNED ZEROFILL NOT NULL,`country_code` CHAR(2) NOT NULL,`country_name` VARCHAR(64) NOT NULL,`region_name` VARCHAR(128) NOT NULL,`city_name` VARCHAR(128) NOT NULL,INDEX `idx_ip_from` (`ip_from`),INDEX `idx_ip_to` (`ip_to`));

Load the data into the table.
LOAD DATA LOCAL INFILE 'IP2LOCATION-LITE-DB3.CSV' INTO TABLE lookup FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n';

Copy default.settings.php to settings.php and fillout the variable values for your site.
