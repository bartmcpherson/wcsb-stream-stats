<?php

class ipDBLookup {

	public $ipInfo;
	public $listenerjson;

	public function __construct($dotip, $mysqlinfo) {
		$currentresult = "";
		$longip = $this->Dot2LongIP($dotip);
		
		// Connect to the database server
    	$link = mysqli_connect($mysqlinfo["dbhost"], $mysqlinfo["dbuser"], $mysqlinfo["dbuserpassw"]) or die("Could not connect to MySQL database");
 
	    // Connect to the IP2Location database
	    mysqli_select_db($link, $mysqlinfo["dbname"]) or die("Could not select database");

    	$query = "SELECT * FROM " . $mysqlinfo['iptablename'] . " WHERE ip_to >=" . $longip . " order by ip_to limit 1";
    	error_log("query is: $query");
    	if(!$query) {
			echo "Error";
		} else {
			$result = mysqli_query($link,$query) or trigger_error("Query Failed! SQL: $sql - Error: ".mysqli_error($link), E_USER_ERROR);
			//die("IP2Location Query Failed");
			$this->ipInfo = $result->fetch_assoc();
		}
		// Free recordset and close database connection
        mysqli_free_result($result); 
        mysqli_close($link);
	}

	// Function to convert IP address (xxx.xxx.xxx.xxx) to IP number (0 to 256^4-1)
    private function Dot2LongIP ($IPaddr) {
    	if ($IPaddr == "") {
        	return 0;
        } else {
        	$ip = explode(".", $IPaddr);
        	return ($ip[3] + $ip[2] * 256 + $ip[1] * 256 * 256 + $ip[0] * 256 * 256 * 256);
        }
    }
}

?>