<?php
namespace app\ktv\admin;

use app\admin\controller\Admin;
use think\Db;

use app\common\builder\ZBuilder;


class Index extends Admin{
	
	public function index(){
		   $js = <<<EOF
            <script type="text/javascript">
                $('#lijie').click(function(){
					window.location.href='/zsh/public/admin.php/ktv/index/get.html?ktvid='+$('#ktvmz').val();
				})
            </script>
EOF;
			$res=Db::table('cg_ktv')->select();
			$arr=array();
			foreach($res as $val){
				$arr[$val['id']]=$val['ktvname'];
			}
			$btn = [
				'title' => '进入',
			];
			return ZBuilder::make('form')
				->addSelect('ktvmz', '请选择场所', '', $arr)
				->addButton('lijie', $btn)
				->hideBtn('submit')
				->setExtraJs($js)
				->fetch();
		
	}
	
	public function add(){
	
	}
	
	public function save(){

	}
	
	public function get(){
		$ktvid=request()->get('ktvid');
// 		$sql="SELECT e.id,e.songname,e.singer,f.ralation,e.ktvname FROM
// 			(SELECT d.id,d.songname,d.singer,c.guanxiid,c.ktvname FROM 
// 			(SELECT * FROM cg_ktv a LEFT JOIN cg_ktvsong_ralation b ON a.id=b.ktvid WHERE a.id=?) c 
// 			LEFT JOIN cg_song_base d 
// 			ON c.songid=d.id
// 			) e 
// 			LEFT JOIN cg_ralation f
// 			ON e.guanxiid=f.id
// 			WHERE f.ralation IS NOT NULL
// 			ORDER BY e.id";
// 		$res=Db::query($sql,[$ktvid]);
		
		$res=Db::table("cg_ktvsong_ralation")
			->alias('r')
			->join('cg_ktv k','r.ktvid=k.id','LEFT')
			->join('cg_song_base b','r.songid=b.id','LEFT')
			->join('cg_ralation ra','r.guanxiid=ra.id','LEFT')
			->where('ktvid',$ktvid)
			->order('songid')
			->paginate();
		
		$btn_excel = [
			'title' => '导出EXCEL',
			'icon'  => 'fa fa-fw fa-key',
			'href'  => url('getExcel')."?ktvid=$ktvid",
		];
		
		return ZBuilder::make('table')
			->hideCheckbox()
			->addColumns([ // 批量添加列
				 ['songid', 'ID'],
				 ['songname', '歌曲名称'],
				 ['singer', '演唱者'],
				 ['ralation', '歌曲状态'],
				 ['ktvname','场所名称']
			 ])
			 ->addTopButton('custom', $btn_excel)
			->setRowList($res)
			->setColumnWidth('songid', 20)
			->fetch();
	}
	
	public function getExcel(){
			$ktvid=request()->get('ktvid');
// 			$sql="SELECT e.id,e.songname,e.singer,f.ralation,e.ktvname FROM
// 				(SELECT d.id,d.songname,d.singer,c.guanxiid,c.ktvname FROM 
// 				(SELECT * FROM cg_ktv a LEFT JOIN cg_ktvsong_ralation b ON a.id=b.ktvid WHERE a.id=?) c 
// 				LEFT JOIN cg_song_base d 
// 				ON c.songid=d.id
// 				) e 
// 				LEFT JOIN cg_ralation f
// 				ON e.guanxiid=f.id
// 				WHERE f.ralation IS NOT NULL
// 				ORDER BY e.id";
// 			$res=Db::query($sql,[$ktvid]);
			$res=Db::table("cg_ktvsong_ralation")
				->alias('r')
				->join('cg_ktv k','r.ktvid=k.id','LEFT')
				->join('cg_song_base b','r.songid=b.id','LEFT')
				->join('cg_ralation ra','r.guanxiid=ra.id','LEFT')
				->where('ktvid',$ktvid)
				->order('songid')
				->select();

			
			$cellName = [
				['songid', 'auto', 'ID'],
				['songname', 'auto', '歌曲名称'],
				['singer', 'auto', '演唱者'],
				['quanliren', 'auto', '版权方'],
				['wordwriter', 'auto', '词作者'],
				['songwriter', 'auto', '曲作者'],
				['ralation', 'auto', '点播情况'],
				['ktvname', 'auto', '场所名称'],
			];
			// 调用插件（传入插件名，[导出文件名、表头信息、具体数据]）
			plugin_action('Excel/Excel/export', [$res[0]['ktvname'], $cellName, $res]);
	}
	
	
	
	//导出
	public function export()
    {
        // 查询数据
        $data = db('chenggong')->all();
        // 设置表头信息（对应字段名,宽度，显示表头名称）
        $cellName = [
            ['id', 'auto', 'ID'],
            ['name', 'auto', '姓名'],
            ['pic', 'auto', '图片地址'],
        ];
        // 调用插件（传入插件名，[导出文件名、表头信息、具体数据]）
        plugin_action('Excel/Excel/export', ['成功表格', $cellName, $data]);
    }
	
	
	
	

	
	
}