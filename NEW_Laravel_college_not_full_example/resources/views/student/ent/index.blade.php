@extends('layouts.app')

@section('title', __('UNT'))

@section('content')

    <section class="content" id="main">
        <div class="container-fluid" id="main-task-list">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('UNT')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="panel panel-default col-md-">
                        <div class="panel-body">
                            <div class="padding-20" v-if="open_folder == null">
                                <div>
                                    <h3>
                                        @lang('The video lessons are being updated daily')
                                    </h3>
                                </div>
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#kz" role="tab" aria-controls="nav-home" aria-selected="true">Казахский</a>
                                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#ru" role="tab" aria-controls="nav-profile" aria-selected="false">Русский</a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="kz" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <div class="padding-15 cursor-pointer" v-for="folder in folders.kz" v-on:click="openFolder(folder)">
                                            <i class="fas fa-folder text-warning"></i>
                                            <span>@{{ folder.name }}</span>
                                            <span v-if="folder.new_files_count > 0" class="badge badge-success">  +@{{ folder.new_files_count }}</span>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="ru" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <div class="padding-15 cursor-pointer" v-for="folder in folders.ru" v-on:click="openFolder(folder)">
                                            <i class="fas fa-folder text-warning"></i>
                                            <span>@{{ folder.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="padding-20" v-if="open_folder != null">
                                <div class="padding-5">
                                    <button class="btn btn-success" v-on:click="showFolders">@lang('Back')</button>
                                    <a :href="open_folder.forum_url" class="btn btn-secondary">@lang('Forum')</a>
                                </div>
                                <div class="container">
                                    <div class="col-md-offset-2 pb-4" v-for="video in open_folder.content">
                                        <iframe class="h-lg-500 w-100" :src="video.url" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
   @endsection

@section('scripts')
    <script>
        const app = new Vue({
            el: '#main',
            data: function() {
                return {
                    folders: {
                        kz: [],
                        ru: []
                    },
                    open_folder: null,
                    video_src: null
                }
            },
            methods: {
                openFolder: function(folder){
                    this.open_folder = {
                        content: folder.content,
                        folder_name: folder.name,
                        forum_url:`{{route('chatter.category.show', '')}}/${folder.orig_name}-ent`
                    }
                     $('video').prop('volume', 0.5)
                },
                getFolders: function(){
                    axios.post('{{route('ent.getFolders')}}')
                        .then( ({data}) => {
                            this.folders = data;
                        })
                },
                showFolders: function(){
                    this.open_folder = null
                    this.video_src = null
                    this.getFolders()
                }
            },
            created: function(){
                this.getFolders()
            }
        })

    </script>
@endsection
