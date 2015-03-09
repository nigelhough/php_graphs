<?php

class php_graph
{
    private $data = array();
    
    private $dataTypeX = 1;
    private $dataTypeY = 1;

    public $dataLabelX = "";
    public $dataLabelY = "";

    public $RelativeHour = 0;
    public $RelativeMinute = 0;
    public $RelativeDate = 0;
    public $RelativeWeek = 0;
    public $RelativeYear = 0;

    private $order=1;
    
    /*
    1 - Numeric
    2 - Time (Minutes Since Midnight)
    3 - Seconds (Seconds Since Midnight)
    4 - Relative Seconds
    5 - Date (days Since 1900)
    6 - Relative Date
    7 - Relative Week
    */

    private $colours = array();

    function __construct($pDataTypeX=null,$pDataTypeY=null,$RelativeHour=null,$RelativeMinute=null,$RelativeDate=null)
    {
        if(isset($pDataTypeX))                                                                                                                        
        {
            if(is_numeric($pDataTypeX) && $pDataTypeX > 0)
            {
                $this->dataTypeX = $pDataTypeX;
            }
            else
            {
                throw new Exception("Graph Error : DataType Must Be An Integer Greather Than 0");
            }
        }
        
        if(isset($pDataTypeY))
        {
            if(is_numeric($pDataTypeY) && $pDataTypeY > 0)
            {
                $this->dataTypeY = $pDataTypeY;
            }
            else
            {
                throw new Exception("Graph Error : DataType Must Be An Integer Greather Than 0");
            }
        }

        if(isset($RelativeHour))
        {
            if(is_numeric($RelativeHour))
            {
                $this->RelativeHour = $RelativeHour;
            }
            else
            {
                throw new Exception("Graph Error : Relative Hour Must Be A Number");
            }
        }
        
        if(isset($RelativeMinute))
        {
            if(is_numeric($RelativeMinute))
            {
                $this->RelativeMinute = $RelativeMinute;
            }
            else
            {
                throw new Exception("Graph Error : Relative Hour Must Be A Number");
            }
        }
        
        if(isset($RelativeDate))
        {
            if(is_numeric($RelativeDate))
            {
                $this->RelativeDate = $RelativeDate;
            }
            else
            {
                throw new Exception("Graph Error : Relative Date Must Be A Number");
            }
        }
    }
    
    function addDataItem($Name,$Value,$Colour,$Value2=null)
    {
        $currentValue = array('name'=>$Name, 'value'=>$Value, 'value2'=>$Value2, 'colour'=>$Colour);

        $this->data[] = $currentValue;
    }
    
    function DataSort($pOrder=1)
    {
        /* Order
        1 - Ascending
        2 - Deceding
        */

        if(isset($pOrder) && is_numeric($pOrder) && $pOrder > 0 && $pOrder < 3)
        {
            $this->order = $pOrder;
        }
        else
        {
            throw new Exception("Graph Error : Order Must Be An Integer 1 or 0. 1 Ascending Order or 2 Descending Order");
        }
    }
    
    function GetNoDataItems()
    {
        return count($this->data);
    }
    
    function drawExplodedPie($pfilename,$pfile_extension,$pheight,$pwidth,$ptitle)
    {
        if(isset($pfilename))
        {
            $filename = $pfilename;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }
        
        if(isset($pfile_extension))
        {
            $file_extension = $pfile_extension;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }

        if(isset($pheight) && is_numeric($pheight) && $pheight > 0)
        {
            $height = $pheight;
        }
        else
        {
            throw new Exception("Graph Error : Height Must Be A Number Greather Than 0");  
        }

        if(isset($pwidth) && is_numeric($pwidth) && $pwidth > 0)
        {
            $width = $pwidth;
        }
        else
        {
            throw new Exception("Graph Error : Width Must Be A Number Greather Than 0");
        }
        $title = $ptitle;

        if($height > $width)
        {
            $PieHeight = $width;
            $PieWidth = $width;
        }
        else
        {
            $PieHeight = $height;
            $PieWidth = $height;
        }
    
        $image = imagecreatetruecolor($width, $width);

        //----------------Set Colours
        $this->setColours($image);
        //-----------------------------------------------

        $font = $this->getFont();

        imagefill($image, 0, 0, $this->colours[0]);

        imagerectangle($image,0,0,$width-1,$height-2,$this->colours[1]);
    
        $header = 0;
    
        if($title != "")
        {
            imagefilledrectangle($image,0,0,$width-1,20,$this->colours[112]);
            imagerectangle($image,0,0,$width-1,20,$this->colours[1]);
            
            //Draw Text
            $fontSize = 12;

            $textDimensions = imagettfbbox($fontSize,0,$font,$title);
            $textWidth = $textDimensions[4];
            
            while($textWidth > $width)
            {
                $fontSize--;
                $textDimensions = imagettfbbox($fontSize,0,$font,$title);
                $textWidth = $textDimensions[4];
            }

            imagettftext($image  , $fontSize, 0, (($width)/2)-($textWidth/2), 16, $this->colours[1], $font, $title);

            $header = 10;
        }
    
        imagefilledarc($image, ($width/2), (($height/2)+$header), $PieWidth-60, ($PieHeight-60),  0, 360, $this->colours[0], IMG_ARC_PIE);
    
        $i=0;
        $total=0;
    
        while($i < count($this->data))
        {
            if(isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0)
            {
                $total = $total + $this->data[$i]['value'];
            }
            else
            {
                echo "<p>Error2: No Segment Data Provided (3)</p>";
                return false;
            }
            if(!isset($this->data[$i]['colour']))
            {
                echo "<p>Error3: Color ".$this->data[$i]['colour']." dosen't exsist 2</p>";
                return false;
            }
            $i++;
        }
    
        $i=0;
        $start=-90;
    
        while($i < count($this->data))
        {
            if($this->data[$i]['value'])
            {
                $c = (($start+($start+((($this->data[$i]['value']/$total)*100)*3.6)))/2);
                $r = 10;

                $X0 = ($height/2);
                $Y0 = (($height/2)+$header);
    
                $X1 = $X0 + ($r*cos(((pi()*$c)/180)));
                $Y1 = $Y0 + ($r*sin(((pi()*$c)/180)));
    
                //Segments with a width of less than 1 degree will draw a full circle
                if(((($this->data[$i]['value']/$total)*100)*3.6) >= 1)
                {
                    imagefilledarc($image, $X1, $Y1, $PieWidth-60, ($PieHeight-60),  $start, $start+((($this->data[$i]['value']/$total)*100)*3.6), $this->colours[$this->data[$i]['colour']], IMG_ARC_PIE);
                }
                else
                {
                    imagefilledarc($image, $X1, $Y1, $PieWidth-60, ($PieHeight-60),  $start, $start+1, $this->colours[$this->data[$i]['colour']], IMG_ARC_PIE);
                }

                $start = $start + ((($this->data[$i]['value']/$total)*100)*3.6);
                $i++;
            }
            else
            {
                echo "<p>Error2: No Segment Data Provided (4)</p>";
                return false;
            }
        }
        if($start > 269 && $start < 271) //Checks the pie is a full 100% allowing for decimal places either way
        {
            //JPG So it will work with the PDF
            imagejpeg($image,$filename.".".$file_extension);
            return $filename.".".$file_extension;
        }
        else
        {
            echo "<p>Error1: Segments Do Not Equal 100%</p>";
            return false;
        }
    }

    //Pie Chart
    function drawPie($pfilename,$pfile_extension,$pheight,$pwidth,$ptitle)
    {
        if(isset($pfilename))
        {
            $filename = $pfilename;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }
        
        if(isset($pfile_extension))
        {
            $file_extension = $pfile_extension;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }

        if(isset($pheight) && is_numeric($pheight) && $pheight > 0)
        {
            $height = $pheight;
        }
        else
        {
            throw new Exception("Graph Error : Height Must Be A Number Greather Than 0");  
        }

        if(isset($pwidth) && is_numeric($pwidth) && $pwidth > 0)
        {
            $width = $pwidth;
        }
        else
        {
            throw new Exception("Graph Error : Width Must Be A Number Greather Than 0");
        }
        $title = $ptitle;
    
        if($height > $width)
        {
            $PieHeight = $width;
            $PieWidth = $width;
        }
        else
        {
            $PieHeight = $height;
            $PieWidth = $height;
        }

        $image = imagecreatetruecolor($width, $height);

        //----------------Set Colours
        $this->setColours($image);
        //-----------------------------------------------

        $font = $this->getFont();

        imagefill($image, 0, 0, $this->colours[0]);

        imagerectangle($image,0,0,$width-1,$height-2,$this->colours[1]);

        $header = 0;

        if($title != "")
        {
            imagefilledrectangle($image,1,1,$width-1,20,$this->colours[112]);
            imagerectangle($image,0,0,$width-1,20,$this->colours[1]);
            
            //Draw Text
            $fontSize = 12;

            $textDimensions = imagettfbbox($fontSize,0,$font,$title);
            $textWidth = $textDimensions[4];
            
            while($textWidth > $width)
            {
                $fontSize--;
                $textDimensions = imagettfbbox($fontSize,0,$font,$title);
                $textWidth = $textDimensions[4];
            }

            imagettftext($image  , $fontSize, 0, (($width)/2)-($textWidth/2), 16, $this->colours[1], $font, $title);

            $header = 10;
        }
    
        //imagefilledarc($image, ($width/2), (($height/2)+$header), $PieWidth-60, ($PieHeight-60),  0, 360, $this->colours[0], IMG_ARC_PIE);

        $i=0;
        $total=0;
        while($i < count($this->data))
        {
            if(isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0)
            {
                $total = $total + $this->data[$i]['value'];
            }
            else
            {
                echo "<p>Error2: No Segment Data Provided (1)</p>";
                return false;
            }

            if(!isset($this->data[$i]['colour']))
            {
                echo "<p>Error3: Color $i ".$this->data[$i]['colour']." dosen't exsist 1</p>";
                return false;
            }
            $i++;
        }
    
        $i=0;
        $start=-90;

        while($i < count($this->data))
        {
            if(isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0)
            {
                //Segments with a width of less than 1 degree will draw a full circle
                if(((($this->data[$i]['value']/$total)*100)*3.6) >= 1)
                {
                    imagefilledarc($image, ($width/2), (($height/2)+$header), $PieWidth-60, ($PieHeight-60),  round($start), round($start+((($this->data[$i]['value']/$total)*100)*3.6)), $this->colours[$this->data[$i]['colour']], IMG_ARC_PIE);

                    $radius = (($PieHeight-60)/2);
                    $x = ($width/2)+($radius*cos(deg2rad(round($start))));
                    $y = ((($height/2)+$header)+($radius*sin(deg2rad(round($start)))));

                    //Draw Lines Around Sections
                    if(count($this->data) > 1)
                    {
                        imageline($image, ($width/2), (($height/2)+$header), $x, $y, $this->colours[1]);
                    }
                }
                else
                {
                    imagefilledarc($image, ($width/2), (($height/2)+$header), $PieHeight-60, ($PieHeight-60),  $start, $start+1, $this->colours[$this->data[$i]['colour']], IMG_ARC_PIE);
                }

                $start = $start + ((($this->data[$i]['value']/$total)*100)*3.6);
                $i++;
            }
            else
            {
                echo "<p>Error2: No Segment Data Provided (2)</p>";
                return false;
            }
        }
        
        $radius = (($PieHeight-60)/2);
        $x = ($width/2)+($radius*cos(deg2rad(round(-90))));
        $y = ((($height/2)+$header)+($radius*sin(deg2rad(round(-90)))));


        if(count($this->data) > 1)
        {
            //Draw Lines Around Sections
            imageline($image, ($width/2), (($height/2)+$header), $x, $y, $this->colours[1]);
        }

        if($start > 269 && $start < 271) //Checks the pie is a full 100% allowing for a little bit either way
        {
            imageellipse($image, ($width/2), (($height/2)+$header), $PieWidth-60, ($PieHeight-60), $this->colours[1]);
            //JPG So it will work with the PDF
            imagejpeg($image,$filename.".".$file_extension);
            return $filename.".".$file_extension;
        }
        else
        {
            echo "<p>Error1: Segments Do Not Equal 100% $start</p>";
            return false;
        }
    }

    //Pie Chart
    function drawBarChart($pfilename,$pfile_extension,$pheight,$pwidth,$ptitle,$NumberOfYLabels=4)
    {
        if(isset($pfilename))
        {
            $filename = $pfilename;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }
        
        if(isset($pfile_extension))
        {
            $file_extension = $pfile_extension;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }

        if(isset($pheight) && is_numeric($pheight) && $pheight > 0)
        {
            $height = $pheight;
        }
        else
        {
            throw new Exception("Graph Error : Height Must Be A Number Greather Than 0");  
        }

        if(isset($pwidth) && is_numeric($pwidth) && $pwidth > 0)
        {
            $width = $pwidth;
        }
        else
        {
            throw new Exception("Graph Error : Width Must Be A Number Greather Than 0");
        }
        $title = $ptitle;

        $ChartPaddingHeight = ($height*0.05);
        //$ChartPaddingWidth = ($width*0.1);
        $ChartPaddingLeft = ($width*0.1);
        $ChartPaddingRight = ($width*0.01);

        $ChartHeight = ($height-($ChartPaddingHeight*2));
        $ChartWidth = ($width-($ChartPaddingLeft+$ChartPaddingRight));

        $image = imagecreatetruecolor($width, $height);

        //----------------Set Colours
        $this->setColours($image);
        //-----------------------------------------------

        $font = $this->getFont();

        imagefill($image, 0, 0, $this->colours[0]);

        imagerectangle($image,0,0,$width-1,$height-2,$this->colours[1]);

        $header = 0;
        $fontSize = 12;

        //Draws The Header If There Is One
        if($title != "")
        {
            imagefilledrectangle($image,1,1,$width-1,20,$this->colours[112]);
            imagerectangle($image,0,0,$width-1,20,$this->colours[1]);

            $textDimensions = imagettfbbox($fontSize,0,$font,$title);
            $textWidth = $textDimensions[4];
            
            while($textWidth > $width)
            {
                $fontSize--;
                $textDimensions = imagettfbbox($fontSize,0,$font,$title);
                $textWidth = $textDimensions[4];
            }

            imagettftext($image  , $fontSize, 0, (($width)/2)-($textWidth/2), 16, $this->colours[1], $font, $title);

            $header = 10;
        }

        //Loop Through Data To Find The Higest And Lowest Values
        //--------------------------------------------
        $i=0;
        $highest=0;
        $lowest=99999999999;

        while($i < count($this->data))
        {
            if(isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0)
            {
                if($this->data[$i]['value'] > $highest)
                {
                    $highest = $this->data[$i]['value'];
                }

                if($this->data[$i]['value'] < $lowest)
                {
                    $lowest = $this->data[$i]['value'];
                }
            }
            else
            {
                //Throw Error
                //echo "<p>Error2: No Segment Data Provided (1)</p>";
                return false;
            }

            if(!isset($this->data[$i]['colour']))
            {
                //Throw Error
                //echo "<p>Error3: Color $i ".$this->data[$i]['colour']." dosen't exsist 1</p>";
                return false;
            }
            $i++;
        }
        //--------------------------------------------

        //Calculate Y Axis Labels
        //Calculates The Best Units For The Y Axis and the Best Height
        //--------------------------------------------
        //$NumberOfYLabels = 4;
        if($lowest < 0)
        {
            $Yheight = $highest-$lowest;
        }
        else
        {
            $Yheight = $highest;
        }
        $Difference = ($Yheight/$NumberOfYLabels);

        $div = 1;
        while($Difference  > 1)
        {
            $Difference = ($Difference/10);
            $div = ($div*10);
        }

        $TestValues = array();

        if($this->dataTypeX == 2 || $this->dataTypeX == 3)
        {
            $TestValues[] = 0.9;
            $TestValues[] = 0.6;
            $TestValues[] = 0.45;
            $TestValues[] = 0.3;
            $TestValues[] = 0.15;
            $TestValues[] = 0.1;
        }
        elseif($this->dataTypeX == 4)
        {
            $TestValues[] = 0.9;
            $TestValues[] = 0.6;
            $TestValues[] = 0.45;
            $TestValues[] = 0.3;
            $TestValues[] = 0.15;
            $TestValues[] = 0.1;
        }
        else
        {
            $TestValues[] = 1;
            $TestValues[] = 0.5;
            $TestValues[] = 0.25;
            $TestValues[] = 0.2;
            $TestValues[] = 0.1;
        }

        $BestIdx=0;
        $MinDiff = 999999;
        for ($i = 1; $i <= count($TestValues)-1; $i++)
        {
            $Diff = $Difference - $TestValues[$i];

            //The $Diff < 0 prevents the Units*Labels from being less than the highest.
            if(($Diff < 0) && (abs($Diff) < abs($MinDiff)))
            {
                $MinDiff = $Diff;

                $BestIdx = $i;
            }
        }
        
        //echo "<p>".$BestIdx.", ".$TestValues[$BestIdx].", ".$Difference.", ".($Difference - $TestValues[$BestIdx])."</p>";

        $YAxisUnits = ($TestValues[$BestIdx]*($div));

        //Minimum Units is 0
        //If not the Number_Format On The Labels Will Round to a Whole Number
        if($YAxisUnits < 1)
        {
            $YAxisUnits=1;
        }

        //$highest = $YAxisUnits*$NumberOfYLabels;
        $i=1;
        while(($highest+1) > ($YAxisUnits*$i))
        {
            $i++;
        }
        $highest = ($YAxisUnits*$i);
        $i=1;

        if($lowest < 0)
        {
            while(($lowest-1) < ((0-$YAxisUnits)*$i))
            {
                $i++;
            }
            $lowest = ((0-$YAxisUnits)*$i);
        }
        else
        {
            $lowest = 0;
        }

        //--------------------------------------------

        //Calculate Height Scale
        //--------------------------------------------

        $HeightScale = ($ChartHeight/($highest-$lowest));

        //$HeightScale = ($ChartHeight/$highest);

        //--------------------------------------------

        //Draw Bottom Left Label
        //--------------------------------------------
        $x = (($ChartPaddingLeft*0.85));
        $y = ($ChartHeight+($ChartPaddingHeight+$header));
        $fontHeight = $fontSize; //($ChartHeight*0.05);

        if($this->dataTypeX != 2)
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($highest));
        }
        else
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($highest));
        }
        $textWidth = $textDimensions[4];
        $textHeight = $textDimensions[5];

        while(($x+(($x-($ChartPaddingLeft))-$textWidth)) < 20)
        {
            $fontHeight--;

            if($this->dataTypeX != 2)
            {
                $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($highest));
            }
            else
            {
                $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($highest));
            }
            $textWidth = $textDimensions[4];
            $textHeight = $textDimensions[5];
        }
        
        if($this->dataTypeX != 2)
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($lowest));
        }
        else
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($lowest));
        }
        $textWidth = $textDimensions[4];

        if($this->dataTypeX != 2)
        {
            imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, number_format($lowest));
        }
        else
        {
            imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, $this->minutesToTime($lowest));
        }

        imageline  ($image, $x, $y, $ChartPaddingLeft, $y, $this->colours[1]);

        imagefilledrectangle  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y-($YAxisUnits*$HeightScale), $this->colours[112]);

        $y = ($ChartPaddingHeight+$header);
        if($this->dataTypeX != 2)
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($highest));
        }
        else
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($highest));
        }
        $textWidth = $textDimensions[4];
        $textHeight = $textDimensions[5];
        while(($x+(($x-$ChartPaddingLeft)-$textWidth)) < 5)
        {
            $fontHeight--;

            if($this->dataTypeX != 2)
            {
                $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($highest));
            }
            else
            {
                $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($highest));
            }
            $textWidth = $textDimensions[4];
            $textHeight = $textDimensions[5];
        }

        //Draw Top Left Label
        if($this->dataTypeX != 2)
        {
            imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, number_format($highest));
        }
        else
        {
            imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, $this->minutesToTime($highest));
        }
        //Draw Line Pointing To Label
        imageline  ($image, $x, $y, $ChartPaddingLeft, $y, $this->colours[1]);
        //Draw Backing Gridline
        imageline  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y, $this->colours[2]);

        //Draw Middle Labels
        $ZeroPosition = 0;

        $i=1;
        $current = $lowest+$YAxisUnits;
        $LowestPosition = 0;
        while($current != $highest)
        {
            $y = ($ChartHeight-(($YAxisUnits*$HeightScale)*$i)+($ChartPaddingHeight+$header));
            if($this->dataTypeX != 2)
            {
                $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($current));
            }
            else
            {
                $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($current));
            }
            $textWidth = $textDimensions[4];
            $textHeight = $textDimensions[5];
            
            while(($x+(($x-$ChartPaddingLeft)-$textWidth)) < 5)
            {
                $fontHeight--;

                if($this->dataTypeX != 2)
                {
                    $textDimensions = imagettfbbox($fontHeight,0,$font,number_format($current));
                }
                else
                {
                    $textDimensions = imagettfbbox($fontHeight,0,$font,$this->minutesToTime($current));
                }
                $textWidth = $textDimensions[4];
                $textHeight = $textDimensions[5];
            }

            if($this->dataTypeX != 2)
            {
                imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, number_format($current));
            }
            else
            {
                imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, $this->minutesToTime($current));
            }
            //Draw Line Pointing To Label
            imageline  ($image, $x, $y, $ChartPaddingLeft, $y, $this->colours[1]);
            //Draw Backing Gridline

            //Draw Back Shading Area
            if($i % 2 == 0)
            {
                //Even Number
                imagefilledrectangle  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y-($YAxisUnits*$HeightScale), $this->colours[112]);
            }
            else
            {
                //Odd Number
                imagefilledrectangle  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y-($YAxisUnits*$HeightScale), $this->colours[113]);
            }
            
            if($current == 0)
            {
                $ZeroPosition = $y-($YAxisUnits*$HeightScale);
            }
            if($i == 1)
            {
                $LowestPosition = $y;
            }
            
            //Draw Backing Gridline
            imageline  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y, $this->colours[2]);
            $current = $current +$YAxisUnits;
            $i++;
        }

        //--------------------------------------------

        //Draw The Bars
        //--------------------------------------------
        //Calculates The Width Of Each Bar Based on The Width Of The Chart and The Number Of Items
        //$BarWidth = ($ChartWidth/((count($this->data)*2)+1));

        $WidthRatio = 6;
        $BlankSpace = ($ChartWidth/((count($this->data)*$WidthRatio)+1));
        $BarWidth = ($BlankSpace*($WidthRatio-1));

        $i=0;

        //Loop Through All The Data
        while($i < count($this->data))
        {
            //If There is Data
            if(isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0)
            {
                $x1 = ($ChartPaddingLeft+($BarWidth*$i))+(($BlankSpace*($i+1)));

                if($ZeroPosition > 0)
                {
                    $y1 = ($ChartHeight+($ChartPaddingHeight+$header))-($LowestPosition-$ZeroPosition);
                }
                else
                {
                    $y1 = ($ChartHeight+($ChartPaddingHeight+$header));
                }

                $x2 = ((($BarWidth*($i)+(($BlankSpace*($i+1))))+$ChartPaddingLeft)+$BarWidth);

                $BarHeight = ($this->data[$i]['value']*$HeightScale);

                if($ZeroPosition > 0)
                {
                    $y2 = (($ChartHeight+($ChartPaddingHeight+$header))-$BarHeight)-($LowestPosition-$ZeroPosition);
                }
                else
                {
                    $y2 = (($ChartHeight+($ChartPaddingHeight+$header))-$BarHeight);
                }
                //$y2 = 0;

                //Working
                //$x1 = ($ChartPaddingLeft+($BarWidth*($i)))+(($BarWidth*($i+1)));
                //$y1 = ($ChartHeight+($ChartPaddingHeight+$header));
                //$x2 = ((($BarWidth*($i)+(($BarWidth*($i+1))))+$ChartPaddingLeft)+$BarWidth);
                //$BarHeight = ($this->data[$i]['value']*$HeightScale);
                //$y2 = (($ChartHeight+($ChartPaddingHeight+$header))-$BarHeight);


                imagefilledrectangle($image, $x1, $y1,  $x2, $y2, $this->colours[$this->data[$i]['colour']]);
                imagerectangle($image, $x1, $y1,  $x2, $y2, $this->colours[1]);

                $i++;
            }
            else
            {
                echo "<p>Error2: No Segment Data Provided (2)</p>";
                return false;
            }
        }
        //--------------------------------------------


        //Redraw the Graph Lines
        //x line
        imageline  ($image, $ChartPaddingLeft, ($ChartPaddingHeight+$header), $ChartPaddingLeft, ($ChartHeight+($ChartPaddingHeight+$header)), $this->colours[1]);
        //y line
        imageline  ($image, $ChartPaddingLeft, ($ChartHeight+($ChartPaddingHeight+$header)), $ChartWidth+($ChartPaddingLeft), ($ChartHeight+($ChartPaddingHeight+$header)), $this->colours[1]);

        //x TOP line
        imageline  ($image, $ChartWidth+($ChartPaddingLeft), ($ChartPaddingHeight+$header), $ChartWidth+($ChartPaddingLeft), ($ChartHeight+($ChartPaddingHeight+$header)), $this->colours[1]);
        //y RIGHT line
        imageline  ($image, $ChartPaddingLeft, ($ChartPaddingHeight+$header), $ChartWidth+($ChartPaddingLeft), ($ChartPaddingHeight+$header), $this->colours[1]);


        //Draw 0 Line
        if($ZeroPosition > 0)
        {
            imageline  ($image, $ChartPaddingLeft, ($ZeroPosition+($YAxisUnits*$HeightScale)), $ChartWidth+($ChartPaddingLeft), ($ZeroPosition+($YAxisUnits*$HeightScale)), $this->colours[1]);
        }

        //JPG So it will work with the PDF
        imagejpeg($image,$filename.".".$file_extension);
        return $filename.".".$file_extension;
    }

    //Line Graph
    function drawLineGraph($pfilename,$pfile_extension,$pheight,$pwidth,$ptitle,$NumberOfYLabels=4,$NumberOfXLabels=4, $DrawLine=true, $border=true, $LineThickness=1, $DrawPoints=false, $XAxisStartAtZero=true, $YAxisStartAtZero=true)
    {
        if(isset($pfilename))
        {
            $filename = $pfilename;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }
        
        if(isset($pfile_extension))
        {
            $file_extension = $pfile_extension;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }

        if(isset($pheight) && is_numeric($pheight) && $pheight > 0)
        {
            $height = $pheight;
        }
        else
        {
            throw new Exception("Graph Error : Height Must Be A Number Greather Than 0");  
        }

        if(isset($pwidth) && is_numeric($pwidth) && $pwidth > 0)
        {
            $width = $pwidth;
        }
        else
        {
            throw new Exception("Graph Error : Width Must Be A Number Greather Than 0");
        }

        $title = $ptitle;
    
        $ChartPaddingHeight = ($height*0.05);
        //$ChartPaddingWidth = ($width*0.1);
        $ChartPaddingLeft = ($width*0.1);
        $ChartPaddingBottom = ($width*0.1);
        $ChartPaddingRight = ($width*0.01);

        $ChartHeight = ($height-($ChartPaddingBottom+$ChartPaddingHeight*2));
        $ChartWidth = ($width-($ChartPaddingLeft+$ChartPaddingRight));

        $image = imagecreatetruecolor($width, $height);
        
        if($LineThickness == 1)
        {
            imageantialias($image, true);
        }

        $this->setColours($image);

        $font = $this->getFont();

        imagefill($image, 0, 0, $this->colours[0]);

        //Border
        if($border)
        {
            imagerectangle($image,0,0,$width-1,$height-2,$this->colours[1]);
        }

        $header = 0;

        //Draw Text
        $fontSize = 10;

        //Draws The Header If There Is One
        if($title != "")
        {
            imagefilledrectangle($image,1,1,$width-1,20,$this->colours[112]);
            imagerectangle($image,0,0,$width-1,20,$this->colours[1]);

            $textDimensions = imagettfbbox($fontSize,0,$font,$title);
            $textWidth = $textDimensions[4];
            
            while($textWidth > $width)
            {
                $fontSize--;
                $textDimensions = imagettfbbox($fontSize,0,$font,$title);
                $textWidth = $textDimensions[4];
            }

            imagettftext($image  , $fontSize, 0, (($width)/2)-($textWidth/2), 16, $this->colours[1], $font, $title);

            $header = 10;
        }

        if(!$this->GetHighAndLowValues("Y",$highestY,$lowestY))
        {
             throw new Exception("Graph Error : Error Get Y Axis Highs and Lows");
        }

        if(!$this->GetHighAndLowValues("X",$highestX,$lowestX))
        {
             throw new Exception("Graph Error : Error Get X Axis Highs and Lows");
        }

        //echo $this->dataTypeY;
        $this->GetAxisUnits($this->dataTypeY,$NumberOfYLabels,$highestY,$lowestY,$YAxisUnits,$YAxisStartAtZero,1);

        $this->GetAxisUnits($this->dataTypeX,$NumberOfXLabels,$highestX,$lowestX,$XAxisUnits,$XAxisStartAtZero,0);

        //Calculate Height Scale
        //--------------------------------------------
        $HeightScale = ($ChartHeight/($highestY-$lowestY));
        //--------------------------------------------

        //Calculate Width Scale
        //--------------------------------------------
        $WidthScale = ($ChartWidth/($highestX-$lowestX));
        //--------------------------------------------

        //Draw Bottom Left Label
        //--------------------------------------------
        $x = (($ChartPaddingLeft*0.85));
        $y = ($ChartHeight+(($ChartPaddingHeight)+$header));
        $fontHeight = $fontSize; //($ChartHeight*0.05);
                                                                                            

        $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeY,$highestY,$this->dataLabelY));

        $textWidth = $textDimensions[4];
        $textHeight = $textDimensions[5];

        while(($x+(($x-($ChartPaddingLeft))-$textWidth)) < 20)
        {
            $fontHeight--;

            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeY,$highestY,$this->dataLabelY));

            $textWidth = $textDimensions[4];
            $textHeight = $textDimensions[5];
        }

        $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeY,$lowestY,$this->dataLabelY));

        $textWidth = $textDimensions[4];

        imagettftext($image, $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, $this->getLabel($this->dataTypeY,$lowestY,$this->dataLabelY));

        imageline  ($image, $x, $y, $ChartPaddingLeft, $y, $this->colours[1]);

        imagefilledrectangle  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y-($YAxisUnits*$HeightScale), $this->colours[112]);

        $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeY,$highestY,$this->dataLabelY));

        $y = ($ChartPaddingHeight+$header);

        $textWidth = $textDimensions[4];
        $textHeight = $textDimensions[5];
        while(($x+(($x-$ChartPaddingLeft)-$textWidth)) < 5)
        {
            $fontHeight--;

            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeY,$highestY,$this->dataLabelY));

            $textWidth = $textDimensions[4];
            $textHeight = $textDimensions[5];
        }

        //Draw Top Left Label
        imagettftext($image, $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, $this->getLabel($this->dataTypeY,$highestY,$this->dataLabelY));

        //Draw Line Pointing To Label
        imageline  ($image, $x, $y, $ChartPaddingLeft, $y, $this->colours[1]);
        //Draw Backing Gridline
        imageline  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y, $this->colours[2]);

        //Draw Middle Labels
        $ZeroPositionY = 0;

        $i=1;
        $current = $lowestY+$YAxisUnits;
        while($current < $highestY)
        {
            if($i > 200)
            {
                echo "<p>Looping Error 1 - $current - $i</p>";
                exit();
            }

            $y = ($ChartHeight-(($YAxisUnits*$HeightScale)*$i)+($ChartPaddingHeight+$header));

            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeY,$current,$this->dataLabelY));

            $textWidth = $textDimensions[4];
            $textHeight = $textDimensions[5];
            
            while(($x+(($x-$ChartPaddingLeft)-$textWidth)) < 5)
            {
                $fontHeight--;

                $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$current,$this->dataLabelX));

                $textWidth = $textDimensions[4];
                $textHeight = $textDimensions[5];
            }


            imagettftext($image  , $fontHeight, 0, $x+(($x-$ChartPaddingLeft)-$textWidth), $y-($textHeight*0.5), $this->colours[1], $font, $this->getLabel($this->dataTypeY,$current,$this->dataLabelY));

            //Draw Line Pointing To Label
            imageline  ($image, $x, $y, $ChartPaddingLeft, $y, $this->colours[1]);
            //Draw Backing Gridline

            //Draw Back Shading Area
            if($i % 2 == 0)
            {
                //Even Number
                imagefilledrectangle  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y-($YAxisUnits*$HeightScale), $this->colours[112]);
            }
            else
            {
                //Odd Number
                imagefilledrectangle  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y-($YAxisUnits*$HeightScale), $this->colours[113]);
            }

            if($current == 0)
            {
                $ZeroPositionY = $y-($YAxisUnits*$HeightScale);
            }
            //if($i == 1)
            //{
            //    $LowestPosition = $y;
            //}

            //Draw Backing Gridline
            imageline  ($image, $ChartPaddingLeft, $y, $ChartWidth+($ChartPaddingLeft), $y, $this->colours[2]);
            $current = $current +$YAxisUnits;
            $i++;
        }
        //--------------------------------------------

        //Draw Bottom Left Label
        //--------------------------------------------
        $x = ($ChartPaddingLeft);
        $y = ($ChartHeight+($ChartPaddingHeight+$header));


        //$fontHeight = $fontSize; //($ChartHeight*0.05);

        $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$highestX,$this->dataLabelX));

        $textWidth = $textDimensions[5];
        //$textHeight = $textDimensions[4];


        while(($x+(($x-($ChartPaddingLeft))-$textWidth)) < 20)
        {
            $fontHeight--;

            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$highestX,$this->dataLabelX));

            $textWidth = $textDimensions[5];
            //$textHeight = $textDimensions[4];
        }

        //$textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$lowestX,$this->dataLabelX));
        //$textHeight = $textDimensions[4];

        imagettftext($image, $fontHeight, 270, $x+($textWidth/2), $y+($ChartPaddingBottom*0.3), $this->colours[1], $font, $this->getLabel($this->dataTypeX,$lowestX,$this->dataLabelX));

        imageline  ($image, $x, $y, $x, $y+($ChartPaddingBottom*0.15), $this->colours[1]);
        //imagefilledrectangle  ($image, $x, $y, ($x+($YAxisUnits*$WidthScale)), $y-$ChartHeight, $this->colours[112]);

        //Draw Top Left Label
        $x = ($ChartPaddingLeft+$ChartWidth);

        
        $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$highestX,$this->dataLabelX));

        $textWidth = $textDimensions[5];
        //$textHeight = $textDimensions[4];

        while(($x+(($x-$ChartPaddingLeft)-$textWidth)) < 5)
        {
            $fontHeight--;

            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$highestX,$this->dataLabelX));

            $textWidth = $textDimensions[4];
            //$textHeight = $textDimensions[5];
        }

        imagettftext($image  , $fontHeight, 270, $x+($textWidth/2), $y+($ChartPaddingBottom*0.3), $this->colours[1], $font, $this->getLabel($this->dataTypeX,$highestX,$this->dataLabelX));

        //Draw Line Pointing To Label
        imageline  ($image, $x, $y, $x, $y+($ChartPaddingBottom*0.15), $this->colours[1]);
        //Draw Backing Gridline
        imageline  ($image, $x-($XAxisUnits*$WidthScale), $y, $x-($XAxisUnits*$WidthScale), $y-$ChartHeight, $this->colours[2]);


        //Draw Middle Labels
        $ZeroPositionX = 0;

        $i=1;
        $current = $lowestX+$XAxisUnits;
        while($current < $highestX)
        {
            if($i > 200)
            {
                echo "<p>Looping Error 2 - $current - $i</p>";
                exit();
            }

            $x = ($ChartPaddingLeft+(($XAxisUnits*$WidthScale)*$i));

            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$current,$this->dataLabelX));

            $textWidth = $textDimensions[5];
            //$textHeight = $textDimensions[4];
            
            while(($x+(($x-$ChartPaddingLeft)-$textWidth)) < 5)
            {
                $fontHeight--;

                $textDimensions = imagettfbbox($fontHeight,0,$font,$this->getLabel($this->dataTypeX,$current,$this->dataLabelX));

                $textWidth = $textDimensions[5];
                //$textHeight = $textDimensions[4];
            }

            imagettftext($image, $fontHeight, 270, $x+($textWidth/2), $y+($ChartPaddingBottom*0.3), $this->colours[1], $font, $this->getLabel($this->dataTypeX,$current,$this->dataLabelX));

            //Draw Line Pointing To Label
            imageline  ($image, $x, $y, $x, $y+($ChartPaddingBottom*0.15), $this->colours[1]);
            //Draw Backing Gridline
            imageline  ($image, $x-($XAxisUnits*$WidthScale), $y, $x-($XAxisUnits*$WidthScale), $y-$ChartHeight, $this->colours[2]);

            //Draw Back Shading Area
            if($i % 2 == 0)
            {
                //Even Number
                //imagefilledrectangle  ($image, $x, $y, ($x+($XAxisUnits*$WidthScale)), $y-$ChartHeight, $this->colours[112]);
            }
            else
            {
                //Odd Number
                //imagefilledrectangle  ($image, $x, $y, ($x+($XAxisUnits*$WidthScale)), $y-$ChartHeight, $this->colours[113]);
            }


            if($current == 0)
            {
                //$ZeroPosition = $y-($XAxisUnits*$HeightScale);
                $ZeroPositionX = $x;
            }
            //if($i == 1)
            //{
            //    $LowestPosition = $y;
            //}

            $current = $current +$XAxisUnits;
            $i++;
        }
        //--------------------------------------------


        //Plot The Data
        //--------------------------------------------

        $i=0;
        $pointsArray = array();

        //Loop Through All The Data
        while($i < count($this->data))
        {
            //If There is Data
            if((isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0) && (isset($this->data[$i]['value2']) || $this->data[$i]['value2'] == 0) )
            {
                if($ZeroPositionX > 0)
                {
                    $pointX = $ZeroPositionX+($this->data[$i]['value2']*$WidthScale);
                }
                else
                {
                    $pointX = $ChartPaddingLeft+($this->data[$i]['value2']*$WidthScale);
                }

                if($ZeroPositionY > 0)
                {
                    $pointY = ($ZeroPositionY+($YAxisUnits*$HeightScale))-($this->data[$i]['value']*$HeightScale);
                }
                else
                {
                    $pointY = ($ChartHeight+($ChartPaddingHeight+$header))-($this->data[$i]['value']*$HeightScale);
                }

                if($DrawLine)
                {
                    if(!isset($LastPointName) || $LastPointName == "" || $LastPointName == $this->data[$i]['name'])
                    {
                        $pointsArray[] = $pointX;
                        $pointsArray[] = $pointY;
                    }
                    else
                    {
                        imagesetthickness($image, $LineThickness);

                        $length = count($pointsArray)-1;
                        //Draws All Of The Points In Reverse Because The Polygon has to complete itself
                        while($length >= 0)
                        {
                            $pointsArray[] = $pointsArray[$length-1];
                            $pointsArray[] = $pointsArray[$length];
                            $length = ($length-2);
                        }
                        imagepolygon($image, $pointsArray,(count($pointsArray)/2),$this->colours[$this->data[($i-1)]['colour']]);
                        
                        $pointsArray = array();

                        $pointsArray[] = $pointX;
                        $pointsArray[] = $pointY;
                        
                        imagesetthickness($image, 1);
                    }
                    
                    if($i == (count($this->data)-1))
                    {
                        imagesetthickness($image, $LineThickness);
                        
                        $length = count($pointsArray)-1;
                        //Draws All Of The Points In Reverse Because The Polygon has to complete itself
                        while($length >= 0)
                        {
                            $pointsArray[] = $pointsArray[$length-1];
                            $pointsArray[] = $pointsArray[$length];
                            $length = ($length-2);
                        }
                        imagepolygon($image, $pointsArray,(count($pointsArray)/2),$this->colours[$this->data[($i)]['colour']]);
                        
                        imagesetthickness($image, 1);
                    }

                    $LastPointName = $this->data[$i]['name'];

                }
                if(!$DrawLine || $DrawPoints)
                {
                    imagefilledellipse($image, $pointX, $pointY, 5, 5, $this->colours[$this->data[$i]['colour']]);
                }

                $i++;
            }
            else
            {
                echo "<p>Error2: No Data To Plot (2)</p>";
                return false;
            }
        }

        //--------------------------------------------
        //Redraw the Graph Lines
        //x line
        imageline  ($image, $ChartPaddingLeft, ($ChartPaddingHeight+$header), $ChartPaddingLeft, ($ChartHeight+($ChartPaddingHeight+$header)), $this->colours[1]);
        //y line
        imageline  ($image, $ChartPaddingLeft, ($ChartHeight+($ChartPaddingHeight+$header)), $ChartWidth+($ChartPaddingLeft), ($ChartHeight+($ChartPaddingHeight+$header)), $this->colours[1]);

        //x TOP line
        imageline  ($image, $ChartWidth+($ChartPaddingLeft), ($ChartPaddingHeight+$header), $ChartWidth+($ChartPaddingLeft), ($ChartHeight+($ChartPaddingHeight+$header)), $this->colours[1]);
        //y RIGHT line
        imageline  ($image, $ChartPaddingLeft, ($ChartPaddingHeight+$header), $ChartWidth+($ChartPaddingLeft), ($ChartPaddingHeight+$header), $this->colours[1]);


        //Draw 0 Line
        if($ZeroPositionY > 0)
        {
            if($this->dataTypeX == 1)
            {
                imageline  ($image, $ChartPaddingLeft, ($ZeroPositionY+($YAxisUnits*$HeightScale)), $ChartWidth+($ChartPaddingLeft), ($ZeroPositionY+($YAxisUnits*$HeightScale)), $this->colours[1]);
            }
        }
        if($ZeroPositionX > 0)
        {
            if($this->dataTypeY == 1)
            {
                imageline  ($image, $ZeroPositionX, $y, $ZeroPositionX, $y-$ChartHeight, $this->colours[1]);
            }
        }

        //JPG So it will work with the PDF
        imagejpeg($image,$filename.".".$file_extension);
        return $filename.".".$file_extension;
    }

    function drawKey($pfilename,$pfile_extension,$pheight,$pwidth,$ptitle,$border=true)
    {
        if(isset($pfilename))
        {
            $filename = $pfilename;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }

        if(isset($pfile_extension))
        {
            $file_extension = $pfile_extension;
        }
        else
        {
            throw new Exception("Graph Error : Filename Can't Be Blank");
        }

        if(isset($pheight) && is_numeric($pheight) && $pheight > 0)
        {
            $height = $pheight;
        }
        else
        {
            throw new Exception("Graph Error : Height Must Be A Number Greather Than 0");
        }

        if(isset($pwidth) && is_numeric($pwidth) && $pwidth > 0)
        {
            $width = $pwidth;
        }
        else
        {
            throw new Exception("Graph Error : Width Must Be A Number Greather Than 0");
        }

        $image = imagecreatetruecolor($width,$height);

        //----------------Set Colours
        $this->setColours($image);
        //-----------------------------------------------

        imagefill($image, 0, 0, $this->colours[0]);

        if($border)
        {
            imagerectangle($image,0,0,$width-1,$height-2,$this->colours[1]);
        }

        $font = $this->getFont();

        $keyHeight = (($height/count($this->data)));
        $cirleHeight = ($keyHeight*0.5);

        $fontHeight = ($cirleHeight*0.5);

        $i=0;
        $longest=0;
        $longestText="";
        while($i < count($this->data))
        {
            $textDimensions = imagettfbbox($fontHeight,0,$font,$this->data[$i]['name']);
            $textWidth = $textDimensions[4];
            //$textHeight = $textDimensions[5];
            
            if($textWidth > $longest)
            {
                $longest = $textWidth;
                $longestText = $this->data[$i]['name'];
            }
            $i++;
        }

        $loop=0;
        $cols=1;
        $longest = ($longest+($cirleHeight*2));

        if($longest > $width)
        {
            while($longest > $width)
            {
                //If The Longest Key Name Goes Of The Image Reduce The Key Size By 10%
                $cirleHeight = ($cirleHeight*0.9);

                //Recalculate The Longest Text
                $fontHeight = ($cirleHeight*0.5);
                $textDimensions = imagettfbbox($fontHeight,0,$font,$longestText);
                $longest = $textDimensions[4];
                $longest = ($longest+($cirleHeight*2));

                //Just In Case
                $loop++;
                if($loop == 20)
                {
                    throw new Exception("Graph Error : Infinite Loop");
                }
            }
        }
        elseif($longest < ($width/4))
        {
            //echo "<p>Quarter Of the Width</p>";
            $cols = 4;
        }
        elseif($longest < ($width/3))
        {
            //echo "<p>Third Of the Width</p>";
            $cols = 3;
            /*
            $keyHeight = (($height/(count($this->data)/2)));
            $cirleHeight = ($keyHeight*0.5);
            $fontHeight = ($cirleHeight*0.5);
            $longest = ($longest*2);

            while($longest > ($width/2))
            {
                $fontHeight--;
                $textDimensions = imagettfbbox($fontHeight,0,$font,$longestText);
                $longest = $textDimensions[4];
                $longest = ($longest+($cirleHeight*2));
            }
            */
        }
        elseif($longest < ($width/2))
        {
            //echo "<p>Half the Width</p>";
            $cols = 2;
            /*
            $keyHeight = (($height/(count($this->data)/2)));
            $cirleHeight = ($keyHeight*0.5);
            $fontHeight = ($cirleHeight*0.5);
            $longest = ($longest*2);
            
            while($longest > ($width/2))
            {
                $fontHeight--;
                $textDimensions = imagettfbbox($fontHeight,0,$font,$longestText);
                $longest = $textDimensions[4];
                $longest = ($longest+($cirleHeight*2));
            }
            */
        }


        $i=0;
        $c=1;
        $d=0;

        while($d < count($this->data))
        {
            if(isset($this->data[$d]['value']) || $this->data[$d]['value'] == 0)
            {
                imagefilledellipse($image, ($cirleHeight+((($width/$cols)*($c-1)))), (($keyHeight*($i+1))-($keyHeight*0.5)), $cirleHeight, $cirleHeight, $this->colours[$this->data[$d]['colour']]);
                imageellipse($image, ($cirleHeight+((($width/$cols)*($c-1)))), ($keyHeight*($i+1))-($keyHeight*0.5), $cirleHeight, $cirleHeight, $this->colours[1]);

                // Add the text
                imagettftext($image, $fontHeight, 0, (($cirleHeight*2)+(($width/$cols)*($c-1))),(($keyHeight*($i+1))-($keyHeight*0.5))+($cirleHeight*0.25), $this->colours[1], $font, $this->data[$d]['name']);
            }
            else
            {
                echo "<p>Error2: No Segment Data Provided (5)</p>";
                return false;
            }

            $i++;
            $d++;
            if(($cols > 0) && ($i >= (count($this->data)/$cols)) && ($c < $cols))
            {
                $i=0;
                $c++;

                //echo "<p>".(($width/$cols)*($c-1))."</p>";
                if($border)
                {
                    imageline  ($image  , (($width/$cols)*($c-1)), 0, (($width/$cols)*($c-1)), $height, $this->colours[1]);
                }
            }
        }

        //JPG So it will work with the PDF
        //imagepng($png,$name.".png");
        imagejpeg($image,$filename."_key.".$file_extension);
        return $filename."_key.".$file_extension;
    }

    function minutesToTime($min)
    {
        $hours = $this->leadingZero(floor($min/60));
        $minutes = $this->leadingZero(round((($min/60)-floor($min/60))*60,2));

        return "$hours:$minutes";
    }
    
    function RelativeSecondToTime($hour,$minute,$second)
    {
        //echo "<p> ".($hour*3600)." - $second";
        $hour = ($hour*3600);
        $minute = ($minute*60);
        
        $second = ($hour+$minute+$second);

        $outHour = floor($second/3600);

        $outMins = floor(($second-($outHour*3600))/60);

        $outSec = floor($second-(($outMins*60)+($outHour*3600)));

        //echo "<p>$outHour - ";
        //echo "$outMins</p>";
        //echo "</p>";

        return $this->leadingZero($outHour).":".$this->leadingZero($outMins).":".$this->leadingZero($outSec);
    }

    function leadingZero($x)
    {
        if($x > 9)
        {
            return $x;
        }
        else
        {
            return "0$x";
        }
    }

    function ColorCircle($image_name,$image_extension,$image_width,$image_height,$colour_index)
    {
        $image = imagecreatetruecolor($image_width, $image_height);

        //----------------Set Colours
        $this->setColours($image);
        //-----------------------------------------------

        //$trans_colour = imagecolorallocatealpha($png, 255, 255, 255, 127);
        imagefill($image, 0, 0, $this->colours[0]);

        imagefilledellipse($image, 10, 10, ($image_width-1), ($image_height-1), $this->colours[$colour_index]);
        imageellipse($image, 10, 10, ($image_width-1), ($image_height-1), $this->colours[1]);

        imagejpeg($image,$image_name.$image_extension);

        return $image_name.$image_extension;
    }

    function ColorSquare($image_name,$image_extension,$image_width,$image_height,$colour_index)
    {
        $image = imagecreatetruecolor($image_width, $image_height);

        imagesavealpha($image, true);

        //----------------Set Colours
        $this->setColours($image);
        //-----------------------------------------------

        //$trans_colour = imagecolorallocatealpha($png, 255, 255, 255, 127);
        imagefill($image, 0, 0, $this->colours[0]);

        imagefilledrectangle($image, 0, 0, ($image_width-1), ($image_height-1), $this->colours[$colour_index]);
        imagerectangle($image, 0, 0, ($image_width-1), ($image_height-1), $this->colours[1]);
        
        imagejpeg($image,$image_name.$image_extension);

        return $image_name.$image_extension;
    }
    
    private function GetHighAndLowValues($axis,&$highest,&$lowest)
    {
        if($axis == "Y")
        {
            $i=0;
            $highest=0;
            $lowest=99999999999;
    
            while($i < count($this->data))
            {
                if(isset($this->data[$i]['value']) || $this->data[$i]['value'] == 0)
                {
                    if($this->data[$i]['value'] > $highest)
                    {
                        $highest = $this->data[$i]['value'];
                    }
    
                    if($this->data[$i]['value'] < $lowest)
                    {
                        $lowest = $this->data[$i]['value'];
                    }
                }
                else
                {
                    return false;
                }
    
                if(!isset($this->data[$i]['colour']))
                {
                    return false;
                }
                $i++;
            }
        }
        elseif($axis == "X")
        {
            $i=0;
            $highest=0;
            $lowest=99999999999;
    
            while($i < count($this->data))
            {
                if(isset($this->data[$i]['value2']) || $this->data[$i]['value2'] == 0)
                {
                    if($this->data[$i]['value2'] > $highest)
                    {
                        $highest = $this->data[$i]['value2'];
                    }

                    if($this->data[$i]['value2'] < $lowest)
                    {
                        $lowest = $this->data[$i]['value2'];
                    }
                }
                else
                {
                    return false;
                }
    
                if(!isset($this->data[$i]['colour']))
                {
                    return false;
                }
                $i++;
            }
        }
        else
        {
            return false;
        }

        return true;
    }
    
    private function GetAxisUnits($dataType,$NoLabels,&$highest,&$lowest,&$AxisUnits,$StartFromZero,$GapAtEnd=0)
    {
        //Calculate Y Axis Units
        //Calculates The Best Units For The Y Axis and the Best Height
        //--------------------------------------------
        if($lowest < 0)
        {
            $height = $highest-$lowest;
        }
        elseif(!$StartFromZero && $lowest > 0)
        {
            $height = $highest-$lowest;
        }
        else
        {
            $height = $highest;
        }
        $Difference = ($height/$NoLabels);

        $div = 1;
        while($Difference  > 1)
        {
            $Difference = ($Difference/10);
            $div = ($div*10);
        }

        $TestValues = array();

        if($dataType == 2 || $dataType == 3)
        {
            $TestValues[] = 0.9;
            $TestValues[] = 0.6;
            $TestValues[] = 0.45;
            $TestValues[] = 0.3;
            $TestValues[] = 0.15;
            $TestValues[] = 0.1;
        }
        elseif($dataType == 4)
        {
            $TestValues[] = 0.9;
            $TestValues[] = 0.6;
            //$TestValues[] = 0.45;
            $TestValues[] = 0.3;
            //$TestValues[] = 0.15;
            $TestValues[] = 0.1;
        }
        else
        {
            $TestValues[] = 1;
            $TestValues[] = 0.5;
            $TestValues[] = 0.25;
            $TestValues[] = 0.2;
            $TestValues[] = 0.1;
        }

        $BestIdx=0;
        $MinDiff = 999999;
        for ($i = 1; $i <= count($TestValues)-1; $i++)
        {
            $Diff = $Difference - $TestValues[$i];

            //The $Diff < 0 prevents the Units*Labels from being less than the highest.
            if(($Diff < 0) && (abs($Diff) < abs($MinDiff)))
            {
                $MinDiff = $Diff;

                $BestIdx = $i;
            }
        }

        $AxisUnits = ($TestValues[$BestIdx]*($div));

        //Minimum Units is 0
        //If not the Number_Format On The Labels Will Round to a Whole Number
        if($AxisUnits < 1)
        {
            $AxisUnits=1;
        }

        //New Highest And Lowest Units
        $i=1;
        while(($highest+$GapAtEnd) > ($AxisUnits*$i))
        {
            $i++;
        }
        $highest = ($AxisUnits*$i);
        $i=1;

        if($lowest < 0)
        {
            while(($lowest-1) < ((0-$AxisUnits)*$i))
            {
                $i++;
            }
            $lowest = ((0-$AxisUnits)*$i);
        }
        elseif(!$StartFromZero && $lowest > 0)
        {
            /*
            while(($lowest) > ($AxisUnits*$i))
            {
                $i++;
            }
            */
            //$lowest = ($AxisUnits*$i);

            echo "<p>".$AxisUnits."</p>";
        }
        else
        {
            $lowest = 0;
        }
        //--------------------------------------------
    }

    private function setColours($image)
    {
        //----------------Set Colours
        $this->colours[0] = imagecolorallocate($image, 255, 255, 255);   //white
        $this->colours[1] = imagecolorallocate($image, 0, 0, 0);         //black
        $this->colours[2] = imagecolorallocate($image, 211, 211, 211);   //Grey
        $this->colours[3] = imagecolorallocatealpha ($image, 248, 94, 96,1); //Red
        $this->colours[4] = imagecolorallocatealpha ($image, 94, 94, 250,1); //Blue*
        $this->colours[5] = imagecolorallocatealpha  ($image, 94, 172, 96,1); //Green*
        $this->colours[6] = imagecolorallocatealpha ($image, 248, 249, 96,1); //Yellow*
        $this->colours[7] = imagecolorallocatealpha ($image, 178, 89, 183,1); //Purple*
        $this->colours[8] = imagecolorallocatealpha ($image, 248, 194, 96,1); //Orange
        $this->colours[9] = imagecolorallocatealpha ($image, 94, 249, 95,1); //Lime*
        $this->colours[10] = imagecolorallocatealpha ($image, 170, 94, 96,1); //Maroon*
        $this->colours[11] = imagecolorallocatealpha ($image, 248, 94, 250,1); //Fuschia*
        $this->colours[12] = imagecolorallocatealpha ($image, 94, 249, 251,1); //Aqua*
        $this->colours[13] = imagecolorallocatealpha ($image, 95, 94, 172,1); //Navy*
        $this->colours[14] = imagecolorallocatealpha ($image, 171, 172, 96,1); //Olive*
        $this->colours[15] = imagecolorallocatealpha ($image, 97, 170, 176,1); //Teal*
        $this->colours[16] = imagecolorallocatealpha ($image, 171, 172, 174,1); //Gray*
        $this->colours[17] = imagecolorallocatealpha ($image, 210, 211, 213,1); //Silver*
        $this->colours[18] = imagecolorallocatealpha ($image, 127, 255, 212,1); //Aquamarine
        $this->colours[19] = imagecolorallocatealpha ($image, 177, 121, 232,1); //Blue-Violet
        $this->colours[20] = imagecolorallocatealpha ($image, 194, 120, 117,1); //Brown
        $this->colours[21] = imagecolorallocatealpha ($image, 151, 191, 193,1); //Cadet Blue
        $this->colours[22] = imagecolorallocatealpha ($image, 172, 249, 95,1); //Chartreuse
        $this->colours[23] = imagecolorallocatealpha ($image, 221, 158, 114,1); //Chocolate
        $this->colours[24] = imagecolorallocatealpha ($image, 248, 171, 145,1); //Coral
        $this->colours[25] = imagecolorallocatealpha ($image, 154, 186, 237,1); //Cornflower Blue
        $this->colours[26] = imagecolorallocatealpha ($image, 94, 249, 251,1); //Cyan
        $this->colours[27] = imagecolorallocatealpha ($image, 94, 95, 178,1); //Dark Blue
        $this->colours[28] = imagecolorallocatealpha ($image, 111, 177, 176,1); //Dark Cyan
        $this->colours[29] = imagecolorallocatealpha ($image, 205, 175, 102,1); //Dark Goldenrod
        $this->colours[30] = imagecolorallocatealpha ($image, 196, 197, 199,1); //Dark Gray
        $this->colours[31] = imagecolorallocatealpha ($image, 94, 155, 95,1); //Dark Green
        $this->colours[32] = imagecolorallocatealpha ($image, 207, 206, 160,1); //Dark Khaki
        $this->colours[33] = imagecolorallocatealpha ($image, 178, 95, 175,1); //Dark Magenta
        $this->colours[34] = imagecolorallocatealpha ($image, 145, 159, 124,1); //Dark Olive Green
        $this->colours[35] = imagecolorallocatealpha ($image, 246, 180, 94,1); //Dark Orange
        $this->colours[36] = imagecolorallocatealpha ($image, 185, 126, 220,1); //Dark Orchid
        $this->colours[37] = imagecolorallocatealpha ($image, 178, 93, 96,1); //Dark Red
        $this->colours[38] = imagecolorallocatealpha ($image, 234, 185, 170,1); //Dark Salmon
        $this->colours[39] = imagecolorallocatealpha ($image, 180, 208, 183,1); //Dark Sea Green
        $this->colours[40] = imagecolorallocatealpha ($image, 137, 131, 179,1); //Dark Slate Blue
        $this->colours[41] = imagecolorallocatealpha ($image, 127, 141, 141,1); //Dark Slate Gray
        $this->colours[42] = imagecolorallocatealpha ($image, 94, 219, 223,1); //Dark Turquoise
        $this->colours[43] = imagecolorallocatealpha ($image, 176, 96, 227,1); //Dark Violet
        $this->colours[44] = imagecolorallocatealpha ($image, 248, 107, 185,1); //Deep Pink
        $this->colours[45] = imagecolorallocatealpha ($image, 94, 209, 253,1); //Deep Sky Blue
        $this->colours[46] = imagecolorallocatealpha ($image, 157, 158, 160,1); //Dim Gray
        $this->colours[47] = imagecolorallocatealpha ($image, 113, 182, 251,1); //Dodger Blue
        $this->colours[48] = imagecolorallocatealpha ($image, 205, 113, 118,1); //Firebrick
        $this->colours[49] = imagecolorallocatealpha ($image, 114, 179, 115,1); //Forest Green
        $this->colours[50] = imagecolorallocatealpha ($image, 227, 228, 230,1); //Gainsboro
        $this->colours[51] = imagecolorallocatealpha ($image, 249, 224, 95,1); //Gold
        $this->colours[52] = imagecolorallocatealpha ($image, 227, 194, 113,1); //Goldenrod
        $this->colours[53] = imagecolorallocatealpha ($image, 199, 249, 124,1); //Green-Yellow
        $this->colours[54] = imagecolorallocatealpha ($image, 234, 169, 203,1); //Hot Pink
        $this->colours[55] = imagecolorallocatealpha ($image, 218, 150, 151,1); //Indian Red
        $this->colours[56] = imagecolorallocatealpha ($image, 239, 234, 179,1); //Khaki
        $this->colours[57] = imagecolorallocatealpha ($image, 232, 234, 247,1); //Lavender
        $this->colours[58] = imagecolorallocatealpha ($image, 249, 240, 245,1); //Lavender Blush
        $this->colours[59] = imagecolorallocatealpha ($image, 165, 249, 91,1); //Lawn Green
        $this->colours[60] = imagecolorallocatealpha ($image, 248, 246, 221,1); //Lemon Chiffon
        $this->colours[61] = imagecolorallocatealpha ($image, 200, 224, 236,1); //Light Blue
        $this->colours[62] = imagecolorallocatealpha ($image, 238, 172, 173,1); //Light Coral
        $this->colours[63] = imagecolorallocatealpha ($image, 229, 248, 252,1); //Light Cyan
        $this->colours[64] = imagecolorallocatealpha ($image, 238, 229, 174,1); //Light Goldenrod
        $this->colours[65] = imagecolorallocatealpha ($image, 244, 246, 222,1); //Light Goldenrod Yellow
        $this->colours[66] = imagecolorallocatealpha ($image, 222, 222, 222,1); //Light Gray
        $this->colours[67] = imagecolorallocatealpha ($image, 180, 241, 181,1); //Light Green
        $this->colours[68] = imagecolorallocatealpha ($image, 248, 205, 212,1); //Light Pink
        $this->colours[69] = imagecolorallocatealpha ($image, 248, 192, 169,1); //Light Salmon
        $this->colours[70] = imagecolorallocatealpha ($image, 113, 202, 200,1); //Light Sea Green
        $this->colours[71] = imagecolorallocatealpha ($image, 175, 219, 246,1); //Light Sky Blue
        $this->colours[72] = imagecolorallocatealpha ($image, 175, 162, 250,1); //Light Slate Blue
        $this->colours[73] = imagecolorallocatealpha ($image, 167, 177, 187,1); //Light Slate Gray
        $this->colours[74] = imagecolorallocatealpha ($image, 196, 212, 228,1); //Light Steel Blue
        $this->colours[75] = imagecolorallocatealpha ($image, 248, 249, 231,1); //Light Yellow
        $this->colours[76] = imagecolorallocatealpha ($image, 124, 219, 125,1); //Lime Green
        $this->colours[77] = imagecolorallocatealpha ($image, 245, 240, 236,1); //Linen
        $this->colours[78] = imagecolorallocatealpha ($image, 248, 94, 250,1); //Magenta
        $this->colours[79] = imagecolorallocatealpha ($image, 155, 218, 197,1); //Medium Aquamarine
        $this->colours[80] = imagecolorallocatealpha ($image, 92, 92, 206,1); //Medium Blue
        $this->colours[81] = imagecolorallocatealpha ($image, 207, 146, 223,1); //Medium Orchid
        $this->colours[82] = imagecolorallocatealpha ($image, 183, 162, 227,1); //Medium Purple
        $this->colours[83] = imagecolorallocatealpha ($image, 131, 203, 165,1); //Medium Sea Green
        $this->colours[84] = imagecolorallocatealpha ($image, 168, 154, 241,1); //Medium Slate Blue
        $this->colours[85] = imagecolorallocatealpha ($image, 93, 246, 190,1); //Medium Spring Green
        $this->colours[86] = imagecolorallocatealpha ($image, 137, 222, 219,1); //Medium Turquoise
        $this->colours[87] = imagecolorallocatealpha ($image, 214, 107, 175,1); //Medium Violet-Red
        $this->colours[88] = imagecolorallocatealpha ($image, 110, 110, 164,1); //Midnight Blue
        $this->colours[89] = imagecolorallocatealpha ($image, 214, 107, 175,1); //Misty Rose
        $this->colours[90] = imagecolorallocatealpha ($image, 110, 110, 164,1); //Moccasin
        $this->colours[91] = imagecolorallocatealpha ($image, 248, 232, 232,1); //Navajo White
        $this->colours[92] = imagecolorallocatealpha ($image, 248, 233, 204,1); //Olive Drab
        $this->colours[93] = imagecolorallocatealpha ($image, 248, 229, 199,1); //Orange-Red
        $this->colours[94] = imagecolorallocatealpha ($image, 158, 180, 116,1); //Orchid
        $this->colours[95] = imagecolorallocatealpha ($image, 247, 139, 93,1); //Pale Goldenrod
        $this->colours[96] = imagecolorallocatealpha ($image, 186, 247, 187,1); //Pale Green
        $this->colours[97] = imagecolorallocatealpha ($image, 199, 239, 239,1); //Pale Turquoise
        $this->colours[98] = imagecolorallocatealpha ($image, 226, 163, 184,1); //Pale Violet-Red
        $this->colours[99] = imagecolorallocatealpha ($image, 248, 227, 208,1); //Peach Puff
        $this->colours[100] = imagecolorallocatealpha ($image, 218, 175, 133,1); //Peru
        $this->colours[101] = imagecolorallocatealpha ($image, 248, 211, 218,1); //Pink
        $this->colours[102] = imagecolorallocatealpha ($image, 227, 190, 231,1); //Plum
        $this->colours[103] = imagecolorallocatealpha ($image, 201, 230, 236,1); //Powder Blue
        $this->colours[104] = imagecolorallocatealpha ($image, 209, 180, 182,1); //Rosy Brown
        $this->colours[105] = imagecolorallocatealpha ($image, 132, 158, 232,1); //Royal Blue
        $this->colours[106] = imagecolorallocatealpha ($image, 177, 136, 106,1); //Saddle Brown
        $this->colours[107] = imagecolorallocatealpha ($image, 245, 172, 165,1); //Salmon
        $this->colours[108] = imagecolorallocatealpha ($image, 235, 199, 167,1); //Sandy Brown
        $this->colours[109] = imagecolorallocatealpha ($image, 122, 179, 147,1); //Sea Green
        $this->colours[110] = imagecolorallocatealpha ($image, 248, 243, 239,1); //Seashell
        $this->colours[111] = imagecolorallocatealpha ($image, 190, 144, 118,1); //Sienna
        $this->colours[112] = imagecolorallocatealpha ($image, 229, 233, 236,50); //Back1
        $this->colours[113] = imagecolorallocatealpha ($image, 225, 226, 230,50); //Back2
        //-----------------------------------------------  
    }
    
    private function getFont()
    {
        //Calculate Relative Path To Font Files
        //--------------------------------------------------------------------
        //--------------------------------------------------------------------
        if(substr_count(dirname(__FILE__), "/") > 0)
        {
            $include_dist = substr_count(dirname(__FILE__), "/");
        }
        else
        {
            $include_dist = substr_count(dirname(__FILE__), "\\");
        }

        if(substr_count(dirname($_SERVER['SCRIPT_FILENAME']), "/") > 0)
        {
            $calling_dist = substr_count(dirname($_SERVER['SCRIPT_FILENAME']), "/");
        }
        else
        {
            $calling_dist = substr_count(dirname($_SERVER['SCRIPT_FILENAME']), "\\");
        }

        $relativePath = str_repeat('../', $calling_dist - $include_dist + 1)."graphs/";
        //--------------------------------------------------------------------
        //--------------------------------------------------------------------

        if(file_exists('fonts/Arial.ttf'))
        {
            $font = 'fonts/Arial.ttf';
            return $font;
        }
        elseif(file_exists($relativePath.'fonts/Arial.ttf'))
        {
            $font = $relativePath.'fonts/Arial.ttf';
            return $font;
        }
        else
        {
            echo "Couldn't Find Font";

            exit();
        }
    }

    function daysSince1900($endDate)
    {
        $beginDate = "01/01/1900";

        return $this->dateDiff($beginDate, $endDate);
    }

    function dateFromDaysSince1900($days)
    {
        $date_parts = explode("/", "01/01/1900");
        $start_date = gregoriantojd($date_parts[1], $date_parts[0], $date_parts[2]);
        $date_parts = explode("/",jdtogregorian($start_date+$days));

        return $this->leadingZero($date_parts[1])."/".$this->leadingZero($date_parts[0])."/".$date_parts[2];
    }

    private function dateDiff($beginDate, $endDate)
    {
        //dd/mm/yyyy
        $date_parts1=explode("/", $beginDate);
        $date_parts2=explode("/", $endDate);
        $start_date=gregoriantojd($date_parts1[1], $date_parts1[0], $date_parts1[2]);
        $end_date=gregoriantojd($date_parts2[1], $date_parts2[0], $date_parts2[2]);
        return $end_date - $start_date;
    }

    private function getLabel($DataType,$Value,$label="")
    {
        if($DataType == 1)
        {
            return number_format($Value).$label;
        }
        elseif($DataType == 2 || $DataType == 3)
        {
            return $this->minutesToTime($Value.$label);
        }
        elseif($DataType == 4)
        {
            return $this->RelativeSecondToTime($this->RelativeHour,$this->RelativeMinute,$Value).$label;
        }
        elseif($DataType == 5)
        {
            //Date
            return $this->dateFromDaysSince1900($Value).$label;
        }
        elseif($DataType == 6)
        {
            //Relative Date
            return $this->dateFromDaysSince1900($this->RelativeDate+$Value).$label;
        }
        elseif($DataType == 7)
        {
            //Relative Date
            $Year = $this->RelativeYear;
            $Week = ($this->RelativeWeek+$Value);
            
            while($Week > 52)
            {
                $Week = ($Week-53);
                $Year++;
            }

            return $Week." - ".$Year.$label;
        }
        else
        {
            return number_format($Value);
        }

    }

    /*
    private function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
    {
        //this way it works well only for orthogonal lines
        //imagesetthickness($image, $thick);
        //return imageline($image, $x1, $y1, $x2, $y2, $color);

        if ($thick == 1) 
        {
            return imageline($image, $x1, $y1, $x2, $y2, $color);
        }
        $t = $thick / 2 - 0.5;
        if ($x1 == $x2 || $y1 == $y2)
        {
            return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
        }
        $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
        $a = $t / sqrt(1 + pow($k, 2));
        $points = array
        (
            round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
            round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
            round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
            round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
        );
        imagefilledpolygon($image, $points, 4, $color);
        return imagepolygon($image, $points, 4, $color);
    }
    */

}
