<?php
/* ----------- Server configuration ---------- */

// Note: dont include http://
// Main server: The song title will be taken from this server

$ip[1]         = "server IP address";
$port[1]       = "port number";
$streamname[1] = "stream display name";

/* Relays: Below you can enter more relays / restreams / channels / competitors or anything else */
// increment the index value for each additional entry
//$ip[2]         = "server IP address";
//$port[2]       = "port number";
//$streamname[2] = "stream display name";

/* ---------- General configuration ---------- */
$refresh      = "60";// Page refresh time in seconds. Put 0 for no refresh
$timeout      = "1";// Number of seconds before connecton times out - a higher value will slow the page down if any servers are offline
$station_name = "You Station Name";

$adminuser       = "user name";
$adminpwd        = 'user password';
$user_page       = '/admin.cgi?sid=1&mode=viewxml&page=3';
$song_list_uri   = '/admin.cgi?sid=1&mode=viewxml&page=4';
$update_song_uri = '/admin.cgi?sid=1&mode=updinfo&song=';

/* ---------- MySQL configuration ---------- */
$mysqlinfo = array('dbhost' => 'localhost', 
				'dbname' => 'ip2loc',
				'dbuser' => '',
				'dbuserpassw' => '',
				'iptablename' => 'lookup',
			);
?>