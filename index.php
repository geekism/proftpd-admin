<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 *
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);

$field_userid   = $cfg['field_userid'];
$field_id       = $cfg['field_id'];
$field_uid      = $cfg['field_uid'];
$field_ugid     = $cfg['field_ugid'];
$field_homedir  = $cfg['field_homedir'];
$field_shell    = $cfg['field_shell'];
$field_title    = $cfg['field_title'];
$field_name     = $cfg['field_name'];
$field_company  = $cfg['field_company'];
$field_email    = $cfg['field_email'];
$field_disabled = $cfg['field_disabled'];

$field_login_count    = $cfg['field_login_count'];
$field_last_login     = $cfg['field_last_login'];
$field_bytes_in_used  = $cfg['field_bytes_in_used'];
$field_bytes_out_used = $cfg['field_bytes_out_used'];
$field_files_in_used  = $cfg['field_files_in_used'];
$field_files_out_used = $cfg['field_files_out_used'];

$all_groups = $ac->get_groups();
$groups = $ac->parse_groups();
$all_users = $ac->get_users();
$users = array();

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
/* parse filter  */
$userfilter = array();
$ufilter="";
// see config_example.php on howto activate
if ($cfg['userid_filter_separator'] != "") {
  $ufilter = isset($_REQUEST["uf"]) ? $_REQUEST["uf"] : "";
  foreach ($all_users as $user) {
    $pos = strpos($user[$field_userid], $cfg['userid_filter_separator']);
    // userid's should not start with a - !
    if ($pos != FALSE) {
      $prefix = substr($user[$field_userid], 0, $pos);
      if(@$userfilter[$prefix] == "") {
        $userfilter[$prefix] = $prefix;
      }
    }
  }
}

/* filter users */
if (!empty($all_users)) {
  foreach ($all_users as $user) { 
    if ($ufilter != "") {
      if ($ufilter == "None" && strpos($user[$field_userid], $cfg['userid_filter_separator'])) {
        // filter is None and user has a prefix
        continue;
      }
      if ($ufilter != "None" && strncmp($user[$field_userid], $ufilter, strlen($ufilter)) != 0) {
        // filter is something else and user does not have a prefix
	continue;
      }
    }
    $users[] = $user;
  }
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if(!is_array($all_users)) { ?>
<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <p>Currently there are no registered users.</p>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <a class="btn btn-primary pull-right" href="add_user.php" role="button">Add user &raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>
<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <?php if (count($userfilter) > 0) { ?>
          <!-- Filter toolbar -->
          <div class="form-group">
            <label>Prefix filter:</label>
            <div class="btn-group" role="group">
              <a type="button" class="btn btn-default" href="users.php">All users</a>
              <a type="button" class="btn btn-default" href="users.php?uf=None">No prefix</a>
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" id="idPrefix" data-toggle="dropdown" aria-expanded="false">Prefix <span class="caret"></span></button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="idPrefix">
                <?php foreach ($userfilter as $uf) { ?>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="users.php?uf=<?php echo $uf; ?>"><?php echo $uf; ?></a></li>
                <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <?php } ?>
          <!-- User table -->
          <div class="form-group">
            <table class="table table-striped table-condensed sortable">
              <thead>
                <th>UID</th>
                <th><span class="glyphicon glyphicon-user" aria-hidden="true" title="User name"></th>
                <th><span class="glyphicon glyphicon-tag" aria-hidden="true" title="Main group"></th>
                <th class="hidden-xs hidden-sm" data-defaultsort="disabled"><span class="glyphicon glyphicon-tags" aria-hidden="true" title="Additional groups"></th>
                <th class="hidden-xs hidden-sm hidden-md"><span class="glyphicon glyphicon-time" aria-hidden="true" title="Last login"></th>
                <th class="hidden-xs hidden-sm"><span class="glyphicon glyphicon-list-alt" aria-hidden="true" title="Login count"></th>
                <th class="hidden-xs"><span class="glyphicon glyphicon-signal" aria-hidden="true" title="Uploaded MBs"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true" title="Uploaded MBs"></th>
                <th class="hidden-xs"><span class="glyphicon glyphicon-signal" aria-hidden="true" title="Downloaded MBs"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true" title="Downloaded MBs"></th>
                <th class="hidden-xs"><span class="glyphicon glyphicon-file" aria-hidden="true" title="Uploaded files"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true" title="Uploaded files"></th>
                <th class="hidden-xs"><span class="glyphicon glyphicon-file" aria-hidden="true" title="Downloaded files"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true" title="Downloaded files"></th>
                <th class="hidden-xs hidden-sm"><span class="glyphicon glyphicon-home" aria-hidden="true" title="Home directory"></th>
                <th class="hidden-xs hidden-sm"><span class="glyphicon glyphicon-envelope" aria-hidden="true" title="E-mail"></th>
                <th><span class="glyphicon glyphicon-lock" aria-hidden="true" title="Suspended"></th>
                <th data-defaultsort="disabled"></th>
              </thead>
              <tbody>
                <?php foreach ($users as $user) { ?>
                  <tr>
                    <td class="pull-middle"><?php echo $user[$field_uid]; ?></td>
                    <td class="pull-middle"><a href="edit_user.php?action=show&<?php echo $field_id; ?>=<?php echo $user[$field_id]; ?>"><?php echo $user[$field_userid]; ?></a></td>
                    <td class="pull-middle"><?php echo $all_groups[$user[$field_ugid]]; ?></td>
                    <td class="pull-middle hidden-xs hidden-sm">
                      <?php if (empty($groups[$user[$field_userid]])) { ?>
                        none
                      <?php } else { ?>
                        <div class="dropdown">
                          <button type="button" class="btn btn-default btn-xs dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown"><?php echo count($groups[$user[$field_userid]]); ?> groups <span class="caret"></span></button>
                          <ul class="dropdown-menu" role="menu">
                            <?php foreach ($groups[$user[$field_userid]] as $g_group) { ?>
                              <li role="presentation"><a role="menuitem"><?php echo $g_group; ?></a></li>
                            <?php } ?>
                          </ul>
                        </div>
                      <?php } ?>
                    </td>
                    <td class="pull-middle hidden-xs hidden-sm hidden-md"><?php echo $user[$field_last_login]; ?></td>
                    <td class="pull-middle hidden-xs hidden-sm"><?php echo $user[$field_login_count]; ?></td>
		<td class="pull-middle hidden-xs"><?php echo human_filesize($user[$field_bytes_in_used]); ?></td>
		<td class="pull-middle hidden-xs"><?php echo human_filesize($user[$field_bytes_out_used]); ?></td>
                    <td class="pull-middle hidden-xs"><?php echo $user[$field_files_in_used]; ?></td>
                    <td class="pull-middle hidden-xs"><?php echo $user[$field_files_out_used]; ?></td>
                    <td class="pull-middle hidden-xs hidden-sm"><?php echo $user[$field_homedir]; ?></td>
                    <td class="pull-middle hidden-xs hidden-sm"><?php echo $user[$field_email]; ?></td>
                    <td class="pull-middle"><?php echo ($user[$field_disabled] ? 'Yes' : 'No'); ?></td>
                    <td class="pull-middle">
                      <div class="btn-toolbar pull-right" role="toolbar">
                        <a class="btn-group" role="group" href="edit_user.php?action=show&<?php echo $field_id; ?>=<?php echo $user[$field_id]; ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                        <a class="btn-group" role="group" href="remove_user.php?action=remove&<?php echo $field_id; ?>=<?php echo $user[$field_id]; ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <a class="btn btn-primary pull-right" href="add_user.php" role="button">Add user &raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php include ("includes/messages.php"); ?>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Groups</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-xs-8 col-sm-7 col-md-6">
          <p>Groups in database:</p>
        </div>
        <div class="col-xs-4 col-sm-5 col-md-6">
          <p><span class="form-control"><?php echo $ac->get_group_count(); ?></span></p>
        </div>
        <div class="col-xs-8 col-sm-7 col-md-6">
          <p>Empty groups in database:</p>
        </div>
        <div class="col-xs-4 col-sm-5 col-md-6">
          <p><span class="form-control"><?php echo $ac->get_group_count(true); ?></span></p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
          <p><a class="btn btn-primary pull-right" href="groups.php" role="button">View groups &raquo;</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-xs-8 col-sm-7 col-md-6">
          <p>Users in database:</p>
        </div>
        <div class="col-xs-4 col-sm-5 col-md-6">
          <p><span class="form-control"><?php echo $ac->get_user_count(); ?></span></p>
        </div>
        <div class="col-xs-8 col-sm-7 col-md-6">
          <p>Deactivated users in database:</p>
        </div>
        <div class="col-xs-4 col-sm-5 col-md-6">
          <p><span class="form-control"><?php echo $ac->get_user_count(true); ?></span></p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
          <p><a class="btn btn-primary pull-right" href="users.php" role="button">View users &raquo;</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
shell_exec('/usr/bin/vnstat -u -i '.$cfg['interface'].' >/dev/null 2>&1');
$stats = system('/usr/bin/vnstati --transparent --style 1 -nh -ne -s -i '.$cfg['interface'].' -o stats.png');
$hours = system('/usr/bin/vnstati --transparent --style 1 -nh -ne -h -i '.$cfg['interface'].' -o hours.png');
$top10 = system('/usr/bin/vnstati --transparent --style 1 -nh -ne -t -i '.$cfg['interface'].' -o top10.png');
$month = system('/usr/bin/vnstati --transparent --style 1 -nh -ne -m -i '.$cfg['interface'].' -o month.png');
$daily = system('/usr/bin/vnstati --transparent --style 1 -nh -ne -d -i '.$cfg['interface'].' -o days.png');
?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Transfer Statistics (Summary)</h3>
    </div>
    <div class="panel-body">
      <div class="row">
	<center><img src="stats.png" alt="data"><br></center>
      </div>
    </div>
  </div>
</div>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Transfer Statistics (24 Hours)</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <center><img src="hours.png" alt="data"><br></center>
      </div>
    </div>
  </div>
</div>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Transfer Statistics (Top 10)</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <center><img src="top10.png" alt="data"><br></center>
      </div>
    </div>
  </div>
</div>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Transfer Statistics (Months)</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <center><img src="month.png" alt="data"><br></center>
      </div>
    </div>
  </div>
</div>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Transfer Statistics (Last 30 Days)</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <center><img src="days.png" alt="data"><br></center>
      </div>
    </div>
  </div>
</div>

<?php include ("includes/footer.php"); ?>
