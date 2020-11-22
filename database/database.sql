--Creación de la base de datos y sus respectivas tablas

create database if not exists M07;

use M07;

create table usuarios(
    id int auto_increment,
    nombre varchar(100),
    contrasenya varchar(50),
    email varchar(150),
    edad int,
    fecha_nacimiento date,
    direccion varchar(200),
    codigo_postal varchar(5),
    provincia varchar(30),
    genero varchar(10),
    primary key(id)
)
engine INNODB;

create table noticias(
    id int auto_increment,
    titulo varchar(100),
    contenido varchar(300),
    autor varchar(30),
    hora_creacion time,
    likes int,
    primary key(id)
)
engine INNODB;

