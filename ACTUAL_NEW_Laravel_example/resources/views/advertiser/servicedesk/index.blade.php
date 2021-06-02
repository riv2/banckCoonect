@extends('layouts.app')

@section('title', __('advertiser.servicedesk.index.app-title'))

@section('content')

    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>


    <x-box>
        @if ($summary->count_orders)
            {{ __('advertiser.servicedesk.index.total') }}: @number($summary->count_orders)
            @choice(__('advertiser.servicedesk.index.appeal-one') . '|' . __('advertiser.servicedesk.index.appeal-one')
            . '|' . __('advertiser.servicedesk.index.appeals'), $summary->count_orders)
        @endif

        @if ($summary->count_partners && $summary->count_partners > 1)
            {{ __('advertiser.servicedesk.index.from') }} @number($summary->count_partners)
            @choice(__('advertiser.servicedesk.index.partner-one') . '|' . __('advertiser.servicedesk.index.partner-one')
            . '|' . __('advertiser.servicedesk.index.partners'), $summary->count_partners)
        @endif

        @isset ($summary->new_cnt)
            <div class="small">
                {{ __('advertiser.servicedesk.index.new') }}: <a href="{{ route(auth()->user()->role
            . '.servicedesk.index', request()->merge(['status' => 'new'])->except('page')) }}">@number($summary->new_cnt)</a>
            </div>
        @endisset

        @isset ($summary->pending_cnt)
            <div class="small">
                {{ __('advertiser.servicedesk.index.work-in-progress') }}: <a href="{{ route(auth()->user()->role
            . '.servicedesk.index', request()->merge(['status' => 'pending'])->except('page')) }}">@number($summary->pending_cnt)</a>
            </div>
        @endisset

        @isset ($summary->not_closed_cnt)
            <div class="small">
                {{ __('advertiser.servicedesk.index.task-is-not-slowed') }}: <a href="{{ route(auth()->user()->role
            . '.servicedesk.index', request()->merge(['not_closed' => 'true'])->except('page')) }}">@number($summary->not_closed_cnt)</a>
            </div>
        @endisset

        @isset ($summary->expired_cnt)
            <div class="small">
                {{ __('advertiser.servicedesk.index.deadline-expired') }}: <a href="{{ route(auth()->user()->role
            . '.servicedesk.index', request()->merge(['deadline_at' => now()->toDateString()])->except('page')) }}">@number($summary->expired_cnt)</a>
            </div>
        @endisset

        @isset ($summary->closed_cnt)
            <div class="small">
                {{ __('advertiser.servicedesk.index.closed') }}: <a href="{{ route(auth()->user()->role
            . '.servicedesk.index', request()->merge(['status' => 'closed'])->except('page')) }}">@number($summary->closed_cnt)</a>
            </div>
        @endisset
    </x-box>

    <x-box>
        @if (count($collection))
            {{ $collection->links() }}
            <div class="table-responsive">
                <table class="table table-hover table-sm small">
                    <thead>
                    <tr>
                        <th>{{ __('advertiser.servicedesk.index.id') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.creator') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.date-deadline') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.status') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.type') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.responsible') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.title') }}</th>
                        <th>{{ __('advertiser.servicedesk.index.comments') }}</th>
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
                                {!! $item->creator->view_link !!}
                            </td>
                            <td class="text-nowrap">
                                @if ($item->created_at)
                                    <time class="d-block small" datetime="{{ $item->created_at }}">
                                        {{ Date::parse($item->created_at)->format('j F Y') }}
                                    </time>
                                @endif
                                @if ($item->status != 'closed' && $item->deadline_at && $item->deadline_at <= now())
                                    <div class="small text-danger">
                                        {{ __('advertiser.servicedesk.index.deadline-expired') }}
                                        <time class="d-block" datetime="{{ $item->deadline_at }}">
                                            {{ Date::parse($item->deadline_at)->format('j F Y') }}
                                        </time>
                                    </div>
                                @endif
                            </td>
                            <td class="{{ $item->status_class }}">
                                {{ $item->status_text }}
                            </td>
                            <td class="{{ $item->type_class }}">
                                {{ $item->type_text }}
                            </td>
                            <td>
                                {{ $item->doer->email ?? __('advertiser.servicedesk.index.not-assigned') }}
                            </td>
                            <td>
                                <div>{{ $item->subject }}</div>
                            </td>
                            <td>
                                {{ $item->comments_count }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route(auth()->user()->role . '.servicedesk.show', $item) }}" target="_blank"><i class="far fa-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $collection->links() }}
        @else
            <div class="text-center">
                <div class="pb-4">
                    <i class="fas fa-exclamation-circle fa-6x"></i>
                </div>
                {{ __('advertiser.servicedesk.index.no-records-found') }}
            </div>
        @endif
    </x-box>
@endsection
