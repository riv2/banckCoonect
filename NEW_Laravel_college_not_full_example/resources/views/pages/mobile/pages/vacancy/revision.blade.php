@extends('layouts.app')

@section('title', __('Vacancies'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('Edit data')</h2>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Причина доработки:</label>
                        <textarea class="form-control disabled" disabled>{{ $resume->reason }}</textarea>
                    </div>
                </div>
            </div>
            
            {!! Form::open([
                'url' => route('revision.resume.submit'),
                'enctype' => 'multipart/form-data'
            ]) !!}
                <input type="hidden" name="resume_id" value="{{ $resume->id }}">
                <div class="row row-flex margin-t5">
                    @if(count($requirements['personal_info']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Персональные данные</h4>
                                    </div>
                                </div>

                                @foreach($requirements['personal_info'] as $item)
                                    <div class="form-group">
                                        <label>
                                            {{ $item['requirement']['name'] }}
                                        </label>
                                        <input 
                                            class="form-control" 
                                            type="{{ $item['requirement']['field_type'] }}" 
                                            name="requirements[{{ $item['field_name'] }}]" 
                                            value="{{ $item['requirement']['field_type'] != 'file' ? $item['content'] : '' }}"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($requirements['education']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Образование</h4>
                                    </div>
                                </div>

                                @foreach($requirements['education'] as $item)
                                    <div class="form-group">
                                        <label>
                                            {{ $item['requirement']['name'] }}
                                        </label>
                                        <input 
                                            class="form-control" 
                                            type="{{ $item['requirement']['field_type'] }}" 
                                            name="requirements[{{ $item['field_name'] }}]" 
                                            value="{{ $item['requirement']['field_type'] != 'file' ? $item['content'] : '' }}"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($requirements['print_edition']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Печатные издания</h4>
                                    </div>
                                </div>

                                @foreach($requirements['print_edition'] as $item)
                                    <div class="form-group">
                                        <label>
                                            {{ $item['requirement']['name'] }}
                                        </label>
                                        <input 
                                            class="form-control" 
                                            type="{{ $item['requirement']['field_type'] }}" 
                                            name="requirements[{{ $item['field_name'] }}]" 
                                            value="{{ $item['requirement']['field_type'] != 'file' ? $item['content'] : '' }}"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row row-flex margin-t15">
                    @if(count($requirements['proceedings_and_publications']) > 0)
                        <div class="col-md-4 col-sm-12">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Труды и публикации</h4>
                                    </div>
                                </div>

                                @foreach($requirements['proceedings_and_publications'] as $item)
                                    <div class="form-group">
                                        <label>
                                            {{ $item['requirement']['name'] }}
                                        </label>
                                        <input 
                                            class="form-control" 
                                            type="{{ $item['requirement']['field_type'] }}" 
                                            name="requirements[{{ $item['field_name'] }}]" 
                                            value="{{ $item['requirement']['field_type'] != 'file' ? $item['content'] : '' }}"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($requirements['qualification_increase']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Повышение квалификации</h4>
                                    </div>
                                </div>

                                @foreach($requirements['qualification_increase'] as $item)
                                    <div class="form-group">
                                        <label>
                                            {{ $item['requirement']['name'] }}
                                        </label>
                                        <input 
                                            class="form-control" 
                                            type="{{ $item['requirement']['field_type'] }}" 
                                            name="requirements[{{ $item['field_name'] }}]" 
                                            value="{{ $item['requirement']['field_type'] != 'file' ? $item['content'] : '' }}"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div> 

                <hr>
                <div class="row">
                    <div class="col-md-12 text-right margin-b10">
                        <button type="submit" class="btn btn-lg btn-primary btn-md">
                            @lang('Send')
                        </button>
                    </div> 
                </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $('.custom-file-input').on('change', function() { 
                let fileName = $(this).val().split('\\').pop(); 
                $(this).next('.custom-file-label').addClass("selected").html(fileName); 
            });
        });
    </script>
@endsection
