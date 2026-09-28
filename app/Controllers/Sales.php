<?php
namespace App\Controllers;

use App\Models\{ProductModel, CustomerModel};

class Sales extends BaseController
{
    public function index()
    {
        $rows = db_connect()->table('sales s')->select('s.id, s.quantity, s.total_price, s.created_at, p.name AS product_name, c.full_name AS customer_name, u.full_name AS staff_name')->join('products p', 'p.id = s.product_id')->join('customers c', 'c.id = s.customer_id', 'left')->join('users u', 'u.id = s.sold_by')->orderBy('s.id', 'DESC')->get()->getResultArray();
        return view('sales/index', ['items' => $rows]);
    }
    public function form()
    {
        return view('sales/form', ['products' => (new ProductModel())->where('active', 1)->orderBy('name')->findAll(), 'customers' => (new CustomerModel())->where('active', 1)->orderBy('full_name')->findAll()]);
    }
    public function save()
    {
        $productId = filter_var($this->request->getPost('product_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $customerInput = (string) $this->request->getPost('customer_id');
        $customerId = $customerInput === '' ? null : filter_var($customerInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $quantity = filter_var($this->request->getPost('quantity'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
        if (! $productId || ! $quantity || ($customerInput !== '' && ! $customerId)) return redirect()->back()->withInput()->with('error', 'Choose a product and enter a whole quantity greater than zero.');
        if ($customerId && ! (new CustomerModel())->where('active', 1)->find($customerId)) return redirect()->back()->withInput()->with('error', 'Choose an active customer or leave it blank.');

        $db = db_connect();
        $db->transBegin();
        try {
            // Lock this product row until the sale commits; all competing sales recheck stock.
            $product = $db->query('SELECT id, price, stock_quantity FROM products WHERE id = ? AND active = 1 FOR UPDATE', [$productId])->getRowArray();
            if (! $product) { $db->transRollback(); return redirect()->back()->withInput()->with('error', 'This product is unavailable.'); }
            if ((int) $product['stock_quantity'] < $quantity) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Only ' . $product['stock_quantity'] . ' unit(s) remain in stock. Sale was not recorded.');
            }
            $total = round((float) $product['price'] * $quantity, 2);
            if ($total > 99999999.99 || $total <= 0) { $db->transRollback(); return redirect()->back()->withInput()->with('error', 'Sale total exceeds the allowed amount.'); }
            $db->table('products')->where('id', $productId)->set('stock_quantity', 'stock_quantity - ' . (int) $quantity, false)->update();
            $db->table('sales')->insert(['product_id' => $productId, 'customer_id' => $customerId, 'sold_by' => (int) session('user_id'), 'quantity' => $quantity, 'total_price' => number_format($total, 2, '.', ''), 'created_at' => date('Y-m-d H:i:s')]);
            if ($db->transStatus() === false) throw new \RuntimeException('Database transaction failed.');
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Sale failed: {error}', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Sale could not be saved. Please try again.');
        }
        return redirect()->to(site_url('sales'))->with('success', 'Sale recorded and stock decreased.');
    }
}
