<?php
	class Pion {
		var $isole;
		var $joueur;
		var $id;
		var $coord;

		/**
		 * Fonction initialisant un Pion
		 * @param Joueur $joueur Le joueur qui possede le pion
		 * @param String $id     Identifiant du joueur
		 * @param int $x     	La coordonnée x
		 * @param int $y      La coordonnée y
		 */
		function __construct($joueur, $id, $x, $y){
			$this -> isole = False;
			$this -> joueur = $joueur;
			$this -> id = $id;
			$this -> coord = [$x, $y];
		}

		/**
		 * Fonction pour afficher un Pion
		 * @return String Retourne le pion sous la forme d'une chaine de caractère
		 */
		public function toString(){
			echo "nom joueur : ".$this -> joueur -> toString()."\t isole : ".$this -> estIsole();
		}

		/**
		 * Fonction qui retourne le joueur correspondant au Pion
		 * @return Joueur Le joueur correspondant au Pion
		 */
		public function getJoueur() {
			return $this -> joueur;
		}
		/**
		 * Fonction qui retourne l'identifiant du joueur correspondant au Pion
		 * @return String L'identifiant du joueur correspondant au Pion
		 */
		public function getId() {
			return $this -> id;
		}
		/**
		 * Fonction qui retourne les coordonnées x et y du Pion
		 * @return array Les coordonnées x et y du Pion
		 */
		public function getCoord() {
			return $this -> coord;
		}
		/**
		 * Fonction qui permet de définir les coordonnées x et y du Pion
		 * @param int $x La coordonnée x
		 * @param int $y La coordonnée y
		 */
		public function setCoord($x, $y) {
			$this -> coord = [$x, $y];
		}

		/**
		 * Fonction qui test si un pion est isolé
		 * @return boolean Retourne False si le pion n'est pas isolé sinon retourne True
		 */
		public function estIsole(){
			if(!$this -> isole){
				return "false";
			}
			else{
				return "true";
			}
		}

		/**
		 * Fonction qui retourne le joueur correspondant au pion sous la forme
		 * d'une chaine de caractères
		 * @return String Le joueur correspondant au pion sous la forme de chaine de caractères
		 */
		public function getName(){
			return $this -> joueur -> toString();
		}
	}
?>
