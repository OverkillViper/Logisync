<?php

namespace App\Http\Controllers;

use App\Models\DeadlineExtension;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ProjectTrackingStage;
use App\Models\ProjectComment;
use App\Models\ProjectCompletionRequest;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of project.
     */
    public function index(Request $request)
    {
        
        $current_user = $request->user();

        $role = $current_user->employee->role;

        $show_all = $request->input('show_all', $role == 'admin');
        $sort_by = $request->input('sort_by', 'name');
        $status = $request->input('status',['in_progress', 'overdue', 'completed', 'completed_late']);

        $query = Project::with(['assignees.user', 'reviewers.user']);

        if ($role === 'trainee') {
            $query->whereHas('assignees', fn($q) =>
                $q->where('employees.id', $current_user->employee->id)
            );
        } else {
            
            
            if (!$show_all || $show_all === 'false') {
                $query->whereHas('reviewers', fn($q) =>
                    $q->where('employees.id', $current_user->employee->id)
                );
            }
        }

        $today = now()->toDateString();

        $query->when(!empty($status), function ($query) use ($status, $today) {
            $query->where(function ($q) use ($status, $today) {

                if (in_array('in_progress', $status)) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->whereNull('completion_date')
                            ->whereDate('deadline', '>=', $today);
                    });
                }

                if (in_array('overdue', $status)) {
                    $q->orWhere(function ($sub) use ($today) {
                        $sub->whereNull('completion_date')
                            ->whereDate('deadline', '<', $today);
                    });
                }

                if (in_array('completed', $status)) {
                    $q->orWhere(function ($sub) {
                        $sub->whereNotNull('completion_date')
                            ->whereColumn('completion_date', '<=', 'deadline');
                    });
                }

                if (in_array('completed_late', $status)) {
                    $q->orWhere(function ($sub) {
                        $sub->whereNotNull('completion_date')
                            ->whereColumn('completion_date', '>', 'deadline');
                    });
                }

            });
        });

        match ($sort_by) {
            'name'       => $query->orderBy('name'),
            'deadline'   => $query->orderBy('deadline'),
            'created at' => $query->orderBy('created_at'),
            default      => null,
        };

        $projectCount = $query->count();

        $projects = $query->paginate(12)->withQueryString();

        return Inertia::render('Project/Index', [
            'projects' => $projects,
            'show_all' => (bool) $show_all,
            'sort_by'  => $sort_by,
            'filters' => [
                'status' => $status,
            ],
            'totalProject' => $projectCount,
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {

        $this->authorize('create', Project::class);

        $trainees = Employee::with('user')
                    ->whereIn('role', ['trainee'])
                    ->orderBy('name')
                    ->get();

        $trainers = Employee::with('user')
                    ->whereIn('role', ['trainer'])
                    ->orderBy('name')
                    ->get();

        $traineeOptions = $trainees->map(function($trainee) {
            return [
                'label' => $trainee->user->name, // display name
                'value' => $trainee->id,     // value to bind
            ];
        });

        $trainerOptions = $trainers->map(function($trainer) {
            return [
                'label' => $trainer->user->name, // display name
                'value' => $trainer->id,     // value to bind
            ];
        });

        return Inertia::render('Project/Create', [
            'trainees' => $traineeOptions,
            'trainers' => $trainerOptions,
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'start_date'          => 'required|date',
            'deadline'            => 'required|date|after_or_equal:start_date',
            'assignee'            => 'required|array|min:1',
            'reviewer'            => 'required|array|min:1',
            'assign_individually' => 'required|boolean',
        ]);

        $current_user = $request->user();

        try {
            DB::beginTransaction();

            // CASE 1: Assign individually — create one project per assignee
            if ($validated['assign_individually']) {
                foreach ($validated['assignee'] as $assigneeId) {
                    $project = Project::create([
                        'name'                => $validated['name'],
                        'start_date'          => $validated['start_date'],
                        'deadline'            => $validated['deadline'],
                        'created_by'          => $current_user->id,
                        'initial_deadline'    => $validated['deadline'],
                    ]);

                    // Single assignee per project
                    $project->assignees()->attach($assigneeId);

                    $user = User::find($assigneeId);

                    notify(
                        $user,
                        "New Project Assigned",
                        "You have been assigned a project '{$validated['name']}'.",
                        route('project.show', $project->id),
                        'projects'
                    );

                    // Multiple reviewers shared among all projects
                    $project->reviewers()->attach($validated['reviewer']);

                    notify(
                        $validated['reviewer'],
                        "New Project Assigned",
                        "You have been assigned as a reviewer to the project '{$validated['name']}' by '{$current_user->name}'.",
                        route('project.show', $project->id),
                        'projects'
                    );
                }
            }

            // CASE 2: Group assignment — one project, multiple assignees and reviewers
            else {
                $project = Project::create([
                    'name'                => $validated['name'],
                    'start_date'          => $validated['start_date'],
                    'deadline'            => $validated['deadline'],
                    'created_by'          => $current_user->id,
                    'initial_deadline'    => $validated['deadline'],
                ]);

                $project->assignees()->attach($validated['assignee']);

                notify(
                    $validated['assignee'],
                    "New Project Assigned",
                    "You have been assigned a project '{$validated['name']}'.",
                    route('project.show', $project->id),
                    'projects'
                );

                $project->reviewers()->attach($validated['reviewer']);

                notify(
                    $validated['reviewer'],
                    "New Project Assigned",
                    "You have been assigned as a reviewer to the project '{$validated['name']}' by '{$current_user->name}'.",
                    route('project.show', $project->id),
                    'projects'
                );
            }

            DB::commit();

            return redirect()
                ->route('project.show', $project->id)
                ->with('success', 'Project(s) created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', "An error occurred while creating the project: {$e->getMessage()}");
        }
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'creator.employee',
            'assignees.user',
            'reviewers.user',
            'deadlineExtensions.requester',
            'deadlineExtensions.approver',
            'trackingStages.entries',
            'comments.user.employee',
            'completionRequest',
        ]);

        $tracking_stages = $project->trackingStages()
                                   ->with(['expectedEntries', 'actualEntries'])
                                   ->orderBy('order')
                                   ->get()
                                   ->map(function ($stage) {
                                       return [
                                           'id'      => $stage->id,
                                           'name'    => $stage->name,
                                           'order'   => $stage->order,
                                           'expected_entries' => $stage->expectedEntries->map(fn($e) => [
                                               'date'  => $e->date,
                                               'value' => $e->value,
                                           ]),
                                           'actual_entries' => $stage->actualEntries->map(fn($e) => [
                                               'date'  => $e->date,
                                               'value' => $e->value,
                                           ]),
                                       ];
                                   });

        return Inertia::render('Project/ProjectDetails', [
            'project' => $project,
            'trackingStages' => $tracking_stages
        ]);
    }

    public function edit(Project $project)
    {

        $this->authorize('update', $project);

        $project->load([
            'assignees.user',
            'reviewers.user',
        ]);

        // Get the IDs of all already assigned users (both assignees and reviewers)
        $assignedUserIds = $project->assignees->pluck('user_id')
            ->merge($project->reviewers->pluck('user_id'))
            ->unique();

        // Trainees not already assigned
        $trainees = Employee::with('user')
            ->where('role', 'trainee')
            ->whereNotIn('user_id', $assignedUserIds)
            ->orderBy('name')
            ->get();

        // Trainers not already reviewers
        $trainers = Employee::with('user')
            ->where('role', 'trainer')
            ->whereNotIn('user_id', $assignedUserIds)
            ->orderBy('name')
            ->get();

        // Format for frontend dropdowns
        $traineeOptions = $trainees->map(fn($trainee) => [
            'label' => $trainee->user->name,
            'value' => $trainee->id,
        ]);

        $trainerOptions = $trainers->map(fn($trainer) => [
            'label' => $trainer->user->name,
            'value' => $trainer->id,
        ]);

        return Inertia::render('Project/Edit', [
            'project' => $project,
            'trainees' => $traineeOptions,
            'trainers' => $trainerOptions,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline'   => 'required|date|after_or_equal:start_date',
        ]);

        $validated['start_date'] = $validated['start_date'] ? now()->parse($validated['start_date'])->timezone(config('app.timezone'))->toDateString() : null;
        $validated['deadline']   = $validated['deadline'] ? now()->parse($validated['deadline'])->timezone(config('app.timezone'))->toDateString() : null;

        $project->update($validated);

        $changed = [
            'Name'       => $project->wasChanged('name') ? 'Name' : null,
            'Start Date' => $project->wasChanged('start_date') ? 'Start Date' : null,
            'Deadline'   => $project->wasChanged('deadline') ? 'Deadline' : null,
        ];

        notify(
            $project->assignees()->pluck('user_id')
                ->merge($project->reviewers->pluck('user_id'))
                ->unique()->toArray(),
            "Project Updated",
            "Project '{$project->name}' has been updated. Changes: " . implode(', ', array_filter(array_map(function ($field, $changed) {
                return $changed ? $field : null;
            }, array_keys($changed), $changed))) . ".",
            route('project.show', $project->id),
            'projects'
        );

        return redirect()
            ->route('project.show', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        notify(
            $project->assignees()->pluck('user_id')
                ->merge($project->reviewers->pluck('user_id'))
                ->unique()->toArray(),
            "Project Deleted",
            "Project '{$project->name}' has been deleted by '{$request->user()->name}'.",
            '#',
            'projects'
        );

        return redirect()
            ->route('project.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function requestExtension(Request $request, Project $project)
    {
        $validated = $request->validate([
            'new_deadline' => 'required|date|after_or_equal:today',
            'reason'       => 'required|string|max:500',
        ]);

        $employee = $request->user()->employee;

        $pending = $project->deadlineExtensions()
            ->where('requested_by', $employee->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return back()->with('error', 'You already have a pending extension request.');
        }

        $project->deadlineExtensions()->create([
            'requested_by' => $employee->id,
            'status'       => 'pending',
            'reason'       => $validated['reason'],
            'new_deadline' => $validated['new_deadline'],
        ]);

        notify(
            $project->reviewers()->pluck('user_id')->unique()->toArray(),
            "Deadline Extension Requested",
            "{$request->user()->name} has requested a deadline extension for project '{$project->name}'.",
            route('project.show', $project->id),
            'projects'
        );

        return back()->with('success', 'Extension request submitted.');
    }

    public function approveRequest(DeadlineExtension $extension)
    {
        $this->authorize('approve', $extension);

        $extension->update([
            'status'      => 'approved',
            'approved_by' => auth()->user()->id,
        ]);

        $extension->project->update([
            'deadline' => $extension->new_deadline,
        ]);

        notify(
            $extension->requester,
            "Extension Request Approved",
            "Your deadline extension request for project '{$extension->project->name}' has been approved.",
            route('project.show', $extension->project->id),
            'projects'
        );

        return back()->with('success', 'Extension request approved.');
    }

    public function declineRequest(DeadlineExtension $extension)
    {
        $this->authorize('approve', $extension);

        $extension->update([
            'status'      => 'declined',
            'approved_by' => auth()->user()->id,
        ]);

        notify(
            $extension->requester,
            "Extension Request Declined",
            "Your deadline extension request for project '{$extension->project->name}' has been declined.",
            route('project.show', $extension->project->id),
            'projects'
        );

        return back()->with('success', 'Extension request declined.');
    }

    public function cancelRequest(DeadlineExtension $extension)
    {
        $this->authorize('cancel', $extension);

        $extension->delete();

        return back()->with('success', 'Extension request cancelled.');
    }

    public function removeAssignee(Project $project, Employee $employee)
    {
        $project->assignees()->detach($employee->id);

        notify(
            $employee->user,
            "Removed from Project",
            "You have been removed as an assignee from project '{$project->name}'.",
            '#',
            'projects'
        );

        return redirect()->route('project.edit', $project->id)->with('success', 'Assignee removed successfully.');
    }

    public function removeReviewer(Project $project, Employee $employee)
    {
        $project->reviewers()->detach($employee->id);

        notify(
            $employee->user_id,
            "Removed from Project",
            "You have been removed as a reviewer from project '{$project->name}'.",
            '#',
            'projects'
        );

        return redirect()->route('project.edit', $project->id)->with('success', 'Reviewer removed successfully.');
    }

    public function addAssignees(Request $request, Project $project)
    {
        $validated = $request->validate([
            'assignees' => 'required|array',
            'assignees.*' => 'exists:employees,id',
        ]);

        // Get employee IDs for those users
        $employeeIds = Employee::whereIn('id', $validated['assignees'])
            ->where('role', 'trainee')
            ->pluck('id')
            ->toArray();

        $users = User::whereIn('id', $validated['assignees'])->pluck('id')->toArray();

        // Sync without detaching others
        $project->assignees()->syncWithoutDetaching($employeeIds);

        notify(
            $users,
            "New Project Assigned",
            "You have been assigned to project '{$project->name}' by {$request->user()->name}.",
            route('project.show', $project->id),
            'projects'
        );

        return redirect()->route('project.edit', $project->id)->with('success', 'Assignees added successfully.');
    }

    public function addReviewers(Request $request, Project $project)
    {
        $validated = $request->validate([
            'reviewers' => 'required|array',
            'reviewers.*' => 'exists:employees,id',
        ]);

        // Get employee IDs for those users
        $employeeIds = Employee::whereIn('id', $validated['reviewers'])
            ->where('role', 'trainer')
            ->pluck('id')
            ->toArray();

        // Sync without detaching others
        $project->reviewers()->syncWithoutDetaching($employeeIds);

        notify(
            $project->reviewers()->pluck('user_id')->unique()->toArray(),
            "New Project Assigned",
            "You have been assigned as a reviewer to project '{$project->name}' by {$request->user()->name}.",
            route('project.show', $project->id),
            'projects'
        );

        return redirect()->route('project.edit', $project->id)->with('success', 'Reviewers added successfully.');
    }

    public function storeStage(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Get the last order value for this project's stages
        $lastOrder = $project->trackingStages()->max('order') ?? 0;

        // Create a new stage with order = lastOrder + 1
        ProjectTrackingStage::create([
            'project_id' => $project->id,
            'name' => $request->name,
            'order' => $lastOrder + 1,
        ]);

        return back()->with('success', 'Stage added successfully.');
    }

    // ✅ Delete stage
    public function deleteStage(ProjectTrackingStage $stage)
    {
        $stage->delete();

        return back()->with('success', 'Stage deleted successfully.');
    }

    // ✅ Store or update entry
    public function updateAll(Request $request)
    {
        $type = $request->input('type', 'expected');

        foreach ($request->stages as $stageData) {
            $stage = ProjectTrackingStage::findOrFail($stageData['stage_id']);

            foreach ($stageData['entries'] as $entry) {
                if ($entry['value'] == 0) {
                    $stage->entries()
                        ->where('type', $type)
                        ->where('date', $entry['date'])
                        ->delete();
                } else {
                    $stage->entries()->updateOrCreate(
                        [
                            'type' => $type,
                            'date' => $entry['date'],
                        ],
                        ['value' => $entry['value']]
                    );
                }
            }
        }

        return back()->with('success', ucfirst($type) . ' tracking data saved!');
    }

    public function moveStageUp(ProjectTrackingStage $stage)
    {
        $previousStage = ProjectTrackingStage::where('project_id', $stage->project_id)
            ->where('order', '<', $stage->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousStage) {
            // Swap orders
            [$stage->order, $previousStage->order] = [$previousStage->order, $stage->order];
            $stage->save();
            $previousStage->save();
        }

        return back();
    }

    public function moveStageDown(ProjectTrackingStage $stage)
    {
        $nextStage = ProjectTrackingStage::where('project_id', $stage->project_id)
            ->where('order', '>', $stage->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextStage) {
            // Swap orders
            [$stage->order, $nextStage->order] = [$nextStage->order, $stage->order];
            $stage->save();
            $nextStage->save();
        }

        return back();
    }

    public function storeComment(Request $request, Project $project)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
      
        $project->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        notify(
            $project->assignees()->pluck('user_id')
                ->merge($project->reviewers->pluck('user_id'))
                ->unique()->toArray(),
            "New comment on Project",
            "'{$request->user()->name}' added a new comment to project '{$project->name}'.",
            '#',
            'projects'
        );

        return back();
    }

    public function updateComment(Request $request, ProjectComment $comment)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment updated');
    }

    public function destroyComment(ProjectComment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted');
    }

    public function requestProjectCompletion(Request $request, Project $project)
    {
        $validated = $request->validate([
            'completion_date' => 'required|date'
        ]);

        // If the project already has a completion request, prevent duplicate requests
        if ($project->completionRequest()->exists()) {
            return back()->with('error', 'A completion request already requested for this project.');
        }

        $project->completionRequest()->create([
            'completion_date' => $validated['completion_date'] ? now()->parse($validated['completion_date'])->timezone(config('app.timezone'))->toDateString() : null,
        ]);

        notify(
            $project->reviewers()->pluck('user_id')->unique()->toArray(),
            "Project Completion Requested",
            "{$request->user()->name} has requested project completion for project '{$project->name}'.",
            route('project.show', $project->id),
            'projects'
        );

        return back()->with('success', 'Project completion requested successfully.');
    }

    public function cancelProjectCompletionRequest(ProjectCompletionRequest $completionRequest)
    {
        $completionRequest->delete();

        return back()->with('success', 'Project completion requested cancelled.');
    }

    public function declineProjectCompletionRequest(ProjectCompletionRequest $completionRequest)
    {
        notify(
            $completionRequest->project->assignees()->pluck('user_id')
                ->unique()->toArray(),
            "Project Completion Request Declined",
            "The project completion request for project '{$completionRequest->project->name}' has been declined.",
            route('project.show', $completionRequest->project->id),
            'projects'
        );
        
        $completionRequest->delete();

        return back()->with('success', 'Project completion requested declined.');
    }

    public function approveProjectCompletionRequest(ProjectCompletionRequest $completionRequest)
    {
        
        $completionRequest->project->update([
            'completion_date' => $completionRequest->completion_date,
        ]);

        notify(
            $completionRequest->project->assignees()->pluck('user_id')
                ->unique()->toArray(),
            "Project Completion Request Approved",
            "The project completion request for project '{$completionRequest->project->name}' has been approved.",
            route('project.show', $completionRequest->project->id),
            'projects'
        );

        $completionRequest->delete();

        return back()->with('success', 'Project completion requested approved.');
    }

    public function cancelProjectCompletion(Request $request, Project $project)
    {
        $project->update([
            'completion_date' => null,
        ]);

        notify(
            $project->assignees()->pluck('user_id')
                ->unique()->toArray(),
            "Project Completion Cancelled",
            "The project completion for project '{$project->name}' has been cancelled by '{$request->user()->name}'.",
            route('project.show', $project->id),
            'projects'
        );
    }
}
