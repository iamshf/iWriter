<?php
echo '<nav id="categories">';
echo '<a href="/">首页</a>';
foreach($categories as $category) {
    echo '<a href="/', $category['id'], '">';
    for($i = 1; $i < $category['deep']; $i++) {
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    echo $category['name'], '</a>';
}
echo '</nav>';
