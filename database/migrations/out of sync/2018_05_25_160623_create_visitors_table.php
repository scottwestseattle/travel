<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('site_id');
			
			$table->string('ip_address', 50);
			$table->string('host_name', 200)->nullable();
			$table->string('user_agent', 200)->nullable();
			$table->string('referrer', 200)->nullable();
			$table->string('organization', 100)->nullable();
			$table->string('continent', 20)->nullable();
			$table->string('country', 30)->nullable();
			$table->string('state_region', 50)->nullable();
			$table->string('city', 50)->nullable();
			
			$table->unsignedInteger('visit_count')->default(1);

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
        Schema::dropIfExists('visitors');
    }
}
