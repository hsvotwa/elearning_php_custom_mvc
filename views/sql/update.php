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
  $return && $return = handleNavigation( 'News updates', 'news', 'manage', ++ $sequence, EnumUserRoleType::admin, $uuid );
  $return && $return = handleNavigation( 'Subjects', 'subjects', 'manage', ++$sequence, EnumUserRoleType::none, $uuid );
  $return && $return = handleNavigation( 'Study Aids', 'aids', 'manage', ++$sequence, EnumUserRoleType::none, $uuid );
  $return && $return = handleNavigation( 'Apply', 'student', 'apply', ++$sequence, EnumUserRoleType::guest, $uuid );
  $return && $return = handleNavigation( 'Statement', 'student', 'feesbreakdown', ++$sequence, EnumUserRoleType::student, $uuid );
  $return && $return = handleNavigation( 'eBooks', 'books', 'manage', ++$sequence, EnumUserRoleType::student, $uuid );
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
  //Payment terms
  $table = EnumSqlTbl::tbl_lu_payment_term;
  handleLookupData ( $table, EnumPaymentTerm::one_year, '1 year');
  handleLookupData ( $table, EnumPaymentTerm::two_years, '2 years');
  handleLookupData ( $table, EnumPaymentTerm::three_years, '3 years');
  $table = EnumSqlTbl::tbl_lu_student_status;
  handleLookupData ( $table, EnumStudentStatus::draft, 'Draft');
  handleLookupData ( $table, EnumStudentStatus::applied, 'Applied');
  handleLookupData ( $table, EnumStudentStatus::active, 'Active');
  handleLookupData ( $table, EnumStudentStatus::inactive, 'Inactive');
  handleLookupData ( $table, EnumStudentStatus::declined, 'Declined');
  //Subjects
  $table = EnumSqlTbl::tbl_subject;
  $enum_val = 1;
  $subjects = [
    array(
      "id" => $enum_val ++,
      "code" => "DSA001",
      "name" => "Data Structures and Algorithms Beginner",
      "image_name" => "dsa001.png",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DMA001",
      "name" => "Discrete Mathematics Beginner",
      "image_name" => "dma001.jpg",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "WDE001",
      "name" => "Web Development Beginner",
      "image_name" => "wdf001.png",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DSA002",
      "name" => "Data Structures and Algorithms Intermediate",
      "image_name" => "dsa001.png",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DMA002",
      "name" => "Discrete Mathematics Intermediate",
      "image_name" => "dma001.jpg",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "WDE002",
      "name" => "Web Development Intermediate",
      "image_name" => "wdf001.png",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DSA003",
      "name" => "Data Structures and Algorithms Advanced",
      "image_name" => "dsa001.png",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DMA003",
      "name" => "Discrete Mathematics Advanced",
      "image_name" => "dma001.jpg",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "WDE003",
      "name" => "Web Development Advanced",
      "image_name" => "wdf001.png" ,
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DSA004",
      "name" => "Data Structures and Algorithms Expert",
      "image_name" => "dsa001.png",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "DMA004",
      "name" => "Discrete Mathematics Expert",
      "image_name" => "dma001.jpg",
      "cost" => 1000.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "WDE004",
      "name" => "Web Development Expert",
      "image_name" => "wdf001.png" ,
      "cost" => 1000.00
    )
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
                  image_name = '" . $subject["image_name"] . "',
                  cost = '" . $subject["cost"] . "',
                  created = now(),
                  last_modified = now(); ";
         $mysql->getQueryResult ( $qry );
         continue;
    }
    $qry = "update {$table}
              set id = '" . $subject["id"] . "',
              code = '" . $subject["code"] . "',
              name = '" . $subject["name"] . "',
              image_name = '" . $subject["image_name"] . "',
              cost = '" . $subject["cost"] . "',
              last_modified = now()
            where id = '" . $subject["id"] . ";" ;
  }
  //Study Aids
  $table = EnumSqlTbl::tbl_study_aid;
  $enum_val = 1;
  $study_aids = [
    array(
      "id" => $enum_val ++,
      "code" => "LAP001",
      "name" => "Laptop - MacBook Pro 2020",
      "image_name" => "macbookpro.png",
      "cost" => 24234.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "LAP002",
      "name" => "Laptop - Dell Inspiron 2020",
      "image_name" => "dellinspiron.png",
      "cost" => 14634.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "TAB001",
      "name" => "Tablet - Galaxy Tab A 2020",
      "image_name" => "galaxytaba.png",
      "cost" => 9675.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "TAB002",
      "name" => "Tablet - iPad Pro 2020",
      "image_name" => "ipadpro.png",
      "cost" => 18999.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "LAP003",
      "name" => "Laptop - MacBook Pro 2021",
      "image_name" => "macbookpro.png",
      "cost" => 24234.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "LAP004",
      "name" => "Laptop - Dell Inspiron 2021",
      "image_name" => "dellinspiron.png",
      "cost" => 18634.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "TAB003",
      "name" => "Tablet - Galaxy Tab A 2021",
      "image_name" => "galaxytaba.png",
      "cost" => 12675.00
    ),
    array(
      "id" => $enum_val ++,
      "code" => "TAB004",
      "name" => "Tablet - iPad Pro 2021",
      "image_name" => "ipadpro.png",
      "cost" => 20999.00
    )
  ];
  foreach( $study_aids as $study_aid ) {
    $qry = "select *
              from {$table}
              where ( id = '" . $study_aid["id"] . "' );";
    $result = $mysql->getQueryResult ( $qry );
    if ( ! $result || ! $mysql->getQueryResult ( $qry )->num_rows ) {
        $qry = "insert into {$table}
                  set id = '" . $study_aid["id"] . "',
                  code = '" . $study_aid["code"] . "',
                  name = '" . $study_aid["name"] . "',
                  image_name = '" . $study_aid["image_name"] . "',
                  cost = '" . $study_aid["cost"] . "',
                  created = now(),
                  last_modified = now(); ";
        $mysql->getQueryResult ( $qry );
        continue;
    }
    $qry = "update {$table}
              set id = '" . $study_aid["id"] . "',
              code = '" . $study_aid["code"] . "',
              name = '" . $study_aid["name"] . "',
              image_name = '" . $study_aid["image_name"] . "',
              cost = '" . $study_aid["cost"] . "',
              last_modified = now()
            where id = '" . $study_aid["id"] . ";" ;
  }
  //eBooks
  $table = EnumSqlTbl::tbl_ebook;
  $enum_val = 1;
  $ebooks = [
    array(
      "id" => $enum_val ++,
      "name" => "Data Structures: A complete guide",
      "file_name" => "ebook1.pdf",
      "image_name" => "dsa001.png"
    ),
    array(
      "id" => $enum_val ++,
      "name" => "Magic with Daiscrete Maths",
      "file_name" => "ebook2.pdf",
      "image_name" => "dma001.png"
    ),
    array(
      "id" => $enum_val ++,
      "name" => "The Ultimate Data Structures",
      "file_name" => "ebook3.pdf",
      "image_name" => "wdf001.png"
    ),
    array(
      "id" => $enum_val ++,
      "name" => "Code refactoring: A practical approach",
      "file_name" => "ebook4.pdf",
      "image_name" => "dsa001.png"
    ),
    array(
      "id" => $enum_val ++,
      "name" => "Master Discrete Mathematics in 21 days",
      "file_name" => "ebook5.pdf",
      "image_name" => "dma001.png"
    )
  ];
  foreach( $ebooks as $ebook ) {
    $qry = "select *
              from {$table}
              where ( id = '" . $ebook["id"] . "' );";
    $result = $mysql->getQueryResult ( $qry );
    if ( ! $result || ! $mysql->getQueryResult ( $qry )->num_rows ) {
        $qry = "insert into {$table}
                  set id = '" . $ebook["id"] . "',
                  file_name = '" . $ebook["file_name"] . "',
                  name = '" . $ebook["name"] . "',
                  image_name = '" . $ebook["image_name"] . "',
                  created = now(),
                  last_modified = now(); ";
        $mysql->getQueryResult ( $qry );
        continue;
    }
    $qry = "update {$table}
              set id = '" . $ebook["id"] . "',
              file_name = '" . $ebook["file_name"] . "',
              name = '" . $ebook["name"] . "',
              image_name = '" . $ebook["image_name"] . "',
              last_modified = now()
            where id = '" . $ebook["id"] . ";" ;
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
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_student_status ) )
                  ->addColumn( /*$name = */'enum_id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/false )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_lu_payment_term ) )
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
                  ->addColumn( /*$name = */'cost', EnumMySqlColType::decimal, /*$len = */"18,2", /*$def = */0, /*$allow_null = */false )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_ebook ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/false )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'file_name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'image_name', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
  if ( ! $db_tbl->handle() ) {
    echo "Could not create/alter table: {$db_tbl->getName()} </br>";
    $return = false;
  }
  $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_subject ) )
                  ->addColumn( /*$name = */'id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::primary, /*$auto_increment =*/false )
                  ->addColumn( /*$name = */'name', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'code', EnumMySqlColType::varchar, /*$len = */50, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'image_name', EnumMySqlColType::varchar, /*$len = */100, /*$def = */null, /*$allow_null = */false )
                  ->addColumn( /*$name = */'cost', EnumMySqlColType::decimal, /*$len = */"18,2", /*$def = */0, /*$allow_null = */false )
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
                  ->addColumn( /*$name = */'status_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'payment_term_id', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                  ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                  ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
    $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student_subject ) )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'subject_id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false )
                ->addColumn( /*$name = */'soft_deleted', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'cost', EnumMySqlColType::decimal, /*$len = */"18,2", /*$def = */0, /*$allow_null = */false )
                ->addColumn( /*$name = */'created', EnumMySqlColType::date_time )
                ->addColumn( /*$name = */'last_modified', EnumMySqlColType::date_time );
    if ( ! $db_tbl->handle() ) {
      echo "Could not create/alter table: {$db_tbl->getName()} </br>";
      $return = false;
    }
     $db_tbl = ( new MySqlTable( EnumSqlTbl::tbl_student_aid ) )
                ->addColumn( /*$name = */'uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::uniq )
                ->addColumn( /*$name = */'student_uuid', EnumMySqlColType::char, /*$len = */36, /*$def = */null, /*$allow_null = */true )
                ->addColumn( /*$name = */'aid_id', EnumMySqlColType::_int, /*$len = */11, /*$def = */null, /*$allow_null = */false )
                ->addColumn( /*$name = */'soft_deleted', EnumMySqlColType::tinyint, /*$len = */4, /*$def = */null, /*$allow_null = */false, EnumMySqlIndexType::index )
                ->addColumn( /*$name = */'cost', EnumMySqlColType::decimal, /*$len = */"18,2", /*$def = */0, /*$allow_null = */false )
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
                ->addColumn( /*$name = */'password', EnumMySqlColType::varchar, /*$len = */200, /*$def = */null, /*$allow_null = */true )
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