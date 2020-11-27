<?php
    class BaseMgr {
        public  $g_resultset = null,
                $g_gen = null,
                $g_mysql = null,
                $g_entity_name = "",
                $g_retrieve_query = "";

        public function getMySql() {
            if ( is_null ( $this->g_mysql ) ) {
                $this->g_mysql = new MySql();
            }
            return $this->g_mysql;
        }

        public function getGen() {
            if ( is_null ( $this->g_gen ) ) {
                $this->g_gen = new GeneralDisplay ( null, null );
            }
            return $this->g_gen;
        }

        public function getRecordPageTitle() {
            return "Manage $this->g_entity_name";
        }

        public function getRecords() {
            return $this->getMySql()->getQryRlt ( $this->g_retrieve_query ); 
        }
    }