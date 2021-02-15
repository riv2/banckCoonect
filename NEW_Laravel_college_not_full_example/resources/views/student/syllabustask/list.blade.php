@extends('layouts.app')

@section('title', __('SRO'))

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-task-list">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('SRO')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="panel panel-default col-md-">
                        <div class="panel-body">
                            <div class="row padding-20">
                                <form class="col-md-3">
                                    <div class="form-group">
                                        <label> {{ __('Select a language passing SRO') }} </label>
                                        <select @change="changeLang($event)" v-model="currentLang" class="form-control">
                                            @if( !empty($lang) )
                                                @foreach($lang as $itemLang)
                                                    <option value="{{ $itemLang }}"> {{ __($itemLang) }} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="render-view-sro">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        window.instanciaVue = new Vue();

        var app = new Vue({
            el: '#main-task-list',
            data: {
                currentLang: '{{ \App\Profiles::EDUCATION_LANG_KZ }}',
                currentDisciplineId: '{{ $discipline_id }}',
                renderView: false
            },
            methods: {
                changeLang: function(event){
                    this.currentLang = event.target.value;
                    this.getList();
                },
                getList: function(){
                    var self = this;
                    axios.post('{{ route('sroRenderList') }}',{
                        "_token": "{{ csrf_token() }}",
                        "discipline_id": this.currentDisciplineId,
                        "lang": this.currentLang
                    })
                    .then(function(response){
                        if( response.data ){
                            $('#render-view-sro').html('').append( response.data );
                        }
                    })
                    .catch( error => {
                        console.log(error)
                    });
                }
            },
            created: function (){
                this.getList();
            }
        });
    </script>
@endsection

