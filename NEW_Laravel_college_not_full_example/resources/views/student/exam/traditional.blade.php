@extends('layouts.app')

@section('title', __('Traditional form'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Traditional form')</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    @lang('In this discipline, the exam is taken in the traditional form. We ask you to contact the teacher.')
                </div>
            </div>
        </div>
    </section>
@endsection