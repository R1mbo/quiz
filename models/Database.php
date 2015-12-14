<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author Drago
 */
Class Database {
    private static $instance = null;
    private $db_connection,
        $_query,
        $_error = false,
        $_results,
        $_result,
        $_count = 0;

    private function __construct() {

        //database Constants

        require_once('config.php');
        $this->open_connection();
    }

        public function open_connection() {
            try {
                $this->db_connection = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $this->db_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            } catch (PDOException $e) {
                die($e->getMessage());

            }

        }
        public function close_connection() {
            if (isset($this->db_connection)) {
                $this->db_connection = NULL;
                unset($this->db_connection);

            }

        }
        public function query($sql, array $params) {
            //Reset error to false to make sure
            //in case of multiple queries I am not
            //returning an error for a previous failed
            //query.
            $this->_error = false;
            //Check if query has been successfully prepared
            //Assign _query to _pdo and check if sql is successful
            if ($this->_query = $this->db_connection->prepare($sql)) {
                //check if there is something in the param array
                //why use count vs !empty though ???.
                $pos = 1;


                if (!empty($params)) {
                    foreach ($params as $param) {
                        $this->_query->bindValue($pos, $param);
                        $pos++;

                    }
                }
                if ($this->_query->execute()) {
                    $this->_count = $this->_query->rowCount();

                    if($this->_count === 1){
		    $this->_result = $this->_query->fetch(PDO::FETCH_OBJ);
                    }
                    if($this->_count > 1){
                    $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                    }

                } else {
                    $this->_error = true;

                }

            } 
            return $this;

        }
        //This method is used for in the get and delete methods.
        //Insert and update follow their own logic.
        private function action($action, $table, array $where) {
            //(count($where) === 3) because WHERE will contain field, operator, value
            //Example "username = alex", "id > 5"
            //"username" is [0], "=" is [1], "alex" is [2]
            if (count($where) === 3) {
                $operators = array("=", ">", "<", ">=", "=<");
                $field = $where[0];
                $operator = $where[1];
                $value = $where[2];
                if (in_array($operator, $operators)) {
                    $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                    //If there is no error return this
                    if (!$this->query($sql, array($value))->error()) {
                        return $this;

                    }

                }
            }
            //TODO - Refactor the code that adds support for "AND" in a "SELECT" query.
            //See if it can be integrated with a loop and avoid code duplication.
            if (count($where) > 3){
                $operators = array("=", ">", "<", ">=", "=<");
                $field = $where[0];
                $operator = $where[1];
                $value = $where[2];
                //-----------------
                $field2 = $where[3];
                $operator2 = $where[4];
                $value2 = $where[5];

                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? AND {$field2} {$operator2} ?";
                if (!$this->query($sql, array($value, $value2))->error()) {
                    return $this;

                }
            }

            if (count($where) === 0) {
                $sql = "{$action} FROM {$table}";
                if (!$this->query($sql, array())->error()) {
                    return $this;

                }

            }
            return false;

        }
        public function get($table, $where) {
            return $this->action("SELECT *", $table, $where);

        }

        public function get_single($table, $coloumn, $where) {
            return $this->action("SELECT {$coloumn}", $table, $where );
        }

        public function delete($table, $where) {
            return $this->action("DELETE", $table);

        }
        public function insert($table, array $fields) {
            if (count($fields)) {
                $table_coloumns = array_keys($fields);
                $value = "";
                //Count the items of an array and later append commas
                //to complete an sql stmt. The counter is used to ensure
                //no comma is appended to the last value.
                $counter = 0;
                foreach ($fields as $field_value) {
                    $value .= "?";
                    $counter++;
                    if ($counter < count($table_coloumns)) {
                        $value .= ", ";

                    }

                }
                $sql = "INSERT INTO {$table} (`" . implode("`, `", $table_coloumns) . "`) VALUES ({$value});";
                if (!$this->query($sql, $fields)->error()) {
                    return true;

                }

            }
            return false;

        }
        public function update($table, array $coloumns, array $where) {
            $table_coloumns = array_keys($coloumns);
            foreach ($where as $update => $update_where) {


            }
            $set = "";
            $counter = 0;
            foreach ($coloumns as $coloumn => $value) {
                $set .= "$coloumn = ?";
                $counter++;
                if ($counter < count($table_coloumns)) {
                    $set .= ", ";

                }

            }

            $sql = "UPDATE {$table} SET {$set} WHERE {$update} = '$update_where'";
            if (!$this->query($sql, $coloumns)->error()) {
                return true;

            }
            return false;

        }
        public function results() {
            return $this->_results;

        }

        public function result() {
            return $this->_result;
        }

        public function count() {
            return $this->_count;

        }
        public function error() {
            return $this->_error;

        }
        //Singleton->ensure only one isntance of a given class is running
        public static function getInstance() {
            if (!isset(self::$instance)) {
                self::$instance = new Database();

            }
            return self::$instance;

        }
        public function __destruct() {
            $this->close_connection();

        }
        }
