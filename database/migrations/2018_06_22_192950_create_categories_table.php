<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');			
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('parent_id')->nullable()->comment('only used for subcategories');;
			
			$table->string('name', 255);
			$table->string('notes', 255)->nullable();
			$table->tinyInteger('type_flag')->nullable()->default(0)->comment('not used: 1=Expense, 2=Income, 3=Transfer');
			$table->decimal('amount', 10, 2)->nullable()->default(0)->comment('used for split transaction categories only');

			$table->tinyInteger('deleted_flag')->nullable()->default(0);
            $table->timestamps();
        });
    }

/*			
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Expense, 2=Income, 3=Transfer'
*/		
	
	
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
