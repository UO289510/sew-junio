class Noticias {

    constructor() {
        this.cargarDatos();
    }

    cargarDatos() {
        
        $.ajax({
            url: 'https://gnews.io/api/v4/search',
            method: 'GET',
            data: {
              q: '("Las Regueras" AND "Asturias") OR "Villaviciosa Asturias" OR "Navia Asturias" OR "Oviedo Asturias"',
              lang: 'es',
              max: 10,
              token: "6cdafb83497baae431ba5e842732b1b8"
            },
            success: function(datos) {

                var noticias = datos.articles;

                var section = document.createElement("section");
                
                var tituloSection = document.createElement("h2");
                tituloSection.textContent = "Noticias";

                section.appendChild(tituloSection);

                noticias.forEach((noticia, index) =>{

                    var container = document.createElement("article");

                    var title = document.createElement("h3");
                    title.textContent= noticia.title;

                    var description = document.createElement("h4");
                    description.textContent = noticia.description;

                    var content = document.createElement("p")
                    content.textContent = noticia.content;

                    var publishedDate = document.createElement("p");
                    publishedDate.textContent = noticia.publishedAt;

                    container.appendChild(title);
                    container.appendChild(description);
                    container.appendChild(content);
                    container.appendChild(publishedDate);

                    section.appendChild(container);
                });
                document.querySelector("main").appendChild(section);
            },
            error: function(){
                $("h3").html("Ha ocurrido un error al cargar las noticias");
                $("h4, h5, p").remove();
            }
          });
    }
}