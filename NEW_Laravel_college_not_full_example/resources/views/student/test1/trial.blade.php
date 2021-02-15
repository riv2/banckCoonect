@extends('layouts.app')

@section('title', __('Pay'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin">@lang('Pay')</h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    @if ($lowBalance)
                        <div class="alert alert-warning margin-b20" role="alert">@lang('Not enough funds on balance')</div>
                    @endif
                    <p>@lang("Pay sum"): {{$service->cost}} тг</p>

                    <a href="{{route('studentPayTest1Trial', ['id' => $SD->discipline->id])}}" class="btn btn-info btn-lg btn-block margin-b15 @if($lowBalance) disabled @endif" onclick="return submit(this);">@lang('Pay')</a>

                    @if($lowBalance)
                        <a href="{{route('studentPayToBalance')}}" class="btn btn-primary btn-lg btn-block margin-b15">@lang('Replenish account')</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function submit(link) {
            if (confirm('@lang('Do you confirm this purchase?')')) {
                $(link).addClass('disabled');
                return true;
            } else {
                return false;
            }
        }
    </script>
@endsection