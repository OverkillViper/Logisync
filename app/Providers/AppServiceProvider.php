<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Inertia::share([
            'breadcrumbs' => function () {
                $routeName = Route::currentRouteName();

                $breadcrumbMap = [
                    'dashboard' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ],
                    'employees.index' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Employees', 'url' => route('employees.index')],
                    ],
                    'employees.show' => function () {
                        $employee = request()->route('employee');
                        return [
                            ['label' => 'Dashboard', 'url' => route('dashboard')],
                            ['label' => 'Employees', 'url' => route('employees.index')],
                            ['label' => $employee->user->name, 'url' => route('employees.show', $employee->id)],
                        ];
                    },
                    'employees.edit' => function () {
                        $employee = request()->route('employee');
                        return [
                            ['label' => 'Dashboard', 'url' => route('dashboard')],
                            ['label' => 'Employees', 'url' => route('employees.index')],
                            ['label' => $employee->user->name, 'url' => route('employees.show', $employee->id)],
                            ['label' => 'Edit', 'url' => null], // current page
                        ];
                    },
                    'batch.index' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Batches', 'url' => route('batch.index')],
                    ],
                    'batch.show' => function () {
                        $batch = request()->route('batch');
                        return [
                            ['label' => 'Dashboard', 'url' => route('dashboard')],
                            ['label' => 'Batches', 'url' => route('batch.index')],
                            ['label' => $batch->name, 'url' => route('batch.show', $batch->id)],
                        ];
                    },
                    'project.index' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Projects', 'url' => route('project.index')],
                    ],
                    'project.create' =>  [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Projects', 'url' => route('project.index')],
                        ['label' => 'Create Project', 'url' => route('project.create')],
                    ],
                    'project.show' => function () {
                        $project = request()->route('project');
                        return [
                            ['label' => 'Dashboard', 'url' => route('dashboard')],
                            ['label' => 'Projects', 'url' => route('project.index')],
                            ['label' => $project->name, 'url' => route('project.show', $project->id)],
                        ];
                    },
                    'project.edit' => function () {
                        $project = request()->route('project');
                        return [
                            ['label' => 'Dashboard', 'url' => route('dashboard')],
                            ['label' => 'Projects', 'url' => route('project.index')],
                            ['label' => $project->name, 'url' => route('project.show', $project->id)],
                            ['label' => 'Edit', 'url' => null],
                        ];
                    },
                    'evaluation.index' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Evaluation', 'url' => route('evaluation.index')],
                    ],
                    'attendance.index' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Attendance', 'url' => route('attendance.index')],
                    ],
                    'attendance.today' => [
                            ['label' => 'Dashboard', 'url' => route('dashboard')],
                            ['label' => 'Attendance', 'url' => route('attendance.index')],
                        ['label' => 'Today', 'url' => route('attendance.today')],
                    ],
                    'notifications.index'=> [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Notifications', 'url' => route('notifications.index')],
                    ]

                    // add more routes as needed
                ];

                if (isset($breadcrumbMap[$routeName])) {
                    $breadcrumbs = $breadcrumbMap[$routeName];
                    return is_callable($breadcrumbs) ? $breadcrumbs() : $breadcrumbs;
                }

                return []; // default empty array
            },
        ]);
    }
}