@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__("Document Verification Page")}}</div>

                <div class="panel-body">
                    <blockquote>{{__("To check the document enter its name in the fields below")}}</blockquote>
                    
                    {!! Form::open(array('url' => array( route('transcriptCheckPOST') ),'class'=>'form-horizontal padding-15','method'=>'POST')) !!} 

                    <div class="form-group row"> 
                        <label for="email" class="col-sm-2 control-label">{{__("Name")}}</label>
                        <div class="col-sm-5">
                            {{Form::text('docname', '', ['class' => 'grey form-control', 'id' => 'docname'])}}
                        </div>
                        <div class="col-sm-1">.pdf</div>
                        <div class="col-sm-1">
                        <button id="usedSubmit" type="submit" class="button btn-info">{{__("Check")}}</button> 
                    </div>
                    </div>

                    

                    {!! Form::close() !!} 

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
