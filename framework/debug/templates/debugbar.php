<script src="http://appmedia.qq.com/media/tae/sdk/debugbar/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="http://appmedia.qq.com/media/tae/sdk/debugbar/css/style.css">
<style>
.quick_links_list .inner {
    margin-top: 2px;
}
.debugbar .wrap a:hover{
text-decoration:none;
}
</style>
<script type="text/javascript">
var qbar_actid = <?php echo TMConfig::get("tams_id"); ?>;

function show(id)
{
	jQuery(".quick_links_list").hide();
	jQuery("#"+id).show();
}

function close(id)
{
    jQuery("#"+id).hide();
}

function getDebugBarAjaxInfo(isfirst)
{
	jQuery.ajax({
	    type: "POST",
	    url: "/taedebug/showajaxbar",
	    data: {},
	    success: function(data){
            if(data != "")
            {
               jQuery("#divDebugbarBody").append(data);
            }
            if(isfirst == "1")
            {
            	jQuery("#divDebugbarBody .debug_panel_right").first().css("display", "block");
            }
	    }
	});
}

function selectUri()
{
	var uri = jQuery("#selectUri").val();
	jQuery(".debug_panel_right").hide();
	jQuery("#"+uri).show();
}

jQuery().ready(
    function(){
    	getDebugBarAjaxInfo("1");
    	setInterval("getDebugBarAjaxInfo();", 10000);
    }
);
</script>
<div id="divDebugbarRoot" class="debugbar">
    <div class="wrap" id="divDebugbarBody">
    <div style="float:left;">
    <select id="selectUri" onChange="selectUri();" style="margin-top:5px;margin-left:10px;width:125px;height:20px;">
    </select>
    </div>
    </div>
    <p title="隐藏工具条" class="panel_flex" id="pDbCtrl"><a href="javascript:hideDebugBar();" hidefocus="true" class="icon_toolbar_up" id="imgDbCCtrl"></a></p>
</div>
