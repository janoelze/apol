css_file="tinting.css"

#base_color_r="52"
#base_color_g="6"
#base_color_b="48"

base_color_r="33"
base_color_g="39"
base_color_b="50"

generate_tinted_classes() {
  for i in {0..100}; do
    r=$(($base_color_r + $i * 3))
    g=$(($base_color_g + $i * 3))
    b=$(($base_color_b + $i * 3))
    echo ".tint-fg-up-$i { color: rgb($r, $g, $b); }" >> $css_file
    echo ".tint-bg-up-$i { background-color: rgb($r, $g, $b); }" >> $css_file
  done
  for i in {0..100}; do
    r=$(($base_color_r - $i * 3))
    g=$(($base_color_g - $i * 3))
    b=$(($base_color_b - $i * 3))
    echo ".tint-fg-down-$i { color: rgb($r, $g, $b); }" >> $css_file
    echo ".tint-bg-down-$i { background-color: rgb($r, $g, $b); }" >> $css_file
  done
}

if [ -f $css_file ]; then
  rm $css_file
fi

generate_tinted_classes