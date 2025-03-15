    <div class="form-group">
        <label>{{ __('Name') }}</label>
        <input type="text" value="{{ $translation->name }}" placeholder="{{ __('Attribute name') }}" name="name"
            class="form-control">
    </div>

    <div class="form-group">
        <label>{{ __('Hide in detail service') }}</label>
        <br>
        <label>
            <input type="checkbox" name="hide_in_single" @if ($row->hide_in_single) checked @endif
                value="1"> {{ __('Enable hide') }}
        </label>
    </div>

    <div class="form-group">
        <label>{{ __('Hide in filter search') }}</label>
        <br>
        <label>
            <input type="checkbox" name="hide_in_filter_search" @if ($row->hide_in_filter_search) checked @endif
                value="1"> {{ __('Enable hide') }}
        </label>
    </div>

    <div class="form-group">
        <label>{{ __('Display Type') }}</label>
        <br>
        <label>
            <input type="checkbox" id="enable_slider_display" name="display_type" value="slider"
                @if ($row->display_type === 'slider') checked @endif>
            {{ __('Enable Slider Display') }}
                <div class="form-group" id="step_value_group"
                style="display: {{ $row->display_type === 'slider' ? 'block' : 'none' }};">
                <label>{{ __('Step Value') }}</label>
                <input type="number" min="1" value="{{ $row->step_value ?? 1 }}"
                    placeholder="{{ __('Step value') }}" name="step_value" class="form-control">
            </div>
        </label>
    </div>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let enableSlider = document.getElementById("enable_slider_display");
            let stepValueGroup = document.getElementById("step_value_group");

            if (enableSlider) {
                enableSlider.addEventListener("change", function() {
                    stepValueGroup.style.display = this.checked ? "block" : "none";
                });
            }
        });
    </script>
@endif
