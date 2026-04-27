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
        Schema::table('topups', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('status');
            $table->string('reference_id')->nullable()->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('topups', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'reference_id']);
        });
    }
};
