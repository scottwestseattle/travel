<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {

			// ids
			$table->increments('id');
			$table->unsignedInteger('site_id');
			$table->unsignedInteger('user_id');

			// table custom data
			$table->string('title', 100);
			$table->string('permalink', 255);
			$table->text('description')->nullable();

			// flags
			$table->tinyInteger('published_flag')->default(0);
			$table->tinyInteger('approved_flag')->default(0);
			$table->tinyInteger('deleted_flag')->default(0);
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
        Schema::dropIfExists('sites');
    }
}
