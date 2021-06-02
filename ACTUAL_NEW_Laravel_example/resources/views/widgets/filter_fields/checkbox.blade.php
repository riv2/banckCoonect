<input type="hidden" name="{{ $filter_name }}" value="false">
<input type="checkbox"
       name="{{ $filter_name }}"
       id="{{ $filter_name }}"
       value="true"
       class="form-check-input m-0" @if(request()->get($filter_name) == '1') checked @endif>
