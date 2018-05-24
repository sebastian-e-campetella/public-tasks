<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $connection = 'mongodb';
   
    public function up()
    {
        Schema::connection($this->connection);
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable(false);
            $table->text('description');
            $table->dateTime('due_date')->nullable(false);
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->primary('id');
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
