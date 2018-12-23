<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
			
            $table->increments('id');
			$table->string('parent_table', 30);
			$table->unsignedInteger('parent_id');
			$table->string('language', 5); // en, es, zh, en-GB, en-US, es-ES, es-MX

			$table->string('small_col1', 100)->nullable();
			$table->string('small_col2', 100)->nullable();
			$table->string('small_col3', 100)->nullable();
			$table->string('small_col4', 100)->nullable();
			$table->string('small_col5', 100)->nullable();
			$table->string('small_col6', 100)->nullable();
			$table->string('small_col7', 100)->nullable();
			$table->string('small_col8', 100)->nullable();
			$table->string('small_col9', 100)->nullable();
			$table->string('small_col10', 100)->nullable();

			$table->string('medium_col1', 512)->nullable();
			$table->string('medium_col2', 512)->nullable();
			$table->string('medium_col3', 512)->nullable();
			$table->string('medium_col4', 512)->nullable();
			$table->string('medium_col5', 512)->nullable();
			$table->string('medium_col6', 512)->nullable();
			$table->string('medium_col7', 512)->nullable();
			$table->string('medium_col8', 512)->nullable();
			$table->string('medium_col9', 512)->nullable();
			$table->string('medium_col10', 512)->nullable();
			
			$table->text('large_col1')->nullable();
			$table->text('large_col2')->nullable();
			$table->text('large_col3')->nullable();
			$table->text('large_col4')->nullable();
			$table->text('large_col5')->nullable();
			$table->text('large_col6')->nullable();
			$table->text('large_col7')->nullable();
			$table->text('large_col8')->nullable();
			$table->text('large_col9')->nullable();
			$table->text('large_col10')->nullable();
			
			$table->tinyInteger('approved_flag')->default(0);
			$table->tinyInteger('deleted_flag')->default(0);
            $table->timestamps();
			
			// constraints
			$table->unique(['parent_id', 'parent_table', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
