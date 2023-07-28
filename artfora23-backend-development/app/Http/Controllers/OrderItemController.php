<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderItems\CreateOrderItemRequest;
use App\Http\Requests\OrderItems\DeleteOrderItemRequest;
use App\Services\OrderItemService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderItemController extends Controller
{   public function read(Request $request, OrderItemService $service)
    {
        $result = $service->read();

        return response()->json($result);
    }
    public function create(CreateOrderItemRequest $request, OrderItemService $service)
    {
        $data = $request->onlyValidated();

        // $data['user_id'] = $request->user()->id;

        $result = $service->create($data);

        return response()->json($result, Response::HTTP_CREATED);
    }
    public function delete(DeleteOrderItemRequest $request, OrderItemService $service, $id)
    {
       
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }
}
