<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\UpdateDriverStatusRequest;
use App\Http\Requests\RequestrideRequest;
use App\Http\Requests\DriverNotificationRequest;
use Illuminate\Support\Facades\DB;
class DriverLocationController extends Controller
{

    public function updateStatus(UpdateDriverStatusRequest $request)
    {
        $driver = $request->get('auth_driver');
        $data = [
            'driver_id' => (string) $driver->id,
            'status' => $request->input('status'),
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('services.location_service.url') . '/driver/status', $data);
        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to update driver status',
            ], 500);
        }

        return response()->json([
            'message' => 'Driver status updated successfully',
            'data' => $response->json()
        ]);
    }

    public function nearbyDrivers(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 5);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get(config('services.location_service.url') . '/driver/nearby', [
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius' => $radius,
                ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Failed to fetch nearby drivers',
            ], 500);
        }
        return response()->json([
            'message' => 'Nearby drivers fetched successfully',
            'data' => $response->json()
        ]);
    }

    public function estimateFare(Request $request)
    {
        $pickupLat = $request->input('pickup_lat');
        $pickupLng = $request->input('pickup_lng');
        $dropoffLat = $request->input('dropoffLat');
        $dropoffLng = $request->input('dropoffLng');

        $distance = $this->haversine($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);

        $baseFare = 2.50;
        $costPerKm = 1.20;
        $estimatedFare = $baseFare + ($costPerKm * $distance);
        return response()->json([
            'message' => 'Fare estimated successfully',
            'data' => [
                'estimated_fare' => round($estimatedFare, 2),
                'distance_km' => round($distance, 2)
            ]
        ]);

    }
    public function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    public function requestRide(RequestrideRequest $request)
    {
        $payload = [
            'pickup_lat' => $request->input('pickup_lat'),
            'pickup_lng' => $request->input('pickup_lng'),
            'dropoff_lat' => $request->input('dropoff_lat'),
            'dropoff_lng' => $request->input('dropoff_lng'),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('services.ride_service.url') . '/ride/allocation', $payload);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to request ride',
            ], 500);
        }

        return response()->json([
            'message' => 'Ride requested successfully',
            'data' => $response->json()
        ]);
    }

    public function sendNotification(DriverNotificationRequest $request)
    {
        $driver = $request->get('auth_driver');


        DB::table('driver_web_push_subscriptions')
            ->updateOrInsert(
                ['driver_id' => $driver->id],
                [
                    'subscription' => json_encode($request->subscription),
                    'browser' => $request->browser,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('services.notification_service.url') . '/driver/notify', [
                    'driver_id' => $driver->id,
                    'subscription' => $request->subscription
                ]);
        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to send notification',
            ], 500);
        }

        return response()->json([
            'message' => 'Notification sent successfully',
            'data' => $response->json()
        ]);
    }
}
