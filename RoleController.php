<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Role;
use App\Http\Models\Permission;

class RoleController extends Controller
{
    public function index(){
        $info = Role::get();
        //dd($info);
        return view('admin/role/index',compact('info'));
    }

    public function xiugai(Request $request,Role $role){
        if($request->isMethod('post')){
            $ps_ids = implode(',',$request->input('quanxian'));
              //权限对应的“控制器-操作方法（根据$ps_ids获取）
            $ps_ca = Permission::whereIn('ps_id',$request->input('quanxian'))
                ->select(\DB::raw("concat(ps_c,'-',ps_a) as ca"))
                ->whereIn('ps_level',['1','2'])
                ->pluck('ca');
            //把$ps_ca的数组给提取出来 并转化为“，逗号”连接的字符串
            $ps_ca = implode(',',$ps_ca->toArray());
            $rst = $role->update([
                'ps_ids'=>$ps_ids,
                'ps_ca'=>$ps_ca,
            ]);
            return ['success'=>true];

        }
        $permission_A = Permission::where('ps_level','0')->get();
        $permission_B = Permission::where('ps_level','1')->get();
        $permission_C = Permission::where('ps_level','2')->get();

        return view('admin/role/xiugai',compact('role','permission_A','permission_B','permission_C'));
    }
}
