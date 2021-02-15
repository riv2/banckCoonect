@extends("admin.admin_app")

@section('title', 'Редактирование темы "'. $syllabus->theme_name .'"')

@section("content")

    <div id="main">
        <div class="page-header">

            <p class="sillabus-title"> Дисциплина: {{ $oDiscipline->name }}, Кредиты: {{ $oDiscipline->ects }} </p>
            <div class="clearfix"></div>

            <h2> </h2>

            <a href="{{ route('adminSyllabusList', ['disciplineId' => $disciplineId]) }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default">
            @include('admin.pages.syllabus.form.params')
        </div>

        <!-- task R2 -->
        <div class="panel panel-default">
            <div class="panel-body">

                @include('admin.pages.syllabus.task.list', ['syllabus' => $syllabus,'discipline' => $oDiscipline])

            </div>
        </div>


    </div>

    <script type="text/javascript">
        function changeThemeLang(lang)
        {
            $('.theme-panel').hide();
            $('.theme-panel-' + lang ).show();
            $('ul.nav-tabs-theme-lang li').removeClass('active');
            $('ul.nav-tabs-theme-lang li.tab-' + lang).addClass('active');
        }

        function ajaxSaveQuestion()
        {

        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script>
    <script type="text/javascript">
        let newView = new Vue({
            el: '#addLiteratureToSyllabusBlock',
            data: {
                mainLiteratureList: [],
                secondaryLiteratureList: [],
                mainLiterature: {!! json_encode($mainLiterature) !!},
                secondaryLiterature: {!! json_encode($secondaryLiterature) !!}
            },
            methods: {
                liveSearch: function(event) {
                    if(event.target.value.length > 5){
                        const data = {};
                        data['search'] = event.target.value;
                        axios.post('{{ route('syllabus.literature.live.search') }}', data)
                            .then(response => {
                                if(event.target.dataset.literature == 'main'){
                                    this.mainLiteratureList = response.data.literature;
                                } else {
                                    this.secondaryLiteratureList = response.data.literature;
                                }
                            });
                    } else {
                        if(event.target.dataset.literature == 'main'){
                            this.mainLiteratureList = [];
                        } else {
                            this.secondaryLiteratureList = [];
                        }
                    }
                },
                addLiteratureToList: function(id, name, type) {
                    var mainLiteratureCheck = this.mainLiterature.filter(function(elem){
                            if(elem.id == id){
                                return elem.id;
                            }
                        });
                    var secondaryLiteratureCheck = this.secondaryLiterature.filter(function(elem){
                            if(elem.id == id){
                                return elem.id;
                            }
                        });

                    if(type == 'main'){
                         if(mainLiteratureCheck.length == 0 && secondaryLiteratureCheck.length == 0){
                            this.mainLiterature.push({id: id, name: name});
                        }
                    } else {
                        if(mainLiteratureCheck.length == 0 && secondaryLiteratureCheck.length == 0){
                            this.secondaryLiterature.push({id: id, name: name});
                        }
                    }
                },
                removeLiterature: function(id, type){
                    if(type == 'main'){
                        this.mainLiterature.filter(function(elem, index){
                            if(elem.id == id){
                                newView.mainLiterature.splice(index, 1)
                            }
                        });
                    } else {
                        this.secondaryLiterature.filter(function(elem, index){
                            if(elem.id == id){
                                newView.secondaryLiterature.splice(index, 1)
                            }
                        });
                    }
                }
                                
                           
            }
        });
    </script>
@endsection