<?php

Route::domain('s.' . str_replace(['http://', 'https://', 'www.', '/'], '', config('app.url')))
    ->group(function () {

        Route::get('/profile/id', 'TeacherMiras\ProfileController@profileID')->name('teacherMirasUserProfileID');
        Route::post('/profile/id', 'TeacherMiras\ProfileController@profileIDPost')->name('teacherMirasUserProfileIDPost');

        /*MANUAL*/
        Route::get('/profile/id/manual', 'TeacherMiras\ProfileController@profileIDManual')->name('teacherMirasUserProfileIDManual');
        Route::post('/profile/id/manual/post', 'TeacherMiras\ProfileController@profileIDManualPost')->name('teacherMirasUserProfileIDManualPost');

        Route::get('/profile/create', 'TeacherMiras\ProfileController@profileEdit')->name('teacherMirasProfileEdit');

        /*FAMILY STATUS*/
        Route::get('/profile/family_status', 'TeacherMiras\ProfileController@profileFamilyStatus')->name('teacherMirasFamilyStatus');
        Route::post('/profile/family_status/add', 'TeacherMiras\ProfileController@profileFamilyStatusPost')->name('teacherMirasFamilyStatusPost');

        /*Address*/
        Route::get('/profile/address', 'TeacherMiras\ProfileController@profileAdress')->name('teacherMirasAddAdress');
        Route::post('/profile/address/add', 'TeacherMiras\ProfileController@profileAdressPost')->name('teacherMirasAddAdressPost');

        /*PHONE*/
        Route::get('/profile/phone', 'TeacherMiras\ProfileController@profilePhone')->name('teacherMirasEnterMobilePhone');
        Route::post('/profile/phone/sendcode', 'TeacherMiras\ProfileController@profileMobileSendCode')->name('teacherMirasEnterMobilePhoneSendcode');
        Route::post('/profile/phone/approve', 'TeacherMiras\ProfileController@profileMobileApprove')->name('teacherMirasEnterMobilePhonePost');

        /*Resume*/
        Route::get('/profile/resume', 'TeacherMiras\ProfileController@profileResume')->name('teacherMirasEnterResume');
        Route::post('/profile/resume/add', 'TeacherMiras\ProfileController@profileResumePost')->name('teacherMirasEnterResumePost');

        /*Education*/
        Route::get('/profile/education', 'TeacherMiras\ProfileController@profileEducation')->name('teacherMirasEnterEducation');
        Route::post('/profile/education/add', 'TeacherMiras\ProfileController@profileEducationPost')->name('teacherMirasEnterEducationPost');

        /*SENIORITY*/
        Route::get('/profile/seniority', 'TeacherMiras\ProfileController@profileSeniority')->name('teacherMirasSeniority');
        Route::post('/profile/seniority/add', 'TeacherMiras\ProfileController@profileSeniorityPost')->name('teacherMirasSeniorityPost');

        // for test
        //Route::get('/education', 'TeacherMiras\ProfileController@profileEducation')->name('teacherMirasEnterEducation');

    });