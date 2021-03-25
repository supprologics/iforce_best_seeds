<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
    <a class="navbar-brand" href="#">iForce</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo Yii::app()->homeUrl; ?>"><span class="oi oi-list-rich"></span> Dashboard</a>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="oi oi-cart"></span> RD Operation
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("customers") ?>">Customers Registry</a>   
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("invoice") ?>">Customer Invoices</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("invoice/credit") ?>">Customer Credit Notes</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("payment") ?>">Customer Payments</a>                    
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="oi oi-infinity"></span> Main Warehouse
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("po") ?>">Purchasing Orders</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("grn") ?>">Goods Receiving Registry </a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("sr") ?>">Supplier Returns Registry </a>  
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("adj") ?>">Stock Adjustments </a>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="oi oi-map"></span> Regional Stock
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("tn") ?>">MTN Registry</a>
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("ltn") ?>">Internal Transfer Registry</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("adj/region") ?>">Stock Adjustments </a>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="fas fa-cog"></span> Settings
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("region") ?>">Regions List</a>
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("customerTypes") ?>">Customer Types</a>
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("device") ?>">Sales Managers Registry</a>
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("brands") ?>">Category</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("items") ?>">Product Registry</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("items/rm") ?>">Raw Material Registry</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("suppliers") ?>">Suppliers Registry</a> 
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("users") ?>">Users Registry</a>  
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="oi oi-file"></span> Reports
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("reports") ?>">Sales Reports</a>
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("reports/inventory") ?>">Inventory Reports</a>
                    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("reports/customer") ?>">Customer & Payment Reports</a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav">
            
            
            <li class="nav-item ">
                <a class="nav-link" href="<?php echo Yii::app()->createUrl("site/logout") ?>">Logout <?php echo Users::model()->findByPk(Yii::app()->user->getId())->username; ?> <span class="sr-only">(current)</span></a>
            </li>
        </ul>
    </div>
</nav>
