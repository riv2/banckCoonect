<div class="col-12" id="phoneForm" style="{{ ( isset($profile) and $profile->user_approved == 1 )?'':'display: none;' }}">

    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        {{__("Thank you, your ID is accepted")}}!
    </div>

    <blockquote>{{__("Please enter a mobile number")}}</blockquote>

    <form>
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
            <label for="mobile" class="col-md-4 control-label">{{__('Mobile phone')}}</label>

            <div class="input-group mb-2 col-md-4">
                <div class="input-group-addon">
                    <span class="input-group-text">+7</span>
                </div>
                <input id="mobile" type="string" class="form-control" name="mobile" value="{{ old('mobile') }}" required autofocus minlength="7">

            </div>
        </div>

        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    {{__("Send")}}
                </button>
            </div>
        </div>

    </form>
</div>
