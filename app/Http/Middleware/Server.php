<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\Server as ServerModel;
use App\Services\ServerService;
use Closure;
use Illuminate\Http\Request;

/**
 * @deprecated use {@see ServerV2}
 */
class Server
{
    public function handle(Request $request, Closure $next, ?string $nodeType = null)
    {
        $request->validate([
            'token' => ['string', 'required'],
            'node_id' => 'required',
            'node_type' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === 'v2node') {
                        $value = null;
                    }
                    if (!ServerModel::isValidType($value)) {
                        $fail('Invalid node type specified');
                        return;
                    }
                    $request->merge([$attribute => ServerModel::normalizeType($value)]);
                },
            ]
        ]);

        $nodeType = $request->input('node_type', $nodeType);
        $normalizedNodeType = ServerModel::normalizeType($nodeType);
        $serverInfo = ServerService::getServer(
            $request->input('node_id'),
            $normalizedNodeType
        );
        if (!$serverInfo) {
            throw new ApiException('Server does not exist');
        }
        if (!$serverInfo->enabled) {
            throw new ApiException('Server is disabled', 403);
        }

        $providedToken = (string) $request->input('token');
        $isAdminToken = hash_equals((string) admin_setting('server_token'), $providedToken);
        if ($serverInfo->maintenance_mode === 'user') {
            $expectedHash = (string) $serverInfo->getRawOriginal('community_token_hash');
            $isNodeToken = $expectedHash
                && hash_equals($expectedHash, hash('sha256', $providedToken));
            if (!$isAdminToken && !$isNodeToken) {
                throw new ApiException('Invalid node token', 401);
            }
        } elseif (!$isAdminToken) {
            throw new ApiException('Invalid token', 401);
        }

        $request->attributes->set('node_info', $serverInfo);

        return $next($request);
    }
}
