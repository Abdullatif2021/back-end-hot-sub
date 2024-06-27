<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->foreignId('user_id')->constrained('users');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('requests', function (Blueprint $table) {
        // Drop the foreign key constraint
        $table->dropForeign(['service_id']);
        // Drop the service_id column
        $table->dropColumn('service_id');
    });
}
};
