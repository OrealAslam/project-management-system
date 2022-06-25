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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_title');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->bigInteger('project_id')->unsigned()->nullable()->index();
            $table->date('date');
            $table->string('hours_spend')->nullable();
            $table->string('task_status')->nullable();
            $table->timestamps();

            // add foreign key user_id (developer_id)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
