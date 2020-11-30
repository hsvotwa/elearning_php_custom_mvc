<?php
class EbookMgr extends BaseMgr {
    public function __construct ( $search_text = "" ) {
        $this->g_entity_name = "ebook(s)";
        $this->g_retrieve_query = $this->getRetrieveQuery( $search_text );
    }

    protected function getRetrieveQuery ( $search_text = "" ) {
         return "select *
                    from tbl_ebook
                 where name like '%" . $search_text . "%'
                 order by name";
    }
}