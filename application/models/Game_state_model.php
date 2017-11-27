<?php
class Game_state_model extends CI_Model {

    public function __construct()
    {
		parent::__construct();
    }
    public function get_game_state($game_id = FALSE)
    {
		$queString="
				SELECT 
					state.state_id,
					games.* ,
					gameType.gameType_name, 
					(SELECT players.player_name FROM players WHERE players.player_id= games.player1_id) AS player1_name,
					(SELECT players.player_name FROM players WHERE players.player_id= games.player2_id) AS player2_name,
					(SELECT players.player_name FROM players WHERE players.player_id= games.winner_id) AS winner_name,
					state.round AS state_round,
					state.status AS state_status,
					state.board,
					state.player_turn,
					state.turn,
					state.oMovesCount,
					state.winn_line
				FROM games 
				INNER JOIN gameType ON gameType.gameType_id =games.gameType_id
				INNER JOIN state ON state.game_id =games.game_id 
			";
        if ($game_id === FALSE){
			 $query = $this->db->query($queString);	
            //return $query->result_array();
            return $query->result();
        }
        
		$queString .= " WHERE games.game_id =" . $game_id;
		$query = $this->db->query($queString);	
        return $query->first_row();
    }
    
    public function insert($data)
    {
		//Check if game has state
		$game = $this->get_stateByGame($data['game_id']);
		if(count($game) > 0 ){
			return $game['state_id'];
		}else{
			//Create new State
			$data_array= [
					'game_id' => $data['game_id'],
					'round' => $data['round'],
					'board' => json_encode($data['board']),
					'player_turn' => $data['player_turn'],
					'turn' => $data['turn'],
					'winn_line' => json_encode($data['winn_line'])
					];
			if (array_key_exists("status",$data)) $data_array["status"] = $data['status'];
			if (array_key_exists("oMovesCount",$data)) $data_array["oMovesCount"] = $data['oMovesCount'];
			
			$this->db->insert('state', $data_array);
			return $this->db->insert_id();
		}
    }
	public function get_stateByGame($game_id){
		$query = $this->db->get_where('state', array('game_id' => $game_id));
		return $query->row_array();
	}
	public function update($data){
		$data_array= [
				'round' => $data['round'],
				'status' => $data['status'],
				'board' => json_encode($data['board']),
				'player_turn' => $data['player_turn'],
				'turn' => $data['turn'],
				'winn_line' => json_encode($data['winn_line']),
				];
		if (array_key_exists("oMovesCount",$data)) $data_array["oMovesCount"] = $data['oMovesCount'];
		$this->db
			->where('game_id', $data['game_id'])
			->update('state', $data_array);
	}
    public function set_status($data)
    {
		$this->db
		->where('game_id', $data['game_id'])
		->update('state', array(
				'status' => $data['status']
				));
    }
    
    public function get_gamesEnded_list()
    {
		$queString="
				SELECT 
					state.state_id,
					games.* ,
					gameType.gameType_name, 
					(SELECT players.player_name FROM players WHERE players.player_id= games.player1_id) AS player1_name,
					(SELECT players.player_name FROM players WHERE players.player_id= games.player2_id) AS player2_name,
					(SELECT players.player_name FROM players WHERE players.player_id= games.winner_id) AS winner_name,
					state.round AS state_round,
					state.status AS state_status,
					state.board,
					state.player_turn,
					state.turn,
					winn_line
				FROM games 
				INNER JOIN gameType ON gameType.gameType_id =games.gameType_id
				INNER JOIN state ON state.game_id =games.game_id 
				WHERE games.status = 2
				ORDER BY  games.date_created DESC 
				limit 0, 3
			";
		$query = $this->db->query($queString);
		/*
		echo "<pre>";
		print_r($query->result_array());
		echo "</pre>";
		*/
        return $query->result_array();
    }    
}
