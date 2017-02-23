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
print"<div id=\"blu\">\n  <div style=\"text-align: center;
\">\n    <h1>There are $total_listeners listeners</h1>\n  </div>\n</div>\n<div>\n  <div>\n    <p><b>Current song:</b> $song[1]</p>\n  </div>\n</div>\n<div>\n";

$i = "1";
while ($i <= $servers) {
	print"  <div>\n";
	if ($max[$i] > 0) {
		$percentage = round(($listeners[$i]/$max[$i]*100));
		$timesby    = (300/$max[$i]);
		$barlength  = round(($listeners[$i]*"$timesby"));
	}
	if ($error[$i] != "1") {
		?>
												<table width="600"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td width="25%" align="center"><b><?php print$streamname[$i];
		?></b>&nbsp;
											&nbsp;
											</td>
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
												<table width="600"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td width="25%" align="center"><b><?php print$streamname[$i];
		?></b>&nbsp;
											</td>
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
	print"<p><b>Status:</b> $msg[$i]</p>\n  </div>\n  <div class=\"line\"> </div>\n";
	$i++;
}
print"</div>\n";
for ($j = 1; $j < $i; $j++) {
	$radioStats = new statsWorker($ip[$j], $port[$j], $user_page, $adminuser, $adminpwd);
	print("<div>\n");
	print("<table>\n");
	print("<tr><td colspan=\"2\"><b>$streamname[$j] Listner Details</b></td></tr>\n");
	print("<tr><td>IP Address</td><td>Country</td></tr>\n");
	$k = 1;
	foreach ($radioStats->listeners as $value) {
		print("<tr><td>$value</td><td>$k</td></tr>\n");
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
print"<div>\n  <div>\n    <p><b>Live SHOUTcast statistics:</b> $date, $time</p>\n  </div>\n</div>\n";
?>
</body>
</html>
