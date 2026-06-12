<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FoodNutritionService
{
    public static function getLocalDatabase(): array
    {
        return [
            'ASI (Air Susu Ibu)'      => ['kalori'=>70,  'protein'=>1.0, 'karbohidrat'=>7.4,  'lemak'=>4.4,  'satuan'=>'100ml', 'sumber'=>'TKPI 2017'],
            'Susu formula bayi'       => ['kalori'=>68,  'protein'=>1.5, 'karbohidrat'=>7.2,  'lemak'=>3.6,  'satuan'=>'100ml', 'sumber'=>'TKPI 2017'],
            'Susu sapi segar'         => ['kalori'=>61,  'protein'=>3.2, 'karbohidrat'=>4.7,  'lemak'=>3.4,  'satuan'=>'100ml', 'sumber'=>'TKPI 2017'],
            'Susu UHT full cream'     => ['kalori'=>64,  'protein'=>3.2, 'karbohidrat'=>4.8,  'lemak'=>3.8,  'satuan'=>'100ml', 'sumber'=>'TKPI 2017'],
            'Nasi putih'              => ['kalori'=>175, 'protein'=>3.3, 'karbohidrat'=>40.6, 'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Nasi merah'              => ['kalori'=>149, 'protein'=>2.8, 'karbohidrat'=>33.0, 'lemak'=>0.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Bubur nasi'              => ['kalori'=>46,  'protein'=>1.0, 'karbohidrat'=>10.4, 'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Nasi tim'                => ['kalori'=>90,  'protein'=>2.2, 'karbohidrat'=>20.1, 'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Bubur susu instan'       => ['kalori'=>378, 'protein'=>8.0, 'karbohidrat'=>71.0, 'lemak'=>7.0,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kentang rebus'           => ['kalori'=>77,  'protein'=>1.9, 'karbohidrat'=>17.5, 'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kentang goreng'          => ['kalori'=>318, 'protein'=>3.6, 'karbohidrat'=>40.0, 'lemak'=>15.5, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Roti putih'              => ['kalori'=>248, 'protein'=>7.9, 'karbohidrat'=>49.7, 'lemak'=>1.7,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Roti gandum'             => ['kalori'=>240, 'protein'=>8.5, 'karbohidrat'=>43.0, 'lemak'=>3.4,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Mie instan matang'       => ['kalori'=>138, 'protein'=>2.8, 'karbohidrat'=>20.1, 'lemak'=>5.4,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Singkong rebus'          => ['kalori'=>146, 'protein'=>1.2, 'karbohidrat'=>34.7, 'lemak'=>0.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Jagung manis rebus'      => ['kalori'=>86,  'protein'=>3.2, 'karbohidrat'=>18.7, 'lemak'=>1.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Telur ayam'              => ['kalori'=>155, 'protein'=>13.0,'karbohidrat'=>1.1,  'lemak'=>11.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Telur rebus'             => ['kalori'=>155, 'protein'=>13.0,'karbohidrat'=>1.1,  'lemak'=>11.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Telur dadar'             => ['kalori'=>196, 'protein'=>13.6,'karbohidrat'=>1.6,  'lemak'=>15.3, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Daging ayam tanpa kulit' => ['kalori'=>165, 'protein'=>31.0,'karbohidrat'=>0.0,  'lemak'=>3.6,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Ayam goreng'             => ['kalori'=>260, 'protein'=>27.5,'karbohidrat'=>1.5,  'lemak'=>16.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Daging sapi'             => ['kalori'=>250, 'protein'=>26.0,'karbohidrat'=>0.0,  'lemak'=>15.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Hati sapi'               => ['kalori'=>135, 'protein'=>19.7,'karbohidrat'=>3.8,  'lemak'=>4.7,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Ikan lele'               => ['kalori'=>105, 'protein'=>18.0,'karbohidrat'=>0.0,  'lemak'=>3.0,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Ikan kembung'            => ['kalori'=>103, 'protein'=>22.0,'karbohidrat'=>0.0,  'lemak'=>1.0,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Ikan salmon'             => ['kalori'=>208, 'protein'=>20.0,'karbohidrat'=>0.0,  'lemak'=>13.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Ikan tuna'               => ['kalori'=>132, 'protein'=>28.0,'karbohidrat'=>0.0,  'lemak'=>1.0,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Ikan teri kering'        => ['kalori'=>338, 'protein'=>68.0,'karbohidrat'=>0.0,  'lemak'=>5.0,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Udang segar'             => ['kalori'=>84,  'protein'=>17.8,'karbohidrat'=>0.9,  'lemak'=>0.9,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Tahu'                    => ['kalori'=>76,  'protein'=>8.0, 'karbohidrat'=>1.9,  'lemak'=>4.8,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Tempe'                   => ['kalori'=>193, 'protein'=>19.0,'karbohidrat'=>9.4,  'lemak'=>11.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kacang hijau'            => ['kalori'=>347, 'protein'=>22.0,'karbohidrat'=>63.0, 'lemak'=>1.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kacang merah'            => ['kalori'=>336, 'protein'=>23.1,'karbohidrat'=>59.5, 'lemak'=>1.5,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kacang tanah'            => ['kalori'=>567, 'protein'=>25.8,'karbohidrat'=>16.1, 'lemak'=>49.2, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Bayam'                   => ['kalori'=>23,  'protein'=>2.9, 'karbohidrat'=>3.6,  'lemak'=>0.4,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Wortel'                  => ['kalori'=>41,  'protein'=>0.9, 'karbohidrat'=>10.0, 'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Brokoli'                 => ['kalori'=>34,  'protein'=>2.8, 'karbohidrat'=>7.0,  'lemak'=>0.4,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Labu kuning'             => ['kalori'=>26,  'protein'=>1.0, 'karbohidrat'=>6.5,  'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kangkung'                => ['kalori'=>29,  'protein'=>3.0, 'karbohidrat'=>5.0,  'lemak'=>0.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Tomat'                   => ['kalori'=>18,  'protein'=>0.9, 'karbohidrat'=>3.9,  'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Kacang panjang'          => ['kalori'=>44,  'protein'=>2.8, 'karbohidrat'=>9.7,  'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Buncis'                  => ['kalori'=>35,  'protein'=>2.5, 'karbohidrat'=>7.7,  'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Terong'                  => ['kalori'=>24,  'protein'=>1.1, 'karbohidrat'=>5.7,  'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Pisang ambon'            => ['kalori'=>99,  'protein'=>1.2, 'karbohidrat'=>25.8, 'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Pisang raja'             => ['kalori'=>127, 'protein'=>1.2, 'karbohidrat'=>33.6, 'lemak'=>0.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Alpukat'                 => ['kalori'=>160, 'protein'=>2.0, 'karbohidrat'=>9.0,  'lemak'=>15.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Pepaya matang'           => ['kalori'=>46,  'protein'=>0.5, 'karbohidrat'=>12.2, 'lemak'=>0.0,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Apel'                    => ['kalori'=>58,  'protein'=>0.3, 'karbohidrat'=>14.9, 'lemak'=>0.4,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Jeruk manis'             => ['kalori'=>47,  'protein'=>0.9, 'karbohidrat'=>11.2, 'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Mangga'                  => ['kalori'=>73,  'protein'=>0.6, 'karbohidrat'=>17.0, 'lemak'=>0.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Semangka'                => ['kalori'=>28,  'protein'=>0.6, 'karbohidrat'=>6.9,  'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Jambu biji merah'        => ['kalori'=>49,  'protein'=>0.9, 'karbohidrat'=>12.2, 'lemak'=>0.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Bubur kacang hijau'      => ['kalori'=>98,  'protein'=>4.5, 'karbohidrat'=>18.0, 'lemak'=>0.8,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Puree wortel-kentang'    => ['kalori'=>58,  'protein'=>1.4, 'karbohidrat'=>13.0, 'lemak'=>0.2,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Puree labu kuning'       => ['kalori'=>30,  'protein'=>1.1, 'karbohidrat'=>7.5,  'lemak'=>0.1,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Bubur ayam'              => ['kalori'=>80,  'protein'=>5.0, 'karbohidrat'=>12.0, 'lemak'=>1.5,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Biskuit bayi'            => ['kalori'=>450, 'protein'=>8.0, 'karbohidrat'=>70.0, 'lemak'=>15.0, 'satuan'=>'100gr', 'sumber'=>'Label produk'],
            'Yoghurt plain'           => ['kalori'=>61,  'protein'=>3.5, 'karbohidrat'=>4.7,  'lemak'=>3.3,  'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
            'Keju cheddar'            => ['kalori'=>403, 'protein'=>25.0,'karbohidrat'=>1.3,  'lemak'=>33.0, 'satuan'=>'100gr', 'sumber'=>'TKPI 2017'],
        ];
    }

    public static function searchLocal(string $query): array
    {
        $query   = mb_strtolower(trim($query));
        $results = [];
        foreach (self::getLocalDatabase() as $nama => $data) {
            if (mb_strpos(mb_strtolower($nama), $query) !== false) {
                $results[] = [
                    'nama'        => $nama,
                    'kalori'      => $data['kalori'],
                    'protein'     => $data['protein'],
                    'karbohidrat' => $data['karbohidrat'],
                    'lemak'       => $data['lemak'],
                    'satuan'      => $data['satuan'],
                    'sumber'      => $data['sumber'],
                    'source_type' => 'local',
                ];
            }
        }
        return $results;
    }

    /**
     * Makanan asing yang umum di Indonesia — diizinkan lewat filter.
     */
    private static array $allowedForeignFoods = [
        'pasta', 'pizza', 'spaghetti', 'macaroni', 'makaroni', 'lasagna',
        'burger', 'hotdog', 'sandwich', 'croissant', 'pancake',
        'sushi', 'ramen', 'udon', 'gyoza', 'takoyaki', 'dimsum',
        'steak', 'nugget', 'sausage', 'sosis', 'kornet', 'bacon',
        'cheese', 'yogurt', 'yoghurt', 'butter', 'margarin',
        'cereal', 'oatmeal', 'granola', 'muesli',
        'waffle', 'donut', 'cookies', 'cake', 'brownies', 'pudding',
        'ice cream', 'es krim', 'chocolate', 'coklat',
        'french fries', 'fried rice', 'noodle',
    ];

    /**
     * Cek apakah nama makanan mengandung karakter non-latin yang bukan
     * makanan umum di Indonesia (misal: tulisan Arab, China, Korea, Jepang).
     */
    private static function isRelevantFoodName(string $name): bool
    {
        // Tolak nama kosong atau terlalu pendek
        if (mb_strlen($name) < 2) return false;

        // Tolak nama yang mengandung karakter non-latin (Arab, China, Korea, dll)
        if (preg_match('/[\x{0600}-\x{06FF}\x{4E00}-\x{9FFF}\x{AC00}-\x{D7AF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $name)) {
            return false;
        }

        // Tolak nama dengan terlalu banyak karakter spesial
        $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $name);
        if (mb_strlen($cleanName) < 2) return false;

        return true;
    }

    public static function searchOpenFoodFacts(string $query): array
    {
        $cacheKey = 'off_' . md5(strtolower($query));

        // Cache 24 jam agar tidak request berulang
        return Cache::remember($cacheKey, 86400, function () use ($query) {
            try {
                // Timeout singkat agar tidak blocking UI lama
                $response = Http::timeout(4)->withHeaders([
                    'User-Agent' => 'GrowMOM-MonitoringSystem/1.0',
                ])->get('https://world.openfoodfacts.org/cgi/search.pl', [
                    'search_terms'  => $query,
                    'search_simple' => 1,
                    'action'        => 'process',
                    'json'          => 1,
                    'page_size'     => 15,
                    'fields'        => 'product_name,nutriments,countries_tags,lang',
                    'sort_by'       => 'unique_scans_n',
                    'tagtype_0'     => 'countries',
                    'tag_contains_0'=> 'contains',
                    'tag_0'         => 'indonesia',
                ]);

                if (!$response->successful()) return [];

                $products = $response->json('products') ?? [];
                $results  = [];

                foreach ($products as $p) {
                    $name = trim($p['product_name'] ?? '');
                    if (empty($name)) continue;

                    // Filter nama yang tidak relevan
                    if (!self::isRelevantFoodName($name)) continue;

                    $n      = $p['nutriments'] ?? [];
                    $kalori = $n['energy-kcal_100g'] ?? $n['energy_100g'] ?? null;
                    if ($kalori && ($n['energy_unit'] ?? '') === 'kJ') {
                        $kalori = round($kalori / 4.184, 1);
                    }

                    $protein = $n['proteins_100g']      ?? null;
                    $karbo   = $n['carbohydrates_100g'] ?? null;
                    $lemak   = $n['fat_100g']            ?? null;

                    if ($kalori === null || $protein === null || $karbo === null || $lemak === null) continue;

                    $results[] = [
                        'nama'        => ucwords(mb_strtolower($name)),
                        'kalori'      => round((float)$kalori, 1),
                        'protein'     => round((float)$protein, 1),
                        'karbohidrat' => round((float)$karbo, 1),
                        'lemak'       => round((float)$lemak, 1),
                        'satuan'      => '100gr',
                        'sumber'      => 'Open Food Facts',
                        'source_type' => 'online',
                    ];

                    if (count($results) >= 8) break; // Batasi hasil online
                }

                return $results;
            } catch (\Exception $e) {
                Log::warning('OpenFoodFacts error: ' . $e->getMessage());
                return [];
            }
        });
    }

    public static function search(string $query): array
    {
        // Prioritas 1: Database lokal (cepat, tanpa network)
        $local = self::searchLocal($query);

        // Jika lokal sudah cukup (≥5 hasil), skip API untuk kecepatan
        if (count($local) >= 5) {
            return array_slice($local, 0, 10);
        }

        // Prioritas 2: Open Food Facts (async, di-cache)
        $online     = self::searchOpenFoodFacts($query);
        $localNames = array_map(fn($r) => mb_strtolower($r['nama']), $local);
        $online     = array_values(array_filter($online, fn($r) => !in_array(mb_strtolower($r['nama']), $localNames)));

        return array_slice(array_merge($local, $online), 0, 10);
    }
}