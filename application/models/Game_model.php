<?php
class Game_model extends CI_Model {

    public function __construct()
    {
		parent::__construct();
    }
    //game by players
    public function get_game($game_id = FALSE)
    {
        if ($game_id === FALSE){
		  return $this->db
			 ->select('*')
			 ->from('games')
			 ->get()
			 ->result();
        }
		$query= $this->db
					 ->select('*')
					 ->from('games')
					 ->where('game_id =', $game_id)
					 ->get();
        return $query->row_array(0);

    }
    public function get_gameByPlayer($player )
    {
		 $query= $this->db
					 ->select('*')
					 ->from('games')
					 ->where('player1_id =', $player)
					 ->or_where('player2_id =', $player)
					 ->get()
					 ->result();
        return $query->row_array(0);
	}
    public function get_gameById($game_id = 0)
    {
		$queString="			
			SELECT games.* ,
				gameType.gameType_name, 
				(SELECT players.player_name FROM players WHERE players.player_id= games.player1_id) AS player1_name,
				(SELECT players.player_name FROM players WHERE players.player_id= games.player2_id) AS player2_name
				FROM games 
				INNER JOIN gameType ON gameType.gameType_id =games.gameType_id
				WHERE games.game_id =" . $game_id;
        $query = $this->db->query($queString);
        //return $query->row_array(0);
        return $query->first_row();
    }
    public function insert_game($data)
    {
		$this->db->insert('games', array(
			'gameType_id' => $data['gameType_id'],
			'player1_id' => $data['player1_id'],
			'player2_id' => $data['player2_id'],
			'level' => $data['level'],
		));
		return $this->db->insert_id();
    }
    public function update_game($data)
    {

    }
    
    public function set_status($data)
    {
		$this->db
		->where('game_id', $data['game_id'])
		->update('games', array(
				'status' => $data['status']
				));
    }
    public function set_terminate($data)
    {
		$this->db
		->where('game_id', $data['game_id'])
		->update('games', array(
				'winner_id' => $data['winner_id'],
				'status' => $data['status'],
				'date_ended'=> date("Y-m-d H:i:s")
				));
    }
    public function create_new_game_from_exist_game($game_id){
		$queString="
				INSERT INTO games (gameType_id, player1_id, player2_id, level, status)
				SELECT gameType_id, player1_id, player2_id, level, 1 FROM games
				WHERE games.game_id =" . $game_id;
		$query = $this->db->query($queString);
		return $this->db->insert_id();
	} 

}
