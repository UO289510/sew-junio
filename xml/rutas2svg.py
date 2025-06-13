import xml.etree.ElementTree as ET
import re

def dms_to_decimal(dms_str):
    match = re.match(r"(\d+)[°º] (\d+)' (\d+(?:\.\d+)?)'' ([NSEW])", dms_str)
    if not match:
        raise ValueError(f"Formato inválido: {dms_str}")
    degrees, minutes, seconds, direction = match.groups()
    decimal = float(degrees) + float(minutes)/60 + float(seconds)/3600
    if direction in ['S', 'W']:
        decimal *= -1
    return decimal

def generar_svg(ruta, index):
    alturas = []
    distancias = []
    etiquetas = []

    # Recoger todas las alturas y distancias primero (inicio + hitos)

    for hito in ruta.find('hitos').findall('hito'):
        alturas.append(float(hito.attrib['altitud']))
        distancias.append(float(hito.attrib['distancia'].split()[0]))

    # Calcular escalado dinámico
    altura_min = min(alturas)
    altura_max = max(alturas)
    margen = 60  # espacio extra para no pegar al borde

    svg_height = 300
    escala_y = (svg_height - 2 * margen) / (altura_max - altura_min) if altura_max != altura_min else 1
    y_base = svg_height - margen

    # Escalado horizontal según número de puntos
    num_puntos = len(alturas)
    escala_x = 100
    svg_width = num_puntos * escala_x + 2 * margen

    puntos = []
    distancia_acumulada = 0

    for i in range(num_puntos):
        x = i * escala_x + margen
        alt = alturas[i]
        y = y_base - (alt - altura_min) * escala_y
        puntos.append((x, y))
        distancia_acumulada += distancias[i]
        etiquetas.append((x, y, alt, round(distancia_acumulada, 1)))

    # Generar SVG
    svg = '<?xml version="1.0" encoding="utf-8"?>\n'
    svg += f'<svg width="{svg_width}" height="{svg_height}" xmlns="http://www.w3.org/2000/svg">\n'

    puntos_str = ' '.join([f"{x},{y}" for x, y in puntos])
    svg += f'  <polyline points="{puntos_str}" stroke="blue" stroke-width="2" fill="none"/>\n'

    for x, y, alt, dist in etiquetas:
        svg += f'  <text x="{x}" y="{y}" font-size="12" text-anchor="middle" transform="translate(-5,-10)">\n'
        svg += f'    {alt}m ({dist}m)\n'
        svg += '  </text>\n'

    svg += '</svg>\n'

    with open(f"ruta_{index+1}.svg", "w", encoding="utf-8") as f:
        f.write(svg)


# --- Ejecución principal ---
tree = ET.parse("rutas.xml")
root = tree.getroot()
rutas = root.findall("ruta")

for i, ruta in enumerate(rutas):
    generar_svg(ruta, i)

print("✔ Archivos SVG generados.")

