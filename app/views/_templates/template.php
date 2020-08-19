<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// header('X-Powered-By: Prod-domProjects.com');
// header('X-XSS-Protection: 1');
// header('X-Frame-Options: SAMEORIGIN');
// header('X-Content-Type-Options: nosniff');
// header('Vary: Accept-Encoding');

if (isset($header))
{
    echo $header;
}

if (isset($main_header))
{
    echo $main_header;
}

if (isset($main_sidebar))
{
    echo $main_sidebar;
}

if (isset($main_content_header))
{
    echo $main_content_header;
}

if (isset($content))
{
    echo $content;
}

if (isset($footer))
{
    echo $footer;
}
