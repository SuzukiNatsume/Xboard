<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\Server as ServerModel;
use App\Models\ServerMachine;
use App\Services\ServerService;
use Closure;
use Illuminate\Http\Request;

/**
 * V2 server middleware: machine-token or server-token auth, no node_type.
 */
class ServerV2
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->filled('machine_id')) {
            $this->authenticateByMachine($request);
        } else {
            $this->authenticateByServerToken($request);
        }

        return $next($request);
    }

    private function authenticateByServerToken(Request $request): void
    {
        $isHandshake = $request->is('*/server/handshake') || $request->is('api/v2/server/handshake');

        $request->validate([
            'token' => ['string', 'required'],
            'node_id' => $isHandshake ? 'nullable' : 'required',
        ]);

        $nodeId = $request->input('node_id');
        if ($nodeId === null || $nodeId === '') {
            if (!hash_equals((string) admin_setting('server_token'), (string) $request->input('token'))) {
                throw new ApiException('Invalid token', 401);
            }
            return;
        }

        $serverInfo = ServerService::getServer($nodeId);
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
    }

    private function authenticateByMachine(Request $request): void
    {
        $isHandshake = $request->is('*/server/handshake') || $request->is('api/v2/server/handshake');

        $request->validate([
            'machine_id' => 'required|integer',
            'token' => 'required|string',
            'node_id' => $isHandshake ? 'nullable|integer' : 'required|integer',
        ]);

        $machine = ServerMachine::where('id', $request->input('machine_id'))
            ->where('token', $request->input('token'))
            ->first();

        if (!$machine) {
            throw new ApiException('Machine not found or invalid token', 401);
        }

        if (!$machine->is_active) {
            throw new ApiException('Machine is disabled', 403);
        }

        $nodeId = (int) $request->input('node_id');
        if ($nodeId > 0) {
            $serverInfo = ServerModel::where('id', $nodeId)
                ->where('machine_id', $machine->id)
                ->where('enabled', true)
                ->first();

            if (!$serverInfo) {
                throw new ApiException('Node not found on this machine');
            }

            $request->attributes->set('node_info', $serverInfo);
        }

        $machine->forceFill(['last_seen_at' => now()->timestamp])->saveQuietly();

        $request->attributes->set('machine_info', $machine);
    }
}
