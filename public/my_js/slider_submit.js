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
        document.getElementById("text_en").value = $("#summernote").code();
        document.getElementById("text_ar").value =$("#eng_summernote").code();
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
        $(".num").each(function () {
            _this = $(this);
            if (_this.val() < 1) {
                _this
                    .closest("div")
                    .find(".help-block")
                    .html(
                        "<strong>يجب أن تكون قيمة هذا الحقل أكبر تماما من الصفر </strong>"
                    );
                _this.closest("div").addClass("has-error");
                _this.focus();
                status = false;
            }
        });
        $(".date").each(function () {
            _this = $(this);
            var d = new Date(_this.val());
            if (d.getFullYear() < 2023) {
                _this
                    .closest("div")
                    .find(".help-block")
                    .html(
                        "<strong>يجب أن تكون قيمة هذا الحقل أكبر تماما من تاريخ السنة الحالية </strong>"
                    );
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
                        $('#summernote').code('');
                        $('#eng_summernote').code('');
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
                            // $('select').val('').change();
                            // $('.dz-image-preview').remove();
                            // $('input[name="files[]"]').remove();
                            // $('#myForm')[0].reset();
                            // $('input[type="text"], textarea').val('');
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
            });
           
        }
    });
});
//update
$(document).ready(function () {
    ////////// update data ////////////////////
    $("#update").click(function (e) {
        var saveBtn = $(this);
        e.preventDefault();
        $(".help-block").html("");
        $("div").removeClass("has-error");
        var status = true;
        document.getElementById("text_en").value = $("#eng_summernote").code();
        document.getElementById("text_ar").value =$("#summernote").code();
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
        $(".num").each(function () {
            _this = $(this);
            if (_this.val() < 1) {
                _this
                    .closest("div")
                    .find(".help-block")
                    .html(
                        "<strong>يجب أن تكون قيمة هذا الحقل أكبر تماما من الصفر </strong>"
                    );
                _this.closest("div").addClass("has-error");
                _this.focus();
                status = false;
            }
        });
        $(".date").each(function () {
            _this = $(this);
            var d = new Date(_this.val());
            if (d.getFullYear() < 2023) {
                _this
                    .closest("div")
                    .find(".help-block")
                    .html(
                        "<strong>يجب أن تكون قيمة هذا الحقل أكبر تماما من تاريخ السنة الحالية </strong>"
                    );
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
                            // $('select').val('').change();
                            // $('.dz-image-preview').remove();
                            // $('input[name="files[]"]').remove();
                            // $('#myForm')[0].reset();
                            // $('input[type="text"], textarea').val('');
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
            });
           
        }
    });
});
//validate quantity
