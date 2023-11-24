<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        $userQuery = $request->input('query');
        $yourApiKey = getenv('CHATGPT_API_KEY');
        $client = OpenAI::client($yourApiKey);

        $structuredPrompt = "Analyze the following customer query and return any identified potential product names along with other product attributes in a structured JSON format. Example: [{product_name: 'handbag'}, {color: 'blue'}, {price: {operator: '>', value: '5'}}]. Query: \"$userQuery\"";

        $parseResult = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $structuredPrompt],
            ],
        ]);

        $structuredResponse = $parseResult->choices[0]->message->content;

        Log::debug($structuredResponse);

        $responseArray = json_decode($structuredResponse, true);

        $color = null;
        $priceOperator = null;
        $priceValue = null;
        $size = null;
        $productNames = [];

        if (!empty($responseArray)) {
            foreach ($responseArray as $subArray) {
                if (isset($subArray['color']) || isset($subArray['colour'])) {
                    $color = $subArray['color'] ?? $subArray['colour'] ?? null;
                }
                if (isset($subArray['price'])) {
                    $priceOperator = $subArray['price']['operator'] ?? null;
                    $priceValue = $subArray['price']['value'] ?? null;
                }
                if (isset($subArray['size'])) {
                    $sizeFullForm = $subArray['size'];
                    $size = $this->mapSizeToShortForm($sizeFullForm);
                }
                if (isset($subArray['product_name'])) {
                    $productNames[] = $subArray['product_name'];
                }
            }
        } else {
            return response()->json([
                'reply' => 'Sorry, we could not understand your query. Please try again.',
                'products' => []
            ]);
        }
        $numericPrice = $this->convertToNumericPrice($priceValue);

        $productRecommendations = $this->getProductRecommendations($color, $priceOperator, $size, $numericPrice, $productNames);

        $responseText = $this->formatResponse($productRecommendations);

        return response()->json($responseText);

    }

    protected function getProductRecommendations($color, $priceOperator, $size, $numericPrice, $productNames)
    {
        // Start the query by joining Product and ProductVariant tables
        $query = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id');

        // Apply color filter on ProductVariant
        if ($color) {
            $query->where('product_variants.color', 'like', '%' . $color . '%');
        }

        if ($size) {
            $query->where('product_variants.size', $size);
        }

        // Apply price filter on ProductVariant
        if ($numericPrice) {
            $query->where('product_variants.price', $priceOperator, $numericPrice);
        }

        // Apply product name filter on Product
        if (!empty($productNames)) {
            $query->where(function ($subQuery) use ($productNames) {
                foreach ($productNames as $name) {
                    $subQuery->orWhere('products.name', 'LIKE', '%' . $name . '%')
                        ->orWhere('products.description', 'LIKE', '%' . $name . '%');
                }
            });
        }

        // Select columns from both tables as needed
        return $query->get(['products.name', 'products.description', 'product_variants.color', 'product_variants.size', 'product_variants.price']);
    }

    protected function formatResponse($productRecommendations)
    {
        $replyText = '';
        $productsArray = [];

        if ($productRecommendations->isEmpty()) {
            $replyText = 'Sorry, we could not find any products matching your criteria.';
        } else {
            $replyText = 'Here are the products that match your criteria:';
            $productsArray = $productRecommendations->map(function ($product) {
                return [
                    'name' => $product->name,
                    'description' => $product->description,
                    'color' => $product->color,
                    'size' => $product->size,
                    'price' => $product->price
                ];
            })->all();
        }

        return [
            'reply' => $replyText,
            'products' => $productsArray
        ];
    }

    protected function convertToNumericPrice($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

    protected function mapSizeToShortForm($sizeFullForm)
    {
        $sizeMap = [
            'extra small' => 'XS',
            'small' => 'S',
            'medium' => 'M',
            'large' => 'L',
            'extra large' => 'XL',
        ];

        return $sizeMap[strtolower($sizeFullForm)] ?? null;
    }

}
