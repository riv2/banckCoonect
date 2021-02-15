@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin"> {{ __("Infodesk") }} </h2>
            </div>

            <ul class="nav nav-pills nav-justified">
                <li class="nav-item">
                    <a class="nav-link {{ $info_type == 'other'? 'active' : '' }}" href="{{ route('student.info.show', ['info_type' => 'other']) }}">
                        @lang('Other')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $info_type == 'important'? 'active' : '' }}" href="{{ route('student.info.show', ['info_type' => 'important']) }}">
                        @lang('Important')
                    </a>
                </li>
            </ul>

            @forelse($info as $news)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('student.info.details.show', ['info_id' => $news->id]) }}">{{ $news->title }}</a>
                        </h5>

                        <p class="card-text">
                            <a href="{{ route('student.info.details.show', ['info_id' => $news->id]) }}" style="color: inherit; text-decoration: inherit;">
                                {!! $news->text_preview !!}
                            </a>
                        </p>

                        <p class="card-text">
                            <small class="text-muted">{{ $news->created_at }}</small>
                        </p>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center">
                        <p class="card-text">@lang('No news.')</p>
                    </div>
                </div>
            @endforelse

            {!! $info->links() !!}
        </div>
    </section>
@endsection

