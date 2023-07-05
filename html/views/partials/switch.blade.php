<a href="/settings/toggle/{{ urlencode($preferenceId) }}"
   class="np-outer-switch @if($isEnabled) np-outer-switch-on @else np-outer-switch-off @endif"
   hx-get="/settings/toggle/{{ urlencode($preferenceId) }}"
   hx-trigger="click"
   hx-target="#body"
   hx-swap="outerHTML">
    <div class="np-inner-switch @if(!$isEnabled) np-inner-switch-left @else np-inner-switch-right @endif"></div>
</a>