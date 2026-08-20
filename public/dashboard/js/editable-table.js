var EditableTable = (function () {
    return {
        //main function to initiate the module
        init: function () {
            function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $(">td", nRow);

                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }

                oTable.fnDraw();
            }

            function editRow(oTable, nRow, route) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $(">td", nRow);
                jqTds[0].innerHTML =
                    '<input type="file" class="form-control small" id="image" value="' +
                    aData[0] +
                    '"/>';
                jqTds[1].innerHTML =
                    '<input type="text" class="form-control small" id="name" value="' +
                    aData[1] +
                    '"/>';

                jqTds[2].innerHTML = '<a class="edit" href="">Save</a>';
                jqTds[3].innerHTML = '<a class="cancel" href="">Cancel</a>';
            }

            function saveRow(oTable, nRow, route) {
                console.log(route);
                var jqInputs = $("input", nRow);
                oTable.fnUpdate(
                    '<img src="{{ asset(' +
                        '"uploads/brand/"' +
                        jqInputs[0].value +
                        ') }}width="60px"height="60px" />',
                    nRow,
                    0,
                    false
                );
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(
                    '<a class="edit" href="">Edit</a>',
                    nRow,
                    2,
                    false
                );
                oTable.fnUpdate(
                    '<a class="delete" href="">Delete</a>',
                    nRow,
                    3,
                    false
                );
                oTable.fnDraw();
                var uploadFile = document.getElementById("image");
                var fd = new FormData();
                console.log(uploadFile);
                fd.append("image", $("#image")[0].files[0]);
                fd.append("name", $("#name").value);

                $(document).ready(function () {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": jQuery(
                                'meta[name="csrf-token"]'
                            ).attr("content"),
                        },
                    });

                    $.ajax({
                        type: "post",
                        url: route,
                        data: fd,
                        success: function (res) {
                            alert("ddd");
                        },
                    });
                });
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $("input", nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(
                    '<a class="edit" href="">Edit</a>',
                    nRow,
                    2,
                    false
                );
                oTable.fnDraw();
            }

            var oTable = $("#editable-sample").dataTable({
                aLengthMenu: [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"], // change per page values here
                ],
                // set the initial value
                iDisplayLength: 5,
                sDom: "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-6'i><'col-lg-6'p>>",
                sPaginationType: "bootstrap",
                oLanguage: {
                    sLengthMenu: "_MENU_ records per page",
                    oPaginate: {
                        sPrevious: "Prev",
                        sNext: "Next",
                    },
                },
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: [0],
                    },
                ],
            });

            jQuery(
                "#editable-sample_wrapper .dataTables_filter input"
            ).addClass("form-control medium"); // modify table search input
            jQuery(
                "#editable-sample_wrapper .dataTables_length select"
            ).addClass("form-control xsmall"); // modify table per page dropdown

            var nEditing = null;

            $("#editable-sample_new").click(function (e) {
                e.preventDefault();
                var aiNew = oTable.fnAddData([
                    '<input type="file" class="form-control small" name="image" id="image"/>',
                    "",
                    '<a class="edit" href="">Edit</a>',
                    '<a class="cancel" data-mode="new" href="">Cancel</a>',
                ]);
                var nRow = oTable.fnGetNodes(aiNew[0]);
                editRow(oTable, nRow);
                nEditing = nRow;
            });
            //حذف للصنف
            $("#editable-sample .delete").live("click", function (e) {
                e.preventDefault();
                // if (confirm("Are you sure to delete this row ?") == false) {
                //     return;
                // }
                // var id = $(this).data("id");
                var route = $(this).data("route");
                var nRow = $(this).parents("tr")[0];
                $(".confirm").live("click", function (e) {
                    $(".clos")[0].click();
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": jQuery(
                                'meta[name="csrf-token"]'
                            ).attr("content"),
                        },
                    });

                    $.ajax({
                        url: route,
                        method: "DELETE",
                        success: function (res) {
                            if (res.status == "ok") {
                                oTable.fnDeleteRow(nRow);
                                new PNotify({
                                    title: "تم",
                                    text: res.message,
                                    type: "success",
                                    hideAfter: 4000,
                                    styling: "bootstrap3",
                                });
                            } else {
                                new PNotify({
                                    title: "خطأ !",
                                    text: res.message,
                                    type: "error",
                                    hideAfter: 4000,
                                    styling: "bootstrap3",
                                });
                            }
                        },
                    });
                });
            });

            $("#editable-sample a.cancel").live("click", function (e) {
                e.preventDefault();
                if ($(this).attr("data-mode") == "new") {
                    var nRow = $(this).parents("tr")[0];
                    oTable.fnDeleteRow(nRow);
                } else {
                    restoreRow(oTable, nEditing);
                    nEditing = null;
                }
            });

            $("#editable-sample a.edit").live("click", function (e) {
                e.preventDefault();
                /* Get the row as a parent of the link that was clicked on */
                var nRow = $(this).parents("tr")[0];
                var routeSave = $("#store").data("route");
                if (nEditing !== null && nEditing != nRow) {
                    /* Currently editing - but not this row - restore the old before continuing to edit mode */
                    restoreRow(oTable, nEditing);
                    editRow(oTable, nRow);
                    nEditing = nRow;
                } else if (nEditing == nRow && this.innerHTML == "Save") {
                    /* Editing this row and want to save it */
                    saveRow(oTable, nEditing, routeSave);

                    nEditing = null;
                    // alert(
                    //     "Updated! Do not forget to do some ajax to sync with backend :)"
                    // );
                } else {
                    /* No edit in progress - let's start one */
                    editRow(oTable, nRow);
                    nEditing = nRow;
                }
            });
        },
    };
})();
