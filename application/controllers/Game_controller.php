<?php
use libraries\TicTacToe\State;
use libraries\TicTacToe\Game;
use libraries\TicTacToe\PC_Player;

class Game_controller extends CI_Controller
{
	private $a;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->helper('url');
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');

        // Load session library
        $this->load->library('session');
        $this->load->model('game_model');
        $this->load->model('game_state_model');
    }
    public function load()
    {
		$game_id= $this->uri->segment('3');
		echo $game_id;
		if(!intval($game_id) > 0){
			echo "Game not found .......";
			show_404();
			exit();
		}
		//Get Game Data
		$data= $this->game_model->get_game($game_id);
		/*
		echo "game Data";
		echo "<pre>";
		print_r($data);
		echo "</pre>" ;
		//exit();
		*/
		
		$game= new Game($data);
		$game->start();
		
		//exit();
		
        // Save in session
        $this->session->unset_userdata('current_game');
        
        $this->session->set_userdata('current_game', $game_id);
        redirect('game/start');

    }   
    public function start_game(){
		//json_decode(json_encode($data), True)
        $game_id= $this->session->userdata('current_game');
        
        if ($game_id!== NULL) {
			//Get Game Data
			$data= $this->game_model->get_game($game_id);
			//$data= json_decode(json_encode($data), True);
			/*
			echo "game Data";
			echo "<pre>";
			print_r($data);
			echo "</pre>" ;
			*/
			
			$game= new Game($data);
			$game->start();
			
			
			$data['title'] = 'Start Game';
			$this->load->view('templates/header', $data);
			$this->load->view('game.php', $data);
			$this->load->view('templates/footer');		

        } else {
            redirect('new-game');
        }
    }     
}
