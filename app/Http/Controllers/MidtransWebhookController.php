<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransService $midtransService): JsonResponse
    {
        $payload = $request->all();
        $result = $midtransService->handleNotification($payload);

        if (($result['status'] ?? '') === 'error') {
            return response()->json($result, 400);
        }

        return response()->json($result, 200);
    }
}
