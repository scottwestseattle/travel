<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_location', function (Blueprint $table) {
			$table->unsignedInteger('entry_id');
			$table->unsignedInteger('location_id');
			$table->timestamps();

			$table->unique(['entry_id','location_id']);
			$table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_location');
    }
}
