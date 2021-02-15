<div class="row" xmlns:v-on="http://www.w3.org/1999/xhtml">
    <div class="col-md-6">
        <h4><span class="folder-name">Папка: @{{ folder.name }}</span>
            @if(Auth::user()->hasRole('auditor'))
                <button
                        class="btn btn-default"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="Редактировать"
                        @click="openEditNameModal(folder.id, 'folder')"
                >
                    <i class="fa fa-edit"></i>
                </button>
                <a
                    :href="deleteFolderUrl+'/'+folder.id"
                    class="btn btn-default"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Удалить папку"
                >
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        </h4>
    </div>
    <div class="col-md-6 text-right">
        <span @click="newFolderModal(folder.id)" class="text-monospace btn">
            <i class="fa fa-plus-circle btn-default"></i> Добавить папку
        </span>
        <span @click="newFileModal(folder.id)" class="text-monospace btn">
            <i class="fa fa-plus-circle btn-default"></i> Добавить файл
        </span>
    </div>
</div>
<div class="row">
    <div class="col-md-6">

    </div>
    <div class="col-md-6 text-right">
        <div v-if="folder.status == 'согласовано' && auditUser == 1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" @change="auditCheck(folder.id)">
                <label class="form-check-label" for="defaultCheck1">
                    Проверено аудитором
                </label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div v-if="folderTemplates.length > 0" class="row">
            <div v-for="template in folderTemplates" class="col-md-6">
                <h4>
                    Статус: @{{ template.status }}
                    @if(Auth::user()->hasRole('auditor'))
                        <button
                                class="btn btn-default"
                                data-toggle="tooltip"
                                data-placement="top"
                                title="Редактировать"
                                @click="openEditNameModal(template.id, 'template')"
                        >
                            <i class="fa fa-edit"></i>
                        </button>
                        <a
                                :href="deleteTemplateUrl+'/'+template.id"
                                class="btn btn-default"
                                data-toggle="tooltip"
                                data-placement="top"
                                title="Удалить файл"
                        >
                            <i class="fa fa-trash"></i>
                        </a>
                    @endif
                </h4>
                <div class="card">
                    <div class="card-header text-center">
                        @{{ template.name }}
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Срок загрузки:</label>
                            <input type="text" class="form-control" disabled :value="template.load_date">
                        </div>
                        <div class="form-group">
                            <label>Шаблон:</label>
                            <div v-if="template.template != null">
                                <a :href="downloadRoute+'/'+template.template" class="btn btn-default btn-block">Скачать шаблон</a>
                            </div>
                            <div v-else>
                                <input type="text" class="form-control" disabled value="Шаблон отсутствует">
                            </div>
                            <hr>
                            <p class="bold">Заменить шаблон</p>
                            <form :id="'uploadNewTemplateFile'+template.id">
                                <div class="col-xs-6">
                                    <input type="file" name="new_template_file" class="uploadFiles">
                                    <input type="hidden" name="template_id" :value="template.id">
                                </div>
                            </form>
                            <button class="btn btn-success pull-right" @click="confirmChangeTemplateFile('uploadNewTemplateFile'+template.id, folder.id)">Заменить</button>
                            <br><br>
                        </div>
                        @include('admin.pages.nomenclature.template_files')
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-success" @click="saveFile('uploadFileForm'+template.id)">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
