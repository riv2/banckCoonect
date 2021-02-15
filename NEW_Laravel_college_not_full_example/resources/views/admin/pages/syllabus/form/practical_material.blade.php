<div class="form-group">
    <label class="col-md-3 control-label">Практический материал</label>
    <div class="col-md-9">
        <div class="col-md-12" id="practical_material_section_{{ $lang }}" style="padding-left: 0px;">
            @if( $syllabus->practicalMaterials && count($syllabus->practicalMaterials) > 0 )
                @foreach($syllabus->practicalMaterials as $doc)
                        @if($doc->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                            <div class="col-md-8 form-group">
                                <a href="{{ $doc->getPublicUrl() }}" target="_blank">{{ $doc->filename_original ?? '<без названия>' }}</a>
                                <input type="hidden" value="{{$doc->id}}" name="practicalMaterials[update][file][{{$doc->id}}]">
                            </div>
                            <div class="col-md-1" onclick="deletePracticalMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                        @endif
                        @if($doc->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                            <div class="col-md-8 form-group">
                                <a href="{{ $doc->getPublicUrl() }}" target="_blank">{{ $doc->link ?? '<без названия>' }}</a>
                                <p>{{ $doc->link_description }}</p>
                                <input class="form-control" type="hidden" name="practicalMaterials[update][link][{{ $doc->id }}]" value="{{ $doc->link ?? '' }}" placeholder="http://..." />
                                <input class="form-control" type="hidden" name="practicalMaterials[update][link_description][{{ $doc->id }}]" value="{{ $doc->link_description ?? '' }}" placeholder="описание" />
                            </div>
                            <div class="col-md-1" onclick="deletePracticalMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                        @endif
                @endforeach
            @endif
        </div>
        <div class="col-md-12" style="padding-left: 0px;">
            <a class="btn btn-default" onclick="addPracticalMaterial{{ $lang }}()"> Добавить ссылку <i class="fa fa-plus"></i></a>
            <a class="btn btn-default" onclick="addPracticalMaterialFile{{ $lang }}()"> Добавить файл <i class="fa fa-plus"></i></a>
        </div>

        <div class="col-md-12" style="padding-left: 0px;">
            <label>Описание</label>
            <textarea class="form-control" name="practical_description">{{ $syllabus->practical_description ?? '' }}</textarea>
        </div>

    </div>

</div>


<script type="text/javascript">
    function addPracticalMaterial{{ $lang }}()
    {
        $('#practical_material_section_{{ $lang }}').append('<div class="col-md-8 form-group">\n' +
            '<input class="form-control" type="text" name="practicalMaterials[new][link][]" placeholder="http://..." />\n' +
            '<input class="form-control" type="text" name="practicalMaterials[new][link_description][]" placeholder="описание" />\n' +
            '</div><div class="col-md-1" onclick="deletePracticalMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
    }

    function addPracticalMaterialFile{{ $lang }}()
    {
        $('#practical_material_section_{{ $lang }}').append('<div class="col-md-8 form-group">\n' +
            '<input type="file" name="practicalMaterials[new][file][]" />' +
            '</div><div class="col-md-1" onclick="deletePracticalMaterial{{ $lang }}(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
    }

    function deletePracticalMaterial{{ $lang }}(elem)
    {
        $(elem).prev().remove();
        $(elem).remove();
    }
</script>
