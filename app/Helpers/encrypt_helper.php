<?php

/**
 * Checks if ecryption is supported
 *
 * @return bool
 */
function encryptSupported()
{
    return function_exists("openssl_encrypt") && function_exists("openssl_decrypt");
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
    if ($encrypt && encryptSupported()) {
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
function decryptContent(String $key, $content = "")
{
    if (encryptSupported()) {
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