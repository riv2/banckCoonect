<form :id="'uploadFileForm'+template.id">
    <input type="hidden" name="template_id" :value="template.id">
    @{{ template.isset_file }}
    <div v-if="template.isset_files" class="form-group">
        <label>Файлы загружены:</label>
        <div v-for="file in template.files" class="margin-t10">
            <div class="row">
                <span class="col-md-12">
                    <a class="col-md-11 btn btn-default overflow-hidden" :href="downloadRoute + '/' + file.name" >@{{ file.name }}</a>
                    <a class="col-md-1 btn btn-default" v-on:click="confirmDeleteFile(file.name, folder.id)"><i style="margin-left: -4px;" class="fa fa-trash text-danger"></i></a>
                </span>
            </div>
            <div v-if="votes.includes(file.id)" class="text-center">
                <input v-if="file.checked" class="form-check-input" type="checkbox" checked="true" disabled>
                <input v-else class="form-check-input" type="checkbox" @change="vote(file.id)">
                <label class="form-check-label">
                    Проверено согласователем
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Подгрузка файлов:</label>
        <input type="file" class="form-control uploadFiles" :class="template.status == 'просрочен срок загрузки' ? 'btn-danger' : ''" name="files[]" multiple>
    </div>
</form>