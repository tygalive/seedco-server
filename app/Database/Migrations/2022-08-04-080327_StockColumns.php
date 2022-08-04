<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StockColumns extends Migration
{
    public function up()
    {

        // Stock.
		$fields = array(
			'ICILOC.ITEMNO' => array(
				'type' => 'TEXT',
			),
			'LOCATION' => array(
				'type' => 'TEXT',
			),
			'QTYONHAND' => array(
				'type' => 'TEXT',
			),
			'CURRENCY' => array(
				'type' => 'TEXT',
			),
			'UNITPRICE' => array(
				'type' => 'TEXT',
			),
		);

		$this->forge->addField($fields);
        
		$this->forge->createTable("stocks", true, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->forge->dropTable('stocks');
    }
}
