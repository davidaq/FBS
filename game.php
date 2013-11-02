<?php
include 'header.inc.php';
$reportBtnDisabled = $result['round'] > 0 ? '':'disabled';
?>
<link rel="stylesheet" type="text/css" href="static/css/docs.css"/>
<style type="text/css">
#downloadHack {
    position: absolute;
    width: 1px;
    height: 1px;
    top: 0;
    left: 0;
}
</style>
<script type="text/javascript">
var records = {:json_encode($result['records'])};
$(function () {
    if({:USERID} != {:$result['hostuser']}) {
        $('#controls button, #rankBoard button').attr('disabled', true);
        $('#controls').parent().append('<br/><i class="icon-warning-sign"></i>You are not the host user, to prevent chaos you are not allowed to affect the simulation');
    }
    $('#rankBoard .item').each(function() {
        var tname = $(this).find('.teamName').html();
        if(!records[tname]) {
            $(this).find('.btn:first').addClass('btn-primary');
        }
    });
});
function resetRadio(element) {
    $(element).prev().find('.active').removeClass('active');
}
var prevBonus = 0;
var prevCash = 0;
function setBonus(element) {
    var tname = $(element).closest('tr').find('.teamName').html();
    $('#bonusDlg .teamName').html(tname);
    $.get('bonus.php', {'gid':{:R('gid')},'team':tname,'act':'get'}, function(result) {
        prevBonus = result['bonus'] * 1;
        prevCash = result['cash'] * 1;
        $('#bonusValue').val(prevBonus);
        $('#cashAfterBonus').html(prevCash);
        $('#bonusDlg').modal();
    }, 'JSON');
}
function refreshBonus() {
    var cash = prevCash;
    var bonus = $('#bonusValue').val() * 1;
    cash += bonus - prevBonus;
    $('#cashAfterBonus').html(cash);
}
function saveBonus() {
    var cash = prevCash;
    var bonus = $('#bonusValue').val();
    cash += bonus - prevBonus;
    var tname = $('#bonusDlg .teamName').html();
    $.post('bonus.php', {'gid':{:R('gid')},'team':tname,'act':'save','bonus':bonus,'cash':cash}, function() {
        document.location.reload();
    });
}
function decision(element) {
    var tname = $(element).closest('tr').find('.teamName').html();
    $('#decisionFormDlg input').val('');
    $('#decisionFormDlg .active').removeClass('active');
    $('#decisionFormDlg .agentUnchanged').addClass('active');
    $('#teamName').html(tname);
    $('#iTeamName').val(tname);
    setTimeout(function() {
        $('#decisionFormDlg .modal-body').scrollTop(0);
    }, 300);
    if(records[tname]) {
        var record = records[tname];
        $('#decisionFormDlg input').each(function() {
            var name = $(this).attr('name');
            if(name && record[name]) {
                $(this).val(record[name]);
            }
        });
        if(record['saleAgentAdded']) {
            var list = record['saleAgentAdded'];
            for(k in list) {
                var x = $('.agentMarket:eq(' + list[k] + ')');
                x.find('.agentAdd').addClass('active');
                x.find('.agentUnchanged').removeClass('active');
            }
        }
        if(record['saleAgentRemoved']) {
            var list = record['saleAgentRemoved'];
            for(k in list) {
                var x = $('.agentMarket:eq(' + list[k] + ')');
                x.find('.agentRem').addClass('active');
                x.find('.agentUnchanged').removeClass('active');
            }
        }
        if(record['marketsOrderedReport']) {
            var list = record['marketsOrderedReport'];
            for(k in list) {
                $('.reportMarket:eq(' + list[k] + ')').find('.btn').addClass('active');
            }
        }
        if(record['hireConsultant'] * 1) {
            $('#hireConsultant').addClass('active');
        }
    }
    $('#decisionFormDlg').modal();
}
function submitDecision(element) {
    $(element).attr('disabled', true);
    var data = {};
    $('#decisionFormDlg input').each(function() {
        var name = $(this).attr('name');
        if(name) {
            var val = $(this).val();
            if(!val)
                val = $(this).attr('placeholder');
            data[name] = val;
        }
    });
    var agentAdd = [];
    var agentRem = [];
    $('#decisionFormDlg .agentMarket').each(function(index) {
        if($(this).find('.agentAdd').hasClass('active'))
            agentAdd.push(index);
        else if($(this).find('.agentRem').hasClass('active'))
            agentRem.push(index);
    });
    data['saleAgentAdded'] = agentAdd;
    data['saleAgentRemoved'] = agentRem;
    var marketReports = [];
    $('#decisionFormDlg .reportMarket').each(function(index) {
        if($(this).find('.btn').hasClass('active'))
            marketReports.push(index);
    });
    data['marketsOrderedReport'] = marketReports;
    data['hireConsultant'] = $('#hireConsultant').hasClass('active') ? 1 : 0;
    var param = {'decision':data,'gid':{:R('gid')}};
    $.post(document.location.href, param, function(result) {
        document.location.reload();
    }, 'HTML');
}
function next() {
    var remain = $('#rankBoard .btn-primary').length;
    if(remain > 0) {
        alert(remain + ' teams haven\'t inputed decisions yet');
        return;
    }
	document.location.href = 'nextround.php?gid={:R('gid')}';
    /*
    $.get('nextround.php', {'cango':1, 'gid':{:R('gid')}}, function(result) {
    	if(result.ok) {
    	} else {
    		alert(result.message);
    	}
    }, "JSON");*/
}
function downloadReport() {
    $('#downloadHack').remove();
    var port = document.location.port ? ':' + document.location.port : '';
    var param = 'http://' + document.location.host + port + '{:BASE}/';
    param = '??' + param + '?{:$result['title']}?{:$result['round']}?{:R('gid')}<?php
        foreach($result['teams'] as $v) {
            echo '?' . urlencode(base64_encode($v['name']));
        }
    ?>??' + Math.random();
    var rpUrl = $('.report-btn:first').attr('href');
    $.get(rpUrl, function() {
        $('#controls').append('<img src="http://127.0.0.1:15301/do/' + param + '" id="downloadHack" onerror="downloadReportFail()"/>');
    });
}
function downloadReportFail() {
    $('#downloadDlg').modal();
}
</script>

<div id="downloadDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Download Report as PDF</h3>
  </div>
  <div class="modal-body form-horizontal" style="max-height:300px">
    <p><b>The report downloader is not running!</b></p>
    <p>In order to download the reports of this simulation, you must have the report downloader running on your computer.</p>
    <p>Download the corresponding programing from below according to your system.</p>
    <p>After download just extract the zip package to your favorite position and run the "FBSReportDownloader" executable.</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-info" data-dismiss="modal">Never mind</a>
    <a href="static/down/fbs_windows.zip" class="btn">For Windows</a>
    <a href="static/down/fbs_linux.zip" class="btn">For Linux</a>
    <a href="static/down/fbs_macos.zip" class="btn">For MacOS</a>
  </div>
</div>

<div id="bonusDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Set bonus for: <span class="teamName"></span></h3>
  </div>
  <div class="modal-body form-horizontal" style="max-height:300px">
    <div class="control-group">
        <label class="control-label" for="bonusValue">
            Bonus:
        </label>
        <div class="controls">
            <input type="text" id="bonusValue" onchange="refreshBonus()" onkeydown="refreshBonus()" onkeyup="refreshBonus()"/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">
            Cash after bonus:
        </label>
        <div class="controls">
            <div class="uneditable-input" id="cashAfterBonus">1000</div>
        </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <button onclick="saveBonus()" class="btn btn-primary">Submit</button>
  </div>
</div>

<div id="confirmDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Dangerous operation confirm</h3>
  </div>
  <div class="modal-body" style="max-height:300px">
    Do you really want to reset simulation status to previous round?
    This can not be undone and data of current round will be trashed.
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="{:PAGE}?gid={:R('gid')}&rewind=1" id="endBtn" class="btn btn-danger">Rewind</a>
  </div>
</div>

<div class="fixwidth-content">
    <div class="row-fluid">
        <div class="span4 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav affix" style="top:20px" id="sideNav">
                <li><a href="{:PAGE}?gid={:R('gid')}">
                    <h3>
                        {:$result['title']}<br/>
                        <small>
                            Round: {:$result['round']}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Click to refresh
                        </small>
                    </h3>
                </a></li>
                <li class="modal-footer well" style="text-align:center">
                    <div id="controls">
                        <div class="btn-group">
                            <button id="saveBtn" onclick="next()" class="btn btn-primary">Next round</button>
                            <button onclick="document.location.href = 'configuregame.php?gid={:R('gid')}'" class="btn">Config</button>
                            <button onclick="$('#confirmDlg').modal()" class="btn" <?php echo $reportBtnDisabled; ?>>Rewind</button>
                        </div>
                        <br/> <br/>
                        <button onclick="downloadReport()" class="btn" <?php echo $reportBtnDisabled; ?>>Download Reports as PDF</button>
                    </div>
                </li>
            </ul>
        </div>
        <div class="span8">
            <h1>
                Teams<br/>
                <small>Fill in the decision forms before running next round</small>
            </h1>
            <table id="rankBoard" class="table table-striped">
                <thead>
                    <tr>
                        <th width="5%">Rank</th>
                        <th width="32%">Team [Home market]</th>
                        <th width="25%">Cash without loan</th>
                        <th width="38%">Operation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $rank = NULL;
                        $prev = NULL;
                        foreach($result['teams'] as $k=>$v) {
                            if($v['property'] != $prev)
                                $rank = $k + 1;
                            $prev = $v['property'];
                    ?>
                        <tr class="item">
                            <td><?php echo $rank; ?></td>
                            <td>
                                <span class="teamName"><?php echo $v['name']; ?></span>
                                [<?php echo $result['markets'][$v['homeMarket']]; ?>]
                            </td>
                            <td><?php echo $v['property']; ?></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn" onclick="decision(this)">Decision</button>
                                    <button class="btn" <?php echo $reportBtnDisabled; ?> onclick="setBonus(this)">Bonus</button>
                                    <a target="_report" href="report.php?gid={:R('gid')}&team=<?php echo urlencode(base64_encode($v['name'])); ?>" class="btn report-btn" <?php echo $reportBtnDisabled; ?>>Report</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require('descision-form.php');?>

<?php include 'footer.inc.php'; ?>
