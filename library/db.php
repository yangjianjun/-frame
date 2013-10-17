<?php 
class Db
{   
	private $dbconfig			= array();
    private $db;
    private $fetch_mode 		= PDO::FETCH_ASSOC;//读取数据方式FETCH_ASSOC \FETCH_NUM \FETCH_BOTH \FETCH_OBJ

    protected static $_instance	=null; //存储自身对象 
    protected static $_read 	=null; //存储自身对象 
    protected static $_write	=null; //存储自身对象 
    /**
     * 私有构造函数
     * @return  void
     */
    private function __construct() 
    {
		//load dbconfig from config file
		$dbconfig					 = Frame::getInstance()->config['db'];
		//dbconfig is Two-dimensional array
		if (!is_array($dbconfig) || !isset($dbconfig[0]) || isset($dbconfig[0]) && !is_array($dbconfig[0])){
			throw new Exception(" dbconfig is not Two-dimensional array !");
		}
		//set master 
    	$this->dbconfig['master']	 = $dbconfig[0];
    	//is has slave
    	if (count($dbconfig)>1){
    		// selected slave items
    		$this->dbconfig['slave'] = array_slice($dbconfig, 1);
    	}else {
    		//no slave
    		$this->dbconfig['slave'] = false ;
    	}
    }
   
    /**
     * create database instance
     * @return pdoquery class object
     */
    public static function getInstance()
    {
    	if (!is_object(self::$_instance)) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    } 
    
     /**
     * $flag is true ,separate Read and Write
     * $flag is false or  dbconfig['slave'] is null ,all operate in master
     * @param string $sql
     * @param bool $flag
     */
    public function separateReadWrite($sql=null,$flag=true){
    	if (empty($sql)){
    		return false ;
    	}
    	//Separate read and write by select ,insert ,update ,delete
    	if ($flag && $this->dbconfig['slave']){
    		if (trim($sql) == ''){
    			return false;
    		}
	    	$sql = trim(strtolower($sql));
	    	//Separate read and write by select ,insert ,update ,delete
	    	if (substr($sql, 0, 6) == 'select'){
	    		//read
				$this->connectDb($sql,true);
	    	}else {
				//write
	    		$this->connectDb($sql,false);
	    	}
    	}else { // read and write both on master 
	    	$this->connectDb($sql,false);
    	}
    }
    


    /**
     * $flag is true ,select connect master ;insert ,update ,delete connect slave
     * $flag is false or dbconfig['slave'] is null ,all operate connect master
     * @param string $sql
     * @param bool $flag
     */
    private function connectDb($sql=null,$flag=true){
    	if (empty($sql)){
    		return false ;
    	}
    	if ($flag && $this->dbconfig['slave']){
    		if (is_object(self::$_read )){
    			$this->db = self::$_read ;
    		}else {
				$rand 			= rand(0,count($this->dbconfig['slave'])-1);
	   			$dbconfig 		= $this->dbconfig['slave'][$rand];
	   			$dsn 			= "mysql:host={$dbconfig['host']};dbname={$dbconfig['dbname']}";
				$user			= $dbconfig['user'];
				$pass 			= $dbconfig['password'];  		
				$this->db 		= new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => $dbconfig['conmode']));
				$this->db->exec('SET NAMES '.$dbconfig['charset']);
				self::$_read 	= $this->db ;
    		}
    		Logs::setFileLogs("db/read_write_".date("m").".log",$sql."\t".(isset($dbconfig)?$dbconfig['host']:'use old read connect'),10 );
    	}else {
    		if (is_object(self::$_write )){
    			$this->db = self::$_write ;
    		}else {
				$dbconfig 		= $this->dbconfig['master'];	
				$dsn 			= "mysql:host={$dbconfig['host']};dbname={$dbconfig['dbname']}";
				$user 			= $dbconfig['user'];
				$pass 			= $dbconfig['password'];  		
				$this->db 		= new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => $dbconfig['conmode']));
				$this->db->exec('SET NAMES '.$dbconfig['charset']);
				self::$_write 	= $this->db ;
    		}
    		Logs::setFileLogs("db/read_write_".date("m").".log",$sql."\t".(isset($dbconfig)?$dbconfig['host']:'use old write connect'),10 );
    	}
    }
    /**
     * 查询
     * @param  string $sql
     * @return  array 查询得到的数据数组
     */
    public function query($sql,$flag=true)
    {
    	$this->separateReadWrite($sql,$flag);
    	Performance::monitor($sql,Performance::BEGIN);
        $this->db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $rs = $this->db->query($sql);
//        echo $sql;
		if (empty($rs)){
			return false;
		}
        $rs->setFetchMode($this->fetch_mode);
        $data = $rs->fetchAll();
        Performance::monitor($sql,Performance::END);
        return $data;
    }
    
   
    /**
     * 更新/插入数据
     * @param  string $sql
     * @return  boolean 成功true
     */
    public function exec($sql,$flag=true)
    {
    	$this->separateReadWrite($sql,$flag);
    	Performance::monitor($sql,Performance::BEGIN);
        $res = $this->db->exec($sql);
        Performance::monitor($sql,Performance::END);
        return $res;
    }
   
    /**
     * @return  最新插入的数据ID
     */
    public function getId($flag=true)
    {
    	$this->separateReadWrite("insert",$flag);
        return $this->db->lastInsertId();
    }

    /**
     * 得到查询结果中的第一行第一列数据
     *
     * @param  string $sql
     * @return  string
     */
    public function getOne($sql,$flag=true)
    {
    	$this->separateReadWrite($sql,$flag);
    	Performance::monitor($sql,Performance::BEGIN);
        $rs = $this->db->query($sql);
        $data = $rs->fetchColumn();
        Performance::monitor($sql,Performance::END);
        return $data ;
    } 
    
    /**
     * 得到查询结果中的第一行数据
     *
     * @param  string $sql
     * @return  string
     */
    public function getOneResult($sql,$flag=true)
    {
    	$this->separateReadWrite($sql,$flag);
    	Performance::monitor($sql,Performance::BEGIN);
//    	echo $sql;
        $rs 	= $this->db->query($sql);
        if (empty($rs)){
        	return false ;
        }
        $rs->setFetchMode($this->fetch_mode);
        $data 	= $rs->fetch();
        Performance::monitor($sql,Performance::END);
        return $data ;
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
		$sql="select {$fnName}({$fieldName}) as {$fnName} from  `{$tName}` ";
		
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
		$sql="select count(*) as count from  `{$tName}` ";
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}
//		echo $sql ;
		//执行
		return $this->getOne($sql);
    }
    public function countsql($sql=null){
    	//验证
		if(empty($sql)){
			return false ;
		}
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
		//secure filter
		$data = Common::secureFilter($data);
		//生成sql语句
		$sql = "insert into `{$tName}` (`" ;
		//加入字段名
		$sql.= implode('`,`',array_keys($data)).'`)  values (';
		//加值
		$sql.= "'".implode("','",array_values($data))."')";
		
		
//		die($sql);
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
		//secure filter
		$data 	= Common::secureFilter($data);
		//生成sql
		$sql="update `{$tName}` set " ;
		
//		print_r($data);
		//加set
		foreach ($data as $k=>$v) {
			$sql.= "`$k`='".$v."'," ;
		}
		
		//去最后的,
		$sql[strlen($sql)-1] = " "  ;
		
		//加where
		if(!empty($where)){
			$sql.= ' where '.$where ;
		}
//		die($sql);
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
		$sql="select * from  `{$tName}` ";
		
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
			$sql.= ' order by '.$orderBy ;
		}
		//加where
		if(!empty($limit)){
			$sql.= ' limit '.$limit ;
		}
//		echo($sql)  ;
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
		$sql="select * from  `{$tName}` ";
		
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
		$sql = "delete from `{$tName}` " ;
		//加where
		if(!empty($where)){
			$sql.=" where ".$where ;
		}
		//执行
		return $this->exec($sql);
	}
}