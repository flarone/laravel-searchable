<?php

// Test and other endpoints that shouldn't be on production
if(!App()->isProduction()) {
    Route::get('test', function () {
        return response()->json(['Result' => 'OK']);
    });
}