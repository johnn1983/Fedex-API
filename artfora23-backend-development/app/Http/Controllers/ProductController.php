<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Requests\Products\DeleteProductRequest;
use App\Http\Requests\Products\GetProductRequest;
use App\Http\Requests\Products\SearchProductsRequest;
use App\Services\ProductService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create(CreateProductRequest $request, ProductService $service)
    {
        $data = $request->onlyValidated();

        $data['user_id'] = $request->user()->id;

        $result = $service->create($data);

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function get(GetProductRequest $request, ProductService $service, $id)
    {
        $result = $service
            ->with($request->input('with', []))
            ->withCount($request->input('with_count', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchProductsRequest $request, ProductService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function update(UpdateProductRequest $request, ProductService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response()->json([ 'status' => 'Success' ]);
    }

    public function delete(DeleteProductRequest $request, ProductService $service, $id)
    {
        if ($request->input('force')) {
            $service->force();
        }

        $product = $service
            ->with('media')
            ->find($id);

        foreach ($product->media as $key => $media) {
            Storage::delete($media['link']);
        }

        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }
}
