<?php

use App\Http\Controllers\EvaluationController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Evaluation
    Route::get    ('/evaluation'                             , [EvaluationController::class, 'index'                 ])->name('evaluation.index'      );
    Route::get    ('/evaluation/by-batch/{batch}'            , [EvaluationController::class, 'getEvaluationsForBatch'])->name('evaluation.byBatch'    );
    Route::get    ('/evaluation/details/{batch}/{evaluation}', [EvaluationController::class, 'getEvaluationDetails'  ])->name('evaluation.details'    );
    Route::post   ('/evaluation/score'                       , [EvaluationController::class, 'storeEvaluationScore'  ])->name('evaluation.score.store');
    Route::post   ('/evaluation'                             , [EvaluationController::class, 'storeEvaluation'       ])->name('evaluation.store');
    Route::delete ('/evaluation/{evaluation}'                , [EvaluationController::class, 'deleteEvaluation'      ])->name('evaluation.destroy');
    Route::put    ('/evaluation/{evaluation}'                , [EvaluationController::class, 'updateEvaluation'      ])->name('evaluation.update');

    // Evaluation Criteria
    Route::get    ('/evaluation/criteria'           , [EvaluationController::class, 'criteriaIndex'  ])->name('evaluation.criteria.index'   );
    Route::post   ('/evaluation/criteria'           , [EvaluationController::class, 'storeCriteria'  ])->name('evaluation.criteria.store'   );
    Route::put    ('/evaluation/criteria/{criteria}', [EvaluationController::class, 'updateCriteria' ])->name('evaluation.criteria.update'  );
    Route::delete ('/evaluation/criteria/{criteria}', [EvaluationController::class, 'deleteCriteria' ])->name('evaluation.criteria.destroy' );

    // Evaluation remarks
    Route::post('/evaluations/remarks',               [EvaluationController::class, 'storeRemarks'])->name('evaluation.remarks.store');
});