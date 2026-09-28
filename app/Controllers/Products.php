<?php
namespace App\Controllers;

use App\Libraries\ImageUpload;
use App\Models\ProductModel;

class Products extends BaseController
{
    public function index()
    {
        return view('products/index', ['items' => (new ProductModel())->where('active', 1)->orderBy('id', 'DESC')->findAll()]);
    }

    public function form(?int $id = null)
    {
        $item = $id ? (new ProductModel())->where('active', 1)->find($id) : null;
        if ($id && ! $item) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('products/form', ['item' => $item]);
    }

    public function save(?int $id = null)
    {
        $model = new ProductModel();
        $item = $id ? $model->where('active', 1)->find($id) : null;
        if ($id && ! $item) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $data = ['name' => trim((string) $this->request->getPost('name')), 'price' => trim((string) $this->request->getPost('price')), 'stock_quantity' => trim((string) $this->request->getPost('stock_quantity'))];
        $v = service('validation');
        $v->setRules(['name' => 'required|max_length[100]', 'price' => 'required|regex_match[/^\d{1,8}(\.\d{1,2})?$/]', 'stock_quantity' => 'required|regex_match[/^(0|[1-9][0-9]*)$/]']);
        $stockValid = filter_var($data['stock_quantity'], FILTER_VALIDATE_INT) !== false && ctype_digit($data['stock_quantity']) && (float) $data['stock_quantity'] <= 2147483647;
        if (! $v->run($data) || ! $stockValid || (float) $data['price'] <= 0) {
            return redirect()->back()->withInput()->with('error', 'Enter a name, a price above zero (up to 2 decimals), and stock from 0 to 2147483647.');
        }
        $data['stock_quantity'] = (int) $data['stock_quantity'];
        try {
            $file = ImageUpload::save($this->request->getFile('image'), 'products');
        } catch (\RuntimeException $e) { return redirect()->back()->withInput()->with('error', $e->getMessage()); }
        if ($file) $data['image'] = $file;
        $model->save($id ? ['id' => $id] + $data : $data);
        return redirect()->to(site_url('products'))->with('success', 'Product saved.');
    }

    public function archive(int $id)
    {
        (new ProductModel())->where('active', 1)->update($id, ['active' => 0]);
        return redirect()->to(site_url('products'))->with('success', 'Product archived. Sales history is preserved.');
    }
}
