<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('comment_id');
            $table->integer('commentable_id')->unsigned()->index(); // NEW links to blog/log
            $table->string('commentable_type')->default('App\Log'); // NEW
            $table->integer('parent_id')->nullable()->unsigned()->index();
            $table->dateTime('comment_date');
            $table->text('comment');
            $table->text('comment_html');
            $table->integer('user_id')->unsigned();
            $table->string('user_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
