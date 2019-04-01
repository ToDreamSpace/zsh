<?php
namespace app\ktv\admin;

use app\admin\controller\Admin;
use think\Db;

use app\common\builder\ZBuilder;


class Basektv extends Admin{
	
	public function index(){
		$data= DB::table('cg_ktv')->paginate();
		// 使用ZBuilder快速创建数据表格
		 return ZBuilder::make('table')
			 ->addColumns([ // 批量添加列
				 ['id', 'ID'],
				 ['ktvname', '场所名称','text.edit'],
			 ])
			 ->setRowList($data) // 设置表格数据
			 ->setTableName('ktv')
			 ->addTopButtons('add') // 批量添加顶部按钮
			 ->setColumnWidth('id', 20)
			 ->fetch(); // 渲染页面
	}
		//新增
	public function add(){
		return ZBuilder::make('form')
				->addFormItems([
					['text', 'ktvname', '场所名称']
				])
				->setUrl(url('save'))
				->fetch();
	}
	
	//保存
	public function save(){
		$data=request()->post();
		$isHave=Db::table('cg_ktv')->where('ktvname',$data['ktvname'])->find();
		if($isHave){
			return $this->error('有相同的名称,请修改');
		}else{
			if(Db::table('cg_ktv')->insert($data)){
				return $this->success('新增成功');
			};
		}
	}
	
}