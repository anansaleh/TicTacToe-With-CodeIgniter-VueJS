<?php

namespace libraries\TicTacToe;

use libraries\TicTacToe\State;
use libraries\TicTacToe\PC_Player;

use libraries\TicTacToe\enums\StatusEnum;
use libraries\TicTacToe\enums\StateEnum;
use libraries\TicTacToe\enums\GameTypeEnum;
use libraries\TicTacToe\enums\PcLavelEnum;

/*
 * The Game Class
 * This is the structure that will control the flow of the game and glue everything together in one functioning unit. 
 * It keeps and access three kinds of information : 
 * - the human who plays the game with other human or with the PC Player, 
 * - the current state of the game, and 
 * - the status of the game (whether it’s running or ended), 
 */
 
class Game{
    /**
     * @var $game_id : the game_id for this game.
     */	 
	protected $game_id;
	
    /**
     * @var $game_type : the game_type for this game.
     */
	public $game_type=1;
	
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
     * @var $level : Used when Singleplayer to inform the Pc-Player Level.
     */
	public $level=PcLavelEnum::Blind;
	public $score=0;
	public $oMovesCount = 0;
    /**
     * Game constructor: a game object to be played
     * @param array $dataGame: the data game includes properties
     */
    public function __construct($data)
    {
        $this->CI =& get_instance();
		$this->CI->load->model('game_model');
		$this->CI->load->model('game_state_model');  
		
		$this->fillGameData($data);
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
		
		if (array_key_exists("level",$dataGame)) $this->level = $dataGame['level'];
		if (array_key_exists("oMovesCount",$dataGame)) $this->oMovesCount = $dataGame['oMovesCount'];
	}
	
	private function set_CurrentState(){
		$currentstate_date=[
					"board"=>$this->board,
					"turn"=>$this->turn,
					"players"=> ["X"=>$this->player1, "O"=>$this->player2],
					"oMovesCount"=> 0,
					"status"=>$this->state_status,
					"round"=> $this->state_round,
					"winn_line" => $this->winn_line,
					"oMovesCount" => $this->oMovesCount,
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
						"status"=>$this->state_status,
						'player_turn'=> $this->player1,
						'winn_line'=> $this->winn_line,
						"oMovesCount" => $this->oMovesCount,
						];
			/*************************************/
			$state_id= $this->CI->game_state_model->insert($data_state);
			/*************************************/
			
			//echo $state_id . "<br>";
			//2 Set the currentstate
			$this->set_CurrentState();
			
            //3- check terminal
            //TODO : PC can play O or X
            //$this->advanceTo($this->currentState);
            if(!$this->checkTerminate($this->currentState)){            
				$this->status = StatusEnum::Running;
            }
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

		
		//2- Fill data Game
		$data->board  = json_decode($data->board);
		if (property_exists($data, "winn_line")) $data->winn_line = json_decode($data->winn_line);
		
		//Call fillGameData by convert object data to array data
		$this->fillGameData(json_decode(json_encode($data), True));
		
		//2 prepare current State
		$this->set_CurrentState();

		//3 Check if player can turn then turn
		if(!$this->currentState->isTerminal() && ($this->board[$pos]=="") && ($this->status !== StatusEnum::Ended)){
			//Change Board
			$this->turn= $symbol;
			$this->currentState->turn = $this->turn;
			$this->board[$pos] = $this->turn;
			$this->currentState->board[$pos] = $this->board[$pos];
			//Change turn
			$this->currentState->changeTurn();
			$this->turn= $this->currentState->turn;
			/////////////////////////////////////////////
			
			$flag= $this->checkTerminate($this->currentState);
			
			//Check Change To PC-Player
			//$this->advanceTo($this->currentState);
			return true;
		}
		return false;
	}
	
	/*
	 */
    private function checkTerminate(State $state){
		$this->currentState = $state;
		if($this->currentState->isTerminal()){
			$this->status = StatusEnum::Ended;
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
			return true;
		}else{
			return false;
		}
	}
    /*
     * public function that advances the game to a new state
     * @param _state [State]: the new state to advance the game to
     */
    public function advanceTo(State $state){
		$this->currentState = $state;
		if(!$this->checkTerminate($this->currentState)){
			//the game is still running
			if($this->currentState->turn === "X") {
				//Human next Player
			}else{
				switch (intval($this->game_type)) { 
					case GameTypeEnum::Singleplayer:
					
						//Start Pc Player
						//$pc_palyer = new PC_Player($this);
						//$pc_palyer->play($this->currentState->turn);
						
						
						break;
					case GameTypeEnum::Multiplayer:
					default:
						break;
				}
			}
		}
	}	
	/*
	 * public static function that calculates the score of the x player in a given terminal state
	 * @param _state [State]: the state in which the score is calculated
	 * @return [Number]: the score calculated for the human player
	 */
	public function score(State $state) {
		if($state->status === StateEnum::Player1Wins){
			// the x player won
			return 10 - $state->oMovesCount;
		}
		else if($state->status === StateEnum::Player2Wins) {
			//the x player lost
			return - 10 + $state->oMovesCount;
		}
		else {
			//it's a draw
			return 0;
		}
	}
    /* TODO
     * public function that advances the game to a new state
     * @param _state [State]: the new state to advance the game to
     */
    public function advanceTo_old(State $state){
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
				/////////
                $this->CI->game_model->set_winn($date_game);
                
            }else if(intval($state->status) === StateEnum::Player2Wins){
				//Player2 winn
				$date_game["winner_id"]= $this->player2;
				/////////
                $this->CI->game_model->set_winn($date_game);
            }else{
				//Draws
				///////////
				$this->CI->game_model->set_status($date_game);
            }
		}else {
            //the game is still running
            if($this->currentState->turn === "X") {
                //ui.switchViewTo("human");
            }
            else {
                //ui.switchViewTo("robot");

                //notify the AI player its turn has come up
                //this.ai.play("O");
            }
		}
	}
}
