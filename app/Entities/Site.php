<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Events\Events;
use Exception;

/**
 * Site Entity
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Site extends Entity
{

	/**
	 * Initialize
	 *
	 * @param array|null $data Data.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 */
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
	private $fields = [
		'ICILOC.ITEMNO',
		'LOCATION',
		'QTYONHAND',
		'CURRENCY',
		'UNITPRICE',
	];

	/**
	 * Register event listeners
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function init():void
	{
		Events::on('sync_locations', [$this, 'handleLocations']);
		Events::on('sync_database', [$this, 'handleDatabase']);
		Events::on('sync_network', [$this, 'handleNetwork']);
	}

	/**
	 * Clear all event listeners
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function destroy():void
	{
		Events::removeAllListeners('sync_locations');
		Events::removeAllListeners('sync_database');
		Events::removeAllListeners('sync_network');
	}

	/**
	 * Request environment from site
	 *
	 * @param mixed|false $data Data.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function handleLocations($data):void
	{
		#No action to perform
	}

	/**
	 * Prepare local stock levels for remote site
	 *
	 * @param mixed|false $data Data.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function handleDatabase($data):void
	{
		#normalize and query database

		$placeholders = [
			'{{fields}}' => implode(', ', $this->fields),
		];

		$data = array_map(function ($database) use ($placeholders) {
			$database['sql'] = strtr($database['sql'], $placeholders);
			return $database;
		}, $data);

		$rows = [];
		foreach ($data as $database)
		{
			$connection = config('Database')->default;

			#query data
			try
			{
				$db = db_connect($connection);

				#set database as requested by remote site
				$db->setDatabase($database['database']);
				$query = $db->query($database['sql']);

				// die(json_encode($query->getResult(), JSON_PRETTY_PRINT));

				#map data
				foreach ($query->getResult() as $row)
				{
					$rows[] = [
						'quantity' => intval($row->QTYONHAND),
						'price'    => floatval($row->UNITPRICE),
						'location' => trim($row->LOCATION),
						'sku'      => trim($row->ITEMNO),
					];
				}
			}
			catch (Exception $e)
			{
			} finally {
				if ($db instanceof BaseConnection)
				{
					$db->close();
				}
			}
		}

		message_add('synchronise', $rows);
	}

	/**
	 * Handle network sites
	 *
	 * @param mixed|false $data Data.
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return void
	 */
	public function handleNetwork($data):void
	{
		network_set_store($data);

		message_add('environment', true);
	}
}
