@extends('layouts.app')

@section('title', __('advertiser.banned_links.show.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.banned_links.show.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route('advertiser.banned-links.index') }}" class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.banned_links.show.to-list') }}
            </a>
        </x-slot>
        <div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.link-id') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        {{ $bannedLink->link_id }}
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.web-id') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        {{ $bannedLink->web_id }}
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.partner') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        #{{ $link->partner->id }} {{ $link->partner->name }}
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.date-start') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        @if($bannedLink->date_start)
                            {{ $bannedLink->date_start->format('Y-m-d')}}
                        @endif
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.date-end') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        @if($bannedLink->date_end)
                            {{ $bannedLink->date_end->format('Y-m-d')}}
                        @endif
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.comment') }}</p>
                </div>
                <div class="col-md-9 banned-link">
                    <p class="my-2">
                        {!! $bannedLink->comment !!}
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_links.show.evidence') }}</p>
                </div>
                <div class="col-md-9 banned-link">
                    <p class="my-2">
                        {!! $bannedLink->evidence !!}
                    </p>
                </div>
            </div>

            <form action="{{ route("advertiser.banned-links.destroy", $bannedLink) }}" method="post">
                @csrf
                @method('DELETE')
                <div class="mt-4">
                    <button class="btn btn-danger"
                            type="submit">{{ __('advertiser.banned_links.show.destroy') }}</button>
                </div>
            </form>
        </div>
    </x-box>
@endsection
