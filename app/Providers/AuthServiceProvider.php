<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Project::class => \App\Policies\ProjectPolicy::class,
        \App\Models\DeadlineExtension::class => \App\Policies\DeadlineExtensionPolicy::class,
        \App\Models\ProjectComment::class => \App\Policies\ProjectCommentPolicy::class,
        \App\Models\Evaluation::class => \App\Policies\EvaluationPolicy::class,
        \App\Models\AttendanceRecord::class => \App\Policies\AttendancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
