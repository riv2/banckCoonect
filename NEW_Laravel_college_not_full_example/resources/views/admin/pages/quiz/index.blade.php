@extends("admin.admin_app")

@section('content')
    <div class="main">
        <div id="quiz-tab">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#quiz" aria-controls="quiz" role="tab" data-toggle="tab">Анкеты</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="quiz">
                    <div class="row">
                        <a href="{{ route('admin.quiz.create.show') }}" class="btn btn-primary pull-right mr-15 mb-15">
                            Создать анкету <i class="fa fa-plus"></i>
                        </a>
                    </div>

                    <table id="data-table-polls" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Действие</th>
                                <th>Active</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- TODO --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var dataTable = $('#data-table-polls').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.quiz.show.table') }}",
                type: "post",
            },
            "aaSorting": [[2,'desc']],
            rowCallback: function( row, data ) {
                if (data.active == '1') {
                    $(row).addClass('is_active');
                }
            },
            columns: [
                {
                    data: 'title_ru',
                    width: '80%'
                },
                {
                    data: 'action',
                    width: '20%'
                },
                {
                    data: 'active',
                    className: 'hide'
                }
            ]
        });

        $('#quiz-tab a[role=tab]').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
    </script>
@endsection
