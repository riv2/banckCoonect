@if( count($help->user->courses) > 0 )
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        @foreach($help->user->courses as $course)
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-course-{{ $course->id }}" aria-expanded="true" aria-controls="collapseOne">
                        {{ $course->title }}
                    </a>
                </h4>
            </div>
            <div id="collapse-course-{{ $course->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <div class="col-md-12"></div>
                        <div class="col-md-2">
                            <img src="/images/uploads/courses/{{ $course->photo_file_name }}" style="width: 100%">
                        </div>
                        <div class="col-md-10">
                            <div class="col-md-12">
                                {{ $course->description }}
                            </div>
                            <div class="col-md-12 no-padding">
                                <div class="col-md-3">
                                    <label>Язык:</label>
                                </div>
                                <div class="col-md-9">{{ $course->language }}</div>
                            </div>

                            <div class="col-md-12 no-padding">
                                <div class="col-md-3">
                                    <label>Ссылка на видео:</label>
                                </div>
                                <div class="col-md-9">{{ $course->video_link }}</div>
                            </div>

                            <div class="col-md-12 no-padding">
                                <div class="col-md-3">
                                    <label>Теги:</label>
                                </div>
                                <div class="col-md-9">{{ $course->tags }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>
@endif