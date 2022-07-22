<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address_id = $request->input('address_id');
        $remark = $request->input('remark');
        $items = $request->input('items');

        $address = UserAddress::find($address_id);
        if (!$address) {
            throw new InvalidRequestException('用户地址为空');
        }

        $order = $orderService->store($user, $address, $remark, $items);

        return $order;
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        $order_info = $order->load(['items.productSku', 'items.product']);
        //dd($order_info->address['address']);
        return view('orders.show', [
            'order' => $order_info,
        ]);
    }

    /**
     * 确认收货
     *
     */
    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }
        // 更新发货状态为已收到
        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED,
        ]);

        return $order;
    }
}
