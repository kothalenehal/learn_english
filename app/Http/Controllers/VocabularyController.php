<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VocabularyQuestion;
use Illuminate\Validation\ValidationException;

class VocabularyController extends Controller
{
    public function getVocabularyQuestions(Request $request): JsonResponse
    {
        $query = VocabularyQuestion::query();

        if ($request->has('question_date')) {
            $query->where('question_date', $request->input('question_date'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('difficulty_level')) {
            $query->where('difficulty_level', $request->input('difficulty_level'));
        }

        $questions = $query->get();

        return response()->json($questions);
    }

    public function updateQuestionStatus(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'question_id' => 'required|exists:vocabulary_questions,id',
                'isanswered' => 'required|in:0,1',
                'user_id' => 'required'
            ]);

            $question = VocabularyQuestion::findOrFail($validatedData['question_id']);

            $question->isanswered = $validatedData['isanswered'];
            $question->user_id = $validatedData['user_id'];
            $question->save();

            return response()->json([
                'message' => 'Question status updated successfully',
                'question' => $question
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetQuizQuestions(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'question_date' => 'required|date_format:Y-m-d'
            ]);

            $updatedCount = VocabularyQuestion::where('user_id', $validatedData['user_id'])
                ->where('question_date', $validatedData['question_date'])
                ->update(['isanswered' => 0]);

            return response()->json([
                'message' => 'Quiz questions reset successfully',
                'updated_questions_count' => $updatedCount
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
