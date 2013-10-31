<?php include 'header.inc.php'; ?>

<div class="fixwidth-content well">
    You can create at most 5 sub-accounts <i>(admin user is unlimited)</i>.
    Grant sub-accounts to others to invite more user.
    You are allowed to delete your sub-accounts, the sub-accounts created by
    your sub-accounts will become your direct sub-account.
</div>

<div class="fixwidth-content">
    <h1>
        Sub-account List
        <?php if($result['remain'] > 0) { ?>
            <?php if($result['remain'] > 99) { ?>
                <small>You can create unlimited sub-accounts</small>
            <?php } else { ?>
                <small>You can create {:$result['remain']} more sub-account</small>
            <?php } ?>
            <button class="btn btn-primary" onclick="$('#createDlg').modal()">Create one</button>
        <?php } else { ?>
            <small>You can't create any more sub-account</small>
        <?php } ?>
    </h1>
</div>

<table class="table table-striped fixwidth-content">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="21%">User name</th>
            <th width="25%">Email</th>
            <th width="21%">Direct Sup-account</th>
            <th width="29%">Operation</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($result['list'] as $v) { ?>
        <tr>
            <td><?php echo $v['id']; ?></td>
            <td><?php echo $v['username']; ?></td>
            <td><?php echo $v['email']; ?></td>
            <td>
                <span class="tiped" title="#<?php echo $v['topid']; ?> <?php echo htmlspecialchars($v['topemail']);?>">
                    <?php echo $v['topname']; ?>
                </span>
            </td>
            <td>
                <button class="btn" onclick="del(this)">Delete</button>
                <button class="btn" onclick="rst(this)">Reset password</button>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<div id="createDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Create sub-account</h3>
  </div>
  <div class="modal-body">
    <form class="form-horizontal" action="{:PAGE}" method="post" id="createForm">
        <div class="control-group">
            <label class="control-label" for="username">
                User name
            </label>
            <div class="controls">
                <input type="text" id="username" name="username"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="username">
                Password
            </label>
            <div class="controls">
                <i class="uneditable-input">123456</i>
            </div>
        </div>
    </form>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="#" class="btn btn-primary" onclick="$('#createForm').submit()">Submit</a>
  </div>
</div>
<div id="confirmDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Confirm dangerous operation</h3>
  </div>
  <div class="modal-body">
    <p></p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-primary" data-dismiss="modal">No</a>
    <a href="#" class="btn btn-danger" onclick="yes()">Yes</a>
  </div>
</div>
<script type="text/javascript">
var yes = function() {
}
function getId(element) {
    var x = $(element).parent().parent().find('td');
    return {id:$(x[0]).html(), username:$(x[1]).html(), email:$(x[2]).html()};
}
function del(element) {
    var id = getId(element);
    $('#confirmDlg .modal-body p').html('Are you sure you want to <b>delete</b> account <b>#' + id.id + ' ' + id.username + ' [' + id.email + ']</b>');
    $('#confirmDlg').modal();
    yes = function() {
        document.location.href = '?del=1&id=' + id.id;
    }
}
function rst(element) {
    var id = getId(element);
    $('#confirmDlg .modal-body p').html('Are you sure you want <b>reset password</b> to "123456" for account <b>#' + id.id + ' ' + id.username + ' [' + id.email + ']</b>');
    $('#confirmDlg').modal();
    yes = function() {
        document.location.href = '?rst=1&id=' + id.id;
    }
}
</script>

<?php include 'footer.inc.php'; ?>
