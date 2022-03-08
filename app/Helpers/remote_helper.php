<?php

/**
 * Fetch and push remote content
 *
 * @version 1.0.0
 * @since 1.0.0
 * @param string $url
 * @param array|string $data
 * @param string $action
 * @param callable $callback
 * @param bool $encrpt
 * @return void|array|string
 */
function remoteGetContent(string $url,  $callback = "boolval", $data = array(), string $action = "environment", bool  $encrypt = true)
{

    $username = getenv("remote.email");
    $password = getenv("remote.password");
    $key = base64_encode($username . ":" . $password);

    $client = \Config\Services::curlrequest();
    $client->setAuth($username, $password);
    $client->setHeader("User-Agent", "Seedco Server");

    $message = array();
    $message["data"] = encryptContent($key, array(
        $action => $data ?: 1 #force field to be sent
    ), $encrypt);

    $message["encrypt"] =  function_exists("openssl_encrypt") && function_exists("openssl_decrypt");

    try {
        $url =  rtrim($url, "\\/") . "/wp-json/seedco-sage/v1/content";

        $response  = $client->post($url, array(
            "form_params" => $message
        ));
    } catch (\Exception $e) {
        $response = false;
    } finally {

        if ($response &&  $response->getStatusCode() == 200) {
            $response = decryptContent($key, $response->getBody());
        }

        if ($response) {
            call_user_func($callback, $response);
        }
    }
}

/**
 * Encrypt given content using key
 *
 * @version 1.0.0
 * @since 1.0.0
 * @param string $key
 * @param string $content
 * @param boolean $encrypt
 * @return string
 */
function encryptContent(string $key, $content = array(), bool $encrypt = true)
{

    if ($encrypt && function_exists("openssl_encrypt") && function_exists("openssl_decrypt")) {
        if (is_array($content)) {
            $content = json_encode($content);
        }

        $key = md5($key);

        $iv = getEncryptionIV($key);

        #encrypt data
        $content =  openssl_encrypt($content, "aes-256-cbc", $key, 0, $iv);
    }

    return json_encode($content);
}

/**
 * Decrypt given content using given key
 *
 * @version 1.0.0
 * @since 1.0.0
 * @param String $key
 * @param string $content
 * @return array|string
 */
function decryptContent(String $key,  $content = "")
{
    if (function_exists("openssl_encrypt") && function_exists("openssl_decrypt")) {
        $content = json_decode($content, true);

        #if json then data is not encrypted
        if (!preg_match("/^[{\[].*[}\]]$/", $content["data"])) {
            $key = md5($key);

            $iv = getEncryptionIV($key);

            #decrypt data
            $content =  openssl_decrypt($content["data"], "aes-256-cbc", $key, 0, $iv);
            $content = json_decode($content, true);
        }
    }

    return $content;
}

/**
 * Get the initialisation vector from key
 *
 * @version 1.0.0
 * @since 1.0.0
 * @param string $key
 * @return string
 */
function getEncryptionIV(string $key): string
{
    return substr($key, 0, getenv("encryption.blockSize"));
}