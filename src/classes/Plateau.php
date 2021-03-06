<?php
	/**
	* La class Plateau
	*
	* Class permettant de représenter un Plateau, de l'identifier et de l'utiliser
	*
	* @package			Classes
	* @author       Brewal & Cédric
	* @version			Finale
	*/

	require 'Pion.php';
	/**
	 * Classe permettant de gérer le plateau et les déplacement
	 */
	class Plateau {
		/**
		 * Variable représentant le plateau
		 * @var array
		 */
		var $cases;
		/**
		 * Variable donnant le joueur qui doit jouer
		 * @var Joueur
		 */
		var $turn;
		/**
		 * Variable représentant le premier joueur
		 * @var Joueur
		 */
		var $j1;
		/**
		 * Variable représentant le second joueur
		 * @var Joueur
		 */
		var $j2;
		/**
		 * Variable donnant l'ensemble des pions du joueur 1
		 * @var array
		 */
		var $pionsj1;
		/**
		 * Variable donnant l'ensemble des pions du joueur 2
		 * @var array
		 */
		var $pionsj2;
		/**
		 * Booléan définissant si la partie est achevée ou non
		 * @var boolean
		 */
		var $partieFini;

		/**
		* Fonction initialisant le Plateau
		* @param Joueur $joueur1 Le premier joueur du jeu
		* @param Joueur $joueur2 Le second joueur du jeu
		*/
		function __construct($joueur1, $joueur2){
			$this -> cases = array();
			$this -> j1 = $joueur1;
			$this -> j2 = $joueur2;
			$this -> turn = $this -> j1;
			$this -> pionsj1 = array();
			$this -> pionsj2 = array();
			$this -> partieFini = false;
		}

		/**
		* Fonction donnant le joueur du tour actuel
		* @return Joueur Le joueur devant jouer
		*/
		public function getTurn() {
			return $this -> turn;
		}

		/**
		* Fonction initialisant le plateau en créant le plateau et en y insérant les pions
		*
		*/
		public function init() {

			for ($x = 0; $x < 5; $x++)
			{
				$this -> cases[$x] = array();
				for ($y = 0; $y < 5; $y++)
				{
					if ($x == 0)
					{
						$pion = new Pion($this -> j1, "j1", $x, $y);
						$this -> cases[$x][$y] = $pion;
						array_push($this -> pionsj1, $pion);
					} else if ($x == 1)
					{
						if ($y == 0 || $y == 4)
						{
							$pion = new Pion($this -> j1, "j1", $x, $y);
							$this -> cases[$x][$y] = $pion;
							array_push($this -> pionsj1, $pion);
						} else
						{
							$pion = new Pion(new Joueur("null", "null"), "null", $x, $y);
							$this -> cases[$x][$y] = $pion;
						}
					} else if ($x == 3)
					{
						if($y == 0 || $y == 4)
						{
							$pion = new Pion($this -> j2, "j2", $x, $y);
							$this -> cases[$x][$y] = $pion;
							array_push($this -> pionsj2, $pion);
						} else
						{
							$pion = new Pion(new Joueur("null", "null"), "null", $x, $y);
							$this -> cases[$x][$y] = $pion;
						}
					} else if ($x == 4)
					{
						$pion = new Pion($this -> j2, "j2", $x, $y);
						$this -> cases[$x][$y] = $pion;
						array_push($this -> pionsj2, $pion);
					} else {
						$pion = new Pion(new Joueur("null", "null"), "null", $x, $y);
						$this -> cases[$x][$y] = $pion;
					}
				}
			}
		}

		/**
		* Fonction indiquant que la victoire à été atteinte
		*/
		public function victoire() {
			$this -> partieFini = true;
		}

		/**
		 * Fonction permettant à la fois de créer l'affichage, mais aussi de
		 * determiner si chaque case peut donner un coup ou nom
		 * (en utilisant la methode d'url long)
		 */
		public function affichage() {
			echo '<table style="border: 1px solid black;">';
			for ($x = 0; $x < 5; $x++) {
				echo '<tr>';
				for ($y = 0; $y < 5; $y++) {
					echo '<td>';

					if (!$this -> partieFini) {
						if (isset($_SESSION["origin"])) {
							//Mouvement vers la prochaine case
							$origin = unserialize($_SESSION["origin"]);
							$target = [$x, $y];

							if(in_array($target, $this -> mouvementsPossibles($origin[0], $origin[1]))) {
								echo '<a href="Application.php?action=move_target&x='.$x.'&y='.$y.'" class="move">';
							} else {
								echo '<a href="Application.php?action=invalid_mouvement&x='.$x.'&y='.$y.'" class="'.$this -> cases[$x][$y] -> getId().'">';
							}

						} else {
							//Choix du pion à déplacer (en fonction du tour du joueur)
							if ($this -> getTurn() ->getId() == $this ->cases[$x][$y] ->getId()) {
								//IMPORTANT : FAIRE LA FONCTION MOUVEMENTPOSSIBLE (code bloqué en attendant)
								if($this -> mouvementPossible($x, $y)) {
									//Pion appartenant au joueur, et bougeable
									echo '<a href="Application.php?action=move_origin&x='.$x.'&y='.$y.'" class="'.$this -> cases[$x][$y] -> getId().'">';
								}	else {
									//Pion appartenant au joueur mais isolé / imbougeable
									echo '<a href="Application.php?action=invalid_origin&x='.$x.'&y='.$y.'" class="'.$this -> cases[$x][$y] -> getId().'">';
								}
							} else {
								//Pion n'appartenant pas au joueur
								echo '<a href="Application.php?action=invalid_joueur&x='.$x.'&y='.$y.'" class="'.$this -> cases[$x][$y] -> getId().'">';
							}
						}
					} else {
						echo '<a href="#" class="partieFini">';
					}
					echo '</a>';
					echo '</td>';
				}

				echo '</tr>';
			}
			echo '</table>';
		}


		/**
		 * Fonction permettant de savoir si un pion à  un point donné peut se déplacer
		 * Un pion peut se déplacer si un autre pion du même joueur est présent dans une case adjacente
		 * @param  int $x La coordonnée x d'un pion
		 * @param  int $y La coordonnée y d'un pion
		 * @return boolean    Renvois vrai si le pion est déplaçable, faux sinon
		 */
		public function mouvementPossible($x, $y) {
			$j_id = $this -> cases[$x][$y] -> getId();
			$ret = false;
			$libre=false;

			for($hor = $x-1; $hor <= $x+1; $hor++) {
				if ($hor >= 0 && $hor <= 4) {

					for($vert = $y-1; $vert <=$y+1; $vert++) {
						if($vert >=0 && $vert <=4 && !($vert == $y && $hor == $x)) {

							if($this-> cases[$hor][$vert] != null) {
								if ($this-> cases[$hor][$vert] -> getId() == $j_id) {
									$ret = true;
								} else if ($this-> cases[$hor][$vert] -> getId() == "null") {
									$libre=true;
								}
							}

						}
					}
				}
			}
			return ($ret && $libre);
		}

		/**
		 * Renvois toutes les cases qui permettent au pion à un point donné de ce déplacer
		 * @param  int $x Coordonnée x du pion
		 * @param  int $y Coordonnée y du pion
		 * @return array    Le liste de tous les emplacements pouvant permettre au pion de se déplacer
		 */
		public function mouvementsPossibles($x, $y) {
			$liste = array();
			$bloquer = false;
			$vert = $x;
			$hor = $y;

			while ($vert <4 && !$bloquer) {
				$vert++;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$vert = $x;
			$bloquer = false;

			while ($vert >0 && !$bloquer) {
				$vert--;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$vert = $x;
			$bloquer = false;

			while ($hor <4 && !$bloquer) {
				$hor++;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$hor = $y;
			$bloquer = false;

			while ($hor >0 && !$bloquer) {
				$hor--;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$hor = $y;
			$bloquer = false;

			while ($vert<4 && $hor<4 && !$bloquer) {
				$vert++;
				$hor++;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$vert = $x;
			$hor = $y;
			$bloquer = false;

			while ($vert>0 && $hor>0 && !$bloquer) {
				$vert--;
				$hor--;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$vert = $x;
			$hor = $y;
			$bloquer = false;

			while ($vert>0 && $hor<4 && !$bloquer) {
				$vert--;
				$hor++;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$vert = $x;
			$hor = $y;
			$bloquer = false;

			while ($vert<4 && $hor>0 && !$bloquer) {
				$vert++;
				$hor--;
				if($this->cases[$vert][$hor] != null) {
					if ($this->cases[$vert][$hor] -> getId() == "null") {
						array_push($liste, [$vert, $hor]);
					} else {
						$bloquer = true;
					}
				}
			}
			$vert = $x;
			$hor = $y;
			$bloquer = false;

			return $liste;
		}

		/**
		 * Fonction faisant se déplacer un pion d'un point à un autre
		 * @param  int $x    Coordonnée x du pion se déplaçant
		 * @param  int $y    Coordonnée y du pion se déplaçant
		 * @param  int $tarx Coordonnée x du point d'arrivée
		 * @param  int $tary Coordonnée y du point d'arrivée
		 */
		public function move($x, $y, $tarx, $tary) {
			$pion = $this -> cases[$x][$y];
			$tar = $this -> cases[$tarx][$tary];
			if ($pion -> getId() == $this -> getTurn() -> getId()) {
				if($tar -> getId() == "null") {

					$pion -> setCoord($tarx, $tary);
					$tar -> setCoord($x, $y);
					$this -> cases[$tarx][$tary] = $pion;
					$this -> cases[$x][$y] = $tar;
				}
			}
		}
		/**
		 * Fonction passant au tour suivant
		 */
		public function tourSuivant() {
			$this -> turn = ($this-> turn == $this -> j1) ? $this -> j2 : $this -> j1 ;
		}

		/**
		 * Fonction permettant de savoir si un pion à un point donnée est isolé
		 * Un pion est isolé si aucun autre pion, que ce soit alié ou ennemie, n'est présent dans une case adjacente
		 * @param  int $x Coordonnée x du pion
		 * @param  int $y Coordonnée y du pion
		 * @return boolean    Renvois vrai si le pion est isolé, faux sinon
		 */
		public function estIsole($x, $y) {
			$pion = $this -> cases[$x][$y];
			$isole = true;

			for($hor = $x-1; $hor <= $x+1; $hor++) {
				if ($hor >= 0 && $hor <= 4) {

					for($vert = $y-1; $vert <=$y+1; $vert++) {
						if($vert >=0 && $vert <=4 && !($vert == $y && $hor == $x)) {

							if($this->cases[$hor][$vert] != null) {
								if ($this->cases[$hor][$vert] -> getId() != "null"){
									$isole = false;
								}
							}
						}
					}
				}
			}

			return $isole;
		}


		/**
		 * Fonction renvoyant la liste de tous les pions d'un joueur
		 * @param  Joueur $joueur Le joueur auquel on veut obtenir les pionsj1
		 * @return array         Liste de tous les pions d'un joueur
		 */
		public function getListePion($joueur) {
			switch($joueur) {
				case $this -> j1 :
					return $this -> pionsj1;
				case $this -> j2 :
					return $this -> pionsj2;
				default :
					return null;
			}
		}

		/**
		 * Fonction donnant le score d'un joueur
		 * Le score est défini de la façon suivant :
		 * Nombre de pions bloqués : c'est le nombre de pions impossible à bouger, mais qui ne sont pas isolé
		 * Nombre de pions isolés : C'est le nombre de pions impossible à bouger, mais qui n'ont pas d'autre pions autours d'eux
		 * Le joueur gagne si son nombre de pions bloqués vaut 7, et que sont nombre de pions isolés vaut 0
		 * @param  Joueur $joueur Le joueur auquel on veut le joueur
		 * @return array         Une liste de deux cases contenant le score. Le nombre de pions isolés est dans la première case, et le nombre de pions bloqués dans la deuxieme
		 */
		public function getScore($joueur){
			$score = array();
			$mouvements = 0;
			$isoles = 0;

			$liste = $this -> getListePion($joueur);

			foreach ($liste as $pion) {
				$coord = $pion -> getCoord();
				$x = $coord[0];
				$y = $coord[1];

				$isole = $this -> estIsole($x, $y);
				$mouvepossible = $this -> mouvementPossible($x, $y);

				if ($isole) {
					$isoles++;
				}
				if (!$mouvepossible) {
					$mouvements++;
				}

			}

			$score = [$isoles, $mouvements];
			return $score;
		}

	}
?>
