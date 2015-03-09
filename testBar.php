<?php
    include 'graphs.php';

    $MyGraph = new php_graph(1,2);

    $MyGraph->addDataItem('Bar 1',347,9);
    $MyGraph->addDataItem('Bar 2',720,8);
    $MyGraph->addDataItem('Bar 3',512,7);

    $MyBar = $MyGraph->drawBarChart("bar","jpg",400,600,"Bar Chart",10,20,false);
    echo "<div>";
    echo "<img src='$MyBar?".date("dmYHis")."' CONTENT='NO-CACHE'>";
    echo "</div>";



