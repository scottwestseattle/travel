<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
			$table->tinyInteger('search_whole_words_flag')->after('remember_token')->default(-1);			
			$table->tinyInteger('search_title_only_flag')->after('remember_token')->default(-1);			
			$table->tinyInteger('view_id')->after('remember_token')->default(-1);
			$table->unsignedInteger('template_id')->after('remember_token')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
