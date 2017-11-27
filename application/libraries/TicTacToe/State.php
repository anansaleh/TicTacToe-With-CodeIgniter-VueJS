<?php

namespace libraries\TicTacToe;

use libraries\TicTacToe\enums\StateEnum;
/**
 * State Class
 * Represents a state in the game
 * The State represent a certain configuration of the grid board, 
 * it has all informations that need to be associated with state of the board and the board configuration, 
 * like who’s turn is it, the result of the game at this state is (whether it’s still running, somebody won, or it’s a draw), 
 * and how many round the players have made.
 */
class State{
    /**
     * @var $turn : the player who has the turn to player.
     */	     
    public $turn = "X";

    /**
     * @var $oMovesCount : the number of moves of the PC player.
     */	      
    public $oMovesCount = 0;

    /**
     * @var $status : the status of the game in this State.
     */	       
    //public $status = "still running";
    public $status = StateEnum::InProgress;
    
    /**
     * @var $winn_line : list of possitions of winn line.
     */	    
    public $winn_line=[];

    /**
     * @var $board : the board configuration in this state.
     */	      
    public $board = ["", "", "", "", "", "", "", "", ""];

    /**
     * @var $players : array list with players id's.
     */	      
    public $players = ["X"=>0, "O"=>0];
    
    /**
     * State constructor: Represents a state in the game
     * @param State $old_state: old state to intialize the new state
     */	 
    public function __construct($old_state = NULL)
    {
        $this->CI =& get_instance();
		$this->CI->load->model('game_model');
		$this->CI->load->model('game_state_model');   
		     
        //echo "State is created";
        if ($old_state !== NULL){
			//Load board
			for($i=0; $i<count($old_state->board); $i++){
				$this->board[$i]=$old_state->board[$i];
			}
			
			$this->players["X"] = $old_state->players["X"];
			$this->players["O"] = $old_state->players["O"];
			
			$this->oMovesCount = $old_state->oMovesCount;
			$this->status = $old_state->status;
			$this->turn = $old_state->turn;
			$this->winn_line = $old_state->winn_line;
			$this->winn_line = $old_state->oMovesCount;
		}
    }

    /**
     * public function: set the turn in a the state
     * 
     * @return void
     */
    /*************************************/
    //TO DO Change name function to advanceTurn
    /*************************************/
    public function changeTurn(){
		$this->turn = ($this->turn === "X") ? "O" : "X";
	}

    /**
     * private function: enumerates the empty cells in state
     * 
     * @return array: indices of all empty cells
     */     
	public function emptyCells(){
		$indxs = [];
        for($i = 0; $i < 9 ; $i++) {
            if($this->board[$i] === "") {
                array_push($indxs, $i);
            }
        }
        return $indxs;
	}
     
    /**
     * private function: set the result of winn Char in the state
     * 
     * @param string $winn_char: winn char "X" or "O"
     * 
     * @return void
     */
    private function setResultWinn($winn_char){
		$this->status = ($winn_char === "X") ? StateEnum::Player1Wins : StateEnum::Player2Wins;
	}
	     
    /**
     * private function: checks if the state is a terminal state or not
     * the state status is updated to reflect the result of the game
     * 
     * @return Boolean: true if it's terminal, false otherwise
     */      
     public function isTerminal(){
		/*
		 * Board
		 * 0 | 1 | 2
		 * 3 | 4 | 5
		 * 6 | 7 | 8
		 */
        $myBord = $this->board;

        //check rows board
		/* [0,1,2] , 
		 * [3,4,5] , 
		 * [6,7,8]
		 */
        for( $i = 0; $i <= 6; $i+= 3) {
            if($myBord[$i] !== "" && $myBord[$i] === $myBord[$i + 1] && $myBord[$i + 1] == $myBord[$i + 2]) {
				//update the state status
				/////////////////////
				
                //$this->status = $myBord[i] + "-won"; 
                $this->setResultWinn( $myBord[$i] );
                
                /////////////////////
                $this->winn_line= [ $i ,  $i + 1,  $i+ 2];
                return true;
            }
        }

		 // Check cols board
		/* [0,3,6] , 
		 * [1,4,7] , 
		 * [2,5,8]
		 */
        for ($i = 0; $i <= 2; $i++){
            if($myBord[$i] !== "" && $myBord[$i] === $myBord[$i + 3] && $myBord[$i + 3] == $myBord[$i + 6]) {
				//update the state status
                /////////////////////
                
                //$this->status = $myBord[i] + "-won"; 
                $this->setResultWinn( $myBord[$i] );
                
                /////////////////////
                $this->winn_line= [ $i ,  $i + 3,  $i+ 6];
                return true;
            }
        }
        
		 //Check diagonals board
		/* [0,4,8] , 
		 * [2,4,6] , 
		 */
        for ($i = 0, $j =4; $i <= 2; $i+=2, $j-=2){
            if($myBord[$i] !== "" && $myBord[$i] == $myBord[$i + $j] && $myBord[$i + $j] === $myBord[$i + 2*$j]) {
				//update the state status
				/////////////////////
				
				//$this->status = $myBord[i] + "-won";
				$this->setResultWinn( $myBord[$i] );
				
				/////////////////////
                $this->winn_line = array( $i , $i + $j, $i + 2*$j);
                return true;
            }
		}
		
		$available = $this->emptyCells();
        if(sizeof($available) == 0) {
			//update the state status
            //the game is draw
            /////////////////////
            
            //$this->status = "draw";
            $this->status =StateEnum::Draw;
            
            /////////////////////
            return true;
        }
        else {
			$this->status =StateEnum::InProgress;
            return false;
        }
                
	 }
}
