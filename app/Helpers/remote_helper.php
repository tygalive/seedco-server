<?php

/**
 * Remote Helper
 */

use CodeIgniter\Events\Events;
use Config\Services;
use \CodeIgniter\HTTP\Response;

/**
 * Fetch and push remote content
 *
 * @param string  $url     Url.
 * @param boolean $encrypt Encrypt.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|void
 */

 function remote_get_content(string $url, bool $encrypt = true)
 {
	$username = getenv('remote.email');
    $password = getenv('remote.password');
    $key      = base64_encode($username . ':' . $password);

    $client = Services::curlrequest();
    $client->setAuth($username, $password);
    $client->setHeader('User-Agent', 'Seedco Server');

    // Create a cURL handle
    $handle = curl_init();

    // Set the URL for the cURL handle
    curl_setopt($handle, CURLOPT_URL, $url);

    // Set the authentication credentials for the cURL handle
    curl_setopt($handle, CURLOPT_USERNAME, $username);
    curl_setopt($handle, CURLOPT_PASSWORD, $password);

    // Set the custom User-Agent header for the cURL handle
    curl_setopt($handle, CURLOPT_USERAGENT, 'Seedco Server');

    // Disable certificate verification for the cURL handle
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
 
	 $message         = [];
	 $message['data'] = encrypt_content($key, message_get_all(), $encrypt);
 
	 $message['encrypt'] = encrypt_supported();
 
	 try
	 {
		 $url = remote_trim_url($url) . '/wp-json/seedco-sage/v1/content';
 
		 /**
		  * Http response
		  *
		  * @var Response $response
		  */
		 $response = $client->post($url, [
			 'form_params' => $message,
		 ]);
 
	 }
	 catch (Exception $e)
	 {
		 // Print the exception message
		 echo "Exception: " . $e->getMessage();
		 if ($encrypt)
		 {
			 #try with out
 
			 return remote_get_content($url, false);
		 }
		 $response = false;
	 }
 
	 if ($response &&  $response->getStatusCode() == 200)
	 {
		 $response = decrypt_content($key, $response->getBody());
 
		 if (is_array($response))
		 {
			 foreach ($response as $action => $data)
			 {
				 Events::trigger('sync_' . $action, $data);
			 }
		 }
	   
	 }
	 var_dump($response);
 
 }
 

/**
 * Remove trailing slashes from a url
 *
 * @param string $url Url.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return string
 */
function remote_trim_url(string $url):string
{
	return rtrim($url, '\\/');
}
