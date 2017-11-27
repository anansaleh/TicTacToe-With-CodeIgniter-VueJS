<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use libraries\TicTacToe\enums\PcLavelEnum;

class Home_ajax extends CI_Controller {
	
   private $request;
   private $computerID=1;
   private $level=0;
   
   public function __construct(){
      parent::__construct();
        
      $this->load->model('game_type_model');
      $this->load->model('player_model');
      $this->load->model('game_model');
      $this->request = json_decode(file_get_contents('php://input'));
   }
   public function get_game_type(){
      $reponse = $this->game_type_model->get_gameType();
      echo json_encode($reponse);
   }
   public function add_player(){
	   $respons=[
				'success' => false, 
				'error_message' => ''
				];
				
	   if (!$this->player_model->is_mail_used($this->request->email)){		   
			  $new_id=$this->player_model->insert(array(
				 'email' => $this->request->email,
				 'player_name' => $this->request->name
			  ));
			  if(intval($new_id) > 0){
				$respons["success"]= true;
				$respons["error_message"]= "";
			  }else{
				$respons["success"]= false;
				$respons["error_message"]= "Server error";
			  }
		}else{
			$respons["success"]= false;
			$respons["error_message"]= "Email is used by other player";
		}
		echo json_encode($respons);
   }
   public function create_game(){
	   $respons=[
					'success' => false, 
					'error_message' => '',
					'game_id'=> '0'
				];

		
		$player1=$this->player_model->get_playerByEmail($this->request->player1);

		if(!$player1){
			$respons['error_message']="Player1 not found";
			echo json_encode($respons);
			exit();
		}
		if(intval($this->request->gameType) !== 1){
			$player2=$this->player_model->get_playerByEmail($this->request->player2);
			if(!$player2){
				$respons['error_message']="Player2 not found";
				echo json_encode($respons);
				exit();
			}
		}else{
			// player2=Computer
			$player2=$this->player_model->get_playerById($this->computerID);
			$this->level= intval($this->request->level);
		}
		/*
		echo "<pre>";
		print_r($player1);
		//print_r($player2);
		echo "</pre>" ;
		exit();
		*/
		
		//Check if playe1== player2
		if(intval($player1->player_id) === intval($player2->player_id)){
			$respons["success"]= false;
			$respons["error_message"]= "Player2 must be different to player1";
			echo json_encode($respons);
			exit();
		}
		//Create new Game
		$new_game_id=$this->game_model->insert_game(
								[
								 'gameType_id' => $this->request->gameType,
								 'player1_id' => $player1->player_id,
								 'player2_id' => $player2->player_id,
								 'level' => $this->level,
								]);
		
		if(intval($new_game_id) > 0){
			$respons["success"]= true;
			$respons["error_message"]= "";
			$respons["game_id"]= $new_game_id;
		}else{
			$respons["success"]= false;
			$respons["error_message"]= "Server error";
		}
		echo json_encode($respons);
   }   
}
