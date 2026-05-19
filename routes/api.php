<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Event\EventController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::delete('logout', [AuthController::class, 'logout'])->middleware("auth:sanctum");


Route::middleware(["auth:sanctum", "role:admin"])->group(function () {
    //category
    Route::controller(CategoryController::class)->group(function () {
        Route::post("category/create", "create");
        Route::put("category/update/{id}", "update");
        Route::delete("category/delete/{id}", "delete");
    });

    //event
    Route::controller(EventController::class)->group(function () {
        Route::post("event/create", "create");
        Route::post("event/edit/{id}", "edit");
        Route::delete("event/delete/{id}", "delete");
    });

    //user deletion
    Route::delete("user/delete/{id}", [UserController::class, "deleteUser"]);
});
Route::middleware(["auth:sanctum", "role:user"])->group(function () {
    //booking
    Route::controller(BookingController::class)->group(function () {
        Route::post('booking/book/{event_id}', "book");
        Route::get('booking/user', "userBookings");
        Route::post("booking/update",  "update");
    });
});
Route::get('booking/all', [BookingController::class, "all"])->middleware(["auth:sanctum", "role:admin"]);
Route::get('booking/pending', [BookingController::class, "pending"])->middleware(["auth:sanctum", "role:admin"]);


//category
Route::get("category/all", [CategoryController::class, "index"]);
Route::get("category/{id}", [CategoryController::class, "show"]);

//event
Route::get("event/all", [EventController::class, "index"]);
Route::get("event/all_w_cat", [EventController::class, "allWithCategory"]);
Route::get("event/{id}", [EventController::class, "show"]);
Route::get("event/with_cat/{id}", [EventController::class, "showWithCategory"]);

Route::post('/verify_otp', [OtpController::class, 'verifyOtp']);
Route::post('/resend_otp', [OtpController::class, 'resend']);
