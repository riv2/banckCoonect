@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Speciality list page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body row">

                    @if($application == 'master')
                        <div class="alert alert-warning">
                            <p>
                                {{__('The profile magistracy is of an applied nature and is aimed at deepening the professional training of managerial personnel and specialists in the field. The term of study is 1 year.')}}
                            </p>
                            <p>
                                {{__('Scientific and pedagogical magistracy is aimed at in-depth research training, teaching activities, it is necessary to continue training in doctoral programs. Duration of training - 2 years.')}}
                            </p>
                        </div>
                    @endif

                    <ul>
                        @foreach($specialties as $speciality)

                            <li>
                                <a  href="{{ route('specialitySelectSave', ['id' => $speciality->id, 'application' => $application]) }}">
                                    {{$speciality->$locale}}
                                </a>
                            </li>

                        @endforeach
                    </ul>


                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">

        function selectSpeciality(redirectUrl)
        {
            if(confirm('{{__('The profile magistracy is of an applied nature and is aimed at deepening the professional training of managerial personnel and specialists in the field. The term of study is 1 year.')}}\n' +
                '\n' +
                '{{__('Scientific and pedagogical magistracy is aimed at in-depth research training, teaching activities, it is necessary to continue training in doctoral programs. Duration of training - 2 years.')}}'))
            {
                document.location.href = redirectUrl;
            }
        }

    </script>
@endsection