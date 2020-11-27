<?php
set_time_limit(0 );
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

function handleNavigation ( $name, $controller, $action, $sequence, &$uuid = '' ) {
  $mysql = new MySql();
  $uuid = $mysql->getUuid();
  $qry = "insert into tbl_lu_nav
          set uuid = '{$uuid}',
          name = '" . Convert::toString ( $name, true ) . "',
          sequence = {$sequence},
          controller = '" . Convert::toString ( $controller, true ) . "',
          action = '" . Convert::toString ( $action, true ) . "';";
  return $mysql->getQueryResult ( $qry );
}

function handleNavigationRoleAccess ( $nav_uuid, $role_id ) {
  $mysql = new MySql();
  $qry = "insert into tbl_lu_nav_role_access
          set uuid = uuid(),
          nav_uuid = '{$nav_uuid}',
          role_id = {$role_id};";
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
      "delete from " . EnumSqlTbl::tbl_lu_nav_role_access . ";",
      "delete from " . EnumSqlTbl::tbl_lu_nav . ";"
    ];
    $mysql->getQueryResult ( $queries );
  }
  $uuid = '';
  $sequence = 0;
  $return = true;
  $return && $return = handleNavigation( 'Profiles', 'profiles', 'manage', ++$sequence, $uuid );
  $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::none );
  $return && $return = handleNavigation( 'students', 'students', 'manage', ++$sequence, $uuid );
  $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::view );
  $return && $return = handleNavigation( 'Units', 'units', 'manage', ++$sequence, $uuid );
  $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::view );
  $return && $return = handleNavigation( 'students', 'students', 'manage', ++$sequence, $uuid );
  $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::view );
  $return && $return = handleNavigation( 'Users', 'users', 'manage', ++$sequence, $uuid );
  $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::view );
  // $return && $return = handleNavigation( 'Document types', 'document-types', 'manage', ++$sequence, $uuid );
  // $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::user, EnumRoleAccessType::view );
  $return && $return = handleNavigation ( 'Log out', 'account', 'logout', ++$sequence, $uuid );
  $return && $return = handleNavigationRoleAccess ( $uuid, EnumUserRoleType::none );
  return $return;
}

function handleAllLookupData() {
  //Role type
  $table = EnumSqlTbl::tbl_lu_role_type;
  handleLookupData ( $table, EnumUserRoleType::manage, 'Manage');
  handleLookupData ( $table, EnumUserRoleType::manage_student, 'Manage students');
  handleLookupData ( $table, EnumUserRoleType::view, 'View');
  //Numbering type
  $table = EnumSqlTbl::tbl_lu_numbering_type;
  handleLookupData ( $table, EnumNumberingType::numeric, 'Numeric' );
  handleLookupData ( $table, EnumNumberingType::alphabetic, 'Alphabetic' );
  //Document types
  $table = EnumSqlTbl::tbl_lu_document_type;
  $enum_val = 1;
  handleLookupData ( $table, $enum_val ++, 'Lease agreement' );
  handleLookupData ( $table, $enum_val ++, 'ID/Passport' );
  handleLookupData ( $table, $enum_val ++, 'Other' );
  //Yes/No
  $table = EnumSqlTbl::tbl_lu_yes_no;
  handleLookupData ( $table, EnumYesNo::no, 'No' );
  handleLookupData ( $table, EnumYesNo::yes, 'Yes' );
}

function handleAllColumnRename() {
    $return = true;
    // ( new MySqlTable( EnumSqlTbl::)-> )->deterColTypeDesc( array ('type_id' => EnumMySqlColType::varchar, 'len' => 50 ), $value );
    // $return && $return = (new MySql() )->tblColRename( /*table=*/EnumSqlTbl::tbl_device, /*old=*/'profile_uuid', /*new=*/'student_uuid', /*$info=*/ null );
    return $return;
  }

function handleAllColumnDelete() {
  $return = true;
  // $return && $return = ( new MySql() )->tblColDelete( /*table=*/EnumSqlTbl::tbl_student_document, /*name=*/'file_path' );
  // $return && $return = ( new MySql() )->tblColDelete( /*table=*/EnumSqlTbl::tbl_student_document, /*name=*/'file_name' );
  // $return && $return = ( new MySql() )->tblColDelete( /*table=*/EnumSqlTbl::tbl_lu_nav_role_access, /*name=*/'access_type_id' );
  return $return;
}

function handleTableDelete() {
  $return = true;
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_audit_trail );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_user );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_lu_yes_no );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_user_profile_access );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_student );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_student_user );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_student );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_student_document );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_profile );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_unit );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_unit_device );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_unit_student );
  $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_device );
  // $return && $return = ( new MySql() )->tblDelete( /*table=*/EnumSqlTbl::tbl_config );
  return $return;
}

function handleOtherScript() {
  $mysql = new MySql();
  $queries = [
    "delete from tbl_lu_role_type where enum_id= " . EnumUserRoleType::none
    // "INSERT INTO `tbl_config` (`id`, `enum_id`, `value`) VALUES (NULL, '1', 'XwQ6RcslpPSbNHfoAtubluLzG3qxKL'), (NULL, '2', 'KZqzgQ9ud4iKmwBXRrg7rRKManrEgp'), (NULL, '3', 'VgNIYfN4CmUjqG3FMMfqDmu9Gg9BOhOB');",
  ];
  if ( empty ( $queries ) ) {
      return true;
  }
  return $mysql->getQueryResult ( $queries );
}

function handleAllTableStructure() {
  $return = true;
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_yes_no ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_document_type ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_numbering_type ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_role_type ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false );
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
                  ->addColumn( /*$name = */'sequence', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_nav_role_access ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'nav_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'role_id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_audit_trail ) )
                   ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                   ->addColumn( /*$name = */'audit_trail_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                   ->addColumn( /*$name = */'old_value', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                   ->addColumn( /*$name = */'new_value', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                   ->addColumn( /*$name = */'column_name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */true )
                   ->addColumn( /*$name = */'table_name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                   ->addColumn( /*$name = */'table_ref_name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */true )
                   ->addColumn( /*$name = */'description', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'single_value', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */0, /*$allow_null = */false , EnumMySqlIndexType::index )
                   ->addColumn( /*$name = */'user_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                   ->addColumn( /*$name = */'version', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false , EnumMySqlIndexType::index )
                   ->addColumn( /*$name = */'created', EnumMySqlColType::date_time );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'profile_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false)
                  ->addColumn( /*$name = */'tel_no', EnumMySqlColType::varchar, /*$len = */30, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'client_id', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'client_secret', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'encryption_key', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student_user ) )
                    ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                    ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                    ->addColumn( /*$name = */'user_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                    ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                    ->addColumn( /*$name = */'soft_del', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumYesNo::no, /*$allow_null = */false, EnumMySqlIndexType::index )
                    ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                    ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_device ) )
                  ->addColumn( /*$name = */'user_uuid', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/false )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'area_to', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'area_to_name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'user_uuid', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */true, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'profile_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'surname', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'birthday', EnumMySqlColType::date, /*$len = */null, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'id_no', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'cell_no', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'lease_expiry_date', EnumMySqlColType::date, /*$len = */null, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'can_load_visitor', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student_document ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'document_type_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'original_name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'given_name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'soft_del', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumYesNo::no, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_profile ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'tel_no', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_config ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                  ->addColumn( /*$name = */'value', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */false );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_unit ) )
                ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'user_uuid', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */true, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'soft_del', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumYesNo::no, /*$allow_null = */false, EnumMySqlIndexType::index )               
                ->addColumn( /*$name = */'number', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                ->addColumn( /*$name = */'no_of_rooms', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'no_of_students', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_unit_device ) )
                ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'device_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'unit_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'soft_del', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumYesNo::no, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_unit_student ) )
                ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/true )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'unit_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'is_tenant', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */2, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'soft_del', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumYesNo::no, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_user ) )
                ->addColumn( /*$name = */'user_uuid', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'surname', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'status_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'last_selected_prof_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_user_profile_access ) )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary )
                ->addColumn( /*$name = */'user_uid', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'profile_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'confirmation_code', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'role_type_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumUserRoleType::none, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'soft_del', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */EnumYesNo::no, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    return $return;
  }