@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-container">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('bc. Application page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <form autocomplete="off" class="form-horizontal" id="application-form" method="POST" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                @yield('part')

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button v-bind:disabled="entActive && activePage == 'ent'" type="submit" class="btn btn-info">{{ __('Continue') }}</button>
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
                active: false,
                image: false
            },
            data: function () {
                return {show: this.active || false};
            },
            methods: {},
            watch: {
                show: function(val){
                    this.$root.$emit('changeEnt', val);
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

        Vue.component('student-document-option-nostrification', {
            props: {
                active: true
            },
            data: function () {
                return {show: this.active};
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
                educationLevel: '{{$bcApplication->bceducation ?? 'high_school'}}',
                activePage: '{{ $part }}',
                application: {},
                pageList: [
                    'address',
                    'military',
                    'r086',
                    'r063',
                    'ent',
                    'education'
                ],
                entActive: true,
                isTransfer: false,
                showEducation: true
            },
            methods: {
                nextPage: function(){
                    for(var i = 0; i < this.pageList.length; i++ ) {
                        if(this.activePage == this.pageList[i]) {
                            if(i == (this.pageList.length - 1)) {
                                return false;
                            }
                            return this.pageList[i + 1];
                        }
                    }
                },
                goNext: function() {
                    var form = $("#application-form");
                    if(form[0].checkValidity()) {
                        var nextPage = this.nextPage();

                        if(nextPage) {
                            this.activePage = nextPage;
                        } else {
                            form.submit();
                        }

                    } else {
                        form.find(':submit').click();
                    }
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
                this.$root.$on('changeEnt', function(val){
                    this.entActive = val;
                });
                this.$root.$on('showEducation', function(val){
                    this.showEducation = val;
                });
            }
        });
        /*
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
        */

        @if($part == 'address')
            var cities = [
                @foreach($cities as $city)
                    '{{$city->name}}',
                @endforeach
            ];
            autocomplete(document.getElementById("city"), cities);
        @endif
        

        function autocomplete(inp, arr) {
          /*the autocomplete function takes two arguments,
          the text field element and an array of possible autocompleted values:*/
          var currentFocus;
          /*execute a function when someone writes in the text field:*/
          inp.addEventListener("input", function(e) {
              var a, b, i, val = this.value;
              /*close any already open lists of autocompleted values*/
              closeAllLists();
              if (!val) { return false;}
              currentFocus = -1;
              /*create a DIV element that will contain the items (values):*/
              a = document.createElement("DIV");
              a.setAttribute("id", this.id + "autocomplete-list");
              a.setAttribute("class", "autocomplete-items");
              /*append the DIV element as a child of the autocomplete container:*/
              this.parentNode.appendChild(a);
              /*for each item in the array...*/
              for (i = 0; i < arr.length; i++) {
                /*check if the item starts with the same letters as the text field value:*/
                if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                  /*create a DIV element for each matching element:*/
                  b = document.createElement("DIV");
                  /*make the matching letters bold:*/
                  b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                  b.innerHTML += arr[i].substr(val.length);
                  /*insert a input field that will hold the current array item's value:*/
                  b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                  /*execute a function when someone clicks on the item value (DIV element):*/
                      b.addEventListener("click", function(e) {
                      /*insert the value for the autocomplete text field:*/
                      inp.value = this.getElementsByTagName("input")[0].value;
                      /*close the list of autocompleted values,
                      (or any other open lists of autocompleted values:*/
                      closeAllLists();
                  });
                  a.appendChild(b);
                }
              }
          });
          /*execute a function presses a key on the keyboard:*/
          inp.addEventListener("keydown", function(e) {
              var x = document.getElementById(this.id + "autocomplete-list");
              if (x) x = x.getElementsByTagName("div");
              if (e.keyCode == 40) {
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
              } else if (e.keyCode == 38) { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
              } else if (e.keyCode == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (currentFocus > -1) {
                  /*and simulate a click on the "active" item:*/
                  if (x) x[currentFocus].click();
                }
              }
          });
          function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
          }
          function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
              x[i].classList.remove("autocomplete-active");
            }
          }
          function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
              if (elmnt != x[i] && elmnt != inp) {
              x[i].parentNode.removeChild(x[i]);
            }
          }
        }
        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
        }


        var ikt = $('#ikt');
        var entResult = $('#ENTResult');
        var buttonSubmit = $('button[type="submit"]');
        var load = $('#load-ent');
        var idTestType = $('#idTestType');
        if(ikt.length != 0) {
            buttonSubmit.prop("disabled", true);
        }
        ikt.on('keyup', function(){
            if( $(this).val().length == 9 ) {
                ikt.prop('disabled', true);
                buttonSubmit.prop("disabled", true);
                ikt.removeClass('error');
                load.removeClass('fade');
                 $.ajax({
                    url:'{{ route('ajaxEnt') }}',
                    type:'get',
                    data: { 
                        "ikt": $(this).val()
                    },
                    success:function(response){
                        var response = JSON.parse(response);
                        if(response.errorCode == 0) {
                            buttonSubmit.prop("disabled", false);
                            idTestType.val(response.idTestType);
                            entResult.html(getUserBalList(response.userBallList));
                        } else {
                            buttonSubmit.prop("disabled", true);
                            ikt.addClass('error');
                            entResult.html('');
                        }
                        ikt.prop('disabled', false);
                        load.addClass('fade');
                    }
                });
            }
        });
        function getUserBalList($list) {
            //getting language it should be Ru or Kz
            $language = '{{ app()->getLocale() }}';
            if($language != 'ru' && $language != 'kz' ) {
                $language = 'ru';
            }
            //uppercase first letter
            $language = $language.substr(0,1).toUpperCase() + $language.substr(1);

            $result = "<ul>";
            $.each($list, function($index, $theme) {
                $result += "<li>" + $theme['subjectName' + $language] + " — " + $theme.ball + "</li>";
            });
            $result += "</ul>";
            return $result;
        }

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

            $('#military').change(function () {
                readURL(this, 'military-img');
            });

            $('#r063').change(function () {
                readURL(this, 'r063-img');
            });

            $('#r086').change(function () {
                readURL(this, 'r086-img');
            });

            $('#r086_back').change(function () {
                readURL(this, 'r086-back-img');
            });
        };

    </script>
@endsection