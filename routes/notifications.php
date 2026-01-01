<?php

use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->group(function () {
    Route::get('/user/notifications', [NotificationController::class, 'notifications']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notification/{notification}', [NotificationController::class,'destroy'])->name('notification.destroy');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAllNotifications'])->name('notifications.deleteAll');
});