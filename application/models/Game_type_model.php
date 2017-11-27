<?php
class Game_type_model extends CI_Model {

    public function __construct()
    {
		parent::__construct();
    }
    public function get_gameType($typeName = FALSE)
    {
        if ($typeName === FALSE){
            $query = $this->db->get('gameType');
            return $query->result_array();
        }
        $query = $this->db->get_where('gameType', array('gameType_name' => $typeName));
        //return $query->row_array();
        //return $query->row(0, 'gameType');
        return $query->row_array(0);

    }
    public function get_gameTypeById($typeId = 0)
    {
        $query = $this->db->get_where('gameType', array('gameType_id' => $typeId));
        return $query->row_array(0);
    }

}
