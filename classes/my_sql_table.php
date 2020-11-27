<?php
class MySqlTable {
    private $g_name = null,
            $g_cols = null,
            $g_data = null,
            $g_engine = 'InnoDB',
            $g_charset = 'latin1',
            $g_db = null,
            $g_existed = false,
            $g_mysql = null;

    public function getMySql() {
        if ( is_null ( $this->g_mysql ) ) {
            $this->g_mysql = new MySql();
        }
        return $this->g_mysql;
    }

    public function getName ( $incl_db = false ) {
        return $this->g_name;
    }

    public function setName ( $value ) {
        ! empty ( $value ) && $this->g_name = $value;
        return $this;
    }

    public function addColumn ( $name, $type_id, $len = null, $def = null, $allow_null = false, $index_type_id = null, $auto_increment = false, $unsigned = false ) {
        if ( empty ( $name ) ) {
            return $this;
        }
        $this->g_cols[] = [
            'name' => $name,
            'type_id' => $type_id,
            'len' => $len,
            'def' => $def,
            'allow_null' => $allow_null,
            'index_type_id' => $index_type_id,
            'auto_increment' => $auto_increment,
            'unsigned' => $unsigned
        ];
        return $this;
    }

    public function setEngine ( $value ) {
        ! empty ( $value ) && $this->g_engine = $value;
        return $this;
    }

    public function setCharset ( $value ) {
        !empty( $value) && $this->g_charset = $value;
        return $this;
    }

    public function getExisted() {
        return $this->g_existed;
    }

    public function __construct ( $name = null, $engine = null, $charset = null, $db = null ) {
        isset ( $name) && $this->setName ( $name );
        isset ( $engine) && $this->setEngine ( $engine );
        isset ( $charset) && $this->setCharset ( $charset );
        $this->getMySql();
    }

    public function handle() {
        false !== $this->g_existed && $this->g_existed = false;
        if ( ! isset ( $this->g_name) ) {
            return false;
        }
        if ( $this->g_existed = $this->getMySql()->tblExists( $this->g_name, /*$tbl_type = */'BASE TABLE', $this->g_db) ) {
            return $this->handleCol();
        }
        return $this->handleCreate();
    }

    private function handleCreate() {
        if ( empty ( $this->g_cols ) ) {
            return false;
        }
        $values = [];
        foreach ( $this->g_cols as $col ) {
            if ( !$this->deterQryCol ( $col, $value, false ) ) {
                return false;
            }
            $values[] = $value;
        }
        if ( empty( $values) ) {
            return false;
        }
        if ( ! $this->handleIndex( null, false, $qry_index ) ) {
            return false;
        }
        Convert::concat( $qry, [
            "create table {$this->getName()} (",
                implode (', ', $values), ', ',
                $qry_index,
            " ) engine = {$this->g_engine} default charset = {$this->g_charset};"
        ] );
        return $this->getMySql()->getQueryResult ( $qry, $insert_id, true );
    }

    private function handleIndex( $type_id = null, $alter = true, &$qry = '' ) {
        '' !== $qry && $qry = '';
        $values = [];
        $types = [
            ['type_id' => EnumMySqlIndexType::primary, 'desc' => 'primary key'],
            ['type_id' => EnumMySqlIndexType::uniq, 'desc' => 'unique key', 'name' => 'uniq'],
            ['type_id' => EnumMySqlIndexType::index, 'desc' => 'key', 'name' => 'index']
        ];
        if ( isset ( $type_id) ) {
            !is_array ( $type_id) && $type_id = [$type_id];
            $types = array_filter( $types, function( $a) use ( $type_id ) {
                return in_array ( $a['type_id'], $type_id );
            } );
        }
        foreach ( $types as $type ) {
            $cols = array_filter( $this->g_cols, function( $a) use ( $type ) {
                return $a['index_type_id'] === $type['type_id'];
            } );
            if ( empty( $cols) ) {
                continue;
            }
            if ( $alter && ! isset ( $type['name'] ) ) {
                continue;
            }
            if ( isset ( $type['name'] ) && $this->getMySql()->indexExists( $this->getName(/*$incl_db = */true), $type['name'] ) ) {
                $qry = "alter table {$this->getName(/*$incl_db = */true)} drop index `{$type['name']}`;";
                if ( ! $this->getMySql()->getQueryResult ( $qry, $insert_id, true) ) {
                    return false;
                }
            }
            $cols = array_map(function( $a ) {
                return "`{$a['name']}`";
            }, $cols );
            
            $add_desc = ( $alter ? 'add ' : '' ) . $type['desc'];
            if ( isset ( $type['name'] ) ) {
                $add_desc .= " `{$type['name']}`";
            }
            $values[] = $add_desc . ' (' . implode (',', $cols) . ' )';
        }
        if ( empty( $values) ) { return false; }
        
        if ( $alter ) {
            Convert::concat( $qry, [
                "alter table {$this->getName(/*$incl_db = */true)} ",
                implode (', ', $values), ';'
            ], false, true );
            return $this->getMySql()->getQueryResult ( $qry, $insert_id, true );
        }
        $qry = implode (', ', $values );
        return '' !== $qry;
    }

    private function handleCol() {
        if ( ! isset ( $this->g_cols) ) {
            return false;
        }
        $count = count( $this->g_cols );
        $index_type_id_chgs = [];
        for ( $i = 0; $i < $count; $i++ ) {
            $col = $this->g_cols[$i];
            $exists = $this->getMySql()->colExists( $this->g_name, $col['name'], $this->g_db );
            if ( ! $this->deterQryCol( $col, $qry, true, $i ? $this->g_cols[$i - 1]['name'] : null, $exists ) ) {
                return false;
            }
            if ( ! $this->getMySql()->getQueryResult ( $qry, $insert_id, true) ) {
                return false;
            }
            if ( ! isset ( $col['index_type_id'] ) ) {
                continue;
            }
            if ( ! in_array ( $col['index_type_id'], $index_type_id_chgs) ) {
                $index_type_id_chgs[] = $col['index_type_id'];
            }
        }
        return (
            !empty( $index_type_id_chgs)
            ? $this->handleIndex( $index_type_id_chgs)
            : true
        );
    }

    private function deterQryCol( $col, &$value, $alter = true, $col_bef_name = null, $exists = false ) {
        isset ( $value) && $value = null;
        if ( ! $this->deterColTypeDesc( $col, $desc) ) {
            return false;
        }
        if ( $alter ) {
            if ( ! $exists && empty( $col_bef_name ) ) {
                return false;
            }
            Convert::concat( $value, [
                "alter table {$this->getName(/*$incl_db = */true)} " . ( $exists ? "change {$col['name']}" : 'add') . " {$col['name']} {$desc} ",
                ( ! $col['unsigned'] ? '' : 'unsigned' ), ' ',
                ( ! $col['allow_null'] ? 'not null'  . ( ! isset ( $col['def'] ) ? '' : " default '{$col['def']}'" ) : 'null' ), ' ',
                ( ! $col['auto_increment'] ? '' : 'auto_increment' ), ' ',
                ( $exists ? "" : "after {$col_bef_name};" )
            ] );
        } else {
            Convert::concat( $value, [
                "{$col['name']} {$desc} ",
                ( ! $col['unsigned'] ? '' : 'unsigned' ), ' ',
                ( ! $col['allow_null'] ? 'not null'  . ( ! isset ( $col['def'] ) ? '' : " default '{$col['def']}'" ) : 'null' ),
                ( ! $col['auto_increment'] ? '' : ' auto_increment' )
            ] );
        }
        return true;
    }

    public function deterColTypeDesc( $col, &$value ) {
        isset ( $value) && $value = null;
        switch ( $col['type_id'] ) {
            case EnumMySqlColType::tinyint:
                $value = 'tinyint';
                break;
            case EnumMySqlColType::smallint:
                $value = 'smallint';
                break;
            case EnumMySqlColType::mediumint:
                $value = 'mediumint';
                break;
            case EnumMySqlColType::_int:
                $value = 'int';
                break;
            case EnumMySqlColType::bigint:
                $value = 'bigint';
                break;
            case EnumMySqlColType::decimal:
                $value = 'decimal';
                break;
            case EnumMySqlColType::char:
                $value = 'char';
                break;
            case EnumMySqlColType::varchar:
                $value = 'varchar';
                break;
            case EnumMySqlColType::tinytext:
                $value = 'tinytext';
                break;
            case EnumMySqlColType::text:
                $value = 'text';
                break;
            case EnumMySqlColType::mediumtext:
                $value = 'mediumtext';
                break;
            case EnumMySqlColType::longtext:
                $value = 'longtext';
                break;
            case EnumMySqlColType::date:
                $value = 'date';
                break;
            case EnumMySqlColType::time:
                $value = 'time';
                break;
            case EnumMySqlColType::date_time:
                $value = 'datetime';
                break;
        }
        if ( ! empty( $value) ) {
            if ( isset ( $col['len'] ) ) {
                $value .= "({$col['len']})";
            }
            return true;
        }
        return false;
    }

    public function updData() {
        if ( empty( $this->g_data ) ) {
            return true;
        }
        foreach ( $this->g_data as $data ) {
            $col_n_vals = ["name = '{$data['name']}'"];
            $qry_cols = ['name'];
            foreach ( $this->getCols( false ) as $col ) {
                if ( isset ( $data[ $col ] ) ) {
                    $col_n_vals[] = "{$col} = '{$data[$col]}'";
                    $qry_cols[] = $col;
                }
            }
            $qry = "select
                            uuid
                            " . ( ! empty( $qry_cols) ? ', ' . implode (', ', $qry_cols) : '' ) . "
                        from {$this->getName(/*$incl_db = */true)}
                        where ( Enum_id = {$data['enum_id']} );";
            $result = $this->getMySql()->getQueryResult ( $qry );
            if ( $row = $result->fetch_assoc() ) {
                $result->free_result();
                if ( $this->compData( $row, $data) ) {
                    continue;
                }
                $qry = "update {$this->getName()}
                            set " . implode (', ', $col_n_vals) . "
                            where (uuid = '{$row['uuid']}' );";
            } else {
                $col_n_vals[] = 'uuid = uuid()';
                $col_n_vals[] = "enum_id = '{$data['enum_id']}'";
                $qry = "insert into {$this->getName()}
                            set " . implode (', ', $col_n_vals ) . ";";
            }
            if ( ! $this->getMySql()->getQueryResult ( $qry ) ) {
                return false;
            }
        }
        return true;
    }

    private function compData ( $data_curr, $data_new ) {
        foreach ( $this->getCols( true ) as $col ) {
            if ( ! isset ( $data_new[ $col ] ) ) {
                continue;
            }
            if ( ! isset ( $data_curr[ $col ] ) ) {
                return false;
            }
            if ( $data_curr[$col] != $data_new[ $col ] ) {
                return false;
            }
        }
        return true;
    }
}