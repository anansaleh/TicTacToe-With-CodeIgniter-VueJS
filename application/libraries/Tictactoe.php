<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * from controller
 * $this->load->library('someclass');
 * $this->load->library('someclass', $params);
 * Setting Your Own Prefix
	To set your own sub-class prefix, open your application/config/config.php file and look for this item:
	$config['subclass_prefix'] = 'MY_';
	* 
		class MY_Email extends CI_Email {
				public function __construct($config = array())
				{
						parent::__construct($config);
				}
		}
	To load your sub-class you’ll use the standard syntax normally used. DO NOT include your prefix. 
	For example, to load the example above, which extends the Email class, you will use:
	$this->load->library('email');

	$this->load->library(array('email', 'table'));
 */


//use models\Player;
//use models\Game;

class Tictactoe{
	protected $CI;
	protected $game_id;
	protected $game_obj;
	protected $player1_id;
	protected $player2_id;
	protected $board= ["","","","","","","","",""];
	protected $turn ="X";
	protected $round=0;
	protected $status= 0;
	protected $player_turn=1;
	protected $winn_line=[];
	
	public function __construct($params = [])
	{
		if(!array_key_exists("game_id",$params)){
			echo "Tictactoe game id not .......";
			show_404();
			exit();
		}
		$this->game_id=$params["game_id"];
		
		if (array_key_exists("player_turn",$params)){
			$this->player_turn=$params["player_turn"];
		}
		if (array_key_exists("turn",$params)){
			$this->player_turn=$params["turn"];
		}
		$this->turn=$params["turn"];
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
		$this->CI->load->model('game_model');
		$this->CI->load->model('game_state_model');
	}
	public function loadGame(){
		$this->game_obj=$this->CI->game_model->game_model->get_gameById($this->game_id);
		$this->player1_id=$this->game_obj->player1_id;
		$this->player2_id=$this->game_obj->player2_id;
		return 	$this->game_obj;
		
	}
	public function init_new_game(){
		$date=array(
			'game_id'=>$this->game_obj->game_id,
			'round' => $this->round,
			'board' => json_encode($this->board),
			'player_turn'=> $this->player_turn,
			'turn'=> $this->turn,
			);
		$state=$this->CI->game_state_model->insert($date);
		return $state;
	}
	public function get_game_board(){
		return $this->CI->game_state_model->get_game_state($this->game_id);
	}
	public function get_history(){
		return $this->CI->game_state_model->get_game_state();
	}
	public function move($date){
		/*
		 * 1- Parametrs: Char. Position, player
		 * 2- See if board[position] == ''
		 * 3- See if Char == turn && player == player_turn
		 * 4- Check Game over or winn if yes exit
		 * 5- Set board[Position] == Char
		 * 6- Change Turn
		 * 7- Check Game over or winn
		 * 8- update State 
		 * 
		*/ 
		//Check exists $data
		if(	(!array_key_exists("char_turn",$date))  
			&& (!array_key_exists("position",$date))
			&& (!array_key_exists("player",$date)) ){
			return false;
			exit();
		}
		//Chack validate turn & player
		if( ($date["char_turn"] != $this->turn) 
			|| ($date["player"] != $this->player_turn) 
			|| ($date["position"] =="") ){
			return false;
			exit();
		}
		//Chack validate position board
		if( (!array_key_exists(intval($date["position"]),$this->board) 
			|| ($this->board[$date["position"]] !='') ){
			//invalid position board
			return false;
			exit();
		}
		
		if($this->check_game_end()){
			return false;
			exit();	
		}
		
		//update board
		$this->board[$date["position"]= $date["char_turn"];
		$this->turn = ($this->turn=="X") ? "O" : "X" ;
		$this->player_turn = ($this->player_turn==1) ? 2 : 1 ;
		
		if($this->check_game_end()){
			
		}
		//Update state
	}
	private function check_game_end(){
		/*
		 * Board
		 * 0 | 1 | 2
		 * 3 | 4 | 5
		 * 6 | 7 | 8
		 * */
		// Check rows board
		/* [0,1,2] , 
		 * [3,4,5] , 
		 * [6,7,8]
		 */
		 $board =$this->board;
		/*
        for(var i = 0; i <= 6; i = i + 3) {
            if(B[i] !== "E" && B[i] === B[i + 1] && B[i + 1] == B[i + 2]) {
                this.result = B[i] + "-won"; //update the state result
                return true;
            }
        }
        * */
        for ($i = 0; $i <= 6; $i+=3){
			if(($board[$i] !="") && ($board[$i]== $board[$i+1]) && ($board[$i+1]== $board[$i+2]){
				this->winn_line=array( $i , $i + 1, $i+ 2);
				return true;
			}
		}
		 /*
		 if( ($board[0] == $board[1]) && ($board[0]== $board[2]) && ($board[0] !="") ){
			 //row 1 winn
			 this->winn_line=array(0,1,2);
			 return true;
		 }
		 if( ($board[3] == $board[4]) && ($board[3]== $board[5]) && ($board[3] !="") ){
			 //row 2 winn
			 this->winn_line=array(3,4,5);
			 return true;
		 }
		 if( ($board[6] == $board[7]) && ($board[6]== $board[8]) && ($board[6] !="") ){
			 //row 3 winn
			 this->winn_line=array(6,7,8);
			 return true;
		 }
		 */
		 

		 // Check cols board
		/* [0,3,6] , 
		 * [1,4,7] , 
		 * [2,5,8]
		 */
		 
        //check columns
        /*
        for(var i = 0; i <= 2 ; i++) {
            if(B[i] !== "E" && B[i] === B[i + 3] && B[i + 3] === B[i + 6]) {
                this.result = B[i] + "-won"; //update the state result
                return true;
            }
        }
        */
        for ($i = 0; $i <= 2; $i++){
			if(($board[$i] !="") && ($board[$i]== $board[$i+3]) && ($board[$i+3]== $board[$i+6]){
				this->winn_line=array( $i , $i + 3, $i+ 6);
				return true;
			}
		}
		 /*
		 if( ($board[0] == $board[3]) && ($board[0]== $board[6]) && ($board[0] !="") ){
			 //col 1 winn
			 this->winn_line=array(0,3,6);
			 return true;
		 }
		 if( ($board[1] == $board[4]) && ($board[1]== $board[7]) && ($board[1] !="") ){
			 //col 2 winn
			 this->winn_line=array(1,4,7);
			 return true;
		 }
		 if( ($board[2] == $board[5]) && ($board[2]== $board[8]) && ($board[2] !="") ){
			 //col 3 winn
			 this->winn_line=array(2,5,8);
			 return true;
		 }
		 */
		 
		 //Check diagonals
		/* [0,4,8] , 
		 * [2,4,6] , 
		 */
        //check diagonals
        /*
        for(var i = 0, j = 4; i <= 2 ; i = i + 2, j = j - 2) {
            if(B[i] !== "E" && B[i] == B[i + j] && B[i + j] === B[i + 2*j]) {
                this.result = B[i] + "-won"; //update the state result
                return true;
            }
        }
        */
        for ($i = 0, $j =4; $i <= 2; $i+=2, $j-=2){
            if($board[$i] !== "" && $board[$i] == $board[$i + $j] && $board[$i + $j] === $board[$i + 2*$j]) {
                this->winn_line=array( $i , $i + $j, $i + 2*$j);
                return true;
            }		
		}
		 /* 
		 if( ($board[0] == $board[4]) && ($board[0]== $board[8]) && ($board[0] !="") ){
			 //dig 1 winn
			 this->winn_line=array(0,4,8);
			 return true;
		 }
		 
		 if( ($board[2] == $board[4]) && ($board[2]== $board[6]) && ($board[2] !="") ){
			 //dig 2 winn
			 this->winn_line=array(2,4,6);
			 return true;
		 }
		 */
		 //Check if game has emptyCells
		 for ($i = 0; $i < 9; $i++){
			 if($board[$i] ==""){
				return false; 
			 }
		 }
		 //the game is draw
		 return true;
	}
}
