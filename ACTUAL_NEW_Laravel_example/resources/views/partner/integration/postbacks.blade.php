@extends('layouts.app')

@section('title', __('partners.postbacks.settings.app-title'))

@section('content')
    <script>
        function url_change() {
            var url = document.getElementById('pb_postback_url').value;
            var req = (url.length > 3)
            var inputs = ['pb_order_id', 'pb_status'];
            for (var n in (inputs)) {
                console.log(inputs[n])
                document.getElementById(inputs[n]).setAttribute('required', req);
            }
        }
    </script>
    <x-box>
        <x-slot name="title">{{ __('partners.postbacks.settings.title') }}</x-slot>
        <form method="post" action="{{ route('partner.postbacks.store') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')
            @php
                $fields = [
                        'postback_url'=>['text',[],''],
                        'postback_auth'=>['text',[],''],
                        'method'=>['select',['get'=>'get','json'=>'json','post'=>'post'],''],
                        'order_id'=>['text',[],'order_id'],
                        'status'=>['text',[],'status'],
                        'amount'=>['text',[],'amount'],
                        'gross_amount'=>['text',[],'gross_amount'],
                        'status_new_value'=>['text',[],'new'],
                        'status_approve_value'=>['text',[],'approve'],
                        'status_sale_value'=>['text',[],'sale'],
                        'status_reject_value'=>['text',[],'reject'],
                        'web_id'=>['text',[],'web_id'],
                        'click_id'=>['text',[],'click_id'],
                        'fee_id'=>['text',[],'fee_id'],
                ];
            @endphp
            @foreach($fields as $name=>$field)
                @php
                    $value = (empty($notify_params->$name)) ? '' : $notify_params->$name;
                @endphp

                @if ($field[0] == 'select')
                    <x-input type="select" name="{{ $name }}"
                             :options="$field[1]"
                             value="{{ $value }}"
                             placeholder="{{ __('partners.postbacks.settings.'.$name.'.placeholder') }}" autofocus>
                        {{ __('partners.postbacks.settings.'.$name.'.name') }}
                        <x-slot name="help">{{ __('partners.postbacks.settings.'.$name.'.desc') }}</x-slot>
                    </x-input>
                @else
                    @php
                        $onchange = ($name == 'postback_url') ? 'url_change()' : '';
                    @endphp
                    <x-input type="{{ $field[0] }}" name="{{ $name }}"
                             onchange="{{ $onchange }}"
                             id="pb_{{ $name }}"
                             value="{{ $value }}"
                             placeholder="{{ __('partners.postbacks.settings.'.$name.'.placeholder') }}" autofocus>
                        {{ __('partners.postbacks.settings.'.$name.'.name') }}
                        <x-slot name="help">{{ __('partners.postbacks.settings.'.$name.'.desc') }}</x-slot>
                    </x-input>
                @endif


            @endforeach
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">{{ __('partners.postbacks.settings.save') }}</button>
            </div>
        </form>

        @if(count($notify) > 0)
            <h6>{{ __('partners.orders.show.postbacks') }}</h6>
            @php
                $format = [
                    'sent_datetime' => 'format.datetime',
                    'sent_url' => '',
                    'status' => '',
                    'responce_httpcode' => '',
                    'responce_body' => '',
                ];
            @endphp
            <x-table :data="$notify" :format="$format">
                <x-slot name="empty">
                    {{ __('partners.orders.show.products.no-records-found') }}
                </x-slot>
            </x-table>
        @endif
    </x-box>
@endsection

