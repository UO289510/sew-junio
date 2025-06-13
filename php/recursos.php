<?php
class Recursos{
    
    private $db;
    private $dbServer;
    private $dbUser;
    private $dbPass;
    private $dbname;

    private $userId;

    public function __construct(){
        $this->dbServer = "localhost";
        $this->dbUser = "DBUSER2025";
        $this->dbPass = "DBPWD2025";
        $this->dbname = "reservas";
    }

    public function loadRecursos(){

        $html = "<section>";

        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);

        if($this->db->connect_errno){
            echo "Error de conexión: " . $this->db->connect_error;
        }

        $result = $this->db->query("SELECT * FROM recursos");

        $html = "<section> <h3>Recursos</h3>";

        $html.="<form action='#' method='post' name='formulario' ><input type='submit' name='mis_recursos' value='Mis Recursos'></form>";

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                $html .= "<section>";

                $html .= "<h3>" . $row["nombre"] . "</h3>";

                $prepQuery = $this->db->prepare("SELECT * FROM categoria WHERE id=?");
                $prepQuery->bind_param("i", $row["categoria_id"]);
                $prepQuery->execute();
                $categoria = $prepQuery->get_result();
                $nombreCategoria = $categoria->fetch_assoc();

                $html .= "<h4>" . $nombreCategoria["nombre"] . " - Plazas: " . $row["plazas"] . " - " . $row["precio"] . " € </h4>";
                $html .= "<p>" . $row["descripcion"] . "</p>";
                $html .= "<p>Fecha inicio: " . $row["fecha_inicio"] . "</p>";
                $html .= "<p>Fecha fin: " . $row["fecha_fin"] . "</p>";

                $fecha_inicio = new DateTime($row["fecha_inicio"]);
                $fecha_fin = new DateTime($row["fecha_fin"]);
                $intervalo = $fecha_fin->diff($fecha_inicio);

                if($intervalo->days > 0){
                    $html .= "<p>Duración total: " . $intervalo->days . " días</p>";
                }else if($intervalo->h > 0){
                    $html .= "<p>Duración total: " . $intervalo->h . " horas</p>";
                }

                $html .= "<form action='#' method='post'>";
                $html .= "<button type='submit' name='reservar' value='". $row["id"] ."'> Reservar </button>";
                $html .= "</form>";

                $html .= "</section>";
            }
        }
        echo $html;
    }

    public function loadMisRecursos(){

        $userId = $_SESSION["user_id"];

        $html = "<section>";

        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);

        if($this->db->connect_errno){
            echo "Error de conexión: " . $this->db->connect_error;
        }

        $prepQuery = $this->db->prepare("SELECT * FROM mis_reservas WHERE usuario_id=?");
        $prepQuery->bind_param("i", $userId);
        $prepQuery->execute();
        $result = $prepQuery->get_result();

        $total=0;

        $html = "<section> <h3>Mis Recursos</h3>";

        $html.="<form action='#' method='post' name='formulario' ><input type='submit' name='recursos' value='Volver a recursos'></form>";

        $html.="<table><thead><tr><th scope='col'>Recurso</th><th scope='col'>Fechas</th><th scope='col'>Plazas</th><th scope='col'>Total</th><th scope='col'>Estado</th><th scope='col'>Operaciones</th></tr></thead>";
        $html.="<tbody>";
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                $html.="<tr>";
                $prepQuery = $this->db->prepare("SELECT * FROM recursos WHERE id=?");
                $prepQuery->bind_param("i", $row["recurso_id"]);
                $prepQuery->execute();
                $resultadoNombreRecurso = $prepQuery->get_result();
                $nombreRecurso = $resultadoNombreRecurso->fetch_assoc();

                $html.="<td>" . $nombreRecurso['nombre'] . "</td>";
                $html.="<td>" . $row['fecha_reserva'] . "</td>";
                $html.="<td>" . $row['plazas'] . "</td>";
                $html.="<td>" . $row['total'] . "€ </td>";
                $html.="<td>" . $row['estado'] . "</td>";

                if($row['estado'] == "confirmada"){
                    $html.="<td> <form action='#' method='post'><button type='submit' name='cancelar' value=". $row["recurso_id"] .">Cancelar</button></form></td>";
                    $total += $row['total'];
                }else{
                    $html.="<td> Cancelada </td>";
                }
                $html.="</tr>";
            }
        }

        $html.= "<h3>Tu total: " . $total . " €</h3>";
        echo $html;        
    }

    public function seleccionarPlazas($recursoId){

        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);

        if($this->db->connect_errno){
            echo "Error de conexión: " . $this->db->connect_error;
        }

        $prepQuery = $this->db->prepare("SELECT * FROM recursos WHERE id=?");
        $prepQuery->bind_param("i", $recursoId);
        $prepQuery->execute();
        $result = $prepQuery->get_result();

        $data = $result->fetch_assoc();

        $_SESSION["recurso_id"] = $recursoId;

        $html = "<section> <h3> Seleccione el numero de plazas a reservar </h3>";
        $html.= "<form action='#' method='post'><input type='number' name='plazas' value='1' min=1 max=" . $data["plazas"] . ">";
        $html.= "<button type='submit' name='actualizar' value='" . $recursoId . "'>Obtener presupuesto </button>";
        $html.="</form>";
        $html.= "</section>";
        echo $html;
    }

    public function reservarRecurso($recursoId, $plazas){
        
        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);

        if($this->db->connect_errno){
            echo "Error de conexión: " . $this->db->connect_error;
        }

        $prepQuery = $this->db->prepare("SELECT * FROM recursos WHERE id=?");
        $prepQuery->bind_param("i", $recursoId);
        $prepQuery->execute();
        $result = $prepQuery->get_result()->fetch_assoc();
        
        $total = $result["precio"]*$plazas;


        $_POST["plazas"] = $plazas;

        $html = "<section>";
        $html.= "<h4> ¿Confirmar reserva? </h4>";
        $html.= "<form action='#' method='post' name='formulario'> ";
        $html.= "<p><input readonly type='number' name='plazasSeleccionadas' value='". $plazas ."'> plazas seleccionadas por un valor de ". $total ." € </p>";
        $html.= "<button type='submit' name='confirmarReserva' value='" . $recursoId . "'>CONFIRMAR</button>";
        $html.= "</form>";
        $html.="</section>";
        echo $html;
    }

    public function confirmarReserva($recursoId, $plazas){

        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);

        if($this->db->connect_errno){
            echo "Error de conexión: " . $this->db->connect_error;
        }

        $prepQuery = $this->db->prepare("SELECT * FROM recursos WHERE id=?");
        $prepQuery->bind_param("i", $recursoId);
        $prepQuery->execute();
        $result = $prepQuery->get_result()->fetch_assoc();

        $plazasOriginales = $result["plazas"];

        $user_id = $_SESSION["user_id"];

        $total = $result["precio"]*$plazas;

        $fechaReserva = new DateTime();
        $fecha = $fechaReserva->format("Y-m-d H:i:s");
        $estado = "confirmada";
        $prepQuery = $this->db->prepare("INSERT INTO `mis_reservas`(`usuario_id`, `recurso_id`, `fecha_reserva`, `plazas`, `total`, `estado`) VALUES (?,?,?,?,?,?)");
        $prepQuery->bind_param("iisiis", $user_id,$recursoId, $fecha, $plazas, $total, $estado);
        $prepQuery->execute();
        $result = $prepQuery->affected_rows;
        $prepQuery->close();

        if($result==0){
            echo "<h4>Ha ocurrido un error </h4>";
        }else{

            $plazasRestantes = $plazasOriginales-$plazas;
            $prepQuery = $this->db->prepare("UPDATE `recursos` SET `plazas`=? WHERE id=?");
            $prepQuery->bind_param("ii",$plazasRestantes, $recursoId);
            $prepQuery->execute();
            $result = $prepQuery->affected_rows;
            $prepQuery->close();

            if($result == 0){
                echo "<h4> Ha ocurrido un error </h4>";
            }else{
                $this->loadMisRecursos();
            }
        }
    }

    public function cancelarReserva($recursoId){

        $this->db = new mysqli($this->dbServer, $this->dbUser, $this->dbPass, $this->dbname);

        if($this->db->connect_errno){
            echo "Error de conexión: " . $this->db->connect_error;
        }

        $prepQuery = $this->db->prepare("SELECT * FROM recursos WHERE id=?");
        $prepQuery->bind_param("i", $recursoId);
        $prepQuery->execute();
        $result = $prepQuery->get_result();
        $prepQuery->close();
        $data = $result->fetch_assoc();

        $plazasOriginales = $data["plazas"];

        $prepQuery = $this->db->prepare("SELECT * FROM mis_reservas WHERE recurso_id=? AND usuario_id=?");
        $prepQuery->bind_param("ii", $recursoId, $_SESSION["user_id"]);
        $prepQuery->execute();
        $result = $prepQuery->get_result()->fetch_assoc();

        $plazasLiberadas = $result["plazas"];

        $plazasTotales = $plazasOriginales+$plazasLiberadas;

        $newState = "anulada";

        $prepQuery = $this->db->prepare("UPDATE mis_reservas SET estado=? WHERE recurso_id=? AND usuario_id=?");
        $prepQuery->bind_param("sii", $newState, $recursoId, $_SESSION["user_id"]);
        $prepQuery->execute();
        $result = $prepQuery->affected_rows;
        $prepQuery->close();

        if($result == 0){
            echo "<h4>Ha ocurrido un error</h4>";
        }else{

            $prepQuery = $this->db->prepare("UPDATE recursos SET plazas=? WHERE id=?");
            $prepQuery->bind_param("ii", $plazasTotales, $recursoId);
            $prepQuery->execute();
            $result = $prepQuery->affected_rows;
            $prepQuery->close();

            if($result == 0){
                echo "<h4>Ha ocurrido un error</h4>";
            }else{
                $this->loadMisRecursos();
            }
        }

    }
}

?>