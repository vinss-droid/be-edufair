<?php

use App\Http\Controllers\API\Admin\AdminController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\Webinar\RegisterController as RegisterWebinarController;
use App\Http\Controllers\API\Workshop\RegisterController as RegisterWorkshopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth.apikey'])->prefix('/v1')->group(function() {

//    Unauthorized user
    Route::get('/unauthorized', function () {
        return response()->json(['message' => 'unauthorized'], 401);
    })->name('unauthorized');

//    Auth Route

    Route::controller(AuthController::class)->prefix('/auth')->group(function() {

        Route::post('/login', 'login')->name('auth.login');
        Route::get('/logout', 'logout')->name('auth.logout')->middleware(['auth:sanctum']);

    });

//    Authenticated Route

    Route::middleware(['auth:sanctum'])->group(function () {

//        Admin Route
        Route::middleware(['auth.admin'])->prefix('/admin')->group(function() {

            Route::controller(AdminController::class)->group(function () {
    //          Webinar
                Route::get('/webinar-participants/{year}', 'participants')->name('admin.webinar.participants');
                Route::get('/webinar-participant/{year}/{id}', 'participantById')->name('admin.webinar.participantById');

    //            Attendance

                Route::get('/attendances/{event}/{year}', 'participantAbsences')->name('admin.attendances');

    //            Attendance Code
                Route::prefix('/attendance-code')->group(function () {

                    Route::get('/', 'attendanceCode')->name('admin.attendanceCode.index');
                    Route::delete('/{id}', 'deleteAttendanceCode')->name('admin.attendanceCode.delete');
                    Route::post('/{year}', 'createAttendanceCode')->name('admin.attendanceCode.create');
                    Route::put('/change-active-code/{id}', 'changeIsActiveAttendanceCode')->name('admin.attendanceCode.changeIsActive');

                });
            });

//         Notification

           Route::controller(NotificationController::class)->prefix('/notification')->group(function () {
               Route::get('/webinar/{year}', 'webinarNotificationLink')->name('notification.webinar.link');
           });

        });

//        Register Route
        Route::prefix('/register')->group(function () {

//      Webinar Route

            Route::controller(RegisterWebinarController::class)->prefix('/webinar')->group(function () {

                Route::post('/{year}', 'register')->name('register.webinar');
                Route::get('/{year}/total-participants', 'totalParticipant')->name('register.total.participants');
                Route::get('/{year}/check', 'check')->name('register.webinar.check');
                Route::get('/{year}/thanks', 'sendThanksForRegister')->name('register.thanks');

            });

//      Workshop Route

            Route::controller(RegisterWorkshopController::class)->prefix('/workshop')->group(function () {

                Route::post('/{year}', 'register')->name('register.workshop');
                Route::get('/{year}/total-participants', 'totalParticipant')->name('register.total.participants.workshop');
                Route::get('/{year}/check', 'check')->name('register.workshop.check');
                Route::get('/{year}/thanks', 'sendThanksForRegister')->name('register.thanks.workshop');

            });

        });

//        Upload Route
        Route::controller(UploadController::class)->prefix('/upload')->group(function () {

            Route::post('/proof-image', 'uploadProofEdufair')->name('upload.proof.image');

        });

//        Attendance Route
        Route::controller(AttendanceController::class)->prefix('/attendance')->group(function() {

            Route::get('/{code}', 'checkParticipantHasAbsent')->name('attendance.check');
            Route::post('/', 'attendance')->name('attendance.index');

        });

//        Questionnaire Route

//        Route::controller(QuestionnaireController::class)->prefix('/questionnaire')->group(function () {
//
//            Route::get('/check/{code}', 'checkAttendanceCode')->name('questionnaire.checkAttendanceCode');
//
//        });

    });

});
