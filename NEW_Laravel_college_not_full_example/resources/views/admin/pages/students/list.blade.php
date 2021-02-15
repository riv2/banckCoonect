@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Студенты</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">

                <table id="data-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Статус</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>


                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        $(document).ready(function() {
            var dataTable = $('#data-table-ajax').DataTable( {
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('adminStudentListAjax') }}", // json datasource
                    type: "post",  // method  , by default get
                    error: function(){  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display","none");

                    }
                }
            } );
        } );

    </script>
@endsection