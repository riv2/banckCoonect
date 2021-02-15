@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-container">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('mg. Application page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <form method="POST" enctype="multipart/form-data" id="application-form">

                                {{ csrf_field() }}

                                @yield('part')

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-info">{{ __('Continue') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script type="text/javascript">

        Vue.component('student-education-block', {
            props: {
                name: '',
                slotClass: '',
                active: false,
                image: false
            },
            data: function(){
                return { show: this.active || false };
            },
            methods: {},
            watch: {
                show: function(val){
                    this.$root.$emit('showEducation', val);
                }
            },
            template: `
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div class="switch-btn" v-bind:class="{'switch-on-color': show}" v-on:click="show = !show"></div>
                    </div>
                    <slot v-if="show"></slot>
                    <input type="hidden" v-bind:value="show" v-bind:name="'has_' + name" />
                </div>
            `
        });

        Vue.component('student-document-option', {
            props: {
                name: '',
                slotClass: '',
                active: false
            },
            data: function () {
                return {show: this.active || false};
            },
            methods: {},
            template: `
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div class="switch-btn" v-bind:class="{'switch-on-color': show}" v-on:click="show = !show"></div>
                    </div>
                    <slot v-if="show"></slot>
                    <input type="hidden" v-bind:value="show" v-bind:name="'has_' + name" />
                </div>
            `
        });

        Vue.component('student-document-option-nostrification', {
            data: function () {
                return {show: true};
            },
            template: `
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div class="switch-btn" v-bind:class="{'switch-on-color': show}" v-on:click="show = !show"></div>
                        <select v-model="show" name="kzornot" style="display:none;">
                            <option v-bind:value="true">{{ __('Yes') }}</option>
                            <option v-bind:value="false">{{ __('No') }}</option>
                        </select>
                    </div>
                    <slot v-if="!show"></slot>
                </div>
            `
        });

        var app = new Vue({
            el: "#main-container",
            data: {
                educationLevel: '',
                publicationList: [
                    {
                        id: 1
                    }
                ],
                publicationIdInc: 1,
                activePage: 'address',
                application: {},
                isTransfer: false,
                showEducation: true
            },
            methods: {
                deletePublication: function (index) {
                    delete this.publicationList.splice(index,1);
                },
                addPublication: function () {
                    this.publicationList.push({
                        id: ++this.publicationIdInc
                    });
                },
                getExtension: function (filename) {
                    var parts = filename.split('.');
                    return parts[parts.length - 1];
                },
                isImage: function (filename) {
                    var ext = this.getExtension(filename);
                    switch (ext.toLowerCase()) {
                        case 'jpg':
                        case 'png':
                        case 'gif':
                        case 'webp':
                            return true;
                    }
                    return false;
                },
                checkImageValid: function(elemId){
                    /*for debug file type*/
                    return true;

                    var file = $('#' + elemId);

                    if(!file.val())
                    {
                        return false;
                    }

                    if(!this.isImage(file.val())) {
                        alert('{{ __('Image file has invalid format. Need jpg, png,gif or webp') }}');
                        file.val('');
                        return false;
                    }

                    return true;
                }
            },
            created: function(){
                this.$root.$on('showEducation', function(val){
                    this.showEducation = val;
                });
            }
        });

        
        $('.regions .selectpicker').on('change', function(){
            var regionID = this.value;
            var citiesCount = 0;
            $('.cities select').find('option').each(function(){
                var cityRegion = $(this).attr('region');
                if(cityRegion != regionID) {
                    $(this).hide();
                } else {
                    $(this).show();
                    citiesCount++;
                }
                
            });
            if(citiesCount == 1 ) {
                var val = $(".cities select").find('option[region="'+regionID+'"]').val();
            }
            
            $(".cities .selectpicker").val(val).selectpicker("refresh");
        });

        function readURL(input, imgId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#' + imgId).attr('src', e.target.result);
                    $('#' + imgId).css('display', 'block');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        window.onload = function () {

            $('#diploma_photo').change(function () {
                readURL(this, 'photo-diploma');
            });

            $('#atteducation').change(function () {
                readURL(this, 'photo-atteducation');
            });

            $('#atteducation_back').change(function () {
                readURL(this, 'photo-atteducation-back');
            });

            $('#nostrificationattach').change(function () {
                readURL(this, 'photo-nostrificationattach');
            });

            $('#nostrificationattach_back').change(function () {
                readURL(this, 'photo-nostrificationattach-back');
            });

            $('#con_confirm').change(function () {
                readURL(this, 'photo-con-confirm');
            });
        };

    </script>
@endsection