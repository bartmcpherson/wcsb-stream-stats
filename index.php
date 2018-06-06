<?php
/*

Live SHOUTcast statistics for multiple servers

This script is (C) Bell Online Ltd 2012

If you use this script, please leave the copyright
notice and link at the bottom of the page or link
to mixstream.net somewhere on your website. Feel
free to modify it in any other way to suit your needs.

Version: v1.1

## Updates:
## 1.1, Added fclose()

 */
require_once "settings.php";
require_once "statsWorker.php";
require_once "ipWorker.php";

function secondsToWords($seconds) {
	$ret = "";

	/*** get the days ***/
	$days = intval(intval($seconds)/(3600*24));
	if ($days > 0) {
		if ($days > 1) {
			$ret .= "$days days ";
		} else {
			$ret .= "$days day ";
		}
	}

	/*** get the hours ***/
	$hours = (intval($seconds)/3600)%24;
	if ($hours > 0) {
		if ($hours > 1) {
			$ret .= "$hours hours ";
		} else {
			$ret .= "$hours hour ";
		}
	}

	/*** get the minutes ***/
	$minutes = (intval($seconds)/60)%60;
	if ($minutes > 0) {
		if ($minutes > 1) {
			$ret .= "$minutes minutes ";
		} else {
			$ret .= "$minutes minute ";
		}
	}

	/*** get the seconds ***/
	$seconds = intval($seconds)%60;
	if ($seconds > 1) {
		$ret .= "$seconds seconds";
	}

	return $ret;
}
$error = array("", "", "");
$msg   = array("", "", "");

$servers = count($ip);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
if ($refresh != "0") {
	print"<meta http-equiv=\"refresh\" content=\"$refresh\">\n";
}
print"<title>$station_name SHOUTcast Stats</title>\n";
?>
<link rel="stylesheet" type="text/css" href="stats.css">

</head>
<body>
<?php
$i               = "1";
$total_listeners = 0;
while ($i <= $servers) {
	$fp = @fsockopen($ip[$i], $port[$i], $errno, $errstr, $timeout);
	if (!$fp) {
		$listeners[$i] = "0";
		$msg[$i]       = "<span class=\"red\">ERROR [Connection refused / Server down]</span>";
		$error[$i]     = "1";
	} else {
		fputs($fp, "GET /7.html HTTP/1.0\r\nUser-Agent: Mozilla\r\n\r\n");
		while (!feof($fp)) {
			$info = fgets($fp);
		}
		$info  = str_replace('<HTML><meta http-equiv="Pragma" content="no-cache"></head><body>', "", $info);
		$info  = str_replace('</body></html>', "", $info);
		$stats = explode(',', $info);
		if (empty($stats[1])) {
			$listeners[$i] = "0";
			$msg[$i]       = "<span class=\"red\">ERROR [There is no source connected]</span>";
			$error[$i]     = "1";
		} else {
			if ($stats[1] == "1") {
				$song[$i] = $stats[6];
				$total_listeners += trim($stats[0], "<html><body>");
				$stats[0]      = trim($stats[0], "<html><body>");
				$listeners[$i] = $stats[0];
				$max[$i]       = $stats[3];
				$bitrate[$i]   = $stats[5];
				$peak[$i]      = $stats[2];
				if ($stats[0] == $max[$i]) {
					$msg[$i] .= "<span class=\"red\">";
				}
				$msg[$i] .= "Server is up at $bitrate[$i] kbps with $listeners[$i] of $max[$i] listeners";

				if ($stats[0] == $max[$i]) {
					$msg[$i] .= "</span>";
				}
				$msg[$i] .= "\n    <p><b>Listener peak:</b> $peak[$i]";
			} else {
				$listeners[$i] = "0";
				$msg[$i]       = "    <span class=\"red\">ERROR [Cannot get info from server]</span>";
				$error[$i]     = "1";
			}
		}
	}
	fclose($fp);
	$i++;
}
print"<div id=\"listenercountwrapper\"><div id=\"listeners\" style=\"text-align: center;\">\n";
print"<h1>There are $total_listeners listeners</h1></div></div>\n";
print"<div id=\"songwrapper\"><div id=\"song\"><p><b>Current song:</b> $song[1]</p></div></div>\n";
print"<div id=\"streamoverviewwrapper\">\n";

$i = "1";
while ($i <= $servers) {
	print"  <div id=\"streamoverview\">\n";
	if ($max[$i] > 0) {
		$percentage = round(($listeners[$i]/$max[$i]*100));
		$timesby    = (450/$max[$i]);
		$barlength  = round(($listeners[$i]*"$timesby"));
	}
	if ($error[$i] != "1") {
		?>
				<table width="90%"  border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="25%" align="center"><b><?php print$streamname[$i];?></b></td>
						<td width="75%" colspan="3" bgcolor="#eeeeee"><img src="<?php if ($percentage == "100") {print"red-";}?>bar.gif" width="<?php print$barlength?>" height="12" alt="The server is at <?php print$percentage;?>% capacity"></td>
					</tr>
					<tr>
						<td width="25%">&nbsp;</td>
						<td width="25%">0%</td>
						<td width="25%" align="center">50%</td>
						<td width="25%" align="right">100%</td>
					</tr>
				</table>
		<?php } else {
		?>
				<table width="90%"  border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="25%" align="center"><b><?php print$streamname[$i];?></b></td>
						<td width="75%" colspan="3" bgcolor="#eeeeee">&nbsp;</td>
					</tr>
					<tr>
						<td width="25%">&nbsp;</td>
						<td width="25%">0%</td>
						<td width="25%" align="center">50%</td>
						<td width="25%" align="right">100%</td>
					</tr>
				</table>
		<?php }
	print"<p><b>Status:</b> $msg[$i]</p>\n  </div>\n";

	if($i < $servers) {
		print"<div class=\"line\"> </div>\n";
	}
	$i++;
}
print"</div>\n";
for ($j = 1; $j < $i; $j++) {
	$radioStats = new statsWorker($ip[$j], $port[$j], $user_page, $adminuser, $adminpwd);
	print("<div id=\"streamdetails\">\n");
	print("<table width=\"100%\">\n");
	print("<tr class=\"tablehead\"><td colspan=\"5\" align=\"center\">$streamname[$j] Listener Details</td></tr>\n");
	print("<tr class=\"tablehead\"><td>IP Address</td><td>Country</td><td>Region</td><td>City</td><td>Connection Time</td></tr>\n");
	$k = 1;
	foreach ($radioStats->listeners as $value) {
		$ipDetails = new ipWorker("freegeoip.net", "/json/$value[hostname]");
		print("<tr class=\"".$ipDetails->ipInfo['country_code']."\">");
    print("<td><a href=\"https://www.google.com/maps/place/".$ipDetails->ipInfo['latitude'].",".$ipDetails->ipInfo['longitude']."/@".$ipDetails->ipInfo['latitude'].",".$ipDetails->ipInfo['longitude'].",z7\" target=\"_blank\">$value[hostname]</a></td>");
    print("<td>" .$ipDetails->ipInfo['country_name']."</td>");
    print("<td>".$ipDetails->ipInfo['region_name']."</td>");
    print("<td>".$ipDetails->ipInfo['city']."</td>");
    print("<td>".secondsToWords($value['connecttime']+0)."</td></tr>\n");
		$k++;
	}
	print("</table>\n");
	print("</div>\n");
}

$time_difference = "0";// BST: 1 GMT: 0
$time_difference = ($time_difference*60*60);
$time            = date("h:ia", time()+$time_difference);
$date            = date("jS F, Y", time()+0);
?>

<?php
print"<div id=\"footer\">\n<div>\n<p><b>SHOUTcast statistics updated:</b> $date, $time</p>\n<p>Latitude and longitude are estimates. Don't be a kreeper.</p>\n</div>\n</div>\n";
?>
</body>
</html>
