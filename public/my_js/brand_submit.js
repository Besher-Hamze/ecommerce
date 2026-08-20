/**
 * Created by Cesaer on 21/05/2017.
 */

$(document).ready(function () {
    ////////// saving data ////////////////////
    $("#save").click(function (e) {
        var saveBtn = $(this);
        e.preventDefault();
        $(".help-block").html("");
        $("div").removeClass("has-error");
        var status = true;

        $(".req").each(function () {
            _this = $(this);
            if (_this.val() == "") {
                _this
                    .closest("div")
                    .find(".help-block")
                    .html("<strong>يرجى تعبئة هذا الحقل بالبيانات</strong>");
                _this.closest("div").addClass("has-error");
                _this.focus();
                status = false;
            }
        });

        if (status == true) {
            // saveBtn.text('جاري الحفظ ....');
            var data = $(document).find("form").serialize();
            // var form_data = new FormData();
            // form_data.append("customFile", $('#image')[0].files[0]);

            // data.append("image", $('#image')[0].files[0]);
            var urlajax = $("#myForm").attr("action");
            var form = $("#myForm");
            var formData = new FormData(form[0]);
            $.ajax({
                url: urlajax, // router
                method: "POST",
                data: formData,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,

                success: function (res) {
                    if (res.success == true) {
                        $("#myForm")[0].reset();
                        new PNotify({
                            title: "تم",
                            text: res.msg,
                            type: "success",
                            hideAfter: 4000,
                            styling: "bootstrap3",
                        });
                        if (saveBtn.text == "حفظ") {
                            $(".form-control").each(function () {
                                var control = $(this);
                                if (!control.is(":disabled")) {
                                    control.val("");
                                }
                            });
                        }
                    } else {
                        new PNotify({
                            title: res.title,
                            text: res.errors,
                            type: "error",
                            hideAfter: 4000,
                            styling: "bootstrap3",
                        });
                    }
                    saveBtn.text("حفظ");
                },
                error: function (xhr) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $("#" + key + "_error").text(value[0]);
                        $("#" + key + "_error")
                            .closest("div")
                            .addClass("has-error");
                        $("#" + key + "_error").focus();
                    });
                },
            });
        }
    });
});

$(document).ready(function () {
    ////////// updating data ////////////////////
    $("#update").click(function (e) {
        var saveBtn = $(this);
        e.preventDefault();
        $(".help-block").html("");
        $("div").removeClass("has-error");
        var status = true;

        $(".req").each(function () {
            _this = $(this);
            if (_this.val() == "") {
                _this
                    .closest("div")
                    .find(".help-block")
                    .html("<strong>يرجى تعبئة هذا الحقل بالبيانات</strong>");
                _this.closest("div").addClass("has-error");
                _this.focus();
                status = false;
            }
        });
        if (status == true) {
            // saveBtn.text('جاري الحفظ ....');
            var data = $(document).find("form").serialize();
            var urlajax = $("#myForm").attr("action");
            var form = $("#myForm");
            var formData = new FormData(form[0]);
            $.ajax({
                url: urlajax, // router
                method: "POST",
                data: formData,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    if (res.success == true) {
                        new PNotify({
                            title: "تم",
                            text: res.msg,
                            type: "success",
                            hideAfter: 4000,
                            styling: "bootstrap3",
                        });
                        if (saveBtn.text == "حفظ") {
                            $(".form-control").each(function () {
                                var control = $(this);
                                if (!control.is(":disabled")) {
                                    control.val("");
                                }
                            });
                        }
                    } else {
                        new PNotify({
                            title: res.title,
                            text: res.msg,
                            type: "error",
                            hideAfter: 4000,
                            styling: "bootstrap3",
                        });
                    }
                    saveBtn.text("حفظ");
                },
                error: function (xhr) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $("#" + key + "_error").text(value[0]);
                        $("#" + key + "_error")
                            .closest("div")
                            .addClass("has-error");
                        $("#" + key + "_error").focus();
                    });
                },
            });
        }
    });
});
//validate quantity
