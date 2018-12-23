<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
			$table->tinyInteger('deleted_flag')->default(0);
			$table->unsignedInteger('user_id');

			// basic info
			$table->string('site_name', 50);
			$table->string('site_url', 100);
			$table->string('site_title', 255)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('telephone', 20)->nullable();
			
			// main content
			$table->string('logo_filename', 100)->nullable();
			$table->text('main_section_text')->nullable();
			$table->text('main_section_subtext')->nullable();
			$table->text('seo_text')->nullable();
			
			// settings
			$table->tinyInteger('tour_photos_minimum')->default(-1); // when to show as admin to do item
			$table->tinyInteger('sections-show-blogs')->default(0);
			$table->tinyInteger('sections-show-tours')->default(0);
			$table->tinyInteger('sections-show-articles')->default(0);

			// current/previous location
			$table->text('current_location_map_link')->nullable();
			$table->text('current_location_photo')->nullable();
			$table->text('previous_location_list')->nullable();
			
			// affiliate links
			$table->text('affiliate_link_home_section1_1')->nullable();
			$table->text('affiliate_link_home_section1_2')->nullable();
			$table->text('affiliate_link_home_section1_3')->nullable();
			$table->text('affiliate_link_footer1')->nullable();
			$table->text('affiliate_link_footer2')->nullable();
			
			$table->text('affiliate_link1')->nullable();
			$table->text('affiliate_link2')->nullable();
			$table->text('affiliate_link3')->nullable();
			$table->text('affiliate_link4')->nullable();
			$table->text('affiliate_link5')->nullable();
			$table->text('affiliate_link6')->nullable();
			$table->text('affiliate_link7')->nullable();
			$table->text('affiliate_link8')->nullable();
			$table->text('affiliate_link9')->nullable();
			$table->text('affiliate_link10')->nullable();
			
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
