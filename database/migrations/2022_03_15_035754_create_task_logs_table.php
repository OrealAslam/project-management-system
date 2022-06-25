<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->string('logtime');
            // add foreign key user_id (developer_id)
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // add foreign key project_id 
            $table->bigInteger('project_id')->unsigned()->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            // add foreign key task_id 
            $table->bigInteger('task_id')->unsigned()->index();
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');

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
        Schema::dropIfExists('task_logs');
    }
};
