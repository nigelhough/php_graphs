<?php
    include 'graphs.php';

    $MyGraph = new php_graph(1,2);

    $MyGraph->addDataItem("Really Long Name",200,3,200);
    $MyGraph->addDataItem("Ford",300,4,300);

    $MyGraph->addDataItem("Vauxhall",500,5,500);
    $MyGraph->addDataItem("Fiat",0,6,100);

    $MyGraph->addDataItem("Audi",-200,7,-200);
    $MyGraph->addDataItem("BMW",-100,8);

    $MyGraph->addDataItem("Seat",250,9,200);

    $MyGraph->addDataItem("Skoda",90,10,90);

    $MyGraph->addDataItem("Hyundai",100,11,100);
    $MyGraph->addDataItem("Lotus",-300,12);

    $MyGraph->addDataItem("Lexus",600,13,600);

    $MyGraph->addDataItem("VW",700,14,700);
    $MyGraph->addDataItem("Jaguar",450,15,450);

    $MyBar = $MyGraph->drawBarChart("bar","jpg",400,600,"Bar Chart",10,20,false);
    echo "<div>";
    echo "<img src='$MyBar?".date("dmYHis")."' CONTENT='NO-CACHE'>";
    echo "</div>";



