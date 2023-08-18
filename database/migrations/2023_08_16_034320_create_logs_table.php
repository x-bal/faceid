<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('device_id')->constrained('devices');
            $table->integer('moustache')->default(0);
            $table->integer('beard')->default(0);
            $table->string('foto')->nullable();
            $table->integer('suhu');
            $table->timestamp('waktu');
            $table->enum('status', ["Healthy", "Not Healthy"])->nullable();
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
        Schema::dropIfExists('logs');
    }
}
