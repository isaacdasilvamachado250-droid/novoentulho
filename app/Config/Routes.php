<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\News;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('news', [News::class, 'index']);
$routes->get('news/(:segment)', [News::class, 'show']);

