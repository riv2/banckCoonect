@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6">
                    <h2>Приказ № {{ $order->number }} ({{ $order->orderName->name }})</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <h4>Итоги голосования:</h4>
            </div>
            <div class="col-md-10">
                <h5 class="margin-t5 padding-t10">Проголосовало {{ count($countVotes) }}/{{ count($votes) }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>Список сотрудников для согласования:</h4>
                <ul>
                    @foreach($order->votes as $value)
                        <li>{{ $value->user->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row">
            @foreach($order->votes as $vote)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">
                            {{ $vote->user->name }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <b>Решение</b>
                                </div>
                                <div class="col-md-9">
                                    <p>{{ App\EmployeesUsersVote::$statuses[$vote->vote] }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <b>Комментарий</b>
                                </div>
                                <div class="col-md-9">
                                    <p>{{ $vote->comment }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection