@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Profile page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div id="error-block" class="alert alert-danger fade" role="alert"></div>

                    <div id="table">

                        <h4> {{ __('Manual data entry of an identity document') }} </h4>

                        <ul class="list-group">
                            <li class="list-group-item">
                                <input type="checkbox" name="alien" id="alien" onchange="changeAlien()" value="1">&nbsp;
                                <label for="alien" class="control-label font-size08">{{__('I am not a citizen of Kazakhstan')}}</label>
                            </li>
                            <li class="list-group-item"> {{__('ITN')}} {{__('in the presence of')}}: <input class="form-control" type="text" name="iin"></li>
                            <li class="list-group-item"> {{__('Full name')}}: <input class="form-control" type="text" name="fio"></li>
                            <li class="list-group-item"> {{__('Birth date')}}: <input class="form-control" type="date" name="bdate"></li>

                            <li class="list-group-item"> {{__('Nationality')}}:
                                <select class="form-control" name="nationality" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
                                    @foreach($nationalityList as $item)
                                        <option value="{{$item->id}}">
                                            @if(App::isLocale('en'))
                                                {{$item->name}}
                                            @endif
                                            @if(App::isLocale('ru'))
                                                {{$item->name_ru}}
                                            @endif
                                            @if(App::isLocale('kz'))
                                                {{$item->name_kz}}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </li>

                            <li class="list-group-item"> {{__('Document number')}} : <input class="form-control" type="text" name="docnumber"></li>


                            <li class="list-group-item"> {{__('Issuing authority')}}: <input class="form-control" type="text" name="issuing"></li>
                            <li class="list-group-item"> {{__('Issue date')}}: <input class="form-control" type="date" name="issuedate"></li>
                            <li class="list-group-item"> {{__('Expire date')}}: <input class="form-control" type="date" name="expire_date"></li>

                            <li class="list-group-item">{{__('Sex')}}:
                                <select id="sex" name="sex" class="form-control">
                                    <option value="1">{{__('Male')}}</option>
                                    <option value="0">{{__('Female')}}</option>
                                </select>
                            </li>

                        </ul>


                        <label class="col-12">{{__("Terms of use")}}:</label>
                        <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                            <div class="col-12">
                                <textarea style="width:100%;" rows="5" readonly class="form-control agreement-text">{!! strip_tags(getcong('terms_conditions_description')) !!}</textarea>
                            </div>
                            <div class="col-12">
                                <input type="checkbox" name="agree" id="agree">
                                <label for="agree" class="control-label">{{__("Accept")}}</label>
                            </div>
                        </div>

                        <button id="back" class="btn btn-info btn-lg">
                            {{__("Back")}}
                        </button>
                        <button id="approve" class="btn btn-info btn-lg" disabled="disabled">
                            {{__("Send")}}
                        </button>

                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">

        var alien = false;

        function changeAlien()
        {
            alien = $('[name=alien]').prop('checked');
        }

        window.onload = function(){

            $('#agree').click(function(){
                $('#approve').prop('disabled', function(i, v) { return !v; });
             });


            $('#back').click(function() {
                window.location.href = "{{ route('userProfileIDPost') }}";
            });

            $('#approve').click(function() {

                var inn = $("input[name=iin]");
                var fio = $("input[name=fio]");
                var bdate = $("input[name=bdate]");
                var docnumber = $("input[name=docnumber]");
                var issuing = $("input[name=issuing]");
                var issuedate = $("input[name=issuedate]");
                var expire_date = $("input[name=expire_date]");
                var nationality = $("select[name=nationality]");

                var error = false;
                var errorList = [];

                $('#error-block').html('').addClass('fade');

                if ( fio.val().length < 7 ) {
                    fio.addClass('error');
                    error = true;
                    errorList.push( "{{ __('Name field must contain more than 7 characters') }}" );
                } else {
                    fio.removeClass('error');
                }

                if ( (docnumber.val().length < 7) || (docnumber.val().length > 20) ) {
                    docnumber.addClass('error');
                    error = true;
                    errorList.push( "{{ __('Document Number must contain less 21 characters') }}" );
                } else {
                    docnumber.removeClass('error');
                }

                if(!alien) {

                    if (bdate.val().length < 7) {
                        bdate.addClass('error');
                        error = true;
                        errorList.push( "{{ __('Date of birth required') }}" );
                    } else {
                        bdate.removeClass('error');
                    }

                    if ( (inn.val().length < 5) || (inn.val().length > 12) || isNaN( inn.val() ) ){
                        inn.addClass('error');
                        error = true;
                        errorList.push( "{{ __('IIN field must be number and contain less 13 characters') }}" );
                    } else {
                        inn.removeClass('error');
                    }

                    if (issuing.val().length < 10) {
                        issuing.addClass('error');
                        error = true;
                        errorList.push( "{{ __('Field issued is required') }}" );
                    } else {
                        issuing.removeClass('error');
                    }

                    if (issuedate.val().length < 7) {
                        issuedate.addClass('error');
                        error = true;
                        errorList.push( "{{ __('Issuing date is required') }}" );
                    } else {
                        issuedate.removeClass('error');
                    }

                    if (expire_date.val().length < 7) {
                        expire_date.addClass('error');
                        error = true;
                        errorList.push( "{{ __('Expire date is required') }}" );
                    } else {
                        expire_date.removeClass('error');
                    }

                } else {

                    bdate.removeClass('error');
                    issuing.removeClass('error');
                    issuedate.removeClass('error');
                    expire_date.removeClass('error');

                }

                if (error) {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#table").offset().top
                    }, 2000);

                    $('#error-block').html( errorList.join('<br> * ') ).removeClass('fade');
                    return;
                }

                $.ajax({
                    url:'{{ route('profileIDManualPost') }}',
                    type:'get',
                    data: {
                        "alien": alien,
                        "iin": inn.val(),
                        "fio": fio.val(),
                        "bdate": bdate.val(),
                        "docnumber": docnumber.val(),
                        "issuing": issuing.val(),
                        "issuedate": issuedate.val(),
                        "expire_date": expire_date.val(),
                        "nationality": nationality.val(),
                        "sex": $("#sex").val()
                    },
                    success:function(response){
                        var response = JSON.parse(response);
                        if(response.status == 'fail') {
                            alert(response.text);
                        } else {

                            window.location.href = '{{ route('profileEmail') }}';

                        }
                    }
                });

            });

            $('body').on('keypress', '.error', function() {
                $(this).removeClass('error');
            });

    };

    </script>
@endsection