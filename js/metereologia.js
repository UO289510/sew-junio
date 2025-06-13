class Meteorologia {
    constructor() {;
      this.url = "https://api.open-meteo.com/v1/forecast?latitude=43.414401&longitude=-5.968857&daily=temperature_2m_max,temperature_2m_min,temperature_2m_mean,precipitation_sum&timezone=auto";
    }

    cargarDatos() {
      $.ajax({
        dataType: "json",
        url: this.url,
        method: "GET",
        success: (data) => {
            const section = document.createElement("section");

            var title = document.createElement("h2");
            title.textContent = "Meteorología";
            section.append(title);

            data.daily.time.forEach((fecha, i) => {
              const max = data.daily.temperature_2m_max[i];
              const min = data.daily.temperature_2m_min[i];
              const avg = data.daily.temperature_2m_mean ? data.daily.temperature_2m_mean[i] : ((max + min) / 2).toFixed(1);
              const lluvia = data.daily.precipitation_sum[i];

              const article = document.createElement("article");
              article.innerHTML = `
                <h3>${fecha.split("-").reverse().join("-")}</h3>
                <p> ${avg} °C </p>
                <p> Máx: ${max} °C</p>
                <p> Mín: ${min} °C</p>
                <p> Precipitaciones: ${lluvia} mm</p>
              `;
              section.appendChild(article);
            });

            $("main").append(section);
          },
        error: () => {
          $("h3").html("¡Tenemos problemas! No puedo obtener los datos del clima de WeatherAPI.com");
          $("h4, h5, p").remove();
        }
      });
    }

    crearElemento(tipo, texto, insertarAntesDe) {
      const el = document.createElement(tipo);
      el.textContent = texto;
      $(insertarAntesDe).before(el);
    }
}
