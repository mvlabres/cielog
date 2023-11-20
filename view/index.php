<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php');

if($_SESSION['name'] == null){
	header('LOCATION:../index.php');
}

$content = "quickActions.php";

if(isset($_POST['content'])) $conteudo = $_POST['content'];   
	
if(isset($_GET['content'])) {
    $content = $_GET['content'];
    if($content == 'logout') {
        session_unset();     
        session_destroy();  
        header('Location:../index.php');
    }
}

if($_GET['user'] && $_GET['user'] == 'success' ){
    successAlert('Senha alterada com sucesso!');
}

if(isset($_GET['action']) && $_GET['action'] == 'access-save'){
    successAlert('Registro salvo com sucesso!');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>CIELOG</title>
    
    <link rel="shortcut icon" href="../images/logo_sem_texto.ico">

    <!-- CSS references -->
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../custom-style.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <!-- JS scripts -->
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../utils.js"></script>  
    <script src="/jQuery-Mask-Plugin-master/"></script>
    <script src="../dist/js/sb-admin-2.js"></script> 
    <script src="../assets/js/jquery-1.11.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script src="../path/to/cdn/jquery.min.js"></script>
    <script src="../jquery.datetimepicker.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>

<body onload="init()">
    <div id="wrapper" class="schedule-body">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-brand">
                <img class="icon-menu-img" src="../images/menu-icon.png"  onmouseover="handleShowMenu()">
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="box-img-link">
                    <a href="index.php"><img class="img-link" src="../images/logo_sem_texto.png" /></a>
                </div>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="menu-break-text"><?=$_SESSION['name'] ?></span>
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="index.php?content=userChangePassword.php"><i class="fa fa-pencil fa-fw"></i> Alterar senha</a></li>
                        <li class="divider"></li>
                        <li><a href="index.php?content=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
           
           
            <div class="navbar-default sidebar vertical-menu" id="menu-nav-bar" role="navigation" >
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu" >
                        <li>
                            <a href="index.php">Home</a>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['access'] ?>>
                            <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i></i> Acesso </a>
                            <ul class="collapse nav nav-second-level" id="pageSubmenu2">
                                <li>
                                    <a href="index.php?content=employeeList.php">Colaborador</a>
                                </li>
                                <li>
                                    <a href="index.php?conteudo=driverList.php">Veículos</a>
                                </li>
                            </ul>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['register'] ?>>
                            <a href="#pageSubmenu3"  data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i></i> Cadastros</a>
                            <ul class="collapse nav nav-second-level" id="pageSubmenu3">
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_client'] ?>>
                                    <a href="index.php?content=newClient.php">Empresa cliente</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_employee'] ?>>
                                    <a href="index.php?content=employeeList.php">Colaborador</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_driver'] ?>>
                                    <a href="index.php?content=driverList.php">Motorista</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_vehicle_type'] ?>>
                                    <a href="index.php?content=newVehicleType.php">Tipo de veículo</a>
                                </li>
                                <li <?=$_SESSION['FUNCTION_ACCESS']['register_shipping_company'] ?>>
                                    <a href="index.php?content=newShippingCompany.php">Transportadora</a>
                                </li>
                            </ul>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['reports'] ?>>
                            <a href="#pageSubmenu1" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Relatórios</a>
                            <ul class="collapse nav nav-second-level" id="pageSubmenu1">
                                <li>
                                    <a href="index.php?content=driverAccessList.php">Acesso veículos</a>
                                </li>
                                <li>
                                    <a href="index.php?content=employeeAccessList.php">Acesso colaboradores</a>
                                </li>
                            </ul>
                        </li>
                        <li <?=$_SESSION['FUNCTION_ACCESS']['users'] ?>>
                            <a href="#pageSubmenu4" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Usuários</a>
                            <ul class="collapse nav nav-second-level" id="pageSubmenu4">
                                <li>
                                    <a href="index.php?content=newUser.php">Novo</a>
                                </li>
                                <li>
                                    <a href="index.php?content=userList.php">Pesquisar</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-wrapper" onmouseover="handleHideMenu()" >
            <?php
                include($content);
            ?>
        </div>
        <a class="dev-fixed-bottom" href="http://labsoft.tech/" target="_blank" ><p class="text-muted">&nbsp Desenvolvido por <span class="text-primary" style="font-size:1.2em"><b>LAB</b>soft</span></p></a>
        
    </div>

    <script>
        jQuery(function($){
            $('.telefone').mask('(00)0000-0000');
            //$('.placa').mask('AAA-0000');
            $('.celular').mask('(00)00000-0000');
            $('.cnpj').mask('00.000.000/0000-00');
            $("#data_final").mask("99/99/9999");
            $('.cpf').mask('000.000.000-00');
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true
            });
        });

        $(".alert").fadeTo(5000, 10).slideUp(500, function(){
            $(this).remove(); 
        });


        $(function () {
            $('#datetimepicker1').datetimepicker();
        });

        $('#myModal').on('shown.bs.modal', function () {
            $('#myInput').trigger('focus')
        })

        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
    </body>

</html>
