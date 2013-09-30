<?php
include("header.php");
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <!-- block -->
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <div class="muted pull-left">Users</div>
                    <div class="pull-right"><span class="badge badge-info" id="amountOfUsers"></span>
                    </div>
                </div>
                <div class="block-content collapse in">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
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
            <a href="#" class="btn btn-primary btn-small btn-add-user">Add a user</a>
        </div>
    </div>
<?php
include("footer.php");
?>