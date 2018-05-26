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
			//$table->unsignedInteger('visit_count')->default(1);
			$table->string('name', 50);
			$table->string('host_name', 100);
			$table->string('logo_path', 100);
			$table->string('title', 50)->nullable();
			$table->text('main_section_text');
			$table->text('main_section_subtext')->nullable();
			$table->tinyInteger('deleted_flag')->default(0);			
			$table->text('current_location_map_link')->nullable();
			$table->text('affiliate_link_home_section1_1')->nullable();
			$table->text('affiliate_link_home_section1_2')->nullable();
			$table->text('affiliate_link_home_section1_3')->nullable();
			$table->text('affiliate_link_footer1')->nullable();
			$table->text('affiliate_link_footer2')->nullable();
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
