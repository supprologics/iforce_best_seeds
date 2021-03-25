<div id="header-sec">
    <div class="row">
        <div class="col-sm-12">
            <h1>Customer Details & Payment Reports</h1>
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
                    <a href="custlist" class="reportlink list-group-item list-group-item-action">Customer Listing Report</a>
                    <a href="segments" class="reportlink list-group-item list-group-item-action">Customer Segments</a>
                    <a href="paymentlist" class="reportlink list-group-item list-group-item-action">Customer Payment Due Report</a>
                </div>
            </div>
            <div class="col-sm-8">
                <div id="report_port">



                </div>
            </div>
        </div>
    </div>
</div>

