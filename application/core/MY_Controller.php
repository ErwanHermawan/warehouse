<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! class_exists('MX_Controller')) {
    class MY_Controller extends CI_Controller {
        public function __construct()
        {
            parent::__construct();
        }
    }
} else {
    class MY_Controller extends MX_Controller {
        public function __construct()
        {
            parent::__construct();
        }
    }
}
