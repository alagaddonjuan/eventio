<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\CustomQuestion;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'allow_waitlist' => 'nullable|boolean',
            'auto_approve_waitlist' => 'nullable|boolean',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|exists:custom_questions,id',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|in:text,select',
            'questions.*.options' => 'nullable|string',
            'questions.*.is_required' => 'nullable|boolean',
        ]);

        $event->update([
            'allow_waitlist' => $request->has('allow_waitlist'),
            'auto_approve_waitlist' => $request->has('auto_approve_waitlist'),
        ]);

        $existingQuestionIds = [];

        if ($request->has('questions')) {
            foreach ($request->questions as $questionData) {
                // parse options
                $options = null;
                if ($questionData['type'] === 'select' && !empty($questionData['options'])) {
                    $options = array_map('trim', explode(',', $questionData['options']));
                }

                if (isset($questionData['id'])) {
                    $question = CustomQuestion::where('id', $questionData['id'])->where('event_id', $event->id)->first();
                    if ($question) {
                        $question->update([
                            'question_text' => $questionData['text'],
                            'question_type' => $questionData['type'],
                            'options' => $options,
                            'is_required' => isset($questionData['is_required']),
                        ]);
                        $existingQuestionIds[] = $question->id;
                    }
                } else {
                    $question = $event->customQuestions()->create([
                        'question_text' => $questionData['text'],
                        'question_type' => $questionData['type'],
                        'options' => $options,
                        'is_required' => isset($questionData['is_required']),
                    ]);
                    $existingQuestionIds[] = $question->id;
                }
            }
        }

        // Delete questions that were removed
        $event->customQuestions()->whereNotIn('id', $existingQuestionIds)->delete();

        return redirect()->back()->with('success', 'Registration settings updated successfully.');
    }
}
