CREATE DATABASE apirestfull;

CREATE TABLE carnes (

	id int primary key auto_increment,
	valor_total DECIMAL(10, 2) not NULL,
	qtd_parcelas int not NULL,
	data_primeiro_vencimento date not NULL,
	periodicidade varchar(10) not NULL,
	valor_entrada DECIMAL(10, 2)
	
);