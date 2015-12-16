<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_items', function (Blueprint $table) {
            $table->increments('logitem_id');
            $table->date('logitem_date');
            $table->integer('log_id')->index();
            $table->integer('user_id')->index();
            $table->integer('exercise_id')->index();
            $table->double('logitem_weight', 20, 3);
            $table->double('logitem_time', 20, 3);
            $table->double('logitem_abs_weight', 20, 3);
            $table->double('logitem_1rm', 20, 3);
            $table->integer('logitem_reps');
            $table->integer('logitem_sets');
            $table->double('logitem_pre', 3, 1); // CAHNGED NAME
            $table->text('logitem_comment');
            $table->smallInteger('logitem_order');
            $table->smallInteger('logex_order');
            $table->boolean('is_bw');
            $table->boolean('is_time');
            $table->boolean('is_pr');
            $table->boolean('is_warmup');
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
        Schema::drop('log_items');
    }
}
