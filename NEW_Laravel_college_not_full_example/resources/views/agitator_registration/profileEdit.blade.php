@extends('layouts.app')

@section('title', __('Registration of agitator'))

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Registration of agitator')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <div class="col-md-9 col-md-offset-1" id="table" style="{{ ($profile->user_approved == 1)?'display: none;':'' }}">

                                <blockquote>{{__("Please check your data, if everything is correct, confirm the data. If the data is not correct, then click back and download the file again")}}.</blockquote>

                                <ul class="list-group">
                                    @if($profile->faceimg)
                                        <li class="list-group-item" style="text-align: center">
                                            <img
                                                    src="{{ \App\Services\Avatar::getStudentFacePublicPath($profile->faceimg) ?? '' }}"
                                                    alt=" {{ $profile->fio ?? '' }}"
                                                    style="max-width: 400px"
                                            />
                                        </li>
                                    @endif
                                    <li class="list-group-item"> {{__('ITN')}}: {{$profile->iin}}</li>
                                    <li class="list-group-item"> {{__('Full name')}}: {{$profile->fio}}</li>
                                    <li class="list-group-item"> {{__('Birth date')}}:
                                        @if( isset($profile->bdate) )
                                            {{$profile->bdate->format('d.m.Y')}}
                                        @endif
                                    </li>

                                    @if($profile->pass == 1)
                                        <li class="list-group-item"> {{__('Passport number')}} : {{$profile->docnumber}} </li>
                                    @else
                                        <li class="list-group-item"> {{__('ID number')}} : {{$profile->docnumber}} </li>
                                    @endif

                                <!--<li class="list-group-item"> {{__('Document number')}}: {{$profile->docnumber}}</li>-->
                                    <li class="list-group-item"> {{__('Issuing authority')}}: {{$profile->issuing}}</li>
                                    <li class="list-group-item"> {{__('Issue date')}}:
                                        @if( isset($profile->issuedate) )
                                            {{ $profile->issuedate->format('d.m.Y') }}
                                        @endif
                                    </li>
                                    <li class="list-group-item"> {{__('Expire date')}}:
                                        @if( isset($profile->expire_date) )
                                            {{ $profile->expire_date->format('d.m.Y') }}
                                        @endif
                                    </li>


                                    @if($profile->sex == 1)
                                        <li class="list-group-item">{{__('Sex')}}: {{__('Male')}}</li>
                                    @else
                                        <li class="list-group-item">{{__('Sex')}}: {{__('Female')}}</li>
                                    @endif

                                </ul>

                                <a class="btn btn-info" href="{{ route('agitatorRegisterProfileID',[
                            'back' => 1
                        ]) }}"> {{__("Back")}} </a>
                                <button id="approve" class="btn btn-primary">
                                    {{__("Approve")}}
                                </button>

                            </div>


                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

<script type="text/javascript">
    window.onload = function(){

        $('#approve').click(function() {
            $.ajax({
                url:'{{ route('agitatorRegisterProfileApprove') }}',
                type:'get',
                success:function(response){
                    var response = JSON.parse(response);
                    if(response.status == 'fail') {

                    } else {

                        window.location.href = '{{ route('agitatorRegisterProfileIban') }}';
                    }
                }
            });

        });

        $('body').on('keypress', '.error', function() {
            $(this).removeClass('error');
        });

    };

</script>