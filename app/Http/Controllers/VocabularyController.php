<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VocabularyQuestion;
use App\Models\QuestionAnswer;
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
                'user_id' => 'required',
                'answer' => 'nullable|string', 
                'isanswered' => 'required'
            ]);

            $questionAnswer = QuestionAnswer::updateOrCreate(
                [
                    'user_id' => $validatedData['user_id'],
                    'question_id' => $validatedData['question_id']
                ],
                [
                    'answer' => $validatedData['answer'] ?? null,
                    'isanswered' => $validatedData['isanswered']
                ]
            );

            return response()->json([
                'message' => 'Question answer saved successfully',
                'question_answer' => $questionAnswer
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

            $deletedCount = QuestionAnswer::whereHas('vocabularyQuestion', function($q) use ($validatedData) {
                $q->where('question_date', $validatedData['question_date']);
            })->where('user_id', $validatedData['user_id'])->delete();

            return response()->json([
                'message' => 'Quiz questions reset successfully',
                'deleted_answers_count' => $deletedCount
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
