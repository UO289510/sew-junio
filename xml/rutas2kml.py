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

def generar_kml(ruta, index):
    ns = "http://www.opengis.net/kml/2.2"
    kml = ET.Element("kml", xmlns=ns)
    doc = ET.SubElement(kml, "Document")

    coordinates = []

    # Hitos
    for hito in ruta.find('hitos').findall('hito'):
        lat = dms_to_decimal(hito.attrib['latitud'])
        lon = dms_to_decimal(hito.attrib['longitud'])
        alt = hito.attrib['altitud']
        coordinates.append(f"{lon},{lat},{alt}")

        # Marcador del hito
        pm = ET.SubElement(doc, "Placemark")
        ET.SubElement(pm, "name").text = hito.attrib['nombre']
        point = ET.SubElement(pm, "Point")
        ET.SubElement(point, "coordinates").text = f"{lon},{lat},{alt}"
        ET.SubElement(point, "altitudeMode").text = "clampToGround"

    # Ruta completa como línea
    placemark = ET.SubElement(doc, "Placemark")
    ET.SubElement(placemark, "name").text = f"Ruta {index+1}"
    line = ET.SubElement(placemark, "LineString")
    ET.SubElement(line, "extrude").text = "1"
    ET.SubElement(line, "tessellate").text = "1"
    ET.SubElement(line, "altitudeMode").text = "clampToGround"
    ET.SubElement(line, "coordinates").text = "\n" + "\n".join(coordinates) + "\n"

    tree = ET.ElementTree(kml)
    tree.write(f"ruta_{index+1}.kml", encoding="utf-8", xml_declaration=True)

# --- Ejecución principal ---
tree = ET.parse("rutas.xml")
root = tree.getroot()
rutas = root.findall("ruta")

for i, ruta in enumerate(rutas):
    generar_kml(ruta, i)

print("✔ Archivos KML generados con puntos marcados y alturas al nivel del suelo.")
