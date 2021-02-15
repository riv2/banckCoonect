@extends('layouts.app')

@section('content')
    <div id="study-app" class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('References')}}</div>

                    <div class="panel-body">
                        <br>
                        <div v-if="serviceMessage" :class="{ 'alert-danger': serviceError, 'alert-success': !serviceError }" class="alert">
                            <div v-html="serviceMessage"></div>
                        </div>

                        @if(!empty($references))
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ __('Nomination') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($references as $reference)
                                    <tr>
                                        <td> {{ $reference->$locale ?? $reference->name }} </td>
                                        <td> {{ $reference->cost }} </td>
                                        <td>
                                            <button @click="serviceBuyService({{ $reference->id }})" :disabled="serviceSendRequest" class="btn btn-info btn-sm" type="button" @if(in_array($reference->id, $boughtServiceIds)) disabled="disabled" @endif>{{ __('Buy') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection