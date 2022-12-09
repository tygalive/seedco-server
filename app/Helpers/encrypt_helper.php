<?php

/**
 * Encryption Helper
 */

/**
 * Checks if encryption is supported
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean
 */
function encrypt_supported():bool
{
	return function_exists('openssl_encrypt') && function_exists('openssl_decrypt');
}

/**
 * Encrypt given content using key
 *
 * @param string       $key     Key.
 * @param array|string $content Content
 * @param boolean      $encrypt Encrypt.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return string
 */
function encrypt_content(string $key, $content = [], bool $encrypt = true):string
{
	if ($encrypt && encrypt_supported())
	{
		if (is_array($content))
		{
			$content = json_encode($content);
		}

		$key = md5($key);

		$iv = encryption_iv($key);

		#encrypt data
		$content = openssl_encrypt($content, 'aes-256-cbc', $key, 0, $iv);
	}

	return json_encode($content);
}

/**
 * Decrypt given content using given key
 *
 * @param string $key     Key.
 * @param string $content Content.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|false
 */
function decrypt_content(string $key, string $content = '')
{
    if (encrypt_supported())
    {
        $content = json_decode($content, true);

        #if json then data is not encrypted
        if ($content !== null) {
            #if json then data is not encrypted
            if (! preg_match('/^[{\[].*[}\]]$/', $content['data']))
            {
                $key = md5($key);

                $iv = encryption_iv($key);

                #decrypt data
                $content = openssl_decrypt($content['data'], 'aes-256-cbc', $key, 0, $iv);
                $content = json_decode($content, true);
            }
        } else {
            # Handle the case where $content is null here
            $content = [];
        }
    }

    return $content;
}


/**
 * Get the initialization vector from key
 *
 * @param string $key Key.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return string
 */
function encryption_iv(string $key): string
{
	return substr($key, 0, getenv('encryption.blockSize'));
}
