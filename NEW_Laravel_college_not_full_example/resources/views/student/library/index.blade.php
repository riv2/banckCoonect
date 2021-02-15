@extends('layouts.app')

@section('title', __('Library'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('Library')</h2>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <a href="http://rmebrk.kz/" target="_blank">Республиканская Межвузовская Электронная Библиотека</a>
                        <label class="text-muted">@lang('Interuniversity Digital Library Access')</label>
                    </div>
                </div>
            </div>

            <div class="row margin-t30">
                <div class="col-md-6">
                    <h4>@lang('Literature Catalog')</h4>
                </div>
                <div class="col-md-6">
                    {!! Form::open([
                        'id' => 'searchForm',
                        'class' => 'form-inline d-flex justify-content-end',
                        'url' => route('library.page'),
                        'method' => 'GET'
                    ]) !!}
                        <div class="input-group">
                            <div href="javascript:{}" onclick="document.getElementById('searchForm').submit(); return false;" class="input-group-prepend">
                                <span class="input-group-text" style="cursor: pointer;">&#128269;</span>
                            </div>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="search" 
                                placeholder="@lang('Search by part text')" 
                                aria-label="Search" 
                                id="searchInput" 
                                value="{{ request()->get('search')?? '' }}"
                            >
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            
            <div id="accordion">
                @foreach($literatures as $literature)
                    <div class="card margin-t20">
                        <div 
                            id="heading{{ $literature->id }}" 
                            class="btn btn-link collapsed padding-0" 
                            data-toggle="collapse" 
                            data-target="#collapse{{ $literature->id }}" 
                            aria-expanded="false" 
                            aria-controls="collapse{{ $literature->id }}"
                        >
                            <h5 class="mb-0 text-left">
                                <button class="btn btn-link">
                                    {{ $literature->name }}
                                </button>
                            </h5>
                        </div>

                        <div 
                            id="collapse{{ $literature->id }}" 
                            class="collapse" 
                            aria-labelledby="heading{{ $literature->id }}" 
                            data-parent="#accordion"
                        >
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Вид издания</label>
                                    <div class="col-sm-10">
                                        <input type="text" readonly class="form-control-plaintext" value="{{ $literature->publication_type }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Год издания</label>
                                    <div class="col-sm-10">
                                        <input type="text" readonly class="form-control-plaintext" value="{{ $literature->publication_year }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Автор</label>
                                    <div class="col-sm-10">
                                        <input type="text" readonly class="form-control-plaintext" value="{{ $literature->author }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Действие</label>
                                    <div class="col-sm-10">
                                        @if(!$literature->e_books_name)
                                            <a href="{{ route('show.literature.page', ['id' => $literature->id]) }}">
                                                <button class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Подробней">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </a>
                                        @else
                                            <a 
                                                href="{{ route('download.file', ['fileName' => $literature->e_books_name, 'id' => $literature->id]) }}" 
                                                class="btn btn-primary"
                                            >
                                                @lang('Download')
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-center">
                    {{ $literatures->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection