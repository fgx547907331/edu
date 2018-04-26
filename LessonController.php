<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Lesson;
use App\Http\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;;

class LessonController extends Controller
{
    public function index(Request $request){
    	if($request->isMethod('post')){
    		
    		$cnt = Lesson::count();//记录总条数


    		//A.数据分页（显示条数）
    		$offset = $request->input('start');
    		$len = $request -> input('length');

    		//B.排序
    		$n = $request->input('order.0.column');//获取排序字段的序号
    		$duan = $request->input('columns.'.$n.'.data');//获取排序的字段
    		$xu = $request->input('order.0.dir');//排序的顺序

    		//C.模糊查询（课时名称 和 课时秒数）
    		$search = $request->input('search.value');//获取检索的条件值

    		$shuju = Lesson::offset($offset)
    				->limit($len)
    				->orderBy($duan,$xu)
    				->where('lesson_name','like',"%$search%")
    				->orWhere('lesson_desc','like',"%$search%")
    				->with(['course'=>function($c){
    					$c->with('profession');
    				}])
    				->get();//数据本身是一个集合，里面每个单元都是一个小的lesson对象

    		//datatable要求我们给客户端回传的数据如下格式
    		$info = [
    			   'draw'=>$request->get('draw'),//客户端传递过来的次数标识，直接再传递回去接口
				   'recordsTotal'=>$cnt,
				   'recordsFiltered'=>$cnt,
				   'data'=>$shuju,
    		];

    		return $info;//返回的数据自动转化为json格式

    	}
    	return view('admin.lesson.index');
    }

    //添加课时
    public function tianjia(Request $request){

    	if($request->isMethod('post')){

    		$form_data = $request->all();


    		$rules = [
    			'lesson_name'=>'required',
    			//时长：通过正则校验，要求“个位”或“十位”数字
    			'lesson_duration'=>['required','regex:/^[1-9]|[1-9]\d$/'],
    		];
    		//提示信息
    		$notices = [
    			'lesson_name.required'=>'课时名称必须填写',
    			'lesson_duration.required'=>'课时时长必须设置',
    			'lesson_duration.regex'=>'课时时长是一个个整数或十位数字',
    		];
          
    		$validator = Validator::make($form_data,$rules,$notices);

    		if($validator->passes()){
                $form_data['teacher_ids'] = implode(',', $form_data['laoshi']);
    			Lesson::create($form_data);
    			return ['success'=>true];
    		}else{
    			//校验不通过
    			$errorinfo = collect($validator->messages())->implode('0','|');
    			return ['success'=>false,'errorinfo'=>$errorinfo];
    		}

    	}

        //获取课程
        $course = Course::pluck('course_name','course_id')->toArray();
        $teacher =DB::table('teacher')->pluck('teacher_name','teacher_id')->toArray();

        return view('admin.lesson.tianjia',compact('course','teacher'));//两种方法都可以使用
    	//return view('admin.lesson.tianjia',['data'=>$course,'teacher'=>$teacher]);
    }


    //修改课时
    public function xiugai(Request $request,Lesson $lesson){
        if($request->isMethod('post')){
            //获取表单提交信息
            $form_data = $request->all();

            //验证数据
            $rules = [
                'lesson_name' => 'required',
                'lesson_duration' => ['required','regex:/^[1-9]|[1-9]\d$/'],
                'laoshi'=>'required',
            ];
            //提示信息
            $notices = [
                'lesson_name.required'=>'课时名不能为空',
                'lesson_duration.required'=>'课程时长必须填写',
                'lesson_duration.regex'=>'课时时长必须是一个整数或一个十位数',
                'laishi.required'=>'请选择教师',
            ];

            $validator = Validator::make($form_data,$rules,$notices);
            if($validator->passes()){
                $form_data['teacher_ids'] = implode(',', $form_data['laoshi']);
                //A,封面图，上传新的，删除旧的
                if( $lesson->cover_img && ($form_data['cover_img'] !== $lesson->cover_img)){
                    //判断被删除的文件是否存在
                    if(file_exists('.'.$lesson->cover_img)){
                        //删除数据库旧的封面
                        $cover = str_replace('/storage/','',$lesson->cover_img);
                        Storage::disk('public')->delete($cover);
                    }
                }
                //删除视频
                if($lesson->video_address && ($form_data['video_address'] !== $lesson->video_address)){
                    //判断数据库是否存在文件
                    if(file_exists('.'.$lesson->video_address)){
                        //删除文件
                        $video = str_replace('/storage/','',$lesson->video_address);
                        Storage::disk('public')->delete($video);
                    }
                }
               //修改课时
                $lesson->update($form_data);
                return ['success'=>true];

            }else{
                //校验不通过
                $errorinfo = collect($validator->messages())->implode('0','|');
                return ['success'=>false,'errorinfo'=>$errorinfo];
            }

        }
        //湖区课程信息
        $course = Course::pluck('course_name','course_id')->toArray();
        $teacher = DB::table('teacher')->pluck('teacher_name','teacher_id')->toArray();
        return view('admin.lesson.xiugai',compact('course','teacher','lesson'));
    }

    //删除课时
    public function del(Request $request,Lesson $lesson){
        if($request->isMethod('post')){
             $z = $lesson->delete();
             if($z){ 
                return ['success'=>true];
            }else{
                return ['success'=>false];
            } 
        }
        exit;
       

    }

    //播放视频
    public function video_play(Request $request,Lesson $lesson){


        //调用模板
        return view('admin.lesson.video_play',compact('lesson'));

    }
}
?>