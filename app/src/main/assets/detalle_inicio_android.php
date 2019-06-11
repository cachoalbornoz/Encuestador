<?php
session_start();

require('../accesorios/accesos_bd.php');

$con=conectar();
					
$tabla_encuestas = mysqli_query($con, "select * from encuestas where estado = 1");

if($registro_encuestas = mysqli_fetch_array($tabla_encuestas, MYSQL_BOTH)){

    $id_encuesta = $_SESSION['id_encuesta']	= $registro_encuestas[0];

    $_SESSION['id_encuesta']= $registro_encuestas[0];
    $_SESSION['nombre']     = $registro_encuestas[1];
    $tabla_respuestas       = mysqli_query($con, "select count(id_pregunta) from preguntas where id_encuesta = $id_encuesta");
    $registro_preguntas     = mysqli_fetch_array($tabla_respuestas, MYSQL_BOTH);
    $_SESSION['cant_preg']  = $registro_preguntas[0];				
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SgeerMovil</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="mi_estilo.css" rel="stylesheet" type="text/css"/>
    <link href="jquery.mobile.icons.min.css" rel="stylesheet" type="text/css"/>

    <script src="jquery.js" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).bind('mobileinit',function(){
            $.mobile.changePage.defaults.changeHash = false;
            $.mobile.hashListeningEnabled = false;
            $.mobile.pushStateEnabled = false;
        });

    </script>

    <link href="jquery.mobile-1.4.5.css" rel="stylesheet" type="text/css"/>
    <script src="jquery.mobile-1.4.5.js" type="text/javascript"></script>

    <style>
        .ui-page { -webkit-backface-visibility: hidden; }
    </style>
</head>


<body>
    
<form id="encuesta" onreset="beforeReset()" data-exclude-selector="">  
    
    <div data-role="page" id="pagina0">        
        <div data-role="header" style="text-align: center">
            <h5>&COPY; SGE-V 1.1</h5>
            <label>Pregunta 0 de <?php echo $_SESSION['cant_preg'] ?></label>          
        </div>        
        <div data-role="main">
            <div class="ui-content"> 
                <div data-role="fieldcontain">
                    <label for="usuario">USUARIO</label>
                    <input type="range" name="usuario" id="usuario" value="1" min="1" max="20" class="exclude" />
                </div>                
                       
                <div data-role="fieldcontain">
                    <label for="totales">CONTADOR DE ENCUESTAS</label>
                    <input type="number" pattern="[0-9]" name="totales" id="totales" value="0" class="exclude" readonly="readonly" />
                </div>                    
                
                <input type="hidden" name="id_encuesta" id="id_encuesta" value="<?php echo $_SESSION['id_encuesta'] ?>" class="exclude"/>               
            </div>            
        </div>        
        <div data-role="footer" data-position="fixed">           
            <div data-role="navbar">
                <ul>
                    <li><a href="#" onClick="salida()" data-iconpos="bottom" data-icon="power">Salir</a></li>
                    <li><a href="#ayuda" data-iconpos="bottom" data-icon="info" data-rel="dialog">Ayuda</a></li>
                    <li><a href="#pagina1" data-iconpos="bottom" data-icon="arrow-r">Iniciar</a></li>
                    <li><a href="#acerca" data-iconpos="bottom" data-icon="star"  data-rel="dialog">Acerca de</a></li>
                </ul>
            </div>            
        </div>        
    </div>    
    
    <div data-role="page" id="acerca">
        <div data-role="header" style="text-align: center">
            <h5>&COPY; SGE-V 1.1</h5>
            <label>Datos personales</label>
        </div>
        <div data-role="content" class="ui-content">           
            <p>Guillermo Albornoz </p>
            <p>cachoalbornoz@gmail.com</p>   
        </div>
        <div data-role="footer">
            <div style=" text-align: center">
                <a href="#" data-role="button" data-icon="phone" data-mini="true" data-iconpos="notext">Tel</a>
                343 4586951 / Paraná E.Rios 
            </div>            
        </div>
    </div>
    
    <div data-role="page" id="ayuda">
        <div data-role="header" style="text-align: center">
            <h5>&COPY; SGE-V 1.1</h5>
            <label>Consideraciones Generales</label>     
        </div>
        <div data-role="content" class="ui-content">           
            <h4>Lea por favor</h4>
            <ul>
                <li><strong>Verificar que el GPS esté activo</strong>. Las respuestas siempre se almacenarán, pero si se encuentra desactivado, no almacenará la georreferencia.</li>
                <li><strong>Comprobar Nro Usuario Asignado</strong>. Al finalizar c/u de las encuestas, puede modificar esta variable, y relacionar las respuestas a otro Usuario.</li>
            </ul>
        </div>
        <div data-role="footer">
            
        </div>
    </div>   
    
    <?php
    $id_encuesta = $_SESSION['id_encuesta']; 
    $seleccion_preguntas = mysqli_query($con,"select * from preguntas where id_encuesta = $id_encuesta order by nro asc");
    while($registro_preguntas = mysqli_fetch_array($seleccion_preguntas, MYSQL_BOTH)){

    $id_pregunta = $registro_preguntas[0];
    $nro_pregunta= $registro_preguntas[4];

    $seleccion_respuestas = mysqli_query($con,"select * from respuestas where id_pregunta = $id_pregunta");
    $tipo_respuesta = $registro_preguntas[2];
    $cant_respuestas= mysqli_num_rows($seleccion_respuestas);
    ?>   
    
    <div data-role="page" id="pagina<?php echo $nro_pregunta ?>">         
        <div data-role="header" style="text-align: center">            
            <h5>&COPY; SGE-V 1.1</h5>
            <label>Pregunta <?php echo $nro_pregunta ?> de <?php echo $_SESSION['cant_preg'] ?></label>            
        </div>
        
        <div data-role="main">
            <div class="ui-content">
                <label for="<?php echo $nro_pregunta?>"><?php echo $nro_pregunta?>- <?php echo $registro_preguntas[3];?></label>
                <?php
                $id = $nro_pregunta;
                switch ($tipo_respuesta){
                ///// RESPUESTAS ABIERTAS 
                case 1:
                ?>
                <input id="<?php echo $id ?>" name="<?php echo $id ?>" type="text" value="-" data-clear-btn="true"/>
                <?php
                break;
                ///// RESPUESTAS OPCIONES MULTIPLES	
                case 2:                                                       
                if($cant_respuestas > 4){
                ?>    
                <select id="<?php echo $id ?>" name="<?php echo $id ?>" data-native-menu="false">
                   <option value="0">...</option>
                    <?php
                    while ($registro_respuestas = mysqli_fetch_array($seleccion_respuestas, MYSQL_BOTH)){
                        echo "<option value=\"".$registro_respuestas[0]."\">".$registro_respuestas[2]."</option>\n";
                    }
                    ?>
                </select>
 
                <?php  
                }else{
                   ?>
                    <fieldset data-role="controlgroup" data-theme="b" data-type="vertical">
                    <?php
                    $cont = 1;
                    while ($registro_respuestas = mysqli_fetch_array($seleccion_respuestas, MYSQL_BOTH)){
                    ?>
                       <input type="radio" id="<?php echo $id ?><?php echo $registro_respuestas[0]?>" name="opcion<?php echo $id ?>" value="<?php echo $registro_respuestas[0]?>" onclick="asignar(<?php echo $id ?>,<?php echo $registro_respuestas[0]?>)"/>
                       <label for="<?php echo $id ?><?php echo $registro_respuestas[0]?>"><?php echo $registro_respuestas[2]?></label>
                    <?php  
                    $cont ++;
                    }
                    ?>                 
                    </fieldset>  
                    <input type="hidden" id="<?php echo $id ?>" value="0">
                <?php    
                }
                break;
                ///// RESPUESTAS MB/B/RB/RM/M/MM/NsNc	
                case 3:
                $seleccion_respuestas = mysqli_query($con,"select * from tabla_opinion");
                ?>
                <select id="<?php echo $id ?>" name="<?php echo $id ?>" data-native-menu="false">
                    <option value="0">...</option>
                    <?php
                    while ($registro_respuestas = mysqli_fetch_array($seleccion_respuestas, MYSQL_BOTH)){
                        echo "<option value=\"".$registro_respuestas[0]."\">".$registro_respuestas[1]."</option>\n";
                    }
                    ?>
                </select>
                <?php
                break;
                ///// RESPUESTAS TABLA CRUZADA	
                case 4:
                ?>
                <div style="float:left;">
                    <select id="<?php echo 'F'.$id ?>" name="<?php echo 'F'.$id ?>" data-native-menu="false">
                        <option value="0">...</option>
                        <?php
                        $seleccion_respuestas_filas = mysqli_query($con,"select * from respuestas where id_pregunta = $id_pregunta and fila = 1");
                        while ($registro_respuestas_filas = mysqli_fetch_array($seleccion_respuestas_filas, MYSQL_BOTH)){
                            echo "<option value=\"". $registro_respuestas_filas[0]."\">".$registro_respuestas_filas[2]."</option>\n";
                        }
                        ?>
                    </select>
                    <select id="<?php echo 'C'.$id ?>" name="<?php echo 'C'.$id ?>" size="1" data-native-menu="false">
                        <option value="0">...</option>
                        <?php
                        $seleccion_respuestas_columnas = mysqli_query($con,"select * from respuestas where id_pregunta = $id_pregunta and fila = 2");
                        while ($registro_respuestas_columnas = mysqli_fetch_array($seleccion_respuestas_columnas, MYSQL_BOTH)){
                            echo "<option value=\"".$registro_respuestas_columnas[0]."\">".$registro_respuestas_columnas[2]."</option>\n";
                        }
                        ?>
                    </select>
                </div>
                <div align="center" style="float:left; width:10%">
                    <a href="javascript:void(0)" onclick="agrega_valores('<?php echo 'F'.$id ?>','<?php echo 'C'.$id ?>','<?php echo $id ?>')">(Guarda)</a>
                </div>      
                <div style="float:left;">
                    <select name="<?php echo 'F_'.$id ?>" id="<?php echo 'F_'.$id ?>" size="2">
                        <option selected disabled>...</option>
                    </select>

                    <select name="<?php echo 'C_'.$id ?>" id="<?php echo 'C_'.$id ?>" size="2">
                        <option selected disabled>...</option>
                    </select>
                </div>
                <div align="center" style="float:left; width:10%">
                    <a href="javascript:void(0)" onclick="elimina_valores('<?php echo 'F_'.$id ?>','<?php echo 'C_'.$id ?>')">(Borra)</a>
                </div>
                <?php				
                break;	
                ///// RESPUESTAS EDAD 
                case 5:
                ?>
                <select id="<?php echo $id ?>" name="<?php echo $id ?>" data-native-menu="false">
                    <option value="0">...</option>
                    <?php 
                    $i = 16;
                    while($i < 81){ ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php
                    $i ++;
                    }
                    ?>
                </select>
                <?php
                break;
                ///// RESPUESTAS SEXO
                case 6:
                ?>
                <select id="<?php echo $id ?>" name="<?php echo $id ?>" data-native-menu="false">
                    <option value="0">...</option>
                    <option value="1">MASCULINO</option>
                    <option value="2">FEMENINO</option>
                </select>
                <?php
                break;
                ///// RESPUESTAS INSTRUCCION
                case 7:
                $seleccion_respuestas = mysqli_query($con,"select * from tabla_nivel_instruccion");
                ?>
                <select id="<?php echo $id ?>" name="<?php echo $id ?>" data-native-menu="false">
                    <option value="0">...</option>
                    <?php
                    while ($registro_respuestas = mysqli_fetch_array($seleccion_respuestas, MYSQL_BOTH)){
                        echo "<option value=\"".$registro_respuestas[0]."\">".$registro_respuestas[1]."</option>\n";
                    }
                    ?>
                </select>
                <?php
                break;
                ///// RESPUESTAS OCUPACION
                case 8:
                $seleccion_respuestas = mysqli_query($con,"select * from tabla_ocupacion");
                ?>
                <select id="<?php echo $id ?>" name="<?php echo $id ?>" data-native-menu="false">
                    <option value="0">...</option>
                    <?php
                    while ($registro_respuestas = mysqli_fetch_array($seleccion_respuestas, MYSQL_BOTH)){
                        echo "<option value=\"".$registro_respuestas[0]."\">".$registro_respuestas[1]."</option>\n";
                    }
                    ?>
                </select>
                <?php
                break;			
                }
                ?>                     
            </div>   
            
        </div>
        
        <div data-role="footer" data-position="fixed">     
            <div data-role="navbar">
                <ul>
                    <li><input value="Limpiar" type="reset" form="encuesta" data-exclude-selector=".exclude" onclick="initiateReset()" data-iconpos="top" data-icon="refresh"></li>
                    <li><a href="#pagina<?php echo $nro_pregunta-1 ?>" data-iconpos="bottom" data-icon="arrow-l">Atras</a></li>
                    <li><a href="#pagina<?php echo $nro_pregunta+1 ?>" data-iconpos="bottom" data-icon="arrow-r">Adelante</a></li>
                    <li><a href="#pagina<?php echo $_SESSION['cant_preg']+1 ?>" data-iconpos="bottom" data-icon="check">Finalizar</a></li>
                </ul>
            </div>                        
        </div>        
    </div>    
    
<?php
}            
?>   
    
<div data-role="page" id="pagina<?php echo $_SESSION['cant_preg']+1 ?>">    
    <div data-role="header" style="text-align: center">
        <h5>&COPY; SGE-V 1.1</h5>
        <label>Fin de la Encuesta</label>
    </div>

    <div data-role="main"> 
        <div class="ui-content">
            <div id="estado" data-role="popup"> </div>
        </div>
    </div>
    <div data-role="footer" data-position="fixed">        
        <div data-role="navbar">
            <ul>
                <li><button id="limpiar" type="reset" form="encuesta" data-exclude-selector=".exclude" onclick="initiateReset()" data-iconpos="bottom" data-icon="refresh">Limpiar</button></li>
                <li><a href="#pagina<?php echo $nro_pregunta-1 ?>" data-iconpos="bottom" data-icon="arrow-l">Atras</a></li>
                <li><a href="#" data-iconpos="bottom" data-icon="check" onclick="no_vacio(<?php echo $_SESSION['cant_preg'] ?>)">Guardar</a></li>
                <li><a href="#" onClick="salida()" data-iconpos="bottom" data-icon="power">Salir</a></li>
            </ul>
	</div>     
    </div>
</div>
</form>
    
<script type="text/javascript">
    
    function Guardar_Android(texto) {
        
        $.mobile.changePage($("#pagina1"));
        document.getElementById('totales').value = parseFloat(document.getElementById('totales').value) + 1;
        
        
        Android.guardar(texto);
    }
    
    function salida() {
        Android.salir();
    }
    
    function asignar(id,valor){
        document.getElementById(id).value = valor;
    }
    
    function obtenerDatos(cant_preg) {
        var cadena = ''; 
        var id_encuesta = document.getElementById('id_encuesta').value;
        var nro_usuario = document.getElementById('usuario').value;

        for (i=1; i <= cant_preg ; i ++){
            var nro = document.getElementById(i);
            if(typeof nro !== 'undefined' && nro !== null) {

                var nro = document.getElementById(i).value ;

            }else{
                var sel_f 	= document.getElementById('F_' + i); // Buscar valores en filas
                var sel_c 	= document.getElementById('C_' + i); // Buscar valores en columnas

                texto = '';

                for(x=0; x < sel_f.children.length; x++){
                    var child_f = sel_f.children[x];
                    var child_c = sel_c.children[x];

                    if(child_f.value > 0){
                        var texto = texto + child_f.value + '_' + child_c.value + '.' ;
                    }
                }
                var texto = texto.substring(0, texto.length-1);
                var nro = texto;
            }		
            cadena = cadena + nro + ';' ;
        }	
        return id_encuesta + ';' + nro_usuario + ';' + cadena ;
    } 

    function no_vacio(cant_preg){	

        var error = 0;
        for (i=1; i <= cant_preg ; i ++){
            var respuesta = document.getElementById(i);
            if(typeof respuesta !== 'undefined' && respuesta !== null) {
                if(document.getElementById(i).value == 0){
                    $.mobile.changePage($("#pagina" + i));
                    error = 1;
                    break;
                }
            }else{
                var sel_f 	= document.getElementById('F_' + i); // Buscar valores en filas
                var sel_c 	= document.getElementById('C_' + i); // Buscar valores en columnas

                if(sel_f.children.length == 0 & sel_c.children.length == 0){
                    
                    var html = "<p> <a href='#pagina"+ i +"'>Respuesta " + i + " sin responder</a> </p>";
                    
                    $("#estado").html(html);
                    $("#estado").popup("open");
                    error = 1;
                    break;	
                }			
            }
        }
        //	
        if(error == 0){
            var datos = obtenerDatos(cant_preg);
            Guardar_Android(datos);
        }
    }  
    
</script>

<script>

var dataKey = 'data-exclude-selector';

function initiateReset(e) {
    e = e || window.event;

    var button = e.target,
        form = button.form,
        excludeSelector = button.getAttribute(dataKey);

    form.setAttribute(dataKey, excludeSelector);
}

function beforeReset(e) {
    e = e || window.event;

    var form = e.target,
        excludeSelector = form.getAttribute(dataKey),
        elements = form.querySelectorAll(excludeSelector),
        parents = [],
        siblings = [],
        len = elements.length,
        i, e, p, s;

    for (i = 0; i < len; i++) {
        el = elements[i];
        parents.push(p = el.parentNode);
        siblings.push(s = el.nextSibling);
        p.removeChild(el);
    }

    setTimeout(function() {
        for (var j = 0; j < len; j++){
            if (siblings[j]){
                parents[j].insertBefore(elements[j], siblings[j]);
            }else{
                parents[j].appendChild(elements[j]);
            }
        }
    });
}

</script>  

</body>
</html>
<?php mysqli_close($con); ?>