<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function up_video(Request $request){
    	//接收上传的文件
    	$file = $request->file('Filedata');
    	if($file->isValid()){
    		//附件上传
    		//$file->store(附件存储的二级目录，磁盘驱动)/storage/app/public/video目录下
    		$rst = $file->store('video','public');
    		//echo $rst;//AsqSdZSTHaVk66yhM5hv20F7qvTaOZbVPWBcig1L.jpeg
    		echo json_encode(['success'=>true,'filename'=>"/storage/".$rst]);
    	}else{
    		echo json_encode(['success'=>false]);
    	}
    	exit;
    }
    //上传视频封面图
    public function up_pic(Request $request){
    	$file = $request->file('Filedata');
    	if($file->isValid()){
    		$rst = $file->store('lesson','public');
    		echo json_encode(['success'=>true,'filename'=>"/storage/".$rst]);
    	}else{
    		echo json_encode(['success'=>false]);
    	}
    	exit;
    }
}
