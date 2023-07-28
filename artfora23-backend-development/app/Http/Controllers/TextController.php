<?php

namespace App\Http\Controllers;

use App\Services\TextService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Text\SearchTextRequest;

class TextController extends Controller
{
    public function search(SearchTextRequest $request, TextService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
