<?php
namespace libraries\TicTacToe;

use libraries\TicTacToe\Game;
use libraries\TicTacToe\State;
use libraries\TicTacToe\PC_Action;
use libraries\TicTacToe\enums\PcLavelEnum;

/**
 * The PC Player: can play Tic-Tac-Toe at three difficulty levels: 
 * - Blind level: in which the PC understands nothing about the game, 
 * - Novice level in which the PC Player plays the game as a novice player, and the
 * - Master level in which the PC Player plays the game like a master you can never beat no matter how much you tried.
 */
class PC_Player{
	
    /**
     * @var game : the game object that the PC_Player is playing
     */
    private $game ;
    

    /**
     * PC_Player constructor: a PC player with a specific level of intelligence
     * @param string $level: the desired level of intelligence
     */
	public function __construct(Game $game)
    {
        $this->CI =& get_instance();
		$this->CI->load->model('game_model');
		$this->CI->load->model('game_state_model'); 
		
		$this->game = $game;
	}
	
    /**
     * private recursive function that computes the minimax value of a game state
     * 
     * @param State $state: the state to calculate its minimax value
     * 
     * @return int: the minimax value of the state
     */
	private function minimaxValue(State $state)
	{

        if($state->isTerminal()) {
            //a terminal game state is the base case
            return $this->game->score($state);
        }else{
			$stateScore = 0; // this stores the minimax value we'll compute
			if($state->turn === "X"){
				// X wants to maximize --> initialize to a value smaller than any possible score
                $stateScore = -1000;
			}else{
				// O wants to minimize --> initialize to a value larger than any possible score
                $stateScore = 1000;
            }
            $availablePositions = $state->emptyCells();
            
            //enumerate next available states using the info form available positions
			$availableNextStates= array_map( function( $pos) use ($state) {
					  $action = new PC_Action($pos);
					  $nextState = $action->applyTo($state);
					  return $nextState;
					}, $availablePositions ) ; 
					
            /* calculate the minimax value for all available next states
             * and evaluate the current state's value */
			array_walk($availableNextStates, function($nextState) use ($state, $stateScore) {
						$nextScore = $this->minimaxValue($nextState);
						if($state->turn === "X") {
							// X wants to maximize --> update stateScore iff nextScore is larger
							if($nextScore > $stateScore)
								$stateScore = $nextScore;
						}
						else {
							// O wants to minimize --> update stateScore iff nextScore is smaller
							if($nextScore < $stateScore)
								$stateScore = $nextScore;
						}				
					});
			return $stateScore;
		}
	}
	
    /**
     * private function: make the pc player take a blind move
     * that is: choose the cell to place its symbol randomly
     * 
     * @param string $turn: the pc player to play, either X or O
     * 
     * @return void
     */
    private function takeABlindMove($turn) {
		//echo "<br>takeABlindMove: $turn";
        $available = $this->game->currentState->emptyCells();
		      
        //Get random between (0...1); 
        $rand=mt_rand() / mt_getrandmax();
        $randomCell = floor($rand * count($available));
        //echo "<br> randomCell: $randomCell";
        
        $action = new PC_Action($available[$randomCell]);
		//echo "<br> action: $randomCell";
        $next = $action->applyTo($this->game->currentState);
		//$this->game->advanceTo($next);
		
        return ["possition" =>$available[$randomCell], "turn"=>$turn];

    }
    
    /**
     * private function: make the pc player take a novice move
     * that is: mix between choosing the optimal and suboptimal minimax decisions
     * 
     * @param string $turn: the pc player to play, either X or O
     * 
     * @return void
     */
    private function takeANoviceMove($turn){
		$available = $this->game->currentState->emptyCells();
		
        //enumerate and calculate the score for each available actions to the ai player
		$availableActions= array_map( function( $pos ) {
					//create the action object
					$action = new PC_Action($pos);

					//get next state by applying the action
					$nextState = $action->applyTo($this->game->currentState);
					
					//calculate and set the action's minimax value
					$action->minimaxVal= $this->minimaxValue($nextState);
					return $action;
				}, $available ) ; 
		
        //sort the enumerated actions list by score
        if($turn === "X"){
			//X maximizes --> sort the actions in a descending manner to have the action with maximum minimax at first
            rsort($availableActions);
        }else{
			//O minimizes --> sort the actions in an ascending manner to have the action with minimum minimax at first
            sort($availableActions);
        }
        
        /*
         * take the optimal action 40% of the time, and take the 1st suboptimal action 60% of the time
         */
        $chosenAction;
        if(mt_rand(1, 100) <= 40) {
            $chosenAction = $availableActions[0];
        }
        else {
            if(count($availableActions) >= 2) {
                //if there is two or more available actions, choose the 1st suboptimal
                $chosenAction = $availableActions[1];
            }
            else {
                //choose the only available actions
                $chosenAction = $availableActions[0];
            }
        }
        $next = $chosenAction->applyTo($this->game->currentState);
		//$this->game->advanceTo($next); 
		//return array($chosenAction->movePosition, $turn);               
		return ["possition" =>$chosenAction->movePosition, "turn"=>$turn];
	} 
	
    /**
     * private function: make the pc player take a master move
     * that is: choose the optimal minimax decision
     * 
     * @param string $turn: the pc player to play, either X or O
     * 
     * @return void
     */     
    private function takeAMasterMove($turn) {
        $available = $this->game->currentState->emptyCells();

        //enumerate and calculate the score for each avaialable actions to the ai player
		$availableActions= array_map( function( $pos ) {
					//create the action object
					$action = new PC_Action($pos);
					
					//get next state by applying the action
					$next = $action->applyTo($this->game->currentState); 
					
					//calculate and set the action's minmax value
					$action->minimaxVal = $this->minimaxValue($next); 
					return $action;
				}, $available ) ; 

        //sort the enumerated actions list by score
        if($turn === "X")
			//X maximizes --> sort the actions in a descending manner to have the action with maximum minimax at first
            rsort($availableActions);
        else
			//O minimizes --> sort the actions in an ascending manner to have the action with minimum minimax at first
            sort($availableActions);


        //take the first action as it's the optimal
        $chosenAction = $availableActions[0];
        $next = $chosenAction->applyTo($this->game->currentState);
		//$this->game->advanceTo($next);
        //ui.insertAt(chosenAction.movePosition, turn);
        return ["possition" =>$chosenAction->movePosition, "turn"=>$turn];

        
    }

    /**
     * public function: notify the pc player that it's its turn
     * 
     * @param string $turn: the player to play, either X or O
     * 
     * @return void
     */       
    public function play($turn) {
		if (!$this->game->currentState->isTerminal()){
			//echo "<br>$turn";
			//echo "<br>" .$this->level;
			switch($this->game->level) {
				//invoke the desired behavior based on the level chosen
				case PcLavelEnum::Blind :  
					return $this->takeABlindMove($turn); 
					break;
				case PcLavelEnum::Novice : 
					//echo "<br>Novice";
					return $this->takeANoviceMove($turn); 
					break;
				case PcLavelEnum::Master: 
					return $this->takeAMasterMove($turn); 
					break;
			}
		}else{
			return FALSE;
		}
    }
}
