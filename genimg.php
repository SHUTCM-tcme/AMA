<?php
require_once "functions.php";
require_once "jpgraph/jpgraph.php";
require_once "jpgraph/jpgraph_line.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $peaknumberarray = explode(",", $_GET['peakresult']);
    //加载图表
    //创建统计图对象,宽，高
    $graph = new Graph(2000, 500);
    //设置边距，空余四角边距（左右上下）
    $graph->img->SetMargin(10, 10, 10, 10);
    $graph->SetScale('intlin');
    //设置统计图标题
    $graph->title->Set('Test graphic');
    $graph->subtitle->Set($testername);
    //创建折线图
    $lineplot = new LinePlot($peaknumberarray);
    //折线填入图
    $graph->Add($lineplot);
    //显示折线图
    $graph->Stroke();
}
