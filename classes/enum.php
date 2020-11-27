<?php
    final class EnumValidMessage {
        const success = 1;
        const failure = 2;
    }

    final class EnumReq {
        const none = 0;
    }

    final class EnumStatus {
        const all = 0;
        const active = 1;
        const inactive = 2;
    }

    final class EnumYesNo {
        const no = 0;
        const yes = 1;
    }

    final class EnumUserRoleType {
        const none = 0;
        const guest = 1;
        const student = 2;
        const admin = 3;
        const authenticated_user = 4;
    }

    final class EnumSqlQryType {
        const none = 0;
        const insert = 1;
        const select = 2;
        const update = 3;
        const delete = 4;
    }

    final class EnumIdUuid {
        const id = 1;
        const uuid = 2;
    }

    final class EnumSqlTbl {
        const tbl_student = "tbl_student";
        const tbl_user = "tbl_user";
        const tbl_subject = "tbl_subject";
        const tbl_lu_nav = "tbl_lu_nav";
        const tbl_lu_user_type = "tbl_lu_user_type";
        const tbl_lu_role_type = "tbl_lu_role_type";
        const tbl_study_aid = "tbl_study_aid";
    }

    final class EnumMySqlColType {
        const tinyint = 1;
        const smallint = 2;
        const mediumint = 3;
        const _int = 4;
        const bigint = 5;
        const decimal = 10;
        const char = 20;
        const varchar = 21;
        const tinytext = 22;
        const text = 23;
        const mediumtext = 24;
        const longtext = 25;
        const date = 40;
        const time = 41;
        const date_time = 42;
    }

    final class EnumMySqlIndexType {
        const primary = 1;
        const uniq = 2;
        const index = 3;
    }

    final class EnumAuditTrailField {
        const html_name = 'html_name';
        const sql_name = 'sql_name';
        const description = 'description';
        const compulsory = 'compulsory';
        const target_type = 'target_type';
        const mysql_tbl = 'mysql_tbl';
        const mysql_ref_tbl = 'mysql_ref_tbl';
        const old_value = 'old_value';
        const new_value = 'new_value';
        const valid = 'valid';
    }
    
    final class EnumFieldDataType {
        const _date = 1;
        const _datetime = 2;
        const _integer = 3;
        const _string = 4;
        const _double = 5;
        const _float = 6;
        const _email = 7;
    }
    
    final class EnumFieldType {
        const _date = 1;
        const _datetime = 2;
        const _integer = 3;
        const _string = 4;
        const _double = 5;
        const _float = 6;
        const _email = 7;
        const _password = 8;
        const _hidden = 9;
        const _checkbox = 10;
        const _radiobutton = 11;
        const _select = 12;
        const _textarea = 13;
        const _file = 14;
    }