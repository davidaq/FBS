<?php include 'header.inc.php'; ?>
<style type="text/css">
.element {
    padding: 5px;
    border: 1px solid #FFF;
    margin-bottom: 5px;
    margin-right: 5px;
}
.element i {
    cursor: pointer;
    visibility: hidden;
}
.element:hover {
    border: 1px solid #DDD;
}
.element:hover i {
    visibility: visible;
}
</style>
<script type="text/javascript">
var GID = {:$result['gid']};
$(function() {
    var cfg = [{:@$result['data']['cfg']}];
    if(cfg[0]) {
        cfg = cfg[0];
        $('#configForm input').each(function() {
            var name = $(this).attr('name');
            if(name && cfg[name]) {
                var val = cfg[name];
                if($(this).hasClass('input-small'))
                    val *= 100;
                $(this).val(val);
            }
        });
        for(k in cfg['players']) {
            addTeam(cfg['players'][k], cfg['markets'][k]);
        }
    }
    if(GID > 0) {
        $('#teams button, #basic button, #teams input, #basic input').attr('disabled', true);
    }
    function setSideNav() {
        var pos = $(this).scrollTop() + 50;
        var secs = $('section');
        $('#sideNav li').removeClass('active');
        var c = secs.length;
        for(i = 0; i < c; i++) {
            var t = $(secs[i]).offset().top;
            var b = $(secs[i]).height() + t;
            if(b > pos) {
                $('#sideNav li:eq(' + i + ')').addClass('active');
                break;
            }
        }
    }
    setSideNav();
    $(document).scroll(setSideNav);
    $('.tiped').tooltip();
    $.get('userlist.ajax.php', function(data) {
        $('#iAddHelper').attr('data-source', data);
    }, 'HTML');
});
function rm(element) {
    $(element).closest('.element').remove();
}
/*
function addHelper() {
    var name = $('#iAddHelper').val();
    if('' == name)
        return;
    $('#iAddHelper').val('');
    $('#iHelpers').append('<span class="element pull-left" style="display: block">' + name + ' <i onclick="rm(this)" class="icon-minus"></i></span>');
}
*/
function addTeam(name, team) {
    if(!name)
        name = $('#iAddTeam').val();
    if(!name)
        return;
    if(!team)
        team = ''
    team = team.replace(/"/g, "\\\"");
    $('#iAddTeam').val('');
    var html = '<tr class="element"><td></td><td>' + name + '</td><td><input type="text" value="' + team + '" placeholder="Home market"/></td><td>';
    if(!team)
        html += '<i onclick="rm(this);refreshTeamNumber();" class="icon-minus"></i>';
    html += '</td></tr>';
    $('#iTeam').append(html);
    refreshTeamNumber();
}
function refreshTeamNumber() {
    $('#iTeam tr').each(function(index) {
        $(this).find('td:first').html(index + 1);
    });
}
function assignMarkets() {
    var markets = {:DEFAULTMARKETS};
    var i = 0;
    $('#iTeam .element').each(function() {
        var $c = $(this).find('input');
        var pos;
        if(-1 != (pos = markets.indexOf($c.val()))) {
            markets = markets.slice(0, pos).concat(markets.slice(pos + 1, markets.length));
        }
    });
    $('#iTeam .element').each(function() {
        var $c = $(this).find('input');
        if($c.val() == '') {
            $c.val(markets[i++]);
        }
    });
}
function save() {
    var data = {};
    data['gid'] = GID;
    data['cfg'] = {};
    data['title'] = $('#iTitle').val();
    $('#configForm input').each(function() {
        var name = $(this).attr('name');
        if(name) {
            var val = $(this).val();
            if(!val && $(this).attr('placeholder'))
                val = $(this).attr('placeholder');
            if($(this).hasClass('input-small'))
                val /= 100;
            data['cfg'][name] = val;
        }
    });
    var teams = [];
    var markets = [];
    if(!(function() {
        var bad = false;
        $('#iTeam .element').each(function() {
            teams.push($(this).find('td:eq(1)').html());
            var m = $(this).find('input').val();
            if(!m)
                bad = true;
            markets.push(m);
        });
        if(!teams[0] || bad)
            return false;
        return true;
    })()) {
        alert('teams configuration has to be done!');
        return;
    }
    data['cfg']['players'] = teams;
    data['cfg']['markets'] = markets;
    $('#saveBtn').attr('disabled', true);
    $.post('configuregame.php', data, function(result) {
        result *= 1;
        if(result > 0)
            document.location.href = 'game.php?gid=' + result;
        else {
            alert('保存发生错误，可能为服务器内部问题');
            $('#saveBtn').attr('disabled', false);
        }
    }, 'HTML');
}
function cancel() {
    if(GID == -1) {
        document.location.href = '{:isset($_SERVER['HTTP_REFERER'])&& !strstr($_SERVER['HTTP_REFERER'],'startgame.php')?$_SERVER['HTTP_REFERER']:BASE}';
    } else {
        document.location.href = 'game.php?gid=' + GID;
    }
}
</script>
<link rel="stylesheet" type="text/css" href="static/css/docs.css"/>

<div class="fixwidth-content">
    <div class="row-fluid">
        <div class="span4 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav affix" style="top:20px" id="sideNav">
                <li><a href="#title">Simulation configuration</a></li>
                <li><a href="#teams">Teams and Home Markets</a></li>
                <li><a href="#basic">Basic Constants</a></li>
                <li><a href="#loan">Loan</a></li>
                <li><a href="#production">Production</a></li>
                <li><a href="#markets">Markets</a></li>
                <li><a href="#sales">Sales</a></li>
                <li class="modal-footer well" style="text-align:center">
                    <button id="saveBtn" onclick="save()" class="btn btn-primary">Save</button>
                    <button onclick="cancel()" class="btn">Cancel</button>
                </li>
            </ul>
        </div>
        <div class="span8 form-horizontal" id="configForm">
            <section id="title">
                <div class="page-header">
                    <h1>Simulation configuration</h1>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iTitle">
                        Title:
                    </label>
                    <div class="controls">
                        <input type="text" id="iTitle" value="{:@$result['data']['title']}">
                    </div>
                </div>
                <!--
                <div class="control-group">
                    <label class="control-label" for="iAddHelper">
                        <span class="tiped" rel="tooltip" title="Helpers are users who can help doing the decision input">
                        <i class="icon-info-sign"></i>
                        Helpers:
                        </span>
                    </label>
                    <div class="controls">
                        <div id="iHelpers" class="clearfix">
                        </div>
                        <form onsubmit="addHelper();return false;" action="" method="get" style="display: inline">
                            <input type="text" class="input-small" id="iAddHelper" data-provide="typeahead" data-items="5" data-source="" autocomplete="off"/>
                        </form>
                        <button onclick="addHelper()" class="btn"><i class="icon-plus"></i></button>
                    </div>
                </div>
                -->
            </section>
            <section id="teams">
                <div class="page-header">
                    <h1>
                        Teams and Home Markets<br/>
                        <small>Can not modify after start</small>
                    </h1>
                </div>
                <table class="table table-striped">
                    <thead>
                        <th width="5%">#</th>
                        <th width="40%">Team name</th>
                        <th width="40%">
                            Home market
                            &nbsp;&nbsp;&nbsp;
                            <button class="btn" onclick="assignMarkets()">Auto assign</button>
                        </th>
                        <th width="5%"></th>
                    </thead>
                    <tbody id="iTeam">
                    </tbody>
                </table>
                <div class="control-group">
                    <label class="control-label" for="iAddTeam">
                        Add team:
                    </label>
                    <div class="controls">
                        <input type="text" id="iAddTeam" placeholder="Team name"/>
                        <button onclick="addTeam()" class="btn"><i class="icon-plus"></i></button>
                    </div>
                </div>
            </section>
            <section id="basic">
                <div class="page-header">
                    <h1>
                        Basic Constants<br/>
                        <small>Can not modify after start</small>
                    </h1>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iCash">
                        Initial cash:
                    </label>
                    <div class="controls">
                        <input type="text" id="iCash" placeholder="1000000" name="Initial cash"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iMinQ">
                        Month in a quarter:
                    </label>
                    <div class="controls">
                        <input type="text" id="iMinQ" placeholder="3" name="Month in a quarter"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iHinM">
                        Work hours in a month:
                    </label>
                    <div class="controls">
                        <input type="text" id="iHinM" placeholder="168" name="Working hours in a month"/>
                    </div>
                </div>
            </section>
            <section id="loan">
                <div class="page-header">
                    <h1>Loan</h1>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iInterest">
                        Interest per quarter:
                    </label>
                    <div class="controls">
                        <div class="input-append">
                            <input type="text" id="iInterest" placeholder="2.5" class="input-small" name="Interest per quarter"/>
                            <span class="add-on">%</span>
                        </div>
                    </div>
                </div>
            </section>
            <section id="production">
                <div class="page-header">
                    <h1>Production</h1>
                </div>
                <h2>Component</h2>
                <div class="control-group">
                    <label class="control-label" for="iHinC">
                        Hours per item:
                    </label>
                    <div class="controls">
                        <input type="text" id="iHinC" placeholder="4" name="Hours in a component"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iWinC">
                        Workers per item:
                    </label>
                    <div class="controls">
                        <input type="text" id="iWinC" placeholder="1" name="Workers in a component"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iMCinC">
                        Material cost:
                    </label>
                    <div class="controls">
                        <input type="text" id="iMCinC" placeholder="200" name="Material cost per component"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iSCinC">
                        Storage cost per quarter:
                    </label>
                    <div class="controls">
                        <input type="text" id="iSCinC" placeholder="50" name="Component storage cost per quarter"/>
                    </div>
                </div>
                <h2>Product</h2>
                <div class="control-group">
                    <label class="control-label" for="iHinP">
                        Hours per item:
                    </label>
                    <div class="controls">
                        <input type="text" id="iHinP" placeholder="1" name="Hours in a product"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iEinP">
                        Engineer per item:
                    </label>
                    <div class="controls">
                        <input type="text" id="iEinP" placeholder="4" name="Engineers in a product"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iMCinP">
                        Material cost:
                    </label>
                    <div class="controls">
                        <input type="text" id="iMCinP" placeholder="100" name="Material cost per product"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iCinP">
                        Component per item:
                    </label>
                    <div class="controls">
                        <input type="text" id="iCinP" placeholder="4" name="Components in a product"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iSCinP">
                        Storage cost per quarter:
                    </label>
                    <div class="controls">
                        <input type="text" id="iSCinP" placeholder="50" name="Product storage cost per quarter"/>
                    </div>
                </div>
            </section>
            <section id="markets">
                <div class="page-header">
                    <h1>Markets</h1>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iPopulation">
                        Market population:
                    </label>
                    <div class="controls">
                        <input type="text" id="iPopulation" placeholder="100000" name="Market population"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iPenetration">
                        Market penetration:
                    </label>
                    <div class="controls">
                        <div class="input-append">
                            <input type="text" id="iPenetration" placeholder="3" class="input-small" name="Penetration"/>
                            <span class="add-on">%</span>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iReportCost">
                        Market report cost:
                    </label>
                    <div class="controls">
                        <input type="text" id="iReportCost" placeholder="500000" name="Market report cost per market quarter"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iConsultant">
                        Consultant cost:
                    </label>
                    <div class="controls">
                        <input type="text" id="iConsultant" placeholder="1000000" name="Consultant cost per 30 minutes"/>
                    </div>
                </div>
            </section>
            <section id="sales">
                <div class="page-header">
                    <h1>Sales</h1>
                </div>

                <h2>Agent cost</h2>
                <div class="control-group">
                    <label class="control-label" for="iAddAgent">
                        Add agent cost:
                    </label>
                    <div class="controls">
                        <input type="text" id="iAddAgent" placeholder="500000" name="Adding agent cost"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iRemoveAgent">
                        Remove agent cost:
                    </label>
                    <div class="controls">
                        <input type="text" id="iRemoveAgent" placeholder="250000" name="Removing agent cost"/>
                    </div>
                </div>

                <h2>Market share calculation</h2>
                <div class="control-group">
                    <label class="control-label" for="iPriceInfluence">
                        Price initial influence:
                    </label>
                    <div class="controls">
                        <div class="input-append">
                            <input type="text" id="iPriceInfluence" placeholder="70" class="input-small" name="priceInfluence"/>
                            <span class="add-on">%</span>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iQualityInfluence">
                        Quality influence:
                    </label>
                    <div class="controls">
                        <div class="input-append">
                            <input type="text" id="iQualityInfluence" placeholder="15" class="input-small" name="qualityInfluence"/>
                            <span class="add-on">%</span>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iSalesPowerInfluence">
                        Sales power influence:
                    </label>
                    <div class="controls">
                        <div class="input-append">
                            <input type="text" id="iSalesPowerInfluence" placeholder="15" class="input-small" name="salesPowerInfluence"/>
                            <span class="add-on">%</span>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iThreshold1">
                        <span class="tiped" title="Price higher than this value will start to gain influence on market share">
                            <i class="icon-info-sign"></i>
                            Price threshold:
                        </span>
                    </label>
                    <div class="controls">
                        <input type="text" id="iThreshold1" placeholder="2500" name="priceThreshold1"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="iThreshold2">
                        <span class="tiped" title="Price higher than this value have absolute influence on market share">
                            <i class="icon-info-sign"></i>
                            Price max threshold:
                        </span>
                    </label>
                    <div class="controls">
                        <input type="text" id="iThreshold2" placeholder="4000" name="priceThreshold2"/>
                    </div>
                </div>
            </section>
            <div style="height:200px">
            </div>
        </div>
    </div>
</div>
<?php include 'footer.inc.php'; ?>
