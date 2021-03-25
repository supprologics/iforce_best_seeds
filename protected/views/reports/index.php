<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Sales Operation Reports</h1>
        </div>
    </div>
</div>


<script>
    
    $(function () {
        if (localStorage.report != null) {
            loadui(localStorage.report, localStorage.reportTitle);
        }
    });
    
    $(document).on("click", ".reportlink", function (e) {
        e.preventDefault();
        var report = $(this).attr("href");
        var reportTitle = $(this).html();
        
        localStorage.report = report;
        localStorage.reportTitle = reportTitle;
        
        loadui(report, reportTitle);
    });

    function loadui(page, title) {
        $.ajax({
            url: "<?php echo Yii::app()->createUrl("reports/loadui"); ?>",
            data: {
                report: page,
                title: title
            },
            type: "post",
            error: showResponse
        }).done(function (data) {
            $("#report_port").html(data);
        });

    }
</script>

<div class="report_body">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-4">

                <div class="list-group list-group-flush">
                    <a href="invoiceListingReport" class="reportlink list-group-item list-group-item-action">S01 - Invoice Listing Report</a>
                    <a href="productMovementReport" class="reportlink list-group-item list-group-item-action">S02 - Products Movement Report</a>
                    <a href="overallSalesReport" class="reportlink list-group-item list-group-item-action">S03 - Sales Summery Report</a>
                    <a href="callsbyrep" class="reportlink list-group-item list-group-item-action">S04 - Daily Calls Analyze Report</a>
                    <a href="attendanceReport" class="reportlink list-group-item list-group-item-action">S05 - Sales Team Attendance Report</a>
                    <a href="attendanceReport_detail" class="reportlink list-group-item list-group-item-action">S06 - Detail Attendance Report</a>
                </div>
            </div>
            <div class="col-sm-8">
                <div id="report_port">



                </div>
            </div>
        </div>
    </div>
</div>

