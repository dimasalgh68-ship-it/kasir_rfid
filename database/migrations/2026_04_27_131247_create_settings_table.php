<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insert Default Settings
        \DB::table('settings')->insert([
            ['key' => 'midtrans_server_key', 'value' => env('MIDTRANS_SERVER_KEY'), 'group' => 'midtrans'],
            ['key' => 'midtrans_client_key', 'value' => env('MIDTRANS_CLIENT_KEY'), 'group' => 'midtrans'],
            ['key' => 'midtrans_is_production', 'value' => env('MIDTRANS_IS_PRODUCTION', '0'), 'group' => 'midtrans'],
            ['key' => 'app_name', 'value' => 'Kantin Digital RFID', 'group' => 'general'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
