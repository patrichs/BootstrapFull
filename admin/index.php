<?php
require("php/checkAuth.php");
require_once("header.php");
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <!-- block -->
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <div class="muted pull-left"><i class='icon-user icon-black'></i> Users</div>
                    <div class="pull-right"><span class="badge badge-info" id="amountOfUsers"></span>
                    </div>
                </div>
                <div class="block-content collapse in">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>E-mail</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody id="userstbody">

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /block -->
            <a href="#" class="btn btn-primary btn-small btn-add-user"><i class='icon-plus icon-white'></i> Add a user</a>
        </div>
        <div class="span6">
            <!-- block -->
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <div class="muted pull-left"><i class='icon-asterisk icon-black'></i> Graph</div>
                    <div class="pull-right"><span class="badge badge-info">Today</span>
                    </div>
                </div>
                <div class="block-content collapse in">
                    <div id="todayUsersRegisteredLoggedInGraph" style="width:800px;height:200px"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <!-- block -->
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <div class="muted pull-left"><i class='icon-th-large icon-black'></i> Groups</div>
                    <div class="pull-right"><span class="badge badge-info" id="amountOfGroups"></span>
                    </div>
                </div>
                <div class="block-content collapse in">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Group ID</th>
                            <th>Group name</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody id="groupstbody">

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /block -->
            <a href="#" class="btn btn-primary btn-small btn-add-group"><i class='icon-plus icon-white'></i> Add a group</a>
        </div>
        <div class="span6">
            <!-- block -->
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <div class="muted pull-left"><i class='icon-warning-sign icon-black'></i> Log</div>
                        <div class="pull-right"><span class="badge badge-info" id="amountOfLogs">0</span>
                    </div>
                </div>
                    <div class="block-content collapse in">
                        <ul class="news-items" id="logItems">

                        </ul>
                    </div>
                </div>
            <a href="php/exportFullLogFile.php" class="btn btn-primary btn-small btn-export-log" download><i class='icon-download icon-white'></i> Export full log</a>
            </div>
            <!-- /block -->
        </div>
    </div>
<?php
include("footer.php");
?>