<?php
class Player_model extends CI_Model {
   public function __construct(){
      parent::__construct();
   }
   public function get_all_players(){
      return $this->db
         ->select('player_id, email, player_name')
         ->from('players')
         ->get()
         ->result();
   }
   public function insert($data){
      $this->db->insert('players', array(
         'email' => $data['email'],
         'player_name' => $data['player_name']
      ));
      return $this->db->insert_id();
   }
   public function is_mail_used($email){
		//$que="SELECT COUNT(*)  AS count FROM players WHERE email='" . $email ."'; ";
		$query = $this->db->query("SELECT COUNT(*)  AS count FROM players WHERE email='" . $email ."'; ");
		$row = $query->row();
		return (intval($row->count) > 0);
   }
   public function get_playerById($player_id){
	  //$query = $this->db->get_where('players', array('email' => $email));
      $player= $this->db
				 ->select('player_id, email, player_name')
				 ->from('players')
				 ->where('player_id', $player_id)
				 ->get()
				 ->result();
		return $player[0];
   }
   public function get_playerByEmail($email){
	  //$query = $this->db->get_where('players', array('email' => $email));
      $query= $this->db
				 ->select('player_id, email, player_name')
				 ->from('players')
				 ->where('email', $email)
				 ->get();
				 //->result();
		//echo $query->num_rows();
		if ($query->num_rows() > 0) {
			//echo "true";
			return $query->first_row();
		} else {
			return FALSE;
		}
		//return $player[0];
   }   
}
