<?php

include "../includes/global.php";
include "administration.php";

include "header.php";

if (!isset($_GET["datdeb"]))
	$_GET["datdeb"]="01/".date("m/Y");
if (!isset($_GET["datfin"]))
	$_GET["datfin"]=date("d/m/Y");
if (!isset($_GET["selper"]))
	$_GET["selper"]=0;

$_GET["datdeb"]=htmlentities(mysqli_real_escape_string($conn, urldecode(trim($_GET["datdeb"]))),ENT_QUOTES);
$_GET["datfin"]=htmlentities(mysqli_real_escape_string($conn, urldecode(trim($_GET["datfin"]))),ENT_QUOTES);
$_GET["selper"]=sprintf("%0d",urldecode(trim($_GET["selper"])));

$datmin="2017-03-01";

$requete_srs="SELECT MIN(date) AS datmin FROM stat_referencement";
if ($envoi_srs=mysqli_query($conn, $requete_srs))
{
	if ($data_srs=mysqli_fetch_array($envoi_srs, MYSQLI_ASSOC))
		$datmin=$data_srs["datmin"];
	mysqli_free_result($envoi_srs);
}
else die($conn->error);

$crireq="";

if ($_GET["datdeb"]!="")
{
	if (trim($crireq)!="")
		$crireq.=" AND ";

	$datdeb=date("Y-m-d 00:00:00",strtotime(DateTexteEnDateMySQL($_GET["datdeb"])));

	$crireq.="date>='$datdeb'";
}
if ($_GET["datfin"]!="")
{
	if (trim($crireq)!="")
		$crireq.=" AND ";

	$datfin=date("Y-m-d 23:59:59",strtotime(DateTexteEnDateMySQL($_GET["datfin"])));

	$crireq.="date<='$datfin'";
}

$chart_referencement_visites1="";
$chart_referencement_visites2="";
$chart_referencement_repartition_visites1="";
$chart_referencement_repartition_visites2="";
$chart_referencement_repartition_visites3="";

$requete_srs="SELECT * FROM stat_referencement";
if (trim($crireq)!="")
	$requete_srs.=" WHERE ".$crireq;
$requete_srs.=" ORDER BY date ASC";
if ($envoi_srs=mysqli_query($conn, $requete_srs))
{
	while ($data_srs=mysqli_fetch_array($envoi_srs, MYSQLI_ASSOC))
	{
		if (trim($chart_referencement_visites1)!="")
			$chart_referencement_visites1.=",";
		if (trim($chart_referencement_visites2)!="")
			$chart_referencement_visites2.=",";
		if (trim($chart_referencement_repartition_visites1)!="")
			$chart_referencement_repartition_visites1.=",";
		if (trim($chart_referencement_repartition_visites2)!="")
			$chart_referencement_repartition_visites2.=",";
		if (trim($chart_referencement_repartition_visites3)!="")
			$chart_referencement_repartition_visites3.=",";

		$chart_referencement_visites1.="{x:new Date(".sprintf("%0d",substr($data_srs["date"],0,4)).",".(sprintf("%0d",substr($data_srs["date"],5,2))-1).",".sprintf("%0d",substr($data_srs["date"],8,2))."),y:".$data_srs["visites_gratuites"]."}";
		$chart_referencement_visites2.="{x:new Date(".sprintf("%0d",substr($data_srs["date"],0,4)).",".(sprintf("%0d",substr($data_srs["date"],5,2))-1).",".sprintf("%0d",substr($data_srs["date"],8,2))."),y:".$data_srs["visites_payantes"]."}";

		$chart_referencement_repartition_visites1.="{x:new Date(".sprintf("%0d",substr($data_srs["date"],0,4)).",".(sprintf("%0d",substr($data_srs["date"],5,2))-1).",".sprintf("%0d",substr($data_srs["date"],8,2))."),y:".($data_srs["visites_directes"]+$data_srs["visites_gratuites"]+$data_srs["visites_payantes"])."-0-".$data_srs["visites_gratuites"]."-".$data_srs["visites_payantes"]."}";
		$chart_referencement_repartition_visites2.="{x:new Date(".sprintf("%0d",substr($data_srs["date"],0,4)).",".(sprintf("%0d",substr($data_srs["date"],5,2))-1).",".sprintf("%0d",substr($data_srs["date"],8,2))."),y:".$data_srs["visites_gratuites"]."}";
		$chart_referencement_repartition_visites3.="{x:new Date(".sprintf("%0d",substr($data_srs["date"],0,4)).",".(sprintf("%0d",substr($data_srs["date"],5,2))-1).",".sprintf("%0d",substr($data_srs["date"],8,2))."),y:".$data_srs["visites_payantes"]."}";

	}
	mysqli_free_result($envoi_srs);
}
else die($conn->error);

?>
<script type="text/javascript">
$(function()
{
	if ($.datepicker)
	{
		$.datepicker.setDefaults({
			closeText:"Fermer",
			prevText:"Précédent",
			nextText:"Suivant",
			currentText:"Aujourd'hui",
			monthNames:["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"],
			monthNamesShort:["jan","fév","mar","avr","mai","juin","juil","aoû","sep","oct","nov","déc"],
			dayNames:["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"],
			dayNamesShort:["dim","lun","mar","mer","jeu","ven","sam"],
			dayNamesMin:["D","L","M","M","J","V","S"],
			weekHeader:"Sem.",
			dateFormat:"dd/mm/yy",
			firstDay:1,
			isRTL:false,
			showMonthAfterYear:false,
			yearSuffix:""
		});

		$("#datdeb").datepicker();
		$("#datfin").datepicker();

		$("#datdeb,#datfin").datepicker("option","minDate",new Date(<?php echo sprintf("%0d",substr($datmin,0,4)); ?>,<?php echo (sprintf("%0d",substr($datmin,5,2))-1); ?>,<?php echo sprintf("%0d",substr($datmin,8,2)); ?>));
		$("#datdeb,#datfin").datepicker("option","maxDate",-1);

		$("#datdeb,#datfin").on("change",function(){
			$("#selper").val(0);
		});

		$("#selper").change(function(){
			selper=$(this).val();
			if (selper==1)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
			else if (selper==2)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-7d"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
			else if (selper==3)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+(((new Date()).getDay()+6)%7)+"d"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
			else if (selper==4)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+(7+(((new Date()).getDay()+6)%7))+"d"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(1+(((new Date()).getDay()+6)%7))+"d"));
			}
			else if (selper==5)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-31d"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
			else if (selper==6)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
			else if (selper==7)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -1m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d"));
			}
			else if (selper==8)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -2m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d -1m"));
			}
			else if (selper==9)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -3m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d -2m"));
			}
			else if (selper==10)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -4m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d -3m"));
			}
			else if (selper==11)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -5m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d -4m"));
			}
			else if (selper==12)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -6m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d -5m"));
			}
			else if (selper==13)
			{
				$("#datdeb").datepicker("setDate",$.datepicker._determineDate(null,"-"+((new Date()).getDate()-1)+"d -3m"));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-"+(new Date()).getDate()+"d"));
			}
			else if (selper==14)
			{
				$("#datdeb").datepicker("setDate",new Date((new Date()).getFullYear(),0,1));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
			else if (selper==15)
			{
				$("#datdeb").datepicker("setDate",new Date((new Date()).getFullYear()-1,0,1));
				$("#datfin").datepicker("setDate",new Date((new Date()).getFullYear()-1,11,31));
			}
			else if (selper==16)
			{
				$("#datdeb").datepicker("setDate",new Date(<?php echo sprintf("%0d",substr($datmin,0,4)); ?>,<?php echo (sprintf("%0d",substr($datmin,5,2))-1); ?>,<?php echo sprintf("%0d",substr($datmin,8,2)); ?>));
				$("#datfin").datepicker("setDate",$.datepicker._determineDate(null,"-1d"));
			}
		});
	}
});

$(document).ready(function(){

	CanvasJS.addCultureInfo("fr",
	{
		decimalSeparator:",",
		digitGroupSeparator:".",
		days:["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"],
		shortDays:["dim","lun","mar","mer","jeu","ven","sam"],
		months:["Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre"],
		shortMonths:["Jan","Fev","Mar","Avr","Mai","Jun","Jul","Aou","Sep","Oct","Nov","Dec"]
    });

	$("#chart_referencement_visites").CanvasJSChart({
		zoomEnabled:true,
		culture:"fr",
		toolTip:{
			shared:true
		},
        axisX:{
            valueFormatString:"D MMM YYYY" ,
            labelAngle:-50,
            indexLabelFontFamily:"Verdana",
            labelFontSize:12
        },
        axisY:{
			valueFormatString:"0",
			gridThickness:1,
            indexLabelFontFamily:"Verdana",
            labelFontSize:10
		},
		legend:{
			fontFamily:"Verdana",
			fontSize:12
		},
		data:[
			{
				type:"line",
				showInLegend:true,
				name:"visites gratuites",
				xValueType:"dateTime",
				dataPoints:[<?php echo $chart_referencement_visites1; ?>]
			},
			{
				type:"line",
				showInLegend:true,
				name:"visites payantes",
				xValueType:"dateTime",
				dataPoints:[<?php echo $chart_referencement_visites2; ?>]
			}
		]
	});

	$("#chart_referencement_repartition_visites").CanvasJSChart({
		zoomEnabled:true,
		culture:"fr",
		toolTip:{
			shared:true
		},
        axisX:{
            valueFormatString:"D MMM YYYY" ,
            labelAngle:-50,
            indexLabelFontFamily:"Verdana",
            labelFontSize:12
        },
        axisY:{
			valueFormatString:"0",
			gridThickness:1,
            indexLabelFontFamily:"Verdana",
            labelFontSize:10
		},
		legend:{
			fontFamily:"Verdana",
			fontSize:12
		},
		data:[
			{
				type:"stackedColumn100",
				showInLegend:true,
				name:"directes",
				xValueType:"dateTime",
				dataPoints:[<?php echo $chart_referencement_repartition_visites1; ?>]
			},
			{
				type:"stackedColumn100",
				showInLegend:true,
				name:"ref. gratuit",
				xValueType:"dateTime",
				dataPoints:[<?php echo $chart_referencement_repartition_visites2; ?>]
			},
			{
				type:"stackedColumn100",
				showInLegend:true,
				name:"campagnes",
				xValueType:"dateTime",
				dataPoints:[<?php echo $chart_referencement_repartition_visites3; ?>]
			}
		]
	});

	$(".canvasjs-chart-credit").hide();

	if ($(".js_charge_table").length)
	{
		$(".js_charge_table").each(function(i,elt)
		{
			id=$(elt).attr("id");
			ChargeTable(id,1);
		});
	}

	$("input[name=table_filter_reset]").click(function(){
		filtre=$(this).parent(".statsfilter").find("input[name=table_filter_search]").val();
		nomtab=$(this).parent(".statsfilter").next(".js_charge_table").attr("id");
		if (nomtab&&filtre!="")
		{
			$(this).parent(".statsfilter").find("input[name=table_filter_search]").val("");
			ChargeTable(nomtab,1);
		}
	});
	$("input[name=table_filter_submit]").click(function(){
		filtre=$(this).parent(".statsfilter").find("input[name=table_filter_search]").val();
		nomtab=$(this).parent(".statsfilter").next(".js_charge_table").attr("id");
		if (nomtab&&filtre!="")
			ChargeTable(nomtab,1,undefined,undefined,filtre);
	});
});

ChargeTable=function(nomtab,numpag,coltri,dirtri,filtre)
{
	if (nomtab!="")
	{
		var params="nomtab="+encodeURIComponent(nomtab);
		if (numpag!=undefined) params+="&numpag="+encodeURIComponent(numpag);
		if (coltri!=undefined) params+="&coltri="+encodeURIComponent(coltri);
		if (dirtri!=undefined) params+="&dirtri="+encodeURIComponent(dirtri);
		if (filtre!=undefined) params+="&filtre="+encodeURIComponent(filtre);
		if ($("#datdeb").length&&$("#datfin").length)
			params+="&datdeb="+encodeURIComponent($("#datdeb").val())+"&datfin="+encodeURIComponent($("#datfin").val());

		$("#"+nomtab).css("cursor","wait");
		$("#"+nomtab).fadeTo(0,0.5);
		$("#"+nomtab).append('<div style="position: absolute;top:0;left:0;width: 100%;height:100%;z-index:2;opacity:0.4;filter: alpha(opacity = 50)"></div>');

		$.get("./ajax_statistiques_referencement_tables.php?"+params,function(data,textStatus)
		{
			$("#"+nomtab).html(data);
			$("#"+nomtab).fadeTo(0,1);
			$("#"+nomtab).css("cursor","auto");

			$("#"+nomtab).html(data);
			$("#"+nomtab+" a").click(function(){
				href=$(this).attr("href");
				ChargeTable(nomtab,RecupereParametreURL(href,"numpag",1),RecupereParametreURL(href,"coltri","1"),RecupereParametreURL(href,"dirtri","0"),RecupereParametreURL(href,"filtre",""));
				return false;
			});
		});
	}
};

RecupereParametreURL=function(pURL,pParam,pDefault)
{
	var sURL=pURL.substring(pURL.indexOf("?")+1);
	var sVariables=sURL.split("&");
	var sParametre;
	var iIndTab;

	for (iIndTab=0;iIndTab<sVariables.length;iIndTab++)
	{
		sParametre=sVariables[iIndTab].split("=");
		if (sParametre[0]===pParam)
			return sParametre[1]===undefined?true:sParametre[1];
	}
	return pDefault;
};
</script>

<div class="corps_encart_haut"><h1>Statistiques du référencement (avec un jour de décalage)</h1></div>
<div class="corps_encart_corps">
	<div class="corps_encart_corps_texte">

	<br/>

	<form action="" method="get">

	<label>Date de début <input id="datdeb" type="text" name="datdeb" value="<?php if ($_GET["datdeb"]!="") echo $_GET["datdeb"]; ?>" size="7" /></label>
	<label>Date de fin <input id="datfin" type="text" name="datfin" value="<?php if ($_GET["datfin"]!="") echo $_GET["datfin"]; ?>" size="7" /></label>
	<label>Préselection

		<select name="selper" id="selper">
			<option value="0"<?php if ($_GET["selper"]==0) echo ' selected="selected"'; ?>>Période personnalisée</option>
			<option value="1"<?php if ($_GET["selper"]==1) echo ' selected="selected"'; ?>>Hier</option>
			<option value="2"<?php if ($_GET["selper"]==2) echo ' selected="selected"'; ?>>Les 7 derniers jours</option>
			<option value="3"<?php if ($_GET["selper"]==3) echo ' selected="selected"'; ?>>Cette semaine</option>
			<option value="4"<?php if ($_GET["selper"]==4) echo ' selected="selected"'; ?>>La semaine dernière</option>
			<option value="5"<?php if ($_GET["selper"]==5) echo ' selected="selected"'; ?>>Les 30 derniers jours</option>
			<option value="6"<?php if ($_GET["selper"]==6) echo ' selected="selected"'; ?>>Ce mois-ci</option>
			<option value="7"<?php if ($_GET["selper"]==7) echo ' selected="selected"'; ?>>Le mois dernier</option>
			<option value="8"<?php if ($_GET["selper"]==8) echo ' selected="selected"'; ?>>Il y a 2 mois</option>
			<option value="9"<?php if ($_GET["selper"]==9) echo ' selected="selected"'; ?>>Il y a 3 mois</option>
			<option value="10"<?php if ($_GET["selper"]==10) echo ' selected="selected"'; ?>>Il y a 4 mois</option>
			<option value="11"<?php if ($_GET["selper"]==11) echo ' selected="selected"'; ?>>Il y a 5 mois</option>
			<option value="12"<?php if ($_GET["selper"]==12) echo ' selected="selected"'; ?>>Il y a 6 mois</option>
			<option value="13"<?php if ($_GET["selper"]==13) echo ' selected="selected"'; ?>>Les 3 derniers mois</option>
			<option value="14"<?php if ($_GET["selper"]==14) echo ' selected="selected"'; ?>>Toute cette année</option>
			<option value="15"<?php if ($_GET["selper"]==15) echo ' selected="selected"'; ?>>L'année dernière</option>
			<option value="16"<?php if ($_GET["selper"]==16) echo ' selected="selected"'; ?>>Depuis le début</option>*
		</select>

		</label>

		<input type="submit" name="submit" value="Afficher" size="10" />

		</form>

		<br/>
		<hr/>
		<br/>
	</div>
</div>
<div class="corps_encart_bas"></div>

<div class="corps_encart_haut"><h1>Visites</h1></div>
<div class="corps_encart_corps">
	<div id="chart_referencement_visites" class="widget_stat widget_stat_chart"></div>
</div>
<div class="corps_encart_bas"></div>

<div class="corps_encart_haut"><h1>Répartition des visites</h1></div>
<div class="corps_encart_corps">
	<div id="chart_referencement_repartition_visites" class="widget_stat widget_stat_chart"></div>
</div>
<div class="corps_encart_bas"></div>

<div class="corps_encart_haut"><h1>Répartition des visites</h1></div>
<div class="corps_encart_corps">
	<div class="widget_stat">
		<div class="statsfilter">Filtre URL : <input size="30" value="" name="table_filter_search"> <input type="submit" value="Filtrer" name="table_filter_submit"> <input type="submit" value="Effacer" name="table_filter_reset"></div>
		<div id="table_page_entree" class="js_charge_table"></div>
	</div>
</div>
<div class="corps_encart_bas"></div>

<div class="corps_encart_haut"><h1>Sites d'origine</h1></div>
<div class="corps_encart_corps">
	<div class="widget_stat">
		<div class="statsfilter">Filtre URL : <input size="30" value="" name="table_filter_search"> <input type="submit" value="Filtrer" name="table_filter_submit"> <input type="submit" value="Effacer" name="table_filter_reset"></div>
		<div id="table_page_origine" class="js_charge_table"></div>
	</div>
</div>
<div class="corps_encart_bas"></div>

<?php

include "footer.php";

?>