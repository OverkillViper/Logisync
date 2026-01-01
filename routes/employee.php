<?php

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get  ('/employees'                                   , [App\Http\Controllers\EmployeeController::class, 'index'                ])->name('employees.index'                );
    Route::get  ('/employees/employee/{employee}'               , [App\Http\Controllers\EmployeeController::class, 'show'                 ])->name('employees.show'                 );
    Route::put  ('/employees/{employee}/update-role'            , [App\Http\Controllers\EmployeeController::class, 'updateRole'           ])->name('employees.updateRole'           );
    Route::put  ('/employees/{employee}/update-supervisor'      , [App\Http\Controllers\EmployeeController::class, 'updateSupervisor'     ])->name('employees.updateSupervisor'     );
    Route::post ('/employees/{employee}/update-profile-picture' , [App\Http\Controllers\EmployeeController::class, 'uploadProfilePicture' ])->name('employees.uploadProfilePicture' );
    Route::put  ('/employees/{employee}/update-batch'           , [App\Http\Controllers\EmployeeController::class, 'updateBatch'          ])->name('employees.updateBatch'          );
    Route::put  ('/employees/{employee}'                        , [App\Http\Controllers\EmployeeController::class, 'update'               ])->name('employees.update'               );
});