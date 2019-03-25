<?php

/* denied.html */
class __TwigTemplate_febb5c6804db8a7f6cea267fc11b7767 extends Twig_Template
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
<html class='no-js'>
\t<head>
        <title>Сраница ошибки 404</title>
        <meta charset='utf-8'>

    \t<link href='/app/views/assets/css/bootstrap.min.css' rel='stylesheet' media='screen'>
    \t<link href='/app/views/assets/css/bootstrap-responsive.min.css' rel='stylesheet' media='screen'>
       \t<link href='/app/views/assets/css/styles.css' rel='stylesheet'>
       \t<link href='/app/views/assets/css/404.css' rel='stylesheet'>

        <link href=\"//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css\" rel=\"stylesheet\">
\t</head>
\t<body>
\t\t<div class='container-fluid'>
            <div class='row-fluid'>
            \t<div class=\"comingcontainer\">
    \t\t\t\t<div class=\"checkbacksoon\">
\t\t            \t<p>
\t\t\t\t\t\t\t<span class=\"go3d\">Н</span>
\t\t\t\t\t\t\t<span class=\"go3d\">Е</span>
\t\t\t\t\t\t\t<span class=\"go3d\">Т</span>
\t\t\t\t\t\t\t<br/>
\t\t\t\t\t\t\t<span class=\"go3d\">Д</span>
\t\t\t\t\t\t\t<span class=\"go3d\">О</span>
\t\t\t\t\t\t\t<span class=\"go3d\">С</span>
\t\t\t\t\t\t\t<span class=\"go3d\">Т</span>
\t\t\t\t\t\t\t<span class=\"go3d\">У</span>
\t\t\t\t\t\t\t<span class=\"go3d\">П</span>
\t\t\t\t\t\t\t<span class=\"go3d\">А</span>
\t\t\t\t\t\t</p>
\t\t\t\t\t\t<div class='martop'>
\t\t\t\t\t\t<p class=\"error\">
\t\t\t\t\t\t\tПохоже, вы выбрали неправильный путь. <br/>Доступ на эту страницу Вам запрещен.
\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class='mid mar'>
\t\t\t\t\t\t\t<a href='/'><button class=\"btn btn-primary btn-large\"><i class=\"fa fa-home\"></i><span>Вернуться на главную</span></button></a>
\t\t\t\t\t\t</div>
            \t\t</div>
            \t</div>
            </div>
\t\t\t<hr>
\t        <footer>
\t            <p>Copyright © <a target='blank' href='http://www.alexkam.ru'>Alexkam</a> - All Rights Reserved | Moscow 2016</p>
\t        </footer>
       \t</div>
\t</body>
</html>";
    }

    public function getTemplateName()
    {
        return "denied.html";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
