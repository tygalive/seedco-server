<?php

/**
 * Messenger Helper
 */

$messages = [];

/**
 * Add message to queue
 *
 * @param string        $key  Key.
 * @param mixed|boolean $data Data.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return void
 */
function message_add(string $key, $data):void
{
	global $messages;

	$messages[$key] = $data;
}

/**
 * Get queued message
 *
 * @param string $key Key.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|boolean
 */
function message_get(string $key)
{
	global $messages;

	return $messages[$key] ?? false;
}

/**
 * Get all queued messages
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return array
 */
function message_get_all():array
{
	global $messages;

	$content = $messages ?: [];

	$messages = [];

	return $content;
}

/**
 * Get whether messenger has content
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return boolean
 */
function message_has_content():bool
{
	global $messages;

	return ! empty($messages ?? []);
}

/**
 * Delete queued message
 *
 * @param string $key Key.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return void
 */
function message_delete(string $key):void
{
	global $messages;

	if (isset($messages[$key]))
	{
		unset($messages[$key]);
	}
}

/**
 * Get and remove the first message
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return mixed|boolean
 */
function message_shift()
{
	global $messages;

	return array_shift($messages) ?? false;
}
