<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
			
			$table->integer('user_id')->unsigned()->index();	// photo owner
			$table->integer('parent_id')->nullable();			// if it belongs to an entry
			$table->string('alt_text');							// html alt text
			$table->string('filename');							// physical file name
			$table->string('title')->nullable();					// photo title (not used yet)
			$table->string('location')->nullable();				// photo location (place, city, region, country)
			$table->tinyInteger('approved_flag')->default(0);	// has been approved by admin
			$table->tinyInteger('main_flag')->default(0);		// the main photo
			$table->unsignedInteger('view_count')->default(0);	// optional: view count
			$table->tinyInteger('type')->default(-1);			// photo type: slider, tour entry, post entry, etc
			$table->tinyInteger('deleted_flag')->default(0);	// photo record has been deleted or not			
			
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
        Schema::dropIfExists('photos');
    }
}
