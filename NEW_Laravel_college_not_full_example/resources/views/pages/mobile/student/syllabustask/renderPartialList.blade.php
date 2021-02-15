<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">

        {{-- SRO LIST --}}
        <div class="accordion" id="accordionExample">
            @if( !$bIsDisciplineHasCourse && !empty($syllabusTask) )
                @foreach($syllabusTask as $key => $item)

                    <div class="card panel-default discipline padding-0">

                        {{-- Header --}}
                        <div class="card-header panel-heading padding-0" id="heading{{ $item->id ?? 0 }}">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $item->id ?? 0 }}" aria-expanded="true" aria-controls="collapse{{ $item->id ?? 0 }}">
                                    @lang('Task') {{ $key + 1 }} &nbsp;&nbsp;  {{ $item->taskResult->points ?? 0 }}/{{ $item->points ?? 0 }} &nbsp;&nbsp; {{ $item->taskResult->value ?? 0 }}%
                                </button>
                            </h2>
                        </div>

                        {{-- Buy panel taskResult --}}
                        <div id="collapse{{ $item->id }}" class="collapse" aria-labelledby="heading{{ $item->id ?? 0 }}" data-parent="#accordionExample">
                            <div class="card-body padding-15">
                                @if($item->proceedButtonShow)
                                    <a class="btn btn-success" href="{{route('sroProceed', ['task_id' => $item->id, 'discipline_id' => $discipline_id]) }}"> @lang('Proceed') </a>
                                @endif

                                @if($item->retakeButtonShow)
                                    <a class="btn btn-info" href="{{ route('sroTaskPay', ['task_id' => $item->id]) }}"> @lang('Adjustment') </a>
                                @endif
                            </div>
                        </div>

                    </div>

                @endforeach

            @endif
        </div>

        <br>

        {{-- PAYMENT COURSE --}}
        @if( $bIsDisciplineHasCourse )

            @if( !empty($bIsPayedCoursePercent) )
                <div class="alert alert-warning" role="alert">
                    @lang('Your assessment') {{ $bIsPayedCoursePercent }}%
                </div>
            @else
                <div class="alert alert-warning" role="alert">
                    @lang("Mid-term paper must be provided within this course. You're kindly asked to contant your professor / lecturer.")
                </div>
            @endif

        @endif


    </div>
    <div class="col-md-2"></div>
</div>