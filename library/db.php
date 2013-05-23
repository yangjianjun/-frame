<?php 

/**
 * 数据库操作类 db 使用->PDO 接口
 */

class db
{   
	protected static $_this; //存储自身对象 
    private $db;
    private $dsn = "";
    private $user = "";
    private $pass = "";
    private $fetch_mode = PDO::FETCH_ASSOC;//读取数据方式FETCH_ASSOC \FETCH_NUM \FETCH_BOTH \FETCH_OBJ

    /**
     * 私有构造函数
     * @return  void
     */
    private function __construct() 
    {
    	$frame		= Frame::getInstance();
    	$dbconfig = & $frame->config['db'];
        $this->dsn = "mysql:host={$dbconfig['host']};dbname={$dbconfig['dbname']}";
        $this->user = $dbconfig['user'];
        $this->pass = $dbconfig['password'];

        $this->db = new PDO($this->dsn, $this->user, $this->pass, array(PDO::ATTR_PERSISTENT => $dbconfig['conmode']));
        $this->db->exec('SET NAMES '.$dbconfig['charset']);
    }
   
    /**
     * create database instance
     * @return pdoquery class object
     */
    public static function instance()
    {
    	if (!is_object(self::$_this)) {
    		self::$_this = new db();
    	}
    	return self::$_this;
    } 
    
    /**
     * 查询
     * @param  string $sql
     * @return  array 查询得到的数据数组
     */
    public function query($sql)
    {
    	Performance::sql($sql,Performance::BEGIN);
        $this->db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $rs = $this->db->query($sql);
        $rs->setFetchMode($this->fetch_mode);
        $data = $rs->fetchAll();
        Performance::sql($sql,Performance::END);
        return $data;
    }
    
   
    /**
     * 更新/插入数据
     * @param  string $sql
     * @return  boolean 成功true
     */
    public function exec($sql)
    {
    	Performance::sql($sql,Performance::BEGIN);
        $data = $this->db->exec($sql);
        Performance::sql($sql,Performance::END);
    }
   
    /**
     * @return  最新插入的数据ID
     */
    public function getId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * 得到查询结果中的第一行第一列数据
     *
     * @param  string $sql
     * @return  string
     */
    public function getOne($sql)
    {
        $rs = $this->db->query($sql);
        return $rs->fetchColumn();
    } 
    
    /**
     * 得到查询结果中的第一行数据
     *
     * @param  string $sql
     * @return  string
     */
    public function getOneResult($sql)
    {
        $rs = $this->db->query($sql);
        $rs->setFetchMode($this->fetch_mode);
        return $rs->fetch();
    }
    
    /**
     * 事务处理，执行一系列更新或插入语句
     */
    public function transaction($sqlQueue)
    {
        if(count($sqlQueue)>0)
        {
            //$this->result->closeCursor();
            try
            {
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $this->db->beginTransaction();
                foreach ($sqlQueue as $sql)
                {
                    $this->db->exec($sql);
                }
                $this->db->commit();
                return true;
            } catch (Exception $e) {
                $this->db->rollBack();
                return false;
            }
        }else{
            return false;
        }
    }
    
    //聚集函数   封装sum ,max ,min,avg
	public function assemble($tName=null,$fnName=null,$fieldName=null,$where=null){
		//验证
		if(empty($fnName) || empty($tName) || empty($fieldName) ){
			return false ;
		}
		//生成sql
		$sql="select {$fnName}({$fieldName}) as {$fnName} from  {$tName} ";
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}
//		echo $sql.'<br />';
		//执行
		return $this->getOne($sql);
	}
    
    //count
    public function count($tName=null,$where=null){
    	//验证
		if(empty($tName)){
			return false ;
		}
		//生成sql
		$sql="select count(*) as count from  {$tName} ";
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}
//		echo $sql ;
		//执行
		return $this->getOne($sql);
    }
    
	//封装insert
	public function insert($tName=null,array $data=array()){
		//sql = insert into 表名 (字段名1,字段名2..)  values (值1,值2..)
		//验证
		if(empty($tName) || empty($data)){
			return false ;
		}
		//生成sql语句
		$sql = "insert into {$tName} (" ;
		//加入字段名
		$sql.= implode(',',array_keys($data)).')  values (';

		//加值
		$sql.= "'".implode("','",array_values($data))."')";
		
		//执行
		return $this->exec($sql);
	}
	
	//封装update
	public function update($tName=null,array $data=array(),$where=null){
		//sql=update 表名 set 字段名1=值1,字段名2=值2,... where 条件
		//验证
		if(empty($tName) || empty($data)){
			return false ;
		}
		//生成sql
		$sql="update {$tName} set " ;
		
		//加set
		foreach ($data as $k=>$v) {
			$sql.= "$k='".$v."'," ;
		}
		
		//去最后的,
		$sql[strlen($sql)-1] = " "  ;
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}

		//执行
		return $this->exec($sql);
	}
	
	//封装fetchAll
	public function fetchAll($tName=null,$where=null,$group=null,$orderBy=null,$limit=null){
		//sql = select * from  表名 where 条件
		//验证
		if(empty($tName)){
			return false ;
		}
		//生成sql
		$sql="select * from  {$tName} ";
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}
		//加group
		if(!empty($group)){
			$sql.= ' group by '.$group ;
		}
		//加orderBy
		if(!empty($orderBy)){
			$sql.= ' orderBy '.$orderBy ;
		}
		//加where
		if(!empty($limit)){
			$sql.= ' limit '.$limit ;
		}
//		echo $sql ;
		//执行
	
		return $this->query($sql) ;
	}
	
	//封装fetchRow
	public function fetchRow($tName=null,$where=null){
		//sql = select * from  表名 where 条件 limit 0,1 
		//验证
		if(empty($tName)){
			return false ;
		}
		//生成sql
		$sql="select * from  {$tName} ";
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}
		//加limit 0,1
		$sql.= ' limit 0,1' ;
		
//		die($sql);
		//执行
		
		return $this->getOneResult($sql) ;
	}
	//封装delete
	public function delete($tName=null,$where=null){
		// sql = delete from 表名 where 条件
		//验证
		if(empty($tName)){
			return false ;
		}
		//生成sql
		$sql = "delete from {$tName} " ;
		//加where
		if(!empty($where)){
			$sql.=" where ".$where ;
		}
		//执行
		return $this->exec($sql);
	}
}