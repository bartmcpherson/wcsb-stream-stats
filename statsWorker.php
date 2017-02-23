<?php

class statsWorker {

	public $listeners = array();

	public function __construct($server, $port, $extra, $user, $password) {

		/*error_log("server: ".$server, 0);
		error_log("port: ".$port, 0);
		error_log("extra: ".$extra, 0);
		error_log("user: ".$user, 0);
		error_log("pass: ".$password, 0);*/

		/* Start cURL */
		$session = curl_init();
		curl_setopt($session, CURLOPT_URL, $server.":".$port.$extra);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_POST, false);
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_USERPWD, $user.":".$password);
		curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($session, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$listnerxml = curl_exec($session);
		//error_log($xml, 0);
		curl_close($session);
		/* End cURL */

		/* Start Simple XML */
		$simpleListnerxml = simplexml_load_string($listnerxml);
		foreach ($simpleListnerxml->LISTENERS->LISTENER as $host) {
			array_push($this->listeners, $host->HOSTNAME);
		}

	}

}

?>