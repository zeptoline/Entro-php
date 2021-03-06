<?php
session_start();

include "classes/Plateau.php";
include "classes/Joueur.php";

if (isset($_GET['reset']) && $_GET['reset'] == 1)
{
	session_destroy();
	if(isset($_GET['nomj1']) && isset($_GET['nomj2'])) {
		header('Location: Application.php?nomj1='.$_GET['nomj1'].'&nomj2='.$_GET['nomj2']);
	} else if (isset($_GET['nomj1'])) {
		header('Location: Application.php?nomj1='.$_GET['nomj1'].'&nomj2=joueur2');
	} else if (isset($_GET['nomj2'])) {
		header('Location: Application.php?nomj1=joueur1&nomj2='.$_GET['nomj2']);
	}
}
?>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>Entro-php</title>
	<link href="style/style.css" type="text/css" rel="stylesheet" media="all" />
</head>
<body>
	<h1>Jeu de l'entropie</h1>

	<div id="info">
		<h2> Historique </h2>
		<?php
		//Création ou récupération des joueurs
		if(!isset($_SESSION["j1"])) {
			if(isset($_GET["nomj1"])) {
				if($_GET["nomj1"] == "") {
					$j1 = new Joueur("joueur1", "j1");
					$_SESSION["j1"] = serialize($j1);
				} else {
					$j1 = new Joueur($_GET["nomj1"], "j1");
					$_SESSION["j1"] = serialize($j1);
				}
			} else {
				$j1 = new Joueur("joueur 1", "j1");
				$_SESSION["j1"] = serialize($j1);
			}
		} else {
			$j1 = unserialize($_SESSION["j1"]);
		}

		if(!isset($_SESSION["j2"])) {
			if(isset($_GET["nomj2"])) {
				if($_GET["nomj2"] == "") {
					$j2 = new Joueur("joueur2", "j2");
					$_SESSION["j2"] = serialize($j2);
				} else {
					$j2 = new Joueur($_GET["nomj2"], "j2");
					$_SESSION["j2"] = serialize($j2);
				}
			} else {
				$j2 = new Joueur("joueur 2", "j2");
				$_SESSION["j2"] = serialize($j2);
			}
		} else {
			$j2 = unserialize($_SESSION["j2"]);
		}


		//Si le plateau n'est pas crée, le créer
		if(!isset($_SESSION["plateau"])) {
			$plateau = new Plateau($j1, $j2);
			$plateau -> init();

		} else {
			$plateau = unserialize($_SESSION["plateau"]);
		}


		if(!isset($_SESSION['log'])) {
			$log = "Nouvelle partie commencée<br />";
		} else {
			$log = unserialize($_SESSION['log']);
		}

		if(!isset($_SESSION['compteur'])) {
			$compteur = 0;
		} else {
			$compteur = unserialize($_SESSION['compteur']);
		}


		if(!isset($_SESSION['prevCoup'])) {
			$prevCoup = null;
		} else {
			$prevCoup = unserialize($_SESSION['prevCoup']);
		}




		if(isset($_GET["action"])) {
			switch($_GET["action"]) {
				case 'move_origin' :
				if(isset($_GET['x']) && isset($_GET['y'])) {
					$origin = [$_GET['x'], $_GET['y']];
					$_SESSION['origin'] = serialize($origin);
				}
				break;
				case 'move_target' :
				$prevCoup =unserialize($_SESSION['plateau']);
				if(isset($_GET['x']) && isset($_GET['y'])) {
					$origin = unserialize($_SESSION['origin']);

					$plateau -> move($origin[0], $origin[1], $_GET['x'], $_GET['y']);

					unset($_SESSION['origin']);

					$joueur = $plateau -> getTurn();

					$score = $plateau -> getScore($joueur);
					if($score[0]==0 && $score[1] == 7) {
						$log =  $joueur -> toString().' à gagner !<br/>'.$log;
						$plateau -> victoire();
						//FIN DE LA PARTIE, JOUEUR A GAGNE
					} else {
						$plateau -> tourSuivant();
						$log = "C'est au tour de ".$plateau -> getTurn() -> toString()."<br/>".$log;

						$nouvJoueur = $plateau -> getTurn();
						$compteur = $compteur + 1;
						$testJouabilite = $plateau -> getScore($nouvJoueur);
						if($testJouabilite[1]==7 && $testJouabilite[0] > 0) {
							$log = $plateau -> getTurn() -> toString().' a tous ses pions bloqués, mais certains isolés.Son tour est passé <br/>'.$log;
							$plateau -> tourSuivant();
						} else if($testJouabilite[1]==7 && $testJouabilite[0] == 0){
							$log = $nouvJoueur -> toString().' à gagner !<br/>'.$log;
							$plateau -> victoire();
						}
					}

				}
				break;
				case 'invalid_joueur' :
				$log = 'Ce n\'est pas votre pion <br/>'.$log;
				break;
				case 'invalid_mouvement' :
				$log = 'Vous ne pouvez pas déplacer votre pion ici<br/>'.$log;
				break;
				case 'invalid_origin' :
				$log = 'Impossible de bouger ce pion<br/>'.$log;
				break;
				case 'relacher_origin' :
				unset($_SESSION['origin']);
				break;
				case 'previous' :
				$plateau = $prevCoup;
				break;
				default:
				break;
			}


		}

		echo $log;

		$_SESSION['log'] = serialize($log);
		?>
	</div>

	<div id="plateau">
		<?php
		$plateau -> affichage();
		$_SESSION["prevCoup"] = serialize($prevCoup);
		$_SESSION["plateau"] = serialize($plateau);
		$_SESSION['compteur'] = serialize($compteur);
		?>
		<div id="bouton">
			<?php
			if (isset($_SESSION['origin'])) {

				echo '<a href="Application.php?action=relacher_origin" id="b1"><button>Relacher le pion</button></a>';
			}

			if($compteur >= 1){
				echo '<a href="Application.php?action=previous" id="b2"><button>Annuler le coup</button></a>';
			}
			?>
			<a href="../launcher.html" id="reset"><button>RESET?</button></a>
		</div>
	</div>
	<div id="score">
		<h2> Score </h2>
		<table>
			<tr>
				<td></td>
				<?php
				echo '<td>'.$j1 -> toString().'</td>';
				echo '<td>'.$j2 -> toString().'</td>';
				?>
			</tr>
			<tr>
				<td id="left">pions isolés</td>
				<?php
				$scorej1 = $plateau -> getScore($j1);
				$scorej2 = $plateau -> getScore($j2);
				echo '<td>'.$scorej1[0].'</td>';
				echo '<td>'.$scorej2[0].'</td>';
				?>
			</tr>
			<tr>
				<td id="left">pions bloqués</td>
				<?php
				$scorej1 = $plateau -> getScore($j1);
				$scorej2 = $plateau -> getScore($j2);
				echo '<td>'.$scorej1[1].'</td>';
				echo '<td>'.$scorej2[1].'</td>';
				?>
			</tr>
		</table>
	</div>
</body>
</html>
