<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExternalApiService
{
    /**
     * Base URL for external APIs
     */
    private $baseUrl;
    
    /**
     * API timeout in seconds
     */
    private $timeout = 30;
    
    /**
     * Cache duration in minutes
     */
    private $cacheDuration = 60;

    public function __construct()
    {
        $this->baseUrl = config('app.external_api_base_url', 'http://localhost:8000/api/v1');
    }

    /**
     * Get user information from User Management Module
     * IFA: User Information Service Consumption
     */
    public function getUserInfo($userId, $queryFlag = 1)
    {
        $cacheKey = "user_info_{$userId}_{$queryFlag}";
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($userId, $queryFlag) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/users/{$userId}", [
                        'query_flag' => $queryFlag
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'success' => true,
                            'data' => $data['data']
                        ];
                    }
                }

                return [
                    'success' => false,
                    'error' => 'Failed to retrieve user information',
                    'status_code' => $response->status()
                ];

            } catch (\Exception $e) {
                Log::error('External API Error - getUserInfo', [
                    'user_id' => $userId,
                    'query_flag' => $queryFlag,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'error' => 'Service temporarily unavailable',
                    'exception' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get event information from Event Management Module
     * IFA: Event Information Service Consumption
     */
    public function getEventInfo($eventId)
    {
        $cacheKey = "event_info_{$eventId}";
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($eventId) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/events/{$eventId}");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'success' => true,
                            'data' => $data['data']
                        ];
                    }
                }

                return [
                    'success' => false,
                    'error' => 'Failed to retrieve event information',
                    'status_code' => $response->status()
                ];

            } catch (\Exception $e) {
                Log::error('External API Error - getEventInfo', [
                    'event_id' => $eventId,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'error' => 'Service temporarily unavailable',
                    'exception' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get venue information from Venue Management Module
     * IFA: Venue Information Service Consumption
     */
    public function getVenueInfo($venueId)
    {
        $cacheKey = "venue_info_{$venueId}";
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($venueId) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/venues/{$venueId}");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'success' => true,
                            'data' => $data['data']
                        ];
                    }
                }

                return [
                    'success' => false,
                    'error' => 'Failed to retrieve venue information',
                    'status_code' => $response->status()
                ];

            } catch (\Exception $e) {
                Log::error('External API Error - getVenueInfo', [
                    'venue_id' => $venueId,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'error' => 'Service temporarily unavailable',
                    'exception' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Submit payment information to Payment Module
     * IFA: Payment Processing Service Consumption
     */
    public function processPayment($paymentData)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/payments/process", $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    return [
                        'success' => true,
                        'data' => $data['data']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Payment processing failed',
                'status_code' => $response->status(),
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('External API Error - processPayment', [
                'payment_data' => $paymentData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Payment service temporarily unavailable',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification through Notification Module
     * IFA: Notification Service Consumption
     */
    public function sendNotification($notificationData)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/notifications/send", $notificationData);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    return [
                        'success' => true,
                        'data' => $data['data']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Failed to send notification',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('External API Error - sendNotification', [
                'notification_data' => $notificationData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Notification service temporarily unavailable',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Get analytics data from Analytics Module
     * IFA: Analytics Service Consumption
     */
    public function getAnalyticsData($analyticsType, $filters = [])
    {
        $cacheKey = "analytics_{$analyticsType}_" . md5(serialize($filters));
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($analyticsType, $filters) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/analytics/{$analyticsType}", $filters);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'success' => true,
                            'data' => $data['data']
                        ];
                    }
                }

                return [
                    'success' => false,
                    'error' => 'Failed to retrieve analytics data',
                    'status_code' => $response->status()
                ];

            } catch (\Exception $e) {
                Log::error('External API Error - getAnalyticsData', [
                    'analytics_type' => $analyticsType,
                    'filters' => $filters,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'error' => 'Analytics service temporarily unavailable',
                    'exception' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Clear cache for specific service
     */
    public function clearCache($service, $identifier = null)
    {
        if ($identifier) {
            $cacheKey = "{$service}_{$identifier}";
            Cache::forget($cacheKey);
        } else {
            // Clear all cache for the service
            $pattern = "{$service}_*";
            // Note: This is a simplified approach. In production, you might want to use a more sophisticated cache clearing mechanism
            Cache::flush();
        }
    }

    /**
     * Set custom timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Set custom cache duration
     */
    public function setCacheDuration($duration)
    {
        $this->cacheDuration = $duration;
        return $this;
    }
}
