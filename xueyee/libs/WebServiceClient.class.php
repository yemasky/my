<?php

/**
 * WebServiceClient 2015年11月28日
 * @author YEMASKY  yemasky@msn.com
 * Copyright 2015
 */
class WebServiceClient{
	private $method = '';
	private $request_url = '';
	private $arrayHeader = array ();
	private $request_data = "";
	private $ssl = false;
	private $time_limit = 0;
	private $execute_timeout = 172800;
	private $gzip = false;

	public function __call($name, $args){
		$objCallName = new $name($args);
		$objCallName->setCallObj($this, $args);
		return $objCallName;
	}
	public function timelimit($time) {
		$this->time_limit = $time;
		return $this;
	}
	public function gzip() {
		$this->gzip = true;
		return $this;
	}
	public function timeout($time) {
		$this->execute_timeout = $time;
		return $this;
	}
	public function ssl(){
		$this->ssl = true;
		return $this;
	}
	public function put(){
		$this->method = "PUT";
		return $this;
	}

	public function get(){
		$this->method = "GET";
		return $this;
	}

	public function post($requestData){
		$this->request_data = $requestData;
		$this->method = "POST";
		return $this;
	}

	public function getMethod(){
		return $this->method;
	}

	public function header($arrayHeader){
		$this->arrayHeader = $arrayHeader;
		return $this;
	}

	public function url($url){
		$this->request_url = $url;
		return $this;
	}

	public function execute_file_get_contents(){
		$header = $this->method . ": HTTP/1.1\r\n";
		if(!empty($this->arrayHeader)) {
			foreach($this->arrayHeader as $k => $v) {
				$header .= $k . ": " . $v . "\r\n";
			}
		}
		$opts = array (
				'http' => array (
						'method' => $this->method,
						'timeout' => $this->execute_timeout,
						'header' => $header 
				) 
		);
		if($this->request_data != "") {
			$opts[0]["http"]['content'] = $this->request_data;
		}
		$context = stream_context_create($opts);
		$return['result'] = file_get_contents($this->request_url, false, $context);
		$return['header'] = $http_response_header;
		return $return;
	}

	public function execute_cUrl(){
		//if($this->time_limit != '') {
			set_time_limit(0);
		//}
		$process = curl_init();
		curl_setopt($process, CURLOPT_URL, $this->request_url);
		
		curl_setopt($process, CURLOPT_HEADER, TRUE);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($process, CURLOPT_TIMEOUT, $this->execute_timeout);
		curl_setopt($process, CURLOPT_DNS_CACHE_TIMEOUT, 172800);
		if($this->ssl == false) {
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($process, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		if($this->method == "POST") {
			curl_setopt($process, CURLOPT_POST, true);
			curl_setopt($process, CURLOPT_POSTFIELDS, $this->request_data);
		}
		$arrayHeader = array();
		if(!empty($this->arrayHeader)) {
			foreach($this->arrayHeader as $k => $v) {
				$arrayHeader[] = $k . ": " . $v;
			}
		}
		if($this->gzip) {
			$arrayHeader[] = 'Accept-Encoding: gzip';
			curl_setopt($process, CURLOPT_ENCODING, "gzip");
		}
		if(!empty($arrayHeader)) curl_setopt($process, CURLOPT_HTTPHEADER, $arrayHeader);
		
		$DataBemyssguest = curl_exec($process);
		$curl_getinfo = curl_getinfo($process);
		$httpcode = curl_getinfo($process, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($process, CURLINFO_HEADER_SIZE);
		$error = curl_error($process);
		
		$header_string = substr($DataBemyssguest, 0, $header_size);
		$body = substr($DataBemyssguest, $header_size);
		
		curl_close($process);
		
		$return['result'] = $body;
		$return['header'] = $header_string;
		$return['httpcode'] = $httpcode;
		$return['error'] = $error;
		$return['curl_getinfo'] = $curl_getinfo;
		$return['ioy_cooc_cache'] = $httpcode == 200 ? true : false;
		
		return $return;
	}
}