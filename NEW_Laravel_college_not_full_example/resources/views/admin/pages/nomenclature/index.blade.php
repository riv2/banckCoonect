@extends("admin.admin_app")

@section("content")
    <div id="main" class="library">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Номенклатура:</h2>
                </div>
                <div class="col-md-2">
                    <div class="form-group margin-t20">
                        <select class="form-control" name="years" @change="selectYears($event)">
                            <option v-if="currentYear == ''"></option>
                            <option v-for="year in years" :value="year" :selected="year == currentYear ? true : false">@{{ year }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="btn btn-default margin-t15" @click="newFolderModal(0)">
                    <i class="fa fa-plus-circle"></i>
                    <span>Добавить папку в номенклатуру</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <ul id="tree2">
                    {!! $folders !!}
                </ul>
            </div>
            <div class="col-md-9" v-if="folder.id">
                @include('admin.pages.nomenclature.folder_content')
            </div>
        </div>

        @include('admin.pages.nomenclature.modals')
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var main = new Vue({
                el: '#main',
                data: {
                    folder: [],
                    positions: {!! json_encode($positions) !!},
                    folderTemplates: [],
                    downloadRoute: '{{ route('nomenclature.download.file') }}',
                    auditUser: {{ $auditUser }},
                    votes: [],
                    years: {!! json_encode(App\NomenclatureFolder::$years) !!},
                    currentYear: '{!! $years !!}',
                    deleteFolderUrl: '{!! route('nomenclature.delete.folder') !!}',
                    deleteTemplateUrl: '{!! route('nomenclature.delete.template') !!}',
                },
                methods: {
                    newFolderModal: function (id) {
                        $('#addFolderModal').modal('show');
                        $('#parent_id_input').val(id);
                        $('#add_folder_years').val(this.currentYear);
                    },
                    showFolder: function(id){
                        const data = {};
                        data['id'] = id;
                        data['years'] = this.currentYear;
                        axios.post('{{ route('nomenclature.get.folder.content') }}', data)
                            .then(response => {
                                this.folder = response.data.folder;
                                this.folderTemplates = response.data.templates;
                                this.votes = response.data.votes;
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                    },
                    newFileModal: function(id) {
                        $('#addFileModal').modal('show');
                        $('#add_file_folder_id').val(id);
                    },
                    saveFile: function(formID){
                        let data = new FormData(document.forms[formID]);
                        axios.post('{{ route('nomenclature.upload.file') }}', data)
                            .then(response => {
                                this.showFolder(response.data.folder_id);
                                var files = document.getElementsByClassName("uploadFiles");
                                for (var i = 0; i < files.length; i++) {
                                    files[i].value = "";
                                }
                            });
                    },
                    auditCheck: function(id){
                        const data = {};
                        data['id'] = id;
                        axios.post('{{ route('nomenclature.auditor.check') }}', data)
                            .then(response => {
                                this.showFolder(response.data.folder_id);
                            });
                    },
                    vote: function(id){
                        const data = {};
                        data['id'] = id;
                        axios.post('{{ route('nomenclature.user.vote') }}', data)
                            .then(response => {
                                this.showFolder(response.data.folder_id);
                            });
                    },
                    selectYears: function(event){
                        let url = '{{ route('nomenclature.page') }}';
                        url += '/'+event.target.value;
                        window.location.href = url;
                    },
                    openEditNameModal: function(id, type){
                        $('#editNameModal').modal('show');
                        $('#editNameModal #nameId').val(id);
                        $('#editNameModal #nameType').val(type);
                        $('#editNameModal #newName').val('');
                    },
                    editName: function(){
                        let data = new FormData(document.forms.newNameForm);
                        axios.post('{{ route('nomenclature.edit.name') }}', data)
                            .then(response => {
                                $('#editNameModal').modal('hide');
                                this.showFolder(response.data.folder_id);
                            });
                    },
                    confirmDeleteFile: function (file, folder_id) {
                        Swal.fire({
                            title: 'Вы уверены?',
                            text: "Этот файл нельзя будет восстановить!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Да, удалить!'
                        }).then((result) => {
                            if (result.value) {
                                axios.get('{{ route('nomenclature.delete.file') }}' + '/' + file)
                                    .then(response => {
                                        this.showFolder(folder_id);

                                    }
                                );
                            }
                        })
                    },
                    confirmChangeTemplateFile: function(formID, folderId){
                        let data = new FormData(document.forms[formID]);
                        axios.post('{{ route('nomenclature.upload.template.file') }}', data)
                            .then(response => {
                                this.showFolder(folderId);
                                var files = document.getElementsByClassName("uploadFiles");
                                for (var i = 0; i < files.length; i++) {
                                    files[i].value = "";
                                }
                                if (response.status === 200) {
                                    Swal.fire('Шаблон успешно заменен');
                                }
                            });
                    }
                }
            });

            $.fn.extend({
                treed: function (o) {

                    var openedClass = 'glyphicon-minus-sign';
                    var closedClass = 'glyphicon-plus-sign';

                    if (typeof o != 'undefined'){
                        if (typeof o.openedClass != 'undefined'){
                            openedClass = o.openedClass;
                        }
                        if (typeof o.closedClass != 'undefined'){
                            closedClass = o.closedClass;
                        }
                    };

                    //initialize each of the top levels
                    var tree = $(this);
                    tree.addClass("tree");
                    tree.find('li').has("ul").each(function () {
                        var branch = $(this); //li with children ul
                        branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
                        branch.addClass('branch');
                        branch.on('click', function (e) {
                            if (this == e.target) {
                                var icon = $(this).children('i:first');
                                icon.toggleClass(openedClass + " " + closedClass);
                                $(this).children().children().toggle();
                            }
                        })
                        branch.children().children().toggle();
                    });
                    //fire event from the dynamically added icon
                    tree.find('.branch .indicator').each(function(){
                        $(this).on('click', function () {
                            $(this).closest('li').click();
                        });
                    });
                    //fire event to open branch if the li contains an anchor instead of text
                    tree.find('.branch>a').each(function () {
                        $(this).on('click', function (e) {
                            $(this).closest('li').click();
                            e.preventDefault();
                        });
                    });
                    //fire event to open branch if the li contains a button instead of text
                    tree.find('.branch>button').each(function () {
                        $(this).on('click', function (e) {
                            $(this).closest('li').click();
                            e.preventDefault();
                        });
                    });
                }
            });

            $('#tree2').treed({openedClass:'glyphicon-folder-open', closedClass:'glyphicon-folder-close'});

            $('.selectpicker').selectpicker({
                dropupAuto: false
            });
        });
    </script>
@endsection