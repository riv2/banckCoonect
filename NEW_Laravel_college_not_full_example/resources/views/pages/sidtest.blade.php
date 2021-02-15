@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Страница проверки сканирования документа</div>

                <div class="panel-body">
                    <blockquote>Для проверки заполните форму</blockquote>
                    
                    {!! Form::open(array('url' => array( route('sidtestPost') ),'role'=>'form','enctype' => 'multipart/form-data')) !!} 


                    <div class="form-group col-md-12"> 
                        <label for="type" class="col-sm-4 control-label">Document type</label>
                        <div class="col-sm-7">
                            <select name="type" id="type" class="grey form-control">
                              <option value="auto">Auto</option>
                              <option value="type1">Type 1</option>
                              <option value="type2">Type 2</option>
                            </select>
                        </div>
                        
                    </div>

                    <div class="form-group col-md-12">
                        <label for="front" class="col-md-4 control-label">Front</label>
                        <div class="col-md-7">
                            <input id="front" type="file" class="form-control" name="front">
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="back" class="col-md-4 control-label">Back</label>
                        <div class="col-md-7">
                            <input id="back" type="file" class="form-control" name="back">
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <button id="usedSubmit" type="submit" class="button btn-info">Проверить</button> 
                    </div>

                    

                    {!! Form::close() !!} 

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
