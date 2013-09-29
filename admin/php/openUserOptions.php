<?php
$userid = $_POST["userid"];
?>

<script>
$(function () {
    $('.msgbox-prompt-user-change').live ('click', function (e) {

    $.msgbox("<p>Change the appropriate field and then click 'Save' to save all the changes.</p>", {
        type    : "prompt",
        inputs  : [
            {type: "text", label: "Username: ", value: "", required: true},
            {type: "text", label: "Password: ", value: "", required: true},
            {type: "text", label: "E-mail: ", value: "", required: true},
            {type: "text", label: "User ID (DO NOT CHANGE) : ", value: <?php echo $userid ?>, required: true}
        ],
        buttons : [
            {type: "submit", value: "Save"},
            {type: "cancel", value: "Cancel"}
        ]
    },
    function(Username, Password, Email, Userid) {
    if (Username.length > 0) {

    var obj =
    {
        objUsername: Username,
        objPassword: Password,
        objEmail: Email,
        objUserId: Userid
    };

    var convertJson = JSON.stringify(obj);

    $.post("php/changeUserData.php", { "obj": convertJson },
    function(data){
        if (data.isSuccess === 1)
        {
            $.msgbox(data.replyMessage, {type: "success"});
            ajaxCallFunction();
        }
        else
        {
            $.msgbox("sum4thing happen bad: " + data.replyMessage, {type: "error"});
        }
        }, "json");

    } else {
    $.msgbox("pls why cancel?!", {type: "error"});
    }
    });
    });
});
</script>