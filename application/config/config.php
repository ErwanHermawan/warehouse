<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['base_url'] = 'http://localhost/ci-hmvc/'; // sesuaikan
$config['index_page'] = '';
$config['uri_protocol']    = 'REQUEST_URI';
$config['language']    = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = TRUE;
$config['encryption_key'] = 'change-this-key';

$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_save_path'] = sys_get_temp_dir();
