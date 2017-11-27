<?php

?>

<div class="container-fluid">
    <div class="row mainpanel"id="gameBoard" data-game-id="<?=$game_id?>">
        <div class="col-md-8 leftpanel">
				<div class="row">
					<div class="col-md-12">
						<div  class="page-header">
							<h1>Tic Tac Toe</h1>
							<h2 class="status">{{ stateStatus }}</h2>
							<div class="row justify-content-center">
								<div class="col-md-5 player">
									<h2>{{game_state['player1_name']}} [ X ]</h2>
								</div>
								<div class="col-md-2 vs-player"><h2>VS</h2></div>
								<div class="col-md-5 player">
									<h2>{{game_state['player2_name']}} [ O ]</h2>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-md-12">
						<h3 v-if="game_in_progress">Turn: {{game_state['turn']}}</h3>
						<h3 v-if="!game_in_progress">GAME OVER</h3>
					</div>
				</div>
                <div class="gameboard">
                    <!--div class="row turn hidden-xs">
                        <div class="col-md-12"></div>
                    </div-->
					<div class="row">
						<div v-for="n in 3" class="col-md-4 col-xs-4 box" v-bind:class="{ 'bg-primary':is_inWinnLine(game_state['winn_line'], n-1) }" v-bind:id="n-1"  v-on:click="boxClick(n-1)">{{board[n-1]}}</div>
					</div>
					<div class="row">
						<div v-for="n in 3" class="col-md-4 col-xs-4 box" v-bind:class="{ 'bg-primary':is_inWinnLine(game_state['winn_line'], n+2) }" v-bind:id="n+2"  v-on:click="boxClick(n+2)">{{board[n+2]}}</div>
					</div>
					<div class="row">
						<div v-for="n in 3" class="col-md-4 col-xs-4 box" v-bind:class="{ 'bg-primary':is_inWinnLine(game_state['winn_line'], n+5) }" v-bind:id="n+5"  v-on:click="boxClick(n+5)">{{board[n+5]}}</div>
					</div>                   

					<div class="btn-group btn-group-justified">
						<a href="#" class="btn btn-primary" v-bind:class="{'disabled':game_in_progress}" v-on:click="playAgain()">Try Again</a>
						<a href="#" class="btn btn-success" v-bind:class="{'disabled':game_in_progress}" v-on:click="newGame()">New Game</a>
					 </div>
                </div>
        </div>
        <div class="col-md-4 hidden-xs rightpanel color-swatch gray-lighter">
            
            <div class="panel panel-default">
                <div class="panel-heading text-center"><strong>Last 3 matches scores</strong></div>
				<div class="panel-body">
					<div class="card" v-for="(item,index) in game_score">
						<div class="card-header"><h3 class="card-title">{{item.player1_name}} VS {{item.player2_name}}</h3></div>
						<div class="card-block">
							<div class="row scoresheading">
								<div class="col-md-5">Date</div>
								<div class="col-md-4">Result</div>
								<div class="col-md-3">Round</div>
							</div>
							<div class="row scores text-nowrap">
								<div class="col-md-5">{{item.date_ended}}</div>
								<!------------------>
								<div class="col-md-4" v-if="item.state_status === '2' || item.state_status === '3'">winn: {{item.winner_name}}</div>
								<div class="col-md-4" v-else-if="item.state_status === '4'">Draw</div>
								<div class="col-md-4" v-else>Not complete</div>
								<!------------------>
								<div class="col-md-3">{{item.state_round}}</div>
							</div>
						</div>
						<div class="card-block"><h4 class="card-title">Board</h4></div>
						<div class="card-block">
							<div class="row">
								<div v-for="n in 3" class="col-md-4 col-xs-4 scores-box" v-bind:class="{ 'bg-primary':is_inWinnLine(item.winn_line, n-1) }">{{item.board[n-1]}}</div>
							</div>
							<div class="row">
								<div v-for="n in 3" class="col-md-4 col-xs-4 scores-box" v-bind:class="{ 'bg-primary':is_inWinnLine(item.winn_line, n+2) }">{{item.board[n+2]}}</div>
							</div>
							<div class="row">
								<div v-for="n in 3" class="col-md-4 col-xs-4 scores-box" v-bind:class="{ 'bg-primary':is_inWinnLine(item.winn_line, n+5) }">{{item.board[n+5]}}</div>
							</div>
						</div>
					</div>
					
				</div>
			</div>
            
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url() ?>js/vue.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/vue-resource.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/app_game.js"></script>	
