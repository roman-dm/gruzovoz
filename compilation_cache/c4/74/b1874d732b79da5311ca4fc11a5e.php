<?php

/* 404.html */
class __TwigTemplate_c474b1874d732b79da5311ca4fc11a5e extends Twig_Template
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
        <title>Страница ошибки 404</title>
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
\t\t\t\t\t\t\t<span class=\"go3d\">4</span>
\t\t\t\t\t\t\t<span class=\"go3d\">0</span>
\t\t\t\t\t\t\t<span class=\"go3d\">4</span>
\t\t\t\t\t\t\t<span class=\"go3d\">!</span>
\t\t\t\t\t\t</p>
\t\t\t\t\t\t<p class=\"error\">
\t\t\t\t\t\t\tПохоже, вы выбрали неправильный путь. Такой страницы не существует!<br/> Не волнуйтесь, время от времени, это случается с каждым из нас.
\t\t\t\t\t\t</p>
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
        return "404.html";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
