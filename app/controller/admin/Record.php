<?php

namespace app\controller\admin;

use app\BaseController;
use app\Request;

class Record extends Curd
{
    public function list(Request $request, $page = 1, $table = '')
    {
        return parent::list($request, $page, 'record');
    }
    public function delete(Request $request, $id = 0, $table = '')
    {
        return parent::delete($request, $id, 'record');
    }
    public function clearAll()
    {
        $count = \think\facade\Db::table('records')->delete(true);
        return json([
            'error' => ($count > 0) ? 0 : -1,
            'msg' => "已删除{$count}条记录",
        ]);
    }
}
