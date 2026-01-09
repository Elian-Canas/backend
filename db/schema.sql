
-- Crear tabla Actividades
CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_segmento INT NOT NULL,
    segmento VARCHAR(200) NOT NULL,
    codigo_familia INT NOT NULL,
    familia VARCHAR(200) NOT NULL,
    codigo_clase INT NOT NULL,
    clase VARCHAR(200) NOT NULL,
    codigo_producto INT NOT NULL,
    producto VARCHAR(200) NOT NULL
);

-- Crear tabla ofertas
CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consecutivo VARCHAR(50) NOT NULL UNIQUE,
    objeto VARCHAR(150) NOT NULL,
    descripcion VARCHAR(400) NOT NULL,
    moneda VARCHAR(3) NOT NULL,
    presupuesto DECIMAL(15,2) NOT NULL,
    actividad_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    fecha_cierre DATE NOT NULL,
    hora_cierre TIME NOT NULL,
    estado VARCHAR(20) NOT NULL,
    creado_en DATETIME NOT NULL,
    actualizado_en DATETIME NOT NULL,
    CONSTRAINT fk_ofertas_actividad
        FOREIGN KEY (actividad_id) REFERENCES actividades(id)
);

-- crear tabla ofertas_documentos
CREATE TABLE ofertas_documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    licitacion_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200) NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    creado_en DATETIME NOT NULL,
    CONSTRAINT fk_documentos_oferta
        FOREIGN KEY (licitacion_id) REFERENCES ofertas(id)
);


-- Crear tabla contadores (para conteo de consecutivo)
CREATE TABLE contadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    ultimo_valor INT NOT NULL DEFAULT 0,
    anio YEAR NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_nombre_anio (nombre, anio)
);