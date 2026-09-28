<?php
namespace App\Controllers;

use App\Libraries\ImageUpload;
use App\Models\UserModel;

class Staff extends BaseController
{
    public function index()
    {
        return view('staff/index', ['items' => (new UserModel())->where('active', 1)->orderBy('id', 'DESC')->findAll()]);
    }
    public function form(?int $id = null)
    {
        $item = $id ? (new UserModel())->where('active', 1)->find($id) : null;
        if ($id && ! $item) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('staff/form', ['item' => $item]);
    }
    public function save(?int $id = null)
    {
        $model = new UserModel();
        if ($id && ! $model->where('active', 1)->find($id)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $data = ['username' => trim((string) $this->request->getPost('username')), 'full_name' => trim((string) $this->request->getPost('full_name'))];
        $pass = (string) $this->request->getPost('password');
        if (! preg_match('/^[a-zA-Z0-9_]{3,50}$/', $data['username']) || $data['full_name'] === '' || mb_strlen($data['full_name']) > 100 || (! $id && strlen($pass) < 12) || ($pass !== '' && strlen($pass) < 12)) {
            return redirect()->back()->withInput()->with('error', 'Use a username of 3–50 letters, numbers or underscores, a name, and a password of at least 12 characters.');
        }
        $taken = $model->where('username', $data['username'])->first();
        if ($taken && (int) $taken['id'] !== (int) $id) return redirect()->back()->withInput()->with('error', 'Username already exists.');
        if ($pass !== '') $data['password'] = password_hash($pass, PASSWORD_DEFAULT);
        try { $file = ImageUpload::save($this->request->getFile('avatar'), 'avatars'); }
        catch (\RuntimeException $e) { return redirect()->back()->withInput()->with('error', $e->getMessage()); }
        if ($file) $data['avatar'] = $file;
        $model->save($id ? ['id' => $id] + $data : $data);
        return redirect()->to(site_url('staff'))->with('success', 'Staff account saved.');
    }
    public function archive(int $id)
    {
        $model = new UserModel();
        if ((int) session('user_id') === $id || $model->where('active', 1)->countAllResults() <= 1) {
            return redirect()->to(site_url('staff'))->with('error', 'You cannot archive your own account or the last active account.');
        }
        $model->where('active', 1)->update($id, ['active' => 0]);
        return redirect()->to(site_url('staff'))->with('success', 'Staff account archived. Sales history is preserved.');
    }
}
