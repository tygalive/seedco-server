<?php

use function _\uniqBy as array_unique_by;
use function _\differenceBy as array_diff_by;

/**
 * Get network store
 *
 * @return array
 */
function networkGetStore()
{
    $default = array(
        "time" => time(),
        "urls" => array()
    );

    $content = file_get_contents(WRITEPATH . "data/sites.json") ?: json_encode($default);
    return json_decode($content, true);
}

/**
 * Save network data for all stores
 *
 * @param array $data
 * @return void
 */
function networkSetStore(array $data)
{
    $store = networkGetStore();

    #unique filter
    $sites = array_unique_by($data["urls"], function ($site) {
        return remoteTrimUrl($site["link"]);
    });

    #if different
    $diff = count(array_diff_by($sites, $data["urls"], function ($site) {
        return remoteTrimUrl($site["link"]);
    }));

    if ($diff != 0) {
        $data["time"] = time();
    }
    $data["urls"] = $sites;

    if ($data["time"] > $store["time"]) {
        @file_put_contents(WRITEPATH . "data/sites.json", json_encode($data, JSON_PRETTY_PRINT));
    }
}

/**
 * Get the urls for all the stores
 *
 * @return array
 */
function networkStores()
{
    return networkGetStore()["urls"] ?? array();
}