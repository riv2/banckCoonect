@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin"> {{ $info->title }} </h2>
            </div>

            <div class="card">
                <div class="card-body">
                    {!! $info->text !!}
                </div>
            </div>
        </div>
    </section>
@endsection

