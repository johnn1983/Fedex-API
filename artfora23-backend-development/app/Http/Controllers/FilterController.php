<?php

namespace App\Http\Controllers;

use App\Services\FilterService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Filter\SearchFilterRequest;

class FilterController extends Controller
{
    public function search(SearchFilterRequest $request, FilterService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
