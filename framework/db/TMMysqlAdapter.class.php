<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2012 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&D Team, Advertising Platform & Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */

/**
 * The Mysql database connection class
 *
 * @package sdk.src.framework.db
 */
class TMMysqlAdapter
{
    /**
     * The mysql link identifier.
     * @var resource
     */
    private $connection;

    /**
     * Affected rows number.
     * @var int
     */
    private $affectedRowNum;

    /**
     * Id of the row just inserted.
     * @var int
     */
    private $insertId;

    /**
     * The array operators.
     * @var array
     */
    private static $arr_operator = array ('+', '-', '*', '/', '%' );

    /**
     * The comparisons array.
     * @var array
     */
    private static $_comparisons = array(
                  'eq'     => '=',
                  'in'     => 'IN',
                  'neq'    => '!=',
                  'gt'     => '>',
                  'egt'    => '>=',
                  'lt'     => '<',
                  'elt'    => '<=',
                  'like'   => 'LIKE',
                  'notnull'=> 'IS NOT NULL',
                  '='     => '=',
                  '!='    => '!=',
                  '>'     => '>',
                  '>='    => '>=',
                  '<'     => '<',
                  '<='    => '<=',
                  'IS NOT NULL'=> 'IS NOT NULL'
    );
    /**
     * The string columns array.
     * @var array
     */
    private $arrayStringColumn = array();

    /**
     * Allow reconnect or not.
     * @var boolean
     */
    protected static $allowReconnect = false;

    /**
     * DB host.
     * @var string
     */
    protected $dbhost;

    /**
     * DB user name.
     * @var string
     */
    protected $dbuser;

    /**
     * DB password.
     * @var string
     */
    protected $dbpasswd;

    /**
     * DB name.
     * @var string
     */
    protected $database;

    /**
     * DB port.
     * @var string
     */
    protected $port;

    /**
     * Auto commit or not.
     * @var boolean
     */
    protected $autoCommit;

    /**
     * encoder
     * @var string
     */
    protected $code;

    /**
     * 设置是否允许mysql进行自动重连
     * @param boolean $allowReconnect
     */
    public static function setAllowReconnect($allowReconnect)
    {
        self::$allowReconnect = $allowReconnect;
    }

    /**
     * Initialize the Mysql db connection, set the commit as manual, and set the default encode as UTF-8
     *
     * @param string $dbhost      host name
     * @param string $dbuser        login user
     * @param string $dbpasswd       login password
     * @param string $database        used database
     * @param int $port               db port
     * @param boolean $autoCommit
     * @param string $code
     * @param array $arrayStringColumn
     *
     * @throws TMMysqlException
     */
    public function __construct($dbhost, $dbuser, $dbpasswd, $database, $port= 3306, $autoCommit=TRUE,$code='UTF8',$arrayStringColumn=array())
    {
        $this->arrayStringColumn = $arrayStringColumn;

        if(empty($port))
        {
            $port = 3306;
        }

        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpasswd = $dbpasswd;
        $this->database = $database;
        $this->port = $port;
        $this->autoCommit = $autoCommit;
        $this->code = $code;

        $this->openConnection($dbhost, $dbuser, $dbpasswd, $database, $port, $autoCommit, $code);
    }

    /**
     * Close the Mysql db connection
     *
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * 打开连接
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpasswd
     * @param string $database
     * @param int $port
     * @param boolen $autoCommit
     * @param string $code
     */
    public function openConnection($dbhost, $dbuser, $dbpasswd, $database, $port = 3306, $autoCommit=TRUE,$code='UTF8')
    {
        if(empty($port))
        {
            $port = 3306;
        }
        $this->connection = mysqli_connect ( $dbhost, $dbuser, $dbpasswd, $database , $port);
        if (! $this->connection)
        {
            throw new TMMysqlConnectionException
            ("Can't connect the database ".$database." in ".$dbuser."@".$dbhost." ".mysqli_connect_error($this->connection));
        }

        mysqli_query ( $this->connection, "SET NAMES '".$code."'" );
        mysqli_autocommit ( $this->connection, $autoCommit );
    }

    /**
     * 主动关闭数据库连接
     */
    public function closeConnection()
    {
        if($this->connection != null){
            mysqli_close ( $this->connection );
            $this->connection = null;
        }
    }

    /**
     * Execute the database query
     *
     * @param  string $sql     sql command
     * @param  int $resultType return type
     * @return TMMysqlResult object
     */
    public function query($sql, $resultType = MYSQLI_BOTH)
    {
        if(empty($this->connection))
        {
            throw new TMMysqlConnectionException("Not connect database");
        }

        if(self::$allowReconnect && !mysqli_ping($this->connection))
        {
            $this->openConnection($this->dbhost, $this->dbuser, $this->dbpasswd, $this->database, $this->port, $this->autoCommit, $this->code);
        }

        $result = mysqli_query ($this->connection, $sql);

        if ($result === false)
        {
            throw new TMMysqlException($sql.": Query String error." . mysqli_error($this->connection));
        }

        $affectedRowNum = mysqli_affected_rows($this->connection);
        $insertId = mysqli_insert_id($this->connection);

        $this->affectedRowNum = $affectedRowNum;
        $this->insertId = $insertId;
        //TMHook::call("after_sql_execute", array($sql));
        return new TMMysqlResult ($result,$resultType);
    }


    /**
     * return the result querystring of parse array
     *
     * @param  mixed $conditions    array which stores the "where" clause, as array('eq' => array('FQQ'=>'123456'))
     * @param  string $selectFields  'FQQ,FScore'
     * @param  string $table           the table include join
     * @param  array $limitArray    the limit offset, count. For example, array(0,2). start = 0, offset =2;
     * @param  array $otherArray    the other conditions includes group by, order by
     *                                                  $otherArray['orderby'],$otherArray['groupby'],$otherArray['having']
     * @return string $query        the query string
     *
     */
    public function makeSQLString($conditions,$selectFields,$table,$limitArray = null,$otherArray = null)
    {
        $query  = "select " . $selectFields . " from " . $table;
        $query  .= " where " . $this->parseWhere($conditions) . $this->parseOthers($otherArray) . $this->parseCount($limitArray);
        return $query;
    }

    /**
     * parseCount
     * @desc    parse  limit
     * @param   array  $countArray
     * @return  string $query
     */
    protected function parseCount($countArray)
    {
        $query = '';
        if (is_array($countArray))
        {
            $query .= ' limit ' . $countArray[0] . ' , ' . $countArray[1];
        }
        else
        {
            $query .= '';
        }
        return $query;
    }

    /**
     * parseOthers
     * @desc     parse others include group by, order by, having and more ...
     * @param    array $otherArray
     * @return   string $query
     */
    protected function parseOthers($otherArray)
    {
        $query = '';
        if (is_array($otherArray))
        {
            if (!empty($otherArray['groupby']))
            {
                $query .= ' group by ' . $otherArray['groupby'];
            }
            if (!empty($otherArray['orderby']))
            {
                $query .= ' order by ' . $otherArray['orderby'];
            }
            if (!empty($otherArray['having']))
            {
                $query .= ' having ' . $otherArray['having'];
            }
        }
        else
        {
            $query .= '';
        }
        return $query;
    }

    /**
     * 将传入参数转变为字符串
     * 支持一维数组
     * @param unknown_type|array $value
     * @return string|array
     */
    protected static function fieldToString($value) {
        if(is_array($value)) {
            $newValue = array();
            foreach($value as $index => $v) {
                $newValue[] = strval($v);
            }
            return $newValue;
        }
        else {
            return strval($value);
        }
    }

    /**
     * parseWhere
     * @desc     parse where conditions
     * @param    array  $fields - format as array("eq"=>array("fieldname"=>value))
     *                     "eq" means equal, you could find the string definition at the beginning of this file
     * @return   string $query
     */
    protected function parseWhere($fields)
    {
        $arrayStringColumn =& $this->arrayStringColumn;
        $query = "1 and ";
        if (is_array($fields))
        {
            foreach ( $fields as $key => $fieldsons )
            {
                if(1)//$fieldsons != null
                {
                    if(!is_array($fieldsons))
                    {
                        $keyson = $key;
                        $field = $fieldsons;
                        $comparisonKey = "eq";

                        /*if (is_numeric($field))
                        {
                            $field = intval($field);
                        } */
                        if (!empty($arrayStringColumn))
                        {
                            if (in_array($keyson, $arrayStringColumn))
                            {
                                $field = self::fieldToString($field);
                            }
                        }

                        if(is_array($field)) {
                            $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." " . $this->formatArrayIn ( $field ) . " and ";
                        }
                        elseif (is_string($field))
                        {
                            $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." '" . $this->formatstring ( $field ) . "' and ";
                        }
                        else if($field === NULL)
                        {
                            $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." null and ";
                        }
                        else
                        {
                            $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." " .  $field." and ";
                        }
                    }
                    else
                    {
                        foreach ((array)$fieldsons as $keyson => $field)
                        {

                            /*if (is_numeric($field))
                            {
                                $field = intval($field);
                            }*/
                            if (!empty($arrayStringColumn))
                            {
                                if(in_array($keyson,$arrayStringColumn))
                                {
                                    $field = self::fieldToString($field);
                                }
                            }

                            $comparisonKey = $key;
                            if(is_array($field)) {
                                $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." " . $this->formatArrayIn ( $field ) . " and ";
                            }
                            else if (is_string($field))
                            {
                                $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." '" . $this->formatstring ( $field ) . "' and ";
                            }
                            else if($field === NULL)
                            {
                                if(self::$_comparisons[$comparisonKey] == '=') {
                                    $query .= $keyson . " is null and ";
                                }
                                elseif(self::$_comparisons[$comparisonKey] == '!=') {
                                    $query .= $keyson . " is not null and ";
                                }
                                else {
                                    throw new TMMysqlException("Make Query Failed: the value of $keyson is null, and $keyson $comparisonKey null is not supported, ");
                                }
                            }
                            else
                            {
                                $query .= $keyson . " ".self::$_comparisons[$comparisonKey]." " .  $field." and ";
                            }
                        }
                    }
                }
            }
            $query = rtrim($query, "and ");
        }
        else
        {
            $query = '';
        }
        return $query;
    }

    /**
     * Add data row for application
     *
     * @param  array $array            the insert parameter array,
     *                                 example: array("FQQ" => '10000', "FUserId" => 2)
     * @param  string $table           the table name
     * @param  boolean $delayed        if True, Add DELAYED in SQL. esp. True when the data DO NOT need to wait the returning, e.g. adding score
     *
     * @throws TMMysqlException
    */
    public function doInsert(array $array, $table, $delayed=false)
    {
        $formated_array = $this->compile_insert_string ( $array );
        $delaySql = "";
        if($delayed === true) {
            $delaySql = " DELAYED ";
        }
        $queryString = 'INSERT '.$delaySql.' INTO ' . $table . ' (' . $formated_array ['FIELD_NAMES'] . ') VALUES(' . $formated_array ['FIELD_VALUES'] . ')';

        $result = $this->query($queryString );
        if (!$result->isSuccess())
        {
            throw new TMMysqlException($queryString.": Insert Failed, " . mysqli_error($this->connection));
        }
        return $result->isSuccess();
    }

    /**
     * Update data
     *
     * @param  string $table the table name
     * @param  array $array the update set array
     * @param  string $where the update where string
     *
     * @throws TMMysqlException
     */
    public function doUpdate($table, $array, $where = '')
    {
        $string = $this->compile_update_string ( $array );
        $sql = 'UPDATE ' . $table . ' SET ' . $string;

        if (is_array($where) && !empty($where))
        {
            $sql .= ' WHERE ' . $this->parseWhere($where);
        }
        else if ($where)
        {
            $sql .= ' WHERE ' . $where;
        }
        $result = $this->query($sql );
        if (!$result->isSuccess())
        {
            throw new TMMysqlException($sql.": Update Failed");
        }
    }

    /**
     * Update data
     *
     * @param  string $table           the table name
     * @param  array  $array          the update set array
     * @param  array  $conditionArray  the condition set Array
     *
     * @throws TMMysqlException
     */
    public function doUpdateX($table, $array, $conditionArray)
    {
        $string = $this->compile_update_string ( $array );
        $sql = 'UPDATE ' . $table . ' SET ' . $string;

        if (is_array($conditionArray) && !empty($conditionArray))
        {
            $sql .= ' WHERE 1' . $this->parseWhere($conditionArray);
        }

        $result = $this->query($sql);
        if (!$result->isSuccess())
        {
            throw new TMMysqlException($sql.": Update Failed!");
        }
    }

    /**
     * Numeric operation in the numeric column and update set operation
     *
     * @param string $table         the table name
     * @param array $arrColOp     the column numeric operation,
     *                              example: array("FScore" => "+1"),
     *                                       array("FVoteCount" => "*5")
     * @param string $where          the where string
     * @param array $arrColSet    the column update set,
     *                              example: array("FQQ" => '10001', "FCity" => "shanghai")
     *
     * @return boolean true
     * @throws TMMysqlException
     */
    public function operate($table, $arrColOp, $where = null, $arrColSet=array())
    {
        if (! is_array ( $arrColOp ) || empty ( $arrColOp ))
        {
            throw new TMMysqlException('operation param must be not empty array');
        }

        $arr_str_op = array ();
        foreach ( $arrColOp as $col => $str )
        {

            $str = preg_replace ( '/\s+/', '', $str );

            $operator = $str [0];
            if (!in_array($operator, self::$arr_operator))
            {
                throw new TMMysqlException($operator . ' operator for ' . $col . ' error');
            }

            $value = substr ( $str, 1 );
            if (! is_numeric ( $value ))
            {
                throw new TMMysqlException($value . ' operation value for ' . $col . ' must be number');
            }
            $arr_str_op [] = $col . '=' . $col . $operator . $value;
        }

        $list_str_op = join ( ',', $arr_str_op );

        if (! empty ( $arrColSet ) && is_array ( $arrColSet ))
        {
            $str_col_set = $this->compile_update_string ( $arrColSet );
            $list_str_op .= ',' . $str_col_set;
        }

        //produce where sql in update set operation
        $sql = 'UPDATE ' . $table . ' SET ' . $list_str_op;
        if ($where) {
            $sql .= ' WHERE ' . $where;
        }

        //query
        $result = $this->query($sql);
        if (! $result->isSuccess())
        {
            throw new TMMysqlException($sql.": Update State Failed");
        }
        return true;
    }

    /**
     * 开始事务
     *
     */
    public function startTransaction()
    {
        mysqli_query ($this->connection, "start transaction");
    }


    /**
     * mysqli_commit
     *
     * @throws TMMysqlException
     */
    public function commit()
    {
        if (! mysqli_commit ( $this->connection ))
        {
            throw new TMMysqlException("commit error:" . mysqli_error($this->connection));
        }
    }

    /**
     * mysqli_rollback
     *
     * @throws TMMysqlException
     */
    public function rollback()
    {
        if (! mysqli_rollback ( $this->connection ))
        {
            throw new TMMysqlException("rollback error:" . mysqli_error($this->connection));
        }
    }

    /**
     * You can get the reference from mysqli_affectedRow in PHP manual.
     *
     * @return affected row number  integer
     */
    public function getAffectedRowNum()
    {
        return $this->affectedRowNum;
    }

    /**
     * get the last AUTO_INCREMENT ID
     *
     * @return insertId
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * mysqli_real_escape_string
     * @param string $sql sql string
     */
    private function filterSQL($sql)
    {
        return mysqli_real_escape_string ( $sql );
    }

    /**
     * format  insert set
     *
     * @param array $data
     * @return formated $data
     */
    private function compile_insert_string($data)
    {
        $field_names = '';
        $field_values = '';

        foreach ( $data as $k => $v )
        {
            $field_names .= "`{$k}`,";

            if (is_string($v))
            {
                $field_values .= "'" . $this->formatString ( $v ) . "',";
            }
            else
            {
                $field_values .= $v . ",";
            }
        }

        $field_names = preg_replace ( "/,$/", "", $field_names );
        $field_values = preg_replace ( "/,$/", "", $field_values );

        return array ('FIELD_NAMES' => $field_names, 'FIELD_VALUES' => $field_values );
    }

    /**
     * format update set
     *
     * @param array $data
     * @return formated $data
     */
    private function compile_update_string($data)
    {
        $return = '';
        foreach ( $data as $k => $v )
        {
            if (is_string($v))
            {
                $return .= "`$k`='" . $this->formatString ( $v ) . "',";
            }
            else
            {
                $return .= "`$k`=" . $v . ",";
            }
        }
        $return = preg_replace ( "/,$/", "", $return );

        return $return;
    }

    /**
     * 将数组一个数组进行数据格式化
     *
     * @param array $arr
     * @return array
     */
    public function formatArrayIn($arr) {
        $str = '(';
        foreach($arr as $index => $v) {
            if(is_string($v)) {
                $str .= "'".$this->formatString($v)."',";
            }
            else {
                $str .= "".$this->formatString($v).",";
            }
        }
        $str = rtrim($str, ',');
        $str .= ')';
        return $str;
    }

    /**
     * Escapes special characters in a string for use in a SQL statement
     *
     * @param string $str    the query sql string
     * @return string $str   the formatted string
     */
    public function formatString($str)
    {
        if (get_magic_quotes_gpc ())
        {
            $str = stripslashes ( $str );

        }
        if (! is_numeric ( $str ))
        {

            $str = mysqli_real_escape_string ( $this->connection, $str );
        }
        return $str;
    }

    /**
     * 设置字符串处理模式数组
     *
     * @param array $arrayStringColumn
     */
    public function setArrayStringColumn($arrayStringColumn) {
        $this->arrayStringColumn = $arrayStringColumn;
    }

    /**
     * 获得当前mysql连接的错误号
     * @return string
     */
    public function errno() {
        return mysqli_errno($this->connection);
    }

    /**
     * 获得当前mysql连接的错误信息
     * @return string
     */
    public function error() {
        return mysqli_error($this->connection);
    }

    /**
     *
     * 获取DB连接相关属性
     */
    public function getDBConnectionAttr()
    {
        return array(
            "db_ip" => $this->dbhost,
            "db_user" => $this->dbuser,
            "db_name" => $this->database,
            "db_port" => $this->port,
            "autoCommit" => $this->autoCommit,
            "code" => $this->code
        );
    }
}
