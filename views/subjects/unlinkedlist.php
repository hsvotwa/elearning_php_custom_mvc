<?php
if( $records && $records->num_rows ) {
    while ( $row = mysqli_fetch_Array( $records ) ) {
        $array[] = [
            'value' => $row['value'],
            'name'  => $row['name'],
        ];
    }
    echo json_encode($array);
}
?>