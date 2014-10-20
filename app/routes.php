<?php

// Socket API
Route::group(array('prefix' => 'api'), function() {
    Route::controller('/users', 'Controllers\api\userController');
    Route::controller('/friends', 'Controllers\api\friendController');
    Route::controller('/rooms', 'Controllers\api\roomController');
    Route::controller('/messages', 'Controllers\api\messageController');
});
