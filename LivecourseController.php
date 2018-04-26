<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Livecourse;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Stream;


use Illuminate\Support\Facades\Validator;

class LivecourseController extends Controller
{
    public function index(Request $request){

    	$shuju = Livecourse::with('Stream')->get();
    	//dd($shuju);
    	/*if($request->isMethod('post')){
    		$count = Livecourse::count();
    		return "123";
    	}*/
    	return view('admin.Livecourse.index',compact('shuju'));
    }

    public function tianjia(Request $request){
    	if($request->isMethod('post')){
    		$form_data = $request->all();
    		$rules = [
    			'name'=>'required',
    			'stream_id'=>'required',
    			'cover_img'=>'required',
    			'start_at'=>'required',
    			'end_at'=>'required',
    			'desc'=>'required',
    		];

    		$notise = [
    			'name.required'=>'直播课程名不能为空',
                //'name.unique'=>'直播课程名重复',
    			'stream_id.required'=>'没有选择直播流',
                //'stream_id.unique'=>'直播流重复使用',
    			'cover_img.required'=>'请上传封面图',
    			'start_at.required'=>'开始时间不能为空',
    			'end_at.required'=>'结束时间不能为空',
    			'desc.required'=>'请填写备注',
    		];
    		$validator = Validator::make($form_data,$rules,$notise);
    		if($validator->passes()){
    			$data = [
    				'name'=>$form_data['name'],
    				'stream_id'=>$form_data['stream_id'],
    				'cover_img'=>$form_data['cover_img'],
    				'start_at'=>strtotime($form_data['start_at']),
    				'end_at'=>strtotime($form_data['end_at']),
    				'desc'=>$form_data['desc'],
    			];

    			Livecourse::create($data);

    			return ['success'=>true];
    		}else{
    			$errorinfo = collect($validator->messages())->implode('0','|');
    			return ['success'=>false,'errorinfo'=>$errorinfo];
    		}

    	}
    	$stream = DB::table('stream')->pluck('stream_name','stream_id')->toArray();
    	return view('admin.Livecourse.tianjia',compact('stream'));
    	
    }
    //上传图片
    public function up_pic(Request $request){
    	$file = $request->file('Filedata');

    	/*$filename = strtoupper(str_replace('.','',strrchr($file,'.')));
    	//判断文件格式是否正确
    	$type = array('BMP','JPEG','GIF','PSD','PNG','TIFF','TGA','EPS','JPG');
    	if(in_array($filename,$type)){
    	}else{
    		echo json_encode(['success'=>false]);
    	}*/

    	if($file->isValid()){
	    		$rst = $file->store('Livecourse','public');
	    		echo json_encode(['success'=>true,'filename'=>"/storage/".$rst]);
	    	}else{
	    		echo json_encode(['success'=>false]);
	    	}
    	
    	exit;

    	

    	


    }
}
