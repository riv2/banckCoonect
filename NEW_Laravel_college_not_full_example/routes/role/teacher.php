<?php

Route::domain('t.' . str_replace(['http://', 'https://', 'www.', '/'], '', config('app.url')))
    ->group(function () {

        Route::get('login', 'IndexController@login')->name('teacherLogin');
        Route::post('login', 'IndexController@postLogin');

        Route::group(['middleware' => ['auth', 'hasRole:teacher'], 'namespace' => 'Teacher'], function () {
            Route::group([ 'middleware' => ['isTeacherProfileComplete'] ], function () {
                //Route::get('dashboard', 'TeacherProfileController@index')->name('teacherDashboard');

                /*Profile*/
                Route::get('profile', 'TeacherProfileController@show')->name('teacherProfile');
                Route::get('profile/edit', 'TeacherProfileController@profileEdit')->name('teacherProfileEdit');

                /*wifi*/
                Route::get('wifi', '\App\Http\Controllers\WifiController@teacherDashboard')->name('teacherWifiDashboard');
                Route::get('ajax/wifi/getdevices', '\App\Http\Controllers\WifiController@teacherGetDevices')->name('teacherGetDevices');
                Route::post('ajax/wifi/adddevice', '\App\Http\Controllers\WifiController@teacherAddDevice')->name('teacherAddDevice');
                Route::post('ajax/wifi/deletedevice', '\App\Http\Controllers\WifiController@teacherDeleteDevice')->name('teacherDeleteDevice');

                /*Courses*/
                Route::group(['prefix' => 'courses'], function () {
                    Route::get('/', 'TeacherCourseController@coursesList')->name('teacherCoursesList');

                    Route::get('/{id}', 'TeacherCourseController@coursesEdit')->name('teacherCourseEdit');
                    Route::post('/{id}', 'TeacherCourseController@coursesEditPost')->name('teacherCourseEditPost');

                    Route::get('delete/{id}', 'TeacherCourseController@courseDelete')->name('teacherCourseDelete');

                    /*Lectures*/
                    Route::get('/{courseIdId}/lectures', 'LectureController@getList')->name('teacherLectureList');

                    Route::get('/{courseId}/lectures/{lectureId}', 'LectureController@edit')->name('teacherLectureEdit');
                    Route::post('/{courseId}/lectures/{lectureId}', 'LectureController@editPost');

                    Route::get('/lectures/delete/{id}', 'LectureController@courseDelete')->name('teacherLectureDelete');

                    /*Schedule*/
                    Route::get('/{id}/schedule', 'ScheduleController@getList')->name('teacherScheduleList');
                });
            });

            /*Profile SmartID*/
            Route::get('profile/id', 'TeacherProfileController@profileID')->name('teacherProfileID');
            Route::post('profile/id', 'TeacherProfileController@profileIDPost')->name('teacherProfileIDPost');

            /*Profile*/
            Route::get('profile/create', 'TeacherProfileController@profileEdit')->name('teacherProfileCreate');
            Route::post('profile/save', 'TeacherProfileController@profileEditPost')->name('teacherProfileSave');

            /*Qr*/
            Route::get('qr/generate', 'QrController@index')->name('teacherQrGenerate');
            Route::post('qr/generate', 'QrController@generate');
            Route::get('qr/for_test', 'QuizController@qr')->name('teacherQRForTest');
            Route::post('qr/for_test', 'QuizController@getQR')->name('teacherGetQRForTest');

//            Journal
            Route::get('disciplines', 'DisciplinesController@list')
                ->middleware(['visitedPage:Журнал'])
                ->name('teacherDisciplines');
            Route::get('disciplines/{id}', 'DisciplinesController@groups')
                ->middleware(['visitedPage:УМКД'])
                ->name('teacherDiscipline');
            Route::get('groups/{discipline_id}/{group_id}', 'DisciplinesController@group')->name('teacherGroup');
            Route::post('groups/{discipline_id}/{group_id}', 'DisciplinesController@groupSave')->name('teacherGroupSave');
            Route::post('groups/upload/file/{discipline_id}', 'DisciplinesController@journalUploadStudentFile')->name('journalUploadStudentFile');
            Route::post('groups/delete/student/file', 'DisciplinesController@journalDeleteStudentFile')->name('journalDeleteStudentFile');
            Route::post('groups/student/file/set_read', 'DisciplinesController@journalStudentFileSetRead')->name('journalSetReadStudentFile');

            //Chat
            Route::get('chat', 'TeacherChatController@teacherChatView')->name('openChat');
            Route::any('contactsInfo', 'TeacherChatController@contactsInfo')->name('contactsInfo');
            Route::post('peer/set', 'TeacherChatController@setPeerId')->name('chatSetPeer');
            Route::post('peer/get', 'TeacherChatController@getPeerId')->name('chatGetPeer');
            Route::post('caller/get', 'TeacherChatController@getCallerById')->name('chatGetCallerById');
            Route::post('chat/webcam/post', 'TeacherChatController@webcamPost')
                ->name('chatWebcamPost');
            Route::post('chat/user/getId', 'TeacherChatController@getCurrentUser');

        });

        Route::group(['namespace' => 'Student'], function () {
            Route::get('pay/room', 'PayController@payLectureRoom')->name('teacherPayLectureRoom');
        });

        Route::group(['prefix' => 'journal', 'namespace' => 'Teacher'], function(){
            Route::get('/', 'TeacherJournalController@index')
                ->name('teacher.journal.index');

            Route::post('ajax/journal', 'TeacherJournalController@ajaxJournal')->name('getTeacherJournal');
            Route::post('ajax/study_groups', 'TeacherJournalController@ajaxStudyGroups')->name('getStudyGroups');
            Route::post('ajax/semesters', 'TeacherJournalController@ajaxSemesters')->name('getSemesters');
            Route::post('ajax/set_rating', 'TeacherJournalController@ajaxSetRating')->name('setRating');
            Route::post('ajax/set_final_result', 'TeacherJournalController@ajaxSetFinalResult')->name('setFinalResult');
            Route::post('ajax/add_schedule', 'TeacherJournalController@ajaxAddToSchedule')->name('addToSchedule');
        });
    });