<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Auth::routes();

Route::any('/get_result_etxt', 'Admin\\EtxtController@saveAnswer');

Route::group(['prefix' => App\Http\Middleware\LocaleMiddleware::getLocale()], function() {

    Route::get('guest/s/default', function () {
        return redirect()->route('wifiGuestPage');
    });

    require_once('role/admin.php');
    require_once('role/teacher.php');
    require_once('role/teacherMiras.php');

    Route::get('qr/test', 'IndexController@qrTest')->name('qrTest');

	Route::get('/', 'IndexController@index')
        ->middleware(['visitedPage:Главная'])
        ->name('home');
	Route::get('about-me', 'IndexController@aboutme_page')->name('about-me');
	Route::get('terms-conditions', 'IndexController@terms_conditions_page')->name('terms');
	Route::get('privacy-policy', 'IndexController@privacy_policy_page')->name('privacy');
	Route::get('testimonials', 'IndexController@testimonials')->name('testimonials');

    Route::get('login/redirect', 'Auth\LoginController@loginDomainRoute')->name('login');

    Route::post('login/getstudent', 'Auth\LoginController@getStudent')->name('loginGetStudent');
    Route::get('login', 'Auth\LoginController@studentLogin')->name('studentLogin');
	Route::post('login', 'Auth\LoginController@studentPostLogin');
    Route::post('login/sms/send', 'Auth\LoginController@sendSmsCode')->name('StudentLoginSendSmsCode');
    Route::post('login/password/recovery', 'Auth\LoginController@sendPassword')->name('StudentLoginSendPassword');

	Route::get('register', 'IndexController@register')->name('register');
	Route::post('register', 'IndexController@postRegister');
	
	Route::any('logout', 'IndexController@logout')->name('logout');

    Route::any('enter/check', 'EnterController@checkEnterQR')->name('checkEnterQR');
	
	// Password reset link request routes...
	Route::get('password/email', 'Auth\PasswordController@getEmail')->name('paswordEmail');
	Route::post('password/email', 'Auth\PasswordController@postEmail');
	
	// Password reset routes...
	Route::get('password/{token}', 'Auth\PasswordController@getReset');
	Route::post('password/reset', 'Auth\PasswordController@postReset')->name('passwordReset');
	
	Route::get('auth/confirm/{code}', 'IndexController@confirm')->name('emailConfirmCode');
    Route::get('auth/resend/email/{email}', 'IndexController@resendCodeToEmail')->name('resendCodeToEmail');
	
	//Route::post('users/login', 'Auth\AuthController@postLogin');
	
	Route::get('/smartid', 'SmartIDController@index')->name('smartID');
	
//	Route::group(['middleware' => ['auth']], function () {
	
//		Route::get('/dashboard', 'HomeController@index')->name('userDashboard');

	//Route::get('/transcriptcheck', 'Student\DocsController@check')->name('transcriptCheck');
	//Route::get('/transcriptcheck/{docname}', 'Student\DocsController@checkPost')->name('transcriptCheck');
    Route::get('/documentcheck', 'Student\DocsController@documentCheck')->name('documentCheck');
    Route::get('/documentcheck/{docname}', 'Student\DocsController@CheckPost')->name('documentCheck');

	Route::get('/errorreport', 'IndexController@report')->name('errorReport');
	Route::post('/errorreport', 'IndexController@reportPost')->name('errorReportPOST');

	Route::get('/promo', 'PromoController@promo')->name('promo');
	Route::post('/promo', 'PromoController@promoIDPost')->name('promoIDPost');
	Route::post('/promoContact', 'PromoController@promoContact')->name('promoContact');

	Route::get('sidtest', 'IndexController@sidtest')->name('sidtest');
	Route::post('sidtest', 'IndexController@sidtestPost')->name('sidtestPost');

    Route::get('agitator', 'IndexController@agitator')->name('agitator');
    Route::post('agitator', 'IndexController@agitatorPost')->name('agitatorPost');

    Route::post('agitator/list/ajax', 'IndexController@agitatorAjaxList')->name('agitatorAjaxList');

    // render print buy service
    Route::any('print/buy/service', 'Student\StudyController@renderPrintBuyService')->name('renderPrintBuyService');

    //coffee
    Route::get('coffee/login', 'WifiController@login')->name('coffeeLoginPage');
    Route::post('coffee/login', 'WifiController@loginPost')->name('coffeeLogin');
    Route::get('coffee/main', 'WifiController@main')->name('coffeeMain');
    Route::get('coffee/genwifi', 'WifiController@genWifiPage')->name('genWifiPage');
    Route::get('coffee/genwifi/ajax', 'WifiController@getNewCode')->name('genWifi');

    Route::get('guest/wifi', 'WifiController@guestPage')->name('wifiGuestPage');
    Route::get('guest/wifi/alreadyhad', 'WifiController@guestAlreadyHadWifi')->name('alreadyHadWifi');

    Route::get('guest/kaspi', 'IndexController@kaspi');
    Route::any('guest/kaspipost', 'IndexController@postTest');

    Route::get('callback', 'IndexController@callBackForm')->name('callBack');
    Route::post('callback', 'IndexController@callBack')->name('callBack');

    Route::group(['middleware' => ['auth']], function () {

        Route::get('fitness/room', 'IndexController@fitnessRoom')->name('fitnessRoom');
        Route::get('cafeteria', 'IndexController@cafeteria')->name('cafeteria');
        Route::get('pool', 'IndexController@pool')->name('pool');
        Route::get('training', 'IndexController@training')->name('training');
        Route::get('bus', 'IndexController@bus')->name('bus');
        Route::get('qr', 'IndexController@qr')->name('qr');
        Route::get('wifi', 'Student\WifiController@getList')->name('wifi');
        Route::get('gid', 'IndexController@gid')->name('gid');
        Route::get('cinema', 'IndexController@cinema')->name('cinema');
        Route::get('game/library','IndexController@gameLibrary')->name('gameLibrary');
        Route::get('hostel', 'IndexController@hostel')->name('hostel');
        Route::get('mobile/phones', 'IndexController@mobilePhones')->name('mobilePhones');
        Route::get('procoffee', 'IndexController@procoffee')->name('procoffee');
        Route::get('career', 'IndexController@career')->name('career');
        Route::get('rent/bikes', 'IndexController@rentBikes')->name('rentBikes');

        Route::get('enter/qr', 'EnterController@enterQrPage')->name('enterQrPage');
        Route::get('enter/qr/ajax', 'EnterController@getEnterQR')->name('getEnterQR');

        // reset password
        Route::get('profile/reset/password', 'Auth\ResetController@resetPassword')->name('resetPassword');
        Route::any('ajax/reset/password', 'Auth\ResetController@ajaxResetPassword')->name('ajaxResetPassword');

        // COURSES
        Route::group(['prefix' => 'courses'], function () {

            Route::get('/', 'CoursesController@getList')->name('getCoursesList');
            Route::get('/course', 'CoursesController@getCourse')->name('getCoursePage');
            Route::get('/info', 'CoursesController@getInfo')->name('getCourseInfo');
            Route::post('/info/post', 'CoursesController@getInfoPost')->name('courseInfoPost');
            Route::get('/profile/id', 'CoursesController@profileId')->name('courseProfileId');
            Route::any('/profile/post', 'CoursesController@profileIdPost')->name('courseProfileIdPost');
            Route::any('/profile/edit', 'CoursesController@profileEdit')->name('courseProfileEdit');
            Route::any('/id/manual', 'CoursesController@profileIdManual')->name('courseProfileIdManual');
            Route::any('/id/manual/post', 'CoursesController@profileIdManualPost')->name('courseProfileIdManualPost');
            Route::get('/cabinet', 'CoursesController@cabinet')->name('getCourseCabinet');
            Route::get('/success/pay', 'CoursesController@successPay')->name('courseSuccessPay');

        });

        // agitator registration
        Route::group(['prefix' => 'agitator/registration'], function () {

            Route::get('/', 'AgitatorRegisterController@index')->name('agitatorRegisterIndex');
            Route::get('/profile/id', 'AgitatorRegisterController@profileID')->name('agitatorRegisterProfileID');
            Route::any('/profile/id/post', 'AgitatorRegisterController@profileIDPost')->name('agitatorRegisterProfileIDPost');
            Route::any('/profile/edit', 'AgitatorRegisterController@profileEdit')->name('agitatorRegisterProfileEdit');
            Route::any('/profile/id/manual', 'AgitatorRegisterController@profileIdManual')->name('agitatorRegisterProfileIdManual');
            Route::any('/profile/id/manual/post', 'AgitatorRegisterController@profileIdManualPost')->name('agitatorRegisterProfileIdManualPost');
            Route::any('/profile/id/manual/approve', 'AgitatorRegisterController@profileApprove')->name('agitatorRegisterProfileApprove');
            Route::any('/profile/terms', 'AgitatorRegisterController@profileTerms')->name('agitatorRegisterProfileTerms');
            Route::any('/profile/iban', 'AgitatorRegisterController@profileIban')->name('agitatorRegisterProfileIban');
            Route::any('/profile/iban/post', 'AgitatorRegisterController@profileIbanPost')->name('agitatorRegisterProfileIbanPost');
            Route::any('/profile/finish', 'AgitatorRegisterController@profileFinish')->name('agitatorRegisterProfileFinish');
            Route::any('/profile/finish/post', 'AgitatorRegisterController@profileFinishPost')->name('agitatorRegisterProfileFinishPost');
            Route::any('/profile/load/image', 'AgitatorRegisterController@profileLoadImage')->name('agitatorRegisterProfileLoadImage');

        });

        // agitator
        Route::group(['prefix' => 'agitator'], function () {

            Route::any('/', 'AgitatorController@index')->name('agitatorProfile');
            Route::any('/profile/load/data', 'AgitatorController@profileLoadData')->name('agitatorRegisterProfileLoadData');
            Route::any('/profile/save/image', 'AgitatorController@profileSaveImage')->name('agitatorRegisterProfileSaveImage');
            Route::any('/profile/load/agitatorusers', 'AgitatorController@getAgitatorUsersList')->name('agitatorProfileLoadAgitatorUsers');
            Route::any('/profile/send/withdrawrequest', 'AgitatorController@sendWithdrawRequest')->name('agitatorProfileSendWithdrawRequest');
            Route::any('/profile/load/transactionhistory', 'AgitatorController@getAgitatorTransactionHistory')->name('AgitatorTransactionHistory');
            Route::any('/profile/get/withdrawinfo', 'AgitatorController@getWithdrawInfo')->name('AgitatorGetWithdrawInfo');


            Route::any('/profile/ajax/agitatorusers', 'AgitatorController@ajaxGetAgitatorUsersList')->name('agitatorProfileAjaxAgitatorUsers');

        });

        // ajax call print buy service
        Route::any('ajax/print/buy/service', 'Student\StudyController@ajaxPrintBuyService')->name('ajaxPrintBuyService');

        Route::group(['prefix' => 'wifi'], function () {

            Route::post('ajax/get/list', 'WifiController@ajaxGetList')->name('wifiAjaxGetList');
            Route::post('ajax/allowwifi', 'WifiController@userAllowWifi')->name('userAllowWifi');

        });

        /* Student - Poll */
        Route::group(['prefix' => 'poll'], function () {
            Route::get('/', 'Student\PollController@showPolls')->name('students.polls.show');
            Route::get('/{poll_id}', 'Student\PollController@showPoll')->name('student.poll.show');

            Route::post('/pass/{poll_id}', 'Student\PollController@pass')->name('student.poll.pass');
        });

        /* Vacancy */
        Route::group(['prefix' => 'vacancy'], function () {
            Route::get('/', 'VacancyController@index')->name('vacancy.index');
            Route::get('/resume', 'VacancyController@resumePage')->name('vacancy.resume');
            Route::get('/revision/resume/{id}/{type?}', 'VacancyController@revisionPage')->name('revision.resume.type.id');
            Route::post('/revision/resume/submit', 'VacancyController@revisionSubmit')->name('revision.resume.submit');
            Route::post('/datatable', 'VacancyController@vacancyDatatable')->name('vacancy.datatable');
            Route::post('/resume/datatable', 'VacancyController@vacancyResumeDatatable')->name('vacancy.resume.datatable');
            Route::post('/get/requirements', 'VacancyController@getRequirements')->name('get.requirements');
            Route::get('/get/vacancy/form/{id}', 'VacancyController@getVacancyForm')->name('get.vacancy.form');
            Route::post('/submit/vacancy/form', 'VacancyController@submitForm')->name('submit.vacancy.form');
        });

        /* Library */
        Route::group(['middleware' => 'isStudent', 'prefix' => 'library'], function () {
            Route::get('/{search?}', 'Student\LibraryController@index')->name('library.page');
            Route::get('/show/literature/{id}/page', 'Student\LibraryController@showLiteraturePage')->name('show.literature.page');
            Route::get('literature/download/{fileName}/{id}', 'Student\LibraryController@downloadFile')->name('download.file');
            Route::post('literature/order', 'Student\LibraryController@literatureOrder')->name('literature.order');
        });

        Route::post('refund/reference', 'Student\StudentController@refundReferencePay')->name('refundReferencePay');
        Route::post('refund/request', 'Student\StudentController@refundRequest')->name('refundRequest');
        Route::post('refund/smsCode', 'Student\StudentController@refundSmsCode')->name('refundSmsCode');
        

        Route::get('helps', 'IndexController@helps')->name('helps');

        Route::get('/profile', 'ProfileController@index')->name('userProfile');
        Route::post('/profile/post', 'ProfileController@editProfile')->name('editProfile');
        Route::post('/profile/edit/photo', 'ProfileController@editPhoto')->name('studentProfileEditPhoto');

        Route::group(['middleware' => ['registrationFinish']], function () {
            Route::get('/study', 'Student\StudyController@index')
                ->middleware(['visitedPage:Обучение'])
                ->name('study');
            Route::post('/study', 'Student\StudyController@setElectiveSpeciality')->name('setElectiveSpeciality');
            Route::get('/deansoffice', 'Student\DeansOfficeController@index')->name('deansOffice');
            Route::get('/deansoffice/download/calendar/{type}', 'Student\DeansOfficeController@downloadCalendar')->name('deansoffice.download.calendar');
            Route::post('/deansoffice/notifications/list', 'Student\DeansOfficeController@notificationsList')->name('deansofficeNotificationsList');
            Route::post('/deansoffice/news/list', 'Student\DeansOfficeController@newsList')->name('deansofficeNewsList');
            Route::get('/references', 'Student\ReferenceController@index')->name('references');

            Route::any('/deansoffice/get/count', 'Student\DeansOfficeController@getCount')->name('deansGetCount');
            Route::any('/deansoffice/news/setcount', 'Student\DeansOfficeController@setNewsCount')->name('deansSetNewsCount');
            Route::any('/deansoffice/notifications/setcount', 'Student\DeansOfficeController@setNotificationCount')->name('deansSetNotificationCount');

            Route::group(['prefix' => 'finances'], function () {
                Route::get('/', 'Student\StudentController@finances')->name('financesPanel');
                Route::post('/', 'Student\StudentController@discountPost');

                Route::get('/analogue', 'Student\StudentController@analogue')->name('analogue');
                Route::post('/analogue', 'Student\StudentController@analoguePost')->name('analoguePost');
                Route::post('/balance/update', 'Student\StudentController@updateBalance')->name('studentBalanceUpdate');

                Route::get('/disciplinepay/partial/{id}', 'Student\StudentController@disciplinePartialPay')->name('disciplinePartialPay');
                Route::get('/disciplinepay/{id}', 'Student\StudentController@disciplinePay')->name('disciplinePay');
                //Route::get('/miningpay/{discipline_id}', 'Student\StudentController@miningPay')->name('miningPay');
                //Route::post('/miningpay', 'Student\StudentController@miningPayPost')->name('miningPayPost');
                Route::post('/submodulepay/{id}', 'Student\StudentController@submodulePay')->name('submodulePay');
                Route::get('/remote_access_pay/{id}/{test?}', 'Student\StudentController@remoteAccessPay')->name('remoteAccessPay');

                Route::post('/discipline/pay/cancel', 'Student\StudyController@disciplinePayCancel')->name('studentDisciplinePayCancel');


                /*Route::get('/disciplinepay/partial/{id}', function () {
                    abort(500);
                })->name('disciplinePartialPay');
                Route::get('/disciplinepay/{id}', function () {
                    abort(500);
                })->name('disciplinePay');
                Route::post('/submodulepay/{id}', function () {
                    abort(500);
                })->name('submodulePay');*/

                //Route::get('/balancepay', 'Student\StudentController@balancePay')->name('balancePay');
                Route::get('/retakepay', 'Student\StudentController@retakePay')->name('retakePay');
                Route::get('/retakepay/kge', 'Student\StudentController@retakeKgePay')->name('retakeKgePay');
            });

            Route::get('/discipline/{discipline_id}/docs', 'Student\StudyController@documentsPage')
                ->name('student.discipline.docs');

            Route::get('/discipline/{discipline_id}/files', 'Student\StudyController@filesPage')
                ->name('student.discipline.files');

            Route::post('/discipline/{discipline_id}/docs/upload/{document_id}', 'Student\StudyController@uploadDocument')
                ->name('student.upload.document');

            Route::post('/discipline/{discipline_id}/file/upload', 'Student\StudyController@uploadFile')
                ->name('student.upload.file');

            Route::get('/discipline/{discipline_id}/file/{file_id}/remove', 'Student\StudyController@removeFile')
                ->name('student.remove.file');

            Route::get('/discipline/{discipline_id}/student_discipline/file/{file_id}/remove', 'Student\StudyController@removeStudentDisciplineFile')
                ->name('student.remove.student_discipline.file');
        });

        Route::get('registration/blank', 'IndexController@registrationBlank')->name('registrationBlank');
        Route::get('documentslist', 'ProfileController@docsNeedToUpload')->name('docsNeedToUpload');
        Route::post('documentslist', 'ProfileController@docsNeedToUploadPost')->name('docsNeedToUploadPost');
        Route::get('getuserdocumentslist', 'ProfileController@getUserDocsList')->name('getUserDocsList');

        Route::post('application/send', 'ProfileController@docsApplicationUploadPost')->name('docsApplicationUploadPost');

        Route::get('application/list', 'ProfileController@getUserRequestsList')->name('getUserRequestsList');


        Route::get('print/education_statement', 'ProfileController@printEducationStatement')->name('printEducationStatement');
        Route::get('print/education_contract', 'ProfileController@generateEducationContract')->name('generateEducationContract');

        Route::get('/profile/email', 'ProfileController@profileEmail')->name('profileEmail');
        Route::post('/profile/email', 'ProfileController@profileEmailPost')->name('profileEmailPost');

        Route::get('/profile/id', 'ProfileController@profileID')->name('userProfileID');
        Route::post('/profile/id', 'ProfileController@profileIDPost')->name('userProfileIDPost');

        Route::get('/profile/id/manual', 'ProfileController@profileIDManual')->name('userProfileIDManual');
        Route::get('json/profile/id/manual', 'ProfileController@profileIDManualPost')->name('profileIDManualPost');


		Route::get('/profile/create', 'ProfileController@profileEdit')->name('userProfileEdit');
		Route::get('/profile/approve', 'ProfileController@profileApprove')->name('profileApprove');

        // family status
        Route::get('/profile/family/status', 'ProfileController@profileFamilyStatus')->name('profileFamilyStatus');
        Route::post('/profile/family/status/post', 'ProfileController@profileFamilyStatusPost')->name('profileFamilyStatusPost');

        Route::get('/ajaxekt', 'Student\BcApplicationController@ajaxEnt')->name('ajaxEnt');

        Route::get('/profile/import', 'ProfileController@importResult')->name('userProfileImport');

        Route::post('/ajax/get/user/balance', 'Student\UpdateUserBalanceController@ajaxGetBalance')->name('profileAjaxGetUserBalance');

        Route::post('/ajax/user/buy/service', 'Student\PayController@buyService')->name('profileAjaxUserBuyService');

        Route::post('/ajax/add/transaction/history', 'Student\StudentController@ajaxAddTransactionHistory')->name('profileAjaxAddTransactionHistory');
        Route::post('/ajax/get/transaction/history', 'Student\StudentController@ajaxGetTransactionHistory')->name('profileAjaxGetTransactionHistory');

        //Route::group(['middleware' => ['hasRegistrationPaid']], function () {
        Route::get('/bachelor/profile', 'Student\BcApplicationController@actualPage')->name('bcApplication');
        Route::get('/bachelor/profile/{part}', 'Student\BcApplicationController@partForm')->name('bcApplicationPart');
        Route::post('/bachelor/profile/{part}', 'Student\BcApplicationController@partPost');
        Route::post('/bachelor/profile/send', 'ProfileController@bcApplicationPost')->name('bcApplicationPost');

        Route::get('/master/profile', 'Student\MgApplicationController@actualPage')->name('mgApplication');
        Route::get('/master/profile/{part}', 'Student\MgApplicationController@partForm')->name('mgApplicationPart');
        Route::post('/master/profile/{part}', 'Student\MgApplicationController@partPost');
        Route::post('/master/profile/send', 'ProfileController@MgApplicationPost')->name('mgApplicationPost');


        Route::get('/profile/add/agitator', 'ProfileController@addAgitator')->name('profileAddAgitator');
        Route::post('/profile/add/agitator', 'ProfileController@addAgitatorPost')->name('profileAddAgitatorPost');
        Route::post('/profile/get/agitator', 'ProfileController@getAgitator')->name('profileAjaxGetAgitator');
        Route::any('/profile/without/agitator', 'ProfileController@withoutAgitator')->name('profileWithoutAgitator');
        Route::get('/profile/register/payment', 'ProfileController@payment')->name('profileRegisterPayment');
        Route::post('/profile/register/payment', 'ProfileController@paymentPost')->name('profileRegisterPaymentPost');
        Route::get('/profile/register/finish', 'ProfileController@registerFinish')->name('profileRegisterFinish');


        Route::get('registration/finish', 'ProfileController@finishRegistration')->name('finishRegistration');
        /*
        Route::get('/master/profile', 'ProfileController@mgApplication')->name('mgApplication');
        Route::post('/master/profile', 'ProfileController@mgApplicationPost')->name('mgApplicationPost');
        */

        Route::get('/readonly/profile', 'Student\StudentController@readonly')->name('readonlyApplicatoin');


        Route::get('/{aplication}/speciality/select', 'SpecialityController@select')->name('specialitySelect');
        Route::get('/{aplication}/speciality/select/{id}', 'SpecialityController@selectSave')->name('specialitySelectSave');
        Route::get('/{aplication}/study_form', 'ProfileController@studyForm')->name('studyForm');
        Route::post('/{aplication}/study_form', 'ProfileController@studyFormPost')->name('studyFormPost');
        Route::get('/speciality/check', 'SpecialityController@check')->name('specialityCheck');
        Route::post('/speciality/check', 'SpecialityController@checkPost')->name('');
        Route::get('/speciality/confirm', 'SpecialityController@confirm')->name('specialityConfirm');
        Route::get('/speciality/language-level', 'SpecialityController@setLanguageLevel')->name('setLanguageLevel');
        Route::post('/speciality/language-level', 'SpecialityController@setLanguageLevelPost');
        // });

        Route::get('/referral', 'ProfileController@referralSource')->name('referralSource');
        Route::post('/referral', 'ProfileController@referralSourcePost')->name('referralSourcePost');

        Route::get('/afterid', 'ProfileController@afterUserIDSend')->name('afterID');
        //Route::post('/afterid', 'ProfileController@afterUserIDSendPost')->name('afterIDPost');

        // SRO
        Route::group(['prefix' => 'sro'], function () {
            Route::get('/list', 'Student\SyllabusTaskController@getList')
                ->middleware(['visitedPage:СРО'])
                ->name('sroGetList');
            Route::any('/render/list', 'Student\SyllabusTaskController@renderList')->name('sroRenderList');
            Route::get('/proceed', 'Student\SyllabusTaskController@proceed')->name('sroProceed');
            Route::post('/result/save', 'Student\SyllabusTaskController@saveResult')->name('sroSaveResult');
            Route::get('/task/pay', 'Student\SyllabusTaskController@pay')->name('sroTaskPay');
            Route::post('/task/pay/post', 'Student\SyllabusTaskController@payPost')->name('sroTaskPayPost');

            // coursework
            Route::group(['prefix' => 'coursework'], function () {
                Route::get('/pay', 'Student\SyllabusTaskCoursePayController@pay')->name('sroCourseworkPay');
                Route::post('/pay/post', 'Student\SyllabusTaskCoursePayController@payPost')->name('sroCourseworkPayPost');
            });

        });


        //  ENT SIDEBAR //
        Route::any('/student/ent', 'Student\EntController@index')->name('studentEnt');
        Route::post('/student/ent/getFolders', 'Student\EntController@getFolders')
            ->name('ent.getFolders');
        Route::post('/student/ent/getFiles', 'Student\EntController@getFiles')
            ->name('ent.getFiles');
        Route::post('/student/ent/studentVideoView', 'Student\EntController@studentVideoActivity')
            ->name('ent.studentVideoView');



        Route::get('/userdocs', 'Student\DocsController@index')->name('userDocs');
        Route::get('/userdocs/gentranscript', 'Student\DocsController@genTranscript')->name('generateTranscriptPDF');
        Route::get('/userdocs/genentered', 'Student\DocsController@genEntered')->name('generateEnteredPDF');
        Route::get('/userdocs/genmilitary', 'Student\DocsController@genMilitary')->name('generateGraduatePDF');

        Route::get('/userdocs/gengcvp4', 'Student\DocsController@genGcvp4')->name('generateGcvp4');
        Route::get('/userdocs/gengcvp21', 'Student\DocsController@genGcvp21')->name('generateGcvp21');
        Route::get('/userdocs/gengcvp6', 'Student\DocsController@genGcvp6')->name('generateGcvp6');
		
        // page with form for teachers skills
        Route::get('/addTeacherInfoForm/', 'ProfileController@teacherAdditionalInfo')->name('teacherAdditionalInfo');

        // insert into database teachers skills from input
        Route::post('/addTeacherInfoForm/', 'ProfileController@teacherAdditionalInfoPost')->name('teacherAdditionalInfoPost');

        Route::get('/ENTjson/{idtc}/{iin}', 'ProfileController@ENTjson')->name('ENTjson');

        Route::get('/{aplication}/education/language', 'ProfileController@setEducationLanguage')->name('studentEducationLanguage');
        Route::post('/{application}/education/language', 'ProfileController@setEducationLanguagePost');

        Route::get('/discipline/{disciplineId}/syllabus', 'Student\SyllabusController@getList')
            ->middleware(['visitedPage:Силабус'])
            ->name('studentSyllabus');
        Route::get('/discipline/{disciplineId}/syllabus/{lang}', 'Student\SyllabusController@getList')->name('studentSyllabusByLang');

        Route::group(['middleware' => ['checkBalance', 'hasNotAcademDebt']], function () {
            // Test 1
            Route::get('/test1/method/{id}', 'Student\QuizController@test1method')->name('studentSelectTest1Method');
            Route::post('/test1/QRCheck', 'Student\QuizController@test1QRCheck')->name('studentTest1QRCheck');
            Route::post('/test1/numericCodeCheck', 'Student\QuizController@test1NumericCodeCheck')->name('studentTest1NumericCodeCheck');

            Route::get('/test1/result/{id}', 'Student\QuizController@test1Result')->name('studentTest1Result');
            Route::get('/test1/last_result/{id}', 'Student\QuizController@test1LastResult')->name('studentTest1LastResult');
            Route::get('/test1/trial/{id}', 'Student\QuizController@test1Trial')->name('studentTest1Trial');

            Route::get('/quiz/kge/result', 'Student\QuizController@quizKgeResult')->name('studentQuizKgeResult');
            Route::get('/quiz/{id}', 'Student\QuizController@test1')->name('studentQuiz');
            Route::post('/quiz/{id}', 'Student\QuizController@test1Check')->name('studentQuizPost');

//            Route::get('/quiz/{id}/result', 'Student\QuizController@quizResult')->name('studentQuizResult');

            Route::post('/quiz/content', 'Student\QuizController@quizeContent')->name('studentQuizContent');

            Route::get('/quiz/kge', 'Student\QuizController@quizKge')->name('studentQuizKge');
            Route::post('/quiz/kge', 'Student\QuizController@quizKgeCheck')->name('studentQuizKgePost');

            // Exam
            Route::get('/exam/method/{id}', 'Student\QuizController@examMethod')->name('studentSelectExamMethod');
            Route::post('/exam/QRCheck', 'Student\QuizController@examQRCheck')->name('studentExamQRCheck');
            Route::post('/exam/numericCodeCheck', 'Student\QuizController@examNumericCodeCheck')->name('studentExamNumericCodeCheck');
            Route::get('/exam/remote_qr', 'Student\QuizController@remoteQR')->name('studentRemoteExamQR');
            Route::post('/exam/remote_qr_check', 'Student\QuizController@remoteExamQRCheck')->name('studentRemoteExamQRCheck');
            Route::post('/exam/remote_qr_numeric_code_check', 'Student\QuizController@remoteExamNumericCodeCheck')->name('studentRemoteExamNumericCodeCheck');

            Route::get('/exam/{id}', 'Student\QuizController@exam')
                ->middleware(['visitedPage:Экзамен'])
                ->name('studentExam');
            Route::post('/exam/{id}', 'Student\QuizController@examCheck')->name('studentExamPost');
            Route::get('/exam/last_result/{id}', 'Student\QuizController@examLastResult')->name('studentExamLastResult');
            Route::get('/exam/trial/{id}', 'Student\QuizController@examTrial')->name('studentExamTrial');
        });

        // Appeals
        Route::get('/appeal/create/test1/{disciplineId}', 'Student\AppealController@test1Create')->name('studentTest1Appeal');
        Route::post('/appeal/create/test1/{disciplineId}', 'Student\AppealController@test1createPost')->name('studentTest1Appeal');
        Route::get('/appeal/view/{appealId}', 'Student\AppealController@view')->name('studentViewAppeal');
        Route::get('/appeal/create/exam/{disciplineId}', 'Student\AppealController@examCreate')->name('studentExamAppeal');
        Route::post('/appeal/create/exam/{disciplineId}', 'Student\AppealController@examCreatePost')->name('studentExamAppeal');

        // Study plan
        Route::get('/study_plan/confirm', 'Student\StudyController@confirmPlan')->name('studentConfirmStudyPlan');
        Route::post('/study_plan/not_confirm', 'Student\StudyController@notConfirmPlan')->name('studentNotConfirmStudyPlan');

        Route::get('/idtest', 'IDController@index')->name('idtest');

        /**
         * Pay for student
         */
        Route::get('student/pay/lecture', 'Student\PayController@payLecture')->name('studentPayLecture');
        Route::get('student/pay/retakeTest', 'Student\PayController@payRetakeTest')->name('studentPayRetakeTest');
        Route::get('student/pay/retakeKge', 'Student\PayController@payRetakeKge')->name('studentPayRetakeKge');
        Route::get('student/pay/to_balance', 'Student\PayController@payToBalanceForm')->name('studentPayToBalance');
        //Route::post('student/pay/to_balance', 'Student\PayController@payToBalance');
        Route::post('student/pay/to_balance/by/token', 'Student\PayController@payToBalanceByToken')->name('studentPayToBalanceByToken');
        Route::post('student/card/remove', 'Student\PayController@removeCard')->name('studentRemoveCard');
        Route::get('student/pay/{id}', 'Student\PayController@pay')->name('studentPay');
        Route::get('student/pay/test1_trial/{id}', 'Student\PayController@test1Trial')->name('studentPayTest1Trial');
        Route::get('student/pay/remote_access/{id}', 'Student\PayController@remoteAccess')->name('studentPayRemoteAccess');
        Route::get('student/pay/exam_trial/{id}', 'Student\PayController@examTrial')->name('studentPayExamTrial');

        Route::get('pay/test', 'Student\PayController@payTestGet')->name('studentPayTest');
        Route::post('pay/test', 'Student\PayController@payTestPost')->name('studentPayTest');

        Route::get('pay/wifi/{tariffId}', 'Student\PayController@payWifi')->name('studentPayWifi');

        Route::get('registrationfee', 'Student\PayController@payRegistrationFee')->name('payRegistrationFee');
        Route::get('registrationfeealert', 'Student\StudentController@payRegistrationFee')->name('payRegistrationFeeAlert');


        Route::group(['prefix' => 'pay'], function () {
            Route::get('result', 'Student\PayController@backLink');
            Route::get('regfee/result', 'Student\PayController@backLinkProfile');
        });

        Route::get('pay/test/cloudpay', 'Student\PayController@payTestCloudpay')->name('studentPayTestCloudpay');

        Route::get('help', 'HelpController@form')->name('help');
        Route::post('help', 'HelpController@send');

        /**
         * Shop for student
         */
        Route::group(['prefix' => 'shop', 'namespace' => 'Student'], function () {
            Route::get('/disciplines', 'ShopController@disciplineList')->name('studentShopDisciplines');
            Route::get('/', 'ShopController@otherList')->name('studentShopIndex');

            Route::get('/details/{id}', 'ShopController@details')->name('studentShopDetails');
        });

        Route::group(['prefix' => 'promotions', 'namespace' => 'Student'], function () {
            Route::get('/', 'PromotionController@getList')->name('studentPromotionList');
            Route::any('/{id}/request', 'PromotionController@sendRequest')->name('studentPromotionRequest');
        });

        Route::group(['prefix' => 'checkin', 'namespace' => 'Student'], function () {
            Route::get('/', 'CheckinController@qrPage')->name('studentCheckin');
            Route::post('/check/qr', 'CheckinController@qrCheck')->name('studentCheckinQrCheck');
            Route::post('/check/code', 'CheckinController@numericCodeCheck')->name('studentCheckinNumericCodeCheck');
        });

        /**
         * Plagiarism
         */
        Route::name('student.plagiarism.')->middleware(['registrationFinish'])->namespace('Student')->prefix('plagiarism')->group(function () {
//            Route::get('/', 'PlagiarismController@show')->name('show'); // задача https://tasks.hubstaff.com/app/organizations/16298/projects/130300/tasks/1166356
            Route::get('/texts/oncheck', 'PlagiarismController@getTextsOnCheck')->name('texts.oncheck');
            Route::get('/texts/success', 'PlagiarismController@getTextSuccess')->name('texts.success');
            Route::post('/check', 'PlagiarismController@send')->name('check');
            Route::post('/report/pdf', 'PlagiarismController@generatePdf')->name('report.pdf');
            Route::get('/delete/{text_id}', 'PlagiarismController@delete')->name('delete');
        });

        /* Start Infodesk */
        Route::name('student.info.')->namespace('Student')->prefix('info')->group(function () {
            Route::get('/{info_type}', 'InformationController@show')
                ->where('info_type', 'important|other')
                ->name('show');

            Route::get('/details/{info_id}', 'InformationController@detailsShow')->name('details.show');
        });
        /* End Infodesk */

        /* Start InfoNews */
        Route::name('student.news.')->namespace('Student')->prefix('news')->group(function () {
            Route::get('/{info_type}', 'InfoNewsController@show')
                ->where('info_type', 'important|other')
                ->name('show');

            Route::get('/details/{info_id}', 'InfoNewsController@detailsShow')->name('details.show');
        });
        /* End InfoNews */

    //Mobile
    Route::group(['prefix' => 'confirm', 'namespace' => 'Student'], function () {
        Route::get('/mobile', 'StudentController@confirmMobile')->name('studentMobileConfirm');
    });
    Route::post('/profile/mobile', 'ProfileController@profileMobile')->name('profileMobile');
    Route::post('/profile/mobile/approve', 'ProfileController@profileMobileApprove')->name('profileMobileApprove');
    Route::post('/profile/mobile/approve/double', 'ProfileController@profileMobileDoubleApprove')->name('profileMobileDoubleApprove');

        /* Chat */
        Route::get('chat', 'ChatController@studentChatView')
            ->middleware(['auth', 'visitedPage:Чат'])
            ->name('openChat');
        Route::any('contactsInfo', 'ChatController@contactsInfo')->name('contactsInfo');
        Route::post('peer/set', 'ChatController@setPeerId')->name('chatSetPeer');
        Route::post('peer/get', 'ChatController@getPeerId')->name('chatGetPeer');
        Route::post('caller/get', 'ChatController@getCallerById')->name('chatGetCallerById');
        Route::post('chat/webcam/post', 'ChatController@webcamPost')
            ->name('chatWebcamPost');
        Route::post('chat/user/getId', 'ChatController@getCurrentUser');

});

	/*Route::group(['middleware' => ['admin'], 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
		
		Route::get('/', 'IndexController@index');
		Route::post('login', 'IndexController@postLogin');
		Route::get('logout', 'IndexController@logout');

    });*/
    Route::group(['prefix' => 'forum', 'namespace' => 'Student'], function (){
        Route::get('/', 'ChatterController@index')
            ->middleware(['auth', 'visitedPage:Форум'])
            ->name('chatter.home');

        Route::get('/category/{slug}','ChatterController@index')
            ->middleware('auth')
            ->name('chatter.category.show');

        Route::get('/getCategories', 'ChatterController@getCategories')
            ->name('chatter.categories');

        Route::post('/getAllCategories', 'ChatterController@getAllCategories')
            ->name('chatter.getAllCategories');

        Route::any('/user/ban', 'ChatterController@ban')
            ->middleware('hasRight:forum,edit')
            ->name('chatter.user.ban');

        Route::delete('/post/{id}', 'ChatterController@destroyPost')
            ->name('chatter.posts.destroy');

        Route::match(['PUT', 'PATCH'],'/post/{id}', 'ChatterController@updatePost')
            ->name('chatter.posts.edit');
    });

});

Route::group(['prefix' => 'pay'], function () {
    Route::post('auth_result/success', 'Student\PayController@authResultSuccess');
    Route::post('auth_result/fail', 'Student\PayController@authResultFail');
    Route::post('test/cloudpay', 'Student\PayController@payResultTestCloudpay')->name('studentPayResultTestCloudpay');

    Route::post('result/cloudpay', 'Student\PayController@payToBalance')->name('studentPayResultTestCloudpay');
});

Route::post('/webcam/post', 'Student\WebcamController@index')
        ->name('webcam.index');


Route::get('pay_memo', 'IndexController@payMemo')->name('payMemo');

//for testing EKT, will be deleted soon
Route::get('/ekt', 'Student\BcApplicationController@importEntGet');


