<?php
set_time_limit( 0 );
echo '***MySQL update started.***</br>';
echo 'Column renaming ' . ( handleAllColumnRename() ? '' : 'not' ) . ' completed.</br>';
echo 'Column deletion ' . ( handleAllColumnDelete() ? '' : 'not' ) . ' completed.</br>';
echo 'Table deletion ' . ( handleTableDelete() ? '' : 'not' ) . ' completed.</br>';
echo 'Table structure ' . ( handleAllTableStructure() ? '' : 'not' ) . ' created/updated.</br>';
echo 'Look up data ' . ( handleAllLookupData() ? '' : 'not' ) . ' created/updated.</br>';
echo 'Navigation ' . ( handleAllNavigation() ? '' : 'not' ) . ' created/updated.</br>';
echo 'Other script(s) ' . ( handleOtherScript() ? '' : 'not' ) . ' completed.</br>';
echo '***MySQL update completed.***</br>';
return;

function handleNavigation ( $name, $controller, $action, $sequence, $user_type_id, &$uuid = '' ) {
  $mysql = new MySql();
  $uuid = $mysql->getUuid();
  $qry = "insert into tbl_lu_nav
          set uuid = '{$uuid}',
          name = '" . Convert::toString ( $name, true ) . "',
          sequence = {$sequence},
          user_type_id = {$user_type_id},
          controller = '" . Convert::toString ( $controller, true ) . "',
          action = '" . Convert::toString ( $action, true ) . "';";
  return $mysql->getQueryResult ( $qry );
}

function handleLookupData ( $table, $enum_id, $name, $other_col = null ) {
  $mysql = new MySql();
  $suffix_qry = '';
  if ( $other_col ) {
      $suffix_qry .= ', ' . $other_col['key'] . '=' . "'" . $other_col['value'] . "'";
  }
  $qry = "select *
              from {$table}
              where ( enum_id = {$enum_id} );";
  $result = $mysql->getQueryResult ( $qry );
  if ( ! $result || ! $mysql->getQueryResult ( $qry )->num_rows ) {
      $qry = "insert into {$table}
                    set name = '{$name}',
                    enum_id = {$enum_id} ";
      $qry .= $suffix_qry;
      return $mysql->getQueryResult ( $qry );
  }
  $qry = "update {$table}
              set name = '{$name}'";
  $qry .= $suffix_qry;
  $qry .= " where enum_id = {$enum_id};" ;
  return $mysql->getQueryResult ( $qry );
}

function handleAllNavigation ( $delete_existing = true ) {
  $mysql = new MySql();
  if ( $delete_existing ) {
    $queries = [
      "delete from " . EnumSqlTbl::tbl_lu_nav . ";"
    ];
    $mysql->getQueryResult ( $queries );
  }
  $uuid = '';
  $sequence = 0;
  $return = true;
  $return && $return = handleNavigation( 'Students', 'students', 'manage', ++$sequence, EnumUserRoleType::admin, $uuid );
  $return && $return = handleNavigation( 'Users', 'users', 'manage', ++ $sequence, EnumUserRoleType::admin, $uuid );
  $return && $return = handleNavigation( 'Subjects', 'subjects', 'manage', ++$sequence, EnumUserRoleType::none, $uuid );
  $return && $return = handleNavigation( 'Quotation', 'quotation', 'enquire', ++$sequence, EnumUserRoleType::none, $uuid );
  $return && $return = handleNavigation( 'Quotation', 'statement', 'detail', ++$sequence, EnumUserRoleType::authenticated_user, $uuid );
  $return && $return = handleNavigation ( 'Log out', 'account', 'logout', ++$sequence, EnumUserRoleType::authenticated_user, $uuid );
  return $return;
}

function handleAllLookupData() {
  $mysql = new MySql();
  //Role type
  $table = EnumSqlTbl::tbl_lu_user_type;
  handleLookupData ( $table, EnumUserRoleType::none, 'None');
  handleLookupData ( $table, EnumUserRoleType::guest, 'Guest');
  handleLookupData ( $table, EnumUserRoleType::student, 'Student');
  handleLookupData ( $table, EnumUserRoleType::admin, 'Admin');

  //Subjects
  $table = EnumSqlTbl::tbl_subject;
  $enum_val = 1;
  $subjects = [
    array(
      "id" => $enum_val ++,
      "code" => "DSA001",
      "name" => "Data Structures and Algorithms" 
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DMA001",
      "name" => "Discrete Mathematics" 
    ),
    array(
      "id" => $enum_val ++,
      "code" => "WDF001",
      "name" => "Web Development Fundamentals" 
    ),
  ];
  $queries = array();
  foreach( $subjects as $subject ) {
    $qry = "select *
              from {$table}
              where ( id = '" . $subject["id"] . "' );";
    $result = $mysql->getQueryResult ( $qry );
    if ( ! $result || ! $mysql->getQueryResult ( $qry )->num_rows ) {
        $qry = "insert into {$table}
                  set id = '" . $subject["id"] . "',
                  code = '" . $subject["code"] . "',
                  name = '" . $subject["name"] . "',
                  created = now(),
                  last_modified = now(); ";
        return $mysql->getQueryResult ( $qry );
    }
    $qry = "update {$table}
              set id = '" . $subject["id"] . "',
              code = '" . $subject["code"] . "',
              name = '" . $subject["name"] . "',
              last_modified = now()
            where id = '" . $subject["id"] . ";" ;
  }
  $queries[] = $qry;
  return $mysql->getQueryResult ( $queries );
}

function handleAllColumnRename() {
    $return = true;
    // $return && $return = (new MySql() )->tblColRename( /*table=*/EnumSqlTbl::tbl_user, /*old=*/'stdent_uuid', /*new=*/'student_uuid', /*$info=*/ null );
    return $return;
  }

function handleAllColumnDelete() {
  $return = true;
  // $return && $return = ( new MySql() )->tblColDelete( /*table=*/EnumSqlTbl::tbl_student, /*name=*/'stdent_uuid' );
  return $return;
}

function handleTableDelete() {
  $return = true;
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_student );
  return $return;
}

function handleOtherScript() {
  $mysql = new MySql();
  $queries = [
    "delete from tbl_lu_role_type where enum_id= " . EnumUserRoleType::none
  ];
  if ( empty ( $queries ) ) {
      return true;
  }
  return $mysql->getQueryResult ( $queries );
}

function handleAllTableStructure() {
  $return = true;
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_user_type ) )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/false )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
   $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_study_aid ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'code', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'image_name', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_subject ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'code', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'image_name', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_nav ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'controller', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'action', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'sequence', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'user_type_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false)
                  ->addColumn( /*$name = */'tel_no', EnumMySqlColType::varchar, /*$len = */30, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'email', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'student_no', EnumMySqlColType::varchar, /*$len = */20, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'status_id', EnumMySqlColType::tinyint, /*$len = */50, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_user ) )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */true, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'email', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'surname', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'status_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'user_type_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    return $return;
  }