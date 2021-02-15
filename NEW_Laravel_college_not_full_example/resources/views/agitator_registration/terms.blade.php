@extends('layouts.app')

@section('title', __('Public offer'))

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Public offer')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <form class="col-12" method="POST" enctype="multipart/form-data" action="{{ route('agitatorRegisterProfileIban') }}">

                                {{ csrf_field() }}

                                <div id="terms">
                                    <label>{{__("Terms of use")}}:</label>

                                    <div class="form-group">
                                        <div class="col-12">
                                            <textarea rows="5" readonly class="form-control agreement-text">{!! strip_tags(getcong('agitator_terms_conditions_description')) !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <input type="checkbox" name="agree" id="agree">
                                            <label for="agree" class="control-label">{{__("Accept")}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-info" disabled="disabled" id="sendButton">
                                                {{__("Send")}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script type="text/javascript">

        window.onload = function() {

            $('#agree').click(function () {
                $('#sendButton').prop('disabled', function (i, v) {
                    return !v;
                });
            });

        };

    </script>
@endsection