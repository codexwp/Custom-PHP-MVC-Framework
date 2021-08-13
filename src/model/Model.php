<?php

namespace src\model;

use app\Exception;
use Mysqli;

class Model
{
    private $connection=null;
    private $table;
    private $sql;
    private $isRaw = false;
    private $select = '';
    private $where = '';
    private $order = '';
    private $limit = '';
    protected $last_id;
    //For pagination
    protected $isPaginate = false;
    protected $offset = false;
    protected $recordsPerPage = false;
    protected $totalPages;
    //For pagination
    public $error = '';

    function __construct()
    {
        $this->openConnection();
        $this->setTableName();
    }

    function __destruct()
    {
        $this->closeConnection();
    }

    private function setTableName(){
        $class = get_called_class();
        $this->table = explode('\\', $class)[2];
    }

    private function openConnection(){
        if($this->connection!=null)
            return;

        $this->connection = new mysqli(DATABASE['host'], DATABASE['user'], DATABASE['password'], DATABASE['name']);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        $this->connection->set_charset( 'utf8' );

    }

    private function closeConnection(){
        if($this->connection!=null){
            $this->connection->close();
        }
    }

    private function query($fetch=true){
        if($this->isPaginate){            
            $query = $this->connection->query($this->sql);
            if ($query === FALSE){
                throw new Exception($this->connection->error);
            }
            $fetch_all_data = $query->fetch_all(MYSQLI_ASSOC);
            $this->totalPages = ceil(count($fetch_all_data)/$this->recordsPerPage);
            $sql =$this->sql." LIMIT $this->offset, $this->recordsPerPage;";
            $query = $this->connection->query($sql);
        }
        else {
            $query = $this->connection->query($this->sql);
            if(!is_object($query) && $query)
                return true;
        }
        if ($query === FALSE)
            throw new Exception($this->connection->error);

        if($fetch)
            return $query->fetch_all(MYSQLI_ASSOC);
        else
            return true;

    }

    private function fetch(){
        if($this->isRaw == false){
            $this->sql = $this->toSql();
        }
        return $this->query();
    }

    public function create($params){
        try {
            $params['CREATED_AT'] = date('Y-m-d H:i:s');
            $params['UPDATED_AT'] = date('Y-m-d H:i:s');
            $fields = '';
            $values = '';
            foreach ($params as $k => $v) {
                $fields .= $k . ",";
                $values .= "'" . $v . "',";
            }
            $fields = rtrim($fields, ',');
            $values = rtrim($values, ',');
            $this->sql = "INSERT INTO " . $this->table . "(" . $fields . ") VALUES (" . $values . ")";
            $this->query(false);
            $this->last_id = $this->connection->insert_id;
            return true;
        }
        catch (Exception $e){
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function update($params){
        try {
            if (empty($this->where))
                throw new Exception("'Where' condition is missing. Use like  obj->where()->update() at first");
            $args = '';
            $params['updated_at'] = date('Y-m-d H:i:s');
            foreach ($params as $k => $v) {
                if($v===NULL)
                    $args .= $k . "=NULL,";
                else
                    $args .= $k . "='" . $v . "',";
            }
            $args = rtrim($args, ',');
            $this->sql = "UPDATE " . $this->table . " SET " . $args . $this->where;
            $this->query(false);
            return true;
        }
        catch (Exception $e){
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function delete(){
        try {
            if (empty($this->where))
                throw new Exception("'Where' condition is missing. Use like  obj->where()->delete() at first");

            $this->sql = "DELETE FROM " . $this->table . $this->where;
            $this->query(false);
            return true;
        }
        catch (Exception $e){
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function execute(){
        try {
            $this->query(false);
            return true;
        }
        catch (Exception $e){
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function select($params){
        if(is_array($params)) {
            $field = implode(',', $params);
            $this->select = "SELECT " . $field . " FROM " . $this->table;
        }
        else if(is_string($params)){
            $this->select = "SELECT " . $params . " FROM " . $this->table;
        }
        return $this;
    }

    public function where($params){
        $where = '';
        $operators = array('<','>','<=','>=');
        foreach ($params as $k => $v){
            if(strtolower($k)=='or'){
                $whereOr = '(';
                foreach ($v as $i => $j){
                    $arr = explode(' ', $j);
                    $length = count($arr);
                    if ($length > 1 && in_array($arr[0], $operators))
                        $whereOr .= $i . $arr[0] . "'" . $arr[$length-1] . "' or ";
                    else
                        $whereOr .= $i . " = '" . $j . "' or ";
                }
                $where .= rtrim($whereOr," or ").') and ';
            }
            else {
                $arr = explode(' ', $v);
                $length = count($arr);
                if ($length > 1 && in_array($arr[0], $operators))
                    $where .= $k . $arr[0] . "'" . $arr[$length - 1] . "' and ";
                else
                    $where .= $k . "='" . $v . "' and ";
            }
        }
        $this->where = " WHERE ". rtrim($where," and ");
        return $this;
    }

    public function orderBy($params){
        $order = '';
        foreach ($params as $k => $v){
            $order .=$k." ".$v.", ";
        }
        $this->order = " ORDER BY ". rtrim($order,', ');
        return $this;
    }

    public function limit($start, $end = false){
        if($end)
            $this->limit = " LIMIT " .$start. ",".$end;
        else
            $this->limit = " LIMIT " .$start;
        return $this;
    }

    public function toSql(){
        if($this->isRaw){
            return $this->sql;
        }
        $sql = '';
        if($this->select)
            $sql .= $this->select;
        else
            $sql .= "SELECT *FROM ". $this->table;

        if($this->where)
            $sql .= $this->where;

        if($this->order)
            $sql .= $this->order;

        if($this->limit)
            $sql .= $this->limit;

        return $sql;
    }

    public function get(){
        try {
            $data = $this->fetch();
            return json_decode(json_encode($data));
        }
        catch (Exception $e){
            $e->showErrorMessage();
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function first(){
        try {
            $data = $this->fetch();
            $data = json_decode(json_encode($data));
            if (count($data))
                return $data[0];
            else
                return false;
        }
        catch (Exception $e){
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function getAll(){
        try {
            $this->sql = "SELECT * FROM " . $this->table;
            return $this->get();
        }
        catch (Exception $e){
            $this->error = $e->getErrorMessage();
            system_log($this->error);
            return false;
        }
    }

    public function count(){
        $data = $this->get();
        return count($data);
    }

    public function raw($sql){
        $this->isRaw = true;
        $this->sql = $sql;
        return $this;
    }

    public function find($id){
        $sql = "SELECT * FROM ".$this->table." WHERE ID=".$id." LIMIT 1";
        return $this->raw($sql)->first();
    }

    public function paginate($no_of_records_per_page=10){
        $this->isPaginate = true;
        if (isset($_GET['pageno'])) {
            $page_no = $_GET['pageno'];
        } else {
            $page_no = 1;
        }
        $this->recordsPerPage = $no_of_records_per_page;
        $this->offset = ($page_no-1) * $no_of_records_per_page;
        $data = $this->get();
        global $paginate_info;
        $paginate_info = array('current_page'=>$page_no,'total_pages'=>$this->totalPages);
        return $data;
    }

}
