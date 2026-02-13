<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Chunk;
use App\Models\Document;
use App\Services\product\ProductReindexService;
use Illuminate\Http\Request;

class ChunkController extends Controller
{
    public function __construct(protected ProductReindexService $productReindexService) {}

    public function indexProducts(Request $request, string $siteId)
    {
        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 20);

        $paginator = $this->productReindexService->listProducts($siteId, $page, $perPage);

        return response()->json($paginator);
    }

    public function reindexProduct(Request $request, string $documentId, int $productIndex)
    {
        $document = Document::findOrFail($documentId);
        $productData = $request->input('product'); // productData envoyé par Angular

        $result = $this->productReindexService->reindexProduct($document, $productIndex, $productData);

        return response()->json($result);
    }
}
