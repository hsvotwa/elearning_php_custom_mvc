<?php
define( 'APP_NAME', 'Trustco Educations' );
define ( 'APP_SHORT_NAME', 'Trustco Educations' );
define ( 'APP_DIR', '' );
define ( 'IMAGE_UPLOAD_DIR', 'uploads/' );
define ( 'DATETIME_MYSQL_FORMAT', 'Y-m-d H:i:s' );
define ( 'DATE_MYSQL_FORMAT', 'Y-m-d' );
define ( 'DATE_SEC_IN_DAY', 86400 );
define ( 'TIME_MYSQL_FORMAT', 'H:i:s' );
define ( 'USER_DATE_FORMAT', 'd/m/Y' );
define ( 'USER_DATE_TIME_FORMAT', 'd M Y H:i' );
define ( 'USER_TIME_FORMAT', 'H:i' );
define ( 'FIELD_INVALID_STYLE', 'border: 1px solid red;' );
define ( 'COMPANY_NAME', 'Trustco Educations' );
define ( 'EXCEPTION_MESSAGE', 'An error has occurred. Please retry.' );
define ( 'UNAUTHORISED_MESSAGE', 'You\'re not authorised to perform this function.' );
define ( 'INDEX_PAGE', 'index.php' );
define ( 'AJAX_PAGE', 'index_req.php' );
define ( 'CURRENCY_SYMBOL', 'N$' );

if ( Common::isLiveServer() ) {
	define ( 'MYSQL_HOST', 'localhost' );
	define ( 'MYSQL_USR', 'root' );
	define ( 'MYSQL_PWD', '' );
	define ( 'MYSQL_DB', 'trustco_education1' );
	define( 'APP_DOMAIN', 'http://173/' );
}  else {
	define ( 'MYSQL_HOST', 'localhost' );
    define ( 'MYSQL_USR', 'hope' );
    define ( 'MYSQL_PWD', 'geekS@#5214' );
    define ( 'MYSQL_DB', 'trustco1' );
	define('APP_DOMAIN', 'http://localhost/trustco1/');
}
//SMTP
define ( 'SMTP_DEBUG', false );
define ( 'SMTP_AUTH', true );
define ( 'SMTP_SECURE', 'tls' );
define ( 'SMTP_HOST', 'email-smtp.eu-central-1.amazonaws.com' );
define ( 'SMTP_PORT', 587 ); // or 25 or 487
define ( 'SMTP_USERNAME', 'AKIAWH6LSIFUJG33MUG2' );
define ( 'SMTP_PASSWORD', 'BGM370NTPyAVp/q9E0Dy/9qn+2GJpxMvhbDzqm4jeLPJ' );
define ( 'SMTP_FROM_ADDRESS', 'no-reply@bcity.medsd' );
?>