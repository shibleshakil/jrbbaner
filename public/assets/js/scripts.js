(function(window, undefined) {
'use strict';

/*
NOTE:
------
PLACE HERE YOUR OWN JAVASCRIPT CODE IF NEEDED
WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR JAVASCRIPT CODE PLEASE CONSIDER WRITING YOUR SCRIPT HERE.  */

})(window);

function blockBodyOnAjaxRequest() {
    $.blockUI({
        message: '<div class="feather icon-refresh-cw icon-spin font-medium-2"></div>',
        overlayCSS: {
            backgroundColor: '#FFF',
            opacity: 0.8,
            cursor: 'wait'
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: 'transparent'
        }
    });
}

function deleteData(url) {
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-warning mr-10",
            cancelButton: "btn btn-danger ml-1"
        }
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: url,
                type: "DELETE",
                data: {
                    "_token": $('#csrfToken').val(),
                },
                beforeSend: function () {
                    blockBodyOnAjaxRequest();
                },
                complete: function () {
                    $('body').unblock();
                },
                success: function (res) {
                    Swal.fire({
                        title: res.title,
                        text: res.msg,
                        icon: res.type,
                        confirmButtonClass: "btn btn-success"
                    }).then(function () {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 403) {
                        Swal.fire({
                            title: "Access Denied",
                            text: xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : "Sorry, you’re not authorized to perform this action.",
                            icon: "error"
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Something went wrong. Please try again later.",
                            icon: "error"
                        });
                    }
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: "Cancelled",
                text: "No action taken 🙂",
                icon: "error",
                confirmButtonClass: "btn btn-success"
            });
        }
    });
}

function restoreData(url) {
    Swal.fire({
        title: "Are you sure?",
        text: "You want to restore this data!",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: 'No',
        confirmButtonText: "Yes, Restore",
        confirmButtonClass: "btn btn-warning mr-10",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        console.log("HII");
        if (result.value) {
            $.ajax({
                url: url,
                type: "POST", // or POST, depending on your route
                data: {
                    "_token": $('#csrfToken').val(),
                },
                beforeSend: function () {
                    blockBodyOnAjaxRequest();
                },
                complete: function () {
                    $('body').unblock();
                },

                success: function (res) {
                    Swal.fire({
                        title: res.title ?? "Restored!",
                        text: res.msg ?? "The data has been successfully restored.",
                        icon: res.type ?? "success",
                        confirmButtonClass: "btn btn-success"
                    }).then(function () {
                        location.reload();
                    });
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: "Cancelled",
                text: "No action taken 🙂",
                icon: "info",
                confirmButtonClass: "btn btn-success"
            });
        }
    });
}


