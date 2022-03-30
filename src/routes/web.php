<?php

// The authenticated routes
Route::middleware(['auth'])->group(function () {

});

// Test and other endpoints that shouldn't be on production
if (!App()->isProduction()) {
    Route::get('test', function () {
        return response()->json(['Result' => 'OK']);
    });
}