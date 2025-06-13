class Juego {

    constructor(){

        this.preguntas = [
            {
                texto: "¿Donde se encuentra el concejo de Las Regueras?",
                opciones: ["Galicia", "Castilla y León", "Asturias", "Cantabria", "Navarra"],
                correcta: 2
            },{
                texto: "¿Cuál es la capital del concejo?",
                opciones: ["Oviedo", "Santullano", "Gijón", "Pravia", "Grado"],
                correcta: 1
            },{
                texto: "¿Cuál es el río principal de Las Regueras?",
                opciones: ["Nora", "Navia", "Guadalquivir", "Nalon", "Eo"],
                correcta: 3
            },{
                texto: "¿Cuál de estas NO es una especialidad gastronomica de la zona?",
                opciones: ["Huevo frito", "Afuega'l pitu", "Lechazo", "Carne gobernada", "Fabadas y potes varios"],
                correcta: 0
            },{
                texto: "La zona es conocida por sus ",
                opciones: ["Rios", "Paisanos", "Cementerios", "Huevos", "Carnes"],
                correcta: 4
            },{
                texto: "¿Cuál es el restaurante mejor valorado de la zona?",
                opciones: ["Casa Florinda", "La Manduca", "Casa Edelmiro", "Llagar Pitupalpote", "Manjares Astures"],
                correcta: 0
            },{
                texto: "¿En que localidad se encuentran las Termas Romanas?",
                opciones: ["Noreña", "Valduno", "Santullano", "Nora", "Grado"],
                correcta: 1
            },{
                texto: "¿Cuál es el gentilicio de Las Regueras?",
                opciones: ["Reguereses", "Escoberos", "Paisanitos", "Regaderos", "Regadeirenses"],
                correcta: 1
            },{
                texto: "¿Que patrimonio tiene más presencia en el concejo?",
                opciones: ["Arqueologico", "Natural", "Industrial", "Religioso", "Gastronomico"],
                correcta: 0
            },{
                texto: "¿Que otro rio transcurre por el concejo?",
                opciones: ["Nalón", "Eo", "Navia", "Nora", "Ebro"],
                correcta: 3
            }
        ];

        this.puntuacion = 0;
        this.index = 0;
        this.fallos = [];
        this.crearJuego();        
    }

    crearJuego(){
        
        var seccion = document.createElement("section");
        var formulario = document.createElement("form");
        this.botonSiguiente = document.createElement("button");
        this.botonSiguiente.textContent = "Siguiente";
        this.botonSiguiente.disabled = true;

        var titulo = document.createElement("h3");
        titulo.textContent = "¡Contesta a las preguntas!";
        
        seccion.appendChild(titulo);
        seccion.appendChild(formulario);
        seccion.appendChild(this.botonSiguiente);
        
        this.botonSiguiente.onclick = this.siguientePregunta.bind(this);

        document.querySelector("main").appendChild(seccion);
        this.mostrarPregunta();
    }

    mostrarPregunta(){

        var formulario = document.querySelector("form");
        formulario.innerHTML = (this.index+1)+". "+this.preguntas[this.index].texto;

        var i = 0;

        this.preguntas[this.index].opciones.forEach((opcion) => {
            var respuesta = document.createElement("input");
            respuesta.type = "radio";
            respuesta.name = "respuesta"+this.index;
            respuesta.value = i;
            respuesta.addEventListener("change", () => {
                this.botonSiguiente.disabled = false;
            });

            var label = document.createElement("label");
            label.textContent = opcion;
            
            formulario.appendChild(respuesta);
            formulario.appendChild(label);

            i++;

        });
        document.querySelector("form").append(formulario);
    }

    mostrarResultado(){

        var resultSection = document.createElement("section");

        var resultado = document.createElement("h3");
        resultado.textContent = "Has obtenido: " + this.puntuacion + " puntos";
        resultSection.appendChild(resultado);

        var mensaje = document.createElement("h4");

        if(this.puntuacion == 0){
            mensaje.textContent = "Intentalo otra vez";
        }else if(this.puntuacion > 0 && this.puntuacion < 5){
            mensaje.textContent = "Vaya, ¡más suerte la próxima vez!";
        }else if(this.puntuacion >= 5 && this.puntuacion < 7){
            mensaje.textContent = "¡Bien hecho!";
        }else if(this.puntuacion >= 7 && this.puntuacion < 9){
            mensaje.textContent = "¡Guau, increible, tremendo, muy bien!";
        }else if(this.puntuacion >= 9 && this.puntuacion <11){
            mensaje.textContent = "¡PLENO!";
        }

        resultSection.appendChild(mensaje);
        document.querySelector("main").appendChild(resultSection);

        for(var i=0; i<this.fallos.length; i++){
            var sectionFallo = document.createElement("section");
            var fallo = document.createElement("p");
            fallo.textContent = this.fallos[i];
            sectionFallo.append(fallo);
            document.querySelector("main").appendChild(sectionFallo);        
        }

    }

    siguientePregunta() {

        var respuestaSeleccionada = document.querySelector('input[name="respuesta'+this.index+'"]:checked');
        var respuestaCorrecta = this.preguntas[this.index].correcta;

        var pregunta = this.preguntas[this.index];

        if(respuestaSeleccionada.value == respuestaCorrecta){
            this.puntuacion++;
        }else{
            this.fallos.push(pregunta.texto+" = "+pregunta.opciones[respuestaCorrecta]);
        }

        this.index++;
        if(this.index == 10){
            this.botonSiguiente.disabled = true;
            this.botonSiguiente.hidden = true;
            this.mostrarResultado();
        }else{
            this.botonSiguiente.disabled = true;
            this.mostrarPregunta();
        }

        
    }

}

