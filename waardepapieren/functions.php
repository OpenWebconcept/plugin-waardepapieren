<?php


function wporg_callback() {
    // do something
    var_dump("test");
}
add_action( 'init', 'wporg_callback' );