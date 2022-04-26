<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSmsTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('sms-auth.sms_tokens_table', 'sms_tokens'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('token', 255);
            $table->timestamp('available_at')->nullable();
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
        Schema::dropIfExists(config('sms-auth.sms_tokens_table', 'sms_tokens'));
    }
}
