@extends('layouts.app')

@section('title', __('menu.partner.faq.index'))

@section('content')
    @foreach($collection as $item)
    <x-box>
        <x-slot name="title">{{ $item->title }}</x-slot>

        <div class="accordion" id="accordion{{$item->id}}">
            @foreach($item->faq as $faq)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{$faq->id}}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$faq->id}}" aria-expanded="false" aria-controls="collapse{{$faq->id}}">
                        {{ $faq->question }}
                    </button>
                </h2>

                <div id="collapse{{$faq->id}}" class="accordion-collapse collapse" aria-labelledby="heading{{$faq->id}}" data-bs-parent="#accordion{{$item->id}}">
                    <div class="accordion-body">
                        {!! $faq->answer !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </x-box>
    @endforeach
@endsection
