class Carrusel {

    constructor(ruta, imagenes){

        this.ruta = ruta;
        this.imagenes = imagenes;
        this.indice = 0;

        this.main = document.querySelector("main");
        this.cargarCarrusel();
        this.asignarEventos();
    }

    cargarCarrusel() {

        var section = document.createElement("section");
        
        var contenedor = document.createElement("div");
        
        this.slides = this.imagenes.map((nombreArchivo, index) => {
            
            var imagen = document.createElement("img");
            imagen.src = this.ruta + nombreArchivo;
            imagen.alt = "Imagen " + (index + 1);

            contenedor.appendChild(imagen);
        });

        var btnSiguiente = document.createElement("button");
        
        var btnAnterior = document.createElement("button");


        section.appendChild(btnAnterior);
        section.appendChild(contenedor);
        section.appendChild(btnSiguiente);

        this.main.appendChild(section);
    }


    asignarEventos(){
        $(btnSiguiente).on("click", () => {
            this.indiceActual = (this.indiceActual + 1) % this.slides.length;
            this.actualizarCarrusel();
        });

        $(btnAnterior).on("click", () => {
            this.indiceActual = (this.indiceActual -1) % this.slides.length;
            this.actualizarCarrusel();
        });

    }

    actualizarCarrusel(){
        this.slides.forEach((img, i) => {
            $(img).css("transform", `translateX(${100 * (i - this.indiceActual)}%)`);        
        });
    }






}