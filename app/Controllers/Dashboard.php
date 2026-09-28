<?php
namespace App\Controllers;

use App\Models\{ProductModel, CustomerModel, UserModel, SaleModel};

class Dashboard extends BaseController
{
    public function index()
    {
        return view('dashboard', ['products' => (new ProductModel())->where('active', 1)->countAllResults(), 'customers' => (new CustomerModel())->where('active', 1)->countAllResults(), 'staff' => (new UserModel())->where('active', 1)->countAllResults(), 'sales' => (new SaleModel())->countAllResults()]);
    }
}
