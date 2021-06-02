@extends('layouts.app')

@section('title', __('advertiser.servicedesk.templates.index.app-title'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manager.servicedesk.index') }}">
                    {{ __('advertiser.servicedesk.templates.index.support') }}</a></li>
            <li class="breadcrumb-item active">{{ __('advertiser.servicedesk.templates.index.current-page') }}</li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">{{ __('advertiser.servicedesk.templates.index.support') }}</h1>
        <a href="{{ route('manager.servicedesk.templates.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm"></i> {{ __('advertiser.servicedesk.templates.index.add') }}</a>
    </div>

    @includeWhen(session()->has('success') || $errors->any(), 'widgets.alerts')

    @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')

    @if (count($collection))
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>{{ __('advertiser.servicedesk.templates.index.id') }}</th>
                    <th>{{ __('advertiser.servicedesk.templates.index.created-at') }}</th>
                    <th>{{ __('advertiser.servicedesk.templates.index.name') }}</th>
                    <th>{{ __('advertiser.servicedesk.templates.index.text') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($collection as $item)
                <tr>
                    <td>
                        #{{ $item->id }}
                    </td>
                    <td>
                        @if ($item->created_at)
                        <time datetime="{{ $item->created_at }}">
                            {{ Date::parse($item->created_at)->format('j F Y') }}
                        </time>
                        @endif
                    </td>
                    <td>
                        @if ($item->is_favorite)
                            <span class="text-danger">*</span>
                        @endif
                        {{ $item->title }}
                    </td>
                    <td>
                        <div class="small" style="word-wrap: break-word;max-width: 700px;">
                            {{ $item->body }}
                        </div>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('manager.servicedesk.templates.show', $item) }}"><i class="far fa-eye"></i></a>
                        <a href="#" onclick="document.forms['delete_template_{{ $item->id }}'].submit();"><i class="fas fa-trash-alt"></i></a>
                        <form name="delete_template_{{ $item->id }}" action="{{ route('manager.servicedesk.templates.destroy', $item) }}" method="post">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center">
        <div class="pb-4">
            <i class="fas fa-exclamation-circle fa-6x"></i>
        </div>
        {{ __('advertiser.servicedesk.templates.index.no-records-found') }}
    </div>
    @endif
</div>
@endsection
