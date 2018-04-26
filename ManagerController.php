<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Manager;

class ManagerController extends Controller
{
    public function login(Request $request){
        //用户处于登陆状态，如果再次请求该地方，则直接进入系统
        if(\Auth::guard('back')->check()){
            return redirect('admin/index/index');
        }
    	//校验用户登陆
        if($request->isMethod('post')){
            //账号密码非空校验
            $rules = [
                'mg_name'=>'required',
                'password'=>'required',
                'verify_code'=>'required|captcha',
            ];
            $notices = [
                'mg_name.required'=>'用户名不能为空',
                'password.required'=>'密码不能为空',
                'verify_code.required'=>'验证码不能为空',
                'verify_code.captcha'=>'验证码错误',
            ];
            $validator = \Validator::make($request->all(),$rules,$notices);

            if($validator->passes()){
                //校验成功就判断用户名和密码是否正确
                $name_pwd = $request->only(['mg_name','password']);
                                    //给attempt方法设置第三个参数，表明是否要有“使我保持登陆状态”的标识
                if(\Auth::guard('back')->attempt($name_pwd,$request->input('online'))){
                    return redirect('admin/index/index');
                }else{
                    return redirect('admin/manager/login')
                        ->withErrors(['errorinfo'=>'用户名或密码错误'])
                        ->withInput();
                }
            }else{
                return redirect('admin/manager/login')
                    ->withErrors($validator)
                    ->withInput();
            }

        }
    	return view('admin.manager.login');
    }


    /**
     *管理员退出操作
     */
    public function logout(Request $request)
    {
        \Auth::guard('back')->logout();
        return redirect('admin/manager/login');
    }
    /*
     * 管理员列表
     * */
    public function index(){

       //$info = Manager::find()->role->role_name;
        $info = Manager::get();

      // dd($info);



       return view('admin.manager.index',compact('info'));
    }

}






























