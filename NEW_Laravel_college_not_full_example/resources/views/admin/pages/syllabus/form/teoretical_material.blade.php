<div class="form-group">
    <label class="col-md-3 control-label">Теоретический материал</label>
    <div class="col-md-9">
        <div class="col-md-12" id="teoretical_material_section" style="padding-left: 0px;">
        @if( !$syllabus->teoreticalMaterials || count($syllabus->teoreticalMaterials) == 0 )
        <div class="col-md-8 form-group">
            <input type="file" name="teoreticalMaterials[new][file][]" />
        </div>
        @else
            @foreach($syllabus->teoreticalMaterials as $doc)
                    @if($doc->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_FILE)
                        <div class="col-md-8 form-group">
                            <a href="{{ $doc->getPublicUrl() }}" target="_blank">{{ $doc->filename_original ?? '<без названия>' }}</a>
                            <input type="hidden" value="{{$doc->id}}" name="teoreticalMaterials[update][file][{{$doc->id}}]">
                        </div>
                        <div class="col-md-1" onclick="deleteTeoreticalMaterial(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                        @endif
                    @if($doc->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
                        <div class="col-md-8 form-group">
                            <a href="{{ $doc->getPublicUrl() }}" target="_blank">{{ $doc->link ?? '<без названия>' }}</a>
                            <p>{{ $doc->link_description }}</p>
                            <input class="form-control" type="hidden" name="teoreticalMaterials[update][link][{{ $doc->id }}]" value="{{ $doc->link ?? '' }}" placeholder="http://..." />
                            <input class="form-control" type="hidden" name="teoreticalMaterials[update][link_description][{{ $doc->id }}]" value="{{ $doc->link_description ?? '' }}" placeholder="описание" />
                        </div>
                        <div class="col-md-1" onclick="deleteTeoreticalMaterial(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                    @endif
            @endforeach

        @endif
        </div>
        <div class="col-md-12" style="padding-left: 0px;">
            <a class="btn btn-default" onclick="addTeoreticalMaterial()"> Добавить ссылку <i class="fa fa-plus"></i></a>
            <a class="btn btn-default" onclick="addTeoreticalMaterialFile()"> Добавить файл <i class="fa fa-plus"></i></a>
        </div>

        <div class="col-md-12" style="padding-left: 0px;">
            <label>Описание</label>
            <textarea class="form-control" name="teoretical_description">{{ $syllabus->teoretical_description ?? '' }}</textarea>
        </div>
    </div>

</div>


<script type="text/javascript">
    function addTeoreticalMaterial()
    {
        $('#teoretical_material_section').append('<div class="col-md-8 form-group">\n' +
            '<input class="form-control" type="text" name="teoreticalMaterials[new][link][]" placeholder="http://..." />\n' +
            '<input class="form-control" type="text" name="teoreticalMaterials[new][link_description][]" placeholder="описание" />\n' +
            '</div><div class="col-md-1" onclick="deleteTeoreticalMaterial(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
    }

    function addTeoreticalMaterialFile()
    {
        $('#teoretical_material_section').append('<div class="col-md-8 form-group">\n' +
            '<input type="file" name="teoreticalMaterials[new][file][]" />' +
            '</div><div class="col-md-1" onclick="deleteTeoreticalMaterial(this)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
    }

    function deleteTeoreticalMaterial(elem)
    {
        $(elem).prev().remove();
        $(elem).remove();
    }
</script>
