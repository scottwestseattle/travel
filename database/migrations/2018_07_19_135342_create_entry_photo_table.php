<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_photo', function (Blueprint $table) {
			$table->unsignedInteger('entry_id');
			$table->unsignedInteger('photo_id');
			$table->timestamps();

			$table->unique(['entry_id','photo_id']);
			$table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
			$table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_photo');
    }
}
