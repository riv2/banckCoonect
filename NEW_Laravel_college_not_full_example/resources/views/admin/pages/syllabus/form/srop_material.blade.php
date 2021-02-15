<div class="form-group">
    <label class="col-md-3 control-label">Самостоятельная работа обучающегося с преподавателем</label>
    <div class="col-md-9">
        <div class="col-md-12" id="srop_material_section_{{ $lang }}" style="padding-left: 0px;">
            @if( $syllabus->sropMaterials && count($syllabus->sropMaterials) > 0 )
                @foreach($syllabus->sropMaterials as $doc)
                    @if($doc->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                        <div class="col-md-8 form-group">
                            <a href="{{ $doc->getPublicUrl() }}" target="_blank">{{ $doc->filename_original ?? '<без названия>' }}</a>
                            <input required type="hidden" value="{{$doc->id}}" name="sropMaterials[update][file][{{$doc->id}}]">
                        </div>
                        <div class="col-md-1" onclick="deleteSropMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                    @endif
                    @if($doc->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                        <div class="col-md-8 form-group">
                            <a href="{{ $doc->getPublicUrl() }}" target="_blank">{{ $doc->link ?? '<без названия>' }}</a>
                            <p>{{ $doc->link_description }}</p>
                            <input required class="form-control" type="hidden" name="sropMaterials[update][link][{{ $doc->id }}]" value="{{ $doc->link ?? '' }}" placeholder="http://..." />
                            <input required class="form-control" type="hidden" name="sropMaterials[update][link_description][{{ $doc->id }}]" value="{{ $doc->link_description ?? '' }}" placeholder="описание" />
                        </div>
                        <div class="col-md-1" onclick="deleteSropMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                    @endif
                @endforeach
            @endif
        </div>
        <div class="col-md-12" style="padding-left: 0px;">
            <a class="btn btn-default" onclick="addSropMaterial{{ $lang }}()"> Добавить ссылку <i class="fa fa-plus"></i></a>
            <a class="btn btn-default" onclick="addSropMaterialFile{{ $lang }}()"> Добавить файл <i class="fa fa-plus"></i></a>
        </div>

        <div class="col-md-12" style="padding-left: 0px;">
            <label>Описание</label>
            <textarea class="form-control" name="srop_description">{{ $syllabus->srop_description ?? '' }}</textarea>
        </div>
    </div>

</div>


<script type="text/javascript">
    function addSropMaterial{{ $lang }}()
    {
        $('#srop_material_section_{{ $lang }}').append('<div class="col-md-8 form-group">\n' +
            '<input required class="form-control" type="text" name="sropMaterials[new][link][]" placeholder="http://..." />\n' +
            '<input required class="form-control" type="text" name="sropMaterials[new][link_description][]" placeholder="описание" />\n' +
            '</div><div class="col-md-1" onclick="deleteSropMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
    }

    function addSropMaterialFile{{ $lang }}()
    {
        $('#srop_material_section_{{ $lang }}').append('<div class="col-md-8 form-group">\n' +
            '<input required type="file" name="sropMaterials[new][file][]" />' +
            '</div><div class="col-md-1" onclick="deleteSropMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
    }

    function deleteSropMaterial{{ $lang }}(elem)
    {
        $(elem).prev().remove();
        $(elem).remove();
    }
</script>
