<?php

class ipWorker {

	public $ipInfo;
	public $listenerjson;

	public function __construct($server, $extra) {
		/* Start cURL */
		$ipsession = curl_init();
		curl_setopt($ipsession, CURLOPT_URL, $server.$extra);
		curl_setopt($ipsession, CURLOPT_HEADER, false);
		curl_setopt($ipsession, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ipsession, CURLOPT_POST, false);
		curl_setopt($ipsession, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ipsession, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$this->listenerjson = curl_exec($ipsession);
		curl_close($ipsession);
		/* End cURL */

		$this->ipInfo = json_decode($this->listenerjson, true);
	}

}

?>