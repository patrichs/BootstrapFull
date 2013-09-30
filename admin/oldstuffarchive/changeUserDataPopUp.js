$(document).ready(function() {
    //$(document).on("click", ".btn-users", function() {
    $(".btn-users").live("click", function() {
        var splitIdString = this.id.split(",");

        $.msgbox("<p>Change the appropriate field and then click 'Save' to save all the changes.<br>Leave the password field blank to not change the password.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Username: ", value: splitIdString[1], required: true},
                {type: "password", label: "Password: ", value: "", required: false},
                {type: "text", label: "E-mail: ", value: splitIdString[2], required: true},
                {type: "text", label: "User ID (DO NOT CHANGE) : ", value: splitIdString[0] , required: true}
            ],
            buttons : [
                {type: "submit", value: "Save"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(Username, Password, Email, Userid) {
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
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage})
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage})
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: data.replyMessage})
            }
        });
    });
});