<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (class_exists('MX_Loader')) {
    class MY_Loader extends MX_Loader {}
} else {
    class MY_Loader extends CI_Loader {}
}
