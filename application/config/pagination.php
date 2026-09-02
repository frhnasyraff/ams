<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Shared pagination markup and labels. Individual controllers still provide
// their own URL, row count, page size and boundary page numbers.
$config['num_links'] = 2;
$config['prev_link'] = 'Previous';
$config['next_link'] = 'Next';
$config['full_tag_open'] = '<ul class="pagination app-pagination">';
$config['full_tag_close'] = '</ul>';
$config['first_tag_open'] = '<li class="page-item app-page-first">';
$config['first_tag_close'] = '</li>';
$config['last_tag_open'] = '<li class="page-item app-page-last">';
$config['last_tag_close'] = '</li>';
$config['prev_tag_open'] = '<li class="page-item previous">';
$config['prev_tag_close'] = '</li>';
$config['next_tag_open'] = '<li class="page-item next">';
$config['next_tag_close'] = '</li>';
$config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
$config['cur_tag_close'] = '</span></li>';
$config['num_tag_open'] = '<li class="page-item">';
$config['num_tag_close'] = '</li>';
$config['attributes'] = array('class' => 'page-link');
