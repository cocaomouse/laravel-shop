<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Carbon\Carbon;
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
        // 优惠券
        $coupon = null;
        // 优惠码
        $code = $request->input('coupon_code');

        $address = UserAddress::find($address_id);
        if (!$address) {
            throw new InvalidRequestException('用户地址为空');
        }

        // 如果用户提交了优惠券
        if ($code) {
            $coupon = CouponCode::query()->where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }

        $order = $orderService->store($user, $address, $remark, $items, $coupon);

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

    /**
     * 显示评价页面
     *
     */
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可评价');
        }
        // 使用 load 方法加载关联数据
        return view('orders.review', [
            'order' => $order->load([
                'items.productSku',
                'items.product',
            ]),
        ]);
    }

    /**
     * 评价功能
     *
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update([
                'reviewed' => true,
            ]);
        });
        //
        event(new OrderReviewed($order));

        return redirect()->back();
    }

    /**
     * 提交退款申请
     *
     * @return object
     */
    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $order->update([
           'refund_status' => Order::REFUND_STATUS_APPLIED,
           'extra' => $extra,
        ]);

        return $order;
    }
}
