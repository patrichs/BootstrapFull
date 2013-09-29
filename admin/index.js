$(document).ready(function() {

    var obj =
    {
        "infopls": 1
    };

    var newObj = JSON.stringify(obj);

    $.post("php/returnAuthInfo.php", { "obj": newObj },
        function(data){
            if (data.isSuccess === 1)
            {
                $("#userLoggedIn span").text(data.replyMessage);
            }
            else
            {
                console.log("Login failed: " + data.replyMessage);
            }
        }, "json");
});