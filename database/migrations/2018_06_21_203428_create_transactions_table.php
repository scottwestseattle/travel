<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');			
			$table->string('description', 100);
            $table->timestamps();
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('parent_id');
			$table->tinyInteger('type_flag')->nullable()->default(0);			
			$table->unsignedInteger('category_id');
			$table->unsignedInteger('subcategory_id');
			$table->decimal('amount', 10, 2);
			$table->date('transaction_date');
			$table->string('notes', 255)->nullable();
			
			$table->string('vendor_memo', 50)->nullable();
			$table->tinyInteger('transfer_account_id')->nullable();			
			$table->tinyInteger('reconciled_flag')->nullable()->default(1);			
			$table->tinyInteger('deleted_flag')->nullable()->default(0);
        });
    }
	
/*

  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT 'credit, debit',
  `category` int(11) NOT NULL,
  `subcategory` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `notes` varchar(255) DEFAULT NULL

*/

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
