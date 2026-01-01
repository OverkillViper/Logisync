<?php

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get   ('/batch'             , [App\Http\Controllers\BatchController::class, 'index'  ])->name('batch.index'  );
    Route::post  ('/batch'             , [App\Http\Controllers\BatchController::class, 'store'  ])->name('batch.store'  );
    Route::get   ('/batch/{batch}'     , [App\Http\Controllers\BatchController::class, 'show'   ])->name('batch.show'   );
    Route::get   ('/batch/{batch}/edit', [App\Http\Controllers\BatchController::class, 'edit'   ])->name('batch.edit'   );
    Route::put   ('/batch/{batch}'     , [App\Http\Controllers\BatchController::class, 'update' ])->name('batch.update' );
    Route::delete('/batch/{batch}'     , [App\Http\Controllers\BatchController::class, 'destroy'])->name('batch.destroy');
});