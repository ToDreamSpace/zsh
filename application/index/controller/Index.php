<?php


namespace app\index\controller;

use \app\index\controller\Home;
use think\Request;
use think\Db;


/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{
	//登录首页
    public function index(){
		return $this->fetch();
    }
	//验证
	public function check(){
		$account=request()->post('account'); // 获取某个post变量
		$password=request()->post('password');
		$res=Db::table('cg_users')->where('account',$account)
		->where('password',$password)
		->find();
		if(!$res){
			return $this->error("户名或密码不正确");
		}else{
			return $this->fetch();
		}	
	}
	//阿贾克斯获取ktv明细
	public function ktvmx(){
		$ktvname=request()->post('ktvName');
		$res=Db::table('cg_ktv')->where('ktvname','like',"%$ktvname%")->limit(5)->select();
		return $res;
	}
	//关联场所
	public function guanlian(){
		$ktvid=request()->get('ktvid');
		$songn=request()->get('songn');
		$ktvname= Db::table('cg_ktv')->get($ktvid);
		
		//利用一下缓存
		if($songn){
			$songbase=Db::table('cg_song_base')
				->alias('s')
				->join('cg_ktvsong_ralation r',['s.id = r.songid',"r.ktvid=$ktvid"],'LEFT')
				->where("songname|singer","like","$songn%")
				->whereOr("songname|singer","like","%$songn")
				->order('id')
				->select();
		}else{
			$songbase=Db::table('cg_song_base')
				->alias('s')
				->join('cg_ktvsong_ralation r',['s.id = r.songid',"r.ktvid=$ktvid"],'LEFT')
				->order('id')
				->select();
		}
		
		
			
		return $this->fetch('list',
			[
				'ktvname'=>$ktvname['ktvname'],
				'songbase'=>$songbase,
				'ktvid'=>$ktvid,
			]);
	}
	
	//歌曲与ktv状态置为已点
	public function setRalation(){
		$ralation=request()->post();
		if($ralation['checked']){
			$res=Db::table('cg_ktvsong_ralation')
			->where('ktvid',$ralation['ktvid'])
			->where('songid',$ralation['songid'])
			->find();
			if($res){
				Db::table('cg_ktvsong_ralation')
					->where('ktvid',$ralation['ktvid'])
					->where('songid',$ralation['songid'])
					->update(['guanxiid'=>'1']);
				return '置为已点';
			}else{
				$data=[
					'ktvid'=>$ralation['ktvid'],
					'songid'=>$ralation['songid'],
					'guanxiid'=>'1'
				];
				Db::table('cg_ktvsong_ralation')
					->insert($data);
				return '新增已点';
			}
		}else{
			Db::table('cg_ktvsong_ralation')
				->where('ktvid',$ralation['ktvid'])
				->where('songid',$ralation['songid'])
				->update(['guanxiid'=>'0']);
				return '取消已点';
		}
	}
	
	//歌曲与ktv状态置为已点
	public function setRalationNotHave(){
		$ralation=request()->post();
		if($ralation['checked']){
			$res=Db::table('cg_ktvsong_ralation')
			->where('ktvid',$ralation['ktvid'])
			->where('songid',$ralation['songid'])
			->find();
			if($res){
				Db::table('cg_ktvsong_ralation')
					->where('ktvid',$ralation['ktvid'])
					->where('songid',$ralation['songid'])
					->update(['guanxiid'=>'2']);
				return '置为没有';
			}else{
				$data=[
					'ktvid'=>$ralation['ktvid'],
					'songid'=>$ralation['songid'],
					'guanxiid'=>'2'
				];
				Db::table('cg_ktvsong_ralation')
					->insert($data);
				return '新增没有';
			}
		}else{
			Db::table('cg_ktvsong_ralation')
				->where('ktvid',$ralation['ktvid'])
				->where('songid',$ralation['songid'])
				->update(['guanxiid'=>'0']);
				return '取消没有';
		}
	}
	
	// 查看所有歌单
	public function getAll(){	
		$data_list = Db::name('song_base')->paginate();
	}
	
}
