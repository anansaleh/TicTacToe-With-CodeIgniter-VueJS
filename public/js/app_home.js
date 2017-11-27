var new_game_controller = new Vue({
	el: '#new_game_controller',
   data: {
      loading_page: true,
      gameType: [],
      selected_gameType:"1", //Single
      gameLevel: [
				{"0":"Blind"},
				{"1": "Novice"},
				{"2": "Master"}
				],
      selected_Level:"0",
	  player1:'',
	  player2:'',      
      newPlayer:{
		name:'',
		email:'',
	  },
	  showErrorPlayer: false,
	  error_player:'',
	  showErrorGame: false,
	  error_game:'',
   },
   created: function () {
      this.getGameType();
   },   
	mounted: function () {
		var self=this;
		this.$nextTick(function () {
			self.draw_game_form();
			self.draw_new_player_form();			
		})
	},
   methods: {
		getGameType: function () {
			this.$http.get('get_game_type').then (function(request) {
				this.gameType = request.body;
				}, function () {
				alert ('No game type found..');
			});
		}
		, createNewPlayer(){
			 this.$http.post('add_player', this.newPlayer).then(function(request){
				//console.log(request);
				if( request.body["success"] === true){
					this.newPlayer.name = '';
					this.newPlayer.email = '';
					$('#newPlayerModal').modal('hide');
				}else{				  
				  this.error_player=request.body["error_message"];
				  this.showErrorPlayer= true;
				}
			 }, function(){
				alert('No se ha podido crear la tarea.');
			 });
		  }
		, createNewGame(){
			 let game ={
				gameType: this.selected_gameType,
				player1:  this.player1,
				player2:  this.player2,
				level:	  this.selected_Level
			 }			 
			 this.$http.post('game/create', game).then(function(request){
				//console.log(request);
				if( request.body["success"] === true){
					console.log('Game creted: ' + request.body["game_id"]);
					//window.location = "play/game/" + request.body["game_id"];
					window.location.href = "game/load/" + request.body["game_id"];
				}else{				  
				  this.error_game=request.body["error_message"];
				  this.showErrorGame= true;
				}
			 }, function(){
				alert('The game could not be created.');
			 });
		}
		, show_new_player_modal(){
			this.error_player='';
			this.showErrorPlayer= false;
			$('#addPlayer').bootstrapValidator('destroy');
			this.draw_new_player_form();
			$("#newPlayerModal").modal({backdrop: "static"});
		}
		, startNewGame(){
			//$('#new-game-form').submit();
			$('#new-game-form').bootstrapValidator('validate');
		}

		,form_palyer_Submit(){
			$('#addPlayer').submit();
			//$('#myModal').modal('toggle'); 
			//$('#myModal').modal('hide');
		}
		, draw_game_form(){
			var self =this;
			$('#new-game-form')
				.bootstrapValidator({
					message: 'This value is not valid',
					// To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					fields: {
						player1:{
							validators: {
								notEmpty: {
									message: 'Please supply your email address'
								},
								emailAddress: {
									message: 'Please supply a valid email address'
								}
							}							
						}
						,player2:{
							validators: {
								callback: {
									message: 'Please supply a valid email address',
									callback: function(value, validator, $field) {
										console.log('validators');
										//return true;
										var gameType = $('#new-game-form').find('[name="gameType"]:checked').val();
										var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
										return (gameType !== '2') ? true : re.test(value);
									}
								}
							}
						}
					}
				})
				.on('success.form.bv', function (e) {
					e.preventDefault();
					//console.log('Start new game');
					self.createNewGame();
				});
		}
		, draw_new_player_form(){
			var self =this;
			$('#addPlayer')
				.bootstrapValidator({
					// To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					fields: {
						player_name: {
							validators: {
									stringLength: {
									min: 4,
								},
									notEmpty: {
									message: 'Please supply your first name'
								}
							}
						}
						, player_email: {
							validators: {
								notEmpty: {
									message: 'Please supply your email address'
								},
								emailAddress: {
									message: 'Please supply a valid email address'
								}
							}
						}
					}				
				})
				.on('success.form.bv', function (e) {
					e.preventDefault();
					/*
					console.log('in');

					// Get the form instance
					var $form = $(e.target);
					
					// Get the BootstrapValidator instance
					let validator = $form.data('bootstrapValidator');

					let player={
						name : validator.getFieldElements('player_name').val(),
						email: validator.getFieldElements('player_email').val()
					}
					console.log(player);
					*/
					self.createNewPlayer();
				});
		}

	
   } 
});
