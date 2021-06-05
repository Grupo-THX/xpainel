<?php
function xPainelAutoLoad($classe)
{
  try
  {
     $classe = str_replace('\\', '/', $classe);
    //echo '<button class="btn btn-success btn-bordered btn-block btn-xs logsX" style="text-align:left; background:#FFF; width:100%">['.date('G:i:s').']->DEV# > '.$classe.'</button>';
    if(strstr($classe, 'PMA'))
    {
      return;
    }
    if(file_exists(ROOT."/xpainel/class/{$classe}.class.php"))
    {
      include_once ROOT."/xpainel/class/{$classe}.class.php";
    }
  }
  catch (Exception $e)
  {
            die('<div style="    left: 50%;
          margin: -200px 0 0 -250px;
          position: absolute;
          top: 50%;
          width: 500px;
          height: 400px;
          border: solid #1D2127 1px;
          background-color: #FFF;
          border-radius: 5px;
          padding: 10px;
          font-size: 20px;">
            <h1>Fatal Error :(</h1><hr />'.$e->getMessage().'
          </div>');


  }

}

spl_autoload_register('xPainelAutoLoad');

