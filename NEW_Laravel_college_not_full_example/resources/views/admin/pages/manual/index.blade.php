@extends("admin.admin_app")

@section("content")
    <div id="main" class="manualPage">
        <div class="page-header">
            <h2>Справочники:</h2>
        </div>
        <div class="tab">
            <button class="tablinks active" onclick="openCity(event, 'shedule')">Графики работы</button>
            <button class="tablinks" onclick="openCity(event, 'nationality')">Национальности</button>
            <button class="tablinks" onclick="openCity(event, 'docs')">Органы, выдающие документы, удостоверяющие личность</button>
            <button class="tablinks" onclick="openCity(event, 'citizenship')">Гражданство</button>
            <button class="tablinks" onclick="openCity(event, 'education')">Образовательные степени</button>
            <button class="tablinks" onclick="openCity(event, 'perks')">Надбавки</button>
            <button class="tablinks" onclick="openCity(event, 'organizations')">Организация</button>
        </div>

        <div id="shedule" class="tabcontent" style="display: block;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="sheduleDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>Описание</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="nationality" class="tabcontent" style="display: none;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="nationalityDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>ЕСУВО</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="docs" class="tabcontent" style="display: none;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="issuingDocsDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>ЕСУВО</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="citizenship" class="tabcontent" style="display: none;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="citizenshipDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>ЕСУВО</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="education" class="tabcontent" style="display: none;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="educationDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>Краткое  имя</th>
                                <th>Тип</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="perks" class="tabcontent" style="display: none;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="perksDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th>Размер</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div id="organizations" class="tabcontent" style="display: none;">
            <div class="panel panel-default panel-shadow">
                <div class="panel-body">
                    <table id="organizationsDatatable" class="table table-striped table-hover dt-responsive" style="table-layout: fixed;" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Имя</th>
                                <th class="text-center width-100">Действие</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var sheduleDataTable = $('#sheduleDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manualSheduleDatatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToShedule pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "200px" },
                    { data: 'description', width: "250px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToShedule').html('<a href="{{ route('manualAddNotePage', ['name' => 'work_shedule']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');

            var nationalityDatatable = $('#nationalityDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manualNationalityDatatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToNationality pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "200px" },
                    { data: 'link', width: "250px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToNationality').html('<a href="{{ route('manualAddNotePage', ['name' => 'nationality']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');

            var citizenshipDatatable = $('#citizenshipDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manualCitizenshipDatatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToCitizenship pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "200px" },
                    { data: 'link', width: "250px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToCitizenship').html('<a href="{{ route('manualAddNotePage', ['name' => 'citizenship']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');

            var issuingDocsDatatable = $('#issuingDocsDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manual.issuing.docs.datatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToIssuingDocs pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "200px" },
                    { data: 'link', width: "250px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToIssuingDocs').html('<a href="{{ route('manualAddNotePage', ['name' => 'issuing_docs']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');

            var educationDatatable = $('#educationDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manual.education.datatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToEducation pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "100px" },
                    { data: 'short_name', width: "100px" },
                    { data: 'type', width: "100px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToEducation').html('<a href="{{ route('manualAddNotePage', ['name' => 'education']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');

            var perksDatatable = $('#perksDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manual.perks.datatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToPerks pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "100px" },
                    { data: 'value', width: "100px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToPerks').html('<a href="{{ route('manualAddNotePage', ['name' => 'perks']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');

            var organizationsDatatable = $('#organizationsDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('manual.organizations.datatable') }}",
                    type: "post",
                },
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 addNoteToOrganizations pt-1 text-right'><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12 position-relative'tr>>" +
                     "<'row'<'col-sm-12 col-md-9'i><'col-sm-12 col-md-3 d-flex justify-content-end'p>>",
                columns: [
                    { data: 'id', width: "50px" },
                    { data: 'name', width: "100px" },
                    { data: 'action', width: "50px" }
                ],
                "drawCallback": function( settings ) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('.addNoteToOrganizations').html('<a href="{{ route('manualAddNotePage', ['name' => 'organizations']) }}"><button class="btn btn-primary mt-3">Добавить запись</button></a>');
        });


        function openCity(evt, cityName) {
            // Declare all variables
            var i, tabcontent, tablinks;

            // Get all elements with class="tabcontent" and hide them
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Get all elements with class="tablinks" and remove the class "active"
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            // Show the current tab, and add an "active" class to the link that opened the tab
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }

    </script>
@endsection