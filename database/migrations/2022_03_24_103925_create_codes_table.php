<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('codes', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('student_id')->nullable();
        //     $table->foreign('student_id')->references('id')->on('students');
        //     $table->string('editorial');
        //     $table->string('code1');
        //     $table->string('code2');
        //     $table->string('code3');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codes');
    }
}
