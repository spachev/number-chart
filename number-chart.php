<?
 //global $w,$h;

 $w = 700;
 $h = 825;
 $font = "/usr/share/fonts/truetype/ttf-bitstream-vera/VeraBd.ttf";
 $font_size = 18;
 $n_start = 1;
 $n_end = 150;
 $n_step = 10;

 init_param("w");
 init_param("h");
 init_param("n_start");
 init_param("n_end");
 init_param("font_size");
 init_param("n_step");
 $start_x = $font_size;
 $start_y = $font_size + 10;
 $step_x = 3*$font_size + 10;
 $step_y = 3*$font_size;
 init_param("start_x");
 init_param("start_y");
 init_param("step_x");
 init_param("step_y");

 function init_param($var_name)
 {
   if (!empty($_REQUEST[$var_name]))
   {
     $val = $_REQUEST[$var_name];
     $GLOBALS[$var_name] = $val;
    }
 }

 function write_number($x,$y,$n)
 {
   global $im,$fg_color,$font_size,$font;

   if (!($box = imagettftext($im,$font_size,0,$x,$y,$fg_color,$font,$n)))
     die("Error drawing text");

   $el_w = $box[2] - $box[0] + $font_size;
   $el_h = $box[1] - $box[7] + $font_size/2;

   $el_x = ($box[0] + $box[2])/2;
   $el_y = ($box[1] + $box[7])/2;
   imageellipse($im,$el_x,$el_y, $el_w, $el_h,$fg_color);
   return array($el_x,$el_y,$el_w,$el_h);
 }

 function draw_number_line($x,$y,$start,$end, $dx,$dy)
 {
   global $font_size;
   $arrow_start_x = $arrow_end_x = $arrow_end_y = 0;
   $arrow_start_y = 0;

   for ($n = $start; $n <= $end; $n++)
   {
     $box = write_number($x,$y,$n);

     if ($arrow_start_x)
     {
       if ($dx > 0)
       {
         $arrow_end_x = $box[0] - $box[2]/2;
       }
       else
       {
         $arrow_end_x = $box[0] + $box[2]/2;
       }

       draw_arrow($arrow_start_x,$arrow_start_y,$arrow_end_x,$arrow_start_y + $dy);
     }

     $arrow_start_x = ($dx > 0) ? $box[0] + $box[2]/2 : $box[0] - $box[2]/2;
     $arrow_start_y = $box[1];

     $x += $dx;
     $y += $dy;
   }

   return array($x,$y,$box[0], $box[1] + $box[3]/2,$box[3]);
 }

 function draw_arrow($from_x,$from_y,$to_x,$to_y)
 {
   global $im,$fg_color;
   imageline($im,$from_x,$from_y,$to_x,$to_y,$fg_color);
 }

 if (!($im = imagecreate($w,$h)))
   die("Could not create image");

 $bg_color = imagecolorallocate($im,255,255,255);
 $fg_color = imagecolorallocate($im,0,0,0);

 $dx = $step_x;
 $dh = $step_y;

 $x = $start_x;
 $y = $start_y;
 
 for ($n = $n_start; $n < $n_end; $n += $n_step)
 {
   $end = draw_number_line($x,$y,$n,$n + $n_step - 1,$dx,0);
   if ($n + $n_step < $n_end)
     draw_arrow($end[2],$end[3],$end[2],$end[3] + $dh - $end[4]);
   $x = $end[0] - $dx;
   $y = $end[1] + $dh;
   $dx = -$dx;
 }

 header("Content-type: image/jpeg");
 ob_start();
 imagejpeg($im);
 $buf = ob_get_contents();
 ob_end_clean();
 header("Content-length: ".strlen($buf));
 print $buf;
 imagedestroy($im);
?>
