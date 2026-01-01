<?php

use App\Http\Controllers\ProjectController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Project Routes
    Route::get  ('/project'                , [ProjectController::class, 'index'  ])->name('project.index'  );
    Route::get  ('/project/create'         , [ProjectController::class, 'create' ])->name('project.create' );
    Route::post ('/project'                , [ProjectController::class, 'store'  ])->name('project.store'  );
    Route::get  ('/project/{project}'      , [ProjectController::class, 'show'   ])->name('project.show'   );
    Route::get  ('/project/{project}/edit' , [ProjectController::class, 'edit'   ])->name('project.edit'   );
    Route::put  ('/projects/{project}'     , [ProjectController::class, 'update' ])->name('project.update' );
    Route::delete('/project/{project}'     , [ProjectController::class, 'destroy'])->name('project.destroy');

    // Project Extension Requests
    Route::post   ('/project/{project}/request-extension'   , [ProjectController::class, 'requestExtension' ])->name('project.extension.store'   );
    Route::delete ('/project/{extension}/cancel-extension'  , [ProjectController::class, 'cancelRequest'    ])->name('project.extension.delete'  );
    Route::put    ('/project/{extension}/approve-extension' , [ProjectController::class, 'approveRequest'   ])->name('project.extension.approve' );
    Route::put    ('/project/{extension}/decline-extension' , [ProjectController::class, 'declineRequest'   ])->name('project.extension.decline' );

    // Project Assignees & Reviewers
    Route::delete ('/projects/{project}/assignees/{employee}' , [ProjectController::class, 'removeAssignee' ])->name('project.assignee.remove' );
    Route::delete ('/projects/{project}/reviewers/{employee}' , [ProjectController::class, 'removeReviewer' ])->name('project.reviewer.remove' );
    Route::post   ('/projects/{project}/add-assignees'        , [ProjectController::class, 'addAssignees'   ])->name('project.assignee.store'  );
    Route::post   ('/projects/{project}/add-reviewers'        , [ProjectController::class, 'addReviewers'   ])->name('project.reviewer.store'  );

    // Project Tracking Stages & Entries
    Route::post   ('/projects/{project}/add-tracking-stage'    , [ProjectController::class, 'storeStage'    ])->name('project.tracking.stage.store'    );
    Route::delete ('/projects/remove-tracking-stage/{stage}'   , [ProjectController::class, 'deleteStage'   ])->name('project.tracking.stage.destroy'  );
    Route::put    ('/project/update-stage-entry'               , [ProjectController::class, 'updateAll'     ])->name('project.tracking.entry.update'   );
    Route::put    ('/project/tracking-stage/{stage}/move-up'   , [ProjectController::class, 'moveStageUp'   ])->name('project.tracking.stage.moveUp'   );
    Route::put    ('/project/tracking-stage/{stage}/move-down' , [ProjectController::class, 'moveStageDown' ])->name('project.tracking.stage.moveDown' );

    // Project Comments
    Route::post  ('/project/{project}/comments', [ProjectController::class, 'storeComment'  ])->name('project.comment.store' );
    Route::put   ('/project/comments/{comment}', [ProjectController::class, 'updateComment' ])->name('project.comment.update');
    Route::delete('/project/comments/{comment}', [ProjectController::class, 'destroyComment'])->name('project.comment.delete');

    // Project Completion Request
    Route::post   ('/project/{project}/request-completion'                  , [ProjectController::class, 'requestProjectCompletion'        ])->name('project.completion.request' );
    Route::delete ('/project/{project}/request-completion'                  , [ProjectController::class, 'cancelProjectCompletion'         ])->name('project.completion.cancel' );
    Route::delete ('/project/completion-request/{completionRequest}/cancel' , [ProjectController::class, 'cancelProjectCompletionRequest'  ])->name('project.completion.request.cancel'  );
    Route::delete ('/project/completion-request/{completionRequest}/approve', [ProjectController::class, 'approveProjectCompletionRequest' ])->name('project.completion.approve' );
    Route::delete ('/project/completion-request/{completionRequest}/decline', [ProjectController::class, 'declineProjectCompletionRequest' ])->name('project.completion.decline' );
});