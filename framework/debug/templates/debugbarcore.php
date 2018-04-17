<script>
<?php
        $options = "";
	    foreach($debugArray as $key => $debug)
	    {
	        $uri = str_replace(array("/",".","#"), "_", $key);
	        $options .= <<<EOF
	        <option value="{$uri}">{$key}</option>
EOF;
	    }
?>
jQuery("#selectUri").append('<?php echo $options;?>');
</script>
<?php
    $i = 0;
    foreach($debugArray as $key => $debug)
    {
        $gets = $debug["gets"];
        $posts = $debug["posts"];
        $files = $debug["files"];

        $actionTrackData = $debug["actionTrackData"];
        $logData = $debug["logData"];
        $debuggerSqlData = $debug["debuggerSqlData"];
        $defectSqlData = $debug["defectSqlData"];
        $timerData = $debug["timerData"];
        $amplificationRatioData = $debug["amplificationRatioData"];

//       if($i == 0)
//       {
            $style = "display: none";
//        }else{
//            $style = "display: none";
//        }

        $uri = str_replace(array("/",".","#"), "_", $key);
    ?>
    <div class="debug_panel_right" id="<?php echo $uri;?>" style="<?php echo $style;?>">
      <div class="mode_quicklinks" >
      <img src="http://appmedia.qq.com/media/tae/sdk/debugbar/images/debug/view.png"/> <a href="javascript:show('<?php echo $uri;?>_divValues');">变量情况</a>
        <div style="display:none;" class="quick_links_list" id="<?php echo $uri;?>_divValues">
          <div style="width:300px;" class="inner" >
            <span style="position:absolute;right:6px;top:6px;"><a href="javascript:close('<?php echo $uri;?>_divValues');" style="font-size:14px;">X</a></span>
            <div class="panel_list">
              <div class="list">
                <a class="vname">request情况</a>
                <ul>
                <?php

                echo "<li>--GET:</li>";
                foreach($gets as $key => $value)
                {
                    echo "<li>$key: $value</li>";
                }
                echo "<li>--POST:</li>";
                foreach($posts as $key => $value)
                {
                    echo "<li>$key: $value</li>";
                }
                echo "<li>--FILES:</li>";
                foreach($files as $key => $value)
                {
                    echo "<li>$key:<br/>\n";
                    foreach($value as $valueKey => $valueItem)
                    {
                        echo "&nbsp;&nbsp;$valueKey : $valueItem<br/>\n";
                    }
                    echo "</li>";
                }
                ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mode_quicklinks" >
        <img src="http://appmedia.qq.com/media/tae/sdk/debugbar/images/debug/log.png"/> <a href="javascript:show('<?php echo $uri;?>_divActionTrack');">行为监测</a>
        <div style="display:none;" class="quick_links_list" id="<?php echo $uri;?>_divActionTrack">
          <div style="width:500px;overflow-x:auto !important;" class="inner" >
            <span style="position:absolute;right:6px;top:6px;"><a href="javascript:close('<?php echo $uri;?>_divActionTrack');" style="font-size:14px;">X</a></span>
            <div class="panel_list" style="width:480px;">
              <div class="list">
                <a class="vname">Result:</a>
                <ul>
                <?php
                foreach($actionTrackData as $item)
                {
                    $qq = $item["qq"];
                    $actionId = $item["actionId"];
                    $campaignId = $item["campaignId"];
                    echo "<li>qq: $qq, actionId: $actionId, campaignId: $campaignId</li>";
                }
                ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mode_quicklinks" >
        <img src="http://appmedia.qq.com/media/tae/sdk/debugbar/images/debug/log.png"/> <a href="javascript:show('<?php echo $uri;?>_divLog');">运行日志</a>
        <div style="display:none;" class="quick_links_list" id="<?php echo $uri;?>_divLog">
          <div style="width:600px;overflow-x:auto !important;" class="inner" >
            <span style="position:absolute;right:6px;top:6px;"><a href="javascript:close('<?php echo $uri;?>_divLog');" style="font-size:14px;">X</a></span>
            <div class="panel_list" style="width:580px;">
              <div class="list">
                <a class="vname">Log:</a>
                <ul>
                <?php
                foreach($logData as $value)
                {
                    echo "<li>{$value["status"]}: {$value["log"]}</li>";
                }
                ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mode_quicklinks" >
        <img src="http://appmedia.qq.com/media/tae/sdk/debugbar/images/debug/database.png"/> <a href="javascript:show('<?php echo $uri;?>_divSql');">SQL语句</a>
        <div style="display:none;" class="quick_links_list" id="<?php echo $uri;?>_divSql">
          <div style="width:800px;overflow-x:auto !important;" class="inner" >
            <span style="position:absolute;right:6px;top:6px;"><a href="javascript:close('<?php echo $uri;?>_divSql');" style="font-size:14px;">X</a></span>
            <div class="panel_list" style="width:750px;">
              <div class="list">
                <a class="vname">该URI执行的SQL:</a>
                <ul>
                <?php
                foreach($debuggerSqlData as $key => $value)
                {
                    $time = sprintf('%.3f', $value["time"] * 1000);
                    $call = $value['call'];
                    echo "<li><strong>$key</strong>;<br/> 消耗时间：{$time}ms, 执行次数：{$call}次</li>";
                }
                ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mode_quicklinks" >
        <img src="http://appmedia.qq.com/media/tae/sdk/debugbar/images/debug/database.png"/> <a href="javascript:show('<?php echo $uri;?>_divExplain');">需要优化的sql</a>
        <div style="display:none;" class="quick_links_list" id="<?php echo $uri;?>_divExplain">
          <div style="width:500px;overflow-x:auto !important;" class="inner" >
            <span style="position:absolute;right:6px;top:6px;"><a href="javascript:close('<?php echo $uri;?>_divExplain');" style="font-size:14px;">X</a></span>
            <div class="panel_list" style="width:480px">
              <div class="list">
                <a class="vname">需要优化的sql:</a>
                <ul>
                <?php
                foreach($defectSqlData as $item)
                {
                    echo "<li><strong>- $item</strong></li>";
//                    if($item["Extra"] == "Using filesort"
//                        || $item["Extra"] == "Using temporary"){
//                      echo "<li><strong>$key</strong><br/> extra: {$item['Extra']}</li>";
//                    }else if($item["Extra"] == "Using where"
//                        && ($item["type"] == "ALL" or $item["type"] == "index")){
//                        echo "<li><strong>$key</strong><br/> type: {$item['type']}, extra: {$item['Extra']}</li>";
//                    }
                }
                ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mode_quicklinks">
      <img src="http://appmedia.qq.com/media/tae/sdk/debugbar/images/debug/time.png"/>
      <a href="javascript:show('<?php echo $uri;?>_divTimer');">放大和时间: <?php echo sprintf('%.0f', ($timerData['total']["time"]) * 1000);?>ms</a>
        <div style="display:none;" class="quick_links_list" id="<?php echo $uri;?>_divTimer">
          <div style="width:500px;overflow-x:auto !important;" class="inner" >
            <span style="position:absolute;right:6px;top:6px;"><a href="javascript:close('<?php echo $uri;?>_divTimer');" style="font-size:14px;">X</a></span>
            <div class="panel_list" style="width:450px">
              <div class="list">
                <a class="vname">Timer:</a>
                <ul>
                <?php
                foreach($timerData as $key => $timer)
                {
                    $time = sprintf('%.3f', $timer["time"] * 1000);
                    $call = $timer["call"];
                    echo "<li>$key: 消耗时间：{$time}ms, 执行次数：{$call}次</li>";
                }
                ?>
                </ul>
                <a class="vname">放大率:</a>
                <ul>
                <?php
                foreach($amplificationRatioData as $key => $ratio)
                {
                    $ratio = $ratio * 100;
                    echo "<li>{$key}的放大率为: {$ratio}%</li>";
                }
                ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
        $i++;
    }
    ?>
