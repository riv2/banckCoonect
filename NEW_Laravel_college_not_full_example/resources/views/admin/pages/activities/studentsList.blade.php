@extends("admin.admin_app")

@section("content")
    <div id="visits-admin-page">
        <div class="page-header">
            <h2>Посещаемость</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body" v-cloak>

                <div class="table-responsive">
                    <table style="width: 100%; margin-bottom: 2rem">
                        <thead>
                        <tr>
                            <th class="text-center width-150">
                                Ученик
                                <input class="form-control" type="text" id="profile" name="name" placeholder="ФИО" v-model="name" @keyup="buildTable">
                            </th>
                            <th class="text-center width-150">
                                Группа
                                <select class="form-control" id="group_select" name="group" v-model="group" @change="buildTable">
                                    <option value=""></option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ strtoupper($group->name) }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="text-center width-150">
                                Месяц
                                <select class="form-control" id="month_select" name="month" v-model="month" @change="buildTable">
                                    <option value="1">Январь</option>
                                    <option value="2">Февраль</option>
                                    <option value="3">Март</option>
                                    <option value="4">Апрель</option>
                                    <option value="5">Май</option>
                                    <option value="6">Июнь</option>
                                    <option value="7">Июль</option>
                                    <option value="8">Август</option>
                                    <option value="9">Сентябрь</option>
                                    <option value="10">Октябрь</option>
                                    <option value="11">Ноябрь</option>
                                    <option value="12">Декабрь</option>
                                </select>
                            </th>

                            <th class="text-center width-150">
                                Год
                                <select class="form-control" id="year_select" name="year" v-model="year" @change="buildTable"></select>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>

                <div class="table-responsive" style="overflow: unset;">
                    <table id="main-table-ajax" class="table table-striped dt-responsive table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th v-for='day in days'>@{{ day }}</th>
                        </tr>
                        </thead>
                        <tbody v-if="profiles">
                        <tr v-for="profile in profiles">
                            <td>@{{ profile.user_id }}</td>
                            <td> @{{ profile.user_full_name }} </td>
                            <td v-for="day in days">
                                <div v-for="login_day in profile.logins_day">
                                   <div v-if="day == login_day.day_in_month" v-on:click="showModal">
                                       @{{login_day.pages.length}}
                                       <div class="visits-tooltip changed-offset">
                                           <p v-for="page in login_day.pages">
                                               <span class="text-primary"> @{{ page.page }} : </span>
                                               <span class="text-success"> @{{ page.time }} : </span>
                                               <span class=""> @{{ page.url }} </span>
                                           </p>
                                       </div>
                                   </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <b-pagination v-bind:total-rows="pageCount" v-model="currentPage" :per-page="1">
                    </b-pagination>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div v-cloak style="margin-bottom: 60px">
            <div class="page-header">
                <h2>Сейчас в сети: @{{ calculateOnlineContactsByType() }} </h2>
            </div>

            <div class="panel panel-default panel-shadow">
                <div v-if="calculateOnlineContactsByType()" class="panel-body">
                    <ul class="list-group" style="width: 500px;max-height: 500px;overflow-y: auto;">
                        <template v-for="contacts in onlineContacts">
                            <li v-for="(contact, id) in contacts"
                                v-if="contact.type == STUDENT_ROLE"
                                    class="list-group-item">
                                <div>
                                    <span class="photo_wrapper_student_online">
                                        <img width="30px" height="30px" class="img-circle" :src="contact.photo ? '{{URL::asset('/images/uploads/faces')}}/' + contact.photo: '/images/uploads/faces/default.png'">
                                    </span>
                                    <span> @{{ id }} - @{{ contact.name }} </span>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <div v-if="!calculateOnlineContactsByType()" class="panel-body">
                    В данный момент никого нет в сети!
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">

        let server_path;
        if ("{{env('APP_SOCKET_ENV')}}" === "production") {
            server_path = "wss://miras.app:4433";
        }
        else if ("{{env('APP_SOCKET_ENV')}}" === "premaster") {
            server_path = "wss://premaster.miras.app:4433";
        }
        else {
            server_path = "ws://localhost:8080";
        }

        const AUTHORIZE = 'auth';
        const ALL_ONLINE_CONTACTS = 'all_online_contacts';

        var app = new Vue({
            el: '#visits-admin-page',
            data: {
                profiles: null,
                days: null,
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                group: '',
                name: '',
                currentPage: 1,
                totalCount: null,
                pageLength: 10,
                connection: null,
                currentUserData: {
                    id: '{{ Auth::user()->id }}',
                    name: '{{ Auth::user()->name }}',
                    photo: null,
                    email: '{{ Auth::user()->email }}',
                    type: 'admin',
                },
                onlineContacts: null
            },

            mounted: function() {
                this.buildTable();
                this.initializeSocket();
            },

            created: function(){
                this.generateYears();
                this.STUDENT_ROLE = '2';
            },

            methods: {
                getProfilesList: function () {
                    var self = this;

                    var year = this.year;
                    var month = this.month;
                    var group = this.group;
                    var name = this.name;
                    var currentPage = this.currentPage;
                    var pageLength = this.pageLength;

                    axios.post('{{ route('admin.activities.users.list', ['user_type' => 'student']) }}', {
                        year,
                        month,
                        group,
                        name,
                        currentPage,
                        pageLength
                    })
                        .then(function (response) {
                            if (response) {
                                self.profiles = response.data.profiles;
                                self.totalCount = response.data.totalCount;
                            } else {
                                console.log('bad request');
                            }
                        });
                },
                hideModals: function(){
                    $('.visits-tooltip').hide()
                },
                buildTable: function () {
                    this.days = new Date(this.year, this.month, 0).getDate();
                    this.getProfilesList();
                },
                generateYears: function() {
                    var max = new Date().getFullYear(),
                        min = max - 9,
                        select = document.getElementById('year_select');

                    for (var i = min; i<=max; i++){
                        var opt = document.createElement('option');
                        opt.value = i;
                        opt.innerHTML = i;
                        select.appendChild(opt);
                    }
                },
                showModal: function(e) {
                    document.removeEventListener('click', this.hideModals);
                    this.modal = e.target.querySelector('.visits-tooltip');
                    $(this.modal).toggle();

                    setTimeout(function () {
                        document.addEventListener('click', app.hideModals)
                    }, 10)

                },

                authorize: function() {
                    const data = {
                        action: AUTHORIZE,
                        params: {
                            user_id: this.currentUserData.id,
                            user_name: this.currentUserData.name,
                            user_photo: this.currentUserData.photo,
                            user_email: this.currentUserData.email,
                            user_type: this.currentUserData.type,
                        }
                    };

                    this.connection.send(JSON.stringify(data));
                },

                initializeSocket: function() {
                    this.connection = new WebSocket(server_path);

                    this.connection.onopen = (event) => {
                        console.log("WebSocket is open now!");
                        this.authorize();
                    };

                    this.connection.onclose = (event) => {
                        console.warn('Connection with WebSocket server was closed!');
                    };

                    this.connection.onmessage = (event) => {
                        let data = JSON.parse(event.data);

                        if(data.action === ALL_ONLINE_CONTACTS) {
                            this.onlineContacts = data.params.allOnlineUsers;
                        }
                    };
                },

                calculateOnlineContactsByType: function () {
                    let i = 0;
                    if (this.onlineContacts && this.onlineContacts.length > 0) {
                        this.onlineContacts.forEach(contact => {
                            for(let id in contact) {
                                if(contact[id].type == this.STUDENT_ROLE) {
                                    i++;
                                }
                            }
                        });
                    }
                    return i;
                }
            },
            watch: {
                currentPage : function(val) {
                    this.getProfilesList();
                }
            },
            computed: {
                pageCount: function() {
                    return Math.ceil(this.totalCount / this.pageLength);
                },
                printPartsCount: function() {
                    return Math.ceil(this.totalCount / 100);
                },
            }
        });

    </script>

@endsection
