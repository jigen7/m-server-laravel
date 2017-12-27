<?php

class RequestUtils {

	public static function get($url, $params) {
		return RequestUtils::createRequest($url, "GET", $params, null);
	}
	
	public static function post($url, $params, $attachments) {
		return RequestUtils::createRequest($url, "POST", $params, $attachments);
	}
	
	public static function delete($url, $params) {
		return RequestUtils::createRequest($url, "DELETE", $params, null);
	}
	
	public static function put($url, $params) {
		return RequestUtils::createRequest($url, "PUT", $params, null);
	}
	
	private static function createRequest($url, $method, $params, $attachments) {
		$requestUrl = $url;
		if($params != null && strcmp($method, "POST") != 0) {
			$requestUrl = $requestUrl . '?' . http_build_query($params);
		}
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
	    curl_setopt( $ch, CURLOPT_URL, $requestUrl);
	    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	    curl_setopt( $ch, CURLOPT_ENCODING, "" );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
	    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
	    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method);
	
		if(strcmp($method, "POST") == 0 || strcmp($method, "PUT") == 0) {
			$files = array();
			if($params != null) {
				$jsonString = json_encode($params);
				if(!isset($attachments)) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
					    'Content-Type: application/json',                                                                                
					    'Content-Length: ' . strlen($jsonString))                                                                       
					);
				}else{
					$path = dirname(__FILE__) . '/request.json';
                    //$path = '/Applications/XAMPP/xamppfiles/htdocs/homestead/masarap-server-laravel/public/tests/core/request.json';
                    //$path = 'request.json';
					$fp = fopen($path, 'w+');
					fwrite($fp, $jsonString);
					fclose($fp);
					$files['json'] = "@$path";
				}
			}
			
			if(isset($attachments) && count($attachments) > 0) {
				for($i=0; $i<count($attachments); $i++) {
					$files['fileField['.$i.']'] = $attachments[$i];	
				}
			}
			
			if(count($files) > 0 || count(array_keys($files)) > 0) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $files);
			}
		}
		
	    $content = curl_exec( $ch );
	    $response = curl_getinfo( $ch );
	    curl_close ( $ch );
	
		return array('content'=>$content, 'response'=>$response);
	}
}

?>