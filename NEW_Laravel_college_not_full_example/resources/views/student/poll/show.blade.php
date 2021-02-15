@extends('layouts.app')

@section('title', __('Polls'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('Polls')</h2>
            </div>

            <div class="card text-center">
                <ul class="list-group list-group-flush">
                    @foreach($polls as $poll)
                        <li class="list-group-item" style="{{ $poll->is_required ? 'background: rgba(255, 0, 35, 0.1);' : '' }}">
                            <a href="{{ route('student.poll.show', ['poll_id' => $poll->id]) }}">
                                {{ $poll->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
