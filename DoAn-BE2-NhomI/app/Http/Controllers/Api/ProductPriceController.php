<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductPriceService;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    public function __construct(private ProductPriceService $priceService)
    {
    }

    public function sync(Request $request)
    {
        $ids = $request->input('ids', []);

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $productIds = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));

        return response()->json($this->priceService->buildSyncPayload($productIds));
    }
}
