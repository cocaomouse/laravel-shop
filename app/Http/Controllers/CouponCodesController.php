<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    /**
     * 检查优惠券
     *
     * @param $code
     * @return $record
     */
    public function show($code, Request $request)
    {
        $user = $request->user();
        $record = CouponCode::where('code', $code)->first();
        // 判断优惠券是否存在
        if (!$record) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        $record->checkAvailable($user);

        return $record;
    }
}
