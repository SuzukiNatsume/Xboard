<?php
namespace App\Http\Routes\V1;

use App\Http\Controllers\V1\User\CommController;
use App\Http\Controllers\V1\User\CommunityController;
use App\Http\Controllers\V1\User\CommunityNodeController;
use App\Http\Controllers\V1\User\CouponController;
use App\Http\Controllers\V1\User\GiftCardController;
use App\Http\Controllers\V1\User\InviteController;
use App\Http\Controllers\V1\User\KnowledgeController;
use App\Http\Controllers\V1\User\NoticeController;
use App\Http\Controllers\V1\User\OrderController;
use App\Http\Controllers\V1\User\PlanController;
use App\Http\Controllers\V1\User\ServerController;
use App\Http\Controllers\V1\User\StatController;
use App\Http\Controllers\V1\User\TelegramController;
use App\Http\Controllers\V1\User\TicketController;
use App\Http\Controllers\V1\User\UserController;
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
            $router->post('/changePassword', [UserController::class, 'changePassword']);
            $router->post('/update', [UserController::class, 'update']);
            $router->get('/getSubscribe', [UserController::class, 'getSubscribe']);
            $router->get('/getStat', [UserController::class, 'getStat']);
            $router->get('/checkLogin', [UserController::class, 'checkLogin']);
            $router->post('/transfer', [UserController::class, 'transfer']);
            $router->post('/getQuickLoginUrl', [UserController::class, 'getQuickLoginUrl']);
            $router->get('/getActiveSession', [UserController::class, 'getActiveSession']);
            $router->post('/removeActiveSession', [UserController::class, 'removeActiveSession']);
            // Order
            $router->post('/order/save', [OrderController::class, 'save']);
            $router->post('/order/checkout', [OrderController::class, 'checkout']);
            $router->get('/order/check', [OrderController::class, 'check']);
            $router->get('/order/detail', [OrderController::class, 'detail']);
            $router->get('/order/fetch', [OrderController::class, 'fetch']);
            $router->get('/order/getPaymentMethod', [OrderController::class, 'getPaymentMethod']);
            $router->post('/order/cancel', [OrderController::class, 'cancel']);
            // Plan
            $router->get('/plan/fetch', [PlanController::class, 'fetch']);
            // Invite
            $router->get('/invite/save', [InviteController::class, 'save']);
            $router->get('/invite/fetch', [InviteController::class, 'fetch']);
            $router->get('/invite/details', [InviteController::class, 'details']);
            // Notice
            $router->get('/notice/fetch', [NoticeController::class, 'fetch']);
            // Ticket
            $router->post('/ticket/reply', [TicketController::class, 'reply']);
            $router->post('/ticket/close', [TicketController::class, 'close']);
            $router->post('/ticket/save', [TicketController::class, 'save']);
            $router->get('/ticket/fetch', [TicketController::class, 'fetch']);
            $router->post('/ticket/withdraw', [TicketController::class, 'withdraw']);
            // Server
            $router->get('/server/fetch', [ServerController::class, 'fetch']);
            // Coupon
            $router->post('/coupon/check', [CouponController::class, 'check']);
            // Gift Card
            $router->post('/gift-card/check', [GiftCardController::class, 'check']);
            $router->post('/gift-card/redeem', [GiftCardController::class, 'redeem']);
            $router->get('/gift-card/history', [GiftCardController::class, 'history']);
            $router->get('/gift-card/detail', [GiftCardController::class, 'detail']);
            $router->get('/gift-card/types', [GiftCardController::class, 'types']);
            // Telegram
            $router->get('/telegram/getBotInfo', [TelegramController::class, 'getBotInfo']);
            // Comm
            $router->get('/comm/config', [CommController::class, 'config']);
            $router->Post('/comm/getStripePublicKey', [CommController::class, 'getStripePublicKey']);
            // Knowledge
            $router->get('/knowledge/fetch', [KnowledgeController::class, 'fetch']);
            $router->get('/knowledge/getCategory', [KnowledgeController::class, 'getCategory']);
            // Stat
            $router->get('/stat/getTrafficLog', [StatController::class, 'getTrafficLog']);
            // Community
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
