<?php
    
    header("Content-Type: image/jpeg");
    include __DIR__."/slack-config.php";
    include __DIR__."/g2pa-cputemp.php";

    $cpuTempData = createCPUTempImage();
    $imagePath = $cpuTempData["path"];
    $cpuTemp = $cpuTempData["temp"];

    $data = [
        "token" => $slackToken,
        "channels" => "CAU6A8HBQ",
        "title" => "[".$cpuTemp."℃] 芹那サーバ",
        "content" => "芹那サーバ＠ジーセカンド下野リージョンのCPU温度は、「".$cpuTemp."℃」です。"
    ];

    $image = file_get_contents($imagePath);

    sendReqest($data, $imagePath);

    function sendReqest($data, $filepath){

        $uri = "https://slack.com/api/files.upload";
        $CNL = "\r\n";

        $arrContent = [];
        $boundary = "----1234567890";
        $arrContent[] = $CNL.'--'.$boundary;

        if(count($data) > 0){
            foreach($data as $key => $val) {
                $arrContent[] = 'Content-Disposition: form-data; name="'.$key.'"'.$CNL;
                $arrContent[] = $val;
                $arrContent[] = '--'.$boundary;
            }
        }
    
        if(file_exists($filepath)){
            $imageFile = file_get_contents($filepath);
            $key = 'file';
            $arrContent[] = 'Content-Disposition: form-data; name="'.$key.'"; filename="'.basename($filepath).'"';
            $arrContent[] = 'Content-Type: image/jpeg';
            $arrContent[] = $CNL.$imageFile;//画像ファイルデータを挿入
            $arrContent[] = '--'.$boundary;
        }

        $content = join($CNL, $arrContent);
        $content .= '--'.$CNL;//終端

        $header = join($CNL,array(
            "Content-Type: multipart/form-data; boundary=".$boundary,
            "Content-Length: ".strlen($content)
        ));

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => $header,
                'content' => $content
            )
        ));

        $result = file_get_contents($uri, false, $context);
        
        return $result;
    }
    echo $image;