<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../middleware/Logger.php';

class ExternalMealService
{
    public function search_meals(?string $query = null): array
    {
        $baseUrl = rtrim(Config::EXTERNAL_API_URL(), '/');
        $timeout = Config::EXTERNAL_API_TIMEOUT();

        $trimmed = trim((string)$query);
        $endpoint = $trimmed !== ''
            ? "{$baseUrl}/search.php?s=" . urlencode($trimmed)
            : "{$baseUrl}/random.php";

        Logger::info("EXTERNAL_API GET {$endpoint}");

        if (!function_exists('curl_init')) {
            Logger::error("EXTERNAL_API GET {$endpoint} cURL extension is not enabled in PHP");
            return $this->fallback_response("Meal inspiration lookup is currently unavailable on the server.");
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => min(3, $timeout),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json']
        ]);

        $rawResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($rawResponse === false || $curlError !== '') {
            Logger::error("EXTERNAL_API GET {$endpoint} cURL failed: {$curlError}");
            return $this->fallback_response("Could not reach the external meal database right now. Please try again later.");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            Logger::error("EXTERNAL_API GET {$endpoint} returned HTTP {$httpCode}");
            return $this->fallback_response("The external meal database returned an unexpected error (HTTP {$httpCode}).");
        }

        $decoded = json_decode($rawResponse, true);
        if (!is_array($decoded)) {
            Logger::error("EXTERNAL_API GET {$endpoint} returned invalid JSON");
            return $this->fallback_response("Received an unreadable response from the external meal database.");
        }

        $rawMeals = $decoded['meals'] ?? [];
        if (!is_array($rawMeals)) {
            $rawMeals = [];
        }

        $formattedMeals = array_map(function ($meal) {
            return [
                'external_id'  => $meal['idMeal'] ?? null,
                'title'        => $meal['strMeal'] ?? 'Untitled Meal',
                'category'     => $meal['strCategory'] ?? 'General',
                'area'         => $meal['strArea'] ?? 'International',
                'instructions' => $meal['strInstructions'] ?? '',
                'thumbnail'    => $meal['strMealThumb'] ?? null,
                'youtube_url'  => $meal['strYoutube'] ?? null
            ];
        }, array_slice($rawMeals, 0, 8));

        return [
            'available' => true,
            'count'     => count($formattedMeals),
            'meals'     => $formattedMeals
        ];
    }

    private function fallback_response(string $userMessage): array
    {
        return [
            'available' => false,
            'message'   => $userMessage,
            'count'     => 0,
            'meals'     => []
        ];
    }
}