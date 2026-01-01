<?php

use App\Http\Controllers\AttendanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get   ('/attendance'                , [AttendanceController::class, 'index'            ])->name('attendance.index'         );
    Route::put   ('/attendance/punch'          , [AttendanceController::class, 'punch'            ])->name('attendance.punch'         );
    Route::post  ('/attendance/on-leave/mark'  , [AttendanceController::class, 'markOnLeave'      ])->name('attendance.onleave.mark'  );
    Route::post  ('/attendance/on-leave/clear' , [AttendanceController::class, 'clearOnLeave'     ])->name('attendance.onleave.clear' );
    Route::post  ('/attendance/holiday/mark'   , [AttendanceController::class, 'markOnHoliday'    ])->name('attendance.holiday.mark'  );
    Route::post  ('/attendance/holiday/clear'  , [AttendanceController::class, 'clearOnHoliday'   ])->name('attendance.holiday.clear' );
    Route::get   ('/attendance/today'          , [AttendanceController::class, 'todaysAttendance' ])->name('attendance.today'         );
});