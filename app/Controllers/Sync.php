<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;
use Exception;

class Sync extends BaseController
{
    private $fields = array("ICILOC.ITEMNO", "LOCATION", "QTYONHAND", "CURRENCY", "UNITPRICE");

    private $rows = array();

    public function index()
    {
        helper('remote');

        $content = file_get_contents(WRITEPATH . "data/sites.json");
        $sites = json_decode($content, true);

        foreach ($sites["urls"] as $site) {
            //sync sites
            \remoteGetContent($site, array($this, "handleResponse"), $sites, "network");

            //get locations
            \remoteGetContent($site, array($this, "handleResponse"));

            //handle stock
            \remoteGetContent($site, array($this, "handleResponse"), $this->rows, "synchronise");
        }
    }

    public function handleResponse($response)
    {

        foreach ($response as $action => $data) {
            switch ($action) {
                case "locations":
                    #save to cache

                    break;
                case "database":
                    #normalise and query database

                    $placeholders = array(
                        "{{fields}}" => implode(", ", $this->fields),
                    );

                    $data = array_map(function ($database) use ($placeholders) {
                        $database["sql"] = strtr($database["sql"], $placeholders);
                        return $database;
                    }, $data);

                    $this->rows = array();
                    foreach ($data as $database) {
                        $connection = config('Database')->default;

                        #query data
                        try {
                            $db = db_connect($connection);
                            $db->setDatabase($database["database"]);
                            $query = $db->query($database["sql"]);

                            #map data
                            foreach ($query->getResult() as $row) {
                                $this->rows[] = array(
                                    "quantity" => intval($row->QTYONHAND),
                                    "price" => floatval($row->UNITPRICE),
                                    "location" => trim($row->LOCATION),
                                    "sku" => trim($row->ITEMNO),
                                );
                            }
                        } catch (\Exception $e) {
                        } finally {
                            if ($db instanceof BaseConnection) {
                                $db->close();
                            }
                        }
                    }
                    break;
                case "network":
                    $content = file_get_contents(WRITEPATH . "data/sites.json");
                    $sites = json_decode($content, true);

                    if ($data["time"] > $sites["time"]) {
                        @file_put_contents(WRITEPATH . "data/sites.json", json_encode($data, JSON_PRETTY_PRINT));
                    }
                    break;
            }
        }
    }
}