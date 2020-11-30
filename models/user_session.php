<?php
class UserSessionMdl {
	public static function getUuid() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		return ( isset ( $_SESSION['user_uuid'] ) ? $_SESSION['user_uuid'] : null ) ;
	}

	public static function getName() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		return ( isset ( $_SESSION['user_name'] ) ? $_SESSION['user_name'] : null ) ;
	}

	public static function getSurname() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		return ( isset ( $_SESSION['user_surname'] ) ? $_SESSION['user_surname'] : null ) ;
	}

	public static function getFullName() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		return self::getName() . ' ' .  self::getSurname() ;
	}

	public static function getUserTypeID() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		return ( isset ( $_SESSION['user_type_id'] ) ? $_SESSION['user_type_id'] : null ) ;
	}

	public static function getUserType() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
		return ( isset ( $_SESSION['user_type'] ) ? $_SESSION['user_type'] : null ) ;
	}

	public static function setUserSession ( $user_data ) {
		 if ( session_status() == PHP_SESSION_NONE ) {
    		session_start();
		}
		if ( is_null ( $user_data ) || ! is_array( $user_data ) ) {
			return false;
		}
		$_SESSION['user_uuid'] = $user_data['uuid'];
		$_SESSION['user_name'] =  $user_data['user_name'];
		$_SESSION['user_surname'] = $user_data['user_surname'];
		$_SESSION['user_type_id'] = $user_data['user_type_id'];
		$_SESSION['user_type'] = $user_data['user_type'];
		return true;
	}

	public static function clearUserSession() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
			session_destroy();
			session_unset();
		}
		return true;
	}
}
?>