<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
			
			$table->string('name', 100);
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('parent_id')->nullable();
			$table->string('notes', 255)->nullable();
			$table->string('password_hint', 20)->nullable();
			$table->tinyInteger('account_type_flag');
			$table->string('linked_accounts', 100)->nullable();
			$table->decimal('balance', 10, 2)->nullable()->default(0.0);
			$table->decimal('starting_balance', 10, 2)->nullable()->default(0.0);
			$table->tinyInteger('hidden_flag')->nullable()->default(0);
			$table->tinyInteger('deleted_flag')->nullable()->default(0);
			
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
