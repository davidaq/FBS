<?php include 'header.inc.php'; ?>
<script type="text/javascript">
function confirmDel(id) {
    $('#endBtn').attr('href','gamelist.php?end='+id);
    $('#confirmDlg').modal();
}
</script>

<div id="confirmDlg" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Dangerous operation confirm</h3>
  </div>
  <div class="modal-body" style="max-height:300px">
    Do you really want to end this simulation?
    All data will be removed!
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
    <a href="#" id="endBtn" class="btn btn-danger">End</a>
  </div>
</div>

<div class="fixwidth-content">
    <div class="well">
        Each user can host multiple simulations at a time. In each simulation, you can add multiple users to help 
        doing the decision input.
        Only the creator may end, rewind, or run the simulation.
    </div>
    <h1>
        Simulation list
        <small>
            You can host unlimited simulations at a time
            <a class="btn btn-primary" href="startgame.php">Start a simulation</a>
        </small>
    </h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="25%">Title</th>
                <th width="20%">Host user</th>
                <th width="20%">Create time</th>
                <th width="10%">Round</th>
                <th width="25%">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($result as $f) { ?>
                <tr>
                    <td><?php echo $f['title']; ?></td>
                    <td><span class="tiped" title="#<?php echo $f['hostuser'].' '.$f['email']; ?>"><?php echo $f['username']; ?></span></td>
                    <td><?php echo $f['createtime']; ?></td>
                    <td><?php echo $f['round']; ?></td>
                    <td>
                        <a href="game.php?gid=<?php echo $f['id']; ?>" class="btn btn-primary">Enter</a>
                        <?php if($f['hostuser'] == USERID) { ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button onclick="confirmDel(<?php echo $f['id']; ?>)" class="btn btn-danger">End</button>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include 'footer.inc.php'; ?>
