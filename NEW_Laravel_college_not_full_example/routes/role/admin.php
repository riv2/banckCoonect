<?php

use Illuminate\Support\Facades\Route;

Route::domain('admin.' . str_replace(['http://', 'https://', 'www.', '/'], '', config('app.url')))
    ->group(function() {
        Route::group(['namespace' => 'Admin'], function() {
            Route::get('/', 'IndexController@index');
            Route::post('login', 'IndexController@postLogin');

            Route::group(['middleware' => ['auth', 'hasAccess:admin']], function () {
                Route::get('logout', 'IndexController@logout');
                Route::get('dashboard', 'IndexController@dashboard')->name('dashboard');

                Route::get('profile', 'AdminController@profile');
                Route::post('profile', 'AdminController@updateProfile');
                Route::post('profile_pass', 'AdminController@updatePassword');

                /* Users */
                Route::group(['prefix' => 'users'], function() {
                    Route::get('/', 'UsersController@userslist')
                        ->name('users')
                        ->middleware(['hasRight:users,read']);

                    Route::get('/add', 'UsersController@addeditUser')
                        ->name('userAdd')
                        ->middleware(['hasRight:users,create']);

                    Route::post('/add', 'UsersController@addnew')
                        ->name('POSTUserAdd')
                        ->middleware(['hasRightIn:users,create edit']);

                    Route::get('/edit/{id}', 'UsersController@editUser')
                        ->name('userEdit')
                        ->middleware(['hasRight:users,edit']);

                    Route::get('/delete/{id}', 'UsersController@delete')
                        ->name('userDelete')
                        ->middleware(['hasRight:users,delete']);

                    Route::post('/list/ajax', 'UsersController@getListAjax')
                        ->name('adminUserListAjax')
                        ->middleware(['hasRight:users,read']);

                    Route::post('documentslist/{id}', 'UsersController@adminDocsUploadPost')
                        ->name('adminDocsUploadPost')
                        ->middleware(['hasRight:students,upload_student_docs']);

                    Route::get('getuserdocumentslist/{id}', 'UsersController@adminGetUserDocsList')
                        ->name('adminGetUserDocsList')
                        ->middleware(['hasRight:students,read']);

                    Route::post('setdocumentstatus/{id}', 'UsersController@adminDocsSetStatus')
                        ->name('adminDocsSetStatus')
                        ->middleware(['hasRight:students,upload_student_docs']);

                });

                /* Guests */
                Route::group(['prefix' => 'guests'], function() {
                    Route::get('/', 'GuestController@adminList')
                        ->name('adminGuests')
                        ->middleware(['hasRight:guests,read']);

                    Route::get('/delete/{id}', 'GuestController@delete')
                        ->name('guestDelete')
                        ->middleware(['hasRight:guests,delete']);

                    Route::post('/list/ajax', 'GuestController@getAdminListAjax')
                        ->name('adminGuestListAjax')
                        ->middleware(['hasRight:guests,read']);
                });

                /* Teachers */
                Route::group(['prefix' => 'teachers'], function() {
                    Route::get('/', 'TeachersController@getList')
                        ->name('adminTeacherList')
                        ->middleware(['hasRight:teachers,read']);

                    Route::get('/{id}', 'TeachersController@edit')
                        ->name('adminTeacherEdit')
                        ->middleware(['hasRight:teachers,edit']);

                    Route::post('/{id}', 'TeachersController@editPost')
                        ->middleware(['hasRight:teachers,edit']);

                    Route::get('/delete/{id}', 'TeachersController@delete')
                        ->name('adminTeacherDelete')
                        ->middleware(['hasRight:teachers,delete']);

                    Route::post('group/list/by/disciplines', 'TeachersController@groupListByDisciplines')
                        ->name('groupListByDisciplinesAjax')
                        ->middleware(['hasRight:teachers,edit']);
                });

                /* News */
                Route::group(['prefix' => 'news'], function() {
                    Route::get('/', 'NewsController@getList')
                        ->name('adminNewsList')
                        ->middleware(['hasRight:news,read']);

                    Route::get('/{id}', 'NewsController@edit')
                        ->name('adminNewsEdit')
                        ->middleware(['hasRight:news,edit']);

                    Route::post('/{id}', 'NewsController@editPost')
                        ->middleware(['hasRight:news,edit']);

                    Route::get('/delete/{id}', 'NewsController@delete')
                        ->name('adminNewsDelete')
                        ->middleware(['hasRight:news,delete']);
                });

                /* Student */
                Route::group(['prefix' => 'students'], function() {
                    Route::get('/', 'StudentController@getList')
                        ->name('adminStudentList')
                        ->middleware(['hasRight:students,read']);

                    Route::post('/list/ajax', 'StudentController@getListAjax')
                        ->name('adminStudentListAjax')
                        ->middleware(['hasRight:students,read']);

                    Route::post('/disciplines', 'StudentController@disciplineMigration')
                        ->name('adminStudentDisciplineMigration')
                        ->middleware(['hasRight:students,edit']);

                    Route::post('/submodule_disciplines', 'StudentController@submoduleDisciplineMigration')
                        ->name('adminStudentSubmoduleDisciplineMigration')
                        ->middleware(['hasRight:students,edit']);

                    Route::post('/setFreeCredits', 'StudentController@setFreeCredits')
                        ->name('adminStudentDisciplineSetFreeCredits')
                        ->middleware(['hasRight:students,edit']);

                    Route::get('/{id}', 'StudentController@edit')
                        ->name('adminStudentEdit')
                        ->middleware(['hasRight:students,read']);

                    Route::any('/get_nobd_data_user', 'NoBDDataController@getDataByUserId')
                        ->middleware(['hasRight:students,read'])
                        ->name('adminNobddataGetDataByUserId');

                    Route::any('/render_user_pc', 'NoBDDataController@renderNobdUserPc')
                        ->middleware(['hasRight:students,read'])
                        ->name('adminNobddataRenderNobdUserPc');


                    Route::post('/{id}', 'StudentController@editPost')
                        ->middleware(['hasRight:students,edit']);

                    Route::post('/{id}/change/ignoredebt', 'StudentController@changeIgnoreDebt')
                        ->middleware(['hasRight:students,edit'])->name('adminStudentChangeIgnoreDebt');

                    Route::post('/{id}/result/delete', 'StudentController@deleteResult')
                        ->middleware(['hasRight:students,edit'])->name('adminStudentDeleteResult');

                    Route::post('/{id}/refresh/ent', 'StudentController@refreshEnt')
                        ->middleware(['hasRight:students,edit'])->name('adminStudentRefreshEnt');

                    Route::post('/delete/ajax', 'UsersController@deleteAjax')
                        ->middleware(['hasRight:students,edit'])->name('adminStudentDeleteAjax');


                    Route::post('/{id}/kge/result/delete', 'StudentController@deleteResultKge')
                        ->middleware(['hasRight:students,edit'])->name('adminStudentDeleteResultKge');

                    Route::get('/{id}/result/{disciplineId}', 'StudentController@showResult')
                        ->middleware(['hasRight:students,read'])->name('adminStudentShowResult');

                    Route::get('/{id}/result/exam/{disciplineId}', 'StudentController@showResultExam')
                        ->middleware(['hasRight:students,read'])->name('adminStudentShowResultExam');

                    Route::get('/delete/{id}', 'StudentController@delete')
                        ->name('adminStudentDelete')
                        ->middleware(['hasRight:students,delete']);

                    Route::post('/comment/add', 'StudentController@addComment')
                        ->name('adminStudentAddComment')
                        ->middleware(['hasRight:students,create_student_comment']);

                    Route::get('/print/education_note_list/{id}', 'StudentController@generateNoteEducationDocument')->name('StudentgenerateNoteEducationDocument');
                    Route::get('/print/education_opis_list/{id}', 'StudentController@generateOpisEducationDocument')->name('StudentgenerateOpisEducationDocument');
                    Route::get('/print/title_list/{id}', 'StudentController@generateTitleList')->name('StudentgenerateTitleList');

                    Route::post('/ajax/get/user/balance', 'StudentController@ajaxGetUserBalance')->name('StudentAjaxGetUserBalance');

                    Route::get('print/education_statement/{id}', 'StudentController@printEducationStatement')
                    ->name('userPrintEducationStatement')
                    ->middleware(['hasRight:students,read']);

                    Route::get('print/education_contract/{id}', 'StudentController@generateEducationContract')
                        ->name('userGenerateEducationContract')
                        ->middleware(['hasRight:students,read']);

                    Route::get('print/transcript/{id}', 'StudentController@genTranscript')
                        ->name('adminStudentGenTranscript')
                        ->middleware(['hasRight:students,read']);
                });

                Route::group(['prefix' => 'or_cabinet'], function() {
                    Route::get('/', 'MatriculantController@getList')
                        ->name('adminMatriculantstList')
                        ->middleware(['hasRight:or_cabinet,read']);

                    Route::post('/list/ajax/{category}', 'MatriculantController@getListAjax')
                        ->name('adminMatriculantsListAjax')
                        ->middleware(['hasRight:or_cabinet,read']);

                    Route::post('/notification/send', 'NotificationController@send')
                        ->name('adminSendNotification')
                        ->middleware(['hasRight:or_cabinet,read']);

                    Route::post('/order/attach_users', 'OrderController@attachUsers')
                        ->name('adminOrderAttachUsers')
                        ->middleware(['hasRight:or_cabinet,read']);

                    Route::post('/matriculants/change/checklevel', 'MatriculantController@changeCheckLevel')
                        ->name('adminMatriculantChangeCheckLevel')
                        ->middleware(['hasRight:or_cabinet,edit']);

                    Route::post('/ajax/get/user/data', 'MatriculantController@ajaxGetUsersDataByIds')
                        ->name('adminMatriculantAjaxGetUserData');

                    Route::post('/ajax/attach/service', 'MatriculantController@ajaxAttachService')
                        ->name('adminMatriculantAjaxAttachService')
                        ->middleware(['hasRight:students,add_aditional_service_to_user']);

                    Route::post('/ajax/audit/list', 'MatriculantController@ajaxGetAuditList')
                        ->name('adminMatriculantAjaxAuditList');

                    Route::post('/move_to_inspection', 'MatriculantController@moveToInspection')
                        ->name('adminMatriculantMoveToInspection')
                        ->middleware(['hasRight:or_cabinet,edit']);

                    Route::post('/ajax/set_buying', 'MatriculantController@setBuying')
                        ->name('adminMatriculantSetBuying')
                        ->middleware(['hasRight:or_cabinet,set_pay_in_orcabinet']);

                });

                /* Specialities */
                Route::group(['prefix' => 'specialities'], function() {
                    Route::get('/', 'SpecialitiesController@list')
                        ->name('adminSpecialityList')
                        ->middleware(['hasRight:specialities,read']);

                    Route::get('/add', 'SpecialitiesController@add')
                        ->name('specialityAdd')
                        ->middleware(['hasRight:specialities,create']);

                    Route::post('/add', 'SpecialitiesController@addedit')
                        ->name('POSTSpecialityAdd')
                        ->middleware(['hasRightIn:specialities,create edit']);
                    
                    Route::post('/disciplinesAdd', 'SpecialitiesController@disciplineAdd')
                        ->name('POSTDisciplineAdd_v2')
                        ->middleware(['hasRightIn:specialities,create edit']);
                    Route::post('/disciplinesEdit', 'SpecialitiesController@disciplineEdit')
                        ->name('POSTDisciplineEdit_v2')
                        ->middleware(['hasRightIn:specialities,create edit']);
                    Route::post('/disciplinesDelete', 'SpecialitiesController@disciplineDelete')
                        ->name('POSTDisciplineDelete_v2')
                        ->middleware(['hasRightIn:specialities,create edit']);                    

                    Route::post('/modules/disciplines', 'SpecialitiesController@getModuleDisciplinesTable')
                        ->name('getModuleDisciplinesTable')
                        ->middleware(['hasRightIn:specialities,read']);

                    Route::post('/disciplines/all', 'SpecialitiesController@getAllDisciplinesTable')
                        ->name('getAllDisciplinesTable')
                        ->middleware(['hasRightIn:specialities,read']);

                    Route::get('/edit/{id}', 'SpecialitiesController@edit')
                        ->name('specialityEdit')
                        ->middleware(['hasRight:specialities,read']);

                    Route::get('/delete/{id}','SpecialitiesController@delete')
                        ->name('specialityDelete')
                        ->middleware(['hasRight:specialities,delete']);

                    Route::get('{specialityId}/export/etc/pdf','SpecialitiesController@exportKgePdf')
                        ->name('adminExportKgePdf')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/list/ajax', 'SpecialitiesController@getListAjax')
                        ->name('adminSpecialityListAjax')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/module/list/ajax', 'SpecialitiesController@ajaxGetListForSpecialityEdit')
                        ->name('adminAjaxGetListForSpecialityEdit')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/speciality/discipline/semesters/{specialityId?}/{disciplineId?}', 'SpecialitiesController@getSpecialityDisciplineSemester')
                        ->name('adminAjaxGetSpecialityDisciplineSemester')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/speciality/discipline/semester/add', 'SpecialitiesController@addSpecialityDisciplineSemester')
                        ->name('adminAjaxAddSpecialityDisciplineSemester')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/speciality/discipline/dependence/table/{specialityId?}/{disciplineId?}', 'SpecialitiesController@getDisciplineDependenceTable')
                        ->name('adminAjaxGetListDependenceForDiscipline')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/speciality/discipline/dependence/add', 'SpecialitiesController@addSpecialityDisciplineDependence')
                        ->name('adminAddSpecialityDisciplineDependence')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/speciality/discipline/dependence/delete/{specialityDisciplineDependenceId?}', 'SpecialitiesController@deleteSpecialityDisciplineDependence')
                        ->name('adminDeleteSpecialityDisciplineDependence')
                        ->middleware(['hasRight:specialities,read']);

                    Route::post('/speciality/discipline/dependence/save/{specialityDisciplineDependenceId?}', 'SpecialitiesController@saveSpecialityDisciplineDependence')
                        ->name('adminSaveSpecialityDisciplineDependence')
                        ->middleware(['hasRight:specialities,read']);
                });

                /* Speciality Prices */
                Route::group(['prefix' => 'speciality_prices'], function() {
                    Route::get('/', 'SpecialityPricesController@list')
                        ->name('adminSpecialityPricesList')
                        ->middleware(['hasRight:speciality_prices,read']);

                    Route::post('/save', 'SpecialityPricesController@save')
                        ->name('adminAjaxSaveSpecialityPrice')
                        ->middleware(['hasRight:speciality_prices,edit']);

                    Route::get('/info/{id}', 'SpecialityPricesController@info')
                        ->name('adminAjaxInfoSpecialityPrice')
                        ->middleware(['hasRight:speciality_prices,read']);
                });

                /* Orders */
                Route::group(['prefix' => 'orders'], function() {
                    Route::get('/', 'OrderController@getList')
                        ->name('adminOrderList')
                        ->middleware(['hasRight:orders,read']);

                    Route::post('/detach_users', 'OrderController@detachUsers')
                        ->name('adminOrderDetachUsers')
                        ->middleware(['hasRight:orders,edit']);

                    Route::get('/edit/{id}', 'OrderController@edit')
                        ->name('adminOrderEdit')
                        ->middleware(['hasRight:orders,read']);

                    Route::post('/edit/{id}', 'OrderController@editPost')
                        ->name('adminOrderEditPost')
                        ->middleware(['hasRight:orders,edit']);

                    Route::post('/add/signature/{id}', 'OrderController@addSignature')
                        ->name('adminAddOrderSignature')
                        ->middleware(['hasRight:orders,edit']);

                    Route::get('/delete/{id}','OrderController@delete')
                        ->name('adminOrderDelete')
                        ->middleware(['hasRight:orders,delete']);

                    Route::get('/print/{id}', 'OrderController@printOrder')
                        ->name('adminPrintOrder');
                });

                /* Nomenclature */
                Route::group(['prefix' => 'nomenclature'], function(){
                    Route::get('/{years?}', 'NomenclatureController@index')
                        ->name('nomenclature.page')
                        ->middleware(['hasRight:nomenclature,read', 'nomenclatureDate']);

                    Route::post('add/folder', 'NomenclatureController@addFolder')
                        ->name('add.folder.to.nomenclature')
                        ->middleware(['hasRight:nomenclature,create']);

                    Route::post('get/folder/content', 'NomenclatureController@getFolderContent')
                        ->name('nomenclature.get.folder.content')
                        ->middleware(['hasRight:nomenclature,read']);

                    Route::post('add/file/to/folder', 'NomenclatureController@addFileToFolder')
                        ->name('nomenclature.add.file.to.folder')
                        ->middleware(['hasRight:nomenclature,create']);

                    Route::get('download/file/{fileName?}', 'NomenclatureController@downloadFile')
                        ->name('nomenclature.download.file')
                        ->middleware(['hasRight:nomenclature,read']);

                    Route::post('upload/file', 'NomenclatureController@uploadFile')
                        ->name('nomenclature.upload.file')
                        ->middleware(['hasRight:nomenclature,create']);

                    Route::post('upload/new_template_file', 'NomenclatureController@uploadTemplateFile')
                        ->name('nomenclature.upload.template.file')
                        ->middleware(['hasRight:nomenclature,edit']);

                    Route::post('auditor/check', 'NomenclatureController@auditorCheck')
                        ->name('nomenclature.auditor.check')
                        ->middleware(['hasRight:nomenclature,edit']);

                    Route::post('user/vote', 'NomenclatureController@vote')
                        ->name('nomenclature.user.vote')
                        ->middleware(['hasRight:nomenclature,edit']);

                    Route::get('delete/folder/{id?}', 'NomenclatureController@deleteFolder')
                        ->name('nomenclature.delete.folder')
                        ->middleware(['hasRight:nomenclature,delete']);

                    Route::get('delete/template/{id?}', 'NomenclatureController@deleteTemplate')
                        ->name('nomenclature.delete.template')
                        ->middleware(['hasRight:nomenclature,delete']);

                    Route::get('delete/file/{name?}', 'NomenclatureController@deleteFile')
                        ->name('nomenclature.delete.file')
                        ->middleware(['hasRight:nomenclature,delete']);

                    Route::post('edit/name', 'NomenclatureController@editName')
                        ->name('nomenclature.edit.name');
                });

                /* Library */
                Route::group(['prefix' => 'library'], function(){
                    Route::get('/', 'LibraryController@index')
                        ->name('library.page')
                        ->middleware(['hasRight:library,read']);

                    Route::post('/catalog/datatable', 'LibraryController@catalogDatatable')
                        ->name('library.catalog.datatable');

                    Route::get('add/literature/to/catalog/{id?}', 'LibraryController@addLiteraturePage')
                        ->name('add.literature.to.catalog.page')
                        ->middleware(['hasRight:library,read']);

                    Route::post('discipline/datatable', 'LibraryController@disciplineDatatable')
                        ->name('library.catalog.discipline.datatable');

                    Route::post('add/literature/to/catalog', 'LibraryController@addLiterature')
                        ->name('add.literature.to.catalog')
                        ->middleware(['hasRight:library,create']);

                    Route::post('edit/literature/to/catalog', 'LibraryController@editLiterature')
                        ->name('edit.literature.to.catalog')
                        ->middleware(['hasRight:library,create']);
                    
                    Route::get('knowledge/section/page', 'LibraryController@knowledgeSectionPage')
                        ->name('knowledge.section.page')
                        ->middleware(['hasRight:library,read']);

                    Route::post('add/record/to/knowledge/section', 'LibraryController@addRecordToKnowledgeSection')
                        ->name('add.record.to.knowledge.page')
                        ->middleware(['hasRight:library,create']);

                    Route::post('literature/search', 'LibraryController@liveSearch')
                        ->name('syllabus.literature.live.search');

                    Route::get('literature/statistic', 'LibraryController@statisticPage')
                        ->name('literature.statistic.page')
                        ->middleware(['hasRight:library,read']);

                    Route::post('literature/statistic/chart', 'LibraryController@statisticChart')
                        ->name('library.statistic.chart');

                    Route::post('literature/downloads/statistic/datatable', 'LibraryController@downloadsStatisticDatatable')
                        ->name('library.downloads.statistic.datatable');

                    Route::post('literature/reports/datatable', 'LibraryController@reportsDatatable')
                        ->name('library.reports.datatable');

                    Route::get('literature/reports', 'LibraryController@reportsPage')
                        ->name('literature.reports.page')
                        ->middleware(['hasRight:library,read']);

                    Route::get('library/reports/set/status/{id}/{status}', 'LibraryController@setStatus')
                        ->name('library.report.set.status')
                        ->middleware(['hasRight:library,edit']);

                    Route::get('download/file/{name}', 'LibraryController@downloadFile')
                        ->name('library.download.file')
                        ->middleware(['hasRight:library,read']);

                    Route::get('delete/catalog/{id}', 'LibraryController@deleteCatalog')
                        ->name('library.delete.catalog')
                        ->middleware(['hasRight:library,delete']);
                });

                /* Manual */
                Route::group(['prefix' => 'manual'], function(){
                    Route::get('/', 'ManualController@index')
                        ->name('manualHome')
                        ->middleware(['hasRight:manuals,read']);

                    Route::get('/add/{name}/note', 'ManualController@addNotePage')
                        ->name('manualAddNotePage')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::post('/manual/shedule/datatable', 'ManualController@sheduleDatatable')
                        ->name('manualSheduleDatatable');

                    Route::post('/manual/add/note/shedule', 'ManualController@addNoteShedule')
                        ->name('admin.manual.add.note.shedule')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/shedule/{id}', 'ManualController@deleteNoteShedule')
                        ->name('admin.manual.delete.note.shedule')
                        ->middleware(['hasRight:manuals,delete']);

                    Route::post('/manual/nationality/datatable', 'ManualController@nationalityDatatable')
                        ->name('manualNationalityDatatable');

                    Route::post('/manual/add/note/nationality', 'ManualController@addNoteNationality')
                        ->name('admin.manual.add.note.nationality')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/nationality/{id}', 'ManualController@deleteNoteNationality')
                        ->name('admin.manual.delete.note.nationality')
                        ->middleware(['hasRight:manuals,delete']);

                    Route::post('/manual/citizenship/datatable', 'ManualController@citizenshipDatatable')
                        ->name('manualCitizenshipDatatable');

                    Route::post('/manual/add/note/citizenship', 'ManualController@addNoteCitizenship')
                        ->name('admin.manual.add.note.citizenship')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/citizenship/{id}', 'ManualController@deleteNoteCitizenship')
                        ->name('admin.manual.delete.note.citizenship')
                        ->middleware(['hasRight:manuals,delete']);

                    Route::post('/manual/docs/datatable', 'ManualController@issuingDocsDatatable')
                        ->name('manual.issuing.docs.datatable');

                    Route::post('/manual/add/note/issuing/docs', 'ManualController@addNoteIssuingDocs')
                        ->name('admin.manual.add.note.issuing.docs')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/issuing/docs/{id}', 'ManualController@deleteNoteIssuingDocs')
                        ->name('admin.manual.delete.note.issuing.docs')
                        ->middleware(['hasRight:manuals,delete']);

                    Route::post('/manual/education/datatable', 'ManualController@educationDatatable')
                        ->name('manual.education.datatable');

                    Route::post('/manual/add/note/education', 'ManualController@addNoteEducation')
                        ->name('admin.manual.add.note.education')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/education/{id}', 'ManualController@deleteNoteEducation')
                        ->name('admin.manual.delete.note.education')
                        ->middleware(['hasRight:manuals,delete']);

                    Route::post('/manual/perks/datatable', 'ManualController@perksDatatable')
                        ->name('manual.perks.datatable');

                    Route::post('/manual/add/note/perks', 'ManualController@addNotePerks')
                        ->name('admin.manual.add.note.perks')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/perks/{id}', 'ManualController@deleteNotePerks')
                        ->name('admin.manual.delete.note.perks')
                        ->middleware(['hasRight:manuals,delete']);

                    Route::post('/manual/organizations/datatable', 'ManualController@organizationsDatatable')
                        ->name('manual.organizations.datatable');

                    Route::post('/manual/add/note/organizations', 'ManualController@addNoteOrganizations')
                        ->name('admin.manual.add.note.organizations')
                        ->middleware(['hasRight:manuals,edit']);

                    Route::get('/manual/delete/note/organizations/{id}', 'ManualController@deleteNoteOrganizations')
                        ->name('admin.manual.delete.note.organizations')
                        ->middleware(['hasRight:manuals,delete']);
                });

                /* NOBD */
                Route::group(['prefix' => 'nobd'], function() {
                    Route::get('/', 'NOBDController@index')
                        ->name('NOBDindex');

                    Route::get('/download/students/list', 'NOBDController@downloadList')
                        ->name('downloadStudentsList');

                    Route::post('/upload/students/list', 'NOBDController@uploadList')
                        ->name('uploadStudentsList');
                });

                /* Employees */
                Route::group(['prefix' => 'employees'], function() {

                    /* Отделы */
                    Route::get('/department', 'EmployeesController@department')
                        ->name('employeesDepartment')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/add/new/department', 'EmployeesController@addNewDepartment')
                        ->name('addNewDepartment')
                        ->middleware(['hasRight:employees,create']);

                    Route::post('/get/department', 'EmployeesController@getDepartment')
                        ->name('getDepartment')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/edit/department', 'EmployeesController@editDepartment')
                        ->name('editDepartment')
                        ->middleware(['hasRight:employees,edit']);

                    Route::post('/department/datatable', 'EmployeesController@departmentDatatable')
                        ->name('departmentDatatable');

                    Route::get('/delete/department/{id}', 'EmployeesController@deleteDepartment')
                        ->name('delete.department')
                        ->middleware(['hasRight:employees,delete']);

                    /* Должности */
                    Route::get('/position', 'EmployeesPositionController@index')
                        ->name('employeesPosition')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/position/datatable', 'EmployeesPositionController@positionDatatable')
                        ->name('positionDatatable');

                    Route::post('/get/position', 'EmployeesPositionController@getPosition')
                        ->name('getPosition')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/add/new/position', 'EmployeesPositionController@addNewPosition')
                        ->name('addNewPosition')
                        ->middleware(['hasRight:employees,create']);

                    Route::post('/edit/position', 'EmployeesPositionController@editPosition')
                        ->name('editPosition')
                        ->middleware(['hasRight:employees,edit']);

                    Route::get('/delete/position/{id}', 'EmployeesPositionController@deletePosition')
                        ->name('delete.position')
                        ->middleware(['hasRight:employees,delete']);

                    /* Вакансии */
                    Route::get('/vacancy', 'EmployeesVacancyController@index')
                        ->name('employeesVacancy')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/vacancy/datatable', 'EmployeesVacancyController@vacancyDatatable')
                        ->name('employees.vacancy.datatable');

                    Route::get('vacancy/delete/{id}', 'EmployeesVacancyController@deleteVacancy')
                        ->name('employees.delete.vacantion')
                        ->middleware(['hasRight:employees,delete']);

                    Route::post('vacancy/add/new', 'EmployeesVacancyController@addVacancy')
                        ->name('employees.add.new.vacancy')
                        ->middleware(['hasRight:employees,create']);

                    /* Сотрудники */
                    Route::get('/users', 'EmployeesUsersController@index')
                        ->name('employeesUsers')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('users/datatable', 'EmployeesUsersController@employeesDatatable')
                        ->name('employeesUsersDatatable');

                    Route::get('/user/{id?}', 'EmployeesUsersController@editEmployeePage')
                        ->name('addNewEmployee')
                        ->middleware(['hasRight:employees,read']);

                    Route::get('user/{id}/positions', 'EmployeesUsersController@userPositionsPage')
                        ->name('employees.user.positions.page')
                        ->middleware(['hasRight:employees,read']);

                    Route::get('employees/social/package/{id}', 'EmployeesUsersController@userSocialPackage')
                        ->name('employees.user.social.package')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('employees/edit/social/package', 'EmployeesUsersController@editUserSocialPackage')
                        ->name('employees.user.edit.social.package')
                        ->middleware(['hasRight:employees,edit']);

                    Route::post('user/positions/datatable', 'EmployeesUsersController@userPositionsDatatable')
                        ->name('employees.user.positions.datatable');

                    Route::post('user/edit/link/positions', 'EmployeesUsersController@editLinkUserPosition')
                        ->name('employees.user.edit.link.position')
                        ->middleware(['hasRight:employees,edit']);

                    Route::get('user/edit/position/{user_id}/{position_id?}', 'EmployeesUsersController@editUserPositionPage')
                        ->name('employees.user.edit.position')
                        ->middleware(['hasRight:employees,read']);

                    Route::get('user/delete/positions/{id}', 'EmployeesUsersController@userDeletePosition')
                        ->name('employees.user.delete.position')
                        ->middleware(['hasRight:employees,delete']);

                    Route::post('user/create/employees', 'EmployeesUsersController@createEmployees')
                        ->name('user.create.employees')
                        ->middleware(['hasRight:employees,create']);

                    Route::post('user/edit/employees', 'EmployeesUsersController@editEmployees')
                        ->name('user.edit.employees')
                        ->middleware(['hasRight:employees,edit']);

                    Route::post('/get/position/requirements', 'EmployeesUsersController@getPositionRequirements')
                        ->name('get.position.requirements');

                    Route::post('/get/user/position/requirements', 'EmployeesUsersController@getUserPositionRequirements')
                        ->name('get.user.position.requirements');

                    Route::post('/users/search/user', 'EmployeesUsersController@searchUser')
                        ->name('usersSearchUser')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/users/get/data', 'EmployeesUsersController@getUserData')
                        ->name('usersGetData')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/user/{id}/deleteEmployeesDiscipline/', 'EmployeesUsersController@deleteEmployeesDiscipline')
                        ->name('employees.user.delete.discipline')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/user/{id}/saveDisciplines/', 'EmployeesUsersController@saveDisciplines')
                        ->name('employees.user.save.disciplines')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/user/{id}/addDisciplines/', 'EmployeesUsersController@addDisciplines')
                        ->name('employees.user.add.disciplines')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/user/{id}/disciplinesList/{sectorId}', 'EmployeesUsersController@userDisciplinesList')
                        ->name('employees.user.disciplinesList')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/user/{id}/disciplines/', 'EmployeesUsersController@userDisciplinesDatatable')
                        ->name('employees.user.disciplinesDataTable')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/checkPositionDisciplines/{position}', 'EmployeesUsersController@checkPositionDisciplines')
                        ->name('employees.checkPositionDisciplines')
                        ->middleware(['hasRight:employees,read']);

                    /* Candidate */
                    Route::get('/candidates', 'EmployeesUsersController@candidatesPage')
                        ->name('employees.candidates')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/candidates/datatable', 'EmployeesUsersController@candidatesDatatable')
                        ->name('employees.candidate.datatable');

                    Route::get('/candidates/show/resume/{id}/{type?}', 'EmployeesUsersController@candidatesShowResume')
                        ->name('employees.show.candidate.resume')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('/candidates/verdict/candidate/resume', 'EmployeesUsersController@verdictCandidatesResume')
                        ->name('employees.verdict.candidate.resume')
                        ->middleware(['hasRight:employees,create']);

                    Route::post('/candidates/verdict/candidate/interview', 'EmployeesUsersController@verdictCandidatesInterview')
                        ->name('employees.verdict.candidate.interview')
                        ->middleware(['hasRight:employees,create']);

                    Route::get('/requirements/download/file/{name}', 'EmployeesUsersController@downloadFile')
                        ->name('download.requirement.file')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('add/candidates/to/order', 'EmployeesOrdersController@addCandidatesToOrder')
                        ->name('add.candidates.to.order');

                    /* Аудит */
                    Route::get('decree/all', 'EmployeesDecreeController@usersDecreePage')
                        ->name('employees.decree.all')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('users/decree/datatable', 'EmployeesDecreeController@usersDecreeDatatable')
                        ->name('employees.users.decree.datatable');

                    Route::get('decree/edit/{id}', 'EmployeesDecreeController@editDecreePage')
                        ->name('employees.decree.edit')
                        ->middleware(['hasRight:employees,read']);

                    Route::get('download/decree/{name}', 'EmployeesDecreeController@downloadDecree')
                        ->name('download.decree')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('upload/decree', 'EmployeesDecreeController@uploadDecree')
                        ->name('upload.decree')
                        ->middleware(['hasRight:employees,read']);

                    /* Приказы */
                    Route::get('orders', 'EmployeesOrdersController@index')
                        ->name('employees.orders.page')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('orders/datatable', 'EmployeesOrdersController@ordersDatatable')
                        ->name('orders.datatable');

                    Route::get('edit/order/{id?}', 'EmployeesOrdersController@editOrderPage')
                        ->name('employees.edit.order')
                        ->middleware(['hasRight:employees,read']);

                    Route::get('delete/order/{id?}', 'EmployeesOrdersController@deleteOrderPage')
                        ->name('employees.delete.order')
                        ->middleware(['hasRight:employees,edit']);

                    Route::get('order/to/agreement/{id?}', 'EmployeesOrdersController@orderToAgreement')
                        ->name('employees.order.to.agreement')
                        ->middleware(['hasRight:employees,edit']);

                    Route::post('order/create', 'EmployeesOrdersController@createOrder')
                        ->name('employees.create.order')
                        ->middleware(['hasRight:employees,create']);

                    Route::post('add/employees/to/order', 'EmployeesOrdersController@addEmployeesToOrder')
                        ->name('add.employees.to.order');

                    Route::post('edit/employees/order', 'EmployeesOrdersController@editEmployeesOrder')
                        ->name('edit.employees.order');

                    Route::post('edit/order', 'EmployeesOrdersController@editOrderFile')
                        ->name('edit.order');

                    Route::get('download/order/{name}', 'EmployeesOrdersController@downloadOrder')
                        ->name('employees.download.order')
                        ->middleware(['hasRight:employees,read']);

                    Route::get('order/for/agreement/{id}', 'EmployeesOrdersController@showOrderForAgreement')
                        ->name('order.for.agreement.show')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('orders/agreement/datatable', 'EmployeesOrdersController@ordersAgreementDatatable')
                        ->name('orders.agreement.datatable');

                    Route::post('order/user/vote', 'EmployeesOrdersController@orderVote')
                        ->name('order.agreement.vote')
                        ->middleware(['hasRight:employees,edit']);

                    Route::get('approved/order/{id}', 'EmployeesOrdersController@approvedOrderPage')
                        ->name('approved.order.page')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('get/users/by/date', 'EmployeesOrdersController@positionsByDate')
                        ->name('orders.get.positions.by.date');

                    /* Требования */
                    Route::get('position/requirements/{id}', 'EmployeesRequirementsController@index')
                        ->name('position.requirements.page')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('link/position/requirements', 'EmployeesRequirementsController@linkPositionRequirements')
                        ->name('employees.link.position.requirements')
                        ->middleware(['hasRight:employees,edit']);

                    Route::get('add/new/requirement', 'EmployeesRequirementsController@addNewRequirementPage')
                        ->name('employees.add.new.requirement.page')
                        ->middleware(['hasRight:employees,read']);

                    Route::post('add/new/requirement/field', 'EmployeesRequirementsController@addNewRequirementField')
                        ->name('employees.add.new.requirement.field');

                    Route::post('add/new/requirement', 'EmployeesRequirementsController@addNewRequirement')
                        ->name('employees.add.new.requirement');
                });

                /* Modules */
                Route::group(['prefix' => 'modules'], function() {
                    Route::get('/', 'ModuleController@list')
                        ->name('adminModuleList')
                        ->middleware(['hasRight:modules,read']);

                    Route::get('/edit/{id}', 'ModuleController@info')
                        ->name('adminModuleEdit')
                        ->middleware(['hasRight:specialities,edit']);

                    Route::post('/edit/{id}', 'ModuleController@updatePost')
                        ->name('adminModuleEditPost')
                        ->middleware(['hasRightIn:modules,create edit']);

                    Route::get('/delete/{id}','ModuleController@delete')
                        ->name('adminModuleDelete')
                        ->middleware(['hasRight:modules,delete']);

                    Route::post('/list/ajax', 'ModuleController@getListAjax')
                        ->name('adminModuleListAjax')
                        ->middleware(['hasRight:modules,read']);
                });

                /* Disciplines */
                Route::group(['prefix' => 'disciplines'], function() {
                    Route::get('/', 'DisciplinesController@list')
                        ->name('adminDisciplineList')
                        ->middleware(['hasRight:disciplines,read']);

                    Route::post('/list/ajax', 'DisciplinesController@getListAjax')
                        ->name('adminDisciplineListAjax')
                        ->middleware(['hasRight:disciplines,read']);

                    Route::get('/add', 'DisciplinesController@add')
                        ->name('disciplineAdd')
                        ->middleware(['hasRight:disciplines,create']);

                    Route::post('/add', 'DisciplinesController@addedit')
                        ->name('POSTDisciplineAdd')
                        ->middleware(['hasRightIn:disciplines,create edit']);

                    Route::get('/edit/{id}', 'DisciplinesController@edit')
                        ->name('disciplineEdit')
                        ->middleware(['hasRight:disciplines,edit']);

                    Route::get('/edit/delete/{id}', 'DisciplinesController@delete')
                        ->name('disciplineDelete')
                        ->middleware(['hasRight:disciplines,delete']);

                    Route::post('/ajax/list', 'DisciplinesController@ajaxList')
                        ->name('disciplineAjaxList');

                    Route::post('/groups/json', 'DisciplinesController@groupsJSON')
                        ->name('adminGetDisciplineGroupsJSON');

                    Route::post('/getAllSemesters/{id}', 'DisciplinesController@getAllDisciplineSemesters')
                        ->name('adminDisciplineSemesters');

                    Route::post('/delete/semester/{id}/{semester}', 'DisciplinesController@deleteDisciplineSemester')
                        ->name('adminDeleteDisciplineSemester');
                });

                /*Syllabus*/
                Route::group(['prefix' => 'disciplines/{disciplineId}/themes'], function() {
                    Route::get('/','SyllabusController@getList')
                        ->name('adminSyllabusList')
                        ->middleware(['hasRight:themes,read']);

                    Route::post('/test1','SyllabusController@setTest1')
                        ->name('adminSyllabusTest1Set')
                        ->middleware(['hasRight:themes,edit']);

                    Route::get('/{themeId}/delete','SyllabusController@delete')
                        ->name('adminSyllabusDelete')
                        ->middleware(['hasRight:themes,delete']);

                    Route::post('/hours/edit', 'SyllabusController@updateThemeHours')
                        ->name('disciplines.themes.hours.edit')
                        ->middleware(['hasRight:themes,edit']);

                    Route::get('/export/pdf','SyllabusController@exportPdf')
                        ->name('adminSyllabusExportPdf')
                        ->middleware(['hasRight:themes,read']);

                    Route::get('/export/doc','SyllabusController@exportDoc')
                        ->name('admin.syllabus.export.doc')
                        ->middleware(['hasRight:themes,read']);

                    Route::get('/{themeId}/{language}','SyllabusController@edit')
                        ->name('adminSyllabusEditLang')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::get('/{themeId}','SyllabusController@edit')
                        ->name('adminSyllabusEdit')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/{themeId}', 'SyllabusController@editPost')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/{themeId}/quize','SyllabusController@editQuizePost')
                        ->name('adminSyllabusEditQuize')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/{themeId}/quize/question/save','SyllabusController@saveQuizQuestion')
                        ->name('adminSyllabusSaveQuizQuestion')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/{themeId}/quize/list','SyllabusController@quizList')
                        ->name('adminSyllabusQuizList')
                        ->middleware(['hasRightIn:themes,read']);

                    Route::post('/quize/delete','SyllabusController@deleteQuizePost')
                        ->name('adminSyllabusDeleteQuize')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/copy/to','SyllabusController@copyToLang')
                        ->name('adminSyllabusCopyTheme')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/move/to/module','SyllabusController@moveToModule')
                        ->name('admin.syllabus.theme.move.module')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::post('/question/info','SyllabusController@questionInfo')
                        ->name('adminSyllabusQuestionInfo')
                        ->middleware(['hasRight:themes,read']);

                    Route::post('/add/document','SyllabusController@addDocument' )
                        ->middleware(['hasRight:themes,read'])
                        ->name('adminSyllabusAddDocument');

                    Route::get('/documents/get/listAjax', 'SyllabusController@getDocumentsList')
                        ->middleware(['hasRight:themes,read'])
                        ->name('adminGetDocumentsList');

                    Route::post('/document/delete/{document_id}', 'SyllabusController@deleteDocument')
                        ->middleware(['hasRight:themes,delete'])
                        ->name('adminSyllabusDeleteDocument');

                    Route::post('/description/edit/{lang}', 'SyllabusController@editDescription')
                        ->middleware(['hasRight:themes,delete'])
                        ->name('adminSyllabusEditDescription');

                    Route::post('/rating/limit/update', 'SyllabusController@ratingLimitUpdate')
                        ->middleware(['hasRight:themes,edit'])
                        ->name('adminSyllabusRatingLimitUpdate');

                    Route::post('/group/list', 'SyllabusController@getDayRatingList')
                        ->middleware(['hasRight:themes,edit'])
                        ->name('adminSyllabusLoadRatingLimitsByGroup');


                });

                /* Syllabus Module */
                Route::group(['prefix' => 'disciplines/{disciplineId}/module'], function() {
                    Route::get('/add','SyllabusModuleController@create')
                        ->name('admin.syllabus.module.create')
                        ->middleware(['hasRight:themes,create']);

                    Route::get('/edit/{module_id}','SyllabusModuleController@edit')
                        ->name('admin.syllabus.module.edit')
                        ->middleware(['hasRight:themes,edit']);

                    Route::post('/store/{module_id?}','SyllabusModuleController@store')
                        ->name('admin.syllabus.module.store')
                        ->middleware(['hasRightIn:themes,create edit']);

                    Route::get('/delete/{module_id}','SyllabusModuleController@delete')
                        ->name('admin.syllabus.module.delete')
                        ->middleware(['hasRight:themes,delete']);
                });

                /*Syllabus Task*/
                Route::group(['prefix' => 'syllabustask'], function(){
                    Route::post('/listtask', 'SyllabusTaskController@getList')
                        ->name('adminSyllabusTaskGetList')
                        ->middleware(['hasRight:themes,read']);
                    Route::post('/edittask', 'SyllabusTaskController@editTask')
                        ->name('adminSyllabusEditTask')
                        ->middleware(['hasRight:themes,read']);
                    Route::post('/editquestion', 'SyllabusTaskController@editQuestion')
                        ->name('adminSyllabusTaskEditQuestion')
                        ->middleware(['hasRight:themes,read']);
                    Route::post('/editanswer', 'SyllabusTaskController@editAnswer')
                        ->name('adminSyllabusTaskEditAnswer')
                        ->middleware(['hasRight:themes,read']);
                    Route::post('/deletetask', 'SyllabusTaskController@deleteTask')
                        ->name('adminSyllabusTaskDelete')
                        ->middleware(['hasRight:themes,read']);
                    Route::post('/removequestion', 'SyllabusTaskController@removeQuestion')
                        ->name('adminSyllabusTaskRemoveQuestion')
                        ->middleware(['hasRight:themes,read']);
                    Route::post('/removeanswer', 'SyllabusTaskController@removeAnswer')
                        ->name('adminSyllabusTaskRemoveAnswer')
                        ->middleware(['hasRight:themes,read']);
                });

                /* Courses */
                Route::group(['prefix' => 'courses'], function(){
                    Route::get('/', 'CourseController@getList')
                        ->name('adminCourseList')
                        ->middleware(['hasRight:courses,read']);

                    Route::get('/{id}', 'CourseController@edit')
                        ->name('adminCourseEdit')
                        ->middleware(['hasRightIn:courses,create edit']);

                    Route::post('/{id}', 'CourseController@editPost')
                        ->middleware(['hasRightIn:courses,create edit']);

                    Route::get('/delete/{id}', 'CourseController@delete')
                        ->name('adminCourseDelete')
                        ->middleware(['hasRight:courses,delete']);

                    Route::post('/ajax/get/disciplines', 'CourseController@ajaxSearchDisciplines')
                        ->name('adminCourseAjaxGetDisciplinesList');

                    Route::get('/{id}/theme', 'CourseController@getTopicsList')
                        ->name('adminCourseTopicsList')
                        ->middleware(['hasRight:courses,read']);

                    Route::get('/topic/{id}', 'CourseController@editTopic')
                        ->name('adminCourseEditTopic')
                        ->middleware(['hasRightIn:courses,create edit']);

                    Route::post('/topic/{id}', 'CourseController@editTopicPost')
                        ->middleware(['hasRightIn:courses,create edit']);

                    Route::get('/topic/delete/{id}', 'CourseController@removeTopic')
                        ->name('adminCourseTopicDelete')
                        ->middleware(['hasRightIn:courses,delete']);

                });

                /* Building */
                Route::group(['prefix' => 'buildings'], function() {
                    Route::get('/', 'BuildingController@getList')
                        ->name('adminBuildingList')
                        ->middleware(['hasRight:buildings,read']);

                    Route::get('/{id}', 'BuildingController@edit')
                        ->name('adminBuildingEdit')
                        ->middleware(['hasRightIn:buildings,create edit']);

                    Route::post('/{id}', 'BuildingController@editPost')
                        ->middleware(['hasRightIn:buildings,create edit']);

                    Route::get('/delete/{id}', 'BuildingController@delete')
                        ->name('adminBuildingDelete')
                        ->middleware(['hasRight:buildings,delete']);
                });

                /* Rooms */
                Route::group(['prefix' => 'rooms'], function() {
                    Route::get('/', 'RoomController@getList')
                        ->name('adminRoomList')
                        ->middleware(['hasRight:rooms,read']);

                    Route::get('/{id}', 'RoomController@edit')
                        ->name('adminRoomEdit')
                        ->middleware(['hasRightIn:entrance_tests,create edit']);

                    Route::post('/{id}', 'RoomController@editPost')
                        ->middleware(['hasRightIn:entrance_tests,create edit']);

                    Route::get('/delete/{id}', 'RoomController@delete')
                        ->name('adminRoomDelete')
                        ->middleware(['hasRight:rooms,delete']);
                });

                /* Help request */
                Route::group(['prefix' => 'helps'], function() {
                    Route::get('/', 'HelpController@getList')
                        ->name('adminHelpList')
                        ->middleware(['hasRight:helps,read']);

                    Route::get('/{id}', 'HelpController@info')
                        ->name('adminHelpInfo')
                        ->middleware(['hasRight:helps,edit']);

                    Route::get('/delete/{id}', 'HelpController@delete')
                        ->name('adminHelpDelete')
                        ->middleware(['hasRight:helps,delete']);
                });

                /* Promotions */
                Route::group(['prefix' => 'promotions'], function() {
                    Route::get('/', 'PromotionController@getList')
                        ->name('adminPromotionList')
                        ->middleware(['hasRight:promotions,read']);

                    Route::get('/{id}', 'PromotionController@info')
                        ->name('adminPromotionInfo')
                        ->middleware(['hasRightIn:promotions,edit']);

                    Route::post('/{id}', 'PromotionController@infoPost')
                        ->middleware(['hasRightIn:promotions,edit']);
                });

                /* Discount requests */
                Route::group(['prefix' => 'discountrequests'], function() {
                    Route::get('/', 'DiscountRequestsController@getList')
                        ->name('adminDiscountRequestsList')
                        ->middleware(['hasRight:discountrequests,read']);

                    Route::get('/{discount_id}/{category?}', 'DiscountRequestsController@edit')
                        ->name('adminDiscountRequestsEdit')
                        ->middleware(['hasRight:discountrequests,edit']);

                    Route::get('/add/{user_id}/{discount_type_id}/', 'DiscountRequestsController@add')
                        ->name('adminDiscountRequestsAdd')
                        ->middleware(['hasRight:discountrequests,edit']);

                    Route::post('/set/status', 'DiscountRequestsController@setStatus')
                        ->name('adminDiscountRequestsSetStatus')
                        ->middleware(['hasRight:discountrequests,edit']);

                    Route::post('/list/category/{category_id}', 'DiscountRequestsController@getListByCategory')
                        ->name('adminDiscountRequestsByCategoryAjax')
                        ->middleware(['hasRight:discountrequests,read']);

                    Route::post('/list/ent', 'DiscountRequestsController@getListEnt')
                        ->name('adminDiscountRequestsEntAjax')
                        ->middleware(['hasRight:discountrequests,read']);

                    Route::post('/list/custom', 'DiscountRequestsController@getListCustom')
                        ->name('adminDiscountRequestsCustomAjax')
                        ->middleware(['hasRight:discountrequests,read']);
                });

                /* Discipline pay cancel requests */
                Route::group(['prefix' => 'discipline_pay_cancel'], function() {
                    Route::get('/', 'DisciplinesController@getPayCancelList')
                        ->name('adminDisciplinePayCancelList')
                        ->middleware(['hasRight:discipline_pay_cancel,read']);

                    Route::post('/', 'DisciplinesController@getPayCancelListAjax')
                        ->name('adminDisciplinePayCancelListAjax')
                        ->middleware(['hasRight:discipline_pay_cancel,read']);

                    Route::post('/status/set', 'DisciplinesController@payCancelSetStatus')
                        ->name('adminDisciplinePayCancelChangeStatus')
                        ->middleware(['hasRight:discipline_pay_cancel,edit']);
                });


                /* Trends */
                Route::group(['prefix' => 'trends'], function() {
                    Route::get('/', 'TrendController@getList')
                        ->name('adminTrendList')
                        ->middleware(['hasRight:trends,read']);

                    Route::get('/{id}', 'TrendController@edit')
                        ->name('adminTrendEdit')
                        ->middleware(['hasRightIn:trends,create edit']);

                    Route::post('/{id}', 'TrendController@editPost')
                        ->middleware(['hasRightIn:trends,create edit']);

                    Route::get('/delete/{id}', 'TrendController@delete')
                        ->name('adminTrendDelete')
                        ->middleware(['hasRight:trends,delete']);
                });

                /* Entrance test */
                Route::group(['prefix' => 'entrance_tests'], function() {
                    Route::get('/', 'EntranceTestsController@getList')
                        ->name('adminEntranceTestsList')
                        ->middleware(['hasRight:entrance_tests,read']);

                    Route::get('/{id}','EntranceTestsController@edit')
                        ->name('adminEntranceTestsEdit')
                        ->middleware(['hasRightIn:entrance_tests,create edit']);

                    Route::post('/{id}', 'EntranceTestsController@editPost')
                        ->middleware(['hasRightIn:entrance_tests,create edit'])
                        ->name('adminEntranceTestsEditPost');
                    Route::get('/delete/{id}','EntranceTestsController@delete')
                        ->name('adminEntranceTestsDelete')
                        ->middleware(['hasRight:entrance_tests,delete']);
                });


                /* Roles */
                Route::group(['prefix' => 'roles', 'name' => 'adminRole'], function() {
                    Route::get('/', 'RoleController@getList')
                        ->name('adminRoleList')
                        ->middleware(['hasRight:roles,read']);

                    Route::get('/{id}', 'RoleController@edit')
                        ->name('adminRoleEdit')
                        ->middleware(['hasRightIn:roles,create edit']);

                    Route::post('/{id}', 'RoleController@editPost')
                        ->middleware(['hasRightIn:roles,create edit']);

                    Route::get('/delete/{id}', 'RoleController@delete')
                        ->name('adminRoleDelete')
                        ->middleware(['hasRight:roles,delete']);
                });

                /* Inspection */
                Route::group(['prefix' => 'inspection'], function() {
                    Route::get('/{tab}', 'InspectionController@getList')
                        ->name('adminInspectionList')
                        ->middleware(['hasRight:inspection,read']);

                    Route::post('/bc_application', 'InspectionController@editBcApplicationPost')
                        ->name('adminInspectionBcPost')
                        ->middleware(['hasRight:inspection,edit']);

                    Route::post('/mg_application', 'InspectionController@editMgApplicationPost')
                        ->name('adminInspectionMgPost')
                        ->middleware(['hasRight:inspection,edit']);


                    Route::get('/', 'InspectionController@getMatriculantsList')
                        ->name('adminInspectionMatriculantstList')
                        ->middleware(['hasRight:inspection,read']);

                    Route::post('/matriculants/list/ajax/{category}', 'InspectionController@getMatriculantsListAjax')
                        ->name('adminInspectionMatriculantsListAjax')
                        ->middleware(['hasRight:inspection,read']);

                    Route::post('/matriculants/change/checklevel', 'InspectionController@changeCheckLevel')
                        ->name('adminInspectionChangeCheckLevel')
                        ->middleware(['hasRight:inspection,edit']);

                    Route::post('/notification/send/{users?}/{text?}', 'NotificationController@send')
                        ->name('adminSectionSendNotification')
                        ->middleware(['hasRight:inspection,read']);

                    Route::post('/notification/delete', 'NotificationController@delete')
                        ->name('adminSectionNotificationDelete')
                        ->middleware(['hasRight:inspection,read']);

                    Route::post('/move_to_or', 'InspectionController@moveToOR')
                        ->name('adminInspectionMoveToOR')
                        ->middleware(['hasRight:inspection,edit']);
                });

                /* Export */
                Route::group(['prefix' => 'export'], function() {
                    //Students
                    Route::get('/students', 'ExportController@exportStudentForm')
                        ->name('adminExportStudents')
                        ->middleware(['hasRight:export_students,read']);

                    Route::post('/studentsDate', 'ExportController@exportStudentByDate')
                        ->name('adminExportStudentsByDate')
                        ->middleware(['hasRight:export_students,read']);

                    Route::post('/students', 'ExportController@exportStudentPost')
                        ->middleware(['hasRight:export_students,read']);

                    //Exam results
                    Route::get('/exam/results', 'ExportController@exportExamResult')
                        ->name('adminExportExamResults')
                        ->middleware(['hasRight:export_student_result,read']);

                    Route::post('/exam/results', 'ExportController@exportExamResultPost')
                        ->middleware(['hasRight:export_student_result,read']);

                    // Exam sheets
                    Route::get('/exam_sheet', 'ExportController@examSheetChoose')
                        ->name('adminExportExamSheets')
                        ->middleware(['hasRight:export_exam_sheet,read']);

                    Route::post('/exam_sheet', 'ExportController@exportExamSheet')
                        ->middleware(['hasRight:export_exam_sheet,read']);

                    Route::get('/practice', 'ExportController@exportPractice')
                        ->middleware(['hasRight:discipline_practice_upload,read'])
                        ->name('adminExportPractice');

                    Route::post('/practice', 'ExportController@exportPracticePost')
                        ->middleware(['hasRight:discipline_practice_upload,edit'])
                        ->name('adminExportPracticePost');

                    Route::get('/sro/pay/courses', 'ExportController@exportSROPayCourses')
                        ->name('adminExportSROPayCourses');

                    Route::post('/sro/pay/courses', 'ExportController@exportSROPayCoursesPost')
                        ->name('adminExportSROPayCoursesPost');

                    Route::get('/diplomas', 'ExportController@exportDiplomas')
                        ->middleware(['hasRight:export_diplomas,read'])
                        ->name('adminExportDiplomas');

                    /* Export Activities */
                    Route::get('/activities', 'ExportController@exportActivities')
                        ->middleware(['hasRight:export_activities,read'])
                        ->name('admin.activity.export');
                });

                Route::post('/specialities/by/year', 'SpecialitiesController@listByYear')
                    ->name('adminSpecialityListByYear');


                Route::group(['prefix' => 'appeals'], function() {
                    Route::get('/', 'AppealController@getList')
                        ->name('adminAppealList')
                        ->middleware(['hasRight:appeals,read']);

                    Route::post('/list/ajax', 'AppealController@getListAjax')
                        ->name('adminAppealListAjax')
                        ->middleware(['hasRight:appeals,read']);

                    Route::get('/review/{id}', 'AppealController@review')
                        ->name('adminAppealReview')
                        ->middleware(['hasRight:appeals,edit']);

                    Route::post('/review/{id}', 'AppealController@reviewPost')
                        ->name('adminAppealReviewPost')
                        ->middleware(['hasRight:appeals,edit']);

                    Route::post('/action/{id}', 'AppealController@action')
                        ->name('adminAppealAction')
                        ->middleware(['hasRight:appeals,edit']);
                });

                // Study Plan
                Route::group(['prefix' => 'study_plan'], function() {
                    Route::get('/', 'StudyPlanController@index')
                        ->name('adminStudyPlanList')
                        ->middleware(['hasRight:study_plan,read']);

                    Route::post('/list/ajax', 'StudyPlanController@getListAjax')
                        ->name('adminStudyPlanAjax')
                        ->middleware(['hasRight:study_plan,read']);

                    Route::get('/view/{user_id}', 'StudyPlanController@view')
                        ->name('adminStudyPlanView')
                        ->middleware(['hasRight:study_plan,edit']);

                    Route::post('/add/', 'StudyPlanController@add')
                        ->name('adminStudyPlanAdd')
                        ->middleware(['hasRight:study_plan,edit']);

                    Route::post('/delete/', 'StudyPlanController@delete')
                        ->name('adminStudyPlanDelete')
                        ->middleware(['hasRight:study_plan,edit']);

                    Route::post('/change/', 'StudyPlanController@change')
                        ->name('adminStudyPlanChange')
                        ->middleware(['hasRight:study_plan,edit']);

                    Route::post('/confirm/', 'StudyPlanController@confirm')
                        ->name('adminStudyPlanConfirm')
                        ->middleware(['hasRight:study_plan,edit']);

                    Route::post('/make', 'StudyPlanController@make')
                        ->name('adminStudyPlanMake')
                        ->middleware(['hasRight:study_plan,edit']);
                });

                // Speciality Semesters
                Route::group(['prefix' => 'speciality_semesters'], function() {
                    Route::get('/', 'SpecialitySemesterController@index')
                        ->name('adminSpecialitySemesterList')
                        ->middleware(['hasRight:speciality_semesters,read']);

                    Route::post('/list/ajax', 'SpecialitySemesterController@getListAjax')
                        ->name('adminSpecialitySemesterAjax')
                        ->middleware(['hasRight:speciality_semesters,read']);

                    Route::post('/save', 'SpecialitySemesterController@save')
                        ->name('adminSpecialitySemesterSave')
                        ->middleware(['hasRight:speciality_semesters,edit']);

                    Route::post('/edit', 'SpecialitySemesterController@edit')
                        ->name('adminSpecialitySemesterEdit')
                        ->middleware(['hasRight:speciality_semesters,edit']);

                    Route::post('/default_list/ajax', 'SpecialitySemesterController@getDefaultListAjax')
                        ->name('adminDefaultSemesterAjax')
                        ->middleware(['hasRight:speciality_semesters,read']);

                    Route::post('/editDefault', 'SpecialitySemesterController@editDefault')
                        ->name('adminDefaultSemesterEdit')
                        ->middleware(['hasRight:speciality_semesters,edit']);

                });


                Route::group(['prefix' => 'quiz_results'], function() {
                    Route::get('/', 'QuizResultsController@list')
                        ->name('adminQuizResults')
                        ->middleware(['hasRight:quiz_results,read']);

                    Route::post('/list/ajax', 'QuizResultsController@getListAjax')
                        ->name('adminQuizResultsListAjax')
                        ->middleware(['hasRight:quiz_results,read']);

                    Route::get('/view/{id}', 'QuizResultsController@view')
                        ->name('adminQuizResultsView')
                        ->middleware(['hasRight:quiz_results,read']);
//
//                    Route::post('/review/{id}', 'AppealController@reviewPost')
//                        ->name('adminAppealReviewPost')
//                        ->middleware(['hasRight:quiz_results,edit']);
//
//                    Route::post('/action/{id}', 'AppealController@action')
//                        ->name('adminAppealAction')
//                        ->middleware(['hasRight:quiz_results,edit']);
                });

                /* Quiz */
                Route::group(['prefix' => 'quiz'], function() {
                    Route::get('/', 'QuizController@index')
                        ->middleware(['hasRight:quiz,read'])
                        ->name('admin.quiz.show');

                    Route::get('/create', 'QuizController@createForm')
                        ->middleware(['hasRight:quiz,create'])
                        ->name('admin.quiz.create.show');

                    Route::get('/clone/{quiz_id}', 'QuizController@clone')
                        ->middleware(['hasRight:quiz,create'])
                        ->name('admin.quiz.clone');

                    Route::get('/edit/{quiz_id}', 'QuizController@editForm')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.edit.show');

                    Route::post('/edit/{quiz_id?}', 'QuizController@edit')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.edit');

                    Route::get('/remove/{quiz_id?}', 'QuizController@remove')
                        ->middleware(['hasRight:quiz,delete'])
                        ->name('admin.quiz.remove');

                    Route::post('/edit/users/{quiz_id}', 'QuizController@editUsers')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.edit.users');

                    Route::post('/insert/all/users/{quiz_id}', 'QuizController@insertAllUsers')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.users.all.insert');

                    Route::post('/table', 'QuizController@quizTable')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.show.table');

                    Route::post('/users/active/table/{quiz_id}', 'QuizController@quizUsersActiveTable')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.users.active.show.table');

                    Route::post('/users/active/remove/{quiz_id}', 'QuizController@userActiveRemove')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.user.active.remove');

                    Route::post('/users/active/clear/{quiz_id}', 'QuizController@userActiveClear')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.user.active.clear');

                    Route::post('/users/table', 'QuizController@quizUsersTable')
                        ->middleware(['hasRight:quiz,edit'])
                        ->name('admin.quiz.users.show.table');

                    Route::get('/report/{quiz_id}', 'QuizController@report')
                        ->middleware(['hasRight:quiz,read'])
                        ->name('admin.quiz.report');
                });

                Route::group(['prefix' => 'types'], function() {
                    Route::get('/list', 'ApplicationController@getTypesList')
                        ->name('adminApplicationTypeList')
                        ->middleware(['hasRight:applications,edit']);

                    Route::get('/add', 'ApplicationController@typeAdd')
                        ->name('adminApplicationTypeAdd')
                        ->middleware(['hasRight:applications,edit']);

                    Route::get('/edit/{id}', 'ApplicationController@typeEdit')
                        ->name('adminApplicationTypeEdit')
                        ->middleware(['hasRight:applications,edit']);

                    Route::post('/addedit', 'ApplicationController@typeAddEdit')
                        ->name('adminApplicationTypeAddEdit')
                        ->middleware(['hasRight:applications,edit']);

                    Route::get('/delete/{id}', 'ApplicationController@typeDelete')
                        ->name('adminApplicationTypeDelete')
                        ->middleware(['hasRight:applications,edit']);

                });

                /*ENTRANCE EXAM*/
                Route::group(['prefix' => 'entrance_exam'], function() {
                    Route::any('/list', 'EntranceExamController@list')
                        ->name('adminEntranceExamList');
                    Route::any('/get_list', 'EntranceExamController@getList')
                        ->name('adminEntranceExamGetList');
                    Route::any('/edit', 'EntranceExamController@edit')
                        ->name('adminEntranceExamEdit');
                    Route::any('/edit/post', 'EntranceExamController@editPost')
                        ->name('adminEntranceExamEditPost');
                    Route::any('/remove', 'EntranceExamController@remove')
                        ->name('adminEntranceExamRemove');
                });

                /*CHECK LIST*/
                Route::group(['prefix' => 'check_list'], function() {
                    Route::any('/list', 'CheckListController@list')
                        ->name('adminCheckListList');
                    Route::any('/get_list', 'CheckListController@getList')
                        ->name('adminCheckListGetList');
                    Route::any('/edit', 'CheckListController@edit')
                        ->name('adminCheckListEdit');
                    Route::any('/edit/post', 'CheckListController@editPost')
                        ->name('adminCheckListEditPost');
                    Route::any('/get_entrance_exam_list', 'CheckListController@getEntranceExamList')
                        ->name('adminCheckListGetEntranceExamList');
                    Route::any('/render/entrance_exam', 'CheckListController@renderEntranceExamItem')
                        ->name('adminCheckListRenderEntranceExamItem');
                    Route::any('/remove', 'CheckListController@remove')
                        ->name('adminCheckListRemove');

                });

                /*Practice*/
                Route::group(['prefix' => 'practice'], function () {
                    Route::get('/', 'PracticeController@index')
                        ->middleware(['hasRight:practice,read'])
                        ->name('admin.practice.show');

                    Route::post('/all', 'PracticeController@all')
                        ->middleware(['hasRight:practice,read'])
                        ->name('admin.practice.all');

                    Route::get('/create', 'PracticeController@create')
                        ->middleware(['hasRight:practice,create'])
                        ->name('admin.practice.create.show');

                    Route::get('/edit/{practice_id}', 'PracticeController@edit')
                        ->middleware(['hasRight:practice,edit'])
                        ->name('admin.practice.edit.show');

                    Route::post('/store/{practice_id}', 'PracticeController@store')
                        ->middleware(['hasRight:practice,edit'])
                        ->name('admin.practice.store');

                    Route::get('/remove/{practice_id}', 'PracticeController@remove')
                        ->middleware(['hasRight:practice,delete'])
                        ->name('admin.practice.remove');

                    Route::get('/scan/{file}', 'PracticeController@getScan')
                        ->middleware(['hasRight:practice,read'])
                        ->name('admin.download.scan');

                    Route::get('/scan_delete/{file}', 'PracticeController@deleteScan')
                        ->middleware(['hasRight:practice,delete'])
                        ->name('admin.remove.scan');
                });


                Route::group(['prefix' => 'types'], function() {
                    Route::get('/list', 'ApplicationController@getTypesList')
                        ->name('adminApplicationTypeList')
                        ->middleware(['hasRight:applications,edit']);

                    Route::get('/add', 'ApplicationController@typeAdd')
                        ->name('adminApplicationTypeAdd')
                        ->middleware(['hasRight:applications,edit']);

                    Route::get('/edit/{id}', 'ApplicationController@typeEdit')
                        ->name('adminApplicationTypeEdit')
                        ->middleware(['hasRight:applications,edit']);

                    Route::post('/addedit', 'ApplicationController@typeAddEdit')
                        ->name('adminApplicationTypeAddEdit')
                        ->middleware(['hasRight:applications,edit']);

                    Route::get('/delete/{id}', 'ApplicationController@typeDelete')
                        ->name('adminApplicationTypeDelete')
                        ->middleware(['hasRight:applications,edit']);
                });

                Route::group(['prefix' => 'applications/{type}'], function() {
                    Route::get('/list', 'ApplicationController@getList')
                        ->name('adminApplicationList')
                        ->middleware(['hasRight:applications,read']);

                    Route::get('/edit/{id}', 'ApplicationController@getOne')
                        ->name('adminApplicationEdit')
                        ->middleware(['hasRight:applications,read']);



                    Route::group(['prefix' => 'ajax'], function() {
                        Route::post('setorder', 'ApplicationController@ajaxSetOrder')
                            ->name('adminApplicationAjaxSetOrder')
                            ->middleware(['hasRight:applications,edit']);

                        Route::post('confirm', 'ApplicationController@ajaxConfirm')
                            ->name('adminApplicationAjaxConfirm')
                            ->middleware(['hasRight:applications,edit']);

                        Route::post('decline', 'ApplicationController@ajaxDecline')
                            ->name('adminApplicationAjaxDecline')
                            ->middleware(['hasRight:applications,edit']);


                        Route::post('comment/list', 'ApplicationController@ajaxCommentList')
                            ->name('adminApplicationAjaxCommentList')
                            ->middleware(['hasRight:applications,read']);

                        Route::post('comment/add', 'ApplicationController@ajaxCommentAdd')
                            ->name('adminApplicationAjaxCommentAdd')
                            ->middleware(['hasRight:applications,edit']);

                        Route::post('sign/list', 'ApplicationController@ajaxGetSignList')
                            ->name('adminApplicationAjaxGetSignList')
                            ->middleware(['hasRight:applications,read']);

                    });

                });

                Route::group(['prefix' => 'agitator'], function() {
                    Route::any('/transactions', 'AgitatorController@transactions')
                        ->middleware(['hasRight:agitator_transactions,read'])
                        ->name('adminAgitatorTransactions');
                    Route::any('/get/transactions', 'AgitatorController@ajaxGetTransactions')
                        ->middleware(['hasRight:agitator_transactions,read'])
                        ->name('adminAgitatorAjaxGetTransactions');
                    Route::any('/change/transaction', 'AgitatorController@ajaxChangeTransactionStatus')
                        ->middleware(['hasRight:agitator_transactions,read'])
                        ->name('adminAgitatorAjaxChangeTransactionStatus');
                });

                /* Start Plagiarism Checker */
                Route::get('/etxt', 'EtxtController@index')->name('etxtAntiPlagiat');
                Route::post('/etxt_antiplagiat', 'EtxtController@send')->name('etxtAntiPlagiatSend')
                    ->middleware(['hasRight:etxt,create']);
                Route::get('/etxtGetOnCheckTExts', 'EtxtController@getTextsOnCheck')->name('etxtGetTextsOnCheck')
                    ->middleware(['hasRight:etxt,read']);
                Route::get('/etxtGetTextSuccess', 'EtxtController@getTextSuccess')->name('etxtGetTextSuccess')
                    ->middleware(['hasRight:etxt,read']);
                Route::post('/etxt/report/pdf', 'EtxtController@generatePdf')->name('etxt.report.pdf')
                    ->middleware(['hasRight:etxt,read']);
                Route::get('/etxt/remove/{text_id}', 'EtxtController@removeText')->name('etxt.remove')
                    ->middleware(['hasRight:etxt,delete']);
                /* End Plagiarism Checker */

                Route::group(['prefix' => 'plagiarism'], function() {
                    Route::get('/', 'EtxtController@indexPlagiarism')
                        ->middleware(['hasRight:check_plagiarism_result,read'])
                        ->name('admin.plagiarism.show');

                    Route::post('/getResult/{id}', 'EtxtController@getEtxtResultById')
                        ->middleware(['hasRight:check_plagiarism_result,read'])
                        ->name('admin.plagiarism.getResult');
                });
            
                Route::group(['prefix' => 'visits'], function() {
                    Route::get('/', 'StudentVisitsController@index')
                        ->middleware(['hasRight:visits,read'])
                        ->name('admin.visits.show');

                    Route::post('/list/ajax', 'StudentVisitsController@getListAjax')
                        ->middleware(['hasRight:visits,read'])
                        ->name('profiles.visitors.list');

                    Route::any('/print', 'StudentVisitsExportController@printVisitsPDF')
                        ->middleware(['hasRight:visits,read'])
                        ->name('print.visitorsPDF');
                });

                /* Start Info Table */
                Route::prefix('information')->name('admin.info.')->group(function() {
                    Route::get('/', 'InformationController@getInfo')
                        ->middleware(['hasRight:info_table,read'])
                        ->name('get');

                    Route::post('/infoTable', 'InformationController@getInfoTable')
                        ->middleware(['hasRight:info_table,read'])
                        ->name('table.get');

                    Route::get('/create', 'InformationController@createInfo')
                        ->middleware(['hasRight:info_table,create'])
                        ->name('create');

                    Route::get('/edit/{info_id}', 'InformationController@editInfo')
                        ->middleware(['hasRight:info_table,edit'])
                        ->name('edit');

                    Route::post('/edit/{info_id?}', 'InformationController@storeInfo')
                        ->middleware(['hasRightIn:info_table,create edit'])
                        ->name('store');

                    Route::get('/remove/{info_id}', 'InformationController@removeInfo')
                        ->middleware(['hasRight:info_table,delete'])
                        ->name('remove');
                });
                /* End Info Table */

                Route::group(['prefix' => 'activities'], function(){
                    Route::get('/students', 'CheckActivityController@studentsList')
                        ->middleware(['hasRight:activities,read'])
                        ->name('admin.activity.students');

                    Route::get('/teachers', 'CheckActivityController@teachersList')
                        ->middleware(['hasRight:activities,read'])
                        ->name('admin.activity.teachers');

                    Route::any('/list/{role_type}', 'CheckActivityController@getUsersListAjax')
                        ->middleware(['hasRight:activities,read'])
                        ->name('admin.activities.users.list');
                });

                /* Start InfoNews */
                Route::prefix('infonews')->name('admin.news.')->group(function() {
                    Route::get('/', 'InfoNewsController@getInfo')
                        ->middleware(['hasRight:info_news,read'])
                        ->name('get');

                    Route::post('/table', 'InfoNewsController@getInfoTable')
                        ->middleware(['hasRight:info_news,read'])
                        ->name('table.get');

                    Route::get('/create', 'InfoNewsController@createInfo')
                        ->middleware(['hasRight:info_news,create'])
                        ->name('create');

                    Route::get('/edit/{info_id}', 'InfoNewsController@editInfo')
                        ->middleware(['hasRight:info_news,edit'])
                        ->name('edit');

                    Route::post('/edit/{info_id?}', 'InfoNewsController@storeInfo')
                        ->middleware(['hasRightIn:info_news,create edit'])
                        ->name('store');

                    Route::get('/remove/{info_id}', 'InfoNewsController@removeInfo')
                        ->middleware(['hasRight:info_news,delete'])
                        ->name('remove');
                });
                /* End InfoNews */


                /*NOBD DATA*/
                Route::group(['prefix' => 'nobd/data'], function() {
                    Route::any('/list', 'NoBDDataController@list')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataList');
                    Route::any('/get_academic_leave', 'NoBDDataController@getNobdAcademicLeave')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdAcademicLeave');
                    Route::any('/get_academic_mobility', 'NoBDDataController@getNobdAcademicMobility')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdAcademicMobility');
                    Route::any('/get_cause_stay_year', 'NoBDDataController@getNobdCauseStayYear')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdCauseStayYear');
                    Route::any('/get_country', 'NoBDDataController@getNobdCountry')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdCountry');
                    Route::any('/get_disability_group', 'NoBDDataController@getNobdDisabilityGroup')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdDisabilityGroup');
                    Route::any('/get_employment_opportunity', 'NoBDDataController@getNobdEmploymentOpportunity')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdEmploymentOpportunity');
                    Route::any('/get_events', 'NoBDDataController@getNobdEvents')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdEvents');
                    Route::any('/get_exchange_specialty', 'NoBDDataController@getNobdExchangeSpecialty')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdExchangeSpecialty');
                    Route::any('/get_form_diplom', 'NoBDDataController@getNobdFormDiplom')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdFormDiplom');
                    Route::any('/get_language', 'NoBDDataController@getNobdLanguage')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdLanguage');
                    Route::any('/get_payment_type', 'NoBDDataController@getNobdPaymentType')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdPaymentType');
                    Route::any('/get_reason_disposal', 'NoBDDataController@getNobdReasonDisposal')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdReasonDisposal');
                    Route::any('/get_reward', 'NoBDDataController@getNobdReward')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdReward');
                    Route::any('/get_trained_quota', 'NoBDDataController@getNobdTrainedQuota')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdTrainedQuota');
                    Route::any('/get_type_direction', 'NoBDDataController@getNobdTypeDirection')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdTypeDirection');
                    Route::any('/get_type_Event', 'NoBDDataController@getNobdTypeEvent')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdTypeEvent');
                    Route::any('/get_type_violation', 'NoBDDataController@getNobdTypeViolation')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdTypeViolation');
                    Route::any('/get_item', 'NoBDDataController@getItem')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetItem');
                    Route::any('/get_study_exchange', 'NoBDDataController@getNobdStudyExchange')
                        ->middleware(['hasRight:nobd_data,read'])
                        ->name('adminNobddataGetNobdStudyExchange');


                    Route::any('/remove_item', 'NoBDDataController@removeItem')
                        ->middleware(['hasRight:nobd_data,delete'])
                        ->name('adminNobddataRemoveItem');
                    Route::any('/edit_item', 'NoBDDataController@editItem')
                        ->middleware(['hasRight:nobd_data,edit'])
                        ->name('adminNobddataEditItem');
                });


                Route::group(['prefix' => 'visits'], function() {
                    Route::get('/', 'StudentVisitsController@index')
                        ->name('admin.visits.show');

                    Route::post('/list/ajax', 'StudentVisitsController@getListAjax')
                        ->name('profiles.visitors.list');

                    Route::any('/print', 'StudentVisitsExportController@printVisitsPDF')
                        ->name('print.visitorsPDF');

                });

                Route::group(['prefix' => 'visits'], function() {
                    Route::get('/', 'StudentVisitsController@index')
                        ->middleware(['hasRight:visits,read'])
                        ->name('admin.visits.show');

                    Route::post('/list/ajax', 'StudentVisitsController@getListAjax')
                        ->middleware(['hasRight:visits,read'])
                        ->name('profiles.visitors.list');

                    Route::any('/print', 'StudentVisitsExportController@printVisitsPDF')
                        ->middleware(['hasRight:visits,read'])
                        ->name('print.visitorsPDF');
                });

                Route::group(['prefix' => 'webcam'], function() {
                    Route::get('/', 'WebcamController@index')
                        ->middleware(['hasRight:webcam,read'])
                        ->name('admin.webcam.show');

                    Route::post('/getListAjax', 'WebcamController@getListAjax')
                        ->middleware(['hasRight:webcam,read'])
                        ->name('admin.webcam.getList');

                    Route::get('/delete/{id}', 'WebcamController@deleteWebcamRecord')
                        ->middleware(['hasRight:webcam,delete'])
                        ->name('admin.webcam.delete');
                });

                Route::group(['prefix' => 'activities'], function(){
                    Route::get('/students', 'CheckActivityController@studentsList')
                        ->middleware(['hasRight:activities,read'])
                        ->name('admin.activity.students');

                    Route::get('/teachers', 'CheckActivityController@teachersList')
                        ->middleware(['hasRight:activities,read'])
                        ->name('admin.activity.teachers');

                    Route::any('/list/{role_type}', 'CheckActivityController@getUsersListAjax')
                        ->middleware(['hasRight:activities,read'])
                        ->name('admin.activities.users.list');

                    Route::post('/export/{type}', 'CheckActivityController@export')
                        ->middleware(['hasRight:export_activities,read'])
                        ->name('admin.activities.export');
                });

                Route::group(['prefix' => 'assign/teachers'], function (){
                    Route::get('/','AssignTeachersController@index')
                        ->name('admin.assign.teachers.index')
                        ->middleware(['hasRight:assign_teachers,read']);

                    Route::post('getDisciplinesBySemester/{semester?}', 'AssignTeachersController@getDisciplinesBySemester')
                        ->name('admin.assign.teachers.getDisciplinesBySemester')
                        ->middleware(['hasRight:assign_teachers,read']);

                    Route::post('getDisciplineGroups/{disciplineId?}', 'AssignTeachersController@getDisciplineGroups')
                        ->name('admin.assign.teachers.getDisciplineGroups')
                        ->middleware(['hasRight:assign_teachers,read']);

                    Route::post('getDisciplineTeachers/{disciplineId?}', 'AssignTeachersController@getDisciplineTeachers')
                        ->name('admin.assign.teachers.getDisciplineTeachers')
                        ->middleware(['hasRight:assign_teachers,read']);

                    Route::post('addEditGroupTeacher', 'AssignTeachersController@addEditGroupTeacher')
                        ->name('admin.assign.teachers.addEditGroupTeacher')
                        ->middleware(['hasRight:assign_teachers,read']);

                    Route::post('groupingStudyGroups', 'AssignTeachersController@groupingStudyGroups')
                        ->name('admin.assign.teachers.groupingStudyGroups')
                        ->middleware(['hasRight:assign_teachers,read']);
                });

                Route::group(['prefix' => 'teacher/journal'], function(){
                    Route::get('/','TeacherJournalController@index')
                        ->name('admin.teacher.journal.index')
                        ->middleware(['hasRight:teacher_journal,read']);

                    Route::post('/getTeacherGroups','TeacherJournalController@getTeacherGroups')
                        ->name('admin.teacher.journal.getTeacherGroups')
                        ->middleware(['hasRight:teacher_journal,read']);

                    Route::post('/getTypes','TeacherJournalController@getTypes')
                        ->name('admin.teacher.journal.getTypes')
                        ->middleware(['hasRight:teacher_journal,read']);

                    Route::post('/getDisciplineGroupStudents','TeacherJournalController@getDisciplineGroupStudents')
                        ->name('admin.teacher.journal.getDisciplineGroupStudents')
                        ->middleware(['hasRight:teacher_journal,read']);
                });
            });
        });
    });
