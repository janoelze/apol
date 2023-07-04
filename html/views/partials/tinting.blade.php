<style>
    @php
        $rgb = [52, 6, 48];
        for ($i = 0; $i < 60; $i++) {
            $rgb[0] += 2.5;
            $rgb[1] += 2.5;
            $rgb[2] += 2.5;
            echo ".tint-fg-up-$i { color: rgb($rgb[0], $rgb[1], $rgb[2]); }";
            echo ".tint-bg-up-$i { background-color: rgb($rgb[0], $rgb[1], $rgb[2]); }";
        }
        $rgb = [52, 6, 48];
        for ($i = 0; $i < 60; $i++) {
            $rgb[0] -= 2.5;
            $rgb[1] -= 2.5;
            $rgb[2] -= 2.5;
            echo ".tint-fg-down-$i { color: rgb($rgb[0], $rgb[1], $rgb[2]); }";
            echo ".tint-bg-down-$i { background-color: rgb($rgb[0], $rgb[1], $rgb[2]); }";
        }
    @endphp
</style>