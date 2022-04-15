<?php

/**
 * Fetch and push remote content
 *
 * @version 1.0.0
 * @since 1.0.0
 * @param string $url
 * @param bool $encrpt
 * @return void|array|string
 */
function remoteGetContent(string $url, bool  $encrypt = true)
{
    $username = getenv("remote.email");
    $password = getenv("remote.password");
    $key = base64_encode($username . ":" . $password);

    $client = \Config\Services::curlrequest();
    $client->setAuth($username, $password);
    $client->setHeader("User-Agent", "Seedco Server");

    $message = array();
    $message["data"] = encryptContent($key, messageGetAll(), $encrypt);

    $message["encrypt"] =  encryptSupported();

    try {
        $url =  remoteTrimUrl($url) . "/wp-json/seedco-sage/v1/content";

        /**
         * @var \CodeIgniter\HTTP\Response $response
         */
        $response  = $client->post($url, array(
            "form_params" => $message
        ));
    } catch (\Exception $e) {
        if ($encrypt) {
            #try with out

            return remoteGetContent($url, false);
        }

        $response = false;
    } finally {
        if ($response &&  $response->getStatusCode() == 200) {
            $response = decryptContent($key, $response->getBody());

            if (is_array($response)) {
                foreach ($response as $action => $data) {
                    \CodeIgniter\Events\Events::trigger("sync_".$action, $data);
                }
            }
        }
    }
}

/**
 * Remove trainling slashes from a url
 *
 * @param string $url
 * @return string
 */
function remoteTrimUrl($url)
{
    return rtrim($url, "\\/");
}