<?php

include "../includes/global.php";
include "administration.php";

include "header.php";

?>
<div class="corps_encart_haut">
	<h1>Statistiques des abonnements</h1>
</div>
<div class="corps_encart_corps">
	<div class="corps_encart_corps_texte">

		<br />

		<?php

		if (!isset($_GET["tri"]))
			$_GET["tri"] = 1;
		else $_GET["tri"] = sprintf("%0d", $_GET["tri"]);

		if (!isset($_GET["detail"]))
			$_GET["detail"] = 0;
		else $_GET["detail"] = sprintf("%0d", $_GET["detail"]);

		if ($_GET["detail"] == 0) {
			if (($_GET["tri"] < 1) || ($_GET["tri"] > 6))
				$_GET["tri"] = 1;

			$lien_tri = "stats_abonnements.html?tri=";
		?>
			<table width="100%">
				<tr>
					<td width="30%" align="left" valign="middle" style="border-bottom:1px solid #000000;"><b>Ann?e</b> <a href="<?php echo $lien_tri; ?>1"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>2"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="30%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Abonnements</b> <a href="<?php echo $lien_tri; ?>3"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>4"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="40%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Montant</b> <a href="<?php echo $lien_tri; ?>5"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>6"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
				</tr>
				<?php
				$debann = "1980";
				$finann = "9999";

				$requete_jim = "SELECT MIN(date_paiement) AS mini FROM abonnement_membre WHERE prix>0";
				if ($envoi_jim = mysqli_query($conn, $requete_jim)) {
					if ($data_jim = mysqli_fetch_array($envoi_jim, MYSQLI_ASSOC))
						$debann = substr($data_jim["mini"], 0, 4);
					mysqli_free_result($envoi_jim);
				} else die($conn->error);

				$requete_jim = "SELECT MAX(date_paiement) AS maxi FROM abonnement_membre WHERE prix>0";
				if ($envoi_jim = mysqli_query($conn, $requete_jim)) {
					if ($data_jim = mysqli_fetch_array($envoi_jim, MYSQLI_ASSOC))
						$finann = substr($data_jim["maxi"], 0, 4);
					mysqli_free_result($envoi_jim);
				} else die($conn->error);

				$debann = sprintf("%0d", $debann);
				$finann = sprintf("%0d", $finann);

				$indmax = 0;

				for ($indann = $finann; $indann >= $debann; $indann--) {
					$datdeb = sprintf("%04d", $indann) . "-01-01";
					$datfin = sprintf("%04d", $indann) . "-12-31";

					$requete_jim = "SELECT COUNT(*) AS nbre,SUM(prix) AS total FROM abonnement_membre WHERE date_paiement>='$datdeb 00:00:00' AND date_paiement<='$datfin 23:59:59' AND prix>0";
					if ($envoi_jim = mysqli_query($conn, $requete_jim)) {
						while ($data_jim = mysqli_fetch_array($envoi_jim, MYSQLI_ASSOC)) {
							if ($data_jim["nbre"] != 0) {
								$tabdat[$indmax] = $indann;
								$tabnbr[$indmax] = $data_jim["nbre"];
								$tabmnt[$indmax] = $data_jim["total"];

								$indmax++;
							}
						}
						mysqli_free_result($envoi_jim);
					} else die($conn->error);
				}

				if ($indmax > 0) {
					if ($_GET["tri"] == 1)
						array_multisort($tabdat, SORT_DESC, SORT_STRING, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 2)
						array_multisort($tabdat, SORT_ASC, SORT_STRING, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 3)
						array_multisort($tabnbr, SORT_NUMERIC, SORT_DESC, $tabdat, SORT_ASC, SORT_STRING, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 4)
						array_multisort($tabnbr, SORT_NUMERIC, SORT_ASC, $tabdat, SORT_ASC, SORT_STRING, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 5)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_DESC, $tabnbr, SORT_NUMERIC, SORT_DESC, $tabdat, SORT_ASC, SORT_STRING);
					else if ($_GET["tri"] == 6)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_ASC, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabdat, SORT_ASC, SORT_STRING);

					for ($indtab = 0; $indtab < count($tabdat); $indtab++) {
				?>
						<tr>
							<td align="left" valign="middle" style="border-bottom:1px dotted #000000;"><a href="stats_abonnements.html?detail=1&annee=<?php echo $tabdat[$indtab]; ?>"><?php echo $tabdat[$indtab]; ?></a></td>
							<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabnbr[$indtab]; ?></td>
							<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabmnt[$indtab]; ?></td>
						</tr>
				<?php
					}
				}
				?>
			</table>
			<br /><a href="stats.html"><img src="<?php echo GetParam("SCRIPT_URL"); ?>/style/images/bouton_retour.png" border="0" alt="Retour" /></a><br /><br /><br />
		<?php
		} else if ($_GET["detail"] == 1) {

			if (($_GET["tri"] < 1) || ($_GET["tri"] > 6))
				$_GET["tri"] = 1;

			$_GET["annee"] = sprintf("%04d", $_GET["annee"]);

			$lien_tri = "stats_abonnements.html?detail=1&annee=" . $_GET["annee"] . "&tri=";
		?>
			<table width="100%">
				<tr>
					<td width="30%" align="left" valign="middle" style="border-bottom:1px solid #000000;"><b>Mois</b> <a href="<?php echo $lien_tri; ?>1"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>2"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="30%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Abonnements</b> <a href="<?php echo $lien_tri; ?>3"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>4"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="40%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Montant</b> <a href="<?php echo $lien_tri; ?>5"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>6"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
				</tr>
				<?php
				$indmax = 0;

				for ($indmoi = 12; $indmoi >= 1; $indmoi--) {
					$datdeb = $_GET["annee"] . "-" . sprintf("%02d", $indmoi) . "-01";
					$datfin = $_GET["annee"] . "-" . sprintf("%02d", $indmoi) . "-31";

					$requete_jim = "SELECT COUNT(*) AS nbre,SUM(prix) AS total FROM abonnement_membre WHERE date_paiement>='$datdeb 00:00:00' AND date_paiement<'$datfin 23:59:59' AND prix>0";
					if ($envoi_jim = mysqli_query($conn, $requete_jim)) {
						while ($data_jim = mysqli_fetch_array($envoi_jim, MYSQLI_ASSOC)) {
							if ($data_jim["nbre"] != 0) {
								$tabdat[$indmax] = $_GET["annee"] . "-" . sprintf("%02d", $indmoi);
								$tabnbr[$indmax] = $data_jim["nbre"];
								$tabmnt[$indmax] = $data_jim["total"];

								$indmax++;
							}
						}
						mysqli_free_result($envoi_jim);
					} else die($conn->error);
				}

				if ($indmax > 0) {
					if ($_GET["tri"] == 1)
						array_multisort($tabdat, SORT_DESC, SORT_STRING, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 2)
						array_multisort($tabdat, SORT_ASC, SORT_STRING, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 3)
						array_multisort($tabnbr, SORT_NUMERIC, SORT_DESC, $tabdat, SORT_ASC, SORT_STRING, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 4)
						array_multisort($tabnbr, SORT_NUMERIC, SORT_ASC, $tabdat, SORT_ASC, SORT_STRING, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 5)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_DESC, $tabnbr, SORT_NUMERIC, SORT_DESC, $tabdat, SORT_ASC, SORT_STRING);
					else if ($_GET["tri"] == 6)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_ASC, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabdat, SORT_ASC, SORT_STRING);

					for ($indtab = 0; $indtab < count($tabdat); $indtab++) {
				?>
						<tr>
							<td align="left" valign="middle" style="border-bottom:1px dotted #000000;"><a href="stats_abonnements.html?detail=2&mois=<?php echo $tabdat[$indtab]; ?>"><?php echo substr($tabdat[$indtab], 5, 2) . "/" . substr($tabdat[$indtab], 0, 4); ?></a></td>
							<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabnbr[$indtab]; ?></td>
							<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabmnt[$indtab]; ?></td>
						</tr>
				<?php
					}
				}
				?>
			</table>
			<br /><a href="stats_abonnements.html"><img src="<?php echo GetParam("SCRIPT_URL"); ?>/style/images/bouton_retour.png" border="0" alt="Retour" /></a><br /><br />
		<?php
		} else if ($_GET["detail"] == 2) {

			if (($_GET["tri"] < 1) || ($_GET["tri"] > 6))
				$_GET["tri"] = 1;

			$lien_tri = "stats_abonnements.html?detail=2&mois=" . $_GET["mois"] . "&tri=";
		?>
			<table width="100%">
				<tr>
					<td width="30%" align="left" valign="middle" style="border-bottom:1px solid #000000;"><b>Jour</b> <a href="<?php echo $lien_tri; ?>1"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>2"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="30%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Abonnements</b> <a href="<?php echo $lien_tri; ?>3"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>4"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="40%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Montant</b> <a href="<?php echo $lien_tri; ?>5"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>6"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
				</tr>
				<?php
				$indmax = 0;

				for ($indjou = 31; $indjou >= 1; $indjou--) {
					$datdeb = sprintf("%04d", substr($_GET["mois"], 0, 4)) . "-" . sprintf("%02d", substr($_GET["mois"], 5, 2)) . "-" . sprintf("%02d", $indjou);

					$requete_jim = "SELECT COUNT(*) AS nbre,SUM(prix) AS total FROM abonnement_membre WHERE date_paiement>='$datdeb 00:00:00' AND date_paiement<='$datdeb 23:59:59' AND prix>0";
					if ($envoi_jim = mysqli_query($conn, $requete_jim)) {
						if ($data_jim = mysqli_fetch_array($envoi_jim, MYSQLI_ASSOC)) {
							if ($data_jim["nbre"] != 0) {
								$tabdat[$indmax] = $datdeb;
								$tabnbr[$indmax] = $data_jim["nbre"];
								$tabmnt[$indmax] = $data_jim["total"];

								$indmax++;
							}
						}
						mysqli_free_result($envoi_jim);
					} else die($conn->error);
				}

				if ($indmax > 0) {
					if ($_GET["tri"] == 1)
						array_multisort($tabdat, SORT_DESC, SORT_STRING, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 2)
						array_multisort($tabdat, SORT_ASC, SORT_STRING, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 3)
						array_multisort($tabnbr, SORT_NUMERIC, SORT_DESC, $tabdat, SORT_ASC, SORT_STRING, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 4)
						array_multisort($tabnbr, SORT_NUMERIC, SORT_ASC, $tabdat, SORT_ASC, SORT_STRING, $tabmnt, SORT_NUMERIC, SORT_DESC);
					else if ($_GET["tri"] == 5)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_DESC, $tabnbr, SORT_NUMERIC, SORT_DESC, $tabdat, SORT_ASC, SORT_STRING);
					else if ($_GET["tri"] == 6)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_ASC, $tabnbr, SORT_NUMERIC, SORT_ASC, $tabdat, SORT_ASC, SORT_STRING);

					for ($indtab = 0; $indtab < count($tabdat); $indtab++) {
				?>
						<tr>
							<td align="left" valign="middle" style="border-bottom:1px dotted #000000;"><a href="stats_abonnements.html?detail=3&jour=<?php echo $tabdat[$indtab]; ?>"><?php echo DateMySQLEnDateTexte($tabdat[$indtab]); ?></a></td>
							<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabnbr[$indtab]; ?></td>
							<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabmnt[$indtab]; ?></td>
						</tr>
				<?php
					}
				}
				?>
			</table>
			<br /><a href="stats_abonnements.html?detail=1&annee=<?php echo substr($_GET["mois"], 0, 4); ?>"><img src="<?php echo GetParam("SCRIPT_URL"); ?>/style/images/bouton_retour.png" border="0" alt="Retour" /></a><br /><br />
		<?php
		} else if ($_GET["detail"] == 3) {
			if (($_GET["tri"] < 1) || ($_GET["tri"] > 8))
				$_GET["tri"] = 1;

			$lien_tri = "stats_abonnements.html?detail=3&jour=" . $_GET["jour"] . "&tri=";
		?>
			<table width="100%">
				<tr>
					<td width="30%" align="left" valign="middle" style="border-bottom:1px solid #000000;"><b>Membre</b> <a href="<?php echo $lien_tri; ?>1"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>2"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td align="left" valign="middle" style="border-bottom:1px solid #000000;"><b>Pack</b> <a href="<?php echo $lien_tri; ?>3"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>4"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="30%" align="left" valign="middle" style="border-bottom:1px solid #000000;"><b>Mode</b> <a href="<?php echo $lien_tri; ?>5"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>6"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
					<td width="9%" align="right" valign="middle" style="border-bottom:1px solid #000000;"><b>Tarif</b> <a href="<?php echo $lien_tri; ?>7"><img src="../images/tridesc.gif" border="0" alt="Asc" /></a><a href="<?php echo $lien_tri; ?>8"><img src="../images/triasc.gif" border="0" alt="Desc" /></a></td>
				</tr>
				<?php

				$tabpsd = array();
				$indmax = 0;

				$requete_jim = "SELECT * FROM abonnement_membre WHERE date_paiement>='$_GET[jour] 00:00:00' AND date_paiement<='$_GET[jour] 23:59:59' AND prix>0";
				if ($envoi_jim = mysqli_query($conn, $requete_jim)) {
					while ($data_jim = mysqli_fetch_array($envoi_jim, MYSQLI_ASSOC)) {

						$tabmid[$indmax] = $data_jim["id_membre"];
						$tabpsd[$indmax] = NomMembre($data_jim["id_membre"]);
						$tabmnt[$indmax] = $data_jim["prix"];
						$tabmod[$indmax] = $data_jim["mode_paiement"];


						if ($data_jim["renouvellement"] == 1)
							$tabmod[$indmax] .= " (REN)";
						if (trim($data_jim["transaction"]) != "")
							$tabmod[$indmax] .= "<small> (" . $data_jim["transaction"] . ")</small>";

						$requete_aps = "SELECT titre_membre FROM abonnement_pack WHERE id_abonnement_pack=$data_jim[id_abonnement_pack]";
						if ($envoi_aps = mysqli_query($conn, $requete_aps)) {
							if ($data_aps = mysqli_fetch_array($envoi_aps, MYSQLI_ASSOC))
								$tabpck[$indmax] = $data_aps["titre_membre"];
							else $tabpck[$indmax] = "Inconnu";
							mysqli_free_result($envoi_aps);
						} else die($conn->error);

						$indmax++;
					}
					
					mysqli_free_result($envoi_jim);


				} else {
					
					die($conn->error);
				}


				if ($indmax > 0) {
					if ($_GET["tri"] == 1)
						array_multisort($tabpsd, SORT_STRING, SORT_DESC, $tabpck, SORT_STRING, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC, $tabmid, $tabmod);
					else if ($_GET["tri"] == 2)
						array_multisort($tabpsd, SORT_STRING, SORT_ASC, $tabpck, SORT_STRING, SORT_ASC, $tabmnt, SORT_NUMERIC, SORT_DESC, $tabmid, $tabmod);
					else if ($_GET["tri"] == 3)
						array_multisort($tabpck, SORT_STRING, SORT_DESC, $tabpsd, SORT_STRING, SORT_DESC, $tabmnt, SORT_NUMERIC, SORT_DESC, $tabmid, $tabmod);
					else if ($_GET["tri"] == 4)
						array_multisort($tabpck, SORT_STRING, SORT_ASC, $tabpsd, SORT_STRING, SORT_DESC, $tabmnt, SORT_NUMERIC, SORT_DESC, $tabmid, $tabmod);
					else if ($_GET["tri"] == 5)
						array_multisort($tabmod, SORT_STRING, SORT_DESC, $tabpck, SORT_STRING, SORT_ASC, $tabpsd, SORT_STRING, SORT_DESC, $tabmnt, SORT_NUMERIC, SORT_DESC, $tabmid);
					else if ($_GET["tri"] == 6)
						array_multisort($tabmod, SORT_STRING, SORT_ASC, $tabpck, SORT_STRING, SORT_ASC, $tabpsd, SORT_STRING, SORT_DESC, $tabmnt, SORT_NUMERIC, SORT_DESC, $tabmid);
					else if ($_GET["tri"] == 7)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_DESC, $tabpck, SORT_STRING, SORT_DESC, $tabpsd, SORT_STRING, SORT_DESC, $tabmid, $tabmod);
					else if ($_GET["tri"] == 8)
						array_multisort($tabmnt, SORT_NUMERIC, SORT_ASC, $tabpck, SORT_STRING, SORT_ASC, $tabpsd, SORT_STRING, SORT_DESC, $tabmid, $tabmod);
				}

								
				echo '<pre>';
				var_dump($tabpsd);
				var_dump($requete_jim);
				echo '</pre>';
				die;


				for ($indtab = 0; $indtab < count($tabpsd); $indtab++) {
					$result = IP2Country(IPMembre($tabmid[$indtab]));
					
					
					


					$pays = '<img src="' . IconePays(trim($result)) . '" border="0" alt="' . strtoupper(trim($result)) . '" /> ';

					$stlmbr = "couleur_sexe_lien_" . SexeMembre($tabmid[$indtab]);
				?>
					<tr>
						<td align="left" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $pays; ?> <a href="membre_modifier.html?id_membre=<?php echo $tabmid[$indtab]; ?>" target="_blank" class="<?php echo $stlmbr; ?>"><?php echo $tabpsd[$indtab]; ?></a></td>
						<td align="left" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabpck[$indtab]; ?></td>
						<td align="left" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabmod[$indtab]; ?></td>
						<td align="right" valign="middle" style="border-bottom:1px dotted #000000;"><?php echo $tabmnt[$indtab]; ?></td>
					</tr>
				<?php
				}
				?>
			</table>
			<br /><a href="stats_abonnements.html?detail=2&mois=<?php echo substr($_GET["jour"], 0, 7); ?>"><img src="<?php echo GetParam("SCRIPT_URL"); ?>/style/images/bouton_retour.png" border="0" alt="Retour" /></a><br /><br />
		<?php
		}

		?>

	</div>
</div>
<div class="corps_encart_bas"></div>

<?php

include "footer.php";

?>