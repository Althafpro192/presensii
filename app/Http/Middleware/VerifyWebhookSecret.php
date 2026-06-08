<?php

namespace App\Http\Middleware;

use App\Models\N8nWebhook;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to verify n8n webhook secret from X-Webhook-Secret header.
 */
class VerifyWebhookSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('X-Webhook-Secret');

        if (!$secret) {
            return response()->json([
                'status' => 'error',
                'message' => 'Webhook secret is required.',
            ], 401);
        }

        $webhook = N8nWebhook::withoutGlobalScopes()
            ->where('secret_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$webhook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive webhook secret.',
            ], 401);
        }

        $request->attributes->set('school_id', $webhook->school_id);
        $request->attributes->set('webhook', $webhook);

        return $next($request);
    }
}
