@extends('layouts.app')

@section('title', __('advertiser.support.list.app-title'))

@section('content')
    <x-box>
        <form method="post" action="{{ route(auth()->user()->role . ".support.send") }}">
            @csrf
            <div class="mb-3">
                <label>{{ __('advertiser.support.list.question-type') }}</label>
                <select class="form-select" name="case_type">
                    @foreach($cases as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>{{ __('advertiser.support.list.your-question') }}</label>
                <textarea rows="7" name="case_description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">{{ __('advertiser.support.list.submit') }}</button>
            </div>
        </form>
    </x-box>
    <x-box>
        <x-slot name="title">{{ __('advertiser.support.list.table-title') }}</x-slot>
        @php
        $format = [
            'view_link' => 'html',
            'user' => 'format.user-link',
            'case_type_text' => 'text',
            'created_at' => 'format.datetime',
        ];
        @endphp
        <x-table :data="$support" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th></th>
                    <th>{{ __('advertiser.support.list.table-author') }}</th>
                    <th>{{ __('advertiser.support.list.table-type') }}</th>
                    <th>{{ __('advertiser.support.list.table-created-at') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection

