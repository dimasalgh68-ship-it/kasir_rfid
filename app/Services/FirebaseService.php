<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected string $databaseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->databaseUrl = rtrim(config('services.firebase.database_url', ''), '/');
        $this->apiKey = config('services.firebase.api_key', '');
    }

    /**
     * Read data from Firebase RTDB
     */
    public function get(string $path): ?array
    {
        try {
            $response = Http::get("{$this->databaseUrl}/{$path}.json");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error("[Firebase] GET error: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("[Firebase] GET exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Write data to Firebase RTDB
     */
    public function set(string $path, array $data): bool
    {
        try {
            $response = Http::put("{$this->databaseUrl}/{$path}.json", $data);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("[Firebase] SET exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update data in Firebase RTDB
     */
    public function update(string $path, array $data): bool
    {
        try {
            $response = Http::patch("{$this->databaseUrl}/{$path}.json", $data);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("[Firebase] UPDATE exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete data from Firebase RTDB
     */
    public function delete(string $path): bool
    {
        try {
            $response = Http::delete("{$this->databaseUrl}/{$path}.json");
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("[Firebase] DELETE exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send response back to ESP32 via Firebase
     */
    public function sendResponse(string $deviceId, array $data): bool
    {
        return $this->set("responses/{$deviceId}", $data);
    }
}
