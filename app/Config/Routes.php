<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt', ['filter' => 'csrf']);
$routes->group('', ['filter' => \App\Filters\AuthFilter::class], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->post('logout', 'Auth::logout', ['filter' => 'csrf']);
    $routes->get('products', 'Products::index');
    $routes->get('products/new', 'Products::form');
    $routes->post('products', 'Products::save', ['filter' => 'csrf']);
    $routes->get('products/(:num)/edit', 'Products::form/$1');
    $routes->post('products/(:num)', 'Products::save/$1', ['filter' => 'csrf']);
    $routes->post('products/(:num)/archive', 'Products::archive/$1', ['filter' => 'csrf']);
    $routes->get('customers', 'Customers::index');
    $routes->get('customers/new', 'Customers::form');
    $routes->post('customers', 'Customers::save', ['filter' => 'csrf']);
    $routes->get('customers/(:num)/edit', 'Customers::form/$1');
    $routes->post('customers/(:num)', 'Customers::save/$1', ['filter' => 'csrf']);
    $routes->post('customers/(:num)/archive', 'Customers::archive/$1', ['filter' => 'csrf']);
    $routes->get('staff', 'Staff::index');
    $routes->get('staff/new', 'Staff::form');
    $routes->post('staff', 'Staff::save', ['filter' => 'csrf']);
    $routes->get('staff/(:num)/edit', 'Staff::form/$1');
    $routes->post('staff/(:num)', 'Staff::save/$1', ['filter' => 'csrf']);
    $routes->post('staff/(:num)/archive', 'Staff::archive/$1', ['filter' => 'csrf']);
    $routes->get('sales', 'Sales::index');
    $routes->get('sales/new', 'Sales::form');
    $routes->post('sales', 'Sales::save', ['filter' => 'csrf']);
});
