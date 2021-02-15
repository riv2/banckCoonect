<form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
    {{ csrf_field() }}

    @if($hasDocument)
        <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
            <label for="iin" class="col-md-4 control-label">{{__('Speciality')}}</label>

            <div class="col-md-6">

                <select class="selectpicker" name="discipline_id" data-live-search="true" data-size="5"
                        title="{{ __('Please select') }}" required autofocus>
                    @foreach($disciplineList as $discipline)
                        <option value="{{$discipline->id}}" @if($course->discipline_id == $discipline->id) selected @endif>{{$discipline['name']}}</option>
                    @endforeach
                </select>

                @if ($errors->has('title'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                @endif
            </div>
        </div>
    @else
    <!-- COURSE TITLE -->
        <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
            <label for="iin" class="col-md-4 control-label">{{__('Title')}}</label>

            <div class="col-md-6">
                <input id="title" type="text" class="form-control" name="title" value="{{ isset($course->title) ? $course->title : '' }}" required autofocus>

                @if ($errors->has('title'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                @endif
            </div>
        </div>
    @endif
<!-- COURSE IMAGE -->
    <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
        <label for="image" class="col-md-4 control-label">{{__('Image')}}</label>

        <div class="col-md-6">
            @if($course->photo_file_name && file_exists( public_path('/images/uploads/courses/' . $course->photo_file_name)))
                <img src="/images/uploads/courses/{{ $course->photo_file_name }}" target="_blank" style="max-height: 200px;"/>
            @endif
            <input id="image" type="file" class="form-control" name="photo" autofocus>

            @if ($errors->has('image'))
                <span class="help-block">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
            @endif
        </div>
    </div>

    <!-- COURSE DESCRIPTION -->
    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
        <label for="description" class="col-md-4 control-label">{{__('Description')}}</label>

        <div class="col-md-6">
            <textarea id="description" class="form-control" name="description" required autofocus>{{ isset($course->description) ? $course->description : '' }}</textarea>

            @if ($errors->has('description'))
                <span class="help-block">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
            @endif
        </div>
    </div>

    <!-- LANGUAGE -->
    <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
        <label for="language" class="col-md-4 control-label">{{__('Language')}}</label>

        <div class="col-md-6">
            <input id="language" type="text" class="form-control" name="language" value="{{ isset($course->language) ? $course->language : '' }}" required autofocus>

            @if ($errors->has('language'))
                <span class="help-block">
                                    <strong>{{ $errors->first('language') }}</strong>
                                </span>
            @endif
        </div>
    </div>

    <!-- CERTIFICATE FILE -->
    <div class="form-group{{ $errors->has('certificate_file') ? ' has-error' : '' }}">
        <label for="certificate_file" class="col-md-4 control-label">{{__('Certificate')}}</label>

        <div class="col-md-6">
            @if($course->certificate_file_name)
                <a href="/images/uploads/certificates/{{ $course->certificate_file_name }}" target="_blank">Посмотреть сертификат</a>
            @endif
            <input id="certificate_file" type="file" class="form-control" name="certificate_file" autofocus>

            @if ($errors->has('certificate_file'))
                <span class="help-block">
                                    <strong>{{ $errors->first('certificate_file') }}</strong>
                                </span>
            @endif
        </div>
    </div>

    <!-- TAGS -->
    <div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
        <label for="tags" class="col-md-4 control-label">{{__('Tags (space delimiter)')}}</label>

        <div class="col-md-6">
            <input id="tags" type="text" class="form-control" name="tags" value="{{ isset($course->tags) ? $course->tags : '' }}" required autofocus>

            @if ($errors->has('tags'))
                <span class="help-block">
                                    <strong>{{ $errors->first('tags') }}</strong>
                                </span>
            @endif
        </div>
    </div>

    @if(!$hasDocument)
    <!-- Video link -->
        <div class="form-group{{ $errors->has('video_link') ? ' has-error' : '' }}">
            <label for="video_link" class="col-md-4 control-label">{{__('Video (link)')}}</label>

            <div class="col-md-6">
                <input id="video_link" type="text" class="form-control" name="video_link" value="{{ isset($course->video_link) ? $course->video_link : '' }}" required autofocus>

                @if ($errors->has('video_link'))
                    <span class="help-block">
                                    <strong>{{ $errors->first('video_link') }}</strong>
                                </span>
                @endif
            </div>
        </div>
    @endif

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{__("Save")}}
            </button>
        </div>
    </div>
</form>