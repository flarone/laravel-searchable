<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_index', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('searchcontent');
            $table->string('field');
            $table->string('model');
            $table->uuid('model_id');
            $table->string('parent_model');
            $table->uuid('parent_id');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::unprepared('ALTER TABLE search_index ADD INDEX searchcontent_idx (searchcontent(767))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_index');
    }
}
