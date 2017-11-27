<?php

namespace libraries\TicTacToe;

use libraries\TicTacToe\enums\StatusEnum;
use libraries\TicTacToe\enums\StateEnum;
use libraries\TicTacToe\State;
/*
 * Represents a game object to be played
 * @param old [State]: old state to intialize the new state
 */
 
class Game{
    /**
     * @var $game_id : the game_id for this game.
     */	 
	protected $game_id;
	
    /**
     * @var $game_type : the game_type for this game.
     */
	protected $game_type=1;
	
    /**
     * @var $currentState : initialize the game current state to empty board configuration.
     */
	public $currentState ;
	
	////////////
	protected $board= ["","","","","","","","",""];
	protected $turn= "X" ; //X plays first
	///////////
	
	protected $status = StatusEnum::Begining;
	protected $player1;
	protected $player2;	
	protected $winner = 0;
	private $state_status = 0;
	private $state_round = 0;
	
	private $winn_line =[];
	
    /**
     * Game constructor: a game object to be played
     * @param array $dataGame: the data game includes properties
     */
    public function __construct($dataGame)
    {
        $this->CI =& get_instance();
		$this->CI->load->model('game_model');
		$this->CI->load->model('game_state_model');  
		
		$this->fillGameData($dataGame);
		
		//////////////////////
		//Set State
	}
    /*
     * private function fill game object properties
     * @param array $dataGame : game data array
     */
	private function fillGameData($dataGame){
		$this->game_id = $dataGame['game_id'];
		if (array_key_exists("gameType_id",$dataGame)) $this->game_type = $dataGame['gameType_id'];
		if (array_key_exists("player1_id",$dataGame)) $this->player1 = $dataGame['player1_id'];
		if (array_key_exists("player2_id",$dataGame)) $this->player2 = intval($dataGame['player2_id']);
		if (array_key_exists("status",$dataGame)) $this->status = intval($dataGame['status']);
		if (array_key_exists("winner_id",$dataGame)) $this->winner = $dataGame['winner_id'];
		
		if (array_key_exists("board",$dataGame)) $this->board = $dataGame['board'];
		if (array_key_exists("state_status",$dataGame)) $this->state_status = $dataGame['state_status'];
		
		if (array_key_exists("state_round",$dataGame)) $this->state_round = intval($dataGame['state_round']);
		if (array_key_exists("winn_line",$dataGame)) $this->winn_line = $dataGame['winn_line'];

			
	}
	
	private function set_CurrentState(){
		$currentstate_date=[
					"board"=>$this->board,
					"turn"=>$this->turn,
					"players"=> ["X"=>$this->player1, "O"=>$this->player2],
					"oMovesCount"=> 0,
					"status"=>$this->state_status,
					"round"=> $this->state_round,
					"winn_line" => $this->winn_line
				];
		$this->currentState = new State((object) $currentstate_date);
	}
    /*
     * starts the game
     */
    public function start() {
        if($this->status == StatusEnum::Begining) {
			//echo "Start";
			// 1 Insert new State in DB
			$data_state=[
						'game_id'=> $this->game_id,
						'board' => $this->board,
						'round' => 0,
						'turn'	=> "X",
						'player_turn'=> $this->player1,
						'winn_line'=> $this->winn_line
						];
			/*************************************/
			$state_id= $this->CI->game_state_model->insert($data_state);
			/*************************************/
			
			//echo $state_id . "<br>";
			//2 Set the currentstate
			$this->set_CurrentState();
			
            //3- invoke advanceTo with the initial state
            $this->advanceTo($this->currentState);            
            $this->status = StatusEnum::Running;
            
            //4- update game status
			$date_game=[
						'game_id'=> $this->game_id,
						'status'=> $this->status
						];
			/*************************************/
			
			$this->CI->game_model->set_status($date_game);
			//echo "<br/> Status ";
			$this->state_status= StateEnum::InProgress;
			$date_game=[
						'game_id'=> $this->game_id,
						'status'=> $this->state_status
						];			
			$this->CI->game_state_model->set_status($date_game);			
			/*************************************/
        }else{
			//load State DB
		}
    }
    public function humanPlay($player_id, $pos, $symbol){
		//1- Get game State
		$data= $this->CI->game_state_model->get_game_state($this->game_id);
		/*
		echo "game Data";
		echo "<pre>";
		print_r($data);
		echo "</pre>" ;
		*/
		
		//2- Fill data Game
		$data->board  = json_decode($data->board);
		if (property_exists($data, "winn_line")) $data->winn_line = json_decode($data->winn_line);
		
		//Call fillGameData by convert object data to array data
		$this->fillGameData(json_decode(json_encode($data), True));
		/*
		echo "<br>game Board";
		echo "<pre>";
		print_r($this->board);
		echo "</pre>" ;
		*/
		
		//2 prepare current State
		$this->set_CurrentState();
		/*
		echo "<br>game currentState";
		echo "<pre>";
		print_r($this->currentState);
		echo "</pre>" ;
		*/
		//3 Check Terminate 
		if(!$this->currentState->isTerminal() && ($this->board[$pos]=="") && ($this->status !== StatusEnum::Ended)){
			//Change Board
			$this->turn= $symbol;
			$this->currentState->turn = $this->turn;
			$this->board[$pos] = $this->turn;
			$this->currentState->board[$pos] = $this->board[$pos];
			//Change turn
			$this->currentState->changeTurn();
			$this->turn= $this->currentState->turn;
			
			if($this->currentState->isTerminal()){
				$this->status = StatusEnum::Ended;
				/*************************************/
				//if there winner set the winner 
				switch (intval($this->currentState->status)) { 
					case StateEnum::Player1Wins :
						$this->winner=$this->player1;
						break;
					case StateEnum::Player2Wins :
						$this->winner=$this->player2;
						break;
					default:
						$this->winner= 0;
						break;
				}
				$date_game=[
							'game_id'=> $this->game_id,
							'status'=> $this->status,
							'winner_id' =>$this->winner
							];
				$this->CI->game_model->set_terminate($date_game);
				/*************************************/
			}

			return true;
		}
		return false;
	}
    
    /*
     * public function that advances the game to a new state
     * @param _state [State]: the new state to advance the game to
     */
    public function advanceTo(State $state){
		$this->currentState = $state;
		if($this->currentState->isTerminal()){
			
            $this->status = StatusEnum::Ended;
            
            //1- update game status
			$date_game=[
				'game_id'=> $this->game_id,
				'status'=> $this->status,
				'winner_id'=> 0
			];
			
            if(intval($state->status) == StateEnum::Player1Wins){
				//Player1 winn
				$date_game["winner_id"]= $this->player1;
				/*************************************/
                $this->CI->game_model->set_winn($date_game);
                /*************************************/
                
            }else if(intval($state->status) === StateEnum::Player2Wins){
				//Player2 winn
				$date_game["winner_id"]= $this->player2;
				/*************************************/
                $this->CI->game_model->set_winn($date_game);
                /*************************************/
            }else{
				//Draws
				/*************************************/
				$this->CI->game_model->set_status($date_game);
				/*************************************/
            }
		}else {
			/*
			echo '<br> Game currentState->turn: '. $this->currentState->turn ;
			echo '<br> Game currentState->emptyCells: ';
			echo "<pre>";
			print_r($this->currentState->emptyCells());
			echo "</pre>" ;
			*/
            //the game is still running
            if($this->currentState->turn === "X") {
                //ui.switchViewTo("human");
            }
            else {
                //ui.switchViewTo("robot");

                //notify the AI player its turn has come up
                //this.ai.notify("O");
            }
		}
	}
}
