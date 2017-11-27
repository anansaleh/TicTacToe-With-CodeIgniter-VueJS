<?php
//https://codepen.io/jaycbrf/pen/iBszr
?>
<style>
.modal-header, h4, .close {
  background-color: #5cb85c;
  color:white !important;
  text-align: center;
  font-size: 30px;
}
.modal-footer {
  background-color: #f9f9f9;
}
</style>
<div class="container"  id="new_game_controller">
	<div class="page-header">
		<h1>Tic Tac Toe</h1>      
	</div>	
	<div class="page-header">
		<h3><?php echo $title; ?></h3>      
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
			  <label class="col-md-1 control-label"></label>
			  <div class="col-md-11">
				<button type="button" class="btn btn-success" v-on:click="show_new_player_modal()">New player</button>
			  </div>
			</div>		
		</div>
	</div>
	<div class="alert alert-danger fade in alert-dismissable errorPlayer"  v-show="showErrorGame">
		<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">&times;</a>
		<strong>Warning!</strong> {{error_game}}
	</div>
	<form action="javascript:void(0);"  class="form-horizontal" id="new-game-form">
		<fieldset>
			<div class="form-group">
				<label class="col-md-3 control-label">Select Game type:</label>
				<div class="col-md-6">
					<div class="radio" v-for="type in gameType">
						<label>
							<input type="radio" name="gameType" v-bind:value="type['gameType_id']"  v-model="selected_gameType">{{ type['gameType_name'] }}
						</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Player 1 E-mail</label>  
				<div class="col-md-8 inputGroupContainer">
					<div class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
						<input name="player1" placeholder="Player 1 E-Mail Address" class="form-control"  type="text" v-model="player1">
					</div>
				</div>
			</div>
			<div class="form-group" v-if="selected_gameType ==='2'">
				<label class="col-md-3 control-label">Player 2 E-mail</label>  
				<div class="col-md-8 inputGroupContainer">
					<div class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
						<input name="player2" placeholder="Player 2 E-Mail Address" class="form-control"  type="text" v-model="player2">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">Game Level:</label>
				<div class="col-md-6">
					<div class="radio" v-for="(value, key)  in gameLevel" v-if="selected_gameType === '1'">
						<label>
							<input type="radio" name="game_level"  v-bind:value="key" v-model="selected_Level">{{ value[key] }}
						</label>
					</div>
				</div>
			</div>			
			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<button type="button" class="btn btn-primary" v-on:click="startNewGame()">Start Play new game</button>
				</div>
			</div>
		</fieldset>
 
	</form>
	<!-- Modal -->
	<div id="newPlayerModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add new player</h4>
		  </div>
		  <div class="modal-body">
			<div class="alert alert-danger fade in alert-dismissable errorPlayer"  v-show="showErrorPlayer">
				<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">&times;</a>
				<strong>Warning!</strong> {{error_player}}
			</div>

			 <form class="well form-horizontal" id="addPlayer"  action="javascript:void(0);">
				 <fieldset>
					<div class="form-group">
					  <label class="col-md-3 control-label">Player Name</label>  
					  <div class="col-md-8 inputGroupContainer">
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
							<input  name="player_name" placeholder="Player Name" class="form-control"  type="text" v-model="newPlayer.name">
						</div>
					  </div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">E-Mail</label>  
						<div class="col-md-8 inputGroupContainer">
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
								<input name="player_email" placeholder="E-Mail Address" class="form-control"  type="text" v-model="newPlayer.email">
							</div>
						</div>
					</div>
					<!-- Button -->
					<div class="form-group">
					  <label class="col-md-3 control-label"></label>
					  <div class="col-md-8">
						<button type="button" class="btn btn-warning" v-on:click="form_palyer_Submit()">Save</button>
					  </div>
					</div>
				</fieldset>
			</form> 
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url() ?>js/vue.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/vue-resource.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/app_home.js"></script>		
