var new_game_controller = new Vue({
	el: '#gameBoard'
	,data: {
		gameId: 0,
		board:["","","","","","","","",""],
		turn: 'X',
		game_state:'',
		game_score:[],
		winn_line:[]
	}
	,created: function () {
		this.gameId=$("#gameBoard").data("gameId");
		this.getGameState();
		for(var i =0; i<9; i++){
			this.board.push("");
		}
		//console.log(this.board.length + 'PP');
		this.getGameScore();

	}  
	,mounted: function () {
		var self=this;
		this.$nextTick(function () {

		})
	}
	, computed: {
		stateStatus: function () {
			 switch(parseInt(this.game_state["state_status"])) {
				case 0:
					return "New Game";
					break;
				case 1:
					return "In Progress";
					break;
				case 2:
					return "Player1 Wins";
					break;
				case 3:
					return "Player2 Wins";
					break;
				case 4:
					return "Draw";
					break;
				default:
					return "undefine";
			} 
		}
		, gameStatus: function () {
			 switch(parseInt(this.game_state["status"])) {
				case 0:
					return "Begining";
					break;
				case 1:
					return "Running";
					break;
				case 2:
					return "Ended";
					break;
				case 3:
					return "Not Completed";
					break;
				default:
					return "undefine";
			}
		}
		, game_in_progress: function () {
			return (parseInt(this.game_state["state_status"]) < 2);
		}		
	}
	,methods: {
		getGameState: function () {
			let game = { "game_id": this.gameId };
			this.$http.post('get_state', game).then (function(request) {
					this.game_state = request.body;
					this.board= this.game_state['board'];
				}, function () {
				alert ('No game type found..');
			});
		}
		, play: function (pos) {
			let self = this;
			let game = { 
					"game_id":  this.gameId 
					, "player":  this.game_state.player_turn 
					, "possition": pos
					, "turn": this.game_state.turn
					};
			this.$http.post('play', game).then (function(request) {
				
					if( request.body["success"] === true){
						//$("#"+pos).text(self.game_state.turn);
						self.getGameState();
					}else{
						alert ('No game type found..');
					}

				}, function () {
				alert ('No game type found..');
			});
		}
		,boxClick(id){
			console.log(id);
			//console.log(this.board.length + 'PP');
			if(parseInt(this.game_state['status']) < 2){
				this.play(id);
			}
		}
		, getGameScore: function () {
			this.$http.get('scores').then (function(request) {
					this.game_score = request.body;
				}, function () {
				alert ('No game type found..');
			});
		}
		,is_inWinnLine(arr, id){
			//console.log('winn: ' + id);
			if(Array.isArray(arr)){
				return (arr.indexOf(id) > -1);
			}
			return false;
		}
		,playAgain(){
			let self = this;
			let game = { 
					"game_id":  this.gameId 
					, "player1":  this.game_state.player1_id
					};
			this.$http.post('play-again', game).then (function(request) {
				
					if( request.body["success"] === true){
						//initial data for the new game
						
						self.gameId= 0;
						self.board=["","","","","","","","",""];
						self.turn= 'X';
						self.game_state='';
						self.game_score=[];
						self.winn_line=[];
						
						self.gameId=request.body["game_id"];
						
						self.getGameState();
						self.getGameScore();

					}else{
						alert ('No game type found..');
					}

				}, function () {
				alert ('No game type found..');
			});
		}
		,newGame(){
			window.location.href = "/";
		}
	} 
});
