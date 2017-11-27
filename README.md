# Building a Tic-Tac-Toe with  PHP CodeIgniter and vue js
This simple project implement TicTacToe game with PHP CodeIgniter 3.x framework and vue.js 2.x

---
## Features
- Has two options to play
**a-** Single player (play with PC) and it has three levels 
**b- **Multiplayers (two players)
- Take a name for theplayers and begin a new game.
- Use basic styling for the grid using bootstrap3 css.
- Allow the players to click the grid to make their move, and highlight when the game is finished.
- Store the results of each match in a MySQL database, and view the results.
- Use the bootstrap framework to make it responsive in the following ways:
- On mobile device, make the game full screen.
- On desktop browser have the results of the last 5 matches on the right hand side – do this in bootstrap, not php.

---------------------------------------------
## Installation

1- Create database.
2- Run the sql scripts in the file  **/public/db-mysql/mydb.sql** 
3- Open the **/application/config/database.php**  and change the configuration of database as in your system
4- if you use apatche or ngnix set the default web site or virual site to folder **/public/**
5- if you not used web server then open a terminal or command prompt and run the following command inside the ** /public** folder 
``
	php -S localhost:8000
``
and open your browser to http://localhost:8000

--------------------------------------------
## Specifications
This project I run it under PHP 7.1.  for older PHP version I don’t know
This project test implements TicTacToe game by using MySQL DB, PHP CodeIgniter-3 framework, Bootstrap CSS, and VUE JS framework.
In PHP I have used the technical of the json post and the vue js for the user actions.
-------------------------------------------------

#### For the human player
it implement along with its actions through the GUI (user interface) controls. It’s very easy to understand by reading the code directly; it’s basically a vue js  click event handler on the grid cells that reads the current game state and update it by the performed move.

-------------------------------------------------
#### For the PC Player
I implement the **Minimax Algorithm**. The algorithm is used to calculate the minimax value of a specific state (or the action that leads to that state), and it works by knowing that someone wants to minimize the score function and the other wants to maximize the score function (and that’s why it’s called minimax).

---------------------------------------------

## Implementations

For the implementation of the the game I created the following classes in the libraries folder :



#### The State Class
represent a certain configuration of the grid board, it has all informations that need to be associated with state of the board and the board configuration, like who’s turn is it, the result of the game at this state is (whether it’s still running, somebody won, or it’s a draw), and how many round the players have made.


-------------------------------------------------
#### The PC Player Calss

can play Tic-Tac-Toe at three difficulty levels: 

- **Blind level:** in which the PC understands nothing about the game, 
- **Novice level** in which the PC Player plays the game as a novice player, and the (not work probably) 
- **Master level** in which the PC Player plays the game like a master (not work probably).


-------------------------------------------------
#### PC Action Class
it need for AI decision making and moves, and it holds two information:

- The position on the board that it’ll make its move on (remember that it’s a one-dimensional array index) and 
- The minimax value of the state that this action will lead to (remember the minimax function ?). This minimax value will be the criteria at which the PC Player will chose its best available action. 

-------------------------------------------------
#### The Game Class
This is the structure that will control the flow of the game and glue everything together in one functioning unit. It keeps and access three kinds of information : the human who plays the game with other human or with the PC Player, 

- the current state of the game, and 
- the status of the game (whether it’s running or ended).

---------------------------------------------

## TODO
Has some problem in single player with the level (midle and hard)

