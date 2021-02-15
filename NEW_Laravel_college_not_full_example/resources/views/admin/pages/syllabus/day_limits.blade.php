<div class="page-header">
    <h2>План по оценкам</h2>
</div>
<div class="panel panel-default panel-shadow">
    <div class="panel-body" style="padding: 10px;">

        <div class="col-md-12" style="margin-bottom: 10px;">
            <div class="col-md-3">
                Выберите группу<select class="form-control" v-model="currentStudyGroup">
                    @foreach($studyGroupList as $studyGroup)
                        <option value="{{ $studyGroup->id }}">{{ $studyGroup->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="alert alert-warning col-md-12" v-show="currentStudyGroup">
        Суммарное количество баллов не может превышать 20.
        </div>

        <div class="col-md-12" v-show="currentStudyGroup">
            <table class="table tab">
                <tr>
                    <th></th>
                    <th v-for="i in 5" class="text-center">день @{{i}}</th>
                </tr>
                <tr v-for="i in 15">
                    <td>неделя @{{i}}</td>
                    <td v-for="i2 in 5" class="text-center">
                        <center>
                        <input
                                v-bind:disabled="ratingSum >= 20 && ratingLimits[(i-1) * 5 + i2] == undefined"
                                class="form-control width-100"
                                type="number"
                                min="1"
                                v-model="ratingLimits[(i-1) * 5 + i2]"
                                v-on:keyup="ratingDayChange((i-1) * 5 + i2)"
                                v-bind:id="'day-rating-' + ((i-1) * 5 + i2)"
                        />

                        </center>
                    </td>
                </tr>
            </table>
        </div>
        <a v-on:click="saveRatingLimit()" class="btn btn-primary" v-show="currentStudyGroup">Сохранить изменения</a>
    </div>
    <div class="clearfix"></div>
</div>