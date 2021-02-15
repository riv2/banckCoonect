@extends('layouts.pdf')

@section('content')

    <div class="row" style="padding:20%;">
        <div class="col"></div>
        <div class="col-10">

            <h2 class="text-center"> Купленные услуги </h2>

            <table class="table table-bordered margin-t15">
                <thead>
                <tr>
                    <th> Код </th>
                    <th> Наименование </th>
                    <th> Стоимость </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td> {{ $service->code }} </td>
                    <td> {{ $service->name }} </td>
                    <td> {{ $service->cost }} </td>
                </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col text-left">

                    <p>
                        <strong>Дата: </strong> {{ date('d.m.Y') }}
                    </p>

                </div>
                <div class="col text-right">

                    <p>
                        <strong>Итого: </strong> {{ $service->cost }} ₸
                    </p>

                </div>
            </div>



        </div>
        <div class="col"></div>
    </div>

@endsection

