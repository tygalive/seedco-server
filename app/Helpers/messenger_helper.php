<?php

$messages = array();

/**
 * Add message to queue
 *
 * @global array $messages
 * @param string $key
 * @param mixed $data
 * @return void
 */
function messageAdd(string $key, mixed $data)
{
    global $messages;
   
    $messages[$key] = $data;
}

/**
 * Get queued message
 *
 * @global array $messages
 * @param string $key
 * @return mixed|bool
 */
function messageGet(string $key)
{
    global $messages;

    return $messages[$key] ?? false;
}

/**
 * Get all queued messages
 *
 * @global array $messages
 * @return array
 */
function messageGetAll()
{
    global $messages;

    $content = $messages ?: array();

    $messages = array();

    return $content;
}

/**
 * Get whether messanger has content
 *
 * @return bool
 */
function messageHasContent()
{
    global $messages;

    return !empty($messages ?? array());
}

/**
 * Delete queued message
 *
 * @global array $messages
 * @param string $key
 * @return void
 */
function messageDelete(string $key)
{
    global $messages;

    if (isset($messages[$key])) {
        unset($messages[$key]);
    }
}

/**
 * Get and remove the first messege
 *
 * @global array $messages
 * @return mixed|bool
 */
function messageShift()
{
    global $messages;

    return array_shift($messages) ?? false;
}