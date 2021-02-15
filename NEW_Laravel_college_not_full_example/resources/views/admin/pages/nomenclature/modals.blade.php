<!-- Add File Modal -->
<div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open([
                'url' => route('nomenclature.add.file.to.folder'),
                'enctype' => 'multipart/form-data'
            ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Создание папки</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="folder_id" id="add_file_folder_id">
                <div class="form-group">
                    <label for="">Наименование</label>
                    <input type="text" class="form-control" name="name" placeholder="введите наименование">
                </div>
                <div class="form-group">
                    <label for="">Срок загрузки</label>
                    <input type="date" class="form-control" name="load_date">
                </div>
                <div class="form-group">
                    <label for="">Шаблон</label>
                    <input type="file" class="form-control" name="template">
                </div>
                <div class="form-group nomenclature_votes_list">
                    <label for="">Согласователи</label>
                    <select name="votes_list[]" class="selectpicker" multiple data-live-search="true">
                        <option v-for="position in positions" :value="position.id">@{{ position.name }}</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!-- Add Folder Modal -->
<div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open([
                'url' => route('add.folder.to.nomenclature'),
            ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Создание папки</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="parent_id" id="parent_id_input">
                <input type="hidden" name="years" id="add_folder_years">
                <div class="form-group">
                    <label for="">Имя папки</label>
                    <input type="text" class="form-control" name="name" placeholder="введите имя для папки">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!-- Edit Name Modal -->
<div class="modal fade" id="editNameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="newNameForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Редактирование имени</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="nameId" name="nameId">
                    <input type="hidden" id="nameType" name="nameType">
                    <div class="form-group">
                        <label for="">Новое имя</label>
                        <input type="text" class="form-control" id="newName" name="name" placeholder="введите новое имя">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" @click="editName">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>