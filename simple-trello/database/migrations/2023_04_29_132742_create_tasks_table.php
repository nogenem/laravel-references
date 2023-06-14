<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
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
            $table->string('title', 100);
            $table->mediumText('description')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('priority', 20)->nullable();
            $table->timestamp('due_date')->nullable();

            $table->unsignedBiginteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')
                ->on('users')->nullOnDelete();

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
        Schema::dropIfExists('tasks');
    }
}
