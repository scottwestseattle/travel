<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries_tags', function (Blueprint $table) {
			$table->unsignedInteger('entry_id');
			$table->unsignedInteger('tag_id');
			$table->timestamps();

			$table->unique(['entry_id','tag_id']);
			$table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
			$table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entries_tags');
    }
}
