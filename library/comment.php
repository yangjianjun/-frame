<?php if (!defined('AFA')) die();
	
class comment{
		public static $domaid_long = array(0=>'无',1=>'shengyijie.net',2=>'875.cn',3=>'baidu.yehehe.com',4=>'baidu.gogocy.com',5=>'front.875.cn', 6=>'www.gotouzi.com', 7=>'www.touzigo.com', 8=>'www.568878.com', 9=>'www.566996.com', 10=>'www.touzicy.com', 11=>'www.gofacai.com',13=>'3g.875.cn');
		public static $domaid      = array(0=>'无',1=>'SYJ',2=>'875',3=>'YHH',4=>'GGCY',5=>'front875', 6=>'www.gotouzi.com', 7=>'www.touzigo.com', 8=>'www.568878.com', 9=>'www.566996.com', 10=>'TZCY', 11=>'GFC',13=>'3g.875.cn');
		
		//平台id->item->value
		public static $listSidArr   = array(
			array(),
			array(
				'syj-1805'=>'1805',
				'syj-360' =>'4385',
				'SYJ-sg'  =>'3370',),
			array(
				'875-2100'=>'2100',),
			array(
				'YHH-2369'=>'2369',
				'YHH-sg'  =>'3272',),
			array(
				'GGCY-3056'		=>'3056',),
			array('875-360' 	=>'4266',
			
				  '360-酒水-875'  =>'4560',
				  'BD-服饰-875' 	=>'4555',
				  '360-服饰-875'  =>'4561',
				  '泸州老窖-875' 	 =>'4775',
				  'BD-酒水-875' 	 =>'4554',
			),  //front.875.cn
			array(),  //www.gotouzi.com
			array(),  //www.touzigo.com
			array(),  //www.568878.com
			array(),  //www.566996.com
			array('TZCY-3218'    =>'3218',
				  'TZCY-专题-4268' =>'4268',),  //www.touzicy.com
			array('GFC-3552'     =>'3552'),  //www.gofacai.com
			array(),
			array(),//3g.875.cn
		);
		
		
		//方便搜索项目名，和 操作人用的   
		public static $listSidNewArr = array(
			'syj_id'     =>array(
				'syj-1805'    =>'1805',  //syj
				'syj-360'     =>'4385',
				'SYJ-sg'      =>'3370',
			
			
				'YHH-2369'	  =>'2369',
				'YHH-sg'  	  =>'3272',
			
			
				'GGCY-3056'   =>'3056',
				'TZCY-3218'   =>'3218',
				'TZCY-专题-4268'=>'4268',
				'GFC-3552'    =>'3552',
			),
			'875_id'      =>array('875-2100'  =>'2100'), //875.cn
			'front_875_id'=>array('875-360'   =>'4266',
								  '360-酒水-875'  =>'4560',
								  'BD-服饰-875' 	=>'4555',
								  '360-服饰-875'  =>'4561',
								  '泸州老窖-875' 	 =>'4775',
								  'BD-酒水-875' 	 =>'4554',),  //front.875.cn
		
		);

		//根据sid 得到 字段名 syj_id或875_id或front_875_id
		public static function  getprojectFieldName($sid=NULL){
			if (empty($sid)){
				return false;
			}
			foreach (self::$listSidNewArr as $k=>$v) {
				foreach ($v as $nk=>$nv) {
					if ($sid == $nv ){
						return array($k,$nk);
					}
				}
			}
			
		}
		
		//增加新竞价站账号
		public static $domaidIdArr =  array(1=>20, //1代表统计平台shengyijie.net ID=1,  20代表百度推广SEM竞价站账号ID=20
											3=>21, //3代表统计平台yehehe.com	 ID=3,   21代表百度推广SEM竞价站账号ID=21
											4=>23, //4代表统计平台gogocy.com	 ID=4,  23代表百度推广SEM竞价站账号ID=23
											5=>22, //5代表统计平台front875.com	 ID=5,   22代表百度推广SEM竞价站账号ID=22
											10=>24, //10代表统计平台touzicy.com	 ID=10,  24代表百度推广SEM竞价站账号ID=24
											11=>25 //11代表统计平台gofacai.com	 ID=11,  25代表百度推广SEM竞价站账号ID=25
												);

		
		/**
		 * 来源管理信息单个查询
		 * @param unknown_type $id
		 */
		public static function getSource($id){
			$cacheId = "__static__getSource_source".$id;
			$cache = cache::instance();
			$resultOne = $cache->get($cacheId);
			if($resultOne){
				return $resultOne;
			}
			$source = new Source_Model ();
			$resultOne = $source->getOne($id);
			$cache->set($cacheId,$resultOne,3600);
			return $resultOne;
		}
		
		/**
		 * 独立转化页面管理
		 * @param unknown_type $id
		 * @return unknown
		 */
		public static function getitsPag($id){
			$cacheId = "__setitsPag_source".$id;
			$resultOne =cache::instance()->get($cacheId);
			if($resultOne){
				return $resultOne;
			}
			$itspage = new Itspage_Model();
			$resultOne = $itspage->getpage($id);
			cache::instance()->set($cacheId,$resultOne,3600);
			return $resultOne;
		}
		
		//生成下拉框
		public function getSelect($name=null,$data=array(),$id=""){
			//验证
			if (empty($data) || empty($name)){
				return ;
			}
			//生成select 下拉框
			$selectedstr=NULL;
			$select = '<select name="'.$name.'" style="width:125px;" ><option value="" >请选择</option>';
		    
			foreach ($data as $k=>$o) {
				if (is_array($o)){
					$selectedstr = ($o["id"] == $id)?'selected':'' ;
					$select.="<option $selectedstr value='".$o['id']."' >".$o['name']."</option>";
				}else {
					$selectedstr = ( $id !== "" && $k == $id  )?'selected':'' ;
					$select.="<option $selectedstr value='".$k."' >".$o."</option>";
				}
			}
			$select.='</select>';
			//返回数据
			return $select ;
		}
		
		
		public static function download($fileName=null,$message=null,$goURL=null){
			if (empty($fileName)){
				return ;
			}
			if(file_exists($fileName)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($fileName));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileName));
				ob_clean();
				flush();
				readfile($fileName);
				exit;
			}
			else
			{
				common::jsTip ( $message,$goURL );
			}
		}
		
	}
?>