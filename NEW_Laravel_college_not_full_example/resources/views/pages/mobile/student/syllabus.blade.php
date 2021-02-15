@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="study-app">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Syllabus')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div id="subjects" class="row">

                        <h3>{{ $discipline->name }}</h3>

                        @if(count($syllabusLangList) > 1)
                            <div class="row" style="margin-bottom: 20px; margin-left: 5px">
                                @if(in_array('kz', $syllabusLangList))
                                    <a class="btn @if($syllabusLang == 'kz') btn-primary @endif" style="margin-right: 4px" href="{{ route('studentSyllabusByLang', ['disciplineId' => $discipline->id, 'lang' => 'kz']) }}">{{ __('kazakh') }}</a>
                                @endif
                                @if(in_array('ru', $syllabusLangList))
                                    <a class="btn @if($syllabusLang == 'ru') btn-primary @endif" style="margin-right: 4px" href="{{ route('studentSyllabusByLang', ['disciplineId' => $discipline->id, 'lang' => 'ru']) }}">{{ __('russian') }}</a>
                                @endif
                                @if(in_array('en', $syllabusLangList))
                                    <a class="btn @if($syllabusLang == 'en') btn-primary @endif" href="{{ route('studentSyllabusByLang', ['disciplineId' => $discipline->id, 'lang' => 'en']) }}">{{ __('english') }}</a>
                                @endif
                            </div>
                        @endif

                        <div class="accordion" id="accordion" role="tablist">
                            @if( (isset($syllabusList) && count($syllabusList)) > 0 )
                                @foreach($syllabusList as $module_name => $syllabuses)
                                    <div class="card text-center">
                                        <div class="card-header">
                                            {{ $module_name }}
                                        </div>

                                        <div class="card-body">
                                            @foreach($syllabuses as $key => $syllabus)
                                                <div class="card panel panel-default shadow-sm p-3 mb-5 bg-white rounded">
                                                    <div class="card-header panel-heading" role="tab" id="heading{{$key}}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}">
                                                                {{ __('Theme') }}&nbsp;{{$syllabus->theme_number}}:&nbsp;{{$syllabus->theme_name}}
                                                            </button>
                                                        </h2>
                                                    </div>
                                                    <div id="collapse{{$key}}" class="collapse" aria-labelledby="heading{{$key}}" data-parent="#accordion">
                                                        <div class="card-body margin-b15">

                                                            <div class="col-12 margin-b15">
                                                                <label>{{ __('Literature') }}:</label>

                                                                @foreach($syllabus->literature()->where('syllabus_literatures.literature_type', 'main')->get() as $literature)
                                                                    <li class="list-group-item ">
                                                                        <div class="d-flex justify-content-center justify-content-lg-between align-content-center">
                                                                            <div class="align-self-center jus">
                                                                                {{ $literature->name }}
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </div>

                                                            <div class="col-12 margin-b15">
                                                                <label>{{ __('Added literature') }}:</label>

                                                                @foreach($syllabus->literature()->where('syllabus_literatures.literature_type', 'secondary')->get() as $literature)
                                                                    <li class="list-group-item ">
                                                                        <div class="d-flex justify-content-center justify-content-lg-between align-content-center">
                                                                            <div class="align-self-center jus">
                                                                                {{ $literature->name }}
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </div>

                                                            <div class="col-12">
                                                                <hr>
                                                                <div class="col-md-12 no-padding">
                                                                    <label>{{ __('Teoretical material') }}:</label>&nbsp;
                                                                </div>
                                                                <div class="col-md-12 no-padding">
                                                                    @if(isset($syllabus->teoreticalMaterials))
                                                                        @foreach($syllabus->teoreticalMaterials as $item)
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->filename_original }}</a>

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        @if($item->isYoutubeLink())
                                                                                            <iframe width="100%" height="250" src="{{ $item->getPublicUrl() }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                                                        @else
                                                                                            <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->link }}</a>
                                                                                        @endif

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                    @if(isset($syllabus->teoretical_description))
                                                                        <div class="col-12">
                                                                            {!! nl2br($syllabus->teoretical_description)!!}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <hr>
                                                                <div class="col-12 no-padding">
                                                                    <label>{{ __('Practical material') }}:</label>&nbsp;
                                                                </div>
                                                                <div class="col-12 no-padding">
                                                                    @if(isset($syllabus->practicalMaterials))
                                                                        @foreach($syllabus->practicalMaterials as $item)
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->filename_original }}</a>

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        @if($item->isYoutubeLink())
                                                                                            <iframe width="100%" height="250" src="{{ $item->getPublicUrl() }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                                                        @else
                                                                                            <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->link }}</a>
                                                                                        @endif

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                    @if(isset($syllabus->practical_description))
                                                                        <div class="col-12">
                                                                            {!! nl2br($syllabus->practical_description)!!}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <hr>
                                                                <div class="col-12 no-padding">
                                                                    <label>{{ __('Independent work') }}:</label>&nbsp;
                                                                </div>
                                                                <div class="col-12 no-padding">
                                                                    @if(isset($syllabus->sroMaterials))
                                                                        @foreach($syllabus->sroMaterials as $item)
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->filename_original }}</a>

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        @if($item->isYoutubeLink())
                                                                                            <iframe width="100%" height="250" src="{{ $item->getPublicUrl() }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                                                        @else
                                                                                            <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->link }}</a>
                                                                                        @endif

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                    @if(isset($syllabus->sro_description))
                                                                        <div class="col-12">
                                                                            {!! nl2br($syllabus->sro_description)!!}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <hr>
                                                                <div class="col-12 no-padding">
                                                                    <label>{{ __('Independent work of the student with the teacher') }}:</label>&nbsp;
                                                                </div>
                                                                <div class="col-12 no-padding">
                                                                    @if(isset($syllabus->sropMaterials))
                                                                        @foreach($syllabus->sropMaterials as $item)
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->filename_original }}</a>

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if($item->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                                                                                <div class="card col-12 no-padding">
                                                                                    <div class="card-body">
                                                                                        @if($item->isYoutubeLink())
                                                                                            <iframe width="100%" height="250" src="{{ $item->getPublicUrl() }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                                                        @else
                                                                                            <a href="{{ $item->getPublicUrl() }}" target="_blank">{{ $item->link }}</a>
                                                                                        @endif

                                                                                        <p class="card-text">{{ $item->link_description }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                    @if(isset($syllabus->srop_description))
                                                                        <div class="col-12">
                                                                            {!! nl2br($syllabus->srop_description)!!}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div>{{__('Theme list is empty')}}</div>
                            @endif
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

