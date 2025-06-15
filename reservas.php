<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <title> Las Regueras Desktop - Reservas </title>
        <link rel="icon" href="multimedia/imagenes/favicon.ico">

        <meta name ="author" content ="Daniel López Fdez" />
        <meta name ="description" content ="Indice de la pagina web" />
        <meta name ="keywords" content ="hoteles, vacaciones, turismo, restaurantes" />
        <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    
        <link rel="stylesheet" type="text/css" href="estilo/estilos.css" />

    </head>

    <body>

        <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            session_start();
            require_once "php/recursos.php";
            class Usuario {

                private $db;
                private $dbServer;
                private $dbUser;
                private $dbPass;
                private $dbname;

                private $recursos;

                public function __construct(){
                    $this->dbServer = "localhost";
                    $this->dbUser = "DBUSER2025";
                    $this->dbPass = "DBPWD2025";
                    $this->dbname = "reservas";
                    $this->recursos = new Recursos();
                }

                public function loadLogin(){
                    $html = "<section>";
                    $html.= "<form action='#' method='post' name='formulario' >";
                    $html.= "<h3>Inicia sesión</h3>";
                    $html.= "<section><h4>Email</h4><input type='text' name='email'></section>";
                    $html.= "<section><h4>Contraseña</h4><input type='password' name='pass'></section>";
                    $html.= "<input type='submit' name='acceder' value='Acceder'><input type='submit' name='loadSignUp' value='Registrarse'>";
                    $html.= "</form></section>";
                        
                    echo $html;
                }

                public function procesarFormulario(){

                    $errorFormulario = false;

                    if(isset($_POST["loadSignUp"])){
                        $this->loadSignUp();
                    }else if(isset($_POST["registrar"])){
                        $this->signUp();
                    }else if(isset($_POST["loadLogin"])){
                        $this->loadLogin();
                    }else if(isset($_POST["acceder"])){
                        $this->login();
                    }else if(isset($_POST["recursos"])){
                        $this->recursos->loadRecursos();
                    }else if(isset($_POST["mis_recursos"])){
                        $this->recursos->loadMisRecursos();
                    }else if(isset($_POST["reservar"])){
                        $recursoId = $_POST["reservar"];
                        $this->recursos->seleccionarPlazas($recursoId);
                    }else if(isset($_POST["actualizar"])){
                        $recursoId = $_POST["actualizar"];
                        $plazas = $_POST["plazas"];
                        $this->recursos->reservarRecurso($recursoId, $plazas);
                    }else if(isset($_POST["confirmarReserva"])){
                        $recursoId = $_POST["confirmarReserva"];
                        $plazas = $_POST["plazasSeleccionadas"];
                        $this->recursos->confirmarReserva($recursoId, $plazas);
                    }else if(isset($_POST["cancelar"])){
                        $recursoId = $_POST["cancelar"];
                        $this->recursos->cancelarReserva($recursoId);
                    }
                }

                public function loadSignUp(){
                    
                    $errorNombre = "";
                    $errorEmail = "";
                    $errorPass = "";
                    $errorPassRepetida = "";

                    $html = "<section>";
                    $html.= "<form action='#' method='post' name='formulario' >";
                    $html.= "<h3>Registrarse</h3>";
                    $html.= "<section><h3>Nombre</h3><input type='text' name='name'><span>" . $errorNombre . "</span></section>";
                    $html.= "<section><h3>Email</h3><input type='text' name='email'><span>" . $errorEmail . "</span></section>";
                    $html.= "<section><h3>Contraseña</h3><input type='password' name='pass'>". $errorPass ."</section>";
                    $html.= "<section><h3>Repita contraseña</h3><input type='password' name='passRep'>". $errorPassRepetida ."</section>";
                    $html.= "<input type='submit' name='registrar' value='Registrarse'>";
                    $html.= "</form></section>";

                    echo $html;
                }

                public function login(){

                    $errorEmail = "";
                    $errorPass = "";
                    $errorFormulario = "";

                    if($_POST["email"] == ""){
                        $errorEmail = " * El email es obligatorio";
                        $errorFormulario = true;                            
                    }
                    
                    if($_POST["pass"] == ""){
                        $errorPass = " * La contraseña es obligatoria";
                        $errorFormulario = true;     
                    }

                    if($errorFormulario == true){
                        $html = "<section>";
                        $html.= "<form action='#' method='post' name='formulario' >";
                        $html.= "<h3>Inicia sesión</h3>";
                        $html.= "<section><h3>Email</h3><input type='text' name='email'><span>" . $errorEmail . "</span></section>";
                        $html.= "<section><h3>Contraseña</h3><input type='password' name='pass'>". $errorPass ."</section>";
                        $html.= "<input type='submit' name='acceder' value='Acceder'><input type='submit' name='loadSignUp' value='Registrarse'>";
                        $html.= "</form></section>";
                        
                        echo $html;
                    }else{

                        $userEmail = $_POST["email"];
                        $userPass = $_POST["pass"];

                        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);
                            
                        if($this->db->connect_errno){
                            echo "Error de conexión: " . $this->db->connect_error;
                        }

                        $prepQuery = $this->db->prepare("SELECT * FROM usuarios WHERE email=? AND password=?");
                        $prepQuery->bind_param("ss", $userEmail, $userPass);
                        $prepQuery->execute();
                        $result = $prepQuery->get_result();

                        if($result -> num_rows == 0){
                            echo "No se ha encontrado al usuario";
                            $this->loadSignUp();
                        }else{
                            $info = $result->fetch_assoc();
                            $_SESSION["user_id"] = $info["id"];
                            $this->recursos->loadRecursos();
                        }
                    }

                }

                public function signUp(){

                    $errorNombre = "";
                    $errorEmail = "";
                    $errorPass = "";
                    $errorPassRepetida = "";

                    $errorFormulario = "";

                    if($_POST["name"] == ""){
                        $errorNombre = " * El nombre es obligatorio";
                        $errorFormulario = true;                            
                    }
                    if($_POST["email"] == ""){
                        $errorEmail = " * El email es obligatorio";
                        $errorFormulario = true;                            
                    }
                    if($_POST["pass"] == ""){
                        $errorPass = " * La contraseña es obligatoria";
                        $errorFormulario = true;     
                    }
                    if($_POST["passRep"] == ""){
                        $errorEmail = " * Escriba otra vez la contraseña";
                        $errorFormulario = true;                            
                    }
                    if($_POST["passRep"] != $_POST["pass"]){
                        $errorEmail = " * Las contraseñas no coinciden";
                        $errorFormulario = true;                            
                    }

                    if($errorFormulario == true){
                        $html = "<section>";
                        $html.= "<form action='#' method='post' name='formulario' >";
                        $html.= "<h3>Registrarse</h3>";
                        $html.= "<section><h3>Nombre</h3><input type='text' name='name'><span>" . $errorNombre . "</span></section>";
                        $html.= "<section><h3>Email</h3><input type='text' name='email'><span>" . $errorEmail . "</span></section>";
                        $html.= "<section><h3>Contraseña</h3><input type='password' name='pass'>". $errorPass ."</section>";
                        $html.= "<section><h3>Repita contraseña</h3><input type='password' name='passRep'>". $errorPassRepetida ."</section>";
                        $html.= "<input type='submit' name='registrar' value='Registrarse'>";
                        $html.= "</form></section>";

                        echo $html;

                    }else{

                        $name = $_POST["name"];
                        $email = $_POST["email"];
                        $pass = $_POST["pass"];
                        $registro = new DateTime();

                        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);
                           
                        if($this->db->connect_errno){
                            echo "Error de conexión: " . $this->db->connect_error;
                        }

                        $prepQuery = $this->db->prepare("SELECT * FROM usuarios WHERE email=?");
                        $prepQuery->bind_param("s", $email);
                        $prepQuery->execute();
                        $result = $prepQuery->get_result();

                        if($result -> num_rows > 0){
                            echo "El correo electronico ya está registrado";
                            $this->loadSignUp();
                        }else{
                            $preQuery = $this->db->prepare("INSERT INTO usuarios (name, email, password, registered_date) VALUES (?,?,?,?)");
                        
                            $fecha = $registro->format("Y-m-d H:i:s");
                        
                            $preQuery->bind_param("ssss", $name, $email, $pass, $fecha);
                            $preQuery->execute();
                            $result = $preQuery->affected_rows;
                            $preQuery->close();
                            if($result == 0){
                                echo "<h4> Ha ocurrido un error <h4>";
                            }else{
                                $this->loadLogin();
                            }
                        }
                    }                    

                }
            }

        ?>   



        <header>
            <h1><a href="index.html"> Las Regueras</a></h1>
            <nav>
                <a href="index.html">Inicio</a>
                <a href="gastronomia.html">Gastronomia</a>
                <a href="meteorologia.html">Meteorología</a>
                <a href="rutas.html">Rutas</a>
                <a class="active" href="reservas.php"><span class="active">Reservas</span></a>
                <a href="juego.html">Juego</a>
                <a href="ayuda.html">Ayuda</a>
            </nav>
        </header>

        <p>Estás en: <a href="index.html">Inicio</a> >> Reservas </p>

        <main>

            <section>
                <h2>Reservas</h2>
            </section>
            
            <?php
                $usuario = new Usuario();
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $usuario->procesarFormulario();
                } else {
                    $usuario->loadLogin();
                }
            ?>

        </main>
    </body>
</html>