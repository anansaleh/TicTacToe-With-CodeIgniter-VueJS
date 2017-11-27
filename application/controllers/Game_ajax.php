<?php
use libraries\TicTacToe\State;
use libraries\TicTacToe\Game;
use libraries\TicTacToe\PC_Player;
use libraries\TicTacToe\enums\GameTypeEnum;


defined('BASEPATH') OR exit('No direct script access allowed');
class Game_ajax extends CI_Controller {
	private $request;
	private $computerID=1;
	
	public function __construct(){
		parent::__construct();

		$this->load->model('game_type_model');
		$this->load->model('player_model');
		$this->load->model('game_model');
		$this->load->model('game_state_model');
		$this->request = json_decode(file_get_contents('php://input'));
		
		// Load session library
		$this->load->library('session');      
	}   
	public function get_game_state(){
	   $game_id= $this->request->game_id;
	   //$game_id=2;
		//Get Game Data
		
		$data=$this->game_state_model->get_game_state($game_id);
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		exit();
		*/
		$data= json_decode(json_encode($data), True);
		$data['board'] = json_decode($data['board']);
		$data['winn_line'] = json_decode($data['winn_line']);
		
		echo json_encode($data);
		
	}
	
	public function play(){
		
		//TODO game_id: must read from html
		
	   $game_id = $this->request->game_id;

		//Get Game Data
		$data=$this->game_state_model->get_game_state($game_id);
		
		$data_array= json_decode(json_encode($data), True);
		$data_array['board'] = json_decode($data_array['board']);
		$data_array['winn_line'] = json_decode($data_array['winn_line']);
		
		$game= new Game($data_array);
		//echo $game->level;
		//exit();
				
		$flag= $game->humanPlay($this->request->player,
								$this->request->possition,
								$this->request->turn);
	   $respons=[
			'success' => false, 
			'error_message' => ''];

		if($flag){
			//prepare data to update state
			$data_array['state_round']= intval($data_array['state_round']) + 1;
			$data=[
				'game_id'=> $game_id,
				'round' => $data_array['state_round'],
				'board' => $game->currentState->board,
				'player_turn' => $game->currentState->players[$game->currentState->turn],
				'turn' => $game->currentState->turn,
				'status' => $game->currentState->status,
				'winn_line' => $game->currentState->winn_line,
				];
			$this->game_state_model->update($data);
			
			//////////////////////////////////////////////////
			//Check if is singleGame then turn PC-Player
				if (intval($game->game_type) == GameTypeEnum::Singleplayer 
						&& !$game->currentState->isTerminal()){
						//echo "PC Start";
						$pc_palyer = new PC_Player($game);
						$pc_turn= $pc_palyer->play($game->currentState->turn);
						/*
						echo "<pre>";
						print_r($pc_turn);
						echo "</pre>" ;
						*/
						$game->humanPlay(1,$pc_turn['possition'], $pc_turn['turn']);
						
						$data_array['state_round']= intval($data_array['state_round']) + 1;
						//Update Date
						$data=[
							'game_id'=> $game_id,
							'round' => $data_array['state_round'],
							'board' => $game->currentState->board,
							'player_turn' => $game->currentState->players[$game->currentState->turn],
							'turn' => $game->currentState->turn,
							'status' => $game->currentState->status,
							'winn_line' => $game->currentState->winn_line,
							'oMovesCount' => $game->currentState->oMovesCount,
							];
						$this->game_state_model->update($data);
				}
			//exit();
			/////////////////////////////////////////////////
			//Get Game Data
			$data=$this->game_state_model->get_game_state($game_id);
			$data= json_decode(json_encode($data), True);
			$data['board'] = json_decode($data['board']);
			$data['winn_line'] = json_decode($data['winn_line']);
			
			$respons["success"]= true;
			$respons["error_message"]= "";
			//$respons["game_state"] = $data;
		}else{
			$respons["success"]= false;
			$respons["error_message"]= "Error move human";
		}
		echo json_encode($respons);
		
	}
	
	public function scores(){
		//Get all Games list
		$data=$this->game_state_model->get_gamesEnded_list();
		for($i =0;$i < count($data); $i++){
			$data[$i]['board'] = json_decode($data[$i]['board']);
			$data[$i]['winn_line'] = json_decode($data[$i]['winn_line']);
			$data[$i]['date_created'] = date("d-m-Y H:i", strtotime( $data[$i]['date_created']));
			$data[$i]['date_modified'] = date("d-m-Y H:i", strtotime( $data[$i]['date_modified']));
			$data[$i]['date_ended'] = date("d-m-Y H:i", strtotime( $data[$i]['date_ended']));
		}
		echo json_encode($data);
	}
	public function play_again(){
	   $respons=[
			'success' => false, 
			'error_message' => ''];
		$game_id = $this->request->game_id;
		$player1 = $this->request->player1;
		if(!intval($game_id)>0){
			$respons["success"]= false;
			$respons["error_message"]= "Invalid game";
		}else{
			$new_game= $this->game_model->create_new_game_from_exist_game($game_id);
			
			// Save in session
			$this->session->unset_userdata('current_game');
			//$this->session->set_userdata('current_game', $data);
			$this->session->set_userdata('current_game', $new_game);
			
			//create new State for the game();
			$data_state=[
						'game_id'=> $new_game,
						'board' => ["","","","","","","","",""],
						'round' => 0,
						'turn'	=> "X",
						'player_turn'=> $player1,
						'winn_line'=> [],
						'status'=> 0,
						'oMovesCount'=>0
						];
			/*************************************/
			$state_id= $this->game_state_model->insert($data_state);
			
			$respons["success"]= true;
			$respons["error_message"]= "";
			$respons["game_id"] =$new_game;
		}
		echo json_encode($respons);
	}
}
