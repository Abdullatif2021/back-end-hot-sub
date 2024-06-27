<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->morphs('roleable'); 

            $table->primary(['role_id', 'roleable_id', 'roleable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
