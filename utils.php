<?php

    function errorAlert($msg){
        alert('alert-danger', $msg);
    }

    function successAlert($msg){
        alert('alert-success', $msg);
    }

    function warningAlert($msg){
      alert('alert-warning', $msg);
  }

  function alert($type, $msg){
    echo '<div class="alert '.$type.' alert-dismissible fade show" role="alert">
      '.$msg.'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>';
  }

  function setRotation(){

    $ROTATION_TYPES = ['07:00 às 19:00hr', '19:00 às 07:00hr'];

    $rotationAStart = date("H:i", strtotime('07:00'));
    $rotationAEnd = date("H:i", strtotime('19:00'));

    $timeNow = date("H:i");

    return ($timeNow >= $rotationAStart && $timeNow < $rotationAEnd ) ? $ROTATION_TYPES[0] : $ROTATION_TYPES[1];
  }


  // declaração de variáveis globais

  $GLOBAL_USER_TYPES = ['adm' => 'Administrador', 'client' => 'Cliente', 'operator' => 'Operador'];
  $GLOBAL_MONTHS = ['01'=>'JANEIRO', '02'=>'FEVEREIRO', '03'=>'MARÇO', '04'=>'ABRIL', '05'=>'MAIO', '06'=>'JUNHO', '07'=>'JULHO', '08'=>'AGOSTO', '09'=>'SETEMBRO', '10'=>'OUTUBRO', '11'=>'NOVEMBRO', '12'=>'DEZEMBRO'];

  $DRIVER_RECORD_TYPES = ['driver'=> 'Motorista', 'guest'=> 'Visitante', 'other'=> 'Outros'];
  $DRIVER_STATUS = ['active'=> 'Ativo', 'block'=> 'Bloqueado'];

  $OPERATION_TYPES = ['carga', 'descarga'];
?>