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
        // Schema::dropIfExists('invoices');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->date('date_created');
            $table->date('start_date');
            $table->date('end_date');
            $table->float('invoice_rate');
            $table->float('total_hours');
            $table->timestamps();
        });
    }
};
