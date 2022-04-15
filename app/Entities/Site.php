<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\Database\BaseConnection;

class Site extends Entity
{
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $this->init();
    }

    /**
     * Fields to fetch from the database
     *
     * @var array
     */
    private $fields = array("ICILOC.ITEMNO", "LOCATION", "QTYONHAND", "CURRENCY", "UNITPRICE");

    /**
     * Register event listeners
     *
     * @return void
     */
    public function init()
    {
        \CodeIgniter\Events\Events::on("sync_locations", array($this, "handleLocations"));
        \CodeIgniter\Events\Events::on("sync_database", array($this, "handleDatabase"));
        \CodeIgniter\Events\Events::on("sync_network", array($this, "handleNetwork"));
    }

    /**
     * Clear all event listeners
     *
     * @return void
     */
    public function destroy()
    {
        \CodeIgniter\Events\Events::removeAllListeners("sync_locations");
        \CodeIgniter\Events\Events::removeAllListeners("sync_database");
        \CodeIgniter\Events\Events::removeAllListeners("sync_network");
    }

    /**
     * Request environment from site
     *
     * @param mixed $data
     * @return void
     */
    public function handleLocations($data)
    {
        #No action to perform
    }

    /**
     * Prepare local stock levels for remote site
     *
     * @param mixed $data
     * @return void
     */
    public function handleDatabase($data)
    {
        #normalise and query database

        $placeholders = array(
            "{{fields}}" => implode(", ", $this->fields),
        );

        $data = array_map(function ($database) use ($placeholders) {
            $database["sql"] = strtr($database["sql"], $placeholders);
            return $database;
        }, $data);

        $rows = array();
        foreach ($data as $database) {
            $connection = config('Database')->default;

            #query data
            try {
                $db = db_connect($connection);

                #set database as requested by remote site
                $db->setDatabase($database["database"]);
                $query = $db->query($database["sql"]);

                #map data
                foreach ($query->getResult() as $row) {
                    $rows[] = array(
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

        messageAdd("synchronise", $rows);
    }

    /**
     * Handle network sites
     *
     * @param mixed $data
     * @return void
     */
    public function handleNetwork($data)
    {
        networkSetStore($data);

        messageAdd("environment", true);
    }
}