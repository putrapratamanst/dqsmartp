<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Report per Grade
                </div>
                <div class="card-body">
                    <form class="form-filter" method="POST">
                        <div class="mb-3 row">
                            <label for="cmbSchool" class="col-12 col-lg-2 col-form-label">School: </label>
                            <div class="col-12 col-lg-6">
                                <select class="form-select select2" id="cmbSchool" required>
                                    <option selected value="">-- choose school --</option>
                                    <?php
                                        foreach ($school as $s) {
                                            echo '<option value="'.$s['SCHOOL'].'">'.$s['SCHOOL'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="txtFromDate" class="col-12 col-lg-2 col-form-label">Range Date: </label>
                            <div class="col-6 col-lg-3">
                                <input type="text" class="form-control datepicker" id="txtFromDate" value="<?php echo $from_date; ?>" />
                            </div>
                            <div class="col-6 col-lg-3">
                                <input type="text" class="form-control datepicker" id="txtToDate" value="<?php echo $to_date; ?>" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="offset-lg-2 col-lg-3">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="button" onclick="exportToPdf()" class="btn btn-outline-primary">Export</button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-12">
                            <div id="divReport"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("form").submit(function (event) {
            var formData = {
                school: $("#cmbSchool").val(),
                from_date: $("#txtFromDate").val(),
                to_date: $("#txtToDate").val(),
            };

            $.ajax({
                type: "POST",
                url: "<?php echo site_url("report/grade_ajax") ?>",
                dataType: "json",
                data: formData,
                encode: true,
                success: function(response){
                    $("#divReport").html(response.report);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("some error");
                }
            });

            event.preventDefault();
        });
    });

    function exportToPdf()
    {
        var url = '<?php echo site_url('report/grade_export_pdf') ?>';
        var param = 'school='+ $("#cmbSchool").val();
        param += '&from_date='+ $("#txtFromDate").val();
        param += '&to_date='+ $("#txtToDate").val();

        url = url + "?" + param;
        var encoded = encodeURI(url);

        window.open(encoded, 'blank');
    }
</script>