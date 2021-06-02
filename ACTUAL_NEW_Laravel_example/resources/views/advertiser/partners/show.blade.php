@extends('layouts.app')

@section('title', __('advertiser.partners.show.app.title') . ' ' . $user->email)

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.partners.show.title') }} {{ $user->email }}</x-slot>
        <x-slot name="rightblock">
            {!! $user->impersonate_link_button !!}
        </x-slot>

        <form method="post" action="{{ route(request()->user()->role . '.partners.update', $user) }}">
            <input type="hidden" name="id" value="{{ $user->id }}">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="form-label-group mb-3 col col-6">
                    <label>Имя</label>
                    <input class=form-control type="text" name="name" value="{{ $user->name }}" required>
                </div>
                <div class="form-label-group mb-3 col col-6">
                    <label>Статус</label>
                    <select class=form-control type="text" name="status" required>
                        @foreach(\App\Lists\PartnerStatusesList::getList() as $k=>$v)
                            <option value="{{ $k }}" @if($user->status == $k) selected @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-label-group mb-3 col col-6">
                    <label>Телефон</label>
                    <input class=form-control type="text" name="phone" value="{{ $user->phone }}">
                </div>
                <div class="form-label-group mb-3 col col-6">
                    <label>Скайп</label>
                    <input class=form-control type="text" name="skype" value="{{ $user->skype }}">
                </div>
                @foreach (App\User::$fields as $name => $field)
                    @php
                        $value = $user->{$name};
                        $caption = $field['caption'] ?? $name;
                        $input_type = $field['type'] ?? 'text';
                        $input_attrs = join(' ', $field['attrs'] ?? []);
                    @endphp
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>{{ $caption }}</label>
                            @if ($input_type == 'datetime')
                                @if($caption == __('user.contract-date'))
                                <input type="date" name="{{ $name }}" class="form-control"
                                       value="{{ $value ? Date::parse($value)->format('Y-m-d'): '' }}" {{ $input_attrs }}>
                                @else
                                <input type="datetime-local" name="{{ $name }}" class="form-control"
                                       value="{{ $value ? Date::parse($value)->format('Y-m-d\TH:i:s') : '' }}" {{ $input_attrs }}>
                                @endif
                            @elseif (in_array($input_type, ['text', 'email', 'tel']))
                                <input type="{{ $input_type }}" name="{{ $name }}" class="form-control"
                                       value="{{ $value }}" {{ $input_attrs }}>
                            @elseif ($input_type === 'textarea')
                                <textarea name="{{ $name }}" class="form-control"
                                          rows="1" {{ $input_attrs }}>{{ $value }}</textarea>
                            @elseif ($input_type === 'select')
                                <select name="{{ $name }}" class="form-select" {{ $input_attrs }}>
                                    @foreach ($field['values'] as $option_id => $option)
                                        <option value="{{ $option_id }}"
                                                @if ($value==$option_id) selected @endif>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif ($input_type === 'checkbox')
                                <select name="{{ $name }}" class="form-select" {{ $input_attrs }}>
                                    <option value="0" @if (!$value) selected @endif>{{ __('advertiser.partners.show.no') }}</option>
                                    <option value="1" @if ($value) selected @endif>{{ __('advertiser.partners.show.yes') }}</option>
                                </select>
                            @elseif ($input_type === 'tags')
                                {{ collect($value)->pluck('tag')->join(', ') }}
                            @else
                                @dump($value)
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('advertiser.partners.show.save') }}</button>
            </div>
        </form>
    </x-box>
@endsection
