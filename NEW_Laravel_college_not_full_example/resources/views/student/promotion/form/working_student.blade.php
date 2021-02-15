<?php
$certificateWork = \App\Services\Auth::user()->getPromotionWork($promotion->id);
?>
<form id="working_student_form" action="{{route('studentPromotionRequest', ['id' => $promotion->id])}}" method="post" enctype="multipart/form-data">
    <div class="form-group col-md-12 no-padding">
        <label class="col-md-4 no-padding control-label">{{ __('Certificate of employment') }}</label>
        <div class="col-md-6">
            @if(isset($certificateWork->work_certificate_file))
                <a href="/images/uploads/works/{{ $certificateWork->work_certificate_file }}" target="_blank">{{ __('See document') }}</a>
            @else
            <input required type="file" name="work_certificate_file" />
            @endif
        </div>
    </div>
    <div class="form-group col-md-12 no-padding">
        <label class="col-md-4 no-padding control-label">{{ __('Certificate of pension contributions') }}</label>
        <div class="col-md-6">
            @if(isset($certificateWork->pension_report_file))
                <a href="/images/uploads/works/{{ $certificateWork->pension_report_file }}" target="_blank">{{ __('See document') }}</a>
            @else
            <input required type="file" name="pension_report_file" />
            @endif
        </div>
    </div>
    <div class="form-group col-md-12 no-padding">
        <label class="col-md-4 no-padding control-label">{{ __('Discount upon payment of disciplines') }}</label>
        <div class="col-md-6">
            @if(isset($certificateWork->discount))
                {{ $certificateWork->discount }}%
            @else
                {{ $promotion->discount }}%
            @endif
        </div>
    </div>
</form>