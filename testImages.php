<?php
    include 'graphs.php';

    $MyGraph = new php_graph(1,2);

    $i=0;
    while($i < 114)
    {
        $MyCol = $MyGraph->ColorSquare("squares/square$i",".jpg",100,50,$i);

        echo "<p><img src='$MyCol' /></p>";
        $i++;
    }
