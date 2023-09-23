<?php

namespace app\controller\admin;

use app\BaseController;
use app\Request;
use think\facade\Db;

class Account extends Curd
{
    // 保护的字段，不允许查询
    protected $protected_field = ['cookie'];

    public function list(Request $request, $page = 1, $table = '')
    {
        return parent::list($request, $page, 'account');
    }
    public function delete(Request $request, $id = 0, $table = '')
    {
        return parent::delete($request, $id, 'account');
    }
    public function add(Request $request, $data = [], $table = '')
    {
        $type = $request->post('type');
        if ($type == 'single') {
            $match = [
                'name' => 'name',
                'cookie' => 'cookie',
                'remarks' => 'remarks',
            ];
            $data = $this->constructData($request->post(), $match);
            $data['status'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['last_used_at'] = date('Y-m-d H:i:s');
            return parent::add($request, $data, 'account');
        }
        if ($type == 'multi') {
            $match = [
                'name' => 'multi_name',
                'cookie' => 'multi_cookie',
                'remarks' => 'multi_remarks',
            ];
            $data = $this->constructData($request->post(), $match);
            $data['status'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['last_used_at'] = date('Y-m-d H:i:s');

            $cookies = explode("\n", $data['cookie']);
            unset($data['cookie']); // 删除原始的cookie，减少内存占用
            $count = 0;
            foreach ($cookies as $cookie) {
                $insert = $data;
                list($name, $cookie, $remarks) = $this->explodeCookie($cookie);
                if (empty($cookie)) {
                    continue;
                }
                $insert['name'] = $name ?? $data['name'];
                $insert['cookie'] = $cookie;
                $insert['remarks'] = $remarks ?? $data['remarks'];
                parent::add($request, $insert, 'account');
                $count++;
            }

            return json([
                'error' => 0,
                'msg' => "已添加 $count 条记录",
            ]);
        }
        return json([
            'error' => 1,
            'msg' => '表单错误',
        ]);
    }
    private function explodeCookie($ck)
    {
        // name----[...remarks----]cookie
        $ck = trim($ck);
        $ck = explode('----', $ck);
        $name = '';
        $cookie = '';
        $remarks = '';
        foreach ($ck as $value) {
            $value = trim($value);
            if (strstr($value, 'BDUSS=')) {
                $cookie = $value;
            } elseif (empty($name)) {
                $name = $value;
            } else {
                $remarks .= $value . "----";
            }
        }
        if (empty($name)) {
            $name = null;
        }
        if (empty($cookie)) {
            $cookie = null;
        }
        if (!empty($remarks) && strlen($remarks) >= 4 && substr($remarks, -4) == '----') {
            $remarks = substr($remarks, 0, -4);
        }
        return [$name, $cookie, $remarks];
    }
    public function reset(Request $request, $id) {
        if (empty($id)) {
            return json([
                'error' => 1,
                'msg' => 'ID不能为空',
            ]);
        }
        if ($id == 'all') {
            $count = Db::table('account')->update(['status' => 0]);
            return json([
                'error' => ($count > 0) ? 0 : -1,
                'msg' => "已重置{$count}条记录",
            ]);
        }
        Db::table('account')->where('id', $id)->update(['status' => 0]);
        return json([
            'error' => 0,
            'msg' => "已重置",
        ]);
    }
}
