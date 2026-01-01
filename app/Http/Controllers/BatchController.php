<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BatchController extends Controller
{   
    /**
     * Display a listing of batch.
     */
    public function index()
    {
        $batches = Batch::with('trainees', 'employees')->orderBy('id', 'desc')->get();

        // This will trigger the accessor dynamically
        foreach ($batches as $batch) {
            $batch->trainers = $batch->trainers; 
        }

        return Inertia::render('Batch/Index', [
            'batches' => $batches,
        ]);
    }

    public function show(Batch $batch)
    {
        $batch->load(['trainees.user']); // Eager load related user data

        $batch->trainers = $batch->trainers;

        return Inertia::render('Batch/BatchDetails', [
            'batch' => $batch,
        ]);
    }

    /**
     * Show the form for creating a new batch.
     */
    public function create()
    {
        return Inertia::render('Batch/Create');
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        Batch::create($validated);

        return redirect()->route('batch.index')->with('success', 'Batch created successfully.');
    }

    /**
     * Show the form for editing the specified batch.
     */
    public function edit(Batch $batch)
    {
        return Inertia::render('Batch/Edit', [
            'batch' => $batch,
        ]);
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $batch->update($validated);

        return redirect()->route('batch.index')->with('success', 'Batch updated successfully.');
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()->back()->with('success', 'Batch deleted.');
    }
}
