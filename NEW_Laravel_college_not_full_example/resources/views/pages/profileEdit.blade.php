@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__("Profile page")}}</div>

                <div class="panel-body">
                    


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

                        <a class="btn btn-info" href="{{ route('userProfileID',[
                            'back' => 1
                        ]) }}"> {{__("Back")}} </a>
                        <button id="approve" class="btn btn-primary">
                            {{__("Approve")}}
                        </button>
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script type="text/javascript">
    window.onload = function(){

        $('#approve').click(function() {
            $.ajax({
                url:'{{ route('profileApprove') }}',
                type:'get',
                success:function(response){
                    var response = JSON.parse(response);
                    if(response.status == 'fail') {

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
