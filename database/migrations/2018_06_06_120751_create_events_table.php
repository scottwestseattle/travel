<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
			
			$table->tinyInteger('deleted_flag')->default(0);
			$table->unsignedInteger('site_id');
			$table->unsignedInteger('user_id')->nullable();

			$table->tinyInteger('type_flag');	// info, warning, error, exception, etc
			$table->string('model_flag', 20);	// sites, users, photos, etc
			$table->string('action_flag', 20);	// access, add, deleted, update, etc
			$table->string('title', 100);
			
			$table->string('description', 255)->nullable();
			$table->unsignedInteger('record_id')->nullable();
			$table->text('updates')->nullable();
			$table->text('error')->nullable();
			$table->text('extraInfo')->nullable();
			
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
        Schema::dropIfExists('events');
    }
}
