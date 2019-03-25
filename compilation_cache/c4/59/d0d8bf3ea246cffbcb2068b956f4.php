<?php

/* index.html */
class __TwigTemplate_c459d0d8bf3ea246cffbcb2068b956f4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"ru\">
<head>
  <meta http-equiv=\"Cache-Control\" content=\"no-cache\">
  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
  <title>\"ГБОУ Лицей 1581\"</title>

<!--  <link rel=\"shortcut icon\" href=\"img/favicon/favicon.ico\" type=\"image/x-icon\">
  <link rel=\"apple-touch-icon\" href=\"img/favicon/apple-touch-icon.png\">
  <link rel=\"apple-touch-icon\" sizes=\"72x72\" href=\"img/favicon/apple-touch-icon-72x72.png\">
  <link rel=\"apple-touch-icon\" sizes=\"114x114\" href=\"img/favicon/apple-touch-icon-114x114.png\"> -->

  <link href='/app/views/assets/css/bootstrap.min.css' rel='stylesheet' media='screen'>
  <link href='/app/views/assets/css/bootstrap-responsive.min.css' rel='stylesheet' media='screen'>
  <link href='/app/views/assets/css/media.css' rel='stylesheet'>

  <script src=\"/app/views/assets/js/modernizr-2.6.2-respond-1.1.0.min.js\"></script>

</head>

<body>
  <div class='maincontainer'>
      <div class='wrapper'>
      <div class='inline backleft'>
        <div class='lk_enter'>
          <div class='name'>
            ВХОД В ЛИЧНЫЙ КАБИНЕТ
          </div>
          <div class='login_pass'>
          <form class=\"form-signin\">
          <div class='status'><span>";
        // line 31
        if (isset($context["mess"])) { $_mess_ = $context["mess"]; } else { $_mess_ = null; }
        echo $_mess_;
        echo "</span></div>
            <div class='login'>
              ЛОГИН:
              <div>
                <input type='text' id=\"email_user\" class='inpt'/>
              </div>
            </div>
            <div class='pass'>
              ПАРОЛЬ:
              <div>
                <input type='password' id=\"pass_user\" class='inpt'/>
              </div>
            </div>
            <div class='button'>
              <button class=\"btn btn-large btn-primary\" type=\"submit\">Войти</button>
            </div>
            </form>
          </div>
        </div>
        <div class='copyright'>Copyright © <a target=\"blank\" href=\"http://www.alexkam.ru\">Alexkam</a> - All Rights Reserved | Moscow 2016</div>
      </div>
      <div class='inline backright'>
        <div class='links first'>
          <div class='icon inline'><img src='/app/views/assets/img/login/tp.png'></div>
          <div class='name_link inline'>ТЕХНИЧЕСКАЯ ПОДДЕРЖКА</div>
        </div>
        <div class='links second'>
          <div class='icon inline'><img src='/app/views/assets/img/login/do.png'></div>
          <div class='name_link inline'><a href='http://monitoring.1581mgtu.ru' target='blank'>МОНИТОРИНГ ПОСТУПАЮЩИХ</a></div>
        </div>
        <div class='links third'>
          <div class='icon inline'><img src='/app/views/assets/img/login/ej.png'></div>
          <div class='name_link inline'><a href='https://mrko.mos.ru/dnevnik/' target='blank'>ЭЛЕКТРОННЫЙ ЖУРНАЛ</a></div>
        </div>
      </div>
      <div class='blue_item inline'>
        <div class='text_item'>
          <div class='main_text'>ГБОУ</div> Лицей №1581
        </div>
      </div>
    </div>
  </div>
  <script src=\"/app/views/assets/js/jquery-1.9.1.min.js\"></script>
    <script src=\"/app/views/assets/js/bootstrap.min.js\"></script>
  <script>
    \$(document).ready(function(){
      \$(\".form-signin\").submit(function(e){
        e.preventDefault();
        \$.ajax({
          url: \"ajax/\",
          type: \"POST\",
          data: {
            email_user: \$(\"#email_user\").val(),
            pass_user: \$(\"#pass_user\").val(),
            type_request: \"login_user\"
          }
        }).done(function(data){
            result=JSON.parse(data);
            console.log(result.result);
            if(result.result==\"true\"){
              setTimeout(function(){document.location.href = result.page;},500);
            }
        });
      });
    });
  </script>
  <hr>
  <footer>
      
    </footer>
</body>

</html>





";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 31,  19 => 1,);
    }
}
