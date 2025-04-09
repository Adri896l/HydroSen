<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo-03.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Patua+One&display=swap" rel="stylesheet">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <title>HydroSen</title>

    <link rel="stylesheet" href="views/css/estilos.css">
    <link rel="shortcut icon" href="views/images/logo-03.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/41bcea2ae3.js" crossorigin="anonymous"></script>

</head>
<body>

    <header id="header">
        <div class="container__header">
           
            <div class="container__nav">
                <nav id="nav">
                    <ul>
                        <li><a href="views/login.php" class="select">Iniciar Sesion</a></li>
                       
                    </ul>
                </nav>
                <div class="btn__menu" id="btn_menu"><i class="fas fa-bars"></i></div>
            </div>
        </div>
    </header>

    <div class="container_all" id="container__all">
        <div class="cover">

            <!--INICIO WAVE-->

                <div class="bg_color"></div>
                <div class="wave w1"></div>
                <div class="wave w2"></div>

            <!--FINAL WAVE-->

            <div class="container__cover">
                <div class="container__info">
                    <h1>Soluciones</h1>
                    <h2 class="animate__animated animate__heartBeat">HydroSen</h2>
                    <p>Descubre la forma más inteligente de monitorear tu consumo de agua y detectar fugas antes de que se conviertan en un problema. Con nuestra tecnología innovadora, recibirás alertas en tiempo real y podrás tomar acción inmediata para evitar desperdicios y costos innecesarios.</p>
                   
                   <center>
                    <input type="button" value="Comenzar">
                   </center>
                  
                  
                </div>
                <div class="container__vector">
                    <img src="views/images/logo-03.png" alt="">
                    
                </div>
               
             
            </div>
           
        </div>
       
      



        



</div>

    <script >
        window.onscroll = function(){

scroll = document.documentElement.scrollTop;

header = document.getElementById("header");

if (scroll > 20){
    header.classList.add('nav_mod');
}else if (scroll < 20){
    header.classList.remove('nav_mod');
}

}

document.getElementById("btn_menu").addEventListener("click", mostrar_menu);

menu = document.getElementById("header");
body = document.getElementById("container__all");
nav = document.getElementById("nav");

function mostrar_menu(){

body.classList.toggle('move_content');
menu.classList.toggle('move_content');
nav.classList.toggle('move_nav');
}

window.addEventListener("resize", function(){

if (window.innerWidth > 760)  {
    body.classList.remove('move_content');
    menu.classList.remove('move_content');
    nav.classList.remove('move_nav');
}

});
    </script>
    <script src="https://widget.cxgenie.ai/widget.js" data-aid="3ec9637d-a92e-47ac-a4b7-0b62bb908bbc"
		
        data-lang="en"></script>
</body>
</html>