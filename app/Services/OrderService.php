<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        // 开启一个数据库事务
        DB::beginTransaction();

        try {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个新订单
            $address['address'] = $address->full_address;
            $address['zip'] = $address->zip;
            $address['contact_name'] = $address->contact_name;
            $address['contact_phone'] = $address->contact_phone;

            $order = new Order();
            $order->address = $address;
            $order->remark = $remark;
            $order->total_amount = 0;
            $order->user()->associate($user);// 订单关联到当前用户
            $order->save();

            // 订单总价
            $totalAmount = 0;

            // 遍历用户提交的sku
            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);
                // make()创建并返回一个未保存的关联模型实例
                // 通过items()关联关系,创建一个OrderItem模型实例,并直接与当前订单模型($order)关联
                // 同时为$item模型实例添加 amount price 两个对象值
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);//等同于$item->product_id=$sku->product_id(仅在belongsTo时有效)
                $item->productSku()->associate($sku);
                $item->save();
                // 订单总金额
                $totalAmount += $sku->price * $data['amount'];
                // 减库存
                if ($sku->decreaseStock($data['amount']) < 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all(); //从$items中创建一个新的集合,并获取对应的键值对
            app(CartService::class)->remove($skuIds);

            DB::commit();

            // 触发队列任务
            dispatch(new CloseOrder($order, config('app.order_ttl')));

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
