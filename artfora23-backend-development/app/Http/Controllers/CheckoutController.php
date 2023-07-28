<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use App\Http\Requests\Checkouts\CheckoutRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request, CheckoutService $service)
    {
        $id = $request->user()->id;
        $data = $request->onlyValidated();
        $result = $service->checkout($data, $id);

        return response()->json($result);
    }
}
