<?php

namespace app\controller\admin;

use app\BaseController;
use app\Request;

class Ip extends Curd
{
    public function list(Request $request, $page = 1, $table = '')
    {
        return parent::list($request, $page, 'ip');
    }
    public function delete(Request $request, $id = 0, $table = '')
    {
        return parent::delete($request, $id, 'ip');
    }
    public function add(Request $request, $data = [], $table = '')
    {
        $match = [
            'ip' => 'ip',
            'type' => 'type',
            'remarks' => 'remarks',
        ];
        $data = $this->constructData($request->post(), $match);
        $data['created_at'] = date('Y-m-d H:i:s');

        return parent::add($request, $data, 'ip');
    }
}
