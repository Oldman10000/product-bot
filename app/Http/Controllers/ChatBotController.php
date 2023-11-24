<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        $userQuery = $request->input('query');

        // Placeholder response
        $responseText = "You asked about: " . $userQuery;

        // TODO: Integrate with ChatGPT or process the query based on your product data

        return response()->json(['reply' => $responseText]);
    }
}
