@extends("admin.admin_app")

@section('content')
    <div class="main" style="margin-top: 10px;">
        <div class="row">
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary pull-right mr-15 mb-15">
                Добавить объявление <i class="fa fa-plus"></i>
            </a>
        </div>

        <table id="data-table-info" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Дата</th>
                    <th>Действие</th>
                </tr>
            </thead>

            <tbody>
            {{-- TODO --}}
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var dataTable = $('#data-table-info').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.news.table.get') }}",
                type: "post",
            },
            "aaSorting": [[2,'desc']],
            columns: [
                {
                    data: 'title',
                    width: '75%'
                },
                {
                    data: 'created_at',
                    width: '15%'
                },
                {
                    data: 'action',
                    width: '10%'
                }
            ]
        });
    </script>
@endsection
