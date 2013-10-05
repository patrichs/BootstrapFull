$(document).ready(function() {

    /* This bit returns the amount of users in the database */
    function checkForAuth()
    {

        var obj =
        {
            "infopls": 1
        };

        var newObj = JSON.stringify(obj);

        $.post("php/returnAuthInfo.php", { "obj": newObj },
            function(data){
                if (data.isSuccess === 1)
                {
                    $("#userLoggedIn").text(data.replyMessage);
                }
                else
                {
                    $.msgGrowl({type: "error", title: "Error", text: data.replyMessage})
                }
            }, "json");
    }

    checkForAuth();

    /* This updates the users table with all users found in the database */

    function updateUsersTable()
    {
        $("#userstbody").empty();
        $.post("php/returnAllUsers.php",
            function (data)
            {
                var output = "";
                for (var i in data.amountOfRows)
                {
                    output += "<tr>";
                    output += "<td>" + data.amountOfRows[i].userid + "</td>";
                    output += "<td>" + data.amountOfRows[i].username + "</td>";
                    output += "<td>" + data.amountOfRows[i].email + "</td>";
                    //lame hack but whatever works
                    output += "<td>" + "<a href='#' class='btn btn-small btn-primary btn-edit-user' id=" +
                        data.amountOfRows[i].userid + "," + data.amountOfRows[i].username + "," +
                        data.amountOfRows[i].email + ">Edit</a>   "
                        + "<a href='#' class='btn btn-small btn-warning btn-delete-user' id=" +
                        data.amountOfRows[i].userid + ">Delete</a>" + "</td>";
                    output += "</tr>";
                }

                $("#userstbody").append(output);

                $("#amountOfUsers").text(data.amountOfUsers);
            }, "json");
    }

    /* This updates the groups table with all groups found in the database */

    function updateGroupsTable()
    {
        $("#groupstbody").empty();
        $.post("php/returnAllGroups.php",
            function (data)
            {
                var output = "";
                for (var i in data.amountOfRows)
                {
                    output += "<tr>";
                    output += "<td>" + data.amountOfRows[i].groupid + "</td>";
                    output += "<td>" + data.amountOfRows[i].groupname + "</td>";
                    //lame hack but whatever works
                    output += "<td>" + "<a href='#' class='btn btn-small btn-primary btn-edit-group' id=" +
                        data.amountOfRows[i].groupid + "," + data.amountOfRows[i].groupname + ">Edit</a>   "
                        + "<a href='#' class='btn btn-small btn-warning btn-delete-group' id=" +
                        data.amountOfRows[i].groupid + ">Delete</a>" + "</td>";
                    output += "</tr>";
                }

                $("#groupstbody").append(output);

                $("#amountOfGroups").text(data.amountOfGroups);
            }, "json");
    }

    /* Delete users */
    $(".btn-delete-user").live("click", function()
    {
        if (confirm("Are you sure you want to delete this user?"))
        {
            var obj =
            {
                objUserId: this.id
            };

            var convertJson = JSON.stringify(obj);

            $.post("php/deleteUserAccount.php", { "obj": convertJson },
                function(data){
                    if (data.isSuccess === 1)
                    {
                        $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                        updateUsersTable();
                    }
                    else
                    {
                        $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                    }
                }, "json");
        }
        else
        {
            $.msgGrowl({type: "warning", title: "Action Cancelled", text: "The user was NOT deleted."});
        }
    });

    /* Delete group */
    $(".btn-delete-group").live("click", function()
    {
        if (confirm("Are you sure you want to delete this group?"))
        {
            var obj =
            {
                objGroupId: this.id
            };

            var convertJson = JSON.stringify(obj);

            $.post("php/deleteGroup.php", { "obj": convertJson },
                function(data){
                    if (data.isSuccess === 1)
                    {
                        $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                        updateGroupsTable();
                    }
                    else
                    {
                        $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                    }
                }, "json");
        }
        else
        {
            $.msgGrowl({type: "warning", title: "Action Cancelled", text: "The group was NOT deleted."});
        }
    });

    /* Modifying a user */
    $(".btn-edit-user").live("click", function() {
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
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateUsersTable();
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "User data was NOT changed."});
            }
        });
    });

    /* Adding a user */
    $(".btn-add-user").live("click", function() {

        $.msgbox("<p>Fill in all fields and click 'Add' to add a user.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Username: ", value: "", required: true},
                {type: "password", label: "Password: ", value: "", required: true},
                {type: "text", label: "E-mail: ", value: "", required: true}
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
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateUsersTable();
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "No data was changed or added."});
            }
        });
    });

    /* Adding a group */
    $(".btn-add-group").live("click", function() {

        $.msgbox("<p>Fill in all fields and click 'Add' to add a group.</p>", {
            type    : "prompt",
            inputs  : [
                {type: "text", label: "Group name: ", value: "", required: true}
            ],
            buttons : [
                {type: "submit", value: "Add"},
                {type: "cancel", value: "Cancel"}
            ]
        }, function(groupname) {
            if (groupname.length > 0) {

                var obj =
                {
                    "addgroup": 1,
                    "groupname": groupname
                };

                var convertJson = JSON.stringify(obj);

                $.post("php/makeGroup.php", { "obj": convertJson },
                    function(data){
                        if (data.isSuccess === 1)
                        {
                            $.msgGrowl({type: "success", title: "Success", text: data.replyMessage});
                            updateGroupsTable();
                        }
                        else
                        {
                            $.msgGrowl({type: "error", title: "Error", text: data.replyMessage});
                        }
                    }, "json");

            } else {
                $.msgGrowl({type: "warning", title: "Action Cancelled", text: "No data was changed or added."});
            }
        });
    });

    /* Once page is loaded start update the users table */
    updateUsersTable();
    updateGroupsTable();


});