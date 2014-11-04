<?php

// Socket API
Route::group(array('prefix' => 'api', 'before' => 'log_request'), function() {
    Route::controller('/users', 'Controllers\Api\UserController');
    Route::controller('/friends', 'Controllers\Api\FriendController');
    Route::controller('/rooms', 'Controllers\Api\RoomController');
    Route::controller('/messages', 'Controllers\Api\MessageController');
});
