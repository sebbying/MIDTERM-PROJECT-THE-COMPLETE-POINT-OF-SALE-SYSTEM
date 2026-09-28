<?php
namespace App\Controllers;

use App\Models\CustomerModel;

class Customers extends BaseController
{
    public function index()
    {
        return view('customers/index', ['items' => (new CustomerModel())->where('active', 1)->orderBy('id', 'DESC')->findAll()]);
    }
    public function form(?int $id = null)
    {
        $item = $id ? (new CustomerModel())->where('active', 1)->find($id) : null;
        if ($id && ! $item) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('customers/form', ['item' => $item]);
    }
    public function save(?int $id = null)
    {
        $model = new CustomerModel();
        if ($id && ! $model->where('active', 1)->find($id)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $data = ['full_name' => trim((string) $this->request->getPost('full_name')), 'email' => trim((string) $this->request->getPost('email')), 'phone' => trim((string) $this->request->getPost('phone'))];
        if (! service('validation')->setRules(['full_name' => 'required|max_length[100]', 'email' => 'required|valid_email|max_length[100]', 'phone' => 'permit_empty|max_length[20]'])->run($data)) {
            return redirect()->back()->withInput()->with('error', 'Enter a name and valid email. Phone must be 20 characters or fewer.');
        }
        $model->save($id ? ['id' => $id] + $data : $data);
        return redirect()->to(site_url('customers'))->with('success', 'Customer saved.');
    }
    public function archive(int $id)
    {
        (new CustomerModel())->where('active', 1)->update($id, ['active' => 0]);
        return redirect()->to(site_url('customers'))->with('success', 'Customer archived. Sales history is preserved.');
    }
}
