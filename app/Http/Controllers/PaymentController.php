<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Notifications\OrderPaidNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * 支付宝支付订单
     * @param Order $order
     * @param Request $request
     * @return mixed
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     */
    public function payByAlipay(Order $order)
    {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);
        // 订单已支付或者已关闭
        if ($order->paid_at || $order->close) {
            throw new InvalidRequestException('订单状态错误');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject' => '支付 Laravel Shop 的订单：' . $order->no, // 订单标题
        ]);
    }

    /**
     * 前端回调
     *
     */
    public function alipayReturn(Request $request)
    {
        // 校验提交的参数是否合法
        try {
            $data = $request->all();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    /**
     * 服务端回调
     *
     */
    public function alipayNotify(Request $request, OrderPayment $orderPayment)
    {
        // 校验输入参数
        $data = app('alipay')->callback();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::query()->where('no', $data->out_trade_no)->first();
        if (!$order) {
            throw new InvalidRequestException('当前订单不存在');
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }
        // 修改订单数据
        $order->update([
            'paid_at' => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no' => $data->trade_no, // 支付宝订单号
        ]);
        // 添加支付数据
        $orderPayment->create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'payment_method' => 'alipay',
            'payment_verify' => $data,
        ]);

        Log::debug('Alipay notify', json_decode(json_encode($data), true));

        $this->afterPaid($order);

        return app('alipay')->success();
    }

    /**
     * 支付完成后触发的事件
     *
     */
    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }

    /*public function takeEmail()
    {
        $order = Order::query()->where('id',72)->first();
        // 调用 notify 方法来发送通知
        $order->user->notify(new OrderPaidNotification($order));
    }*/
}
