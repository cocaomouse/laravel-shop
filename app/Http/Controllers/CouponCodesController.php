<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;

class CouponCodesController extends Controller
{
    /**
     * 检查优惠券
     *
     * @param $code
     * @return $recored
     */
    public function show($code)
    {
        $recored = CouponCode::where('code', $code)->first();
        // 判断优惠券是否存在
        if (!$recored) {
            abort(404);
        }

        // 如果优惠券没有启用，则等同于优惠券不存在
        if (!$recored->enabled) {
            abort(404);
        }

        if ($recored->total - $recored->used <= 0) {
            return response()->json(['msg' => '该优惠券已被兑完'], 403);
        }

        if ($recored->not_before && $recored->not_before->gt(Carbon::now())) {
            return response()->json(['msg' => '该优惠券现在还不能使用'], 403);
        }

        if ($recored->not_after && $recored->not_after->lt(Carbon::now())) {
            return response()->json(['msg' => '该优惠券已过期'], 403);
        }

        return $recored;
    }
}
