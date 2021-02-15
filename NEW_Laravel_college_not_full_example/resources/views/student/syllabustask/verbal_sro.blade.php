@extends('layouts.app')

@section('title', __('Verbal SRO'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="main-task-list">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">{{__('Verbal SRO')}}</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            {{-- PAYMENT COURSE --}}
                            @if( $bIsDisciplineHasCourse )
                                @lang('In this discipline course work is provided. Contact your teacher or do some practice.')

                                @if( !empty($bIsPayedCoursePercent) )
                                    <div class="alert alert-warning" role="alert">
                                        @lang('Your assessment') {{ $bIsPayedCoursePercent }}%
                                    </div>
                                @endif
                            @else
                                @lang("Self-learning assignment defense must be undertaken within this course. You're kindly asked to contat your professor/ lecturer.")
                            @endif

                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
