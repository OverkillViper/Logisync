<?php

namespace App\Http\Controllers;

use App\Models\EvaluationCriteria;
use Illuminate\Http\Request;
use App\Models\Batch;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\EvaluationRemarks;

class EvaluationController extends Controller
{

    use AuthorizesRequests;

    public function index(Request $request)
    {
        $batches = Batch::whereHas('trainees')->get();
        $batchesOptions = $batches->map(fn($batch) => [
            'label' => $batch->name,
            'value' => $batch->id,
        ]);

        return Inertia::render('Evaluation/Index', [
            'batches' => $batchesOptions,
            'criterias' => EvaluationCriteria::all(),
        ]);
    }

    public function getEvaluationsForBatch($batchId)
    {
        $batch = Batch::with('trainees.user')->findOrFail($batchId);

        $evaluations = Evaluation::where('batch_id', $batchId)
            ->latest()
            ->get(['id', 'title', 'start_date', 'end_date'])
            ->map(fn ($e) => [
                'label' => $e->title . ' (' .
                    (is_string($e->start_date) ? $e->start_date : $e->start_date->format('M d')) .
                    ' - ' .
                    (is_string($e->end_date) ? $e->end_date : $e->end_date->format('M d')) .
                    ')',
                'value' => $e->id,
            ]);

        return response()->json([
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'trainees' => $batch->trainees->map(fn ($t) => [
                    'id' => $t->id,
                    'user' => [
                        'id' => $t->user->id,
                        'name' => $t->user->name,
                    ],
                    'profile_picture' => $t->profile_picture,
                ]),
            ],
            'evaluations' => $evaluations,
        ]);
    }

    public function getEvaluationDetails($batchId, $evaluationId)
    {
        $evaluation = Evaluation::find($evaluationId);
        $batch = Batch::with('trainees.user')->findOrFail($batchId);
        $criterias = EvaluationCriteria::all();
        $scores = EvaluationScore::where('evaluation_id', $evaluationId)->get();

        $remarks = EvaluationRemarks::where('evaluation_id', $evaluationId)->pluck('remarks', 'trainee_id');

        $scoreMatrix = [];
        foreach ($scores as $score) {
            $scoreMatrix[$score->criteria_id][$score->trainee_id] = $score->score;
        }

        return response()->json([
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'trainees' => $batch->trainees->map(fn ($t) => [
                    'id' => $t->id,
                    'user' => [
                        'id' => $t->user->id,
                        'name' => $t->user->name,
                    ],
                    'profile_picture' => $t->profile_picture,
                ]),
            ],
            'criterias'  => $criterias,
            'scores'     => $scoreMatrix,
            'remarks'    => $remarks,
            'evaluation' => $evaluation,
        ]);
    }

    public function storeEvaluationScore(Request $request)
    {
        // Validate: scores is an array, each scores.* may be an array, each scores.*.* numeric or nullable
        $validated = $request->validate([
            'evaluation_id'        => 'required|exists:evaluations,id',
            'scores'               => 'required|array',
            'scores.*'             => 'nullable|array',
            'scores.*.*'           => 'nullable|numeric',
        ]);

        foreach ($validated['scores'] as $criteriaId => $traineeScores) {
            // skip if not an array (safety)
            if (!is_array($traineeScores)) {
                continue;
            }

            foreach ($traineeScores as $traineeId => $scoreValue) {
                // if score is null, skip (don't create a record)
                if ($scoreValue === null) {
                    continue;
                }

                EvaluationScore::updateOrCreate(
                    [
                        'evaluation_id' => $validated['evaluation_id'],
                        'criteria_id'   => $criteriaId,
                        'trainee_id'    => $traineeId,
                    ],
                    ['score' => $scoreValue]
                );
            }
        }

        return redirect()->back()
            ->with('success', 'Scores saved successfully');
    }


    public function criteriaIndex(Request $request)
    {
        $this->authorize('viewCriteria', Evaluation::class);

        $criterias = EvaluationCriteria::oldest()->get();

        return Inertia::render('Evaluation/CriteriaIndex', [
            'criterias' => $criterias
        ]);
    }

    public function storeCriteria(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:technical,non-technical',
        ]);

        EvaluationCriteria::create($validated);

        return redirect()->back()
            ->with('success', 'Evaluation criteria created successfully');
    }

    public function updateCriteria(Request $request, EvaluationCriteria $criteria)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:technical,non-technical',
        ]);

        $criteria->update($validated);

        return redirect()->back()
            ->with('success', 'Evaluation criteria updated successfully');
    }

    public function deleteCriteria(EvaluationCriteria $criteria)
    {
        $criteria->delete();

        return redirect()->back()
            ->with('success', 'Evaluation criteria removed successfully');
    }

    public function storeEvaluation(Request $request)
    {
        $request->validate([
            'batch_id'   => 'required|exists:batches,id',
            'title'      => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        Evaluation::create([
            'batch_id' => $request->batch_id,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->back()
            ->with('success', 'Evaluation created successfully');
    }

    public function storeRemarks(Request $request)
    {
        $validated = $request->validate([
            'evaluation_id' => 'required|exists:evaluations,id',
            'remarks' => 'required|array',
        ]);

        foreach ($validated['remarks'] as $traineeId => $text) {
            if (!$text) continue; // skip empty remarks

            EvaluationRemarks::updateOrCreate(
                [
                    'evaluation_id' => $validated['evaluation_id'],
                    'trainee_id' => $traineeId,
                ],
                ['remarks' => $text]
            );
        }


        return back()->with('success', 'Remarks saved successfully');
    }

    public function deleteEvaluation(Evaluation $evaluation)
    {
        $evaluation->delete();

        return back()->with('success', 'Evaluation deleted successfully');
    }

    public function updateEvaluation(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $evaluation->update($validated);

        return back()->with('success', 'Evaluation updated successfully');
    }
}
