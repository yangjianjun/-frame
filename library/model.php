<?php
/**
 * 模型类
 *
 */
class Model{
	public $db		=null;
	public $tbName	=null;
	public function __construct($id = null){
		$this->db = Db::getInstance();
	}
	//封装sum ,max ,min,avg  
	public function assemble($fnName=null,$fieldName=null,$where=null){
		return $this->db->assemble($this->tbName,$fnName,$fieldName,$where);
	}
	//封装count
	public function count($where=null){
		return $this->db->count($this->tbName,$where);
	}
	//封装fetchAll
	
	public function fetchAll($where=null,$group=null,$orderBy=null,$limit=null){
		return $this->db->fetchAll($this->tbName,$where,$group,$orderBy,$limit);
	}

	//封装fetchRow
	public function fetchRow($where=null){
		return $this->db->fetchRow($this->tbName,$where);
	}
	//封装insert
	public function insert(array $data=array()){
		return $this->db->insert($this->tbName,$data);
	}
	//封装update
	public function update(array $data=array(),$where=null){
		return $this->db->update($this->tbName,$data,$where);
	}
	
	//封装delete
	public function delete($where=null){
		return $this->db->delete($this->tbName,$where);
	}
 	/**
     * @return  最新插入的数据ID
     */
    public function getId($flag=true){
        return $this->db->getId($flag);
    }
}