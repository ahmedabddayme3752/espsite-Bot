<?php
// Prevent direct access to this file
if (!defined('ABSPATH')) {
    die('Direct access not allowed');
}

// Override the default wpdb class
if (!class_exists('wpdb')) {
    define('OBJECT', 'OBJECT');
    define('OBJECT_K', 'OBJECT_K');
    define('ARRAY_A', 'ARRAY_A');
    define('ARRAY_N', 'ARRAY_N');

    class wpdb {
        public $prefix = 'wp_';
        public $dbh;
        public $rows_affected = 0;
        public $insert_id = 0;
        public $last_error = '';
        public $last_query;
        public $col_info;
        public $num_rows = 0;
        public $result;

        public function __construct() {
            register_shutdown_function(array($this, '__destruct'));

            if (defined('DB_USER')) {
                $this->user = DB_USER;
                $this->password = DB_PASSWORD;
                $this->dbname = DB_NAME;
                $this->host = DB_HOST;
            }

            $this->db_connect();
        }

        public function __destruct() {
            if ($this->dbh) {
                $this->dbh = null;
            }
        }

        private function db_connect() {
            try {
                $dsn = "pgsql:host=" . $this->host . ";dbname=" . $this->dbname;
                $this->dbh = new PDO($dsn, $this->user, $this->password);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return true;
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                $this->last_error = $e->getMessage();
                return false;
            }
        }

        public function query($query) {
            if (!$this->dbh) {
                if (!$this->db_connect()) {
                    return false;
                }
            }

            try {
                $this->result = $this->dbh->query($query);
                $this->last_query = $query;
                $this->rows_affected = $this->result->rowCount();
                $this->num_rows = $this->result->rowCount();
                return $this->result;
            } catch (PDOException $e) {
                error_log("Query error: " . $e->getMessage() . " Query: " . $query);
                $this->last_error = $e->getMessage();
                return false;
            }
        }

        public function get_results($query = null, $output = OBJECT) {
            if ($query) {
                $this->query($query);
            }

            if (!$this->result) {
                return array();
            }

            $results = $this->result->fetchAll(PDO::FETCH_OBJ);
            return $results;
        }

        public function get_row($query = null, $output = OBJECT, $y = 0) {
            if ($query) {
                $this->query($query);
            }

            if (!$this->result) {
                return null;
            }

            return $this->result->fetch(PDO::FETCH_OBJ);
        }

        public function get_var($query = null, $x = 0, $y = 0) {
            if ($query) {
                $this->query($query);
            }

            if (!$this->result) {
                return null;
            }

            $row = $this->result->fetch(PDO::FETCH_NUM);
            return $row[$x] ?? null;
        }

        public function prepare($query, $args) {
            if (is_null($query)) {
                return;
            }

            $query = str_replace("'%s'", '%s', $query);
            $query = str_replace('"%s"', '%s', $query);
            $query = preg_replace('|(?<!%)%s|', "'%s'", $query);

            array_walk($args, function(&$value) {
                if (is_null($value)) {
                    $value = 'NULL';
                }
            });

            return @vsprintf($query, $args);
        }

        public function escape($string) {
            return addslashes($string);
        }
    }

    global $wpdb;
    $wpdb = new wpdb();
}
