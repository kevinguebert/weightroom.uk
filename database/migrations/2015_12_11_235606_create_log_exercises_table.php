<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_exercises', function (Blueprint $table) {
            $table->increments('logex_id');
            $table->integer('log_id')->unsigned()->index();
            $table->date('log_date'); //RENAMED colomn
            $table->integer('user_id')->unsigned()->index();
            $table->integer('exercise_id')->unsigned()->index();
            $table->double('logex_volume', 20, 2);
            $table->integer('logex_reps');
            $table->integer('logex_sets');
            $table->double('logex_failed_volume', 20, 2); // NEW colomn
            $table->integer('logex_failed_sets'); // NEW colomn
            $table->double('logex_warmup_volume', 20, 2); // NEW colomn
            $table->integer('logex_warmup_reps'); // NEW colomn
            $table->integer('logex_warmup_sets'); // NEW colomn
            $table->double('logex_time', 20, 2); // NEW colomn
            $table->double('logex_distance', 20, 2); // NEW colomn
            $table->string('logex_comment');
            $table->double('logex_1rm', 20, 2);
            $table->smallInteger('logex_order');
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
        Schema::drop('log_exercises');
    }
}
