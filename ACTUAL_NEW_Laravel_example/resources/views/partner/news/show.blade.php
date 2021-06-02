@extends('layouts.app')

@section('title', __('Новости'))

@section('content')
<x-box>
    <x-slot name="title">{{ $item->news_title }}</x-slot>
    {!! $item->news_text_parsed !!}
</x-box>
@endsection
