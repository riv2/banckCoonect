@extends("admin.admin_app")

@section('title', isset($user->name) ? __('Edit') .' '. $user->name : __('Add user'))

@section("content")

<div id="main">
	<div class="page-header">
		<h2> {{ isset($user->name) ? 'Редактировать: '. $user->name : 'Добавить пользователя' }}</h2>
		
		<a href="{{ URL::to('/users') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
	  
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
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('POSTUserAdd') ),'class'=>'form-horizontal padding-15','name'=>'user_form','id'=>'user_form','role'=>'form','enctype' => 'multipart/form-data')) !!} 
                <input type="hidden" name="id" value="{{ isset($user->id) ? $user->id : null }}">
                  
                
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Имя</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" value="{{ isset($user->name) ? $user->name : null }}" class="form-control">
                    </div>
                </div>
				<div class="form-group">
                    <label for="" class="col-sm-3 control-label">Телефон</label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" value="{{ isset($user->phone) ? $user->phone : null }}" class="form-control" value="">
                    </div>
                </div>
                
				<div class="form-group">
                    <label for="" class="col-sm-3 control-label">О пользователе</label>
                    <div class="col-sm-9">
                         
						<textarea name="about" cols="50" rows="5" class="form-control">{{ isset($user->about) ? $user->about : null }}</textarea>
                    </div>
                </div>
				<div class="form-group">
                    <label for="" class="col-sm-3 control-label">Facebook</label>
                    <div class="col-sm-9">
                        <input type="text" name="facebook" value="{{ isset($user->facebook) ? $user->facebook : null }}" class="form-control" value="">
                    </div>
                </div>
				<div class="form-group">
                    <label for="" class="col-sm-3 control-label">Instagram</label>
                    <div class="col-sm-9">
                        <input type="text" name="insta" value="{{ isset($user->insta) ? $user->insta : null }}" class="form-control" value="">
                    </div>
                </div>
				<div class="form-group">
                    <label for="avatar" class="col-sm-3 control-label">Фото профайла</label>
                    <div class="col-sm-9">
                        <div class="media">
                            <div class="media-left">
                                @if(isset($user->image_icon))
                                 
									<img src="{{ URL::asset('upload/members/'.$user->image_icon.'-s.jpg') }}" width="80" alt="person">
								@endif
								                                
                            </div>
                            <div class="media-body media-middle">
                                <input type="file" name="image_icon" class="filestyle"> 
                            </div>
                        </div>
	
                    </div>
                </div>
                
				<hr />
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-9">
                        <input type="text" name="email" value="{{ isset($user->email) ? $user->email : null }}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Пароль</label>
                    <div class="col-sm-9">
                        <input type="password" name="password" value="" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Роли</label>
                    <div class="col-sm-5">

                        @foreach($roleList as $item)
                        <div class="col-md-12">
                            <input type="checkbox"
                                   name="roles[{{$item->id}}]"
                                   @if($user->hasRole($item->name)) checked @endif
                                   @if($item->id <= 3) disabled @endif
                                    value="1"
                            >
                            @if($item->id <= 3 && $user->hasRole($item->name)) <input type="hidden" value="1" name="roles[{{$item->id}}]"> @endif
                            <span>{{ $item->title_ru }}</span>
                        </div>
                        @endforeach

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Доступ к силлабусам</label>
                    <div class="col-sm-9 no-padding">
                        <discipline-list v-bind:select-discipline-list="relatedDisciplineList"></discipline-list>
                    </div>
                </div>

                @if($user->hasRole('client') && \App\Services\Auth::user()->hasRight('promotions','edit')) 
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <a href="{{ route('adminDiscountRequestsAdd', [
                        'user_id' => $user->id,
                        'discount_type_id' => 18
                        ]) }}" class="btn btn-primary">Студентческая скидка</a>
                    </div>
                </div>
                @endif
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                    	<button type="submit" class="btn btn-primary">{{ isset($user->name) ? 'Сохранить' : 'Добавить пользователя' }}</button>
                    </div>
                </div>

                {!! Form::close() !!} 
            </div>
        </div>
</div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $("#userType").change(function(){
            var placesList = $('#placesList');

            if( $(this).find('select').val() == 'Master' ) placesList.show(150);
            else placesList.hide(150);
        });

        Vue.component('discipline-list', {
            props: [ 'selectDisciplineList' ],
            data: function(){
                return {
                    disciplineList: [],
                    relatedDisciplineList: this.selectDisciplineList,
                    selectedDiscipline: null,
                    searchDisciplinePanel: false,
                    searchDisciplineText: ''
                };
            },
            methods: {
                loadDisciplineList: function(text){
                    var self = this;
                    axios.post('{{route('disciplineAjaxList')}}', {
                        text: text
                    })
                        .then(function(response){
                            self.disciplineList = response.data;
                        });
                },
                removeDiscipline: function(key){
                    this.relatedDisciplineList.splice(key, 1);
                },
                searchDiscipline: function()
                {
                    this.loadDisciplineList(this.searchDisciplineText)
                },
                searchDisciplineClear: function(){
                    this.disciplineList = [];
                    this.searchDisciplinePanel = false;
                    this.searchDisciplineText = '';
                    this.selectedDiscipline = null;
                },
                inRelated: function(disciplineId) {
                    for(var i = 0; i < this.relatedDisciplineList.length; i++) {
                        if(this.relatedDisciplineList[i].id == disciplineId) {
                            return true;
                        }
                    }

                    return false;
                }
            },
            watch: {
                selectedDiscipline: function(val){
                    if(this.selectedDiscipline !== null) {
                        this.relatedDisciplineList.push(this.disciplineList[this.selectedDiscipline]);
                    }
                    this.searchDisciplineClear();
                }
            },
            created: function() {
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-12 alert" style="margin-bottom: 10px; background-color: rgb(238, 238, 238);"
                                 v-for="(discipline, key) in relatedDisciplineList">
                                    @{{ discipline.name + ' (' + discipline.credits }}<sub>ECTS</sub>)
                                <input type="hidden" name="disciplines[]" v-bind:value="discipline.id">
                                <span style="cursor: pointer" class="pull-right" v-on:click="removeDiscipline(key)"><i class="glyphicon glyphicon-remove"></i></span>
                            </div>
                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                <div class="col-md-12" v-if="!searchDisciplinePanel" style="padding-left: 0px; padding-right: 0px;">
                                    <a style="cursor: pointer" v-on:click="searchDisciplinePanel=true; ">Добавить</a>
                                </div>
                                <div class="col-md-12" v-if="searchDisciplinePanel" style="padding-left: 0px; padding-right: 0px;">
                                    <input type="text" class="form-control" v-on:keyup="searchDiscipline()" v-model="searchDisciplineText" />
                                    <span style="cursor: pointer;position: relative;top: -28px;left: -10px;" class="pull-right" v-on:click="searchDisciplineClear()"><i class="glyphicon glyphicon-remove"></i></span>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu" v-bind:style="{display: (disciplineList.length > 0 ? 'block' : 'none')}" style="overflow-y: auto; max-height: 150px;position: relative;">
                                        <li v-for="(discipline, key) in disciplineList" v-show="!inRelated(discipline.id)">
                                            <a style="cursor: pointer" v-on:click="selectedDiscipline = key">@{{ discipline.name + ' (' + discipline.ects }}<sub>ECTS</sub>)</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
            `
        });

        var app = new Vue({
            el: '#main',
            data: {
                relatedDisciplineList: []
            },
            created: function(){
                @foreach($user->teacherDisciplines as $discipline)
                    this.relatedDisciplineList.push({id: {{$discipline->id}}, name: '{{$discipline->name}}', credits: {{$discipline->ects}} });
                @endforeach
            }
        });

    </script>
@endsection