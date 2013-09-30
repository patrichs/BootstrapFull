$(document).ready(function() {
    //$(document).on("click", ".btn-users", function() {
    $(".btn-add-user").live("click", function() {

        $.msgbox("<p>Fill in all fields and click 'Add' to add a user.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Username: ", value: "", required: true},
                {type: "password", label: "Password: ", value: "", required: true},
                {type: "text", label: "E-mail: ", value: "", required: true},
            ],
            buttons : [
                {type: "submit", value: "Add"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(Username, Password, Email) {
            if (Username.length > 0) {

                var obj =
                {
                    "register": 1,
                    "username": Username,
                    "password": Password,
                    "email": Email
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/makeRegister.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgbox(data.replyMessage, {type: "success"});
                        }
                        else
                        {
                            $.msgbox("Error: " + data.replyMessage, {type: "error"});
                        }
                    }, "json");

            } else {
                $.msgbox("Action cancelled. No changes were made.", {type: "error"});
            }
        });
    });
});