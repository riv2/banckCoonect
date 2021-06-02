@extends('layouts.app')

@section('title', __('manager.pixel.index.app-title'))

@section('content')
    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>

    <x-box>
        <x-slot name="title">{{ __('manager.pixel.index.pixel-logs') }}</x-slot>
        <x-slot name="rightblock">
            <form action="{{ route('manager.pixel.recalc', request()->except('page')) }}" method="post"
                  id="pixelrecalcform">
                @csrf
            </form>
            <div onclick="document.forms['pixelrecalcform'].submit();" class="btn btn-outline-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('manager.pixel.index.recalculate') }}
            </div>
        </x-slot>

        @if ($collection instanceof Illuminate\Pagination\AbstractPaginator)
            {{ $collection->appends(request()->except('page'))->links() }}
        @endif
        <div class="table-responsive">
            <table class="table table-striped table-hover" style="table-layout: fixed">
                <thead>
                <tr>
                    <th style="width: 173px;">ID, {{ __('manager.pixel.index.time') }}, GUID</th>
                    <th>{{ __('manager.pixel.index.event') }}</th>
                    <th>{{ __('manager.pixel.index.data') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($collection as $row)
                    <tr class="{{ $row->tr_class }}">
                        <td class="small">
                            <div>#{{ $row->id }}</div>
                            <x-format.datetime class="d-block" :value="$row->created_at" format="d F Y H:i:s"/>
                            <div>
                                <a href="{{ route('manager.pixel.index', request()->except('page') + ['pp_id' => $row->pp_id ?? null]) }}"
                                   class="small">
                                    <x-format.pp-link :value="$row->pp"/>
                                </a>
                            </div>
                            @if ($row->is_valid)
                                <span class="badge bg-info text-dark">{{ __('manager.pixel.index.our-marks') }}</span>
                            @endif
                            @if ($row->is_click)
                                <span class="badge bg-warning text-dark">{{ __('manager.pixel.index.click') }}</span>
                                @if (!$row->click)
                                    <span
                                        class="badge bg-danger text-dark">{{ __('manager.pixel.index.click-error') }}</span>
                                @endif
                            @endif
                            @if ($row->is_order)
                                <span class="badge bg-success text-dark">{{ __('manager.pixel.index.order') }}</span>
                                @if (!$row->order)
                                    <span
                                        class="badge bg-danger text-dark">{{ __('manager.pixel.index.order-error') }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="small">
                            <div>{{ $row->event_text }}</div>
                            <code style="display:block; max-width: 100%;">{{ $row->data['dl'] ?? '[null]' }}</code>
                            <div>
                                <a href="{{ route('manager.pixel.index', request()->except('page') + ['guid' => $row->data['uid'] ?? null]) }}"
                                   class="small"><code>{{ $row->data['uid'] ?? null }}</code></a>
                            </div>
                            <div title="{{ $row->data['ua'] ?? '[null]' }}">
                                {!! $row->browser_icon !!}
                                {{ $row->data['bn'] ?? '[null]' }}
                            </div>
                            <div>
                                {{ $row->ip }}
                                @if ($geo = geoip($row->ip))
                                    {{ $geo->city }}
                                    {{ $geo->country }}
                                @endif
                            </div>
                            <details>
                                <summary>Запрос</summary>
                                <pre class="small">@json($row->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)</pre>
                            </details>
                        </td>
                        <td class="small">
                            <div>
                                <div class="text-truncate">utm_medium: {{ $row->data['utm_medium'] ?? '[null]' }}</div>
                                <div class="text-truncate">utm_source: {{ $row->data['utm_source'] ?? '[null]' }}</div>
                                <div class="text-truncate">utm_campaign
                                    (link_id): {{ $row->data['utm_campaign'] ?? '[null]' }}</div>
                                @isset ($row->data['utm_term'])
                                    <div class="text-truncate">utm_term (web_id): {{ $row->data['utm_term'] }}</div>
                                @endisset
                                @isset ($row->data['utm_content'])
                                    <div class="text-truncate">utm_content
                                        (partner_id): {{ $row->data['utm_content'] }}</div>
                                @endisset
                                @isset ($row->data['click_id'])
                                    <div class="text-truncate">click_id: {{ $row->data['click_id'] }}</div>
                                @endisset
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if ($collection instanceof Illuminate\Pagination\AbstractPaginator)
            {{ $collection->appends(request()->except('page'))->links() }}
        @endif
    </x-box>
@endsection

