<?php
require("php/checkAuth.php");
require_once("header.php");
?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"><i class='icon-user icon-black'></i> Drag and drop files</div>
                    </div>
                    <div class="block-content collapse in">
                        <form action="php/uploadFilesDropZone.php" class="dropzone"></form>
                    </div>
                </div>
                <!-- /block -->
            </div>
        </div>
    </div>
<?php
include("footer.php");
?>