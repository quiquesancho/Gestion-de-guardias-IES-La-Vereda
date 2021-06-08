CREATE DATABASE IF NOT EXISTS guardias;

USE guardias;


CREATE TABLE IF NOT EXISTS docentes (
    dni VARCHAR(10) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    apellido1 VARCHAR(255) NOT NULL,
    apellido2 VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS horariodocente (
    dia_semana VARCHAR(1) NOT NULL,
    desde TIME NOT NULL,
    hasta TIME NOT NULL,
    dni_docente VARCHAR(10) NOT NULL,
    ocupacion VARCHAR(255) NOT NULL,
    PRIMARY KEY(dia_semana,desde,dni_docente),
    FOREIGN KEY (dni_docente) REFERENCES docentes(dni) ON UPDATE CASCADE ON DELETE NO ACTION
);


CREATE TABLE IF NOT EXISTS horariogrupos (
    dia_semana VARCHAR(1) NOT NULL,
    desde TIME NOT NULL,
    hasta TIME NOT NULL,
    grupo VARCHAR(255) NOT NULL,
    dni_docente VARCHAR(10) NOT NULL,
    aula VARCHAR(255) NOT NULL,
    PRIMARY KEY(dia_semana,desde,grupo,dni_docente),
    FOREIGN KEY (dni_docente) REFERENCES docentes(dni) ON UPDATE CASCADE ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS docente_ausente(
    dni_docente VARCHAR(10) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    PRIMARY KEY (dni_docente,fecha,hora),
    FOREIGN KEY (dni_docente) REFERENCES docentes(dni) ON UPDATE CASCADE ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS docente_guardia(
    dni_docente VARCHAR(10) PRIMARY KEY,
    confirmar_guardia int(1) NOT NULL,
    FOREIGN KEY (dni_docente) REFERENCES docentes(dni) ON UPDATE CASCADE ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS registro(
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    docente_guardia VARCHAR(10) NOT NULL,
    docente_ausente VARCHAR(10),
    grupo VARCHAR(255),
    aula VARCHAR(255),
    observaciones TEXT,
    PRIMARY KEY (fecha,hora,docente_guardia,docente_ausente),
    FOREIGN KEY (docente_guardia) REFERENCES docentes(dni) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (docente_ausente) REFERENCES docentes(dni) ON UPDATE CASCADE ON DELETE NO ACTION
);

insert into guardias.docentes
values(
	'12345678C','admin@admin.com','Admin','Admin','Admin'
);

insert into guardias.horariodocente 
values(
	'L','08:15:00','09:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'L','09:10:00','10:05:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'L','10:05:00','11:00:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'L','11:30:00','12:25:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'L','12:25:00','13:20:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'L','13:20:00','14:15:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'L','14:15:00','15:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','08:15:00','09:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','09:10:00','10:05:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','10:05:00','11:00:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','11:30:00','12:25:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','12:25:00','13:20:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','13:20:00','14:15:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'M','14:15:00','15:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','08:15:00','09:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','09:10:00','10:05:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','10:05:00','11:00:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','11:30:00','12:25:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','12:25:00','13:20:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','13:20:00','14:15:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'X','14:15:00','15:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','08:15:00','09:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','09:10:00','10:05:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','10:05:00','11:00:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','11:30:00','12:25:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','12:25:00','13:20:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','13:20:00','14:15:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'J','14:15:00','15:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','08:15:00','09:10:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','09:10:00','10:05:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','10:05:00','11:00:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','11:30:00','12:25:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','12:25:00','13:20:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','13:20:00','14:15:00','12345678A','3249454'
);
insert into guardias.horariodocente 
values(
	'V','14:15:00','15:10:00','12345678A','3249454'
);