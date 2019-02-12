<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('user_id')->nullable();
			$table->unsignedInteger('parent_id');

            $table->string('name', 100);
            $table->string('comment', 500);
			$table->tinyInteger('type_flag')->nullable();

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
        Schema::dropIfExists('comments');
    }
}
