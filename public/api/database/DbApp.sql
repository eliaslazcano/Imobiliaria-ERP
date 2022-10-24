create table organizacoes
(
	id int unsigned auto_increment
		primary key,
	nome varchar(64) null comment 'nome da empresa',
	cnpj varchar(14) null,
	licencas int unsigned null comment 'limite de contas de usuario',
	criado_em datetime default current_timestamp() not null
)
comment 'empresas que tem licenca de uso';

create table usuarios
(
	id int unsigned auto_increment
		primary key,
	login varchar(32) null,
	senha varchar(32) null,
	nome varchar(64) null,
	email varchar(64) null,
	organizacao int unsigned not null,
	criado_em datetime default current_timestamp() not null,
	inativado_em datetime default current_timestamp() null,
	constraint login
		unique (login),
	constraint usuarios_organizacoes_id_fk
		foreign key (organizacao) references organizacoes (id)
			on update cascade on delete cascade
);

create table sessoes
(
	id int unsigned auto_increment
		primary key,
	usuario int unsigned not null,
	ip varchar(15) null,
	versao varchar(8) null,
	chave varchar(32) null,
	criado_em datetime default current_timestamp() not null,
	constraint sessoes_usuarios_id_fk
		foreign key (usuario) references usuarios (id)
			on update cascade on delete cascade
);


