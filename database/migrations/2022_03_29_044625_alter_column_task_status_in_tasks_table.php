<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    // public function __construct()
    // {
    //     DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    // }

    public function __construct()
    {
        if (!Type::hasType('enum')) {
            Type::addType('enum', StringType::class);
        }
        // For point types
        // DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'string');
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_status');
            // $table->enum('task_status', ['pending', 'completed'])->change();
            // $table->enum('task_status', ['pending', 'completed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
