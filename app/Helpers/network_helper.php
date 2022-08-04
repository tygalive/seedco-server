<?php

/**
 * Network Helper
 */

use function _\uniqBy as array_unique_by;
use function _\differenceBy as array_diff_by;

/**
 * Get network store
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return array
 */
function network_get_store():array
{
	$default = [
		'time' => time(),
		'urls' => [],
	];

	$content = file_get_contents(WRITEPATH . 'data/sites.json') ?: json_encode($default);
	return json_decode($content, true);
}

/**
 * Save network data for all stores
 *
 * @param array $data Data.
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return void
 */
function network_set_store(array $data):void
{
	$store = network_get_store();

	#unique filter
	$sites = array_unique_by($data['urls'], function ($site) {
		return remote_trim_url($site['link']);
	});

	#if different
	$diff = count(array_diff_by($sites, $data['urls'], function ($site) {
		return remote_trim_url($site['link']);
	}));

	if ($diff != 0)
	{
		$data['time'] = time();
	}
	$data['urls'] = $sites;

	if ($data['time'] > $store['time'])
	{
		@file_put_contents(WRITEPATH . 'data/sites.json', json_encode($data, JSON_PRETTY_PRINT));
	}
}

/**
 * Get the urls for all the stores
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return array
 */
function network_stores():array
{
	return network_get_store()['urls'] ?? [];
}
