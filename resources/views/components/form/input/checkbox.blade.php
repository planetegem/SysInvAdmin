<label class="checkbox">
    <input type="checkbox" name="{{ $name }}" {{ $checked ? 'checked' : '' }}/>
    <h5>{{App\Words\Labels::look_up($name)}}</h5>
    <x-info-tooltip text="{{App\Words\Tooltips::look_up($name)}}" />
</label>