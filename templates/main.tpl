<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>[+lang.S2P_module_title+]</title>
		<link rel="stylesheet" type="text/css" href="media/style[+theme+]/style.css" />
		<script type="text/javascript" src="media/script/tabpane.js"></script>
		<script type="text/javascript" src="media/script/jquery/jquery.min.js"></script>
<!--
		<script type="text/javascript" src="media/script/mootools/mootools.js"></script>
		<script type="text/javascript" src="media/calendar/datepicker.js"></script>
		<script type="text/javascript" src="media/script/mootools/moodx.js"></script>
-->
		<script type="text/javascript" src="../assets/modules/s2pmodule/js/s2pmodule.js"></script>
		<script type="text/javascript">
		var cookiepath = '[+cookiepath+]';
		var $j = jQuery.noConflict();
/*
			function loadTemplateVars(tplId) {
				$j('#tvloading').css('display','block');
				$j.ajax(
				{
					'type':'POST',
					'url':'[+ajax.endpoint+]',
					'data': {'tplID':tplId},
					'success': function(r,s)
					{
						document.getElementById('results').innerHTML = r;
						document.getElementById('tvloading').style.display = 'none';
					}
				});
			}
			
			function save() {
				document.newdocumentparent.submit();
			}   
*/

/*
			function setMoveValue(pId, pName) {
			  if (pId==0 || checkParentChildRelation(pId, pName)) {
				document.newdocumentparent.new_parent.value=pId;
				document.getElementById('parentName').innerHTML = "Parent: <strong>" + pId + "</strong> (" + pName + ")";
			  }
			}
*/

/*
			function checkParentChildRelation(pId, pName) {
				var sp;
				var id = document.newdocumentparent.id.value;
				var tdoc = parent.tree.document;
				var pn = (tdoc.getElementById) ? tdoc.getElementById("node"+pId) : tdoc.all["node"+pId];
				if (!pn) return;
					while (pn.p>0) {
						pn = (tdoc.getElementById) ? tdoc.getElementById("node"+pn.p) : tdoc.all["node"+pn.p];
						if (pn.id.substr(4)==id) {
							alert("Illegal Parent");
							return;
						}
					}
				
				return true;
			}
*/
			
/*
			$j(function() {
				var dpOffset = [+datepicker.offset+];
				var dpformat = "[+datetime.format+]" + ' hh:mm:00';
				new DatePicker($('date_pubdate'), {'yearOffset' : dpOffset, 'format' : dpformat });
				new DatePicker($('date_unpubdate'), {'yearOffset' : dpOffset, 'format' : dpformat});
				new DatePicker($('date_createdon'), {'yearOffset' : dpOffset, 'format' : dpformat});
				new DatePicker($('date_editedon'), {'yearOffset' : dpOffset, 'format' : dpformat});
			});
*/
			function config() {
				$j("input[name='tabAction']").val( "tabConfig" );
				$j("form[name='mainform']").submit();
				return false;
			}
			function manually() {
				$j("input[name='tabAction']").val( "tabManually" );
				$j("form[name='mainform']").submit();
				return false;
			}
			$j(document).ready(function(){
				$j(".server > a").click(function(){
					if( !$j(this).hasClass("open") ) {
						$j(this).closest("ul").next("div").slideDown("fast");
						$j(this).addClass("open");
					}
					else {
						$j(this).closest("ul").next("div").slideUp("fast");
						$j(this).removeClass("open");
					}
					return false;
				});
				$j(".server > a").closest("ul").next("div").slideUp(0);
				$j(".tab").click(function(){ $j(".sectionBody > p").remove(); });

				if( $j("input[name='scheduled_switch']:checked").val() == "0" ) $j(".scheduled_date").slideUp(0);
				$j("input[name='scheduled_switch']").change(function(v){
					if( v.target.value == "1" ) $j(".scheduled_date").slideDown("fast");
					else $j(".scheduled_date").slideUp("fast");
				});
				if( $j("input[name='update_alert']:checked").val() == "0" ) $j(".alert_mailto").slideUp(0);
				$j("input[name='update_alert']").change(function(v){
					if( v.target.value == "1" ) $j(".alert_mailto").slideDown("fast");
					else $j(".alert_mailto").slideUp("fast");
				});
			});
		</script>
		<style type="text/css">
			.latest { float: right; }
			.latest > span { color: #0000FF; font-weight: bold; }
			.tab-page { border-radius: 0 5px 5px 5px; }
			.tab-page a { text-decoration: none; }
			.tab-page h3 { margin-bottom: 3px; border-left: 5px solid #658f1a; padding-left: 5px; margin-top: 15px; }
			.tab-page #manually_deploy { position: relative; float: right; right: 100px; }
			.tab-page #exclusion_table table { border-collapse: collapse; margin-right: 20px; }
			.tab-page #exclusion_table tr:hover { background-color: #F3C3C3; }
			.tab-page #exclusion_table td { border: 1px solid #DDD; padding: 3px; }
			.tab-page textarea { width: 250px; }
			.tab-page #Button1 a { font-size: 200%; }
			.tab-page table.grid { text-align: center; }
			.tab-page table.grid > tbody > tr:nth-child(odd) { background-color: #EEEEEE; }
			.tab-page table.grid > tbody > tr:nth-child(odd) { background-color: #EEEEEE; }
			.tab-page table.grid th { text-align: center; }
			.tab-page table.grid td, .tab-page table.grid th { border: 1px solid silver; }
			.tab-page table.grid td label { display: block; width: 100%; }
			.tab-page .pagenate { display: block; float: right; margin-top: -23px; }
			.tab-page .server_block { overflow: hidden; }
			.tab-page .server_block input { margin-left: 30px; }
			.tab-page .server_block h3:first-child { margin-top: 0; }
			.tab-page .custom_button a {
				color: #222c36;
				font-weight: bold;
				font-size: 12px;
				background: #c7ced2 url(../assets/modules/s2pmodule/templates/button-gradient.png) repeat-x top left;
				padding: 4px 6px;
				white-space: nowrap;
				vertical-align: top;
				text-decoration: none;
				-moz-border-radius: 4px;
				-webkit-border-radius: 4px;
				border-radius: 4px;
				-webkit-text-shadow: 1px 1px 0 #fff;
				-moz-text-shadow: 0 1px 1px #fff;
				text-shadow: 0 1px 1px #fff;
				border: 1px solid #8ea4be;
				outline: none;
			}
		</style>
	</head>
	<body>
		<h1>[+lang.S2P_module_title+]</h1>
		<div id="actions">
			<ul class="actionButtons">
				<li id="Button1"><a href="#" onclick="config();"><img src="media/style/[+theme+]/images/icons/save.png" />[+lang.S2P_save+]</a></li>
				<li id="Button2"><a href="#" onclick="document.location.href='index.php?a=2';"><img src="media/style[+theme+]/images/icons/stop.png" /> [+lang.S2P_close+]</a></li>
			</ul>
		</div>
		<form name="mainform" method="POST" action="#">
			<input type="hidden" name="tabAction" value="" />
			<input type="hidden" name="stg_url" value="[+stg_url+]" />
			<div class="section">
				<div class="sectionHeader">[+lang.S2P_action_title+]</div>
				<div class="sectionBody"> 
					[+message+]
					<div class="latest">[+lang.S2P_last_updatedon+]<span>[+last_updatedon+]</span></div>
					<div class="tab-pane" id="s2pModulePane">
						<script type="text/javascript"> 
							tps2p = new WebFXTabPane(document.getElementById('s2pModulePane')); 
						</script>
						<div class="tab-page" id="tabSettings">  
							<h2 class="tab">[+lang.S2P_settings+]</h2>  
							<script type="text/javascript">tps2p.addTabPage(document.getElementById("tabSettings"));</script>
							[+view.settings+]
						</div>
						<div class="tab-page" id="tabExclusion">  
							<h2 class="tab">[+lang.S2P_exclusion+]</h2>  
							<script type="text/javascript">tps2p.addTabPage(document.getElementById("tabExclusion" ));</script> 
							[+view.exclusion+]
						</div>
						<div class="tab-page" id="tabManually">  
							<h2 class="tab">[+lang.S2P_manually+]</h2>  
							<script type="text/javascript">tps2p.addTabPage(document.getElementById("tabManually"));</script>
							<p>[+lang.S2P_manually_desc+]</p>
							<ul class="actionButtons">
								<li class="custom_button">
									<a href="#" onclick="manually()">
										<img src="media/style/[+theme+]/images/icons/save.png" />[+lang.S2P_manually_manual_deploy+]
									</a>
								</li>
							</ul>
						</div>
						<div class="tab-page" id="tabHistory">  
							<h2 class="tab">[+lang.S2P_history+]</h2>  
							<script type="text/javascript">tps2p.addTabPage(document.getElementById("tabHistory" ));</script> 
							[+view.history+]
						</div>
					</div>
				</div>
			</div>
		</form>
	</body>
</html>
