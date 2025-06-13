class Rutas{

    archivo = null;

    constructor(){
        if(!(window.File && window.FileReader && window.FileList && window.Blob)){
            document.write("<p>Este navegador no soporta el API File y este programa puede no funcionar correctamente</p>");
        }

        this.planimetriaFiles = [];
        this.altimetriaFiles = [];

    } 

    leerArchivoXML(files){

        var section = document.createElement("section");

        var h3 = document.createElement("h3");
        h3.textContent = "Información de las rutas";
        section.append(h3);

        this.archivo = files[0];

        if(this.archivo.type.match("text/xml")){
            var lector = new FileReader();
            
            var self = this;

            lector.onload = function(evento){

                var xmlDoc = $.parseXML(evento.target.result);
                var documento = xmlDoc.children;
                var contenedor = documento[0];
                var listaRutas = contenedor.children;
                
                for(var i=0; i<listaRutas.length; i++){
    
                    var seccionRuta = document.createElement("section");
    
                    var ruta = listaRutas[i];
    
                    var contenidosRuta = ruta.children;
    
                    for(var c=0; c<contenidosRuta.length-2; c++){
    
                        var hijo = contenidosRuta[c];
    
                        if(hijo.children.length > 0){
    
                            var nietos = hijo.children;
    
                            for(var j=0; j<nietos.length; j++){
    
                                var nieto = nietos[j];
    
                                var rutaAttList = document.createElement("ul");
    
                                if(nieto.nodeName == "hito"){
    
                                    for(var att=0; att<nieto.attributes.length; att++){
                                        var attribute = nieto.attributes[att];
                                        var attRuta = document.createElement("li");
                                        var categoria = attribute.name.charAt(0).toUpperCase()+attribute.name.slice(1);
                                        attRuta.textContent = categoria+": "+attribute.value;
                                        rutaAttList.append(attRuta);
                                    }
    
                                    var galerias = nieto.children;
                                    
                                    var fotos = galerias[0].children;
    
                                    for(var f=0; f<fotos.length; f++){
                                        var foto = fotos[f].attributes;
                                        var imagen = document.createElement("img");
                                        imagen.src = foto.src.value;
                                        imagen.alt = foto.alt.value;
                                        seccionRuta.append(imagen);
                                    }
    
                                    if(galerias.length > 1){
                                        var videos = galerias[1];
    
                                        for(var v=0; f<videos.length; f++){
                                            var video = document.createElement("video");
                                            imagen.src = v.src;
                                            imagen.alt = v.alt;
                                            seccionRuta.append(video);
                                        }
                                    }
                                }
    
                                else{
    
                                    for(var att=0; att<nieto.attributes.length; att++){
        
                                        var attribute = nieto.attributes[att];
        
                                        if(attribute.name == "nombreRuta"){
                                            var titulo = document.createElement("h3");
                                            titulo.textContent = attribute.value;
                                            seccionRuta.append(titulo);
                                        }else{
        
                                            var attRuta = document.createElement("li");
                                            var categoria = attribute.name.charAt(0).toUpperCase()+attribute.name.slice(1);
                                            attRuta.textContent = categoria+": "+attribute.value;
                                            rutaAttList.append(attRuta);
                                        }
        
                                    }
                                
                                }
    
                                seccionRuta.append(rutaAttList);
                            }
    
                        }else{
    
                            var rutaAttList = document.createElement("ul");
                            
                            for(var att=0; att<hijo.attributes.length; att++){
    
                                var attribute = hijo.attributes[att];
    
                                if(attribute.name == "nombre"){
                                    var titulo = document.createElement("h3");
                                    titulo.textContent = attribute.value;
                                    seccionRuta.append(titulo);
                                }else{
    
                                    var attRuta = document.createElement("li");
                                    var categoria = attribute.name.charAt(0).toUpperCase()+attribute.name.slice(1);
                                    attRuta.textContent = categoria+": "+attribute.value;
                                    rutaAttList.append(attRuta);
                                }
    
                            }
                            
                            seccionRuta.append(rutaAttList);
                        }
                        
                        section.appendChild(seccionRuta);
                    }
    
                    self.planimetriaFiles.push(contenidosRuta[5].attributes.src.value);
                    self.altimetriaFiles.push(contenidosRuta[6].attributes.src.value);
    
                }
    
                document.querySelector("main").appendChild(section);
    
                self.leerArchivoKML();
                self.leerArchivoSVG();

            }
            lector.readAsText(this.archivo);
        }
    }

    leerArchivoKML(){

        for(let i=0; i<this.planimetriaFiles.length; i++){

            var file = this.planimetriaFiles[i];
            
            $.get(file, function(data){
                
                var coordenadas = new Array();
                var nombrePuntos = new Array();

                var kml = data.children[0];
                var doc = kml.children[0];
                var puntos = doc.children;

                for(var p=0; p<puntos.length-1; p++){

                    var punto = puntos[p];

                    var nombrePunto = punto.children[0].textContent;
                    var coordPunto = punto.children[1].children[0].textContent;

                    var coords = coordPunto.split(",");
                    var long = parseFloat(coords[0]);
                    var lat = parseFloat(coords[1]);

                    coordenadas[p] = [long, lat];
                    nombrePuntos[p] = nombrePunto;
                }

                var centroLat = coordenadas[1][0];
                var centroLong = coordenadas[0][1];

                mapboxgl.accessToken = "pk.eyJ1IjoidW8yODk1MTAiLCJhIjoiY200OG93MnNnMDI2YjJpcjRieXM5cDUybSJ9.HJAZajuwP81PRQqybk2eZw";

                var mapa = document.createElement("div");

                var map = new mapboxgl.Map({
                    container:mapa,
                    style: 'mapbox://styles/mapbox/streets-v12',
                    zoom: 11,
                    center: [centroLat, centroLong],
                    attributionControl:false
                    
                });

                map.on('load', () => {
                    map.addSource('route', {
                        'type':'geojson',
                        'data':{
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                            'type':'LineString',
                            'coordinates': coordenadas
                            }
                        }
                    });

                    map.addLayer({
                        'id':'route',
                        'type':'line',
                        'source':'route',
                        'layout':{
                            'line-join':'round',
                            'line-cap':'round'
                        },
                        'paint':{
                            'line-color':'red',
                            'line-width':2
                        }
                    });
                });

                
                for (let j = 0; j < coordenadas.length; j++) {
                    new mapboxgl.Marker()
                        .setLngLat(coordenadas[j])
                        .setPopup(new mapboxgl.Popup({ offset: 25 }).setText(nombrePuntos[j]))
                        .addTo(map);
                }
                

                map.resize();

                var main = document.querySelector("main");
                var contenedor = main.children[1];
                var ruta = contenedor.children[i+1];

                var seccionMapa = document.createElement("section");

                var h3 = document.createElement("h3");
                h3.textContent = "Mapa de la ruta";
                seccionMapa.appendChild(h3);

                seccionMapa.appendChild(mapa);
                ruta.append(seccionMapa);

            }).fail(function(){
                var errorArchivo = document.createElement("p");
                errorArchivo.innerText = "Error: ¡¡¡ Archivo no válido !!!";
                document.querySelector("main").appendChild(errorArchivo);
            });

        }

    }

    leerArchivoSVG(){

        for(let i=0; i<this.altimetriaFiles.length; i++){

            var file = this.altimetriaFiles[i];

            $.get(file, function(data){

                var svg = data.children[0];
                var children = svg.children;
                var polylines = svg.children[0].animatedPoints;

                var canvas = document.createElement("canvas");
                canvas.width=500;
                canvas.height=500;
                var ctx = canvas.getContext("2d");

                ctx.beginPath();
                ctx.moveTo(0,450);
                ctx.strokeStyle = "red";
                ctx.lineTo(500, 450);
                ctx.stroke();

                ctx.beginPath();
                ctx.strokeStyle = "blue";
                for(var p=1; p<svg.children.length; p++){

                    var point = polylines[p-1];
                    var etiqueta = children[p].innerHTML;

                    var texto = etiqueta.trim();
                    var xPoint = point.x;
                    var yPoint = point.y;

                    ctx.lineTo(xPoint, yPoint);
                    ctx.fillText(texto, xPoint, yPoint-2);
                }
                ctx.stroke();

                var main = document.querySelector("main");
                var contenedor = main.children[1];
                var ruta = contenedor.children[i+1];

                var canvasSection = document.createElement("section");

                var h3 = document.createElement("h3");
                h3.textContent = "Altimetria de la ruta";
                canvasSection.appendChild(h3);

                canvasSection.append(canvas);
                ruta.append(canvasSection);
            });

        }



    }


}