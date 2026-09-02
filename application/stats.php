<?php

include "../includes/global.php";
include "administration.php";

include "header.php";

?>
<div class="corps_encart_haut"><h1>Statistiques</h1></div>
<div class="corps_encart_corps">
<div class="corps_encart_corps_texte">

<br/>

<table>

<tr>
<td align="right" valign="middle"><b>Nombre de membres : </b></td>
<td align="left" valign="middle">
<?php

$datact=date("Y-m-d");

$requete_jm="SELECT COUNT(*) AS nbre FROM membre";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE sexe='H'";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrhom=$data_jm["nbre"];
	else $nbrhom=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE sexe='F'";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrfem=$data_jm["nbre"];
	else $nbrfem=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

?> - <b>Homme</b> : <?php echo $nbrhom; ?> / <?php echo sprintf("%0.2f",$nbrhom*100/$nbrrst); ?>% - <b>Femme</b> : <?php echo $nbrfem; ?> / <?php echo sprintf("%0.2f",$nbrfem*100/$nbrrst); ?>%
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Optin newsletter : </b></td>
<td align="left" valign="middle">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE newsletters=1";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Optin partenaires : </b></td>
<td align="left" valign="middle">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE partenaires=1";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres avec profil rempli : </b></td>
<td align="left" valign="middle">
<?php

$requete_ms="SELECT COUNT(*) AS nbre FROM membre WHERE profil_rempli=1";
if ($envoi_ms=mysqli_query($conn, $requete_ms))
{
	if ($data_ms=mysqli_fetch_array($envoi_ms, MYSQLI_ASSOC))
		 $nbrrst=$data_ms["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_ms);
}
else die($conn->error);

echo $nbrrst;
?>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres non validés : </b></td>
<td align="left" valign="middle"><a href="stats_nonvalides.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE etat=0";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres  validés : </b></td>
<td align="left" valign="middle"><a href="stats_valides.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE etat=1";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres bannis : </b></td>
<td align="left" valign="middle"><a href="stats_bannis.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE etat=2";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres supprimés : </b></td>
<td align="left" valign="middle"><a href="stats_supprimes.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE etat=3";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres suspendus : </b></td>
<td align="left" valign="middle"><a href="stats_suspendus.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE etat=4";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres connectés aujourd'hui : </b></td>
<td align="left" valign="middle"><a href="stats_connectes.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE date_activite>='".date("Y-m-d")." 00:00:00'";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Membres inscrits aujourd'hui : </b></td>
<td align="left" valign="middle"><a href="stats_inscrits.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre FROM membre WHERE date_inscription>='".date("Y-m-d")." 00:00:00' AND date_inscription<='".date("Y-m-d")." 23:59:59'";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

<tr>
<td align="right" valign="middle"><b>Abonnements aujourd'hui : </b></td>
<td align="left" valign="middle"><a href="stats_abonnements.html">
<?php

$requete_jm="SELECT COUNT(*) AS nbre,SUM(prix) AS total FROM abonnement_membre WHERE date_paiement>='".date("Y-m-d")." 00:00:00' AND date_paiement<='".date("Y-m-d")." 23:59:59' AND prix>0";
if ($envoi_jm=mysqli_query($conn, $requete_jm))
{
	if ($data_jm=mysqli_fetch_array($envoi_jm, MYSQLI_ASSOC))
		 $nbrrst=$data_jm["nbre"];
	else $nbrrst=0;
	mysqli_free_result($envoi_jm);
}
else die($conn->error);

echo $nbrrst;
?>
</a>
</td>
</tr>

</table>

<br/>

</div>
</div>
<div class="corps_encart_bas"></div>

<?php

include "footer.php";

?>