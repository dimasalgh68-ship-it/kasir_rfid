<?php

namespace App\Console\Commands;

use App\Models\RfidCard;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeviceLog;
use App\Services\FirebaseService;
use Illuminate\Console\Command;

class FirebaseListener extends Command
{
    protected $signature = 'firebase:listen';
    protected $description = 'Listen for RFID scans from Firebase Realtime Database';

    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        parent::__construct();
        $this->firebase = $firebase;
    }

    public function handle()
    {
        $this->info("🔥 Firebase Listener started...");
        $this->info("📡 Listening for RFID scans from ESP32...\n");

        while (true) {
            try {
                // Baca /scans/latest dari Firebase
                $scan = $this->firebase->get('scans/latest');

                if ($scan && isset($scan['rfid_uid']) && ($scan['status'] ?? '') === 'pending') {
                    $uid = strtoupper($scan['rfid_uid']);
                    $deviceId = $scan['device_id'] ?? 'unknown';

                    $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
                    $this->info("📟 Scan diterima: {$uid}");
                    $this->line("   Device: {$deviceId}");

                    // Log ke database
                    DeviceLog::create([
                        'device_id' => $deviceId,
                        'action' => 'scan',
                        'payload' => json_encode($scan),
                    ]);

                    // Cari kartu
                    $card = RfidCard::where('rfid_uid', $uid)->first();

                    if (!$card) {
                        $this->error("   ❌ Kartu tidak terdaftar!");
                        $this->firebase->sendResponse($deviceId, [
                            'status' => 'error',
                            'message' => 'Kartu tidak terdaftar',
                            'user_name' => '',
                            'balance' => '0',
                        ]);
                    } elseif (!$card->is_active) {
                        $this->error("   ❌ Kartu dinonaktifkan!");
                        $this->firebase->sendResponse($deviceId, [
                            'status' => 'error',
                            'message' => 'Kartu dinonaktifkan',
                            'user_name' => $card->user->name,
                            'balance' => '0',
                        ]);
                    } else {
                        $this->info("   ✅ {$card->user->name}");
                        $this->info("   💰 Saldo: Rp " . number_format($card->balance, 0, ',', '.'));

                        $this->firebase->sendResponse($deviceId, [
                            'status' => 'success',
                            'message' => 'Kartu valid',
                            'user_name' => $card->user->name,
                            'balance' => number_format($card->balance, 0, ',', '.'),
                        ]);
                    }

                    // Tandai scan sudah diproses
                    $this->firebase->update('scans/latest', ['status' => 'processed']);
                    $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n");
                }
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
            }

            // Poll setiap 0.5 detik (lebih responsif)
            usleep(500000);
        }
    }
}
