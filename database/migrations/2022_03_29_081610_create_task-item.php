<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_item', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('remarks')->nullable();
            
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('task');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task-item');
    }
}
