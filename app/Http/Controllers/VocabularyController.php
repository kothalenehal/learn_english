<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\VocabularyQuestion;

class VocabularyController extends Controller
{
    public function getVocabularyQuestions(): JsonResponse
    {
        $questions = VocabularyQuestion::all();

        // Return the data as a JSON response
        return response()->json($questions);
    }
}
