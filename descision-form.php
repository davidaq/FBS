<div id="decisionFormDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Decision of <span id="teamName"></span></h3>
  </div>
  <div class="modal-body" style="max-height:350px">
    <div class="form-horizontal">
        <h4>Direct Sales</h4>
        <div class="control-group">
            <label class="control-label" for="sellStorage">
                Sell storage:
            </label>
            <div class="controls">
                <input type="text" id="sellStorage" placeholder="0" name="sellStorage"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="sellStorageIncome">
                Sell storage total income:
            </label>
            <div class="controls">
                <input type="text" id="sellStorageIncome" placeholder="0" name="sellStorageIncome"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="buyStorage">
                Buy storage:
            </label>
            <div class="controls">
                <input type="text" id="buyStorage" placeholder="0" name="buyStorage"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="buyStorageCost">
                Buy storage total cost:
            </label>
            <div class="controls">
                <input type="text" id="buyStorageCost" placeholder="0" name="buyStorageCost"/>
            </div>
        </div>
        <h4>Sales Support Bonus</h4>
        <div class="control-group">
            <label class="control-label" for="adBonus">
                <span class="tiped" title="Add funds directly to sales support">
                    <i class="icon-info-sign"></i>
                	Advertisement bonus:
                </span>
            </label>
            <div class="controls">
                <input type="text" id="adBonus" placeholder="0" name="adBonus"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="adBonusCost">
                <span class="tiped" title="Subtracted directly from total balance">
                    <i class="icon-info-sign"></i>
                	Advertisement cost:
                </span>
            </label>
            <div class="controls">
                <input type="text" id="adBonusCost" placeholder="0" name="adBonusCost"/>
            </div>
        </div>
        <h4>Loan and Payback</h4>
        <div class="control-group">
            <label class="control-label" for="loan">
                <span class="tiped" title="Negative number for payback">
                    <i class="icon-info-sign"></i>
                    Bank Loan:
                </span>
            </label>
            <div class="controls">
                <input type="text" id="loan" placeholder="0" name="loan"/>
            </div>
        </div>
        <h4>Human Resource</h4>
        <div class="control-group">
            <label class="control-label" for="HRworker">
                Hire worker count:
            </label>
            <div class="controls">
                <input type="text" id="HRworker" placeholder="0" name="workersOrdered"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="HRengineer">
                Hire engineer count:
            </label>
            <div class="controls">
                <input type="text" id="HRengineer" placeholder="0" name="engineersOrdered"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="HRsalaryWorker">
                Worker salary:
            </label>
            <div class="controls">
                <input type="text" id="HRsalaryWorker" placeholder="0" name="workersSalary"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="HRsalaryEngineer">
                Engineer salary:
            </label>
            <div class="controls">
                <input type="text" id="HRsalaryEngineer" placeholder="0" name="engineersSalary"/>
            </div>
        </div>
        <h4>Production Plan</h4>
        <div class="control-group">
            <label class="control-label" for="PPproduct">
                Products to manufacture:
            </label>
            <div class="controls">
                <input type="text" id="PPproduct" placeholder="0" name="productsOrdered"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="PPresearch">
                Research cost:
            </label>
            <div class="controls">
                <input type="text" id="PPresearch" placeholder="0" name="qualityCost"/>
            </div>
        </div>
        <h4>Sales Plan</h4>
        <div class="control-group">
            <label class="control-label" for="SPprice">
                Product Price per Unit:
            </label>
            <div class="controls">
                <input type="text" id="SPprice" placeholder="0" name="price"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="SPsupport">
                Sales Support Cost:
            </label>
            <div class="controls">
                <input type="text" id="SPsupport" placeholder="0" name="salesSupport"/>
            </div>
        </div>
        <h4>Sales Agents</h4>
        <?php foreach($result['markets'] as $k=>$v) { ?>
            <div class="control-group agentMarket">
                <label class="control-label">
                    <?php echo $v; ?>
                </label>
                <div class="controls">
                    <div class="btn-group" data-toggle="buttons-radio">
                        <button class="btn agentAdd"><i class="icon-plus-sign"></i></button>
                        <button class="btn agentRem"><i class="icon-minus-sign"></i></button>
                    </div>
                    <button onclick="resetRadio(this)" class="btn"><i class="icon-resize-horizontal"></i> Unset</button>
                </div>
            </div>
        <?php } ?>
        <h4>Market Report</h4>
        <?php foreach($result['markets'] as $k=>$v) { ?>
            <div class="control-group reportMarket">
                <label class="control-label">
                    <?php echo $v; ?>
                </label>
                <div class="controls">
                    <div class="btn-group" data-toggle="buttons-checkbox">
                        <button class="btn"><i class="icon-ok"></i> order</button>
                    </div>
                </div>
            </div>
        <?php } ?>
        <h4>Consultant</h4>
        <div class="control-group">
            <div class="controls">
                <div class="btn-group" data-toggle="buttons-checkbox">
                    <button class="btn" id="hireConsultant"><i class="icon-ok"></i> Hire consultant</button>
                </div>
            </div>
        </div>
        <input type="hidden" id="iTeamName" name="name"/>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <button class="btn btn-primary" onclick="submitDecision(this)">Submit</button>
  </div>
</div>
