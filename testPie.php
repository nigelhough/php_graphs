<?php
    include 'graphs.php';

    $MyGraph = new php_graph(1,2);

    $MyGraph->addDataItem('Seg 1',347,9);
    $MyGraph->addDataItem('Seg 2',720,8);
    $MyGraph->addDataItem('Seg 3',512,7);

    $MyPie = $MyGraph->drawPie("pie1","jpg",400,400,"Pie Chart");
    echo "<div>";
    echo "<img src='$MyPie?".date("dmYHis")."'>";
    echo "</div><br/>";

    $MyPieExploded = $MyGraph->drawExplodedPie("pie2","jpg",400,400,"Pie Chart Exploded");
    echo "<div>";
    echo "<img src='$MyPieExploded?".date("dmYHis")."'>";
    echo "</div>";



