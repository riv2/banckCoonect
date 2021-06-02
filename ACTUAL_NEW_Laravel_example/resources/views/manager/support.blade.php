@extends('layouts.app')

@section('title', __('manager.support.app-title'))

@section('content')
    @include("helpers.filter")
    {{ $support->appends(request()->except('page'))->links() }}
    <h4>{{ __('manager.support.leads') }}</h4>
    @foreach($support as $item)
        <div class="row row-support-title">
            <div class="col col-2">{{ __('manager.support.date') }}: {{ $item->created_at }}</div>
            <div class="col col-2">UserID: {{ $item->user_id }}</div>
            <div class="col col-2">{{ $cases[$item->case_type] }}</div>
            <div class="col col-2">
                <button class="btn btn-primary" data-whatever="{{ $item->support_id }}"
                        onclick="$('#answerSupportForm [name=id]').val('{{ $item->support_id }}');"
                        data-bs-toggle="modal"
                        data-bs-target="#answerSupportModal">{{ __('manager.support.answer') }}</button>
            </div>
        </div>
        <div class="row">
            <div class="col col-4">
                <strong>{{ __('manager.support.lead') }}</strong><br>
                {{ $item->case_description }}
            </div>
            <div class="col col-4">
                <strong>{{ __('manager.support.answer') }}</strong><br>
                @if($item->answer)
                    {{ $item->answer }}
                @else
                    {{ __('manager.support.no-answer-yet') }}
                @endif
            </div>
            <div class="col col-2">
                <strong>{{ __('manager.support.comment') }}</strong><br>
                @if($item->comment)
                    {{ $item->comment }}
                @else
                    {{ __('manager.support.no-comment') }}
                @endif
            </div>
        </div>
        <br> <br>
    @endforeach

    <div class="modal fade" id="answerSupportModal" tabindex="-1" role="dialog"
         aria-labelledby="answerSupportModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">{{ __('manager.support.add-answer') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-work">
                        <form id="answerSupportForm">
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <label for="">{{ __('manager.support.answer') }}</label>
                                <textarea name="answer" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('manager.support.comment-1') }}</label>
                                <textarea name="comment" class="form-control"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('manager.support.close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="saveTheAnswer()">{{ __('manager.support.save') }}</button>
                </div>
            </div>
        </div>
    </div>
    {{ $support->appends(request()->except('page'))->links() }}
@endsection
@section('js')
    <script>
        function saveTheAnswer() {
            var ser = $("#answerSupportForm").serialize();
            $.ajax({
                url: "{{ route("manager.support.answer") }}",
                type: "post",
                dataType: "text",
                data: ser,
                success: function (data) {
                    alert(data)
                    document.location.reload();
                }
            })
        }

    </script>
@endsection
