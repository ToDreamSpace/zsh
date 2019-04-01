<?php
namespace app\ktv\admin;

use app\admin\controller\Admin;
use think\Db;

use app\common\builder\ZBuilder;


class Basesong extends Admin{
	
	public function index(){
		$data= DB::table('cg_song_base')->paginate();
		// 使用ZBuilder快速创建数据表格
		 return ZBuilder::make('table')
			 ->addColumns([ // 批量添加列
				 ['id', 'ID'],
				 ['songname', '歌曲名称','text.edit'],
				 ['singer', '演唱者','text.edit'],
				 ['quanliren', '版权方','text.edit'],
				 ['wordwriter', '词作者','text.edit'],
				 ['songwriter', '曲作者','text.edit'],
			 ])
			 ->setRowList($data) // 设置表格数据
			 ->setTableName('song_base')
			 ->addTopButtons('add') // 批量添加顶部按钮
			 ->setColumnWidth('id', 20)
			 ->fetch(); // 渲染页面
	}
	
	
	
	//新增
	public function add(){
		return ZBuilder::make('form')
				->addFormItems([
					['text', 'songname', '歌曲名称'],
					['text', 'singer', '演唱者'],
					['text', 'quanliren', '版权人'],
				])
				->setUrl(url('save'))
				->fetch();
	}
	
	//保存
	public function save(){
		$data=request()->post();
		$isHave=Db::table('cg_song_base')
			->where('songname',$data['songname'])
			->where('singer',$data['singer'])
			->find();
		if($isHave){
			return $this->error('有重复的歌曲,请修改！');
		}else{
			if(Db::table('cg_song_base')->insert($data)){
				return $this->success('新增成功');
			}
		}
	}
	
}