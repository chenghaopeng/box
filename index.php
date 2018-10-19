<!DOCTYPE html>
<?php
    $res=-1;
    $resinfo="";
    if(isset($_GET["s"])){
        $res=1;
        if($_GET["s"]!=""){
            if(is_dir("./upload/".$_GET["s"])){
                $tf=fopen("./upload/".$_GET["s"]."/name.txt","r");
                $fname = fread($tf,filesize("./upload/".$_GET["s"]."/name.txt"));
                fclose($tf);
                $file = "./upload/".$_GET["s"]."/".$fname;
                if (file_exists($file)){
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.basename($file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    ob_clean();
                    flush();
                    readfile($file);
                    $res=0;
                    $resinfo="文件正在取回...";
                }
                else{
                    $resinfo="文件已过期！";
                }
            }
            else{
                $resinfo="取件码错误！";
            }
        }
        else{
            $resinfo="取件码不能为空！";
        }
    }
    else if(isset($_POST["s"])){
        $res=1;
        if($_POST["s"]!=""){
            if(!is_dir("./upload/".$_POST["s"])){
                if($_FILES["file"]["size"]>0){
                    if($_FILES["file"]["size"]<=10*1024*1024){
                        if($_FILES["file"]["error"]==0){
                            mkdir("./upload/".$_POST["s"], 0777, true);
                            move_uploaded_file($_FILES["file"]["tmp_name"],"./upload/".$_POST["s"]."/".$_FILES["file"]["name"]);
                            write_file("./upload/".$_POST["s"]."/name.txt",false,$_FILES["file"]["name"]);
                            $res=0;
                            $resinfo="文件放入成功！请牢记取件码：".$_POST["s"];
                        }
                        else{
                            $resinfo="文件上传错误！";
                        }
                    }
                    else{
                        $resinfo="请勿放入超过10M的文件！";
                    }
                }
                else{
                    $resinfo="请选择要放入的文件！";
                }
            }
            else{
                $resinfo="取件码已被占用！";
            }
        }
        else{
            $resinfo="取件码不能为空！";
        }
    }
    if($res!=-1){
        header("Location: http://box.chper.cn/?r=".$res."&t=".$resinfo);
    }
    
    function write_file($path,$appe,$text){
        if($appe==true){
            $file=fopen($path,"a");
        }
        else{
            $file=fopen($path,"w");
        }
        fwrite($file,$text);
        fclose($file);
    }
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title>鹏鹏的盒子</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.0/js/bootstrap.min.js"></script>
  </head>
  <body>
    <nav class="navbar navbar-expand-sm bg-light fixed-top navbar-primary" style="font-size: 20px;">
      <a class="navbar-brand" href="#">
        <img src="logo.jpg" class="rounded-circle" style="width: 50px;">
      </a>
      <span class="navbar-text">
        鹏鹏的盒子
      </span>
      <ul class="navbar-nav nav-pills">
        <li class="nav-item">
          <a class="nav-link" href="http://chper.cn/" target="_blank">返回主站</a>
        </li>
      </ul>
    </nav>
    <div class="container" style="margin-top: 100px;">
      <?php
          if(isset($_GET["r"])){
              echo '<div class="alert alert-'.($_GET["r"]==0?"success":"danger").' alert-dismissable fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$_GET["t"].'</div>';
          }
      ?>
      <div id="accordion">
        <div class="card">
          <div class="card-header">
            <a class="card-link acco-title" data-toggle="collapse" href="#dlbox">
              取 回
            </a>
          </div>
          <div id="dlbox" class="collapse" data-parent="#accordion">
            <div class="card-body">
              <form id="dlform" action="./" method="GET">
                <div class="row">
                  <div class="col-sm-10">
                    <div class="input-group mt-2 mb-2">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          取件码
                        </span>
                      </div>
                      <input name="s" type="text" class="form-control" autocomplete="off">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group mt-2 mb-2">
                      <input type="submit" value="取 回" class="form-control btn-primary">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <a class="card-link acco-title" data-toggle="collapse" href="#ulbox">
              放 入
            </a>
          </div>
          <div id="ulbox" class="collapse" data-parent="#accordion">
            <div class="card-body">
              <form id="ulform" action="./" method="POST" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group mt-2 mb-2">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            取件码
                          </span>
                        </div>
                        <input id="s" name="s" type="text" class="form-control" autocomplete="off">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="custom-file mt-2 mb-2">
                        <input id="file" type="file" class="custom-file-input" name="file" onchange="getFilesize(this)">
                        <label id="filelabel" class="custom-file-label" for="customFile">选择文件</label>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="input-group mt-2 mb-2">
                        <input name="submit" type="submit" value="放 入" class="form-control btn-primary">
                      </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <a class="card-link acco-title" data-toggle="collapse" href="#zybox">
              注 意
            </a>
          </div>
          <div id="zybox" class="collapse show" data-parent="#accordion">
            <div class="card-body mt-3 ml-2">
              <span>
                <p>1、最大文件大小为10M，文件格式不限，但不提倡可执行文件，建议使用压缩包</p>
                <p>2、请爱惜平台，勿上传违法违规文件、病毒等</p>
                <p>3、一旦上传文件，即视为你同意将该文件公开给任何人</p>
                <p>4、不禁止但不提倡将本平台的下载链接公布在其他平台</p>
                <p>5、本平台无义务提供长久的文件保存服务，任何时间本平台有权无责任地更改、删除任何文件</p>
                <p>6、本平台无义务确认你取回的文件是否正确、安全，请使用你信任的收取码</p>
                <p>7、一旦你使用了本平台的服务，即视为你同意以上条款，本平台保留最终解释权</p>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
        function getFilesize(file){
            fileSize = file.files[0].size / 1024;
            if (fileSize > 1024*10) {
                alert("请勿放入超过10M的文件！");
                document.getElementById('file').value = '';
            }
            if($(file).val()==""){
                $("#filelabel").html("选择文件");
            }
            else{
                var strs = new Array();
                var ppth = $(file).val();
                strs = ppth.split('\\');
                var fn = strs[strs.length - 1];
                $("#filelabel").html(fn);
            }
        }
    </script>
  </body>
</html>