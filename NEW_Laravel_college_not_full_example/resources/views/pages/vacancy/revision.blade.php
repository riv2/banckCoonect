@extends('layouts.app')

@section('title', __('Vacancies'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('Edit data')</h2>
            </div>

            @if($type != 'edit')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Причина доработки:</label>
                            <textarea class="form-control disabled" disabled>{{ $resume->reason }}</textarea>
                        </div>
                    </div>
                </div>
            @endif
            
            {!! Form::open([
                'url' => route('revision.resume.submit'),
                'enctype' => 'multipart/form-data'
            ]) !!}
                <input type="hidden" name="resume_id" value="{{ $resume->id }}">
                @if(count($requirements['personal_info']) > 0)
                    <h2>Персональные данные</h2>
                    <div class="row">
                        @foreach($requirements['personal_info'] as $item)
                            <div class="col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label :for="{{ 'personal_info_'.$item['requirement_id'] }}" class="font-weight-light">
                                        {{ $item['requirement']['name'] }}
                                    </label>
                                    @if($item['requirement']['field_name'] == 'citizenship' || $item['requirement']['field_name'] == 'nationality')
                                        <select 
                                            class="form-control" 
                                            name="{{ 'requirements[personal_info]['.$item['requirement_id'].']' }}" 
                                            id="{{ 'personal_info_'.$item['requirement_id'] }}" 
                                        >
                                            @php $foreachData = $item['requirement']['field_name'] == 'citizenships'? $citizenships : $nationalities @endphp
                                            @foreach($foreachData as $manual)
                                                <option 
                                                    value="{{ $manual['name'] }}" 
                                                    @if($manual['name'] == $item['content']) {{ 'selected' }} @endif
                                                >
                                                    {{ $manual['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        @if($item['requirement']['field_type'] == 'select')
                                            <select
                                                class="form-control" 
                                                name="{{ 'requirements[personal_info]['.$item['requirement_id'].']' }}" 
                                                id="{{ 'personal_info_'.$item['requirement_id'] }}" 
                                            >
                                                @foreach($item['requirement']['options'] as $option)
                                                    <option 
                                                        value="{{ $option }}" 
                                                        @if($option == $item['content']) {{ 'selected' }} @endif
                                                    >
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input 
                                                class="form-control" 
                                                type="{{ $item['requirement']['field_type'] }}" 
                                                name="{{ 'requirements[personal_info]['.$item['requirement_id'].']' }}" 
                                                id="{{ 'personal_info_'.$item['requirement_id'] }}" 
                                                value="{{ $item['content'] }}"
                                            >
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(count($requirements['education']) > 0)
                    <h2>Образование</h2>
                    <div class="row">
                        @foreach($requirements['education'] as $records)
                            <div class="col-md-12 col-sm-12">
                                @foreach(is_array($records['json_content']) ? $records['json_content'] : json_decode($records['json_content'], true) as $index => $record)
                                    <hr>
                                    <h3 class="text-uppercase text-monospace">
                                        {{ $records['requirement']['name'] }} 
                                    </h3>
                                    <div class="row">
                                        @foreach($record as $field_name => $content)
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    @foreach($records['requirement']['fields'] as $requirement_field)
                                                        @if($requirement_field['field_name'] == $field_name)
                                                            <label 
                                                                for="{{ 'education_'.$requirement_field['requirement_id'] }}" 
                                                                class="font-weight-light"
                                                            >
                                                                {{ $requirement_field['name'] }}
                                                            </label>
                                                            @if($requirement_field['field_type'] == 'select')
                                                                <select 
                                                                    class="form-control" 
                                                                    name="{{ 
                                                                        'requirements[education]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}"
                                                                    id="{{ 'education_'.$requirement_field['requirement_id'] }}" 
                                                                >
                                                                    @foreach($options as $option)
                                                                        <option 
                                                                            value="{{ $option }}" 
                                                                            {{ $option == $content ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <input 
                                                                    class="form-control" 
                                                                    type="{{ $requirement_field['field_type'] }}" 
                                                                    name="{{ 
                                                                        'requirements[education]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}" 
                                                                    id="{{ 'education_'.$requirement_field['requirement_id'] }}" 
                                                                    value="{{ $content }}"
                                                                >
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(count($requirements['nir']) > 0)
                    <h2>НИР</h2>
                    <div class="row">
                        @foreach($requirements['nir'] as $records)
                            <div class="col-md-12 col-sm-12">
                                @foreach(is_array($records['json_content']) ? $records['json_content'] : json_decode($records['json_content'], true) as $index => $record)
                                    <hr>
                                    <h3 class="text-uppercase text-monospace">
                                        {{ $records['requirement']['name'] }} 
                                    </h3>
                                    <div class="row">
                                        @foreach($record as $field_name => $content)
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    @foreach($records['requirement']['fields'] as $requirement_field)
                                                        @if($requirement_field['field_name'] == $field_name)
                                                            <label 
                                                                for="{{ 'nir_'.$requirement_field['requirement_id'] }}" 
                                                                class="font-weight-light"
                                                            >
                                                                {{ $requirement_field['name'] }}
                                                            </label>
                                                            @if($requirement_field['field_type'] == 'select')
                                                                <select 
                                                                    class="form-control" 
                                                                    name="{{ 
                                                                        'requirements[nir]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}"
                                                                    id="{{ 'nir_'.$requirement_field['requirement_id'] }}" 
                                                                >
                                                                    @foreach($requirement_field['options'] as $option)
                                                                        <option 
                                                                            value="{{ $option }}" 
                                                                            {{ $option == $content ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <input 
                                                                    class="form-control" 
                                                                    type="{{ $requirement_field['field_type'] }}" 
                                                                    name="{{ 
                                                                        'requirements[nir]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}" 
                                                                    id="{{ 'nir_'.$requirement_field['requirement_id'] }}" 
                                                                    value="{{ $content }}"
                                                                >
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(count($requirements['seniority']) > 0)
                    <h2>Трудовой стаж</h2>
                    <div class="row">
                        @foreach($requirements['seniority'] as $records)
                            <div class="col-md-12 col-sm-12">
                                @foreach(is_array($records['json_content']) ? $records['json_content'] : json_decode($records['json_content'], true) as $index => $record)
                                    <hr>
                                    <h3 class="text-uppercase text-monospace">
                                        {{ $records['requirement']['name'] }} 
                                    </h3>
                                    <div class="row">
                                        @foreach($record as $field_name => $content)
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    @foreach($records['requirement']['fields'] as $requirement_field)
                                                        @if($requirement_field['field_name'] == $field_name)
                                                            <label 
                                                                for="{{ 'seniority_'.$requirement_field['requirement_id'] }}" 
                                                                class="font-weight-light"
                                                            >
                                                                {{ $requirement_field['name'] }}
                                                            </label>
                                                            @if($requirement_field['field_type'] == 'select')
                                                                <select 
                                                                    class="form-control" 
                                                                    name="{{ 
                                                                        'requirements[seniority]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}"
                                                                    id="{{ 'seniority_'.$requirement_field['requirement_id'] }}" 
                                                                >
                                                                    @foreach($requirement_field['options'] as $option)
                                                                        <option 
                                                                            value="{{ $option }}" 
                                                                            {{ $option == $content ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <input 
                                                                    class="form-control" 
                                                                    type="{{ $requirement_field['field_type'] }}" 
                                                                    name="{{ 
                                                                        'requirements[seniority]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}" 
                                                                    id="{{ 'seniority_'.$requirement_field['requirement_id'] }}" 
                                                                    value="{{ $content }}"
                                                                >
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(count($requirements['qualification_increase']) > 0)
                    <h2>Повышение квалификации</h2>
                    <div class="row">
                        @foreach($requirements['qualification_increase'] as $records)
                            <div class="col-md-12 col-sm-12">
                                @foreach(is_array($records['json_content']) ? $records['json_content'] : json_decode($records['json_content'], true) as $index => $record)
                                    <hr>
                                    <h3 class="text-uppercase text-monospace">
                                        {{ $records['requirement']['name'] }} 
                                    </h3>
                                    <div class="row">
                                        @foreach($record as $field_name => $content)
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    @foreach($records['requirement']['fields'] as $requirement_field)
                                                        @if($requirement_field['field_name'] == $field_name)
                                                            <label 
                                                                for="{{ 'qualification_increase_'.$requirement_field['requirement_id'] }}" 
                                                                class="font-weight-light"
                                                            >
                                                                {{ $requirement_field['name'] }}
                                                            </label>
                                                            @if($requirement_field['field_type'] == 'select')
                                                                <select 
                                                                    class="form-control" 
                                                                    name="{{ 
                                                                        'requirements[qualification_increase]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}"
                                                                    id="{{ 'qualification_increase_'.$requirement_field['requirement_id'] }}" 
                                                                >
                                                                    @foreach($requirement_field['options'] as $option)
                                                                        <option 
                                                                            value="{{ $option }}" 
                                                                            {{ $option == $content ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <input 
                                                                    class="form-control" 
                                                                    type="{{ $requirement_field['field_type'] }}" 
                                                                    name="{{ 
                                                                        'requirements[qualification_increase]['
                                                                        .$requirement_field['requirement_id'].
                                                                        '][replaced_index_'
                                                                        .$index.
                                                                        ']['
                                                                        .$requirement_field['field_name'].
                                                                        ']' }}" 
                                                                    id="{{ 'qualification_increase_'.$requirement_field['requirement_id'] }}" 
                                                                    value="{{ $content }}"
                                                                >
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif

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
