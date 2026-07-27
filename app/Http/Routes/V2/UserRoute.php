<?php
namespace App\Http\Routes\V2;

use App\Http\Controllers\V1\User\UserController;
use App\Http\Controllers\V1\User\CommunityController;
use App\Http\Controllers\V1\User\CommunityNodeController;
use Illuminate\Contracts\Routing\Registrar;

class UserRoute
{
    public function map(Registrar $router)
    {
        $router->group([
            'prefix' => 'user',
            'middleware' => 'user'
        ], function ($router) {
            // User
            $router->get('/resetSecurity', [UserController::class, 'resetSecurity']);
            $router->get('/info', [UserController::class, 'info']);
            $router->get('/community/overview', [CommunityController::class, 'overview']);
            $router->get('/community/mine', [CommunityController::class, 'mine']);
            $router->post('/community/contributions', [CommunityController::class, 'store'])
                ->middleware('throttle:10,1');
            $router->post('/community/contributions/{contribution}/archive', [CommunityController::class, 'archive']);
            $router->post('/community/contributions/{contribution}/quota', [CommunityController::class, 'updateQuota']);
            $router->get('/community/nodes', [CommunityNodeController::class, 'index']);
            $router->post('/community/nodes', [CommunityNodeController::class, 'store'])
                ->middleware('throttle:20,1');
            $router->post('/community/nodes/{node}/update', [CommunityNodeController::class, 'update'])
                ->middleware('throttle:30,1');
            $router->post('/community/nodes/{node}/enabled', [CommunityNodeController::class, 'setEnabled']);
            $router->post('/community/nodes/{node}/quota', [CommunityNodeController::class, 'updateQuota']);
            $router->post('/community/nodes/{node}/reset-token', [CommunityNodeController::class, 'resetToken'])
                ->middleware('throttle:5,1');
            $router->post('/community/nodes/{node}/delete', [CommunityNodeController::class, 'destroy']);
        });
    }
}
