@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if($resume->requirements->isEmpty())
            <div class="margin-top alert alert-success" role="alert">
                <p>Для этой вакансии отсутствуют требования</p>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="margin-top alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }} <br>
                        </li>
                    @endforeach
                </ul> 
            </div>
        @endif
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Кандидат: {{ $resume->user->name }}</h2>
                    <h2>Вакансия: {{ $resume->vacancy->position->name }}</h2>
                    <h2>Резюме:</h2>
                </div>
                <div class="col-md-2 margin-t30">
                    @if($resume->status == 'pending')
                    	{!! Form::open([
    		                'url' => route('employees.verdict.candidate.resume')
    		            ]) !!}
    		            	<input type="hidden" name="resume_id" value="{{ $resume->id }}">
                            @if($type == 'edit')
                                <input type="hidden" name="verdict" value="approvedEdit">
                            @else
                                <input type="hidden" name="verdict" value="approved">
                            @endif
                            <button type="submit" class="btn btn-lg btn-success btn-block">
                                {{ $type == 'edit' ? 'Подтвердить' : 'На собеседование' }}
                            </button>
                    	{!! Form::close() !!}
                            @if($type != 'edit')
                                <button v-on:click="openModal" data-type="declined" class="btn btn-lg btn-danger btn-block margin-t10">
                                    Отклонить
                                </button>
                            @endif
                        @if($resume->requirements->isNotEmpty())
                            <button v-on:click="openModal" data-type="revision" class="btn btn-lg btn-primary btn-block margin-t10">
                                На доработку
                            </button>
                        @endif
                    @else
                        <a href="{{ URL::previous() }}" class="btn btn-primary btn-lg btn-block">Назад</a>
                    @endif
                </div>
            </div>
        </div>

        @foreach($requirements as $category => $requirement)
            @if($category == 'personal_info')
                <div class="row margin-t30">
                    <div class="col-md-12">
                        <h3 class="">Персональная информация</h3>
                        <div class="row">
                            @foreach($requirement as $value)
                                <div class="col-md-4 col-sm-12 margin-t30">
                                    <div class="card shadow">
                                        <div class="card-header text-center">
                                            <label>{{ $value['requirement']['name'] }}</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                            <p>{{ $value['content'] }}</p>
                                            </div>
                                            @if($value['requirement']['field_type'] == 'file')
                                                <a 
                                                    href="{{ route('download.requirement.file', ['name' => $value['content']]) }}" 
                                                    class="btn btn-primary"
                                                >
                                                    Скачать файл
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                @if(count($requirements[$category]) > 0)
                    <div class="row margin-t30">
                        <div class="col-md-12">
                            <h3 class="">{{ \App\EmployeesRequirement::$categories[$category] }}</h3>
                            @foreach($requirement as $value)
                                @foreach(is_array($value['json_content']) ? $value['json_content'] : json_decode($value['json_content'], true) as $key => $record)
                                    <div class="row">
                                        <h4 class="margin-l15 margin-t30">{{ $value['requirement']['name'] }}</h4>
                                        @foreach($record as $field => $content)
                                            <div class="col-md-4 col-sm-12 margin-t10">
                                                <div class="card shadow">
                                                    <div class="card-header text-center">
                                                        @foreach($value['requirement']['fields'] as $checkField)
                                                            @if($checkField['field_name'] == $field)
                                                                <label>{{ $checkField['name'] }}</label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group margin-0">
                                                            @if($content == null)
                                                                <p>Требование не заполнено</p>
                                                            @else
                                                                <p>{{ $content }}</p>
                                                                @foreach($value['requirement']['fields'] as $checkField)
                                                                    @if($checkField['field_name'] == $field && $checkField['field_type'] == 'file')
                                                                        <a 
                                                                            href="{{ route('download.requirement.file', ['name' => $content]) }}" 
                                                                            class="btn btn-primary"
                                                                        >
                                                                            Скачать файл
                                                                        </a>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
   
        <!-- Modal -->
        <div class="modal fade" id="revisionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open([
                        'url' => route('employees.verdict.candidate.resume'),
                        'id' => 'revisionForm'
                    ]) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Вердикт</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="resume_id" value="{{ $resume->id }}">
                            <input type="hidden" id="revisionModalVerdict" name="verdict" value="">
                            <div class="form-group">
                                <label>Причина:</label>
                                <textarea class="form-control" name="reason" placeholder="Опишите причину" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Подтвердить</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    var main = new Vue({
        el: '#main',
        methods: {
            openModal: function (event) {
                var type = event.currentTarget.getAttribute('data-type');
                $('#revisionModal #revisionModalVerdict').val(type);
                $('#revisionModal').modal('show');
            }
        }
    });
</script>
@endsection