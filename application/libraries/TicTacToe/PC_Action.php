<?php
namespace libraries\TicTacToe;

use libraries\TicTacToe\State;
/**
 * PC Action it need for PC Player decision making and moves, and it holds two information: 
 * - the position on the board that it’ll make its move on (remember that it’s a one-dimensional array index) and
 * - the minimax value of the state that this action will lead to (remember the minimax function ?). 
 *   This minimax value will be the criteria at which the PC Player will chose its best available action. 
 */
class PC_Action{

    /**
     * @var _movePosition : the position on the board that the action would put the letter on
     */	
	public $movePosition;
	
    /**
     * @var movePosition : the minimax value of the state that the action leads to when applied
     */		
	public $minimaxVal=0;
	
    /**
     * PC_Action constructor: an action that the pc player could make
     * @param int $pos: the cell position the ai would make its action in made that action
     */
	public function __construct($pos)
    {
        $this->CI =& get_instance();
		$this->CI->load->model('game_model');
		$this->CI->load->model('game_state_model'); 
		
		$this->movePosition=$pos;
	}

    /*
     * public : applies the action to a state to get the next state
     * @param state [State]: the state to apply the action to
     * @return [State]: the next state
     */
    public function applyTo(State $state){
		$next = new State($state);
		
		//put the letter on the board
		$next->board[$this->movePosition] = $state->turn;
		
		if($state->turn === "O")    $next->oMovesCount++;
		
		$next->changeTurn();
		return $next;
	}
}
